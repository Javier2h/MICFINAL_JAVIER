<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';
$rol = $_SESSION['rol'];

if (!in_array($rol, ['Administrador', 'Desarrollador', 'Supervisor'])) {
    die("No tienes permisos para acceder.");
}

if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM DetalleVenta WHERE id_venta = $id");
    $conn->query("DELETE FROM Venta WHERE id_venta = $id");
    header("Location: crud_ventas.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_cliente = $_POST['id_cliente'];
    $id_empleado = $_POST['id_empleado'];
    $fecha_venta = $_POST['fecha_venta'];
    $total = $_POST['total'];
    
    if (isset($_POST['id_venta'])) {
        $id = intval($_POST['id_venta']);
        $conn->query("UPDATE Venta SET id_cliente=$id_cliente, id_empleado=$id_empleado, fecha_venta='$fecha_venta', total=$total WHERE id_venta=$id");
    } else {
        $conn->query("INSERT INTO Venta (id_cliente, id_empleado, fecha_venta, total) VALUES ($id_cliente, $id_empleado, '$fecha_venta', $total)");
    }
    header("Location: crud_ventas.php");
    exit();
}

// Lógica de búsqueda
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$filtro_cliente = isset($_GET['filtro_cliente']) ? intval($_GET['filtro_cliente']) : 0;
$filtro_empleado = isset($_GET['filtro_empleado']) ? intval($_GET['filtro_empleado']) : 0;
$filtro_fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '';
$filtro_fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '';
$filtro_total_min = isset($_GET['total_min']) ? floatval($_GET['total_min']) : 0;
$filtro_total_max = isset($_GET['total_max']) ? floatval($_GET['total_max']) : 0;

// Construir la consulta SQL con filtros
$sql = "
    SELECT v.*, c.nombre_cliente, e.nombre_empleado 
    FROM Venta v 
    LEFT JOIN Cliente c ON v.id_cliente = c.id_cliente 
    LEFT JOIN Empleado e ON v.id_empleado = e.id_empleado 
    WHERE 1=1
";

if (!empty($busqueda)) {
    $busqueda_escaped = $conn->real_escape_string($busqueda);
    $sql .= " AND (c.nombre_cliente LIKE '%$busqueda_escaped%' 
              OR e.nombre_empleado LIKE '%$busqueda_escaped%'
              OR c.cedula LIKE '%$busqueda_escaped%')";
}

if ($filtro_cliente > 0) {
    $sql .= " AND v.id_cliente = $filtro_cliente";
}

if ($filtro_empleado > 0) {
    $sql .= " AND v.id_empleado = $filtro_empleado";
}

if (!empty($filtro_fecha_desde)) {
    $sql .= " AND v.fecha_venta >= '$filtro_fecha_desde'";
}

if (!empty($filtro_fecha_hasta)) {
    $sql .= " AND v.fecha_venta <= '$filtro_fecha_hasta'";
}

if ($filtro_total_min > 0) {
    $sql .= " AND v.total >= $filtro_total_min";
}

if ($filtro_total_max > 0) {
    $sql .= " AND v.total <= $filtro_total_max";
}

$sql .= " ORDER BY v.id_venta";

