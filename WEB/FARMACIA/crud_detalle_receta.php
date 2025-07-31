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

$receta_id = isset($_GET['receta_id']) ? intval($_GET['receta_id']) : 0;
if (!$receta_id) {
    header("Location: crud_recetas.php");
    exit();
}

if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM DetalleReceta WHERE id_detalle = $id");
    header("Location: crud_detalle_receta.php?receta_id=$receta_id");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_medicamento = $_POST['id_medicamento'];
    $dosis = $_POST['dosis'];
    $frecuencia = $_POST['frecuencia'];
    
    if (isset($_POST['id_detalle'])) {
        $id = intval($_POST['id_detalle']);
        $conn->query("UPDATE DetalleReceta SET id_medicamento=$id_medicamento, dosis='$dosis', frecuencia='$frecuencia' WHERE id_detalle=$id");
    } else {
        $conn->query("INSERT INTO DetalleReceta (id_receta, id_medicamento, dosis, frecuencia) VALUES ($receta_id, $id_medicamento, '$dosis', '$frecuencia')");
    }
    header("Location: crud_detalle_receta.php?receta_id=$receta_id");
    exit();
}

// Obtener información de la receta
$receta_info = $conn->query("
    SELECT r.*, c.nombre_cliente 
    FROM Receta r 
    LEFT JOIN Cliente c ON r.id_cliente = c.id_cliente 
    WHERE r.id_receta = $receta_id
")->fetch_assoc();

if (!$receta_info) {
    header("Location: crud_recetas.php");
    exit();
}

$result = $conn->query("
    SELECT dr.*, m.nombre_medicamento, m.presentacion 
    FROM DetalleReceta dr 
    LEFT JOIN Medicamento m ON dr.id_medicamento = m.id_medicamento 
    WHERE dr.id_receta = $receta_id 
    ORDER BY dr.id_detalle
");
$medicamentos = $conn->query("SELECT * FROM Medicamento ORDER BY nombre_medicamento");
$edit = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $res = $conn->query("SELECT * FROM DetalleReceta WHERE id_detalle = $id");
    $edit = $res->fetch_assoc();
}
$solo_lectura = ($rol == 'Supervisor');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Detalle de Receta</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f4; }
        .container { max-width: 1200px; margin: 0 auto; background-color: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
        .receta-info { background-color: #e7f3ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #4CAF50; color: white; }
        tr:hover { background-color: #f5f5f5; }
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 3px; margin: 2px; }
        .btn-edit { background-color: #2196F3; color: white; }
        .btn-delete { background-color: #f44336; color: white; }
        .btn-back { background-color: #666; color: white; }
        form { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        input[type="text"], select { width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #ddd; border-radius: 3px; }
        input[type="submit"] { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; }
        input[type="submit"]:hover { background-color: #45a049; }
        .form-row { display: flex; gap: 20px; }
        .form-group { flex: 1; }
    </style>
</head>
<body>
<div class="container">
    <h2>Detalle de Receta Médica</h2>
    <a href="crud_recetas.php" class="btn btn-back">Volver a Recetas</a>
    
    <div class="receta-info">
        <h3>Información de la Receta</h3>
        <p><strong>Cliente:</strong> <?= htmlspecialchars($receta_info['nombre_cliente']) ?></p>
        <p><strong>Médico:</strong> <?= htmlspecialchars($receta_info['nombre_medico']) ?></p>
        <p><strong>Diagnóstico:</strong> <?= htmlspecialchars($receta_info['diagnostico']) ?></p>
        <p><strong>Fecha de Emisión:</strong> <?= $receta_info['fecha_emision'] ?></p>
    </div>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Medicamento</th>
            <th>Presentación</th>
            <th>Dosis</th>
            <th>Frecuencia</th>
            <?php if (!$solo_lectura): ?><th>Acciones</th><?php endif; ?>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id_detalle'] ?></td>
            <td><?= htmlspecialchars($row['nombre_medicamento']) ?></td>
            <td><?= htmlspecialchars($row['presentacion']) ?></td>
            <td><?= htmlspecialchars($row['dosis']) ?></td>
            <td><?= htmlspecialchars($row['frecuencia']) ?></td>
            <?php if (!$solo_lectura): ?>
            <td>
                <a href="?receta_id=<?= $receta_id ?>&editar=<?= $row['id_detalle'] ?>" class="btn btn-edit">Editar</a>
                <a href="?receta_id=<?= $receta_id ?>&eliminar=<?= $row['id_detalle'] ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro de eliminar este detalle?')">Eliminar</a>
            </td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
    </table>
    
    <?php if (!$solo_lectura): ?>
    <h3><?= $edit ? 'Editar' : 'Agregar' ?> Detalle de Receta</h3>
    <form method="post">
        <?php if ($edit): ?><input type="hidden" name="id_detalle" value="<?= $edit['id_detalle'] ?>"><?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label>Medicamento:</label>
                <select name="id_medicamento" required>
                    <option value="">Seleccionar medicamento</option>
                    <?php while($med = $medicamentos->fetch_assoc()): ?>
                        <option value="<?= $med['id_medicamento'] ?>" <?= (isset($edit['id_medicamento']) && $edit['id_medicamento'] == $med['id_medicamento']) ? 'selected' : '' ?>><?= htmlspecialchars($med['nombre_medicamento']) ?> - <?= htmlspecialchars($med['presentacion']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Dosis:</label>
                <input type="text" name="dosis" value="<?= htmlspecialchars($edit['dosis'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label>Frecuencia:</label>
            <input type="text" name="frecuencia" value="<?= htmlspecialchars($edit['frecuencia'] ?? '') ?>" required>
        </div>
        
        <input type="submit" value="<?= $edit ? 'Actualizar' : 'Guardar' ?>">
    </form>
    <?php endif; ?>
</div>
</body>
</html> 