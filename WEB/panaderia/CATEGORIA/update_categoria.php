<?php
include '../db.php';
include '../rol/acceso.php';

if (!tienePermiso(['admin', 'editor'])) {
    echo "Acceso denegado.";
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID inválido.";
    exit;
}

// Obtener datos actuales
$stmt = $conn->prepare("SELECT nombre_categoria FROM CategoriasProducto WHERE categoria_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($nombre_categoria);
$stmt->fetch();
$stmt->close();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_nombre = trim($_POST['nombre_categoria']);

    // Verificar duplicado
    $verifica = $conn->prepare("SELECT categoria_id FROM CategoriasProducto WHERE nombre_categoria = ? AND categoria_id != ?");
    $verifica->bind_param("si", $nuevo_nombre, $id);
    $verifica->execute();
    $resultado = $verifica->get_result();
    $duplicado = $resultado->num_rows > 0;
    $verifica->close();

    if ($duplicado) {
        echo "<div class='alert alert-warning'>Ya existe una categoría con ese nombre.</div>";
    } else {
        $stmt = $conn->prepare("UPDATE CategoriasProducto SET nombre_categoria = ? WHERE categoria_id = ?");
        $stmt->bind_param("si", $nuevo_nombre, $id);

        if ($stmt->execute()) {
            echo "<script>
                localStorage.setItem('mensaje', 'categoria_actualizada');
                window.location.href = 'read_categoria.php';
            </script>";
        } else {
            echo "<div class='alert alert-danger'>Error: {$stmt->error}</div>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Categoría</title>
    <link rel="stylesheet" href="../CSS/tablas.css">
</head>
<body class="container mt-4">
    <h2>Editar Categoría de Producto</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="nombre_categoria" class="form-label">Nombre de la Categoría</label>
            <input type="text" name="nombre_categoria" id="nombre_categoria" class="form-control" value="<?= htmlspecialchars($nombre_categoria) ?>" required>
        </div>
        <button class="btn btn-primary">Actualizar</button>
        <a href="read_categoria.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
