<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Función para verificar si el rol tiene permiso
function tienePermiso($rolesPermitidos)
{
    return isset($_SESSION['rol']) && in_array($_SESSION['rol'], $rolesPermitidos);
}
