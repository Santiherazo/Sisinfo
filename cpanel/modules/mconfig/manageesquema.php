<?php
echo '<h1 class="text-2xl font-bold mb-6">Editor de Esquema de Evaluación</h1>';

try {
	$cfg = loadConfig('esquema');

	if (!$cfg || !is_array($cfg)) throw new Exception('Archivo de esquema.json vacío o mal formado.');

	if (isset($_POST['save'])) {
		$nuevo = [];
		foreach ($_POST['data_key'] as $i => $campo) {
			$valor = $_POST['data_val'][$i];
			if ($campo) $nuevo[$campo] = $valor;
		}
		foreach ($cfg as $key => $val) {
			if (is_array($val)) $nuevo[$key] = $val;
		}
		$cfg = $nuevo;

		foreach ($_POST['criterios'] as $bloque => $fases) {
			foreach ($fases as $fase => $niveles) {
				foreach ($niveles as $nivel => $texto) {
					$cfg[$bloque][$fase][$nivel] = $texto;
				}
			}
		}

		saveConfig('esquema', $cfg);
		echo '<div class="bg-green-100 text-green-700 p-4 rounded mb-4">Cambios guardados correctamente.</div>';
	}

	echo '<form method="post" class="space-y-6"><div class="bg-white p-6 rounded shadow">';

	echo '<h2 class="text-xl font-semibold mb-4">Información General</h2>';
	echo '<div id="basicFields" class="grid grid-cols-1 md:grid-cols-3 gap-4">';
	foreach ($cfg as $key => $val) {
		if (!is_array($val)) {
			echo '
			<div class="basic-field">
				<label class="block text-sm font-medium text-gray-700 mb-1">Nombre Variable</label>
				<input type="text" name="data_key[]" value="'.htmlspecialchars($key).'" class="block mb-1 w-full border px-2 py-1 rounded">
				<label class="block text-sm font-medium text-gray-700 mb-1">Valor</label>
				<input type="text" name="data_val[]" value="'.htmlspecialchars($val).'" class="w-full px-2 py-1 border rounded">
				<div class="flex space-x-2 mt-1">
					<button type="button" onclick="removeField(this)" class="text-red-600 text-sm">Eliminar</button>
					<button type="button" onclick="moveUp(this)" class="text-blue-600 text-sm">↑</button>
					<button type="button" onclick="moveDown(this)" class="text-blue-600 text-sm">↓</button>
				</div>
			</div>';
		}
	}
	echo '</div>
		<div class="mt-4">
			<button type="button" onclick="addField()" class="bg-gray-800 text-white px-3 py-1 rounded text-sm">+ Agregar campo</button>
		</div>
		<hr class="my-6 border-t border-gray-200" />';

	echo '<div id="criteriosContainer">';
	foreach ($cfg as $criterio => $fases) {
		if (!is_array($fases) || !isset($fases['propuesta'])) continue;
		renderCriterio($criterio, $fases);
	}
	echo '</div>';
	echo '<div class="mt-4">
			<button type="button" onclick="addCriterio()" class="bg-green-700 text-white px-4 py-2 rounded">+ Agregar Criterio</button>
		</div>';

	echo '<div class="mt-6">
			<button type="submit" name="save" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">Guardar Cambios</button>
		</div>';
	echo '</div></form>';

} catch (Exception $e) {
	echo '<div class="bg-red-100 text-red-700 p-4 rounded">Error: '.$e->getMessage().'</div>';
}

function renderCriterio($criterio, $fases) {
	echo "<h3 class='text-lg font-bold mt-6 mb-2 border-b border-gray-300 pb-1'>$criterio</h3>";
	echo "<table class='min-w-full text-sm text-left text-gray-800 border border-gray-300 fases-table' data-criterio=\"$criterio\">";
	echo '<thead class="bg-gray-100 border-b"><tr>
			<th class="p-2 border">Fase</th>
			<th class="p-2 border w-1/3">Deficiente</th>
			<th class="p-2 border w-1/3">Bueno</th>
			<th class="p-2 border w-1/3">Excelente</th>
			<th class="p-2 border w-12">Acciones</th>
		</tr></thead><tbody>';

	foreach ($fases as $fase => $niveles) {
		echo "<tr class='odd:bg-white even:bg-gray-50'>";
		echo "<td class='p-2 border'><input type='text' readonly value='$fase' class='fase-name w-full border px-2 py-1 bg-gray-100'></td>";
		foreach (['Deficiente', 'Bueno', 'Excelente'] as $nivel) {
			$name = "criterios[$criterio][$fase][$nivel]";
			$value = htmlspecialchars($niveles[$nivel] ?? '');
			echo "<td class='p-2 border'><textarea name=\"$name\" rows=\"3\" class=\"w-full px-3 py-2 border rounded\">$value</textarea></td>";
		}
		echo "<td class='p-2 border'><button type='button' onclick='removeFase(this)' class='text-red-600 text-sm'>🗑</button></td>";
		echo "</tr>";
	}
	echo '</tbody></table>';
	echo "<button type='button' onclick=\"addFaseRow('$criterio')\" class='mt-2 bg-gray-800 text-white px-3 py-1 rounded text-sm'>+ Agregar fase</button>";
}
?>

