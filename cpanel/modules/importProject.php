<?php
if(!accessManager()->canAccess($_SESSION['userid'], ['Administrador', 'Coordinador'], [['module' => 'Cpproject', 'action' => 'import']])) {
    die('No tienes permisos para acceder a este módulo.');
}
?>
<div id="import-module" class="module-content fade-in">
              <div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6">
                  <div class="flex items-center justify-between mb-6">
                      <div>
                          <h2 class="text-2xl font-bold text-slate-900 mb-2">Importar Proyectos</h2>
                          <p class="text-slate-600">Importe proyectos desde archivos CSV o Excel</p>
                      </div>
                      <div class="flex items-center gap-2">
                          <button onclick="downloadTemplate()" class="flex items-center gap-2 px-4 py-2 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all">
                              <i data-lucide="download" class="w-4 h-4"></i>
                              Descargar Plantilla
                          </button>
                      </div>
                  </div>

                  <!-- Import Steps -->
                  <div class="mb-8">
                      <div class="flex items-center justify-between mb-6">
                          <div class="flex flex-col items-center">
                              <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold mb-1">1</div>
                              <span class="text-sm font-medium text-blue-600">Seleccionar Archivo</span>
                          </div>
                          <div class="flex-1 h-1 bg-blue-200 mx-2"></div>
                          <div class="flex flex-col items-center">
                              <div class="w-10 h-10 bg-slate-200 rounded-full flex items-center justify-center text-slate-600 font-bold mb-1">2</div>
                              <span class="text-sm font-medium text-slate-600">Mapear Campos</span>
                          </div>
                          <div class="flex-1 h-1 bg-slate-200 mx-2"></div>
                          <div class="flex flex-col items-center">
                              <div class="w-10 h-10 bg-slate-200 rounded-full flex items-center justify-center text-slate-600 font-bold mb-1">3</div>
                              <span class="text-sm font-medium text-slate-600">Confirmar</span>
                          </div>
                      </div>
                  </div>

                  <!-- File Upload Area -->
                  <div class="glassmorphism rounded-xl p-8 border-2 border-dashed border-blue-300 bg-blue-50/30 mb-8 text-center">
                      <div class="max-w-md mx-auto">
                          <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                              <i data-lucide="upload-cloud" class="w-8 h-8 text-blue-600"></i>
                          </div>
                          <h3 class="text-lg font-semibold text-slate-900 mb-2">Arrastra y suelta tu archivo aquí</h3>
                          <p class="text-sm text-slate-600 mb-4">o</p>
                          <label for="file-upload" class="cursor-pointer">
                              <span class="flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg mx-auto w-fit">
                                  <i data-lucide="folder-open" class="w-4 h-4"></i>
                                  Seleccionar archivo
                              </span>
                              <input id="file-upload" type="file" accept=".csv,.xlsx,.xls" class="hidden" onchange="handleFileSelect(event)">
                          </label>
                          <p class="text-xs text-slate-500 mt-4">Formatos soportados: CSV, XLSX (Excel)</p>
                      </div>
                  </div>

                  <!-- File Info (hidden by default) -->
                  <div id="file-info" class="hidden glassmorphism rounded-xl p-6 mb-8">
                      <div class="flex items-center justify-between">
                          <div class="flex items-center gap-4">
                              <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                  <i data-lucide="file-text" class="w-6 h-6 text-green-600"></i>
                              </div>
                              <div>
                                  <h4 id="file-name" class="font-medium text-slate-900">proyectos_2024.csv</h4>
                                  <p id="file-size" class="text-sm text-slate-600">1.2 MB</p>
                              </div>
                          </div>
                          <button onclick="removeFile()" class="p-2 hover:bg-slate-100 rounded-lg transition-colors text-slate-500">
                              <i data-lucide="trash-2" class="w-5 h-5"></i>
                          </button>
                      </div>
                  </div>

                  <!-- Import Options -->
                  <div class="glassmorphism rounded-xl p-6 mb-8">
                      <h3 class="text-lg font-semibold text-slate-900 mb-4">Opciones de Importación</h3>
                      <div class="space-y-4">
                          <div class="flex items-center justify-between">
                              <div>
                                  <p class="font-medium text-slate-900">Actualizar proyectos existentes</p>
                                  <p class="text-sm text-slate-600">Si un proyecto ya existe, actualizar sus datos</p>
                              </div>
                              <label class="switch">
                                  <input type="checkbox" id="update-existing">
                                  <span class="slider"></span>
                              </label>
                          </div>
                          <div class="flex items-center justify-between">
                              <div>
                                  <p class="font-medium text-slate-900">Ignorar errores</p>
                                  <p class="text-sm text-slate-600">Continuar importación aunque haya errores en algunos registros</p>
                              </div>
                              <label class="switch">
                                  <input type="checkbox" id="ignore-errors">
                                  <span class="slider"></span>
                              </label>
                          </div>
                          <div>
                              <label class="block text-sm font-medium text-slate-700 mb-2">Formato de fecha</label>
                              <select class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                  <option>DD/MM/AAAA</option>
                                  <option>MM/DD/AAAA</option>
                                  <option>AAAA-MM-DD</option>
                              </select>
                          </div>
                      </div>
                  </div>

                  <!-- Action Buttons -->
                  <div class="flex gap-4 justify-end">
                      <button onclick="cancelImport()" class="px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all font-medium">
                          Cancelar
                      </button>
                      <button id="continue-btn" onclick="continueToMapping()" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all font-medium shadow-lg opacity-50 cursor-not-allowed" disabled>
                          Continuar
                      </button>
                  </div>
              </div>
          </div>

          <script>
      // Initialize Lucide icons
      lucide.createIcons();

      // Handle file selection
      function handleFileSelect(event) {
          const file = event.target.files[0];
          if (file) {
              // Show file info
              document.getElementById('file-info').classList.remove('hidden');
              document.getElementById('file-name').textContent = file.name;
              document.getElementById('file-size').textContent = formatFileSize(file.size);
              
              // Enable continue button
              document.getElementById('continue-btn').removeAttribute('disabled');
              document.getElementById('continue-btn').classList.remove('opacity-50', 'cursor-not-allowed');
              
              console.log('Archivo seleccionado:', file.name);
          }
      }

      // Remove selected file
      function removeFile() {
          document.getElementById('file-upload').value = '';
          document.getElementById('file-info').classList.add('hidden');
          document.getElementById('continue-btn').setAttribute('disabled', 'true');
          document.getElementById('continue-btn').classList.add('opacity-50', 'cursor-not-allowed');
      }

      // Format file size
      function formatFileSize(bytes) {
          if (bytes === 0) return '0 Bytes';
          const k = 1024;
          const sizes = ['Bytes', 'KB', 'MB', 'GB'];
          const i = Math.floor(Math.log(bytes) / Math.log(k));
          return parseFloat((bytes / Math.pow(k, i)).toFixed(1) + ' ' + sizes[i]);
      }

      // Download template
      function downloadTemplate() {
          console.log('Descargando plantilla...');
          // Aquí iría la lógica para descargar la plantilla
          alert('Plantilla descargada exitosamente');
      }

      // Continue to mapping step
      function continueToMapping() {
          console.log('Continuando a mapeo de campos...');
          // Aquí iría la lógica para avanzar al siguiente paso
          alert('Archivo cargado, procederemos a mapear campos');
      }

      // Cancel import
      function cancelImport() {
          if (confirm('¿Está seguro de que desea cancelar la importación?')) {
              window.location.href = '#projects';
          }
      }

      // Initialize page
      document.addEventListener('DOMContentLoaded', function() {
          console.log('Vista de importación de proyectos inicializada');
      });
  </script>