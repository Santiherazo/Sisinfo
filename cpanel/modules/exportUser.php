<?php
if(!accessManager()->canAccess($_SESSION['userid'], ['Administrador', 'Coordinador'], [['module' => 'Cpuser', 'action' => 'export']])) {
    die('No tienes permisos para acceder a este módulo.');
}

$allUsers = $profileManager->getAllUsers();
$usersJson = json_encode($allUsers);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export_type'])) {
    $format = $_POST['format'];
    $filters = $_POST['filters'] ?? [];
    $selectedFields = $_POST['fields'] ?? [];
    
    $filteredUsers = filterUsers($allUsers, $filters);
    
    if ($format === 'csv') {
        exportCSV($filteredUsers, $selectedFields);
    } elseif ($format === 'json') {
        exportJSON($filteredUsers, $selectedFields);
    }
    exit;
}

function filterUsers($users, $filters) {
    $filtered = $users;
    
    if (!empty($filters['role'])) {
        $filtered = array_filter($filtered, function($user) use ($filters) {
            return $user['role_name'] === $filters['role'];
        });
    }
    
    if (!empty($filters['program'])) {
        $filtered = array_filter($filtered, function($user) use ($filters) {
            return isset($user['program']) && $user['program'] === $filters['program'];
        });
    }
    
    if (!empty($filters['status'])) {
        $statusValue = $filters['status'] === 'Activo' ? 1 : 0;
        $filtered = array_filter($filtered, function($user) use ($statusValue) {
            return $user['status'] == $statusValue;
        });
    }
    
    return array_values($filtered);
}

function exportCSV($users, $selectedFields) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=usuarios_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    fwrite($output, "\xEF\xBB\xBF");
    
    $headers = [];
    $fieldMap = [
        'uid' => 'ID Usuario',
        'unom' => 'Nombre Usuario',
        'ueml' => 'Email',
        'uestado' => 'Estado',
        'last_login' => 'Último Login',
        'ureg' => 'Fecha Registro',
        'first_name' => 'Nombre',
        'middle_name' => 'Segundo Nombre',
        'last_name' => 'Apellido',
        'second_last_name' => 'Segundo Apellido',
        'birth_date' => 'Fecha Nacimiento',
        'gender' => 'Género',
        'country' => 'País',
        'city' => 'Ciudad',
        'address' => 'Dirección',
        'id_type' => 'Tipo Identificación',
        'id_number' => 'Número Identificación',
        'university' => 'Universidad',
        'program' => 'Programa',
        'semester' => 'Semestre',
        'institutional_email' => 'Email Institucional',
        'card_code' => 'Código Tarjeta',
        'phone_number' => 'Teléfono',
        'role_name' => 'Rol'
    ];
    
    foreach ($selectedFields as $field) {
        if (isset($fieldMap[$field])) {
            $headers[] = $fieldMap[$field];
        }
    }
    
    fputcsv($output, $headers);
    
    foreach ($users as $user) {
        $row = [];
        foreach ($selectedFields as $field) {
            switch ($field) {
                case 'uid':
                    $row[] = $user['id'];
                    break;
                case 'unom':
                    $row[] = trim($user['first_name'] . ' ' . ($user['middle_name'] ?? '') . ' ' . $user['last_name'] . ' ' . ($user['second_last_name'] ?? ''));
                    break;
                case 'ueml':
                    $row[] = $user['email'];
                    break;
                case 'uestado':
                    $row[] = $user['status'] == 1 ? 'Activo' : 'Inactivo';
                    break;
                case 'last_login':
                    $row[] = $user['last_login'] ?? '';
                    break;
                case 'ureg':
                    $row[] = $user['created_at'];
                    break;
                case 'first_name':
                    $row[] = $user['first_name'];
                    break;
                case 'middle_name':
                    $row[] = $user['middle_name'] ?? '';
                    break;
                case 'last_name':
                    $row[] = $user['last_name'];
                    break;
                case 'second_last_name':
                    $row[] = $user['second_last_name'] ?? '';
                    break;
                case 'birth_date':
                    $row[] = $user['birth_date'] ?? '';
                    break;
                case 'gender':
                    $row[] = $user['gender'] ?? '';
                    break;
                case 'country':
                    $row[] = $user['country'] ?? '';
                    break;
                case 'city':
                    $row[] = $user['city'] ?? '';
                    break;
                case 'address':
                    $row[] = $user['address'] ?? '';
                    break;
                case 'id_type':
                    $row[] = $user['id_type'] ?? '';
                    break;
                case 'id_number':
                    $row[] = $user['id_number'] ?? '';
                    break;
                case 'university':
                    $row[] = $user['university'] ?? '';
                    break;
                case 'program':
                    $row[] = $user['program'] ?? '';
                    break;
                case 'semester':
                    $row[] = $user['semester'] ?? '';
                    break;
                case 'institutional_email':
                    $row[] = $user['institutional_email'] ?? '';
                    break;
                case 'card_code':
                    $row[] = $user['card_code'] ?? '';
                    break;
                case 'phone_number':
                    $row[] = $user['phone_number'] ?? '';
                    break;
                case 'role_name':
                    $row[] = $user['role_name'];
                    break;
            }
        }
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;
}

