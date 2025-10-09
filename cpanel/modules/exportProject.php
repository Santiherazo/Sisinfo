<?php
if(!accessManager()->canAccess($_SESSION['userid'], ['Administrador', 'Coordinador'], [['module' => 'Cpproject', 'action' => 'export']])) {
    die('No tienes permisos para acceder a este módulo.');
}
?>

<div id="export-module" class="module-content fade-in">
              <div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6">
                  <div class="flex items-center justify-between mb-6">
                      <div>
                          <h2 class="text-2xl font-bold text-slate-900 mb-2">Exportar Proyectos</h2>
                          <p class="text-slate-600">Exporte proyectos a diferentes formatos</p>
                      </div>
                  </div>

                  <!-- Export Options -->
                  <div class="grid gap-6 md:grid-cols-2 mb-8">
                      <!-- Format Selection -->
                      <div class="glassmorphism rounded-xl p-6">
                          <h3 class="text-lg font-semibold text-slate-900 mb-4">Formato de Exportación</h3>
                          <div class="space-y-4">
                              <label class="flex items-center gap-4 p-4 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all cursor-pointer">
                                  <input type="radio" name="export-format" value="csv" checked class="w-5 h-5 text-blue-600">
                                  <div class="flex-1">
                                      <p class="font-medium text-slate-900">CSV (Valores separados por comas)</p>
                                      <p class="text-sm text-slate-600">Ideal para hojas de cálculo</p>
                                  </div>
                                  <i data-lucide="file-text" class="w-6 h-6 text-blue-500"></i>
                              </label>
                              
                              <label class="flex items-center gap-4 p-4 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all cursor-pointer">
                                  <input type="radio" name="export-format" value="excel" class="w-5 h-5 text-blue-600">
                                  <div class="flex-1">
                                      <p class="font-medium text-slate-900">Excel (XLSX)</p>
                                      <p class="text-sm text-slate-600">Formato de Microsoft Excel</p>
                                  </div>
                                  <i data-lucide="file-spreadsheet" class="w-6 h-6 text-green-500"></i>
                              </label>
                              
                              <label class="flex items-center gap-4 p-4 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all cursor-pointer">
                                  <input type="radio" name="export-format" value="json" class="w-5 h-5 text-blue-600">
                                  <div class="flex-1">
                                      <p class="font-medium text-slate-900">JSON</p>
                                      <p class="text-sm text-slate-600">Estructura de datos para desarrolladores</p>
                                  </div>
                                  <i data-lucide="file-code" class="w-6 h-6 text-purple-500"></i>
                              </label>
                              
                              <label class="flex items-center gap-4 p-4 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all cursor-pointer">
                                  <input type="radio" name="export-format" value="pdf" class="w-5 h-5 text-blue-600">
                                  <div class="flex-1">
                                      <p class="font-medium text-slate-900">PDF</p>
                                      <p class="text-sm text-slate-600">Documento imprimible</p>
                                  </div>
                                  <i data-lucide="file-text" class="w-6 h-6 text-red-500"></i>
                              </label>
                          </div>
                      </div>

                      <!-- Content Selection -->
                      <div class="glassmorphism rounded-xl p-6">
                          <h3 class="text-lg font-semibold text-slate-900 mb-4">Contenido a Exportar</h3>
                          <div class="space-y-4">
                              <div class="flex items-center justify-between">
                                  <div>
                                      <p class="font-medium text-slate-900">Información Básica</p>
                                      <p class="text-sm text-slate-600">Título, descripción, estado</p>
                                  </div>
                                  <label class="switch">
                                      <input type="checkbox" checked>
                                      <span class="slider"></span>
                                  </label>
                              </div>
                              
                              <div class="flex items-center justify-between">
                                  <div>
                                      <p class="font-medium text-slate-900">Metadatos</p>
                                      <p class="text-sm text-slate-600">Fechas, palabras clave, líneas de investigación</p>
                                  </div>
                                  <label class="switch">
                                      <input type="checkbox" checked>
                                      <span class="slider"></span>
                                  </label>
                              </div>
                              
                              <div class="flex items-center justify-between">
                                  <div>
                                      <p class="font-medium text-slate-900">Equipo</p>
                                      <p class="text-sm text-slate-600">Estudiantes, directores, evaluadores</p>
                                  </div>
                                  <label class="switch">
                                      <input type="checkbox">
                                      <span class="slider"></span>
                                  </label>
                              </div>
                              
                              <div class="flex items-center justify-between">
                                  <div>
                                      <p class="font-medium text-slate-900">Evaluaciones</p>
                                      <p class="text-sm text-slate-600">Resultados y comentarios</p>
                                  </div>
                                  <label class="switch">
                                      <input type="checkbox">
                                      <span class="slider"></span>
                                  </label>
                              </div>
                              
                              <div class="flex items-center justify-between">
                                  <div>
                                      <p class="font-medium text-slate-900">Documentos</p>
                                      <p class="text-sm text-slate-600">Incluir archivos adjuntos (ZIP)</p>
                                  </div>
                                  <label class="switch">
                                      <input type="checkbox">
                                      <span class="slider"></span>
                                  </label>
                              </div>
                          </div>
                      </div>
                  </div>

                  <!-- Filters -->
                  <div class="glassmorphism rounded-xl p-6 mb-8">
                      <h3 class="text-lg font-semibold text-slate-900 mb-4">Filtros</h3>
                      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                          <div>
                              <label class="block text-sm font-medium text-slate-700 mb-2">Estado</label>
                              <select class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                  <option>Todos los estados</option>
                                  <option>Propuesta</option>
                                  <option>Desarrollo</option>
                                  <option>Aplicación</option>
                                  <option>Finalizado</option>
                              </select>
                          </div>
                          
                          <div>
                              <label class="block text-sm font-medium text-slate-700 mb-2">Línea de Investigación</label>
                              <select class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                  <option>Todas las líneas</option>
                                  <option>Ingeniería del Software</option>
                                  <option>Inteligencia Artificial</option>
                                  <option>Seguridad Informática</option>
                                  <option>Redes y Telemática</option>
                              </select>
                          </div>
                          
                          <div>
                              <label class="block text-sm font-medium text-slate-700 mb-2">Fecha de Creación</label>
                              <select class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                  <option>Todas las fechas</option>
                                  <option>Últimos 7 días</option>
                                  <option>Últimos 30 días</option>
                                  <option>Este año</option>
                                  <option>Personalizado...</option>
                              </select>
                          </div>
                      </div>
                  </div>

                  <!-- Preview Info -->
                  <div class="glassmorphism rounded-xl p-6 mb-8 bg-blue-50/30 border border-blue-200">
                      <div class="flex items-center gap-4">
                          <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                              <i data-lucide="info" class="w-6 h-6 text-blue-600"></i>
                          </div>
                          <div>
                              <h4 class="font-medium text-slate-900">Previsualización</h4>
                              <p class="text-sm text-slate-600">Se exportarán <span class="font-semibold text-blue-600">87 proyectos</span> con la configuración actual</p>
                          </div>
                      </div>
                  </div>

                  <!-- Action Buttons -->
                  <div class="flex gap-4 justify-between">
                      <button onclick="cancelExport()" class="px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all font-medium">
                          Cancelar
                      </button>
                      <div class="flex gap-4">
                          <button onclick="previewExport()" class="px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all font-medium flex items-center gap-2">
                              <i data-lucide="eye" class="w-4 h-4"></i>
                              Vista Previa
                          </button>
                          <button onclick="startExport()" class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all font-medium shadow-lg flex items-center gap-2">
                              <i data-lucide="download" class="w-4 h-4"></i>
                              Exportar Proyectos
                          </button>
                      </div>
                  </div>
              </div>
          </div>

           <script>
      // Initialize Lucide icons
      lucide.createIcons();

      // Preview export
      function previewExport() {
          const format = document.querySelector('input[name="export-format"]:checked').value;
          console.log('Generando vista previa en formato:', format);
          // Aquí iría la lógica para generar la vista previa
          alert('Vista previa generada en formato ' + format.toUpperCase());
      }

      // Start export process
      function startExport() {
          const format = document.querySelector('input[name="export-format"]:checked').value;
          console.log('Iniciando exportación en formato:', format);
          
          // Mostrar loader
          const exportBtn = document.querySelector('[onclick="startExport()"]');
          const originalText = exportBtn.innerHTML;
          exportBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Exportando...';
          
          // Simular proceso de exportación
          setTimeout(() => {
              exportBtn.innerHTML = originalText;
              lucide.createIcons(); // Recrear íconos después de cambiar el HTML
              alert('Exportación completada en formato ' + format.toUpperCase());
          }, 2000);
      }

      // Cancel export
      function cancelExport() {
          if (confirm('¿Está seguro de que desea cancelar la exportación?')) {
              window.location.href = '#projects';
          }
      }

      // Initialize page
      document.addEventListener('DOMContentLoaded', function() {
          console.log('Vista de exportación de proyectos inicializada');
      });
  </script>
