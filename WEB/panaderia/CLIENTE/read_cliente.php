<?php
include '../db.php';
include '../rol/acceso.php';

if (!tienePermiso(['admin', 'editor', 'lector'])) {
    echo "Acceso denegado.";
    exit;
}

$busqueda = $_GET['buscar'] ?? '';

$stmt = $conn->prepare("
    SELECT * FROM Clientes 
    WHERE nombre LIKE CONCAT('%', ?, '%') 
       OR correo LIKE CONCAT('%', ?, '%') 
       OR telefono LIKE CONCAT('%', ?, '%') 
    ORDER BY cliente_id DESC
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
    <title>Listado de Clientes</title>
    <link rel="stylesheet" href="../CSS/tablas.css">
    <script>
        window.onload = () => {
            const mensaje = localStorage.getItem('mensaje');
            if (mensaje) {
                let texto = '';
                if (mensaje === 'cliente_guardado') texto = '¡Cliente registrado con éxito!';
                else if (mensaje === 'cliente_actualizado') texto = '¡Cliente actualizado con éxito!';
                else if (mensaje === 'cliente_eliminado') texto = '¡Cliente eliminado con éxito!';
                const alerta = document.createElement('div');
                alerta.className = 'alert alert-success alert-dismissible fade show';
                alerta.role = 'alert';
                alerta.innerHTML = texto + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                document.body.prepend(alerta);
                localStorage.removeItem('mensaje');
            }
        };
    </script>
</head>
<body class="container mt-4">
    <h2>Clientes</h2>

    <!-- Buscador -->
    <form class="row mb-3" method="GET">
        <div class="col-md-4">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre, correo o teléfono" value="<?= htmlspecialchars($busqueda) ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary">Buscar</button>
            <a href="read_cliente.php" class="btn btn-secondary">Limpiar</a>
        </div>
    </form>

    <?php if ($mostrarAcciones): ?>
        <a href="create_cliente.php" class="btn btn-success mb-3">Agregar Cliente</a>
    <?php endif; ?>

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <?php if ($mostrarAcciones): ?>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= $fila['cliente_id'] ?></td>
                    <td><?= htmlspecialchars($fila['nombre']) ?></td>
                    <td><?= htmlspecialchars($fila['correo']) ?></td>
                    <td><?= htmlspecialchars($fila['telefono']) ?></td>
                    <td><?= htmlspecialchars($fila['direccion']) ?></td>
                    <?php if ($mostrarAcciones): ?>
                        <td>
                            <?php if (tienePermiso(['admin', 'editor'])): ?>
                                <a href="update_cliente.php?id=<?= $fila['cliente_id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                            <?php endif; ?>
                            <?php if (tienePermiso(['admin'])): ?>
                                <a href="delete_cliente.php?id=<?= $fila['cliente_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este cliente?')">Eliminar</a>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
            <?php if ($resultado->num_rows === 0): ?>
                <tr><td colspan="<?= $mostrarAcciones ? 6 : 5 ?>" class="text-center">No hay clientes registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
