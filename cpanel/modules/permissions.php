<script>
let currentRoleId = null;
let currentUserId = null;
let allPermissions = <?= json_encode($resultadoFinal['permissions'] ?? []) ?>;
let allRoles = <?= json_encode($resultadoFinal['roles'] ?? []) ?>;
let allUsers = <?= json_encode($resultadoFinal['users'] ?? []) ?>;
let filteredPermissions = [...allPermissions];
let filteredUsers = [...allUsers];

let currentPage = 1;
const perPage = 15;
let totalPermissions = allPermissions.length;
let totalPages = Math.ceil(totalPermissions / perPage);

document.addEventListener('DOMContentLoaded', function() {
  initializePermissionsModule();
  setupEventListeners();
  setupPagination();
  
  if (typeof lucide !== 'undefined') {
    lucide.createIcons();
  }
});

function initializePermissionsModule() {
  showTab('permissions-tab');
}

function setupEventListeners() {
  document.getElementById('module-filter')?.addEventListener('change', filterPermissions);
  document.getElementById('action-filter')?.addEventListener('change', filterPermissions);
  
  document.getElementById('permission-search')?.addEventListener('input', debounce(searchPermissions, 300));
  
  document.getElementById('search-user-permissions')?.addEventListener('input', debounce(searchUserPermissions, 300));
  
  document.getElementById('user-search')?.addEventListener('input', debounce(filterUsers, 300));
  
  document.getElementById('prev-page')?.addEventListener('click', () => changePage(currentPage - 1));
  document.getElementById('next-page')?.addEventListener('click', () => changePage(currentPage + 1));
  document.getElementById('prev-page-mobile')?.addEventListener('click', () => changePage(currentPage - 1));
  document.getElementById('next-page-mobile')?.addEventListener('click', () => changePage(currentPage + 1));
}

function setupPagination() {
  renderPagination();
  renderPermissionsPage();
}

function renderPagination() {
  const pageNumbersContainer = document.getElementById('page-numbers');
  if (!pageNumbersContainer) return;
  
  pageNumbersContainer.innerHTML = '';
  
  let startPage = Math.max(1, currentPage - 2);
  let endPage = Math.min(totalPages, startPage + 4);
  
  if (endPage - startPage < 4) {
    startPage = Math.max(1, endPage - 4);
  }
  
  for (let i = startPage; i <= endPage; i++) {
    const pageButton = document.createElement('button');
    pageButton.className = `relative inline-flex items-center px-4 py-2 text-sm font-semibold ${i === currentPage ? 'bg-blue-600 text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50'}`;
    pageButton.textContent = i;
    pageButton.addEventListener('click', () => changePage(i));
    pageNumbersContainer.appendChild(pageButton);
  }
  
  document.getElementById('prev-page').disabled = currentPage === 1;
  document.getElementById('prev-page-mobile').disabled = currentPage === 1;
  document.getElementById('next-page').disabled = currentPage === totalPages;
  document.getElementById('next-page-mobile').disabled = currentPage === totalPages;
  
  const startIndex = (currentPage - 1) * perPage + 1;
  const endIndex = Math.min(currentPage * perPage, totalPermissions);
  document.getElementById('current-range').textContent = `${startIndex}-${endIndex}`;
  document.getElementById('total-permissions').textContent = totalPermissions;
}

function changePage(page) {
  if (page < 1 || page > totalPages) return;
  
  currentPage = page;
  renderPagination();
  renderPermissionsPage();
}

