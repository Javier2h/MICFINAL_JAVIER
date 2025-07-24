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
    $conn->query("DELETE FROM Usuario WHERE id_usuario = $id");
    header("Location: crud_usuarios.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_usuario = $_POST['nombre_usuario'];
    $contrasena = $_POST['contrasena_encriptada'];
    $rol_usuario = $_POST['rol'];
    $estado = $_POST['estado'];
    
    if (isset($_POST['id_usuario'])) {
        $id = intval($_POST['id_usuario']);
        $conn->query("UPDATE Usuario SET nombre_usuario='$nombre_usuario', contrasena_encriptada='$contrasena', rol='$rol_usuario', estado='$estado' WHERE id_usuario=$id");
    } else {
        $conn->query("INSERT INTO Usuario (nombre_usuario, contrasena_encriptada, rol, estado) VALUES ('$nombre_usuario', '$contrasena', '$rol_usuario', '$estado')");
    }
    header("Location: crud_usuarios.php");
    exit();
}

// Lógica de búsqueda
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$filtro_rol = isset($_GET['filtro_rol']) ? $_GET['filtro_rol'] : '';
$filtro_estado = isset($_GET['filtro_estado']) ? $_GET['filtro_estado'] : '';

// Construir la consulta SQL con filtros
$sql = "SELECT * FROM Usuario WHERE 1=1";

if (!empty($busqueda)) {
    $busqueda_escaped = $conn->real_escape_string($busqueda);
    $sql .= " AND nombre_usuario LIKE '%$busqueda_escaped%'";
}

if (!empty($filtro_rol)) {
    $sql .= " AND rol = '$filtro_rol'";
}

if (!empty($filtro_estado)) {
    $sql .= " AND estado = '$filtro_estado'";
}

$sql .= " ORDER BY id_usuario";

$result = $conn->query($sql);
$edit = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $res = $conn->query("SELECT * FROM Usuario WHERE id_usuario = $id");
    $edit = $res->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>CRUD Usuarios</title>
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
        input[type="text"], input[type="password"], select { width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #ddd; border-radius: 3px; }
        input[type="submit"] { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; }
        input[type="submit"]:hover { background-color: #45a049; }
        .form-row { display: flex; gap: 20px; }
        .form-group { flex: 1; }
        .estado-activo { color: green; font-weight: bold; }
        .estado-inactivo { color: red; }
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
    <h2>Gestión de Usuarios</h2>
    <a href="index.php" class="btn btn-back">Volver al Menú Principal</a>
    
    <!-- Formulario de búsqueda -->
    <div class="search-form">
        <h3>Buscar Usuarios</h3>
        <form method="get" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Buscar por nombre de usuario:</label>
                    <input type="text" name="buscar" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Ingrese nombre de usuario...">
                </div>
                <div class="form-group">
                    <label>Filtrar por Rol:</label>
                    <select name="filtro_rol">
                        <option value="">Todos los roles</option>
                        <option value="Administrador" <?= $filtro_rol == 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                        <option value="Desarrollador" <?= $filtro_rol == 'Desarrollador' ? 'selected' : '' ?>>Desarrollador</option>
                        <option value="Supervisor" <?= $filtro_rol == 'Supervisor' ? 'selected' : '' ?>>Supervisor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Filtrar por Estado:</label>
                    <select name="filtro_estado">
                        <option value="">Todos los estados</option>
                        <option value="activo" <?= $filtro_estado == 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= $filtro_estado == 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="search-buttons">
                <input type="submit" value="Buscar" class="btn-search">
                <a href="crud_usuarios.php" class="btn-clear">Limpiar Filtros</a>
            </div>
        </form>
    </div>

    <!-- Información de resultados -->
    <?php 
    $total_resultados = $result->num_rows;
    if (!empty($busqueda) || !empty($filtro_rol) || !empty($filtro_estado)): 
    ?>
    <div class="results-info">
        <strong>Resultados de búsqueda:</strong> Se encontraron <?= $total_resultados ?> usuario(s)
        <?php if (!empty($busqueda)): ?> que coinciden con "<?= htmlspecialchars($busqueda) ?>"<?php endif; ?>
        <?php if (!empty($filtro_rol)): ?> con rol "<?= htmlspecialchars($filtro_rol) ?>"<?php endif; ?>
        <?php if (!empty($filtro_estado)): ?> con estado "<?= htmlspecialchars($filtro_estado) ?>"<?php endif; ?>
    </div>
    <?php endif; ?>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre de Usuario</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id_usuario'] ?></td>
            <td><?= htmlspecialchars($row['nombre_usuario']) ?></td>
            <td><?= htmlspecialchars($row['rol']) ?></td>
            <td class="<?= $row['estado'] == 'activo' ? 'estado-activo' : 'estado-inactivo' ?>"><?= htmlspecialchars($row['estado']) ?></td>
            <td>
                <a href="?editar=<?= $row['id_usuario'] ?>" class="btn btn-edit">Editar</a>
                <a href="?eliminar=<?= $row['id_usuario'] ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    
    <h3><?= $edit ? 'Editar' : 'Agregar' ?> Usuario</h3>
    <form method="post">
        <?php if ($edit): ?><input type="hidden" name="id_usuario" value="<?= $edit['id_usuario'] ?>"><?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label>Nombre de Usuario:</label>
                <input type="text" name="nombre_usuario" value="<?= htmlspecialchars($edit['nombre_usuario'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Contraseña:</label>
                <input type="password" name="contrasena_encriptada" value="<?= htmlspecialchars($edit['contrasena_encriptada'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Rol:</label>
                <select name="rol" required>
                    <option value="">Seleccionar rol</option>
                    <option value="Administrador" <?= (isset($edit['rol']) && $edit['rol'] == 'Administrador') ? 'selected' : '' ?>>Administrador</option>
                    <option value="Desarrollador" <?= (isset($edit['rol']) && $edit['rol'] == 'Desarrollador') ? 'selected' : '' ?>>Desarrollador</option>
                    <option value="Supervisor" <?= (isset($edit['rol']) && $edit['rol'] == 'Supervisor') ? 'selected' : '' ?>>Supervisor</option>
                </select>
            </div>
            <div class="form-group">
                <label>Estado:</label>
                <select name="estado" required>
                    <option value="">Seleccionar estado</option>
                    <option value="activo" <?= (isset($edit['estado']) && $edit['estado'] == 'activo') ? 'selected' : '' ?>>Activo</option>
                    <option value="inactivo" <?= (isset($edit['estado']) && $edit['estado'] == 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
        </div>
        
        <input type="submit" value="<?= $edit ? 'Actualizar' : 'Guardar' ?>">
    </form>
</div>
</body>
</html> 