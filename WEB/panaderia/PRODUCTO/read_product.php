<?php
include '../db.php';
include '../rol/acceso.php';

if (!tienePermiso(['admin', 'editor', 'lector'])) {
    echo "Acceso denegado.";
    exit;
}

$busqueda = $_GET['buscar'] ?? '';

// Consulta con búsqueda por nombre, descripción o categoría
$stmt = $conn->prepare("
    SELECT p.producto_id, p.nombre_producto, p.descripcion, p.precio, c.nombre_categoria
    FROM Productos p
    JOIN CategoriasProducto c ON p.categoria_id = c.categoria_id
    WHERE p.nombre_producto LIKE CONCAT('%', ?, '%') 
       OR p.descripcion LIKE CONCAT('%', ?, '%')
       OR c.nombre_categoria LIKE CONCAT('%', ?, '%')
    ORDER BY p.producto_id DESC
");
$stmt->bind_param("sss", $busqueda, $busqueda, $busqueda);
$stmt->execute();
$resultado = $stmt->get_result();

$mostrarAcciones = tienePermiso(['admin', 'editor']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Productos</title>
    <link rel="stylesheet" href="../CSS/tablas.css">
    <script>
        window.onload = function () {
            const mensaje = localStorage.getItem('mensaje');
            if (mensaje) {
                let texto = '';
                if (mensaje === 'producto_guardado') texto = '¡Producto guardado con éxito!';
                else if (mensaje === 'producto_actualizado') texto = '¡Producto actualizado con éxito!';
                else if (mensaje === 'producto_eliminado') texto = '¡Producto eliminado con éxito!';
                if (texto) {
                    const alerta = document.createElement('div');
                    alerta.className = 'alert alert-success alert-dismissible fade show';
                    alerta.role = 'alert';
                    alerta.innerHTML = texto + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>';
                    document.body.prepend(alerta);
                }
                localStorage.removeItem('mensaje');
            }
        }
    </script>
</head>
<body class="container mt-4">
    <h2>Productos</h2>

    <!-- Formulario de búsqueda -->
    <form class="row mb-3" method="GET">
        <div class="col-md-4">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre, descripción o categoría" value="<?= htmlspecialchars($busqueda) ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary" type="submit">Buscar</button>
            <a href="read_product.php" class="btn btn-secondary">Limpiar</a>
        </div>
    </form>

    <?php if ($mostrarAcciones): ?>
        <a href="create_product.php" class="btn btn-success mb-3">Agregar Producto</a>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Categoría</th>
                    <?php if ($mostrarAcciones): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= $fila['producto_id'] ?></td>
                        <td><?= htmlspecialchars($fila['nombre_producto']) ?></td>
                        <td><?= htmlspecialchars($fila['descripcion']) ?></td>
                        <td>$<?= number_format($fila['precio'], 2) ?></td>
                        <td><?= htmlspecialchars($fila['nombre_categoria']) ?></td>
                        <?php if ($mostrarAcciones): ?>
                            <td>
                                <a href="update_producto.php?id=<?= $fila['producto_id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                                <?php if (tienePermiso(['admin'])): ?>
                                    <a href="delete_producto.php?id=<?= $fila['producto_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este producto?')">Eliminar</a>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
                <?php if ($resultado->num_rows === 0): ?>
                    <tr><td colspan="<?= $mostrarAcciones ? 6 : 5 ?>" class="text-center">No hay productos registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
