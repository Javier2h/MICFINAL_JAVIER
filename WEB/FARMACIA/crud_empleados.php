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
    $conn->query("DELETE FROM Empleado WHERE id_empleado = $id");
    header("Location: crud_empleados.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre_empleado'];
    $cargo = $_POST['cargo'];
    $turno = $_POST['turno'];
    $fecha_contrato = $_POST['fecha_contrato'];
    
    if (isset($_POST['id_empleado'])) {
        $id = intval($_POST['id_empleado']);
        $conn->query("UPDATE Empleado SET nombre_empleado='$nombre', cargo='$cargo', turno='$turno', fecha_contrato='$fecha_contrato' WHERE id_empleado=$id");
    } else {
        $conn->query("INSERT INTO Empleado (nombre_empleado, cargo, turno, fecha_contrato) VALUES ('$nombre', '$cargo', '$turno', '$fecha_contrato')");
    }
    header("Location: crud_empleados.php");
    exit();
}

// Lógica de búsqueda
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$filtro_cargo = isset($_GET['filtro_cargo']) ? $_GET['filtro_cargo'] : '';
$filtro_turno = isset($_GET['filtro_turno']) ? $_GET['filtro_turno'] : '';
$filtro_fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '';
$filtro_fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '';

// Construir la consulta SQL con filtros
$sql = "SELECT * FROM Empleado WHERE 1=1";

if (!empty($busqueda)) {
    $busqueda_escaped = $conn->real_escape_string($busqueda);
    $sql .= " AND nombre_empleado LIKE '%$busqueda_escaped%'";
}

if (!empty($filtro_cargo)) {
    $sql .= " AND cargo = '$filtro_cargo'";
}

if (!empty($filtro_turno)) {
    $sql .= " AND turno = '$filtro_turno'";
}

if (!empty($filtro_fecha_desde)) {
    $sql .= " AND fecha_contrato >= '$filtro_fecha_desde'";
}

if (!empty($filtro_fecha_hasta)) {
    $sql .= " AND fecha_contrato <= '$filtro_fecha_hasta'";
}

$sql .= " ORDER BY id_empleado";