function exportJSON($users, $selectedFields) {
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename=usuarios_' . date('Y-m-d') . '.json');
    
    $exportData = [];
    foreach ($users as $user) {
        $item = [];
        foreach ($selectedFields as $field) {
            switch ($field) {
                case 'uid':
                    $item['uid'] = $user['id'];
                    break;
                case 'unom':
                    $item['unom'] = trim($user['first_name'] . ' ' . ($user['middle_name'] ?? '') . ' ' . $user['last_name'] . ' ' . ($user['second_last_name'] ?? ''));
                    break;
                case 'ueml':
                    $item['ueml'] = $user['email'];
                    break;
                case 'uestado':
                    $item['uestado'] = $user['status'] == 1 ? 'Activo' : 'Inactivo';
                    break;
                case 'last_login':
                    $item['last_login'] = $user['last_login'] ?? '';
                    break;
                case 'ureg':
                    $item['ureg'] = $user['created_at'];
                    break;
                case 'first_name':
                    $item['first_name'] = $user['first_name'];
                    break;
                case 'middle_name':
                    $item['middle_name'] = $user['middle_name'] ?? '';
                    break;
                case 'last_name':
                    $item['last_name'] = $user['last_name'];
                    break;
                case 'second_last_name':
                    $item['second_last_name'] = $user['second_last_name'] ?? '';
                    break;
                case 'birth_date':
                    $item['birth_date'] = $user['birth_date'] ?? '';
                    break;
                case 'gender':
                    $item['gender'] = $user['gender'] ?? '';
                    break;
                case 'country':
                    $item['country'] = $user['country'] ?? '';
                    break;
                case 'city':
                    $item['city'] = $user['city'] ?? '';
                    break;
                case 'address':
                    $item['address'] = $user['address'] ?? '';
                    break;
                case 'id_type':
                    $item['id_type'] = $user['id_type'] ?? '';
                    break;
                case 'id_number':
                    $item['id_number'] = $user['id_number'] ?? '';
                    break;
                case 'university':
                    $item['university'] = $user['university'] ?? '';
                    break;
                case 'program':
                    $item['program'] = $user['program'] ?? '';
                    break;
                case 'semester':
                    $item['semester'] = $user['semester'] ?? '';
                    break;
                case 'institutional_email':
                    $item['institutional_email'] = $user['institutional_email'] ?? '';
                    break;
                case 'card_code':
                    $item['card_code'] = $user['card_code'] ?? '';
                    break;
                case 'phone_number':
                    $item['phone_number'] = $user['phone_number'] ?? '';
                    break;
                case 'role_name':
                    $item['role_name'] = $user['role_name'];
                    break;
            }
        }
        $exportData[] = $item;
    }
    
    echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}
