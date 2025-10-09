<div class="container">
    <style>
        .blog-manager {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
        }

        .post-list {
            border-right: 2px solid #eee;
            padding-right: 25px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .post-editor {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .post-card {
            padding: 15px;
            margin: 10px 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            cursor: pointer;
            transition: all 0.3s;
        }

        .post-card:hover {
            transform: translateX(5px);
        }

        .editor-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .editor-toolbar {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .tag-input {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .tag-item {
            background: #e9ecef;
            padding: 4px 10px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .featured-image {
            border: 2px dashed #ddd;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            position: relative;
            margin-bottom: 20px;
        }

        .status-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.8em;
        }
    </style>

    <!-- Lista de Posts -->
    <div class="post-list">
        <div class="search-controls">
            <input type="search" placeholder="Buscar posts..." class="search-input">
            <button class="new-post-btn" onclick="createNewPost()">+ Nuevo Post</button>
        </div>

        <div class="posts-container">
            <div class="post-card published">
                <div class="status-indicator" style="background: #d4edda; color: #155724;">Publicado</div>
                <h4>Guía completa de desarrollo web</h4>
                <p class="post-meta">Por: John Doe | 15 comentarios</p>
                <div class="post-actions">
                    <button class="icon-btn" onclick="editPost(1)">✏️</button>
                    <button class="icon-btn" onclick="deletePost(1)">🗑️</button>
                    <button class="icon-btn" onclick="previewPost(1)">👁️</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Editor de Posts -->
    <div class="post-editor">
        <div class="editor-header">
            <h2>Nuevo Post</h2>
            <div class="editor-status">
                <select class="status-select">
                    <option value="draft">Borrador</option>
                    <option value="published">Publicado</option>
                    <option value="scheduled">Programado</option>
                </select>
                <button class="save-btn" onclick="savePost()">💾 Guardar</button>
                <button class="publish-btn" onclick="publishPost()">🚀 Publicar</button>
            </div>
        </div>

        <input type="text" class="post-title" placeholder="Título del post">

        <div class="featured-image">
            <img id="featuredPreview" src="" alt="Imagen destacada" style="display: none;">
            <p>Arrastra imagen destacada o haz clic para subir</p>
            <input type="file" accept="image/*" hidden>
        </div>

        <div class="editor-toolbar">
            <button class="format-btn" onclick="formatText('bold')">B</button>
            <button class="format-btn" onclick="formatText('italic')">I</button>
            <select class="heading-select" onchange="formatHeading(this.value)">
                <option value="p">Párrafo</option>
                <option value="h2">Título 2</option>
                <option value="h3">Título 3</option>
            </select>
        </div>

        <textarea class="post-content" placeholder="Escribe tu contenido aquí..." rows="15"></textarea>

        <div class="post-settings">
            <div class="setting-group">
                <h4>Configuraciones</h4>
                <label>URL amigable:
                    <input type="text" class="slug-input" placeholder="generar-slug">
                </label>
                
                <label>Fecha de publicación:
                    <input type="datetime-local" class="publish-date">
                </label>
            </div>

            <div class="setting-group">
                <h4>Categorías</h4>
                <div class="category-select">
                    <label><input type="checkbox"> Tecnología</label>
                    <label><input type="checkbox"> Diseño</label>
                    <label><input type="checkbox"> Marketing</label>
                </div>
            </div>

            <div class="setting-group">
                <h4>Etiquetas</h4>
                <div class="tag-input">
                    <input type="text" placeholder="Añadir etiqueta" class="new-tag">
                    <button class="add-tag-btn" onclick="addTag()">+</button>
                </div>
                <div class="tags-container"></div>
            </div>

            <div class="setting-group">
                <h4>SEO</h4>
                <input type="text" placeholder="Meta título" class="meta-title">
                <textarea placeholder="Meta descripción" class="meta-description"></textarea>
                <p class="char-count">0/160 caracteres</p>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="preview-modal" id="previewModal">
        <div class="modal-content">
            <h3>Vista Previa</h3>
            <div id="postPreview"></div>
            <button class="close-btn" onclick="closePreview()">×</button>
        </div>
    </div>
</div>

<script>
    // Gestión de Posts
    let currentPost = null;
    
    function createNewPost() {
        currentPost = {
            id: Date.now(),
            title: '',
            content: '',
            status: 'draft',
            categories: [],
            tags: []
        };
        resetEditor();
    }

    function savePost() {
        // Lógica para guardar post
        alert('Post guardado correctamente');
    }

    function publishPost() {
        // Lógica para publicar post
        alert('Post publicado');
    }

    // Gestión de Imágenes
    document.querySelector('.featured-image input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('featuredPreview').src = e.target.result;
                document.getElementById('featuredPreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Gestión de Etiquetas
    function addTag() {
        const tagInput = document.querySelector('.new-tag');
        if (tagInput.value.trim()) {
            const tag = document.createElement('div');
            tag.className = 'tag-item';
            tag.innerHTML = `
                ${tagInput.value}
                <button onclick="this.parentElement.remove()">×</button>
            `;
            document.querySelector('.tags-container').appendChild(tag);
            tagInput.value = '';
        }
    }

    // Vista Previa
    function previewPost() {
        document.getElementById('postPreview').innerHTML = `
            <h2>${document.querySelector('.post-title').value}</h2>
            ${document.querySelector('.post-content').value}
        `;
        document.getElementById('previewModal').style.display = 'block';
    }

    function closePreview() {
        document.getElementById('previewModal').style.display = 'none';
    }

    // Formateo de Texto
    function formatText(style) {
        const content = document.querySelector('.post-content');
        const start = content.selectionStart;
        const end = content.selectionEnd;
        const selectedText = content.value.substring(start, end);
        
        const formats = {
            bold: `**${selectedText}**`,
            italic: `_${selectedText}_`
        };
        
        content.value = content.value.substring(0, start) + formats[style] + content.value.substring(end);
    }

    // Inicialización
    createNewPost();
</script>