$result = $conn->query($sql);
$edit = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $res = $conn->query("SELECT * FROM Empleado WHERE id_empleado = $id");
    $edit = $res->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>CRUD Empleados</title>
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
        input[type="text"], input[type="date"], select { width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #ddd; border-radius: 3px; }
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
    <h2>Gestión de Empleados</h2>
    <a href="index.php" class="btn btn-back">Volver al Menú Principal</a>
    
    <!-- Formulario de búsqueda -->
    <div class="search-form">
        <h3>Buscar Empleados</h3>
        <form method="get" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>Buscar por nombre:</label>
                    <input type="text" name="buscar" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Ingrese nombre del empleado...">
                </div>
                <div class="form-group">
                    <label>Filtrar por Cargo:</label>
                    <select name="filtro_cargo">
                        <option value="">Todos los cargos</option>
                        <option value="Farmacéutico" <?= $filtro_cargo == 'Farmacéutico' ? 'selected' : '' ?>>Farmacéutico</option>
                        <option value="Cajero" <?= $filtro_cargo == 'Cajero' ? 'selected' : '' ?>>Cajero</option>
                        <option value="Administrador" <?= $filtro_cargo == 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                        <option value="Auxiliar" <?= $filtro_cargo == 'Auxiliar' ? 'selected' : '' ?>>Auxiliar</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Filtrar por Turno:</label>
                    <select name="filtro_turno">
                        <option value="">Todos los turnos</option>
                        <option value="Mañana" <?= $filtro_turno == 'Mañana' ? 'selected' : '' ?>>Mañana</option>
                        <option value="Tarde" <?= $filtro_turno == 'Tarde' ? 'selected' : '' ?>>Tarde</option>
                        <option value="Noche" <?= $filtro_turno == 'Noche' ? 'selected' : '' ?>>Noche</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Fecha de contrato desde:</label>
                    <input type="date" name="fecha_desde" value="<?= htmlspecialchars($filtro_fecha_desde) ?>">
                </div>
                <div class="form-group">
                    <label>Fecha de contrato hasta:</label>
                    <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($filtro_fecha_hasta) ?>">
                </div>
            </div>
            <div class="search-buttons">
                <input type="submit" value="Buscar" class="btn-search">
                <a href="crud_empleados.php" class="btn-clear">Limpiar Filtros</a>
            </div>
        </form>
    </div>

    <!-- Información de resultados -->
    <?php 
    $total_resultados = $result->num_rows;
    if (!empty($busqueda) || !empty($filtro_cargo) || !empty($filtro_turno) || !empty($filtro_fecha_desde) || !empty($filtro_fecha_hasta)): 
    ?>
    <div class="results-info">
        <strong>Resultados de búsqueda:</strong> Se encontraron <?= $total_resultados ?> empleado(s)
        <?php if (!empty($busqueda)): ?> que coinciden con "<?= htmlspecialchars($busqueda) ?>"<?php endif; ?>
        <?php if (!empty($filtro_cargo)): ?> con cargo "<?= htmlspecialchars($filtro_cargo) ?>"<?php endif; ?>
        <?php if (!empty($filtro_turno)): ?> en turno "<?= htmlspecialchars($filtro_turno) ?>"<?php endif; ?>
        <?php if (!empty($filtro_fecha_desde) || !empty($filtro_fecha_hasta)): ?>
            contratados 
            <?php if (!empty($filtro_fecha_desde)): ?>desde <?= $filtro_fecha_desde ?><?php endif; ?>
            <?php if (!empty($filtro_fecha_hasta)): ?>hasta <?= $filtro_fecha_hasta ?><?php endif; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre del Empleado</th>
            <th>Cargo</th>
            <th>Turno</th>
            <th>Fecha de Contrato</th>
            <th>Acciones</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id_empleado'] ?></td>
            <td><?= htmlspecialchars($row['nombre_empleado']) ?></td>
            <td><?= htmlspecialchars($row['cargo']) ?></td>
            <td><?= htmlspecialchars($row['turno']) ?></td>
            <td><?= $row['fecha_contrato'] ?></td>
            <td>
                <a href="?editar=<?= $row['id_empleado'] ?>" class="btn btn-edit">Editar</a>
                <a href="?eliminar=<?= $row['id_empleado'] ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro de eliminar este empleado?')">Eliminar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    
    <h3><?= $edit ? 'Editar' : 'Agregar' ?> Empleado</h3>
    <form method="post">
        <?php if ($edit): ?><input type="hidden" name="id_empleado" value="<?= $edit['id_empleado'] ?>"><?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label>Nombre del Empleado:</label>
                <input type="text" name="nombre_empleado" value="<?= htmlspecialchars($edit['nombre_empleado'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Cargo:</label>
                <select name="cargo" required>
                    <option value="">Seleccionar cargo</option>
                    <option value="Farmacéutico" <?= (isset($edit['cargo']) && $edit['cargo'] == 'Farmacéutico') ? 'selected' : '' ?>>Farmacéutico</option>
                    <option value="Cajero" <?= (isset($edit['cargo']) && $edit['cargo'] == 'Cajero') ? 'selected' : '' ?>>Cajero</option>
                    <option value="Administrador" <?= (isset($edit['cargo']) && $edit['cargo'] == 'Administrador') ? 'selected' : '' ?>>Administrador</option>
                    <option value="Auxiliar" <?= (isset($edit['cargo']) && $edit['cargo'] == 'Auxiliar') ? 'selected' : '' ?>>Auxiliar</option>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Turno:</label>
                <select name="turno" required>
                    <option value="">Seleccionar turno</option>
                    <option value="Mañana" <?= (isset($edit['turno']) && $edit['turno'] == 'Mañana') ? 'selected' : '' ?>>Mañana</option>
                    <option value="Tarde" <?= (isset($edit['turno']) && $edit['turno'] == 'Tarde') ? 'selected' : '' ?>>Tarde</option>
                    <option value="Noche" <?= (isset($edit['turno']) && $edit['turno'] == 'Noche') ? 'selected' : '' ?>>Noche</option>
                </select>
            </div>
            <div class="form-group">
                <label>Fecha de Contrato:</label>
                <input type="date" name="fecha_contrato" value="<?= $edit['fecha_contrato'] ?? '' ?>" required>
            </div>
        </div>
        
        <input type="submit" value="<?= $edit ? 'Actualizar' : 'Guardar' ?>">
    </form>
</div>
</body>
</html> 