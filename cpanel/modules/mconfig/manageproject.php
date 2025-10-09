<div class="container">
    <style>
        /* Mantener estilos anteriores y agregar estos nuevos */
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .edit-button {
            background-color: #ffc107;
            color: black;
        }
        
        .delete-button {
            background-color: #dc3545;
            color: white;
        }
        
        .detail-button {
            background-color: #17a2b8;
            color: white;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
        }
    </style>

    <!-- Contenido anterior manteniendo las estadísticas y filtros -->

    <div class="data-preview">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th>Progreso</th>
                    <th>Fecha Entrega</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="projectsTable">
                <tr data-project-id="1">
                    <td>Migración a la nube</td>
                    <td><span class="status-badge status-progress">En Progreso</span></td>
                    <td><span class="priority-indicator priority-medium"></span>Media</td>
                    <td>
                        <div class="progress-bar" style="width: 120px;">
                            <div class="progress" style="width: 65%;"></div>
                        </div>
                    </td>
                    <td>2024-09-30</td>
                    <td>
                        <div class="action-buttons">
                            <button class="view-button detail-button" onclick="showProjectDetails(1)">👁️ Ver</button>
                            <button class="view-button edit-button" onclick="editProject(1)">✏️ Editar</button>
                            <button class="view-button delete-button" onclick="confirmDelete(1)">🗑️ Eliminar</button>
                        </div>
                    </td>
                </tr>
                <!-- Más filas -->
            </tbody>
        </table>
    </div>

    <!-- Modal de Detalles -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <h3>Detalles del Proyecto</h3>
            <div id="modalContent"></div>
            <button class="view-button" onclick="closeModal()">Cerrar</button>
        </div>
    </div>
</div>

<script>
    // Funcionalidades CRUD
    function showProjectDetails(projectId) {
        // Simular carga de datos
        const project = {
            id: 1,
            name: 'Migración a la nube',
            description: 'Proyecto de migración de servidores locales a AWS',
            manager: 'Carlos Mendoza',
            budget: '$89,500',
            startDate: '2024-03-15',
            endDate: '2024-09-30',
            team: ['Juan Pérez', 'María Gómez']
        };

        const modalContent = `
            <p><strong>Nombre:</strong> ${project.name}</p>
            <p><strong>Descripción:</strong> ${project.description}</p>
            <p><strong>Gerente:</strong> ${project.manager}</p>
            <p><strong>Presupuesto:</strong> ${project.budget}</p>
            <p><strong>Fecha Inicio:</strong> ${project.startDate}</p>
            <p><strong>Fecha Fin:</strong> ${project.endDate}</p>
            <p><strong>Equipo:</strong> ${project.team.join(', ')}</p>
        `;

        document.getElementById('modalContent').innerHTML = modalContent;
        document.getElementById('detailModal').style.display = 'flex';
    }

    function editProject(projectId) {
        // Lógica para abrir formulario de edición
        alert(`Modo edición para proyecto ID: ${projectId}`);
        // Aquí deberías cargar el formulario de edición con los datos del proyecto
    }

    function confirmDelete(projectId) {
        if (confirm('¿Estás seguro de eliminar este proyecto?')) {
            deleteProject(projectId);
        }
    }

    function deleteProject(projectId) {
        // Simular eliminación
        const row = document.querySelector(`tr[data-project-id="${projectId}"]`);
        if (row) {
            row.remove();
            alert(`Proyecto ID: ${projectId} eliminado`);
        }
    }

    function closeModal() {
        document.getElementById('detailModal').style.display = 'none';
    }

    // Cerrar modal al hacer clic fuera
    window.onclick = function(event) {
        const modal = document.getElementById('detailModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>