function renderPermissionsPage() {
  const permissionsList = document.getElementById('permissions-list');
  if (!permissionsList) return;
  
  const startIndex = (currentPage - 1) * perPage;
  const endIndex = Math.min(startIndex + perPage, totalPermissions);
  const currentPermissions = allPermissions.slice(startIndex, endIndex);
  
  permissionsList.innerHTML = '';
  
  currentPermissions.forEach(permission => {
    const status = permission.estado ?? permission.status ?? 0;
    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-50';
    row.dataset.id = permission.id;
    row.dataset.module = permission.module;
    row.dataset.action = permission.action;
    
    row.innerHTML = `
      <td class="px-6 py-4 font-medium text-gray-800">
        <div class="flex items-center gap-2">
          <i data-lucide="shield" class="w-4 h-4 text-blue-500"></i>
          <span class="permission-module">${escapeHtml(permission.module)}</span>
        </div>
      </td>
      <td class="px-6 py-4 font-medium text-gray-800">
        <span class="permission-action">${escapeHtml(permission.action)}</span>
      </td>
      <td class="px-6 py-4 text-gray-600">
        <span class="permission-description">${escapeHtml(permission.description ?? '')}</span>
      </td>
      <td class="px-6 py-4">
        <form method="POST" class="toggle-permission-form">
          <input type="hidden" name="action" value="toggle_permission_status">
          <input type="hidden" name="id" value="${permission.id}">
          <input type="hidden" name="estado" value="${status == 1 ? 0 : 1}">
          <button type="submit" class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors duration-200 ease-in-out ${status == 1 ? 'bg-green-600' : 'bg-gray-300'}">
            <span class="inline-block w-4 h-4 transform bg-white rounded-full shadow-md transition-transform duration-200 ease-in-out ${status == 1 ? 'translate-x-6' : 'translate-x-1'}"></span>
          </button>
        </form>
      </td>
      <td class="px-6 py-4 text-right">
        <div class="flex justify-end space-x-3">
          <button onclick="editPermission(${permission.id})" class="text-gray-500 hover:text-blue-600 p-1.5 rounded-lg hover:bg-blue-50 transition-all">
            <i data-lucide="edit" class="w-5 h-5"></i>
          </button>
          <form method="POST" class="inline-block">
            <input type="hidden" name="action" value="delete_permission">
            <input type="hidden" name="id" value="${permission.id}">
            <button type="submit" onclick="return confirm('Estas seguro de que deseas eliminar este permiso?')" class="text-gray-500 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition-all">
              <i data-lucide="trash-2" class="w-5 h-5"></i>
            </button>
          </form>
        </div>
      </td>
    `;
    
    permissionsList.appendChild(row);
  });
  
  if (typeof lucide !== 'undefined') {
    lucide.createIcons();
  }
}

function validatePermissionForm() {
  const module = document.querySelector('input[name="module"]').value.trim();
  const action = document.querySelector('input[name="action_name"]').value.trim();
  
  if (!module) {
    alert('El campo Modulo es obligatorio');
    return false;
  }
  
  if (!action) {
    alert('El campo Accion es obligatorio');
    return false;
  }
  
  const formData = new FormData(document.getElementById('permissionForm'));
  
  fetch(window.location.href, {
    method: 'POST',
    body: formData
  })
  .then(response => {
    if (response.ok) {
      alert('Permiso creado exitosamente');
      window.location.reload(); 
    } else {
      response.text().then(text => {
        let errorMessage = 'Error al crear el permiso';
        if (response.status === 400) {
          errorMessage = 'Error: Faltan campos obligatorios.';
        } else if (response.status === 409) {
          errorMessage = 'Error: Este permiso ya existe.';
        } else if (response.status === 500) {
          errorMessage = 'Error interno del servidor al intentar guardar el permiso.';
        }
        alert(errorMessage);
      }).catch(() => {
        alert('Error desconocido al procesar la respuesta del servidor.');
      });
    }
  })
  .catch(error => {
    alert('Error de conexion con el servidor: ' + error.message);
  });
  
  return false;
}

function validateEditPermissionForm() {
  const module = document.getElementById('edit-permission-module').value.trim();
  const action = document.getElementById('edit-permission-action').value.trim();
  
  if (!module) {
    alert('El campo Modulo es obligatorio');
    return false;
  }
  
  if (!action) {
    alert('El campo Accion es obligatorio');
    return false;
  }
  
  return true;
}

function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

function editPermission(id) {
  const permission = allPermissions.find(p => p.id == id);
  if (!permission) return;
  
  document.getElementById('edit-permission-id').value = id;
  document.getElementById('edit-permission-module').value = permission.module;
  document.getElementById('edit-permission-action').value = permission.action;
  document.getElementById('edit-permission-description').value = permission.description || '';
  
  document.getElementById('edit-permission-modal').classList.remove('hidden');
}

