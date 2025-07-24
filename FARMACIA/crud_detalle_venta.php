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

$venta_id = isset($_GET['venta_id']) ? intval($_GET['venta_id']) : 0;
if (!$venta_id) {
    header("Location: crud_ventas.php");
    exit();
}

if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM DetalleVenta WHERE id_detalle = $id");
    header("Location: crud_detalle_venta.php?venta_id=$venta_id");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_medicamento = $_POST['id_medicamento'];
    $cantidad = $_POST['cantidad'];
    $subtotal = $_POST['subtotal'];
    
    if (isset($_POST['id_detalle'])) {
        $id = intval($_POST['id_detalle']);
        $conn->query("UPDATE DetalleVenta SET id_medicamento=$id_medicamento, cantidad=$cantidad, subtotal=$subtotal WHERE id_detalle=$id");
    } else {
        $conn->query("INSERT INTO DetalleVenta (id_venta, id_medicamento, cantidad, subtotal) VALUES ($venta_id, $id_medicamento, $cantidad, $subtotal)");
    }
    header("Location: crud_detalle_venta.php?venta_id=$venta_id");
    exit();
}

// Obtener información de la venta
$venta_info = $conn->query("
    SELECT v.*, c.nombre_cliente, e.nombre_empleado 
    FROM Venta v 
    LEFT JOIN Cliente c ON v.id_cliente = c.id_cliente 
    LEFT JOIN Empleado e ON v.id_empleado = e.id_empleado 
    WHERE v.id_venta = $venta_id
")->fetch_assoc();

if (!$venta_info) {
    header("Location: crud_ventas.php");
    exit();
}

$result = $conn->query("
    SELECT dv.*, m.nombre_medicamento, m.presentacion, m.precio_unitario 
    FROM DetalleVenta dv 
    LEFT JOIN Medicamento m ON dv.id_medicamento = m.id_medicamento 
    WHERE dv.id_venta = $venta_id 
    ORDER BY dv.id_detalle
");
$medicamentos = $conn->query("SELECT * FROM Medicamento ORDER BY nombre_medicamento");
$edit = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $res = $conn->query("SELECT * FROM DetalleVenta WHERE id_detalle = $id");
    $edit = $res->fetch_assoc();
}
$solo_lectura = ($rol == 'Supervisor');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Detalle de Venta</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f4; }
        .container { max-width: 1200px; margin: 0 auto; background-color: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
        .venta-info { background-color: #e7f3ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #4CAF50; color: white; }
        tr:hover { background-color: #f5f5f5; }
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 3px; margin: 2px; }
        .btn-edit { background-color: #2196F3; color: white; }
        .btn-delete { background-color: #f44336; color: white; }
        .btn-back { background-color: #666; color: white; }
        form { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        input[type="text"], input[type="number"], select { width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #ddd; border-radius: 3px; }
        input[type="submit"] { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; }
        input[type="submit"]:hover { background-color: #45a049; }
        .form-row { display: flex; gap: 20px; }
        .form-group { flex: 1; }
        .subtotal { font-weight: bold; color: #4CAF50; }
    </style>
</head>
<body>
<div class="container">
    <h2>Detalle de Venta</h2>
    <a href="crud_ventas.php" class="btn btn-back">Volver a Ventas</a>
    
    <div class="venta-info">
        <h3>Información de la Venta</h3>
        <p><strong>Cliente:</strong> <?= htmlspecialchars($venta_info['nombre_cliente']) ?></p>
        <p><strong>Empleado:</strong> <?= htmlspecialchars($venta_info['nombre_empleado']) ?></p>
        <p><strong>Fecha de Venta:</strong> <?= $venta_info['fecha_venta'] ?></p>
        <p><strong>Total:</strong> $<?= number_format($venta_info['total'], 2) ?></p>
    </div>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Medicamento</th>
            <th>Presentación</th>
            <th>Precio Unitario</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
            <?php if (!$solo_lectura): ?><th>Acciones</th><?php endif; ?>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id_detalle'] ?></td>
            <td><?= htmlspecialchars($row['nombre_medicamento']) ?></td>
            <td><?= htmlspecialchars($row['presentacion']) ?></td>
            <td>$<?= number_format($row['precio_unitario'], 2) ?></td>
            <td><?= $row['cantidad'] ?></td>
            <td class="subtotal">$<?= number_format($row['subtotal'], 2) ?></td>
            <?php if (!$solo_lectura): ?>
            <td>
                <a href="?venta_id=<?= $venta_id ?>&editar=<?= $row['id_detalle'] ?>" class="btn btn-edit">Editar</a>
                <a href="?venta_id=<?= $venta_id ?>&eliminar=<?= $row['id_detalle'] ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro de eliminar este detalle?')">Eliminar</a>
            </td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
    </table>
    
    <?php if (!$solo_lectura): ?>
    <h3><?= $edit ? 'Editar' : 'Agregar' ?> Detalle de Venta</h3>
    <form method="post">
        <?php if ($edit): ?><input type="hidden" name="id_detalle" value="<?= $edit['id_detalle'] ?>"><?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label>Medicamento:</label>
                <select name="id_medicamento" required>
                    <option value="">Seleccionar medicamento</option>
                    <?php while($med = $medicamentos->fetch_assoc()): ?>
                        <option value="<?= $med['id_medicamento'] ?>" <?= (isset($edit['id_medicamento']) && $edit['id_medicamento'] == $med['id_medicamento']) ? 'selected' : '' ?>><?= htmlspecialchars($med['nombre_medicamento']) ?> - <?= htmlspecialchars($med['presentacion']) ?> ($<?= number_format($med['precio_unitario'], 2) ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Cantidad:</label>
                <input type="number" name="cantidad" value="<?= $edit['cantidad'] ?? '' ?>" min="1" required>
            </div>
        </div>
        
        <div class="form-group">
            <label>Subtotal:</label>
            <input type="number" step="0.01" name="subtotal" value="<?= $edit['subtotal'] ?? '' ?>" min="0" required>
        </div>
        
        <input type="submit" value="<?= $edit ? 'Actualizar' : 'Guardar' ?>">
    </form>
    <?php endif; ?>
</div>
</body>
</html> 