?>
<div id="export-module" class="module-content fade-in">
        <div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6">
          <div class="flex items-start justify-between mb-6">
            <div>
              <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-600 bg-clip-text text-transparent mb-2">
                <i data-lucide="download" class="w-6 h-6 inline mr-2"></i> Exportar Usuarios
              </h2>
              <p class="text-slate-600">Genere un archivo con los usuarios del sistema según sus criterios</p>
            </div>
            <button onclick="history.back()" class="flex items-center gap-2 px-4 py-2 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all">
              <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver
            </button>
          </div>

          <form id="exportForm" method="POST">
            <input type="hidden" name="export_type" value="users_export">
            
            <div class="glassmorphism rounded-xl p-6 mb-8 shadow-lg">
              <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
                <i data-lucide="filter" class="w-5 h-5 text-blue-600"></i> Filtros de Exportación
              </h3>
              
              <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 mb-6">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Rol de Usuario</label>
                  <select name="filters[role]" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">Todos los roles</option>
                    <option value="administrador">Administrador</option>
                    <option value="coordinador">Coordinador</option>
                    <option value="evaluador">Evaluador</option>
                    <option value="docente">Docente</option>
                    <option value="estudiante">Estudiante</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Programa Académico</label>
                  <select name="filters[program]" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">Todos los programas</option>
                    <option value="Ingeniería de sistemas">Ingeniería de Sistemas</option>
                    <option value="Ingeniería Industrial">Ingeniería Industrial</option>
                    <option value="Ingeniería Civil">Ingeniería Civil</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Estado</label>
                  <select name="filters[status]" class="w-full px-4 py-3 bg-white/60 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">Todos los estados</option>
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                  </select>
                </div>
              </div>
              
              <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl">
                <div class="flex items-center gap-3">
                  <i data-lucide="info" class="w-5 h-5 text-blue-600"></i>
                  <p class="text-sm text-slate-700">Se exportarán <span id="userCount" class="font-semibold"><?php echo count($allUsers); ?> usuarios</span> con los filtros actuales</p>
                </div>
                <button type="button" onclick="updateFilters()" class="flex items-center gap-2 px-4 py-2 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all">
                  <i data-lucide="refresh-cw" class="w-4 h-4"></i> Actualizar
                </button>
              </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2 mb-8">
              <div class="glassmorphism rounded-xl p-6 shadow-lg">
                <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
                  <i data-lucide="file" class="w-5 h-5 text-purple-600"></i> Formato de Exportación
                </h3>
                
                <div class="space-y-3">
                  <label class="flex items-center gap-3 p-3 bg-white/60 rounded-xl hover:bg-white/80 cursor-pointer">
                    <input type="radio" name="format" value="csv" checked class="w-4 h-4 text-blue-600">
                    <div>
                      <p class="font-medium text-slate-900">CSV (Excel)</p>
                      <p class="text-xs text-slate-600">Archivo separado por comas, ideal para hojas de cálculo</p>
                    </div>
                  </label>
                  
                  <label class="flex items-center gap-3 p-3 bg-white/60 rounded-xl hover:bg-white/80 cursor-pointer">
                    <input type="radio" name="format" value="json" class="w-4 h-4 text-blue-600">
                    <div>
                      <p class="font-medium text-slate-900">JSON</p>
                      <p class="text-xs text-slate-600">Estructura de datos para aplicaciones</p>
                    </div>
                  </label>
                </div>
              </div>
              
              <div class="glassmorphism rounded-xl p-6 shadow-lg">
                <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
                  <i data-lucide="list" class="w-5 h-5 text-green-600"></i> Campos a Incluir
                </h3>
                
                <div class="space-y-2">
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="uid" checked class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">ID Usuario</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="unom" checked class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Nombre Usuario</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="ueml" checked class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Email</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="uestado" checked class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Estado</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="role_name" checked class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Rol</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="first_name" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Nombre</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="middle_name" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Segundo Nombre</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="last_name" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Apellido</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="second_last_name" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Segundo Apellido</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="birth_date" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Fecha Nacimiento</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="gender" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Género</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="country" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">País</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="city" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Ciudad</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="address" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Dirección</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="id_type" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Tipo Identificación</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="id_number" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Número Identificación</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="university" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Universidad</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="program" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Programa</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="semester" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Semestre</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="institutional_email" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Email Institucional</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="card_code" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Código Tarjeta</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="phone_number" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Teléfono</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="last_login" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Último Login</span>
                  </label>
                  
                  <label class="flex items-center gap-3 p-2 hover:bg-white/80 rounded-lg cursor-pointer">
                    <input type="checkbox" name="fields[]" value="ureg" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-slate-700">Fecha Registro</span>
                  </label>
                </div>
                
                <div class="flex gap-2 mt-4">
                  <button type="button" onclick="selectAllFields(true)" class="text-xs px-3 py-1 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                    Seleccionar todos
                  </button>
                  <button type="button" onclick="selectAllFields(false)" class="text-xs px-3 py-1 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                    Deseleccionar
                  </button>
                </div>
              </div>
            </div>

            <div class="glassmorphism rounded-xl p-6 shadow-lg mb-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                  <i data-lucide="eye" class="w-5 h-5 text-orange-600"></i> Previsualización
                </h3>
                <span class="text-sm text-slate-600">Mostrando 3 de <span id="previewCount"><?php echo count($allUsers); ?></span> registros</span>
              </div>
              
              <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                  <thead class="text-xs text-slate-700 bg-white/60">
                    <tr>
                      <th class="px-4 py-3">ID</th>
                      <th class="px-4 py-3">Nombre</th>
                      <th class="px-4 py-3">Email</th>
                      <th class="px-4 py-3">Rol</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-white/20">
                    <?php for ($i = 0; $i < min(3, count($allUsers)); $i++): ?>
                    <tr class="hover:bg-white/50">
                      <td class="px-4 py-3 font-mono text-slate-600"><?php echo $allUsers[$i]['id']; ?></td>
                      <td class="px-4 py-3 font-medium text-slate-900"><?php echo trim($allUsers[$i]['first_name'] . ' ' . ($allUsers[$i]['middle_name'] ?? '') . ' ' . $allUsers[$i]['last_name'] . ' ' . ($allUsers[$i]['second_last_name'] ?? '')); ?></td>
                      <td class="px-4 py-3 text-slate-600"><?php echo $allUsers[$i]['email']; ?></td>
                      <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-lg text-xs"><?php echo $allUsers[$i]['role_name']; ?></span></td>
                    </tr>
                    <?php endfor; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="flex flex-col md:flex-row justify-between gap-4">
              <div class="flex items-center gap-3 p-3 bg-yellow-50 rounded-xl">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600"></i>
                <p class="text-sm text-slate-700">
                  <span class="font-semibold">Nota:</span> La exportación puede tomar varios minutos si hay muchos registros
                </p>
              </div>
              
              <div class="flex gap-3">
                <button type="button" onclick="resetExport()" class="px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all font-medium">
                  Reiniciar
                </button>
                <button type="submit" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg font-medium">
                  <i data-lucide="download" class="w-4 h-4"></i> Exportar Usuarios
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