function filterPermissions() {
  const moduleFilter = document.getElementById('module-filter').value.toLowerCase();
  const actionFilter = document.getElementById('action-filter').value.toLowerCase();
  
  filteredPermissions = allPermissions.filter(permission => {
    const module = permission.module.toLowerCase();
    const action = permission.action.toLowerCase();
    
    const matchesModule = !moduleFilter || module.includes(moduleFilter);
    const matchesAction = !actionFilter || action.includes(actionFilter);
    
    return matchesModule && matchesAction;
  });
  
  allPermissions = filteredPermissions;
  totalPermissions = allPermissions.length;
  totalPages = Math.ceil(totalPermissions / perPage);
  currentPage = 1;
  
  renderPagination();
  renderPermissionsPage();
}

function loadRolePermissions() {
  const roleSelector = document.getElementById('role-selector');
  const roleId = roleSelector.value;
  
  console.log('Role ID seleccionado:', roleId);
  console.log('Todos los roles:', allRoles);
  
  if (!roleId) {
    alert('Por favor seleccione un rol');
    return;
  }
  
  currentRoleId = roleId;
  document.getElementById('remove-all-role-id').value = roleId;
  
  // Buscar el rol en la estructura de allRoles
  let selectedRole = null;
  for (const roleKey in allRoles) {
    const roleData = allRoles[roleKey];
    console.log('Comparando:', roleData.role_id, 'con', roleId, 'tipo:', typeof roleData.role_id, typeof roleId);
    
    if (roleData.role_id == roleId) {
      selectedRole = roleData;
      break;
    }
  }
  
  console.log('Rol encontrado:', selectedRole);
  
  if (selectedRole) {
    document.getElementById('selected-role-name').textContent = selectedRole.role_name;
    document.getElementById('selected-role-desc').textContent = selectedRole.role_description || 'Sin descripcion';
    
    // Actualizar contador de permisos
    const assignedCount = selectedRole.permissions ? selectedRole.permissions.length : 0;
    document.getElementById('assigned-count').textContent = assignedCount;
    
    // Actualizar los toggles de permisos
    document.querySelectorAll('.toggle-role-permission-form').forEach(form => {
      const permissionId = parseInt(form.querySelector('input[name="permission_id"]').value);
      const actionInput = form.querySelector('input[name="action"]');
      const idInput = form.querySelector('input[name="id"]');
      
      idInput.value = roleId;
      
      const isAssigned = selectedRole.permissions && 
                        selectedRole.permissions.some(p => p.permission_id == permissionId);
      const button = form.querySelector('button');
      
      console.log(`Permiso ${permissionId}: asignado = ${isAssigned}`);
      
      if (isAssigned) {
        actionInput.value = 'remove_permission_from_role';
        button.classList.remove('bg-gray-300');
        button.classList.add('bg-green-600');
        button.querySelector('span').classList.remove('translate-x-1');
        button.querySelector('span').classList.add('translate-x-6');
      } else {
        actionInput.value = 'assign_permission_to_role';
        button.classList.remove('bg-green-600');
        button.classList.add('bg-gray-300');
        button.querySelector('span').classList.remove('translate-x-6');
        button.querySelector('span').classList.add('translate-x-1');
      }
    });
  } else {
    console.error('No se encontro el rol con ID:', roleId);
    alert('Error: No se pudo cargar la informacion del rol seleccionado');
    return;
  }
  
  document.getElementById('permissions-panel').classList.remove('hidden');
}

function searchPermissions() {
  const searchTerm = document.getElementById('permission-search').value.toLowerCase();
  
  document.querySelectorAll('.permission-module-group').forEach(group => {
    const moduleName = group.querySelector('h4').textContent.toLowerCase();
    let hasMatch = moduleName.includes(searchTerm);
    
    if (!hasMatch) {
      group.querySelectorAll('.font-medium').forEach(el => {
        if (el.textContent.toLowerCase().includes(searchTerm)) {
          hasMatch = true;
        }
      });
    }
    
    group.style.display = hasMatch ? '' : 'none';
  });
}

