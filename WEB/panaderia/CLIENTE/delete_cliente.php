<?php
include '../db.php';
include '../rol/acceso.php';

if (!tienePermiso(['admin'])) {
    echo "Acceso denegado.";
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Si el cliente tiene órdenes asociadas, puedes primero eliminar esas órdenes o mostrar una advertencia.
    // O eliminar en cascada si está permitido por la base.

    // Eliminar el cliente
    $stmt = $conn->prepare("DELETE FROM Clientes WHERE cliente_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
            localStorage.setItem('mensaje', 'cliente_eliminado');
            window.location.href = 'read_cliente.php';
        </script>";
    } else {
        echo "<div class='alert alert-danger'>Error: {$stmt->error}</div>";
    }

    $stmt->close();
}
?>
