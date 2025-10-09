<div class="container">
    <style>
        .hero-builder {
            display: grid;
            grid-template-columns: 3fr 2fr;
            gap: 30px;
            padding: 20px;
        }

        .hero-preview {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 40px;
            min-height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: all 0.3s;
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .controls-section {
            border-left: 2px solid #eee;
            padding-left: 30px;
        }

        .control-group {
            margin-bottom: 25px;
        }

        .style-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .color-picker {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            border: 2px solid #ddd;
            cursor: pointer;
        }

        .button-preview {
            display: inline-flex;
            gap: 10px;
            margin: 15px 0;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
        }

        .image-upload {
            border: 2px dashed #ddd;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            cursor: pointer;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 28px;
        }
    </style>

    <div class="hero-builder">
        <!-- Sección de Preview -->
        <div class="hero-preview" id="heroPreview">
            <div class="hero-content">
                <h1 id="heroTitle" style="font-size: 2.5rem; margin-bottom: 1rem;">Welcome to Our Platform</h1>
                <p id="heroSubtitle" style="font-size: 1.2rem; margin-bottom: 2rem;">Create something amazing with our tools</p>
                <div id="heroButtons" class="button-container">
                    <div class="button-preview" style="background: #007bff; color: white;">Get Started</div>
                    <div class="button-preview" style="background: #e9ecef; color: #333;">Learn More</div>
                </div>
            </div>
        </div>

        <!-- Controles de Edición -->
        <div class="controls-section">
            <div class="control-group">
                <h3>Contenido Principal</h3>
                <input type="text" class="form-input" id="heroTitleInput" placeholder="Título principal" value="Welcome to Our Platform">
                <textarea class="form-input" id="heroSubtitleInput" placeholder="Subtítulo" rows="3">Create something amazing with our tools</textarea>
            </div>

            <div class="control-group">
                <h3>Botones</h3>
                <div id="buttonControls">
                    <div class="button-control">
                        <input type="text" placeholder="Texto del botón" value="Get Started">
                        <input type="color" value="#007bff">
                        <button class="delete-btn">×</button>
                    </div>
                </div>
                <button class="add-button" onclick="addNewButton()">+ Add Button</button>
            </div>

            <div class="control-group">
                <h3>Estilo del Hero</h3>
                <div class="style-options">
                    <div>
                        <label>Tipo de Fondo</label>
                        <select class="form-input" id="bgType">
                            <option value="color">Color Sólido</option>
                            <option value="gradient">Gradiente</option>
                            <option value="image">Imagen</option>
                        </select>
                    </div>
                    
                    <div>
                        <label>Alineación</label>
                        <select class="form-input" id="textAlignment">
                            <option value="left">Izquierda</option>
                            <option value="center" selected>Centro</option>
                            <option value="right">Derecha</option>
                        </select>
                    </div>
                </div>

                <div class="color-controls">
                    <div>
                        <label>Color de Fondo</label>
                        <input type="color" id="bgColor" value="#ffffff">
                    </div>
                    <div>
                        <label>Color de Texto</label>
                        <input type="color" id="textColor" value="#333333">
                    </div>
                </div>

                <div class="image-upload" id="imageUpload">
                    <p>Arrastra imagen o haz clic para subir</p>
                    <input type="file" hidden accept="image/*">
                </div>
            </div>

            <div class="control-group">
                <h3>Configuraciones Avanzadas</h3>
                <label>
                    <input type="checkbox" id="parallaxEffect"> Efecto Parallax
                </label>
                <label>
                    <input type="checkbox" id="overlay"> Overlay
                </label>
            </div>
        </div>
    </div>
</div>

<script>
    // Actualización en tiempo real
    const updatePreview = () => {
        document.getElementById('heroTitle').textContent = document.getElementById('heroTitleInput').value;
        document.getElementById('heroSubtitle').textContent = document.getElementById('heroSubtitleInput').value;
        document.getElementById('heroPreview').style.textAlign = document.getElementById('textAlignment').value;
        document.getElementById('heroPreview').style.backgroundColor = document.getElementById('bgColor').value;
        document.getElementById('heroTitle').style.color = document.getElementById('textColor').value;
        document.getElementById('heroSubtitle').style.color = document.getElementById('textColor').value;
    };

    // Event listeners
    document.querySelectorAll('.form-input, input[type="color"]').forEach(input => {
        input.addEventListener('input', updatePreview);
    });

    // Gestión de botones
    const addNewButton = () => {
        const buttonControl = document.createElement('div');
        buttonControl.className = 'button-control';
        buttonControl.innerHTML = `
            <input type="text" placeholder="Texto del botón" value="New Button">
            <input type="color" value="#007bff">
            <button class="delete-btn" onclick="this.parentElement.remove()">×</button>
        `;
        document.getElementById('buttonControls').appendChild(buttonControl);
    };

    // Gestión de imagen de fondo
    document.getElementById('imageUpload').addEventListener('click', () => {
        document.querySelector('#imageUpload input[type="file"]').click();
    });

    document.querySelector('#imageUpload input[type="file"]').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('heroPreview').style.backgroundImage = `url(${e.target.result})`;
            };
            reader.readAsDataURL(file);
        }
    });

    // Inicialización
    updatePreview();
</script>