$result = $conn->query($sql);
$clientes = $conn->query("SELECT * FROM Cliente ORDER BY nombre_cliente");
$empleados = $conn->query("SELECT * FROM Empleado ORDER BY nombre_empleado");
$edit = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $res = $conn->query("SELECT * FROM Venta WHERE id_venta = $id");
    $edit = $res->fetch_assoc();
}
$solo_lectura = ($rol == 'Supervisor');
?>
<!DOCTYPE html>
<html>
<head>
    <title>CRUD Ventas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f4; }
        .container { max-width: 1200px; margin: 0 auto; background-color: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #4CAF50; color: white; }
        tr:hover { background-color: #f5f5f5; }
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 3px; margin: 2px; }
        .btn-edit { background-color: #2196F3; color: white; }
        .btn-delete { background-color: #f44336; color: white; }
        .btn-back { background-color: #666; color: white; }
        .btn-detail { background-color: #FF9800; color: white; }
        .btn-search { background-color: #4CAF50; color: white; }
        .btn-clear { background-color: #ff9800; color: white; }
        form { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        input[type="text"], input[type="number"], input[type="datetime-local"], select { width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #ddd; border-radius: 3px; }
        input[type="submit"] { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; }
        input[type="submit"]:hover { background-color: #45a049; }
        .form-row { display: flex; gap: 20px; }
        .form-group { flex: 1; }
        .total { font-weight: bold; color: #4CAF50; }
        .search-form { background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .search-form h3 { margin-top: 0; color: #333; }
        .search-buttons { display: flex; gap: 10px; margin-top: 10px; }
        .search-buttons input[type="submit"] { padding: 8px 15px; }
        .search-buttons a { padding: 8px 15px; text-decoration: none; border-radius: 3px; }
        .results-info { background-color: #e8f5e8; padding: 10px; border-radius: 3px; margin-bottom: 15px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Gestión de Ventas</h2>
    <a href="index.php" class="btn btn-back">Volver al Menú Principal</a>
    
    <!-- Formulario de búsqueda -->
    <div class="search-form">
        <h3>Buscar Ventas</h3>
        <form method="get" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Buscar por cliente o empleado:</label>
                    <input type="text" name="buscar" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Ingrese nombre de cliente o empleado...">
                </div>
                <div class="form-group">
                    <label>Filtrar por Cliente:</label>
                    <select name="filtro_cliente">
                        <option value="0">Todos los clientes</option>
                        <?php 
                        $clientes_para_filtro = $conn->query("SELECT * FROM Cliente ORDER BY nombre_cliente");
                        while($cli = $clientes_para_filtro->fetch_assoc()): 
                        ?>
                            <option value="<?= $cli['id_cliente'] ?>" <?= $filtro_cliente == $cli['id_cliente'] ? 'selected' : '' ?>><?= htmlspecialchars($cli['nombre_cliente']) ?> - <?= htmlspecialchars($cli['cedula']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Filtrar por Empleado:</label>
                    <select name="filtro_empleado">
                        <option value="0">Todos los empleados</option>
                        <?php 
                        $empleados_para_filtro = $conn->query("SELECT * FROM Empleado ORDER BY nombre_empleado");
                        while($emp = $empleados_para_filtro->fetch_assoc()): 
                        ?>
                            <option value="<?= $emp['id_empleado'] ?>" <?= $filtro_empleado == $emp['id_empleado'] ? 'selected' : '' ?>><?= htmlspecialchars($emp['nombre_empleado']) ?> - <?= htmlspecialchars($emp['cargo']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Fecha de venta desde:</label>
                    <input type="date" name="fecha_desde" value="<?= htmlspecialchars($filtro_fecha_desde) ?>">
                </div>
                <div class="form-group">
                    <label>Fecha de venta hasta:</label>
                    <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($filtro_fecha_hasta) ?>">
                </div>
                <div class="form-group">
                    <label>Total mínimo:</label>
                    <input type="number" step="0.01" name="total_min" value="<?= $filtro_total_min > 0 ? $filtro_total_min : '' ?>" min="0" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Total máximo:</label>
                    <input type="number" step="0.01" name="total_max" value="<?= $filtro_total_max > 0 ? $filtro_total_max : '' ?>" min="0" placeholder="0.00">
                </div>
            </div>
            <div class="search-buttons">
                <input type="submit" value="Buscar" class="btn-search">
                <a href="crud_ventas.php" class="btn-clear">Limpiar Filtros</a>
            </div>
        </form>
    </div>

    <!-- Información de resultados -->
    <?php 
    $total_resultados = $result->num_rows;
    if (!empty($busqueda) || $filtro_cliente > 0 || $filtro_empleado > 0 || !empty($filtro_fecha_desde) || !empty($filtro_fecha_hasta) || $filtro_total_min > 0 || $filtro_total_max > 0): 
    ?>
    <div class="results-info">
        <strong>Resultados de búsqueda:</strong> Se encontraron <?= $total_resultados ?> venta(s)
        <?php if (!empty($busqueda)): ?> que coinciden con "<?= htmlspecialchars($busqueda) ?>"<?php endif; ?>
        <?php if ($filtro_cliente > 0): ?>
            <?php 
            $cli_nombre = $conn->query("SELECT nombre_cliente FROM Cliente WHERE id_cliente = $filtro_cliente")->fetch_assoc();
            ?> del cliente "<?= htmlspecialchars($cli_nombre['nombre_cliente']) ?>"
        <?php endif; ?>
        <?php if ($filtro_empleado > 0): ?>
            <?php 
            $emp_nombre = $conn->query("SELECT nombre_empleado FROM Empleado WHERE id_empleado = $filtro_empleado")->fetch_assoc();
            ?> del empleado "<?= htmlspecialchars($emp_nombre['nombre_empleado']) ?>"
        <?php endif; ?>
        <?php if (!empty($filtro_fecha_desde) || !empty($filtro_fecha_hasta)): ?>
            realizadas 
            <?php if (!empty($filtro_fecha_desde)): ?>desde <?= $filtro_fecha_desde ?><?php endif; ?>
            <?php if (!empty($filtro_fecha_hasta)): ?>hasta <?= $filtro_fecha_hasta ?><?php endif; ?>
        <?php endif; ?>
        <?php if ($filtro_total_min > 0 || $filtro_total_max > 0): ?>
            con total 
            <?php if ($filtro_total_min > 0): ?>mínimo $<?= number_format($filtro_total_min, 2) ?><?php endif; ?>
            <?php if ($filtro_total_max > 0): ?>máximo $<?= number_format($filtro_total_max, 2) ?><?php endif; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Empleado</th>
            <th>Fecha de Venta</th>
            <th>Total</th>
            <th>Acciones</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id_venta'] ?></td>
            <td><?= htmlspecialchars($row['nombre_cliente']) ?></td>
            <td><?= htmlspecialchars($row['nombre_empleado']) ?></td>
            <td><?= $row['fecha_venta'] ?></td>
            <td class="total">$<?= number_format($row['total'], 2) ?></td>
            <td>
                <?php if (!$solo_lectura): ?>
                <a href="?editar=<?= $row['id_venta'] ?>" class="btn btn-edit">Editar</a>
                <a href="crud_detalle_venta.php?venta_id=<?= $row['id_venta'] ?>" class="btn btn-detail">Detalles</a>
                <a href="?eliminar=<?= $row['id_venta'] ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro de eliminar esta venta?')">Eliminar</a>
                <?php else: ?>
                <a href="crud_detalle_venta.php?venta_id=<?= $row['id_venta'] ?>" class="btn btn-detail">Ver Detalles</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    
    <?php if (!$solo_lectura): ?>
    <h3><?= $edit ? 'Editar' : 'Agregar' ?> Venta</h3>
    <form method="post">
        <?php if ($edit): ?><input type="hidden" name="id_venta" value="<?= $edit['id_venta'] ?>"><?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label>Cliente:</label>
                <select name="id_cliente" required>
                    <option value="">Seleccionar cliente</option>
                    <?php while($cli = $clientes->fetch_assoc()): ?>
                        <option value="<?= $cli['id_cliente'] ?>" <?= (isset($edit['id_cliente']) && $edit['id_cliente'] == $cli['id_cliente']) ? 'selected' : '' ?>><?= htmlspecialchars($cli['nombre_cliente']) ?> - <?= htmlspecialchars($cli['cedula']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Empleado:</label>
                <select name="id_empleado" required>
                    <option value="">Seleccionar empleado</option>
                    <?php while($emp = $empleados->fetch_assoc()): ?>
                        <option value="<?= $emp['id_empleado'] ?>" <?= (isset($edit['id_empleado']) && $edit['id_empleado'] == $emp['id_empleado']) ? 'selected' : '' ?>><?= htmlspecialchars($emp['nombre_empleado']) ?> - <?= htmlspecialchars($emp['cargo']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Fecha y Hora de Venta:</label>
                <input type="datetime-local" name="fecha_venta" value="<?= $edit ? str_replace(' ', 'T', $edit['fecha_venta']) : date('Y-m-d\TH:i') ?>" required>
            </div>
            <div class="form-group">
                <label>Total:</label>
                <input type="number" step="0.01" name="total" value="<?= $edit['total'] ?? '' ?>" min="0" required>
            </div>
        </div>
        
        <input type="submit" value="<?= $edit ? 'Actualizar' : 'Guardar' ?>">
    </form>
    <?php endif; ?>
</div>
</body>
</html> 