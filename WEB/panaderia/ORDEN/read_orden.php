<?php
include '../db.php';
include '../rol/acceso.php';

if (!tienePermiso(['admin', 'editor', 'lector'])) {
    echo "Acceso denegado.";
    exit;
}

// Obtener todas las órdenes con total calculado
$query = "
    SELECT o.orden_id, o.fecha_orden, c.nombre AS cliente, o.estado,
           COALESCE(SUM(d.total_linea), 0) AS total
    FROM Ordenes o
    JOIN Clientes c ON o.cliente_id = c.cliente_id
    LEFT JOIN DetalleOrden d ON o.orden_id = d.orden_id
    GROUP BY o.orden_id, o.fecha_orden, c.nombre, o.estado
    ORDER BY o.orden_id DESC
";

$ordenes = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Órdenes</title>
    <link rel="stylesheet" href="../CSS/tablas.css">
</head>
<body class="container mt-4">
    <h2>Listado de Órdenes</h2>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'eliminado'): ?>
        <div class="alert alert-success">Orden eliminada correctamente.</div>
    <?php endif; ?>

    <a href="create_orden.php" class="btn btn-success mb-3">+ Nueva Orden</a>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Total ($)</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($o = $ordenes->fetch_assoc()): ?>
                <tr>
                    <td><?= $o['orden_id'] ?></td>
                    <td><?= htmlspecialchars($o['cliente']) ?></td>
                    <td><?= $o['fecha_orden'] ?></td>
                    <td><?= $o['estado'] ?></td>
                    <td><?= number_format($o['total'], 2) ?></td>
                    <td>
                        <a href="update_orden.php?id=<?= $o['orden_id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                        <a href="delete_orden.php?id=<?= $o['orden_id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('¿Estás seguro de eliminar esta orden?')">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