function filterUsers() {
  const searchTerm = document.getElementById('user-search').value.toLowerCase();
  
  document.querySelectorAll('#users-list tr').forEach(row => {
    const name = row.querySelector('.font-medium').textContent.toLowerCase();
    const email = row.querySelector('.text-sm').textContent.toLowerCase();
    
    const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
    
    row.style.display = matchesSearch ? '' : 'none';
  });
}

function manageUserPermissions(userId) {
  currentUserId = userId;
  
  const user = allUsers.find(u => (u.user_id ?? u.id) == userId);
  if (!user) return;
  
  const userName = user.name ?? user.full_name;
  
  document.getElementById('user-permissions-modal-title').textContent = `Permisos para ${userName}`;
  document.getElementById('user-full-name').textContent = userName;
  document.getElementById('user-email').textContent = user.email;
  document.getElementById('user-avatar').textContent = userName.substring(0, 2).toUpperCase();
  
  renderUserPermissions(user.permissions || []);
  document.getElementById('user-permissions-modal').classList.remove('hidden');
}

function renderUserPermissions(userPermissions) {
  const container = document.getElementById('user-permissions-list');
  container.innerHTML = '';
  
  if (allPermissions.length === 0) {
    container.innerHTML = '<tr><td colspan="2" class="px-6 py-4 text-center text-gray-500">No hay permisos disponibles</td></tr>';
    return;
  }
  
  allPermissions.forEach(permission => {
    const isAssigned = userPermissions && userPermissions.some(up => up.id == permission.id);
    
    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-50';
    row.innerHTML = `
      <td class="px-6 py-4">
        <div class="text-sm font-medium text-gray-900">${permission.module}.${permission.action}</div>
        <div class="text-sm text-gray-500">${permission.description || ''}</div>
      </td>
      <td class="px-6 py-4">
        <form method="POST" class="toggle-user-permission-form">
          <input type="hidden" name="action" value="${isAssigned ? 'remove_permission_from_user' : 'assign_permission_to_user'}">
          <input type="hidden" name="id" value="${currentUserId}">
          <input type="hidden" name="permission_id" value="${permission.id}">
          <button type="submit" class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors duration-200 ease-in-out ${isAssigned ? 'bg-purple-600' : 'bg-gray-300'}">
            <span class="inline-block w-4 h-4 transform bg-white rounded-full shadow-md transition-transform duration-200 ease-in-out ${isAssigned ? 'translate-x-6' : 'translate-x-1'}"></span>
          </button>
        </form>
      </td>
    `;
    container.appendChild(row);
  });
}

function searchUserPermissions() {
  const searchTerm = document.getElementById('search-user-permissions').value.toLowerCase();
  document.querySelectorAll('#user-permissions-list tr').forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(searchTerm) ? '' : 'none';
  });
}

function showTab(tabId) {
  document.querySelectorAll('.tab-content').forEach(tab => {
    tab.classList.add('hidden');
  });
  
  const selectedTab = document.getElementById(tabId);
  if (selectedTab) {
    selectedTab.classList.remove('hidden');
  }
  
  document.querySelectorAll('#permissions-tabs button').forEach(btn => {
    if (btn.getAttribute('onclick').includes(tabId)) {
      btn.classList.add('border-blue-500', 'text-blue-600');
      btn.classList.remove('border-transparent', 'text-gray-500');
    } else {
      btn.classList.remove('border-blue-500', 'text-blue-600');
      btn.classList.add('border-transparent', 'text-gray-500');
    }
  });
}

function closeModal(modalId) {
  document.getElementById(modalId).classList.add('hidden');
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

if (window.lucide) {
  lucide.createIcons();
}

window.addEventListener('click', function(event) {
  if (event.target.id === 'user-permissions-modal' || event.target.id === 'edit-permission-modal') {
    closeModal(event.target.id);
  }
});
</script>