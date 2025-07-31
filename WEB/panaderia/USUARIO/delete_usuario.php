<?php
include '../db.php';
include '../rol/acceso.php';

if (!tienePermiso(['admin'])) {
    echo "Acceso denegado.";
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM Usuarios WHERE usuario_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
            localStorage.setItem('mensaje', 'eliminado');
            window.location.href = 'read_usuarios.php';
        </script>";
    } else {
        echo "<div class='alert alert-danger'>Error: {$stmt->error}</div>";
    }

    $stmt->close();
}
?>
