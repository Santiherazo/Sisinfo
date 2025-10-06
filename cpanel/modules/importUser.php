<?php
if(!accessManager()->canAccess($_SESSION['userid'], ['Administrador', 'Coordinador'], [['module' => 'Cpuser', 'action' => 'import']])) {
    die('No tienes permisos para acceder a este módulo.');
}

$logger = new ErrorLogger();

$logger->log('Iniciando proceso de importación de usuarios', __FILE__, __LINE__);

$roles = $roleManager->getAllRoles();
$roleMap = [];
foreach ($roles as $role) {
    $roleMap[strtolower($role[_CLMN_ROLE_NAME_])] = $role[_CLMN_ROLE_ID_];
}
$logger->log('Roles cargados: ' . count($roleMap), __FILE__, __LINE__);

$uploadManager = new uploadManager(__PATH_UPLOADS__);
$uploadManager->setAllowedExtensions(['csv']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['import_file'])) {
    $file = $_FILES['import_file'];
    $logger->log('Archivo recibido: ' . $file['name'] . ', tamaño: ' . $file['size'] . ', error: ' . $file['error'], __FILE__, __LINE__);
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMsg = 'Error al subir el archivo: ' . $uploadManager->getUploadError($file['error']);
        $logger->log($errorMsg, __FILE__, __LINE__);
        echo json_encode(['success' => false, 'message' => $errorMsg]);
        exit;
    }
    
    $filePath = $uploadManager->upload($file, $_SESSION['userid'], 'imports');
    
    if ($filePath === false) {
        $errors = $uploadManager->getErrors();
        $errorMsg = 'Error: ' . implode(', ', $errors);
        $logger->log($errorMsg, __FILE__, __LINE__);
        echo json_encode(['success' => false, 'message' => $errorMsg]);
        exit;
    }
    
    $_SESSION['import_file'] = $filePath;
    $logger->log('Archivo subido exitosamente: ' . $filePath, __FILE__, __LINE__);
    
    try {
        $previewData = processFilePreview($filePath);
        $logger->log('Previsualización generada: ' . count($previewData) . ' registros', __FILE__, __LINE__);
        echo json_encode(['success' => true, 'data' => $previewData]);
    } catch (Exception $e) {
        $logger->log('Error en previsualización: ' . $e->getMessage(), __FILE__, __LINE__);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if (isset($_GET['preview'])) {
    if (!isset($_SESSION['import_file']) || !file_exists($_SESSION['import_file'])) {
        $logger->log('No hay archivo para previsualizar', __FILE__, __LINE__);
        die('No hay archivo para previsualizar');
    }
    
    try {
        $previewData = processFilePreview($_SESSION['import_file']);
        $logger->log('Previsualización cargada: ' . count($previewData) . ' registros', __FILE__, __LINE__);
        echo json_encode($previewData);
    } catch (Exception $e) {
        $logger->log('Error cargando previsualización: ' . $e->getMessage(), __FILE__, __LINE__);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_import'])) {
    if (!isset($_SESSION['import_file']) || !file_exists($_SESSION['import_file'])) {
        $logger->log('No hay archivo para procesar', __FILE__, __LINE__);
        die('No hay archivo para procesar');
    }
    
    try {
        $logger->log('Iniciando procesamiento de importación', __FILE__, __LINE__);
        $results = processImportFile($_SESSION['import_file'], $roleMap);
        
        if (file_exists($_SESSION['import_file'])) {
            unlink($_SESSION['import_file']);
        }
        unset($_SESSION['import_file']);
        
        $logger->log('Importación completada: ' . $results['success'] . ' éxitos, ' . $results['errors'] . ' errores', __FILE__, __LINE__);
        echo json_encode($results);
    } catch (Exception $e) {
        $logger->log('Error en procesamiento: ' . $e->getMessage(), __FILE__, __LINE__);
        echo json_encode(['success' => 0, 'errors' => 1, 'messages' => [$e->getMessage()]]);
    }
    exit;
}

if (isset($_GET['cancel'])) {
    if (isset($_SESSION['import_file']) && file_exists($_SESSION['import_file'])) {
        unlink($_SESSION['import_file']);
        $logger->log('Importación cancelada, archivo eliminado', __FILE__, __LINE__);
    }
    unset($_SESSION['import_file']);
    $logger->log('Importación cancelada por el usuario', __FILE__, __LINE__);
    echo json_encode(['success' => true]);
    exit;
}

function processFilePreview($filePath) {
    global $logger;
    $logger->log('Procesando previsualización del archivo: ' . $filePath, __FILE__, __LINE__);
    $data = [];
    
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        $headers = fgetcsv($handle, 1000, ",");
        if ($headers === FALSE) {
            throw new Exception("El archivo CSV está vacío o no tiene formato válido");
        }
        $headers = array_map('trim', $headers);
        $headers = array_map('strtolower', $headers);
        $logger->log('Encabezados detectados: ' . implode(', ', $headers), __FILE__, __LINE__);
        
        $count = 0;
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE && $count < 10) {
            if (count($headers) === count($row)) {
                $data[] = array_combine($headers, $row);
                $count++;
            }
        }
        fclose($handle);
    }
    
    $logger->log('Previsualización generada con ' . count($data) . ' registros', __FILE__, __LINE__);
    return $data;
}

