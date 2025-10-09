<div class="container">
    <style>
        .gallery-manager {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 25px;
        }

        .upload-section {
            border: 2px dashed #ddd;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            height: fit-content;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .gallery-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s;
        }

        .gallery-item:hover {
            transform: translateY(-5px);
        }

        .item-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: none;
            gap: 5px;
        }

        .gallery-item:hover .item-actions {
            display: flex;
        }

        .category-list {
            list-style: none;
            padding: 0;
        }

        .category-item {
            padding: 10px;
            margin: 5px 0;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .metadata-editor {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            display: none;
            z-index: 1000;
            width: 90%;
            max-width: 600px;
        }

        .bulk-actions {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 15px 25px;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: none;
        }
    </style>

    <!-- Sidebar de Categorías -->
    <div class="gallery-sidebar">
        <h3>Categorías</h3>
        <ul class="category-list">
            <li class="category-item active">Todas</li>
            <li class="category-item">Eventos</li>
            <li class="category-item">Productos</li>
            <li class="category-item">Retratos</li>
            <button class="add-button" onclick="addNewCategory()">+ Nueva Categoría</button>
        </ul>
    </div>

    <!-- Área Principal -->
    <div class="gallery-main">
        <div class="upload-section" id="dropZone">
            <i class="material-icons" style="font-size: 48px;">cloud_upload</i>
            <h4>Arrastra imágenes aquí o haz clic para subir</h4>
            <input type="file" multiple accept="image/*" hidden>
        </div>

        <!-- Filtros y Búsqueda -->
        <div class="gallery-controls">
            <input type="search" placeholder="Buscar imágenes..." class="search-input">
            <select class="filter-select">
                <option>Ordenar por fecha</option>
                <option>Ordenar por nombre</option>
                <option>Ordenar por tamaño</option>
            </select>
        </div>

        <!-- Grid de Imágenes -->
        <div class="gallery-grid" id="galleryGrid">
            <div class="gallery-item">
                <img src="placeholder.jpg" alt="Imagen">
                <div class="item-actions">
                    <button class="icon-btn" onclick="showMetadata(1)">📝</button>
                    <button class="icon-btn" onclick="deleteImage(1)">🗑️</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Editor de Metadatos -->
    <div class="metadata-editor" id="metadataEditor">
        <h3>Editar Metadatos</h3>
        <div class="editor-content">
            <div class="image-preview">
                <img id="editPreview" src="" alt="Preview">
            </div>
            <div class="metadata-fields">
                <input type="text" placeholder="Título" id="imageTitle">
                <textarea placeholder="Descripción" id="imageDescription"></textarea>
                <input type="text" placeholder="Etiquetas (separar con comas)" id="imageTags">
                <button class="save-btn" onclick="saveMetadata()">💾 Guardar Cambios</button>
            </div>
        </div>
    </div>

    <!-- Acciones Masivas -->
    <div class="bulk-actions" id="bulkActions">
        <span id="selectedCount">0 seleccionadas</span>
        <button class="danger-btn" onclick="deleteSelected()">🗑️ Eliminar</button>
        <button class="action-btn" onclick="moveSelected()">📂 Mover a...</button>
        <button class="action-btn" onclick="downloadSelected()">📥 Descargar</button>
    </div>
</div>

<script>
    // Gestión de Archivos
    const dropZone = document.getElementById('dropZone');
    const fileInput = dropZone.querySelector('input');
    
    dropZone.addEventListener('click', () => fileInput.click());
    
    fileInput.addEventListener('change', handleFiles);
    dropZone.addEventListener('dragover', e => e.preventDefault());
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        handleFiles(e.dataTransfer.files);
    });

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const item = document.createElement('div');
                item.className = 'gallery-item';
                item.innerHTML = `
                    <img src="${e.target.result}" alt="${file.name}">
                    <div class="item-actions">
                        <button class="icon-btn" onclick="showMetadata('${file.name}')">📝</button>
                        <button class="icon-btn" onclick="deleteImage(this)">🗑️</button>
                    </div>
                `;
                document.getElementById('galleryGrid').appendChild(item);
            };
            reader.readAsDataURL(file);
        });
    }

    // Gestión de Selección
    let selectedItems = new Set();
    
    function toggleSelection(element) {
        element.classList.toggle('selected');
        const id = element.dataset.id;
        selectedItems.has(id) ? selectedItems.delete(id) : selectedItems.add(id);
        updateBulkActions();
    }

    function updateBulkActions() {
        const count = selectedItems.size;
        document.getElementById('selectedCount').textContent = `${count} seleccionadas`;
        document.getElementById('bulkActions').style.display = count > 0 ? 'flex' : 'none';
    }

    // Editor de Metadatos
    function showMetadata(imgSrc) {
        document.getElementById('editPreview').src = imgSrc;
        document.getElementById('metadataEditor').style.display = 'block';
    }

    function saveMetadata() {
        // Lógica para guardar metadatos
        document.getElementById('metadataEditor').style.display = 'none';
    }

    // Acciones Masivas
    function deleteSelected() {
        selectedItems.forEach(id => {
            document.querySelector(`[data-id="${id}"]`).remove();
        });
        selectedItems.clear();
        updateBulkActions();
    }
</script>