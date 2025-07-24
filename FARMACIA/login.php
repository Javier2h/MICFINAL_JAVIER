<?php
session_start();
include 'config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM Usuario WHERE nombre_usuario = ? AND estado = 'activo'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        if ($password == $row['contrasena_encriptada']) { // Usa password_verify si usas hash
            $_SESSION['usuario'] = $row['nombre_usuario'];
            $_SESSION['rol'] = $row['rol'];
            $_SESSION['id_usuario'] = $row['id_usuario'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Contraseña incorrecta";
        }
    } else {
        $error = "Usuario no encontrado o inactivo";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Sistema de Farmacia</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .login-container { max-width: 400px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 3px; }
        input[type="submit"] { width: 100%; padding: 10px; background-color: #4CAF50; color: white; border: none; border-radius: 3px; cursor: pointer; }
        input[type="submit"]:hover { background-color: #45a049; }
        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>
<div class="login-container">
    <h2>Login - Sistema de Farmacia</h2>
    <form method="post">
        Usuario: <input type="text" name="username" required><br>
        Contraseña: <input type="password" name="password" required><br>
        <input type="submit" value="Ingresar">
    </form>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
</div>
</body>
</html> 