function processImportFile($filePath, $roleMap) {
    global $profileManager, $logger;
    $logger->log('Procesando importación completa del archivo: ' . $filePath, __FILE__, __LINE__);
    
    $data = [];
    
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        $headers = fgetcsv($handle, 1000, ",");
        if ($headers === FALSE) {
            throw new Exception("El archivo CSV está vacío o no tiene formato válido");
        }
        $headers = array_map('trim', $headers);
        $headers = array_map('strtolower', $headers);
        $logger->log('Encabezados para importación: ' . implode(', ', $headers), __FILE__, __LINE__);
        
        $rowCount = 0;
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($headers) === count($row)) {
                $data[] = array_combine($headers, $row);
                $rowCount++;
            }
        }
        fclose($handle);
        $logger->log('Total de filas a procesar: ' . $rowCount, __FILE__, __LINE__);
    }
    
    $results = ['success' => 0, 'errors' => 0, 'messages' => []];
    $required_fields = ['first_name', 'last_name', 'id_number', 'email', 'id_type', 'role'];
    
    foreach ($data as $index => $row) {
        try {
            $row = array_map('trim', $row);
            $logger->log('Procesando fila ' . ($index + 1) . ': ' . json_encode($row), __FILE__, __LINE__);
            
            foreach ($required_fields as $field) {
                if (empty($row[$field])) {
                    $errorMsg = "Falta el campo obligatorio: $field";
                    $logger->log($errorMsg . ' en fila ' . ($index + 1), __FILE__, __LINE__);
                    throw new Exception($errorMsg);
                }
            }
            
            if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                $errorMsg = "Email inválido: " . $row['email'];
                $logger->log($errorMsg . ' en fila ' . ($index + 1), __FILE__, __LINE__);
                throw new Exception($errorMsg);
            }
            
            $roleId = null;
            if (is_numeric($row['role'])) {
                $roleId = intval($row['role']);
                $logger->log('Rol numérico detectado: ' . $roleId . ' en fila ' . ($index + 1), __FILE__, __LINE__);
            } else {
                $roleName = strtolower(trim($row['role']));
                if (isset($roleMap[$roleName])) {
                    $roleId = $roleMap[$roleName];
                    $logger->log('Rol por nombre detectado: ' . $roleName . ' -> ' . $roleId . ' en fila ' . ($index + 1), __FILE__, __LINE__);
                } else {
                    $errorMsg = "Rol no válido: " . $row['role'];
                    $logger->log($errorMsg . ' en fila ' . ($index + 1), __FILE__, __LINE__);
                    throw new Exception($errorMsg);
                }
            }
            
            $formData = [
                'first_name' => $row['first_name'],
                'middle_name' => $row['middle_name'] ?? '',
                'last_name' => $row['last_name'],
                'second_last_name' => $row['second_last_name'] ?? '',
                'birth_date' => !empty($row['birth_date']) ? formatDate($row['birth_date']) : null,
                'gender' => $row['gender'] ?? null,
                'country' => $row['country'] ?? '',
                'city' => $row['city'] ?? '',
                'address' => $row['address'] ?? '',
                'id_type' => $row['id_type'],
                'id_number' => $row['id_number'],
                'university' => $row['university'] ?? '',
                'program' => $row['program'] ?? '',
                'semester' => !empty($row['semester']) ? intval($row['semester']) : null,
                'phone_number' => $row['phone_number'] ?? '',
                'institutional_email' => $row['institutional_email'] ?? '',
                'card_code' => $row['card_code'] ?? ''
            ];
            
            $password = $row['password'] ?? '';
            $confirm_password = $row['confirm_password'] ?? $password;
            $email = $row['email'];
            
            if (empty($password) || empty($confirm_password)) {
                $password = $confirm_password = generateRandomPassword(12);
                $logger->log('Contraseña generada automáticamente para fila ' . ($index + 1), __FILE__, __LINE__);
            } elseif ($password !== $confirm_password) {
                $errorMsg = "Las contraseñas no coinciden";
                $logger->log($errorMsg . ' en fila ' . ($index + 1), __FILE__, __LINE__);
                throw new Exception($errorMsg);
            }
            
            $username = strtolower(
                substr($formData['first_name'], 0, 3) . 
                substr($formData['last_name'], 0, 3) . 
                substr($formData['id_number'], -3)
            );
            $logger->log('Username generado: ' . $username . ' para fila ' . ($index + 1), __FILE__, __LINE__);
            
            $userId = $profileManager->registerFullUser(
                $username,
                $email,
                $password,
                $formData,
                $roleId
            );
            
            if (!$userId) {
                $errorMsg = "No se pudo registrar el usuario";
                $logger->log($errorMsg . ' en fila ' . ($index + 1), __FILE__, __LINE__);
                throw new Exception($errorMsg);
            }
            
            $logger->log('Usuario registrado exitosamente: ' . $userId . ' en fila ' . ($index + 1), __FILE__, __LINE__);
            $results['success']++;
            
        } catch (Exception $e) {
            $results['errors']++;
            $errorMsg = "Error en fila " . ($index + 1) . ": " . $e->getMessage();
            $results['messages'][] = $errorMsg;
            $logger->log($errorMsg, __FILE__, __LINE__);
        }
    }
    
    $logger->log('Procesamiento finalizado: ' . $results['success'] . ' éxitos, ' . $results['errors'] . ' errores', __FILE__, __LINE__);
    return $results;
}

