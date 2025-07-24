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
    $conn->query("DELETE FROM CategoriaMedicamento WHERE id_categoria = $id");
    header("Location: crud_categorias_medicamento.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre_categoria'];
    $descripcion = $_POST['descripcion'];
    
    if (isset($_POST['id_categoria'])) {
        $id = intval($_POST['id_categoria']);
        $conn->query("UPDATE CategoriaMedicamento SET nombre_categoria='$nombre', descripcion='$descripcion' WHERE id_categoria=$id");
    } else {
        $conn->query("INSERT INTO CategoriaMedicamento (nombre_categoria, descripcion) VALUES ('$nombre', '$descripcion')");
    }
    header("Location: crud_categorias_medicamento.php");
    exit();
}

// Lógica de búsqueda
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Construir la consulta SQL con filtros
$sql = "SELECT * FROM CategoriaMedicamento WHERE 1=1";

if (!empty($busqueda)) {
    $busqueda_escaped = $conn->real_escape_string($busqueda);
    $sql .= " AND (nombre_categoria LIKE '%$busqueda_escaped%' 
              OR descripcion LIKE '%$busqueda_escaped%')";
}

$sql .= " ORDER BY id_categoria";

$result = $conn->query($sql);
$edit = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $res = $conn->query("SELECT * FROM CategoriaMedicamento WHERE id_categoria = $id");
    $edit = $res->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>CRUD Categorías de Medicamentos</title>
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
        input[type="text"], textarea { width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #ddd; border-radius: 3px; }
        input[type="submit"] { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; }
        input[type="submit"]:hover { background-color: #45a049; }
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
    <h2>Gestión de Categorías de Medicamentos</h2>
    <a href="index.php" class="btn btn-back">Volver al Menú Principal</a>
    
    <!-- Formulario de búsqueda -->
    <div class="search-form">
        <h3>Buscar Categorías</h3>
        <form method="get" action="">
            <label>Buscar por nombre o descripción:</label>
            <input type="text" name="buscar" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Ingrese término de búsqueda...">
            <div class="search-buttons">
                <input type="submit" value="Buscar" class="btn-search">
                <a href="crud_categorias_medicamento.php" class="btn-clear">Limpiar Filtros</a>
            </div>
        </form>
    </div>

    <!-- Información de resultados -->
    <?php 
    $total_resultados = $result->num_rows;
    if (!empty($busqueda)): 
    ?>
    <div class="results-info">
        <strong>Resultados de búsqueda:</strong> Se encontraron <?= $total_resultados ?> categoría(s) que coinciden con "<?= htmlspecialchars($busqueda) ?>"
    </div>
    <?php endif; ?>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre de Categoría</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id_categoria'] ?></td>
            <td><?= htmlspecialchars($row['nombre_categoria']) ?></td>
            <td><?= htmlspecialchars($row['descripcion']) ?></td>
            <td>
                <a href="?editar=<?= $row['id_categoria'] ?>" class="btn btn-edit">Editar</a>
                <a href="?eliminar=<?= $row['id_categoria'] ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">Eliminar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    
    <h3><?= $edit ? 'Editar' : 'Agregar' ?> Categoría de Medicamento</h3>
    <form method="post">
        <?php if ($edit): ?><input type="hidden" name="id_categoria" value="<?= $edit['id_categoria'] ?>"><?php endif; ?>
        <label>Nombre de Categoría:</label>
        <input type="text" name="nombre_categoria" value="<?= htmlspecialchars($edit['nombre_categoria'] ?? '') ?>" required><br>
        <label>Descripción:</label>
        <textarea name="descripcion" rows="3" required><?= htmlspecialchars($edit['descripcion'] ?? '') ?></textarea><br>
        <input type="submit" value="<?= $edit ? 'Actualizar' : 'Guardar' ?>">
    </form>
</div>
</body>
</html> 