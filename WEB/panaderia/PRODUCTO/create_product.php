<?php
include '../db.php';
include '../rol/acceso.php';

if (!tienePermiso(['admin', 'editor'])) {
    echo "Acceso denegado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_producto'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $categoria_id = $_POST['categoria_id'];

    $stmt = $conn->prepare("INSERT INTO Productos (nombre_producto, descripcion, precio, categoria_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $categoria_id);

    if ($stmt->execute()) {
        echo "<script>
            localStorage.setItem('mensaje', 'producto_guardado');
            window.location.href = 'read_producto.php';
        </script>";
    } else {
        echo "<div class='alert alert-danger'>Error: {$stmt->error}</div>";
    }
    $stmt->close();
}

// Obtener categorías
$categorias = $conn->query("SELECT categoria_id, nombre_categoria FROM CategoriasProducto");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="../CSS/tablas.css">
</head>
<body class="container mt-4">
    <h2>Nuevo Producto</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="nombre_producto" class="form-label">Nombre del Producto</label>
            <input type="text" name="nombre_producto" id="nombre_producto" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label for="precio" class="form-label">Precio ($)</label>
            <input type="number" name="precio" step="0.01" id="precio" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="categoria_id" class="form-label">Categoría</label>
            <select name="categoria_id" id="categoria_id" class="form-select" required>
                <?php while ($cat = $categorias->fetch_assoc()): ?>
                    <option value="<?= $cat['categoria_id'] ?>"><?= $cat['nombre_categoria'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="read_product.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