function formatDate($dateValue) {
    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dateValue)) {
        $parts = explode('/', $dateValue);
        return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }
    
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateValue)) {
        return $dateValue;
    }
    
    return null;
}

function generateRandomPassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}
?>
<div id="import-module" class="module-content fade-in">
        <div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6">
          <div class="flex items-start justify-between mb-6">
            <div>
              <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-600 bg-clip-text text-transparent mb-2">
                <i data-lucide="upload" class="w-6 h-6 inline mr-2"></i> Importar Usuarios
              </h2>
              <p class="text-slate-600">Suba un archivo CSV con la información de los usuarios</p>
            </div>
            <button onclick="history.back()" class="flex items-center gap-2 px-4 py-2 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all">
              <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver
            </button>
          </div>

          <div class="grid gap-6 md:grid-cols-3 mb-8">
            <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl shadow-lg">
              <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                <span class="font-bold">1</span>
              </div>
              <span class="font-medium">Seleccionar archivo</span>
            </div>
            <div class="flex items-center gap-3 p-4 bg-white/60 border border-white/20 rounded-xl">
              <div class="w-8 h-8 bg-slate-200 rounded-full flex items-center justify-center">
                <span class="font-bold text-slate-600">2</span>
              </div>
              <span class="font-medium text-slate-700">Mapear campos</span>
            </div>
            <div class="flex items-center gap-3 p-4 bg-white/60 border border-white/20 rounded-xl">
              <div class="w-8 h-8 bg-slate-200 rounded-full flex items-center justify-center">
                <span class="font-bold text-slate-600">3</span>
              </div>
              <span class="font-medium text-slate-700">Confirmar</span>
            </div>
          </div>

          <form id="upload-form" enctype="multipart/form-data" method="post">
            <div class="glassmorphism rounded-2xl p-8 text-center mb-8 border-2 border-dashed border-blue-200 hover:border-blue-300 transition-colors">
              <div class="max-w-md mx-auto">
                <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                  <i data-lucide="upload-cloud" class="w-8 h-8 text-blue-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 mb-2">Arrastra tu archivo aquí</h3>
                <p class="text-sm text-slate-600 mb-4">Formato soportado: .csv</p>
                <label class="cursor-pointer">
                  <span class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg">
                    <i data-lucide="folder-open" class="w-4 h-4"></i> Seleccionar archivo
                    <input type="file" name="import_file" class="hidden" accept=".csv" id="file-input" required>
                  </span>
                </label>
              </div>
            </div>
          </form>

          <div class="grid gap-6 md:grid-cols-2">
            <div class="glassmorphism rounded-xl p-6 shadow-lg">
              <div class="flex items-center gap-3 mb-4">
                <i data-lucide="file-spreadsheet" class="w-5 h-5 text-green-600"></i>
                <h3 class="font-semibold text-slate-900">Descargar plantilla</h3>
              </div>
              <p class="text-sm text-slate-600 mb-4">Utilice nuestro formato estándar para garantizar una importación correcta.</p>
              <button type="button" onclick="downloadTemplate()" class="flex items-center gap-2 px-4 py-2 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all">
                <i data-lucide="download" class="w-4 h-4"></i> Plantilla.csv
              </button>
            </div>

            <div class="glassmorphism rounded-xl p-6 shadow-lg">
              <div class="flex items-center gap-3 mb-4">
                <i data-lucide="help-circle" class="w-5 h-5 text-blue-600"></i>
                <h3 class="font-semibold text-slate-900">Instrucciones</h3>
              </div>
              <ul class="text-sm text-slate-600 space-y-2">
                <li class="flex items-start gap-2">
                  <i data-lucide="check" class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0"></i>
                  <span>La primera fila debe contener los encabezados</span>
                </li>
                <li class="flex items-start gap-2">
                  <i data-lucide="check" class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0"></i>
                  <span>Campos obligatorios: first_name, last_name, id_type, id_number, email, role</span>
                </li>
                <li class="flex items-start gap-2">
                  <i data-lucide="alert-triangle" class="w-4 h-4 text-yellow-500 mt-0.5 flex-shrink-0"></i>
                  <span>Los usuarios duplicados serán actualizados</span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div id="preview-section" class="glassmorphism rounded-2xl p-6 shadow-xl mb-6 hidden">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-slate-900">
              <i data-lucide="file-text" class="w-5 h-5 inline mr-2 text-blue-600"></i>
              Previsualización
            </h3>
            <span id="record-count" class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
              <i data-lucide="check-circle" class="w-4 h-4 inline mr-1"></i>
              <span id="total-records">0</span> registros detectados
            </span>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
              <thead class="text-xs text-slate-700 bg-white/60">
                <tr>
                  <th class="px-4 py-3">Nombre</th>
                  <th class="px-4 py-3">Apellido</th>
                  <th class="px-4 py-3">Tipo ID</th>
                  <th class="px-4 py-3">Número ID</th>
                  <th class="px-4 py-3">Email</th>
                  <th class="px-4 py-3">Rol</th>
                  <th class="px-4 py-3">Estado</th>
                </tr>
              </thead>
              <tbody id="preview-body" class="divide-y divide-white/20">
              </tbody>
            </table>
          </div>

          <div class="flex justify-between items-center mt-6">
            <div id="warning-message" class="text-sm text-slate-600 hidden">
              <i data-lucide="alert-circle" class="w-4 h-4 inline mr-1 text-orange-500"></i>
              <span id="warning-count">0</span> advertencias detectadas
            </div>
            <div class="flex gap-3">
              <button type="button" onclick="cancelImport()" class="px-6 py-2 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all">
                Cancelar
              </button>
              <button type="button" onclick="processImport()" class="flex items-center gap-2 px-6 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg">
                <i data-lucide="user-check" class="w-4 h-4"></i>
                Confirmar Importación
              </button>
            </div>
          </div>
        </div>

        <div id="results-section" class="glassmorphism rounded-2xl p-6 shadow-xl mb-6 hidden">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-slate-900">
              <i data-lucide="check-circle" class="w-5 h-5 inline mr-2 text-green-600"></i>
              Resultados de la Importación
            </h3>
          </div>
          
          <div id="results-content" class="mb-6">
          </div>
          
          <div class="flex justify-end">
            <button type="button" onclick="window.location.reload()" class="flex items-center gap-2 px-6 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg">
              <i data-lucide="refresh-cw" class="w-4 h-4"></i>
              Importar Otro Archivo
            </button>
          </div>
        </div>
      </div>

