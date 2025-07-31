<?php
include '../db.php';
include '../rol/acceso.php';

if (!tienePermiso(['admin', 'editor'])) {
    echo "Acceso denegado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_categoria = trim($_POST['nombre_categoria']);

    // Verificar si ya existe una categoría con ese nombre
    $verificar = $conn->prepare("SELECT * FROM CategoriasProducto WHERE nombre_categoria = ?");
    $verificar->bind_param("s", $nombre_categoria);
    $verificar->execute();
    $resultado = $verificar->get_result();
    $existe = $resultado->num_rows > 0;
    $verificar->close();

    if ($existe) {
        echo "<div class='alert alert-warning'>La categoría ya existe.</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO CategoriasProducto (nombre_categoria) VALUES (?)");
        $stmt->bind_param("s", $nombre_categoria);

        if ($stmt->execute()) {
            echo "<script>
                localStorage.setItem('mensaje', 'categoria_guardada');
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
    <title>Crear Categoría</title>
    <link rel="stylesheet" href="../CSS/tablas.css">
</head>
<body class="container mt-4">
    <h2>Nueva Categoría de Producto</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="nombre_categoria" class="form-label">Nombre de la Categoría</label>
            <input type="text" name="nombre_categoria" id="nombre_categoria" class="form-control" required placeholder="Ej: Panes, Pasteles, Bebidas...">
        </div>
        <button class="btn btn-success">Guardar</button>
        <a href="read_categoria.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
