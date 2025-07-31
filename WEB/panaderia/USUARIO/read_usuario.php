<?php
include '../db.php';
include '../rol/acceso.php';

if (!tienePermiso(['admin'])) {
    echo "Acceso denegado.";
    exit;
}

$busqueda = $_GET['busqueda'] ?? '';
$query = "SELECT usuario_id, nombre_usuario, rol, estado, creado_en FROM Usuarios WHERE nombre_usuario LIKE ?";
$stmt = $conn->prepare($query);
$like = "%$busqueda%";
$stmt->bind_param("s", $like);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Usuarios</title>
    <link rel="stylesheet" href="../CSS/tablas.css">
    <script>
        const mensaje = localStorage.getItem('mensaje');
        if (mensaje) {
            alert("Usuario " + mensaje + " correctamente.");
            localStorage.removeItem('mensaje');
        }
    </script>
</head>
<body class="container mt-4">
    <h2>Usuarios</h2>

    <form class="mb-3" method="GET">
        <input type="text" name="busqueda" placeholder="Buscar usuario" class="form-control" value="<?= htmlspecialchars($busqueda) ?>">
    </form>

    <a href="create_usuario.php" class="btn btn-success mb-3">Agregar Usuario</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Creado en</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($fila['nombre_usuario']) ?></td>
                    <td><?= $fila['rol'] ?></td>
                    <td><?= $fila['estado'] ? 'Activo' : 'Inactivo' ?></td>
                    <td><?= $fila['creado_en'] ?></td>
                    <td>
                        <a href="update_usuario.php?id=<?= $fila['usuario_id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                        <a href="delete_usuario.php?id=<?= $fila['usuario_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