<script>
document.getElementById('file-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const formData = new FormData(document.getElementById('upload-form'));
        
        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchPreviewData();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al subir el archivo');
        });
    }
});

function fetchPreviewData() {
    fetch('?preview=1')
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            return;
        }
        
        document.getElementById('preview-section').classList.remove('hidden');
        document.getElementById('total-records').textContent = data.length;
        
        const previewBody = document.getElementById('preview-body');
        previewBody.innerHTML = '';
        
        data.slice(0, 10).forEach(row => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-white/50';
            tr.innerHTML = `
                <td class="px-4 py-3 font-medium text-slate-900">${row.first_name || ''}</td>
                <td class="px-4 py-3 text-slate-600">${row.last_name || ''}</td>
                <td class="px-4 py-3">${row.id_type || ''}</td>
                <td class="px-4 py-3 text-slate-600">${row.id_number || ''}</td>
                <td class="px-4 py-3 text-slate-600">${row.email || ''}</td>
                <td class="px-4 py-3">${row.role || ''}</td>
                <td class="px-4 py-3"><span class="px-2 py-1 bg-green-100 text-green-800 rounded-lg text-xs">Nuevo</span></td>
            `;
            previewBody.appendChild(tr);
        });
        
        document.getElementById('preview-section').scrollIntoView({ behavior: 'smooth' });
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar la previsualización');
    });
}

