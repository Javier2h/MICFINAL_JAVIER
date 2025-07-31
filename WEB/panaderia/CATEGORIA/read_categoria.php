<?php
include '../db.php';
include '../rol/acceso.php';

if (!tienePermiso(['admin', 'editor', 'lector'])) {
    echo "Acceso denegado.";
    exit;
}

$categorias = $conn->query("SELECT categoria_id, nombre_categoria FROM CategoriasProducto ORDER BY nombre_categoria ASC");

$puedeAgregar = tienePermiso(['admin', 'editor']);
$puedeEditar  = tienePermiso(['admin', 'editor']);
$puedeEliminar = tienePermiso(['admin']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Categorías de Productos</title>
    <link rel="stylesheet" href="../CSS/tablas.css">
    <script>
        window.onload = () => {
            const mensaje = localStorage.getItem('mensaje');
            if (mensaje) {
                let texto = '';
                if (mensaje === 'categoria_guardada') texto = '¡Categoría guardada con éxito!';
                else if (mensaje === 'categoria_actualizada') texto = '¡Categoría actualizada con éxito!';
                else if (mensaje === 'categoria_eliminada') texto = '¡Categoría eliminada con éxito!';

                if (texto) {
                    const alerta = document.createElement('div');
                    alerta.className = 'alert alert-success alert-dismissible fade show';
                    alerta.role = 'alert';
                    alerta.innerHTML = texto + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>';
                    document.body.prepend(alerta);
                    localStorage.removeItem('mensaje');
                }
            }
        };
    </script>
</head>
<body class="container mt-4">
    <h2>Categorías de Productos</h2>

    <?php if ($puedeAgregar): ?>
        <a href="create_categoria.php" class="btn btn-success mb-3">Nueva Categoría</a>
    <?php endif; ?>

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre de la Categoría</th>
                <?php if ($puedeEditar || $puedeEliminar): ?>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($cat = $categorias->fetch_assoc()): ?>
                <tr>
                    <td><?= $cat['categoria_id'] ?></td>
                    <td><?= htmlspecialchars($cat['nombre_categoria']) ?></td>
                    <?php if ($puedeEditar || $puedeEliminar): ?>
                        <td>
                            <?php if ($puedeEditar): ?>
                                <a href="update_categoria.php?id=<?= $cat['categoria_id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                            <?php endif; ?>
                            <?php if ($puedeEliminar): ?>
                                <a href="delete_categoria.php?id=<?= $cat['categoria_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta categoría?')">Eliminar</a>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
            <?php if ($categorias->num_rows === 0): ?>
                <tr><td colspan="<?= ($puedeEditar || $puedeEliminar) ? 3 : 2 ?>" class="text-center">No hay categorías registradas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
