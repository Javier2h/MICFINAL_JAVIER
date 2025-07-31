<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';
$rol = $_SESSION['rol'];

if (!in_array($rol, ['Administrador', 'Desarrollador'])) {
    die("No tienes permisos para acceder.");
}

if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM Proveedor WHERE id_proveedor = $id");
    header("Location: crud_proveedores.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre_proveedor'];
    $ruc = $_POST['ruc'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo_electronico'];
    
    if (isset($_POST['id_proveedor'])) {
        $id = intval($_POST['id_proveedor']);
        $conn->query("UPDATE Proveedor SET nombre_proveedor='$nombre', ruc='$ruc', direccion='$direccion', telefono='$telefono', correo_electronico='$correo' WHERE id_proveedor=$id");
    } else {
        $conn->query("INSERT INTO Proveedor (nombre_proveedor, ruc, direccion, telefono, correo_electronico) VALUES ('$nombre', '$ruc', '$direccion', '$telefono', '$correo')");
    }
    header("Location: crud_proveedores.php");
    exit();
}

// Lógica de búsqueda
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Construir la consulta SQL con filtros
$sql = "SELECT * FROM Proveedor WHERE 1=1";

if (!empty($busqueda)) {
    $busqueda_escaped = $conn->real_escape_string($busqueda);
    $sql .= " AND (nombre_proveedor LIKE '%$busqueda_escaped%' 
              OR ruc LIKE '%$busqueda_escaped%' 
              OR direccion LIKE '%$busqueda_escaped%'
              OR telefono LIKE '%$busqueda_escaped%'
              OR correo_electronico LIKE '%$busqueda_escaped%')";
}

$sql .= " ORDER BY id_proveedor";

$result = $conn->query($sql);
$edit = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $res = $conn->query("SELECT * FROM Proveedor WHERE id_proveedor = $id");
    $edit = $res->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>CRUD Proveedores</title>
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
        input[type="text"], input[type="email"] { width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #ddd; border-radius: 3px; }
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
    <h2>Gestión de Proveedores</h2>
    <a href="index.php" class="btn btn-back">Volver al Menú Principal</a>
    
    <!-- Formulario de búsqueda -->
    <div class="search-form">
        <h3>Buscar Proveedores</h3>
        <form method="get" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Buscar por nombre, RUC, dirección, teléfono o correo:</label>
                    <input type="text" name="buscar" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Ingrese término de búsqueda...">
                </div>
            </div>
            <div class="search-buttons">
                <input type="submit" value="Buscar" class="btn-search">
                <a href="crud_proveedores.php" class="btn-clear">Limpiar Filtros</a>
            </div>
        </form>
    </div>

    <!-- Información de resultados -->
    <?php 
    $total_resultados = $result->num_rows;
    if (!empty($busqueda)): 
    ?>
    <div class="results-info">
        <strong>Resultados de búsqueda:</strong> Se encontraron <?= $total_resultados ?> proveedor(es) que coinciden con "<?= htmlspecialchars($busqueda) ?>"
    </div>
    <?php endif; ?>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre del Proveedor</th>
            <th>RUC</th>
            <th>Dirección</th>
            <th>Teléfono</th>
            <th>Correo Electrónico</th>
            <th>Acciones</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id_proveedor'] ?></td>
            <td><?= htmlspecialchars($row['nombre_proveedor']) ?></td>
            <td><?= htmlspecialchars($row['ruc']) ?></td>
            <td><?= htmlspecialchars($row['direccion']) ?></td>
            <td><?= htmlspecialchars($row['telefono']) ?></td>
            <td><?= htmlspecialchars($row['correo_electronico']) ?></td>
            <td>
                <a href="?editar=<?= $row['id_proveedor'] ?>" class="btn btn-edit">Editar</a>
                <a href="?eliminar=<?= $row['id_proveedor'] ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro de eliminar este proveedor?')">Eliminar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    
    <h3><?= $edit ? 'Editar' : 'Agregar' ?> Proveedor</h3>
    <form method="post">
        <?php if ($edit): ?><input type="hidden" name="id_proveedor" value="<?= $edit['id_proveedor'] ?>"><?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label>Nombre del Proveedor:</label>
                <input type="text" name="nombre_proveedor" value="<?= htmlspecialchars($edit['nombre_proveedor'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>RUC:</label>
                <input type="text" name="ruc" value="<?= htmlspecialchars($edit['ruc'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Dirección:</label>
                <input type="text" name="direccion" value="<?= htmlspecialchars($edit['direccion'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Teléfono:</label>
                <input type="text" name="telefono" value="<?= htmlspecialchars($edit['telefono'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label>Correo Electrónico:</label>
            <input type="email" name="correo_electronico" value="<?= htmlspecialchars($edit['correo_electronico'] ?? '') ?>" required>
        </div>
        
        <input type="submit" value="<?= $edit ? 'Actualizar' : 'Guardar' ?>">
    </form>
</div>
</body>
</html> 