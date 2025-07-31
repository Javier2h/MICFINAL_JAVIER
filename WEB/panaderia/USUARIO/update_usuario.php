<?php
include '../db.php';
include '../rol/acceso.php';

if (!tienePermiso(['admin'])) {
    echo "Acceso denegado.";
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID inválido.";
    exit;
}

// Obtener datos del usuario
$stmt = $conn->prepare("SELECT nombre_usuario, rol FROM Usuarios WHERE usuario_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($nombre_usuario, $rol);
$stmt->fetch();
$stmt->close();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_usuario = trim($_POST['nombre_usuario']);
    $nueva_contrasena = $_POST['contrasena'];
    $nuevo_rol = $_POST['rol'];

    // Verificar si se quiere cambiar la contraseña
    if (!empty($nueva_contrasena)) {
        $hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE Usuarios SET nombre_usuario = ?, contrasena = ?, rol = ? WHERE usuario_id = ?");
        $stmt->bind_param("sssi", $nuevo_usuario, $hash, $nuevo_rol, $id);
    } else {
        $stmt = $conn->prepare("UPDATE Usuarios SET nombre_usuario = ?, rol = ? WHERE usuario_id = ?");
        $stmt->bind_param("ssi", $nuevo_usuario, $nuevo_rol, $id);
    }

    if ($stmt->execute()) {
        echo "<script>
            localStorage.setItem('mensaje', 'actualizado');
            window.location.href = 'read_usuarios.php';
        </script>";
        exit;
    } else {
        $error = "Error: {$stmt->error}";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../CSS/tablas.css">
</head>
<body class="container mt-4">
    <h2>Editar Usuario</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nombre de Usuario</label>
            <input type="text" name="nombre_usuario" class="form-control" value="<?= htmlspecialchars($nombre_usuario) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nueva Contraseña (opcional)</label>
            <input type="password" name="contrasena" class="form-control" placeholder="Dejar en blanco para no cambiar">
        </div>
        <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="rol" class="form-select" required>
                <option value="admin" <?= $rol === 'admin' ? 'selected' : '' ?>>Administrador</option>
                <option value="editor" <?= $rol === 'editor' ? 'selected' : '' ?>>Editor</option>
                <option value="lector" <?= $rol === 'lector' ? 'selected' : '' ?>>Lector</option>
            </select>
        </div>
        <button class="btn btn-primary">Actualizar</button>
        <a href="read_usuarios.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
