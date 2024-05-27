<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Archivos JSON</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .form-control {
            margin-bottom: 10px;
        }
        .form-control label {
            display: block;
            margin-bottom: 5px;
        }
        .form-control input, .form-control select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .form-actions {
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>Gestión de Archivos JSON</h1>
    <div id="form-container">
        <h2>Agregar/Editar Elemento</h2>
        <div class="form-control">
            <label for="active">Activo:</label>
            <select id="active">
                <option value="true">Sí</option>
                <option value="false">No</option>
            </select>
        </div>
        <div class="form-control">
            <label for="type">Tipo:</label>
            <input type="text" id="type">
        </div>
        <div class="form-control">
            <label for="phrase">Frase:</label>
            <input type="text" id="phrase">
        </div>
        <div class="form-control">
            <label for="link">Enlace:</label>
            <input type="text" id="link">
        </div>
        <div class="form-control">
            <label for="visibility">Visibilidad:</label>
            <input type="text" id="visibility">
        </div>
        <div class="form-control">
            <label for="newtab">Nueva Pestaña:</label>
            <select id="newtab">
                <option value="true">Sí</option>
                <option value="false">No</option>
            </select>
        </div>
        <div class="form-control">
            <label for="order">Orden:</label>
            <input type="number" id="order">
        </div>
        <div class="form-actions">
            <button id="save-button">Guardar</button>
            <button id="reset-button">Resetear</button>
        </div>
    </div>
    <h2>Lista de Elementos</h2>
    <table id="json-table">
        <thead>
            <tr>
                <th>Activo</th>
                <th>Tipo</th>
                <th>Frase</th>
                <th>Enlace</th>
                <th>Visibilidad</th>
                <th>Nueva Pestaña</th>
                <th>Orden</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script src="script.js"></script>
</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const apiUrl = 'api.php';

    const tableBody = document.querySelector('#json-table tbody');
    const saveButton = document.getElementById('save-button');
    const resetButton = document.getElementById('reset-button');

    const formFields = {
        active: document.getElementById('active'),
        type: document.getElementById('type'),
        phrase: document.getElementById('phrase'),
        link: document.getElementById('link'),
        visibility: document.getElementById('visibility'),
        newtab: document.getElementById('newtab'),
        order: document.getElementById('order'),
    };

    let editIndex = null;

    // Cargar datos iniciales
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            data.forEach((item, index) => {
                appendRow(item, index);
            });
        });

    function appendRow(item, index) {
        const row = document.createElement('tr');

        row.innerHTML = `
            <td>${item.active}</td>
            <td>${item.type}</td>
            <td>${item.phrase}</td>
            <td>${item.link}</td>
            <td>${item.visibility}</td>
            <td>${item.newtab}</td>
            <td>${item.order}</td>
            <td>
                <button onclick="editItem(${index})">Editar</button>
                <button onclick="deleteItem(${index})">Eliminar</button>
            </td>
        `;

        tableBody.appendChild(row);
    }

    // Guardar datos (crear o actualizar)
    saveButton.addEventListener('click', () => {
        const item = {
            active: formFields.active.value === 'true',
            type: formFields.type.value,
            phrase: formFields.phrase.value,
            link: formFields.link.value,
            visibility: formFields.visibility.value,
            newtab: formFields.newtab.value === 'true',
            order: parseInt(formFields.order.value, 10),
        };

        if (editIndex === null) {
            // Crear nuevo
            fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(item)
            })
                .then(response => response.json())
                .then(data => {
                    appendRow(data, tableBody.children.length);
                });
        } else {
            // Actualizar existente
            fetch(`${apiUrl}?index=${editIndex}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(item)
            })
                .then(response => response.json())
                .then(data => {
                    tableBody.children[editIndex].innerHTML = `
                        <td>${data.active}</td>
                        <td>${data.type}</td>
                        <td>${data.phrase}</td>
                        <td>${data.link}</td>
                        <td>${data.visibility}</td>
                        <td>${data.newtab}</td>
                        <td>${data.order}</td>
                        <td>
                            <button onclick="editItem(${editIndex})">Editar</button>
                            <button onclick="deleteItem(${editIndex})">Eliminar</button>
                        </td>
                    `;
                });
        }

        resetForm();
    });

    // Resetear formulario
    resetButton.addEventListener('click', () => {
        resetForm();
    });

    function resetForm() {
        formFields.active.value = 'true';
        formFields.type.value = '';
        formFields.phrase.value = '';
        formFields.link.value = '';
        formFields.visibility.value = '';
        formFields.newtab.value = 'false';
        formFields.order.value = '';
        editIndex = null;
    }

    // Editar elemento
    window.editItem = function (index) {
        fetch(`${apiUrl}?index=${index}`)
            .then(response => response.json())
            .then(data => {
                formFields.active.value = data.active ? 'true' : 'false';
                formFields.type.value = data.type;
                formFields.phrase.value = data.phrase;
                formFields.link.value = data.link;
                formFields.visibility.value = data.visibility;
                formFields.newtab.value = data.newtab ? 'true' : 'false';
                formFields.order.value = data.order;
                editIndex = index;
            });
    };

    // Eliminar elemento
    window.deleteItem = function (index) {
        fetch(`${apiUrl}?index=${index}`, {
            method: 'DELETE'
        })
            .then(response => response.json())
            .then(() => {
                tableBody.removeChild(tableBody.children[index]);
            });
    };
});
</script>