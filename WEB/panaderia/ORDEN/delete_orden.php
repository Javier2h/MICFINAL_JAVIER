<?php
include '../db.php';
include '../rol/acceso.php';

if (!tienePermiso(['admin'])) {
    echo "Acceso denegado.";
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Asegura que sea número entero

    // Verificar si la orden existe antes de eliminar
    $check = $conn->prepare("SELECT orden_id FROM Ordenes WHERE orden_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
        // Eliminar detalles de la orden
        $stmt_detalle = $conn->prepare("DELETE FROM DetalleOrden WHERE orden_id = ?");
        $stmt_detalle->bind_param("i", $id);
        $stmt_detalle->execute();

        // Eliminar la orden principal
        $stmt = $conn->prepare("DELETE FROM Ordenes WHERE orden_id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<script>
                localStorage.setItem('mensaje', 'eliminado');
                window.location.href = 'read_orden.php';
            </script>";
        } else {
            echo "<div class='alert alert-danger'>Error al eliminar orden: {$stmt->error}</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>La orden no existe.</div>";
    }
} else {
    echo "<div class='alert alert-warning'>ID de orden no especificado.</div>";
}
?>