<script>
function addField() {
	const container = document.getElementById('basicFields');
	const div = document.createElement('div');
	div.className = 'basic-field';
	div.innerHTML = `
		<label class="block text-sm font-medium text-gray-700 mb-1">Nombre Variable</label>
		<input type="text" name="data_key[]" value="" class="block mb-1 w-full border px-2 py-1 rounded">
		<label class="block text-sm font-medium text-gray-700 mb-1">Valor</label>
		<input type="text" name="data_val[]" value="" class="w-full px-2 py-1 border rounded">
		<div class="flex space-x-2 mt-1">
			<button type="button" onclick="removeField(this)" class="text-red-600 text-sm">Eliminar</button>
			<button type="button" onclick="moveUp(this)" class="text-blue-600 text-sm">↑</button>
			<button type="button" onclick="moveDown(this)" class="text-blue-600 text-sm">↓</button>
		</div>`;
	container.appendChild(div);
}

function removeField(btn) {
	btn.closest('.basic-field').remove();
}

function moveUp(btn) {
	const field = btn.closest('.basic-field');
	if (field.previousElementSibling) field.parentNode.insertBefore(field, field.previousElementSibling);
}

function moveDown(btn) {
	const field = btn.closest('.basic-field');
	if (field.nextElementSibling) field.parentNode.insertBefore(field.nextElementSibling, field);
}

function addFaseRow(criterio) {
	const table = document.querySelector(`table[data-criterio="${criterio}"] tbody`);
	const faseName = 'fase_' + Math.floor(Math.random() * 10000);
	const tr = document.createElement('tr');
	tr.className = 'odd:bg-white even:bg-gray-50';
	tr.innerHTML = `
		<td class='p-2 border'><input type='text' name='fase_name' value='${faseName}' class='fase-name w-full border px-2 py-1'></td>
		<td class='p-2 border'><textarea name="criterios[${criterio}][${faseName}][Deficiente]" rows="3" class="w-full px-3 py-2 border rounded"></textarea></td>
		<td class='p-2 border'><textarea name="criterios[${criterio}][${faseName}][Bueno]" rows="3" class="w-full px-3 py-2 border rounded"></textarea></td>
		<td class='p-2 border'><textarea name="criterios[${criterio}][${faseName}][Excelente]" rows="3" class="w-full px-3 py-2 border rounded"></textarea></td>
		<td class='p-2 border'><button type='button' onclick='removeFase(this)' class='text-red-600 text-sm'>🗑</button></td>`;
	table.appendChild(tr);
}

function removeFase(btn) {
	btn.closest('tr').remove();
}

function addCriterio() {
	const nombre = prompt("Nombre del nuevo criterio:");
	if (!nombre) return;

	const container = document.getElementById('criteriosContainer');
	const div = document.createElement('div');

	let contenido = `<h3 class='text-lg font-bold mt-6 mb-2 border-b border-gray-300 pb-1'>${nombre}</h3>`;
	contenido += `<table class='min-w-full text-sm text-left text-gray-800 border border-gray-300 fases-table' data-criterio="${nombre}">`;
	contenido += `<thead class="bg-gray-100 border-b"><tr>
		<th class="p-2 border">Fase</th>
		<th class="p-2 border w-1/3">Deficiente</th>
		<th class="p-2 border w-1/3">Bueno</th>
		<th class="p-2 border w-1/3">Excelente</th>
		<th class="p-2 border w-12">Acciones</th>
	</tr></thead><tbody>`;

	const fases = ['propuesta', 'desarrollo', 'aplicacion'];
	fases.forEach(fase => {
		contenido += `<tr class='odd:bg-white even:bg-gray-50'>
			<td class='p-2 border'><input type='text' readonly value='${fase}' class='fase-name w-full border px-2 py-1 bg-gray-100'></td>
			<td class='p-2 border'><textarea name="criterios[${nombre}][${fase}][Deficiente]" rows="3" class="w-full px-3 py-2 border rounded"></textarea></td>
			<td class='p-2 border'><textarea name="criterios[${nombre}][${fase}][Bueno]" rows="3" class="w-full px-3 py-2 border rounded"></textarea></td>
			<td class='p-2 border'><textarea name="criterios[${nombre}][${fase}][Excelente]" rows="3" class="w-full px-3 py-2 border rounded"></textarea></td>
			<td class='p-2 border'><button type='button' onclick='removeFase(this)' class='text-red-600 text-sm'>🗑</button></td>
		</tr>`;
	});
	contenido += `</tbody></table>`;
	contenido += `<button type='button' onclick="addFaseRow('${nombre}')" class='mt-2 bg-gray-800 text-white px-3 py-1 rounded text-sm'>+ Agregar fase</button>`;

	div.innerHTML = contenido;
	container.appendChild(div);
}
</script>