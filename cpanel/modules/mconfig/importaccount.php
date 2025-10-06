<?php
if(!accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'User', 'action' => 'import']])) {
        die('No tienes permisos para acceder a este módulo.');
    }
?>

<div class="container">
    <style>
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .upload-section {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            margin-bottom: 20px;
            transition: border-color 0.3s;
        }
        
        .upload-section:hover {
            border-color: #007bff;
        }
        
        .upload-section.dragover {
            border-color: #0056b3;
            background-color: #f8f9fa;
        }
        
        .file-input {
            display: none;
        }
        
        .import-button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .import-button:hover {
            background-color: #218838;
        }
        
        .file-info {
            margin: 15px 0;
            color: #666;
        }
        
        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin: 20px 0;
        }
        
        .progress {
            width: 0%;
            height: 100%;
            background-color: #007bff;
            transition: width 0.3s ease;
        }
        
        .import-options {
            margin: 20px 0;
            text-align: left;
        }
        
        .option-checkbox {
            margin: 10px 0;
        }
        
        .format-list {
            color: #666;
            font-size: 0.9em;
            margin-top: 10px;
        }
        
        .log-section {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-top: 20px;
            max-height: 200px;
            overflow-y: auto;
        }
        
        .log-entry {
            font-family: monospace;
            font-size: 0.85em;
            padding: 3px 0;
        }
        
        .log-success {
            color: #28a745;
        }
        
        .log-error {
            color: #dc3545;
        }
    </style>

    <div class="upload-section" id="dropZone">
        <i class="material-icons" style="font-size: 48px; color: #666;">cloud_upload</i>
        <h3>Arrastra archivos aquí o</h3>
        <button class="import-button" onclick="document.getElementById('fileInput').click()">
            Seleccionar archivo
        </button>
        <input type="file" class="file-input" id="fileInput" accept=".csv, .xlsx">
        <div class="format-list">Formatos soportados: CSV, XLSX (Max. 25MB)</div>
    </div>

    <div class="import-options">
        <h4>Opciones de importación:</h4>
        <label class="option-checkbox">
            <input type="checkbox" id="updateExisting"> Actualizar registros existentes
        </label>
        <br>
        <label class="option-checkbox">
            <input type="checkbox" id="sendEmail"> Enviar notificación por email
        </label>
    </div>

    <div class="file-info" id="fileInfo">
        Ningún archivo seleccionado
    </div>

    <div class="progress-bar">
        <div class="progress" id="progressBar"></div>
    </div>

    <div class="log-section" id="importLog">
        <div class="log-entry">Esperando archivo...</div>
    </div>
</div>

<script>
    // Manejo de selección de archivo
    document.getElementById('fileInput').addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });

    // Manejo de drag and drop
    const dropZone = document.getElementById('dropZone');
    
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });

    function handleFiles(files) {
        if (files.length > 0) {
            const file = files[0];
            document.getElementById('fileInfo').textContent = 
                `Archivo seleccionado: ${file.name} (${(file.size/1024/1024).toFixed(2)}MB)`;
            
            // Simular progreso de carga
            simulateUpload();
        }
    }

    function simulateUpload() {
        const progressBar = document.getElementById('progressBar');
        const logSection = document.getElementById('importLog');
        let progress = 0;
        
        const interval = setInterval(() => {
            progress += 10;
            progressBar.style.width = `${progress}%`;
            
            if (progress >= 100) {
                clearInterval(interval);
                logSection.innerHTML += `
                    <div class="log-entry log-success">✅ Importación completada con éxito</div>
                    <div class="log-entry">• 150 registros procesados</div>
                    <div class="log-entry">• 145 registros insertados</div>
                    <div class="log-entry">• 5 registros actualizados</div>
                `;
            }
        }, 300);
    }
</script>