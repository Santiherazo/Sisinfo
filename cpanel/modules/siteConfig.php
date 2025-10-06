<?php
echo '<div class="max-w-6xl mx-auto p-6">';
echo '<div class="glassmorphism rounded-2xl p-8 shadow-xl mb-6">';
    echo '<div class="flex items-center justify-between mb-8">';
        echo '<div>';
            echo '<h2 class="text-3xl font-bold bg-gradient-to-r from-slate-900 to-slate-600 bg-clip-text text-transparent mb-2">Configuración del Sitio Web</h2>';
            echo '<p class="text-slate-600">Administra la imagen corporativa y ajustes generales del sistema</p>';
        echo '</div>';
        echo '<div class="flex gap-3">';
            echo '<button onclick="resetInstallation()" class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-xl hover:from-orange-600 hover:to-red-700 transition-all shadow-lg text-sm">';
                echo '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';
                echo 'Reiniciar Instalación';
            echo '</button>';
        echo '</div>';
    echo '</div>';

    if (isset($_POST['upload_logo']) || isset($_POST['upload_favicon']) || isset($_POST['upload_cover'])) {
        $uploader = new uploadManager();
        
        if (isset($_POST['upload_logo']) && isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
            $result = $uploader->replaceFileKeepName(
                $_FILES['logo_file'], 
                __PATH_TEMPLATE_IMG__ . 'logo.png', 
                'admin',
                'logos'
            );
            
            if ($result !== false) {
                echo '<div class="p-4 mb-6 bg-green-100 text-green-800 rounded-xl flex items-center gap-3">';
                echo '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                echo '<span>Logo actualizado correctamente.</span>';
                echo '</div>';
                
                $config = webengineConfigs();
                $config['website_logo'] = 'logo.png';
                file_put_contents(__PATH_CONFIGS__.'webengine.json', json_encode($config, JSON_PRETTY_PRINT));
            } else {
                echo '<div class="p-4 mb-6 bg-red-100 text-red-800 rounded-xl flex items-center gap-3">';
                echo '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                echo '<span>Error al subir el logo: ' . implode(', ', $uploader->getErrors()) . '</span>';
                echo '</div>';
            }
        }
        
        if (isset($_POST['upload_favicon']) && isset($_FILES['favicon_file']) && $_FILES['favicon_file']['error'] === UPLOAD_ERR_OK) {
            $uploader->setAllowedExtensions(['ico', 'png']);
            $result = $uploader->replaceFileKeepName(
                $_FILES['favicon_file'], 
                __PATH_TEMPLATE__ . 'favicon.ico', 
                'admin',
                'favicons'
            );
            
            if ($result !== false) {
                echo '<div class="p-4 mb-6 bg-green-100 text-green-800 rounded-xl flex items-center gap-3">';
                echo '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                echo '<span>Favicon actualizado correctamente.</span>';
                echo '</div>';
                
                $config = webengineConfigs();
                $config['website_favicon'] = 'favicon.ico';
                file_put_contents(__PATH_CONFIGS__.'webengine.json', json_encode($config, JSON_PRETTY_PRINT));
            } else {
                echo '<div class="p-4 mb-6 bg-red-100 text-red-800 rounded-xl flex items-center gap-3">';
                echo '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                echo '<span>Error al subir el favicon: ' . implode(', ', $uploader->getErrors()) . '</span>';
                echo '</div>';
            }
        }
        
        if (isset($_POST['upload_cover']) && isset($_FILES['cover_file']) && $_FILES['cover_file']['error'] === UPLOAD_ERR_OK) {
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png', 'webp']);
            $result = $uploader->replaceFileKeepName(
                $_FILES['cover_file'], 
                __PATH_IMG__ . 'webengine.jpg', 
                'admin',
                'covers'
            );
            
            if ($result !== false) {
                echo '<div class="p-4 mb-6 bg-green-100 text-green-800 rounded-xl flex items-center gap-3">';
                echo '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                echo '<span>Imagen de portada actualizada correctamente.</span>';
                echo '</div>';
                
                $config = webengineConfigs();
                $config['website_cover'] = 'webengine.jpg';
                file_put_contents(__PATH_CONFIGS__.'webengine.json', json_encode($config, JSON_PRETTY_PRINT));
            } else {
                echo '<div class="p-4 mb-6 bg-red-100 text-red-800 rounded-xl flex items-center gap-3">';
                echo '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                echo '<span>Error al subir la imagen de portada: ' . implode(', ', $uploader->getErrors()) . '</span>';
                echo '</div>';
            }
        }
    }

    echo '<div class="grid gap-8 md:grid-cols-3 mb-8">';
        echo '<div class="glassmorphism rounded-xl p-6 shadow-lg">';
            echo '<h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">';
                echo '<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>';
                echo 'Logo del Sitio';
            echo '</h3>';
            
            echo '<div class="space-y-4">';
                $logoPath = __PATH_TEMPLATE_IMG__ . 'logo.png';
                if (file_exists($logoPath)) {
                    echo '<div class="flex justify-center mb-4">';
                    echo '<img src="' . __PATH_TEMPLATE_IMG__ . 'logo.png?' . time() . '" alt="Logo actual" class="max-h-32 object-contain border border-slate-200 rounded-lg p-2">';
                    echo '</div>';
                }
                
                echo '<form method="post" enctype="multipart/form-data" class="space-y-4">';
                    echo '<div class="space-y-2">';
                        echo '<label class="block text-sm font-medium text-slate-700">Seleccionar nuevo logo</label>';
                        echo '<p class="text-xs text-slate-500 mb-3">Formatos: JPG, PNG, GIF, SVG, WEBP. Tamaño máximo: 2MB</p>';
                        echo '<input type="file" name="logo_file" accept=".jpg,.jpeg,.png,.gif,.svg,.webp" class="w-full px-4 py-2 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">';
                    echo '</div>';
                    
                    echo '<button type="submit" name="upload_logo" class="w-full px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl transition-all shadow-lg font-medium flex items-center justify-center gap-2">';
                        echo '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>';
                        echo 'Actualizar Logo';
                    echo '</button>';
                echo '</form>';
                
                if (file_exists($logoPath)) {
                    $logoSize = filesize($logoPath);
                    $logoModified = filemtime($logoPath);
                    echo '<div class="mt-4 p-3 bg-slate-50 rounded-lg">';
                    echo '<p class="text-xs text-slate-600"><strong>Archivo:</strong> ' . basename($logoPath) . '</p>';
                    echo '<p class="text-xs text-slate-600"><strong>Tamaño:</strong> ' . round($logoSize / 1024, 2) . ' KB</p>';
                    echo '<p class="text-xs text-slate-600"><strong>Modificado:</strong> ' . date('d/m/Y H:i', $logoModified) . '</p>';
                    echo '</div>';
                }
            echo '</div>';
        echo '</div>';
        
        echo '<div class="glassmorphism rounded-xl p-6 shadow-lg">';
            echo '<h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">';
                echo '<svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>';
                echo 'Favicon';
            echo '</h3>';
            
            echo '<div class="space-y-4">';
                $faviconPath = __PATH_TEMPLATE__ . 'favicon.ico';
                if (file_exists($faviconPath)) {
                    echo '<div class="flex justify-center mb-4">';
                    echo '<img src="' . __PATH_TEMPLATE__ . 'favicon.ico?' . time() . '" alt="Favicon actual" class="w-16 h-16 object-contain border border-slate-200 rounded-lg p-2">';
                    echo '</div>';
                }
                
                echo '<form method="post" enctype="multipart/form-data" class="space-y-4">';
                    echo '<div class="space-y-2">';
                        echo '<label class="block text-sm font-medium text-slate-700">Seleccionar nuevo favicon</label>';
                        echo '<p class="text-xs text-slate-500 mb-3">Formatos: ICO, PNG. Tamaño recomendado: 32x32px o 64x64px</p>';
                        echo '<input type="file" name="favicon_file" accept=".ico,.png" class="w-full px-4 py-2 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">';
                    echo '</div>';
                    
                    echo '<button type="submit" name="upload_favicon" class="w-full px-4 py-2 bg-gradient-to-r from-yellow-600 to-orange-600 hover:from-yellow-700 hover:to-orange-700 text-white rounded-xl transition-all shadow-lg font-medium flex items-center justify-center gap-2">';
                        echo '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>';
                        echo 'Actualizar Favicon';
                    echo '</button>';
                echo '</form>';
                
                if (file_exists($faviconPath)) {
                    $faviconSize = filesize($faviconPath);
                    $faviconModified = filemtime($faviconPath);
                    echo '<div class="mt-4 p-3 bg-slate-50 rounded-lg">';
                    echo '<p class="text-xs text-slate-600"><strong>Archivo:</strong> ' . basename($faviconPath) . '</p>';
                    echo '<p class="text-xs text-slate-600"><strong>Tamaño:</strong> ' . round($faviconSize / 1024, 2) . ' KB</p>';
                    echo '<p class="text-xs text-slate-600"><strong>Modificado:</strong> ' . date('d/m/Y H:i', $faviconModified) . '</p>';
                    echo '</div>';
                }
            echo '</div>';
        echo '</div>';
        
        echo '<div class="glassmorphism rounded-xl p-6 shadow-lg">';
            echo '<h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">';
                echo '<svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>';
                echo 'Imagen de Portada';
            echo '</h3>';
            
            echo '<div class="space-y-4">';
                $coverPath = __PATH_IMG__ . 'webengine.jpg';
                if (file_exists($coverPath)) {
                    echo '<div class="flex justify-center mb-4">';
                    echo '<img src="' . __PATH_IMG__ . 'webengine.jpg?' . time() . '" alt="Portada actual" class="max-h-32 object-contain border border-slate-200 rounded-lg p-2">';
                    echo '</div>';
                }
                
                echo '<form method="post" enctype="multipart/form-data" class="space-y-4">';
                    echo '<div class="space-y-2">';
                        echo '<label class="block text-sm font-medium text-slate-700">Seleccionar nueva portada</label>';
                        echo '<p class="text-xs text-slate-500 mb-3">Formatos: JPG, PNG, WEBP. Tamaño recomendado: 1200x600px</p>';
                        echo '<input type="file" name="cover_file" accept=".jpg,.jpeg,.png,.webp" class="w-full px-4 py-2 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm">';
                    echo '</div>';
                    
                    echo '<button type="submit" name="upload_cover" class="w-full px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-xl transition-all shadow-lg font-medium flex items-center justify-center gap-2">';
                        echo '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>';
                        echo 'Actualizar Portada';
                    echo '</button>';
                echo '</form>';
                
                if (file_exists($coverPath)) {
                    $coverSize = filesize($coverPath);
                    $coverModified = filemtime($coverPath);
                    echo '<div class="mt-4 p-3 bg-slate-50 rounded-lg">';
                    echo '<p class="text-xs text-slate-600"><strong>Archivo:</strong> ' . basename($coverPath) . '</p>';
                    echo '<p class="text-xs text-slate-600"><strong>Tamaño:</strong> ' . round($coverSize / 1024, 2) . ' KB</p>';
                    echo '<p class="text-xs text-slate-600"><strong>Modificado:</strong> ' . date('d/m/Y H:i', $coverModified) . '</p>';
                    echo '</div>';
                }
            echo '</div>';
        echo '</div>';
    echo '</div>';
    
    echo '<div class="mb-8 glassmorphism rounded-xl p-6 shadow-lg">';
        echo '<h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">';
            echo '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
            echo 'Configuración Actual';
        echo '</h3>';
        
        $config = webengineConfigs();
        echo '<div class="grid gap-4 md:grid-cols-3">';
            echo '<div class="p-3 bg-slate-50 rounded-lg">';
                echo '<p class="text-sm font-medium text-slate-700">Logo configurado:</p>';
                echo '<p class="text-sm text-slate-600">' . ($config['website_logo'] ?? 'logo.png') . '</p>';
            echo '</div>';
            echo '<div class="p-3 bg-slate-50 rounded-lg">';
                echo '<p class="text-sm font-medium text-slate-700">Favicon configurado:</p>';
                echo '<p class="text-sm text-slate-600">' . ($config['website_favicon'] ?? 'favicon.ico') . '</p>';
            echo '</div>';
            echo '<div class="p-3 bg-slate-50 rounded-lg">';
                echo '<p class="text-sm font-medium text-slate-700">Portada configurada:</p>';
                echo '<p class="text-sm text-slate-600">' . ($config['website_cover'] ?? 'webengine.jpg') . '</p>';
            echo '</div>';
        echo '</div>';
        
        echo '<p class="text-xs text-slate-500 mt-4">Estos nombres se guardan en la configuración y se utilizan para cargar dinámicamente las imágenes en el sitio.</p>';
    echo '</div>';

    echo '<form action="" method="post" class="space-y-8">';
        echo '<div class="glassmorphism rounded-xl p-6 shadow-lg">';
            echo '<h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">';
                echo '<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>';
                echo 'Estado del Sistema';
            echo '</h3>';
            
            echo '<div class="grid gap-6 md:grid-cols-2">';
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Sistema Activo</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">Activa/desactiva tu sitio web completamente.</p>';
                    echo '<div class="flex items-center gap-4">';
                        echo '<label class="inline-flex items-center">';
                            echo '<input type="radio" name="system_active" value="1" '.(config('system_active',true) ? 'checked' : null).' class="w-4 h-4 text-blue-600 bg-white border-slate-300 focus:ring-blue-500">';
                            echo '<span class="ml-2">Activado</span>';
                        echo '</label>';
                        echo '<label class="inline-flex items-center">';
                            echo '<input type="radio" name="system_active" value="0" '.(!config('system_active',true) ? 'checked' : null).' class="w-4 h-4 text-blue-600 bg-white border-slate-300 focus:ring-blue-500">';
                            echo '<span class="ml-2">Desactivado</span>';
                        echo '</label>';
                    echo '</div>';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Reporte de Errores</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">Habilita para mostrar errores (solo desarrollo).</p>';
                    echo '<div class="flex items-center gap-4">';
                        echo '<label class="inline-flex items-center">';
                            echo '<input type="radio" name="error_reporting" value="1" '.(config('error_reporting',true) ? 'checked' : null).' class="w-4 h-4 text-blue-600 bg-white border-slate-300 focus:ring-blue-500">';
                            echo '<span class="ml-2">Activado</span>';
                        echo '</label>';
                        echo '<label class="inline-flex items-center">';
                            echo '<input type="radio" name="error_reporting" value="0" '.(!config('error_reporting',true) ? 'checked' : null).' class="w-4 h-4 text-blue-600 bg-white border-slate-300 focus:ring-blue-500">';
                            echo '<span class="ml-2">Desactivado</span>';
                        echo '</label>';
                    echo '</div>';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Sistema de Bloqueo por IP</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">Activa/desactiva el bloqueo de IPs.</p>';
                    echo '<div class="flex items-center gap-4">';
                        echo '<label class="inline-flex items-center">';
                            echo '<input type="radio" name="ip_block_system_enable" value="1" '.(config('ip_block_system_enable',true) ? 'checked' : null).' class="w-4 h-4 text-blue-600 bg-white border-slate-300 focus:ring-blue-500">';
                            echo '<span class="ml-2">Activado</span>';
                        echo '</label>';
                        echo '<label class="inline-flex items-center">';
                            echo '<input type="radio" name="ip_block_system_enable" value="0" '.(!config('ip_block_system_enable',true) ? 'checked' : null).' class="w-4 h-4 text-blue-600 bg-white border-slate-300 focus:ring-blue-500">';
                            echo '<span class="ml-2">Desactivado</span>';
                        echo '</label>';
                    echo '</div>';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Página de Mantenimiento</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">URL para redireccionar durante mantenimiento.</p>';
                    echo '<input type="text" name="maintenance_page" value="'.config('maintenance_page',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
            echo '</div>';
        echo '</div>';

        echo '<div class="glassmorphism rounded-xl p-6 shadow-lg">';
            echo '<h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">';
                echo '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>';
                echo 'Información del Sitio Web';
            echo '</h3>';
            
            echo '<div class="grid gap-6 md:grid-cols-2">';
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Nombre del Sitio</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">Nombre principal de tu sitio web.</p>';
                    echo '<input type="text" name="website_name" value="'.config('website_name',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Título del Sitio</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">Título que aparece en el navegador.</p>';
                    echo '<input type="text" name="website_title" value="'.config('website_title',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Eslogan</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">Frase corta que describes tu sitio.</p>';
                    echo '<input type="text" name="website_slogan" value="'.config('website_slogan',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Texto de Copyright</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">Información de derechos de autor.</p>';
                    echo '<input type="text" name="website_copyright" value="'.config('website_copyright',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Logo</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">URL del logotipo del sitio.</p>';
                    echo '<input type="text" name="website_logo" value="'.config('website_logo',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Favicon</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">URL del favicon del sitio.</p>';
                    echo '<input type="text" name="website_favicon" value="'.config('website_favicon',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
            echo '</div>';
        echo '</div>';

        echo '<div class="glassmorphism rounded-xl p-6 shadow-lg">';
            echo '<h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">';
                echo '<svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>';
                echo 'Configuración SEO';
            echo '</h3>';
            
            echo '<div class="grid gap-6">';
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Meta Descripción</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">Descripción para motores de búsqueda (máx. 160 caracteres).</p>';
                    echo '<textarea name="website_meta_description" rows="2" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">'.config('website_meta_description',true).'</textarea>';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Palabras Clave</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">Palabras clave separadas por comas.</p>';
                    echo '<input type="text" name="website_meta_keywords" value="'.config('website_meta_keywords',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Autor</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">Autor del sitio web.</p>';
                    echo '<input type="text" name="website_meta_author" value="'.config('website_meta_author',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Instrucciones para Robots</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">Cómo deben indexar los motores de búsqueda.</p>';
                    echo '<input type="text" name="website_meta_robots" value="'.config('website_meta_robots',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
            echo '</div>';
        echo '</div>';

        echo '<div class="glassmorphism rounded-xl p-6 shadow-lg">';
            echo '<h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">';
                echo '<svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>';
                echo 'Redes Sociales';
            echo '</h3>';
            
            echo '<div class="grid gap-6 md:grid-cols-2">';
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Facebook</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">URL completa de tu página de Facebook.</p>';
                    echo '<input type="text" name="social_link_facebook" value="'.config('social_link_facebook',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Instagram</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">URL completa de tu perfil de Instagram.</p>';
                    echo '<input type="text" name="social_link_instagram" value="'.config('social_link_instagram',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Discord</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">URL de invitación a tu servidor de Discord.</p>';
                    echo '<input type="text" name="social_link_discord" value="'.config('social_link_discord',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Twitter</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">URL completa de tu perfil de Twitter.</p>';
                    echo '<input type="text" name="social_link_twitter" value="'.config('social_link_twitter',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">LinkedIn</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">URL completa de your LinkedIn profile.</p>';
                    echo '<input type="text" name="social_link_linkedin" value="'.config('social_link_linkedin',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">YouTube</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">URL completa de tu canal de YouTube.</p>';
                    echo '<input type="text" name="social_link_youtube" value="'.config('social_link_youtube',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
            echo '</div>';
        echo '</div>';

        echo '<div class="glassmorphism rounded-xl p-6 shadow-lg">';
            echo '<h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">';
                echo '<svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>';
                echo 'Seguridad y Validación';
            echo '</h3>';
            
            echo '<div class="grid gap-6 md:grid-cols-2">';
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Long. Mínima de Usuario</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">Mínimo de caracteres para nombres de usuario.</p>';
                    echo '<input type="number" name="username_min_len" value="'.config('username_min_len',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Long. Máxima de Usuario</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">Máximo de caracteres para nombres de usuario.</p>';
                    echo '<input type="number" name="username_max_len" value="'.config('username_max_len',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Long. Mínima de Contraseña</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">Mínimo de caracteres para contraseñas.</p>';
                    echo '<input type="number" name="password_min_len" value="'.config('password_min_len',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Long. Máxima de Contraseña</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">Máximo de caracteres para contraseñas.</p>';
                    echo '<input type="number" name="password_max_len" value="'.config('password_max_len',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">API de Cron</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">Activa/desactiva la API para tareas programadas.</p>';
                    echo '<div class="flex items-center gap-4">';
                        echo '<label class="inline-flex items-center">';
                            echo '<input type="radio" name="cron_api" value="1" '.(config('cron_api',true) ? 'checked' : null).' class="w-4 h-4 text-blue-600 bg-white border-slate-300 focus:ring-blue-500">';
                            echo '<span class="ml-2">Activado</span>';
                        echo '</label>';
                        echo '<label class="inline-flex items-center">';
                            echo '<input type="radio" name="cron_api" value="0" '.(!config('cron_api',true) ? 'checked' : null).' class="w-4 h-4 text-blue-600 bg-white border-slate-300 focus:ring-blue-500">';
                            echo '<span class="ml-2">Desactivado</span>';
                        echo '</label>';
                    echo '</div>';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Clave API de Cron</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">Clave secreta para acceder a la API de cron.</p>';
                    echo '<input type="text" name="cron_api_key" value="'.config('cron_api_key',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
            echo '</div>';
        echo '</div>';

        echo '<div class="glassmorphism rounded-xl p-6 shadow-lg">';
            echo '<h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">';
                echo '<svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                echo 'Configuración Regional';
            echo '</h3>';
            
            echo '<div class="grid gap-6 md:grid-cols-2">';
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Idioma del Sitio</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">Idioma principal del sitio web.</p>';
                    echo '<input type="text" name="website_language" value="'.config('website_language',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
                
                echo '<div class="space-y-2">';
                    echo '<label class="block text-sm font-medium text-slate-700">Zona Horaria</label>';
                    echo '<p class="text-xs text-slate-500 mb-3">Zona horaria del servidor.</p>';
                    echo '<input type="text" name="website_timezone" value="'.config('website_timezone',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">';
                echo '</div>';
            echo '</div>';
        echo '</div>';

        echo '<div class="flex justify-end pt-4">';
            echo '<button type="submit" name="settings_submit" value="ok" class="flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all shadow-lg font-medium">';
                echo '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                echo 'Guardar Configuración';
            echo '</button>';
        echo '</div>';
    echo '</form>';
echo '</div>';
echo '</div>';

echo '<script>';
echo 'function resetInstallation() {';
echo '    if(confirm("¿Estás seguro de que deseas reiniciar la instalación? Esto cambiará webengine_cms_installed a false.")) {';
echo '        window.location.href = "?reset_installation=1";';
echo '    }';
echo '}';
echo '</script>';
?>