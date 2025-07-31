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
    $conn->query("DELETE FROM DetalleReceta WHERE id_receta = $id");
    $conn->query("DELETE FROM Receta WHERE id_receta = $id");
    header("Location: crud_recetas.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_cliente = $_POST['id_cliente'];
    $nombre_medico = $_POST['nombre_medico'];
    $diagnostico = $_POST['diagnostico'];
    $fecha_emision = $_POST['fecha_emision'];
    
    if (isset($_POST['id_receta'])) {
        $id = intval($_POST['id_receta']);
        $conn->query("UPDATE Receta SET id_cliente=$id_cliente, nombre_medico='$nombre_medico', diagnostico='$diagnostico', fecha_emision='$fecha_emision' WHERE id_receta=$id");
    } else {
        $conn->query("INSERT INTO Receta (id_cliente, nombre_medico, diagnostico, fecha_emision) VALUES ($id_cliente, '$nombre_medico', '$diagnostico', '$fecha_emision')");
    }
    header("Location: crud_recetas.php");
    exit();
}

// Lógica de búsqueda
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$filtro_cliente = isset($_GET['filtro_cliente']) ? intval($_GET['filtro_cliente']) : 0;
$filtro_fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '';
$filtro_fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '';

// Construir la consulta SQL con filtros
$sql = "
    SELECT r.*, c.nombre_cliente 
    FROM Receta r 
    LEFT JOIN Cliente c ON r.id_cliente = c.id_cliente 
    WHERE 1=1
";

if (!empty($busqueda)) {
    $busqueda_escaped = $conn->real_escape_string($busqueda);
    $sql .= " AND (c.nombre_cliente LIKE '%$busqueda_escaped%' 
              OR r.nombre_medico LIKE '%$busqueda_escaped%'
              OR r.diagnostico LIKE '%$busqueda_escaped%'
              OR c.cedula LIKE '%$busqueda_escaped%')";
}

if ($filtro_cliente > 0) {
    $sql .= " AND r.id_cliente = $filtro_cliente";
}

if (!empty($filtro_fecha_desde)) {
    $sql .= " AND r.fecha_emision >= '$filtro_fecha_desde'";
}

if (!empty($filtro_fecha_hasta)) {
    $sql .= " AND r.fecha_emision <= '$filtro_fecha_hasta'";
}

$sql .= " ORDER BY r.id_receta";

$result = $conn->query($sql);
$clientes = $conn->query("SELECT * FROM Cliente ORDER BY nombre_cliente");
$edit = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $res = $conn->query("SELECT * FROM Receta WHERE id_receta = $id");
    $edit = $res->fetch_assoc();
}
$solo_lectura = ($rol == 'Supervisor');
?>
<!DOCTYPE html>
<html>
<head>
    <title>CRUD Recetas Médicas</title>
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
        input[type="text"], input[type="date"], select, textarea { width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #ddd; border-radius: 3px; }
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
    <h2>Gestión de Recetas Médicas</h2>
    <a href="index.php" class="btn btn-back">Volver al Menú Principal</a>
    
    <!-- Formulario de búsqueda -->
    <div class="search-form">
        <h3>Buscar Recetas</h3>
        <form method="get" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Buscar por cliente, médico o diagnóstico:</label>
                    <input type="text" name="buscar" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Ingrese término de búsqueda...">
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
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Fecha de emisión desde:</label>
                    <input type="date" name="fecha_desde" value="<?= htmlspecialchars($filtro_fecha_desde) ?>">
                </div>
                <div class="form-group">
                    <label>Fecha de emisión hasta:</label>
                    <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($filtro_fecha_hasta) ?>">
                </div>
            </div>
            <div class="search-buttons">
                <input type="submit" value="Buscar" class="btn-search">
                <a href="crud_recetas.php" class="btn-clear">Limpiar Filtros</a>
            </div>
        </form>
    </div>

    <!-- Información de resultados -->
    <?php 
    $total_resultados = $result->num_rows;
    if (!empty($busqueda) || $filtro_cliente > 0 || !empty($filtro_fecha_desde) || !empty($filtro_fecha_hasta)): 
    ?>
    <div class="results-info">
        <strong>Resultados de búsqueda:</strong> Se encontraron <?= $total_resultados ?> receta(s)
        <?php if (!empty($busqueda)): ?> que coinciden con "<?= htmlspecialchars($busqueda) ?>"<?php endif; ?>
        <?php if ($filtro_cliente > 0): ?>
            <?php 
            $cli_nombre = $conn->query("SELECT nombre_cliente FROM Cliente WHERE id_cliente = $filtro_cliente")->fetch_assoc();
            ?> del cliente "<?= htmlspecialchars($cli_nombre['nombre_cliente']) ?>"
        <?php endif; ?>
        <?php if (!empty($filtro_fecha_desde) || !empty($filtro_fecha_hasta)): ?>
            emitidas 
            <?php if (!empty($filtro_fecha_desde)): ?>desde <?= $filtro_fecha_desde ?><?php endif; ?>
            <?php if (!empty($filtro_fecha_hasta)): ?>hasta <?= $filtro_fecha_hasta ?><?php endif; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Médico</th>
            <th>Diagnóstico</th>
            <th>Fecha de Emisión</th>
            <?php if (!$solo_lectura): ?><th>Acciones</th><?php endif; ?>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id_receta'] ?></td>
            <td><?= htmlspecialchars($row['nombre_cliente']) ?></td>
            <td><?= htmlspecialchars($row['nombre_medico']) ?></td>
            <td><?= htmlspecialchars($row['diagnostico']) ?></td>
            <td><?= $row['fecha_emision'] ?></td>
            <?php if (!$solo_lectura): ?>
            <td>
                <a href="?editar=<?= $row['id_receta'] ?>" class="btn btn-edit">Editar</a>
                <a href="crud_detalle_receta.php?receta_id=<?= $row['id_receta'] ?>" class="btn btn-detail">Detalles</a>
                <a href="?eliminar=<?= $row['id_receta'] ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro de eliminar esta receta?')">Eliminar</a>
            </td>
            <?php else: ?>
            <td>
                <a href="crud_detalle_receta.php?receta_id=<?= $row['id_receta'] ?>" class="btn btn-detail">Ver Detalles</a>
            </td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
    </table>
    
    <?php if (!$solo_lectura): ?>
    <h3><?= $edit ? 'Editar' : 'Agregar' ?> Receta Médica</h3>
    <form method="post">
        <?php if ($edit): ?><input type="hidden" name="id_receta" value="<?= $edit['id_receta'] ?>"><?php endif; ?>
        
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
                <label>Nombre del Médico:</label>
                <input type="text" name="nombre_medico" value="<?= htmlspecialchars($edit['nombre_medico'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Diagnóstico:</label>
                <textarea name="diagnostico" rows="3" required><?= htmlspecialchars($edit['diagnostico'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>Fecha de Emisión:</label>
                <input type="date" name="fecha_emision" value="<?= $edit['fecha_emision'] ?? date('Y-m-d') ?>" required>
            </div>
        </div>
        
        <input type="submit" value="<?= $edit ? 'Actualizar' : 'Guardar' ?>">
    </form>
    <?php endif; ?>
</div>
</body>
</html> 