<script>
const allUsers = <?php echo $usersJson; ?>;
    
function updateFilters() {
    const roleFilter = document.querySelector('select[name="filters[role]"]').value;
    const programFilter = document.querySelector('select[name="filters[program]"]').value;
    const statusFilter = document.querySelector('select[name="filters[status]"]').value;
    
    const filteredUsers = allUsers.filter(user => {
        if (roleFilter && user.role_name !== roleFilter) return false;
        if (programFilter && user.program !== programFilter) return false;
        if (statusFilter) {
            const statusValue = statusFilter === 'Activo' ? 1 : 0;
            if (user.status != statusValue) return false;
        }
        return true;
    });
    
    document.getElementById('userCount').textContent = filteredUsers.length + ' usuarios';
    document.getElementById('previewCount').textContent = filteredUsers.length;
}
    
function selectAllFields(select) {
    document.querySelectorAll('input[name="fields[]"]').forEach(checkbox => {
        checkbox.checked = select;
    });
}
    
function resetExport() {
    document.querySelectorAll('select').forEach(select => {
        select.value = '';
    });
    selectAllFields(false);
    document.querySelector('input[name="format"][value="csv"]').checked = true;
    updateFilters();
}
    
document.getElementById('exportForm').addEventListener('submit', function(e) {
    const selectedFields = Array.from(document.querySelectorAll('input[name="fields[]"]:checked')).map(cb => cb.value);
    
    if (selectedFields.length === 0) {
        e.preventDefault();
        alert('Selecciona al menos un campo para exportar');
        return;
    }
});

document.querySelectorAll('select[name^="filters"]').forEach(select => {
    select.addEventListener('change', updateFilters);
});

updateFilters();
</script>