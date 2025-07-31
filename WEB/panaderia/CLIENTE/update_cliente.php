<?php
include '../db.php';
include '../rol/acceso.php';

if (!tienePermiso(['admin', 'editor'])) {
    echo "Acceso denegado.";
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID de cliente no proporcionado.";
    exit;
}

// Obtener datos actuales del cliente
$stmt = $conn->prepare("SELECT nombre, correo, telefono, direccion FROM Clientes WHERE cliente_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($nombre, $correo, $telefono, $direccion);
$stmt->fetch();
$stmt->close();

// Procesar formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    $stmt = $conn->prepare("UPDATE Clientes SET nombre=?, correo=?, telefono=?, direccion=? WHERE cliente_id=?");
    $stmt->bind_param("ssssi", $nombre, $correo, $telefono, $direccion, $id);

    if ($stmt->execute()) {
        echo "<script>
            localStorage.setItem('mensaje', 'cliente_actualizado');
            window.location.href = 'read_cliente.php';
        </script>";
    } else {
        echo "<div class='alert alert-danger'>Error: {$stmt->error}</div>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cliente</title>
    <link rel="stylesheet" href="../CSS/tablas.css">
</head>
<body class="container mt-4">
    <h2>Editar Cliente</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nombre completo</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($nombre) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($correo) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($telefono) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Dirección</label>
            <textarea name="direccion" class="form-control" required><?= htmlspecialchars($direccion) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="read_cliente.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
