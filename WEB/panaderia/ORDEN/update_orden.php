<?php
include '../db.php';
include '../rol/acceso.php';

if (!tienePermiso(['admin', 'editor'])) {
    echo "Acceso denegado.";
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID de orden no proporcionado.";
    exit;
}

// Obtener datos actuales de la orden
$stmt = $conn->prepare("SELECT cliente_id, fecha_orden, estado FROM Ordenes WHERE orden_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($cliente_id, $fecha_orden, $estado);
$stmt->fetch();
$stmt->close();

// Obtener detalles actuales
$detalles = $conn->query("SELECT * FROM DetalleOrden WHERE orden_id = $id");

// Obtener clientes y productos
$clientes = $conn->query("SELECT cliente_id, nombre FROM Clientes");
$productos = $conn->query("SELECT producto_id, nombre_producto, precio FROM Productos");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $fecha = $_POST['fecha_orden'];
    $estado = $_POST['estado'];
    $productosSeleccionados = $_POST['producto_id'];
    $cantidades = $_POST['cantidad'];
    $precios = $_POST['precio_unitario'];

    // Actualizar la orden
    $stmt = $conn->prepare("UPDATE Ordenes SET cliente_id=?, fecha_orden=?, estado=? WHERE orden_id=?");
    $stmt->bind_param("issi", $cliente_id, $fecha, $estado, $id);
    $stmt->execute();

    // Eliminar detalles anteriores
    $conn->query("DELETE FROM DetalleOrden WHERE orden_id = $id");

    // Insertar nuevos detalles
    for ($i = 0; $i < count($productosSeleccionados); $i++) {
        $producto_id = $productosSeleccionados[$i];
        $cantidad = $cantidades[$i];
        $precio_unitario = $precios[$i];
        $total_linea = $cantidad * $precio_unitario;

        $stmt = $conn->prepare("INSERT INTO DetalleOrden (orden_id, producto_id, cantidad, precio_unitario, total_linea) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiidd", $id, $producto_id, $cantidad, $precio_unitario, $total_linea);
        $stmt->execute();
    }

    echo "<script>
        localStorage.setItem('mensaje', 'actualizado');
        window.location.href = 'read_orden.php';
    </script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Orden</title>
    <link rel="stylesheet" href="../CSS/tablas.css">
</head>
<body class="container mt-4">
    <h2>Editar Orden</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Cliente</label>
            <select name="cliente_id" class="form-select" required>
                <?php while ($c = $clientes->fetch_assoc()): ?>
                    <option value="<?= $c['cliente_id'] ?>" <?= $c['cliente_id'] == $cliente_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nombre']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Fecha</label>
            <input type="date" name="fecha_orden" class="form-control" value="<?= $fecha_orden ?>" required>
        </div>
        <div class="mb-3">
            <label>Estado</label>
            <select name="estado" class="form-select" required>
                <option value="Pendiente" <?= $estado == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                <option value="Pagado" <?= $estado == 'Pagado' ? 'selected' : '' ?>>Pagado</option>
                <option value="Entregado" <?= $estado == 'Entregado' ? 'selected' : '' ?>>Entregado</option>
            </select>
        </div>

        <h5>Productos</h5>
        <div id="productos-container">
            <?php while ($d = $detalles->fetch_assoc()): ?>
                <div class="row mb-2">
                    <div class="col">
                        <select name="producto_id[]" class="form-select" required>
                            <?php
                            $productos->data_seek(0);
                            while ($p = $productos->fetch_assoc()):
                            ?>
                                <option value="<?= $p['producto_id'] ?>" <?= $p['producto_id'] == $d['producto_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['nombre_producto']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col">
                        <input type="number" name="cantidad[]" class="form-control" value="<?= $d['cantidad'] ?>" required>
                    </div>
                    <div class="col">
                        <input type="number" name="precio_unitario[]" step="0.01" class="form-control" value="<?= $d['precio_unitario'] ?>" required>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="read_orden.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
