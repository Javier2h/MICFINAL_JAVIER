<?php
session_start();

if (isset($_POST['rol'])) {
    $_SESSION['rol'] = $_POST['rol'];
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Rol - Panadería KL</title>
    <link rel="stylesheet" href="CSS/inicio.css">
</head>
<body>

    <div class="role-container">
        <div class="icon-container">
            🍞
        </div>
        <h2 class="role-title">Selecciona tu Rol</h2>
        <p class="role-subtitle">Elige cómo deseas ingresar al sistema</p>

        <form method="POST" class="role-form">
            <div class="form-group">
                <label for="rol" class="form-label">Rol de usuario:</label>
                <select name="rol" id="rol" required>
                    <option value="">Seleccione...</option>
                    <option value="admin">Administrador de la panadería</option>
                    <option value="editor">Desarrollador del sistema</option>
                    <option value="lector">Supervisor de operaciones</option>
                </select>
            </div>
            <button type="submit" class="btn-start">Entrar</button>
        </form>
    </div>

</body>
</html>
