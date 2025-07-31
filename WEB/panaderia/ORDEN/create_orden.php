<?php
include '../db.php';
include '../rol/acceso.php';

if (!tienePermiso(['admin', 'editor'])) {
    echo "Acceso denegado.";
    exit;
}

// Obtener clientes y productos
$clientes = $conn->query("SELECT cliente_id, nombre FROM Clientes");
$productos = $conn->query("SELECT producto_id, nombre_producto, precio FROM Productos");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = intval($_POST['cliente_id']);
    $fecha_orden = $_POST['fecha_orden'];
    $estado = $_POST['estado'];

    $producto_ids = $_POST['producto_id'] ?? [];
    $cantidades = $_POST['cantidad'] ?? [];
    $precios_unitarios = $_POST['precio_unitario'] ?? [];

    if (count($producto_ids) === 0) {
        echo "<div class='alert alert-warning'>Debe agregar al menos un producto.</div>";
    } else {
        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare("INSERT INTO Ordenes (cliente_id, fecha_orden, estado) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $cliente_id, $fecha_orden, $estado);
            $stmt->execute();
            $orden_id = $conn->insert_id;

            foreach ($producto_ids as $index => $producto_id) {
                $producto_id = intval($producto_id);
                $cantidad = floatval($cantidades[$index]);
                $precio_unitario = floatval($precios_unitarios[$index]);
                $total_linea = $cantidad * $precio_unitario;

                $stmt_detalle = $conn->prepare("INSERT INTO DetalleOrden (orden_id, producto_id, cantidad, precio_unitario, total_linea) VALUES (?, ?, ?, ?, ?)");
                $stmt_detalle->bind_param("iiidd", $orden_id, $producto_id, $cantidad, $precio_unitario, $total_linea);
                $stmt_detalle->execute();
            }

            $conn->commit();
            echo "<script>
                localStorage.setItem('mensaje', 'guardado');
                window.location.href = 'read_orden.php';
            </script>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<div class='alert alert-danger'>Error: {$e->getMessage()}</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Orden</title>
    <link rel="stylesheet" href="../CSS/tablas.css">
    <script>
        function agregarProducto() {
            const fila = document.querySelector('.producto-row').cloneNode(true);
            fila.querySelectorAll('input').forEach(input => input.value = '');
            document.getElementById('productos').appendChild(fila);
        }
    </script>
</head>
<body class="container mt-4">
    <h2>Nueva Orden</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Cliente</label>
            <select name="cliente_id" class="form-select" required>
                <?php while ($c = $clientes->fetch_assoc()): ?>
                    <option value="<?= $c['cliente_id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha</label>
            <input type="date" name="fecha_orden" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select" required>
                <option value="Pagado">Pagado</option>
                <option value="Entregado">Entregado</option>
                <option value="Pendiente">Pendiente</option>
            </select>
        </div>

        <h5>Productos</h5>
        <div id="productos">
            <div class="row producto-row mb-2">
                <div class="col-md-4">
                    <select name="producto_id[]" class="form-select" required>
                        <?php
                        $productos->data_seek(0);
                        while ($p = $productos->fetch_assoc()): ?>
                            <option value="<?= $p['producto_id'] ?>" data-precio="<?= $p['precio'] ?>">
                                <?= htmlspecialchars($p['nombre_producto']) ?> ($<?= number_format($p['precio'], 2) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="cantidad[]" class="form-control" placeholder="Cantidad" min="1" step="1" required>
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" name="precio_unitario[]" class="form-control" placeholder="Precio Unitario" min="0" required>
                </div>
            </div>
        </div>
        <button type="button" onclick="agregarProducto()" class="btn btn-secondary mb-3">Agregar Producto</button>
        <br>
        <button class="btn btn-success">Guardar Orden</button>
        <a href="read_orden.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
