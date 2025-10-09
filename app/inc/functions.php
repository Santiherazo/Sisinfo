<?php 
function evalcp_base($module="") {
	if(check_value($module)) return __PATH_EPANEL_HOME__ . "?module=" . $module;
	return __PATH_EPANEL_HOME__;
}

function obtenerCalificacionProyecto($calificaciones, $projectId) {
    if (empty($calificaciones) || !is_array($calificaciones)) {
        return null;
    }
    
    foreach ($calificaciones as $calificacion) {
        if (is_array($calificacion) && 
            isset($calificacion['project_id']) && 
            $calificacion['project_id'] == $projectId) {
            return $calificacion['calificacion_total'] ?? null;
        }
    }
    
    return null;
}

?>
