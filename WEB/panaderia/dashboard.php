<?php
include 'db.php';
session_start();

function contar($conn, $tabla) {
    $res = $conn->query("SELECT COUNT(*) AS total FROM $tabla");
    return $res->fetch_assoc()['total'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Panadería</title>
    <link rel="stylesheet" href="CSS/dashboard.css">
</head>
<body>

<!-- Encabezado -->
<div class="dashboard-header">
    👋 ¡Bienvenido, <?= ucfirst($_SESSION['rol']) ?>!
</div>

<!-- Navbar -->
<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-brand">🥖 Panadería Gato • Panel de Control</div>

        <ul class="navbar-menu">
            <li class="navbar-item">
                <span class="dropdown-btn" onclick="toggleDropdown(this)">🧑‍🍳 Clientes</span>
                <div class="dropdown-content">
                    <a href="CLIENTE/read_cliente.php" onclick="cargarContenido(this)">Ver Clientes</a>
                </div>
            </li>
            <li class="navbar-item">
                <span class="dropdown-btn" onclick="toggleDropdown(this)">🥐 Categorías</span>
                <div class="dropdown-content">
                    <a href="CATEGORIA/read_categoria.php" onclick="cargarContenido(this)">Ver Categorías</a>
                </div>
            </li>
            <li class="navbar-item">
                <span class="dropdown-btn" onclick="toggleDropdown(this)">🍞 Productos</span>
                <div class="dropdown-content">
                    <a href="PRODUCTO/read_product.php" onclick="cargarContenido(this)">Ver Productos</a>
                </div>
            </li>
            <li class="navbar-item">
                <span class="dropdown-btn" onclick="toggleDropdown(this)">🧾 Órdenes</span>
                <div class="dropdown-content">
                    <a href="ORDEN/read_orden.php" onclick="cargarContenido(this)">Ver Órdenes</a>
                </div>
            </li>
        </ul>

        <div class="logout-wrapper">
            <a href="index.php" class="btn-logout">🔒 Cerrar sistema</a>
        </div>
    </div>
</nav>

<!-- Contenido dinámico -->
<div class="content-container">
    <iframe id="contenido" src=""></iframe>
</div>

<script>
    function cargarContenido(enlace) {
        event.preventDefault();
        const url = enlace.getAttribute('href');
        document.getElementById('contenido').src = url;
    }

    function toggleDropdown(element) {
        const content = element.nextElementSibling;
        const allDropdowns = document.querySelectorAll('.dropdown-content');
        allDropdowns.forEach(d => {
            if (d !== content) d.style.display = 'none';
        });
        content.style.display = (content.style.display === 'block') ? 'none' : 'block';
    }

    window.onclick = function(event) {
        if (!event.target.matches('.dropdown-btn')) {
            document.querySelectorAll('.dropdown-content').forEach(d => d.style.display = 'none');
        }
    }
</script>

</body>
</html>
