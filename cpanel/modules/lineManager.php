<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  $id = $_POST['id'] ?? null;
  $nombre = $_POST['nombre'] ?? '';

  if ($action === 'create') {
    $manager->create($nombre);
    exit;
  } elseif ($action === 'toggle_status' && $id) {
    $line = $manager->getById($id);
    if ($line) {
      if ($line[DB_RESEARCH_LINE_ESTADO] === 'activo') {
        $manager->deactivate($id);
      } else {
        $manager->restore($id);
      }
    }
    exit;
  } elseif ($action === 'delete' && $id) {
    $manager->delete($id);
    exit;
  } elseif ($action === 'update' && $id && $nombre !== '') {
    $manager->update($id, $nombre);
    exit;
  }
}
?>

<div id="research-lines-module" class="bg-white rounded-lg shadow-sm p-6">
  <div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-1">Líneas de Investigación</h2>
    <p class="text-gray-500">Administra las líneas de investigación de tu institución</p>
  </div>

  <div class="bg-gray-50 rounded-lg p-5 mb-8 border border-gray-100">
    <h3 class="text-lg font-semibold text-gray-700 mb-4">Nueva Línea de Investigación</h3>
    <form method="POST" id="lineForm">
      <input type="hidden" name="action" value="create">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-600 mb-1">Nombre</label>
          <input type="text" name="nombre" required class="w-full px-3 py-2 border bg-white border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent">
        </div>
        <div class="flex items-end">
          <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2.5 px-4 rounded-lg transition-all flex items-center justify-center gap-2 shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Crear Línea
          </button>
        </div>
      </div>
    </form>
  </div>

  <div class="overflow-x-auto rounded-lg border border-gray-100">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Línea de Investigación</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
          <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        <?php foreach ($manager->getAll() as $line): ?>
        <tr class="hover:bg-gray-50 transition-colors" data-id="<?= $line[DB_RESEARCH_LINE_ID] ?>">
          <td class="px-6 py-4 font-medium text-gray-800">
            <span class="line-text"><?= htmlspecialchars($line[DB_RESEARCH_LINE_NOMBRE]) ?></span>
            <div class="hidden line-edit-form flex items-center gap-2">
              <input type="text" class="border bg-white px-2 py-1 rounded w-full" value="<?= htmlspecialchars($line[DB_RESEARCH_LINE_NOMBRE]) ?>">
              <button onclick="confirmEditLine(this, <?= $line[DB_RESEARCH_LINE_ID] ?>)" class="text-blue-600 hover:text-white hover:bg-blue-600 rounded p-1.5 transition-all">
                <i data-lucide="check" class="w-4 h-4"></i>
              </button>
            </div>
          </td>
          <td class="px-6 py-4 text-center">
            <button onclick="toggleStatus(this, <?= $line[DB_RESEARCH_LINE_ID] ?>)" class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors duration-200 ease-in-out <?= $line[DB_RESEARCH_LINE_ESTADO] === 'activo' ? 'bg-blue-600' : 'bg-gray-300' ?>">
              <span class="sr-only">Cambiar estado</span>
              <span class="inline-block w-4 h-4 transform bg-white rounded-full shadow-md transition-transform duration-200 ease-in-out <?= $line[DB_RESEARCH_LINE_ESTADO] === 'activo' ? 'translate-x-6' : 'translate-x-1' ?>"></span>
            </button>
          </td>
          <td class="px-6 py-4 text-right">
            <div class="flex justify-end space-x-3">
              <button onclick="editLine(this)" class="text-gray-500 hover:text-blue-600 p-1.5 rounded-lg hover:bg-blue-50 transition-all">
                <i data-lucide="edit" class="w-5 h-5"></i>
              </button>
              <button data-id="<?= $line[DB_RESEARCH_LINE_ID] ?>" class="delete-button text-gray-500 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition-all">
                <i data-lucide="trash-2" class="w-5 h-5"></i>
              </button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.getElementById('lineForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const form = e.target;
  const data = new URLSearchParams(new FormData(form));
  fetch('', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: data
  }).then(() => location.reload());
});

document.querySelectorAll('.delete-button').forEach(button => {
  button.addEventListener('click', function () {
    const id = this.dataset.id;

    if (!id) {
      console.warn('ID no encontrado en el botón');
      return;
    }

    fetch(window.location.href, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `action=delete&id=${id}`
    }).then(() => location.reload());
  });
});

function toggleStatus(button, id) {
  const isActive = button.classList.contains('bg-blue-600');
  button.classList.toggle('bg-blue-600');
  button.classList.toggle('bg-gray-300');
  const span = button.querySelector('span:not(.sr-only)');
  span.classList.toggle('translate-x-6');
  span.classList.toggle('translate-x-1');
  fetch('', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=toggle_status&id=${id}`
  }).then(response => {
    if (!response.ok) {
      button.classList.toggle('bg-blue-600');
      button.classList.toggle('bg-gray-300');
      span.classList.toggle('translate-x-6');
      span.classList.toggle('translate-x-1');
    }
  });
}

function editLine(button) {
  const row = button.closest('tr');
  const span = row.querySelector('.line-text');
  const form = row.querySelector('.line-edit-form');
  span.classList.add('hidden');
  form.classList.remove('hidden');
}

function confirmEditLine(button, id) {
  const row = button.closest('tr');
  const input = row.querySelector('.line-edit-form input');
  const span = row.querySelector('.line-text');
  const nombre = input.value.trim();
  if (!nombre) return;
  const data = new URLSearchParams();
  data.append('action', 'update');
  data.append('id', id);
  data.append('nombre', nombre);
  fetch('', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: data
  }).then(() => {
    span.textContent = nombre;
    span.classList.remove('hidden');
    row.querySelector('.line-edit-form').classList.add('hidden');
  });
}
</script>