function downloadTemplate() {
    const headers = ['first_name', 'middle_name', 'last_name', 'second_last_name', 'birth_date', 'gender', 'country', 'city', 'address', 'id_type', 'id_number', 'university', 'program', 'semester', 'phone_number', 'email', 'institutional_email', 'card_code', 'role', 'password', 'confirm_password'];
    
    let csvContent = "data:text/csv;charset=utf-8," + headers.join(",") + "\n";
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "plantilla_usuarios.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function cancelImport() {
    if (confirm('¿Cancelar la importación? Los cambios no se guardarán.')) {
        fetch('?cancel=1')
        .then(() => {
            document.getElementById('preview-section').classList.add('hidden');
            document.getElementById('file-input').value = '';
            document.getElementById('results-section').classList.add('hidden');
        });
    }
}

function processImport() {
    const button = document.querySelector('button[onclick="processImport()"]');
    button.disabled = true;
    button.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Procesando...';
    
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'process_import=1'
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('preview-section').classList.add('hidden');
        document.getElementById('results-section').classList.remove('hidden');
        
        let resultsHTML = `
            <div class="mb-4 p-4 rounded-xl ${data.errors > 0 ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800'}">
                <div class="font-semibold">${data.success} usuarios importados correctamente</div>
                ${data.errors > 0 ? `<div class="mt-2">${data.errors} usuarios con errores</div>` : ''}
            </div>
        `;
        
        if (data.messages && data.messages.length > 0) {
            resultsHTML += '<div class="text-sm text-slate-700 mt-4"><strong>Detalles de errores:</strong></div>';
            resultsHTML += '<ul class="text-sm text-slate-600 mt-2 space-y-1'>';
            data.messages.forEach(message => {
                resultsHTML += `<li class="flex items-start gap-2"><i data-lucide="alert-circle" class="w-4 h-4 text-orange-500 mt-0.5 flex-shrink-0"></i> ${message}</li>`;
            });
            resultsHTML += '</ul>';
        }
        
        document.getElementById('results-content').innerHTML = resultsHTML;
        lucide.createIcons();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error durante la importación');
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = '<i data-lucide="user-check" class="w-4 h-4"></i> Confirmar Importación';
    });
}
</script>