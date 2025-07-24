<?php
header('Content-Type: application/json');
include '../config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Búsqueda y listado
        $busqueda = isset($_GET['buscar']) ? $conn->real_escape_string($_GET['buscar']) : '';
        $filtro_proveedor = isset($_GET['filtro_proveedor']) ? intval($_GET['filtro_proveedor']) : 0;
        $filtro_categoria = isset($_GET['filtro_categoria']) ? intval($_GET['filtro_categoria']) : 0;
        $sql = "SELECT m.*, p.nombre_proveedor, c.nombre_categoria FROM Medicamento m LEFT JOIN Proveedor p ON m.id_proveedor = p.id_proveedor LEFT JOIN CategoriaMedicamento c ON m.id_categoria = c.id_categoria WHERE 1=1";
        if (!empty($busqueda)) {
            $sql .= " AND (m.nombre_medicamento LIKE '%$busqueda%' OR m.principio_activo LIKE '%$busqueda%' OR m.presentacion LIKE '%$busqueda%' OR p.nombre_proveedor LIKE '%$busqueda%' OR c.nombre_categoria LIKE '%$busqueda%')";
        }
        if ($filtro_proveedor > 0) {
            $sql .= " AND m.id_proveedor = $filtro_proveedor";
        }
        if ($filtro_categoria > 0) {
            $sql .= " AND m.id_categoria = $filtro_categoria";
        }
        $sql .= " ORDER BY m.id_medicamento";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data]);
        break;
    case 'POST':
        // Crear
        $input = json_decode(file_get_contents('php://input'), true);
        $nombre = $conn->real_escape_string($input['nombre_medicamento']);
        $principio_activo = $conn->real_escape_string($input['principio_activo']);
        $presentacion = $conn->real_escape_string($input['presentacion']);
        $fecha_caducidad = $conn->real_escape_string($input['fecha_caducidad']);
        $stock = intval($input['stock']);
        $precio_unitario = floatval($input['precio_unitario']);
        $id_proveedor = intval($input['id_proveedor']);
        $id_categoria = intval($input['id_categoria']);
        $sql = "INSERT INTO Medicamento (nombre_medicamento, principio_activo, presentacion, fecha_caducidad, stock, precio_unitario, id_proveedor, id_categoria) VALUES ('$nombre', '$principio_activo', '$presentacion', '$fecha_caducidad', $stock, $precio_unitario, $id_proveedor, $id_categoria)";
        $ok = $conn->query($sql);
        echo json_encode(['success' => $ok, 'id' => $conn->insert_id]);
        break;
    case 'PUT':
        // Actualizar
        parse_str(file_get_contents('php://input'), $input);
        $id = intval($input['id_medicamento']);
        $nombre = $conn->real_escape_string($input['nombre_medicamento']);
        $principio_activo = $conn->real_escape_string($input['principio_activo']);
        $presentacion = $conn->real_escape_string($input['presentacion']);
        $fecha_caducidad = $conn->real_escape_string($input['fecha_caducidad']);
        $stock = intval($input['stock']);
        $precio_unitario = floatval($input['precio_unitario']);
        $id_proveedor = intval($input['id_proveedor']);
        $id_categoria = intval($input['id_categoria']);
        $sql = "UPDATE Medicamento SET nombre_medicamento='$nombre', principio_activo='$principio_activo', presentacion='$presentacion', fecha_caducidad='$fecha_caducidad', stock=$stock, precio_unitario=$precio_unitario, id_proveedor=$id_proveedor, id_categoria=$id_categoria WHERE id_medicamento=$id";
        $ok = $conn->query($sql);
        echo json_encode(['success' => $ok]);
        break;
    case 'DELETE':
        // Eliminar
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $ok = false;
        if ($id > 0) {
            $ok = $conn->query("DELETE FROM Medicamento WHERE id_medicamento = $id");
        }
        echo json_encode(['success' => $ok]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Método no permitido']);
        break;
} 