<?php

//$roleManager = new RoleManager();

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if(!accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'User', 'action' => 'manage']])) {
        die('No tienes permisos para acceder a este módulo.');
    }

/*$account = new Account();
$searchTerm = $_GET['search'] ?? '';
$users = $account->getUsers($searchTerm);
$userDetail = null;
$editMode = false;
$activeRow = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_user'])) {
        $uid = $_POST['uid'];
        $coreData = [
            'ucod' => $_POST['ucod'],
            'ueml' => $_POST['ueml'],
            'upwd' => $_POST['upwd']
        ];
        $detailsData = $_POST;
        unset($detailsData['edit_user'], $detailsData['uid']);
        $account->updateUser($uid, $coreData, $detailsData);
        $userDetail = $account->getUserDetails($uid);
        $activeRow = $uid;
        $editMode = false;
    }
    
    if (isset($_POST['delete_user'])) {
        $account->deleteUser($_POST['delete_user']);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
    
    if (isset($_POST['load_user'])) {
        $userDetail = $account->getUserDetails($_POST['load_user']);
        $activeRow = $_POST['load_user'];
        $editMode = isset($_POST['edit_mode']);
    }
}*/
?>

<!-- Interfaz HTML -->
<div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 15px;">
    <h2 style="color: #2c3e50; font-size: 28px; margin-bottom: 25px; font-weight: 600;">Gestión de Usuarios</h2>
    
    <form method="get" style="margin-bottom: 30px; display: flex; gap: 10px;">
        <input type="search" name="search" 
               placeholder="Buscar por nombre, email o cédula..." 
               value="<?= htmlspecialchars($searchTerm) ?>"
               style="flex: 1; padding: 10px 15px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
        <button type="submit" 
                style="padding: 10px 25px; background: #3498db; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
            Buscar
        </button>
    </form>

    <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 15px rgba(0,0,0,0.05);">
        <thead>
            <tr style="background: #f8f9fa;">
                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #eee; color: #6c757d; font-weight: 600;">ID</th>
                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #eee; color: #6c757d; font-weight: 600;">Email</th>
                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #eee; color: #6c757d; font-weight: 600;">Nombre</th>
                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #eee; color: #6c757d; font-weight: 600;">Apellido</th>
                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #eee; color: #6c757d; font-weight: 600;">Cédula</th>
                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #eee; color: #6c757d; font-weight: 600;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr style="border-bottom: 1px solid #f0f0f0; transition: background 0.2s;">
                <td style="padding: 15px; color: #495057;"><?= $user['uid'] ?></td>
                <td style="padding: 15px; color: #495057;"><?= htmlspecialchars($user['ueml']) ?></td>
                <td style="padding: 15px; color: #2c3e50; font-weight: 500;"><?= htmlspecialchars($user['first_name']) ?></td>
                <td style="padding: 15px; color: #2c3e50; font-weight: 500;"><?= htmlspecialchars($user['last_name']) ?></td>
                <td style="padding: 15px; color: #495057;"><?= htmlspecialchars($user['id_number']) ?></td>
                <td style="padding: 15px;">
                    <div style="display: flex; gap: 8px;">
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="load_user" value="<?= $user['uid'] ?>">
                            <button type="submit" 
                                    style="padding: 6px 12px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 13px;">
                                Ver
                            </button>
                        </form>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="load_user" value="<?= $user['uid'] ?>">
                            <input type="hidden" name="edit_mode" value="1">
                            <button type="submit"
                                    style="padding: 6px 12px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 13px;">
                                Editar
                            </button>
                        </form>
                        <form method="post" style="display: inline;" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?')">
                            <input type="hidden" name="delete_user" value="<?= $user['uid'] ?>">
                            <button type="submit"
                                    style="padding: 6px 12px; background: #e74c3c; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 13px;">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </td>
            </tr>

            <?php if ($activeRow == $user['uid'] && $userDetail): ?>
            <tr style="background: #f8fbfe; position: relative;">
                <td colspan="6" style="padding: 20px;">
                    <button onclick="location.href='?'" 
                            style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer; color: #666;">
                        ✕
                    </button>
                    
                    <form method="post" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; align-items: start;">
                        <input type="hidden" name="uid" value="<?= $userDetail['uid'] ?>">
                        
                        <!-- Sección de Cuenta -->
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <h3 style="margin: 0; color: #2c3e50; border-bottom: 2px solid #eee; padding-bottom: 5px;">Cuenta</h3>
                            <div>
                                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 14px;">Usuario:</label>
                                <input type="text" name="ucod" value="<?= htmlspecialchars($userDetail['ucod']) ?>" <?= !$editMode ? 'readonly' : '' ?>
                                       style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 14px;">Contraseña:</label>
                                <input type="password" name="upwd" value="<?= htmlspecialchars($userDetail['upwd']) ?>" <?= !$editMode ? 'readonly' : '' ?>
                                       style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 14px;">Email:</label>
                                <input type="email" name="ueml" value="<?= htmlspecialchars($userDetail['ueml']) ?>" <?= !$editMode ? 'readonly' : '' ?>
                                       style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                            </div>
                        </div>

                        <!-- Sección de Datos Personales -->
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <h3 style="margin: 0; color: #2c3e50; border-bottom: 2px solid #eee; padding-bottom: 5px;">Datos Personales</h3>
                            <div>
                                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 14px;">Nombres Completos:</label>
                                <input type="text" name="first_name" value="<?= htmlspecialchars($userDetail['first_name']) ?>" <?= !$editMode ? 'readonly' : '' ?>
                                       style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                                <input type="text" name="middle_name" value="<?= htmlspecialchars($userDetail['middle_name']) ?>" <?= !$editMode ? 'readonly' : '' ?>
                                       style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; width: 100%; margin-top: 5px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 14px;">Apellidos Completos:</label>
                                <input type="text" name="last_name" value="<?= htmlspecialchars($userDetail['last_name']) ?>" <?= !$editMode ? 'readonly' : '' ?>
                                       style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                                <input type="text" name="second_last_name" value="<?= htmlspecialchars($userDetail['second_last_name']) ?>" <?= !$editMode ? 'readonly' : '' ?>
                                       style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; width: 100%; margin-top: 5px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 14px;">Fecha Nacimiento:</label>
                                <input type="date" name="birth_date" value="<?= htmlspecialchars($userDetail['birth_date']) ?>" <?= !$editMode ? 'readonly' : '' ?>
                                       style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                            </div>
                        </div>

                        <!-- Sección de Información Adicional -->
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <h3 style="margin: 0; color: #2c3e50; border-bottom: 2px solid #eee; padding-bottom: 5px;">Información Adicional</h3>
                            <div>
                                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 14px;">Género:</label>
                                <input type="text" name="gender" value="<?= htmlspecialchars($userDetail['gender']) ?>" <?= !$editMode ? 'readonly' : '' ?>
                                       style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 14px;">Teléfono:</label>
                                <input type="text" name="phone_number" value="<?= htmlspecialchars($userDetail['phone_number']) ?>" <?= !$editMode ? 'readonly' : '' ?>
                                       style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 14px;">Dirección:</label>
                                <input type="text" name="address" value="<?= htmlspecialchars($userDetail['address']) ?>" <?= !$editMode ? 'readonly' : '' ?>
                                       style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; width: 100%;">
                            </div>
                        </div>

                        <?php if ($editMode): ?>
                            <div style="grid-column: 1 / -1; display: flex; gap: 10px; justify-content: flex-end;">
                                <button type="submit" name="edit_user" 
                                        style="padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 6px; cursor: pointer;">
                                    Guardar Cambios
                                </button>
                                <button type="button" onclick="location.href='?'" 
                                        style="padding: 10px 20px; background: #e74c3c; color: white; border: none; border-radius: 6px; cursor: pointer;">
                                    Cancelar
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                </td>
            </tr>
            <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>