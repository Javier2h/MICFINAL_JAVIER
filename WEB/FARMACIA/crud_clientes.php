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
    $conn->query("DELETE FROM Cliente WHERE id_cliente = $id");
    header("Location: crud_clientes.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre_cliente'];
    $cedula = $_POST['cedula'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $fecha_registro = $_POST['fecha_registro'];
    
    if (isset($_POST['id_cliente'])) {
        $id = intval($_POST['id_cliente']);
        $conn->query("UPDATE Cliente SET nombre_cliente='$nombre', cedula='$cedula', telefono='$telefono', correo='$correo', fecha_registro='$fecha_registro' WHERE id_cliente=$id");
    } else {
        $conn->query("INSERT INTO Cliente (nombre_cliente, cedula, telefono, correo, fecha_registro) VALUES ('$nombre', '$cedula', '$telefono', '$correo', '$fecha_registro')");
    }
    header("Location: crud_clientes.php");
    exit();
}

// Lógica de búsqueda
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$filtro_fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '';
$filtro_fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '';

// Construir la consulta SQL con filtros
$sql = "SELECT * FROM Cliente WHERE 1=1";

if (!empty($busqueda)) {
    $busqueda_escaped = $conn->real_escape_string($busqueda);
    $sql .= " AND (nombre_cliente LIKE '%$busqueda_escaped%' 
              OR cedula LIKE '%$busqueda_escaped%' 
              OR telefono LIKE '%$busqueda_escaped%'
              OR correo LIKE '%$busqueda_escaped%')";
}

if (!empty($filtro_fecha_desde)) {
    $sql .= " AND fecha_registro >= '$filtro_fecha_desde'";
}

if (!empty($filtro_fecha_hasta)) {
    $sql .= " AND fecha_registro <= '$filtro_fecha_hasta'";
}

$sql .= " ORDER BY id_cliente";

$result = $conn->query($sql);
$edit = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $res = $conn->query("SELECT * FROM Cliente WHERE id_cliente = $id");
    $edit = $res->fetch_assoc();
}
$solo_lectura = ($rol == 'Supervisor');
?>
<!DOCTYPE html>
<html>
<head>
    <title>CRUD Clientes</title>
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
        .btn-search { background-color: #4CAF50; color: white; }
        .btn-clear { background-color: #ff9800; color: white; }
        form { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        input[type="text"], input[type="email"], input[type="date"] { width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #ddd; border-radius: 3px; }
        input[type="submit"] { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; }
        input[type="submit"]:hover { background-color: #45a049; }
        .form-row { display: flex; gap: 20px; }
        .form-group { flex: 1; }
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
    <h2>Gestión de Clientes</h2>
    <a href="index.php" class="btn btn-back">Volver al Menú Principal</a>
    
    <!-- Formulario de búsqueda -->
    <div class="search-form">
        <h3>Buscar Clientes</h3>
        <form method="get" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Buscar por nombre, cédula, teléfono o correo:</label>
                    <input type="text" name="buscar" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Ingrese término de búsqueda...">
                </div>
                <div class="form-group">
                    <label>Fecha de registro desde:</label>
                    <input type="date" name="fecha_desde" value="<?= htmlspecialchars($filtro_fecha_desde) ?>">
                </div>
                <div class="form-group">
                    <label>Fecha de registro hasta:</label>
                    <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($filtro_fecha_hasta) ?>">
                </div>
            </div>
            <div class="search-buttons">
                <input type="submit" value="Buscar" class="btn-search">
                <a href="crud_clientes.php" class="btn-clear">Limpiar Filtros</a>
            </div>
        </form>
    </div>

    <!-- Información de resultados -->
    <?php 
    $total_resultados = $result->num_rows;
    if (!empty($busqueda) || !empty($filtro_fecha_desde) || !empty($filtro_fecha_hasta)): 
    ?>
    <div class="results-info">
        <strong>Resultados de búsqueda:</strong> Se encontraron <?= $total_resultados ?> cliente(s)
        <?php if (!empty($busqueda)): ?> que coinciden con "<?= htmlspecialchars($busqueda) ?>"<?php endif; ?>
        <?php if (!empty($filtro_fecha_desde) || !empty($filtro_fecha_hasta)): ?>
            registrados 
            <?php if (!empty($filtro_fecha_desde)): ?>desde <?= $filtro_fecha_desde ?><?php endif; ?>
            <?php if (!empty($filtro_fecha_hasta)): ?>hasta <?= $filtro_fecha_hasta ?><?php endif; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre del Cliente</th>
            <th>Cédula</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <th>Fecha de Registro</th>
            <?php if (!$solo_lectura): ?><th>Acciones</th><?php endif; ?>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id_cliente'] ?></td>
            <td><?= htmlspecialchars($row['nombre_cliente']) ?></td>
            <td><?= htmlspecialchars($row['cedula']) ?></td>
            <td><?= htmlspecialchars($row['telefono']) ?></td>
            <td><?= htmlspecialchars($row['correo']) ?></td>
            <td><?= $row['fecha_registro'] ?></td>
            <?php if (!$solo_lectura): ?>
            <td>
                <a href="?editar=<?= $row['id_cliente'] ?>" class="btn btn-edit">Editar</a>
                <a href="?eliminar=<?= $row['id_cliente'] ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro de eliminar este cliente?')">Eliminar</a>
            </td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
    </table>
    
    <?php if (!$solo_lectura): ?>
    <h3><?= $edit ? 'Editar' : 'Agregar' ?> Cliente</h3>
    <form method="post">
        <?php if ($edit): ?><input type="hidden" name="id_cliente" value="<?= $edit['id_cliente'] ?>"><?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label>Nombre del Cliente:</label>
                <input type="text" name="nombre_cliente" value="<?= htmlspecialchars($edit['nombre_cliente'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Cédula:</label>
                <input type="text" name="cedula" value="<?= htmlspecialchars($edit['cedula'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Teléfono:</label>
                <input type="text" name="telefono" value="<?= htmlspecialchars($edit['telefono'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Correo:</label>
                <input type="email" name="correo" value="<?= htmlspecialchars($edit['correo'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label>Fecha de Registro:</label>
            <input type="date" name="fecha_registro" value="<?= $edit['fecha_registro'] ?? date('Y-m-d') ?>" required>
        </div>
        
        <input type="submit" value="<?= $edit ? 'Actualizar' : 'Guardar' ?>">
    </form>
    <?php endif; ?>
</div>
</body>
</html> 