<?php
include '../db.php';
include '../rol/acceso.php';

if (!tienePermiso(['admin'])) {
    echo "Acceso denegado.";
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Antes de eliminar, puedes verificar si hay productos asociados (opcional)
    // $verifica = $conn->prepare("SELECT COUNT(*) FROM Productos WHERE categoria_id = ?");
    // $verifica->bind_param("i", $id);
    // $verifica->execute();
    // $verifica->bind_result($total);
    // $verifica->fetch();
    // $verifica->close();

    // if ($total > 0) {
    //     echo "<div class='alert alert-warning'>No se puede eliminar la categoría porque tiene productos asociados.</div>";
    //     exit;
    // }

    $stmt = $conn->prepare("DELETE FROM CategoriasProducto WHERE categoria_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
            localStorage.setItem('mensaje', 'categoria_eliminada');
            window.location.href = 'read_categoria.php';
        </script>";
    } else {
        echo "<div class='alert alert-danger'>Error: {$stmt->error}</div>";
    }

    $stmt->close();
}
?>
