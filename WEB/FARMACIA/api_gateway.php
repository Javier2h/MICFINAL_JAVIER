<?php
// API Gateway para microservicios en PHP
header('Content-Type: application/json');

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Quitar parámetros de query y base path
$path = parse_url($uri, PHP_URL_PATH);
$path = trim($path, '/');

// Detectar microservicio y construir endpoint
$routes = [
    'medicamentos' => 'api/medicamentos.php',
    // Agrega aquí más rutas: 'clientes' => 'api/clientes.php', etc.
];

// Extraer entidad (ej: api_gateway.php/medicamentos)
$parts = explode('/', $path);
$entity = isset($parts[1]) ? $parts[1] : '';

if (isset($routes[$entity])) {
    // Redirigir la petición al microservicio correspondiente
    $_SERVER['SCRIPT_FILENAME'] = $routes[$entity];
    include $routes[$entity];
    exit;
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Microservicio no encontrado']);
    exit;
} 