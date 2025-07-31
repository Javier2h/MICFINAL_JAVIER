<?php
include '../db.php';
include '../rol/acceso.php';

if (!tienePermiso(['admin'])) {
    echo "Acceso denegado.";
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $contrasena = $_POST['contrasena'];
    $rol = $_POST['rol'];

    // Validar campos
    if (empty($nombre_usuario) || empty($contrasena)) {
        $error = 'Todos los campos son obligatorios.';
    } else {
        // Verificar si el usuario ya existe
        $verificar = $conn->prepare("SELECT usuario_id FROM Usuarios WHERE nombre_usuario = ?");
        $verificar->bind_param("s", $nombre_usuario);
        $verificar->execute();
        $verificar->store_result();

        if ($verificar->num_rows > 0) {
            $error = 'El nombre de usuario ya existe.';
        } else {
            // Hash de contraseña
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);

            // Insertar nuevo usuario
            $stmt = $conn->prepare("INSERT INTO Usuarios (nombre_usuario, contrasena, rol) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nombre_usuario, $hash, $rol);

            if ($stmt->execute()) {
                echo "<script>
                    localStorage.setItem('mensaje', 'guardado');
                    window.location.href = 'read_usuarios.php';
                </script>";
                exit;
            } else {
                $error = "Error al guardar: {$stmt->error}";
            }
        }

        $verificar->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="../CSS/tablas.css">
</head>
<body class="container mt-4">
    <h2>Crear Nuevo Usuario</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nombre de Usuario</label>
            <input type="text" name="nombre_usuario" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="contrasena" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="rol" class="form-select" required>
                <option value="admin">Administrador</option>
                <option value="editor">Editor</option>
                <option value="lector">Lector</option>
            </select>
        </div>
        <button class="btn btn-success">Guardar</button>
        <a href="read_usuarios.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
