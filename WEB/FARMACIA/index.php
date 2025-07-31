<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
$rol = $_SESSION['rol'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sistema de Farmacia</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f4; }
        .container { max-width: 1200px; margin: 0 auto; background-color: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
        .menu { margin: 20px 0; }
        .menu a { display: inline-block; margin: 5px; padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 3px; }
        .menu a:hover { background-color: #45a049; }
        .logout { float: right; }
        .logout a { background-color: #f44336; }
        .logout a:hover { background-color: #da190b; }
        .role-info { background-color: #e7f3ff; padding: 10px; border-radius: 3px; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container">
    <div class="logout">
        <a href="logout.php">Cerrar sesión</a>
    </div>
    
    <h2>Sistema de Gestión de Farmacia</h2>
    
    <div class="role-info">
        <strong>Bienvenido:</strong> <?php echo $_SESSION['usuario']; ?> 
        <strong>Rol:</strong> <?php echo $rol; ?>
    </div>
    
    <div class="menu">
        <?php if ($rol == 'Administrador' || $rol == 'Desarrollador'): ?>
            <a href="crud_categorias_medicamento.php">Categorías</a>
            <a href="crud_proveedores.php">Proveedores</a>
            <a href="crud_medicamentos.php">Medicamentos</a>
            <a href="crud_clientes.php">Clientes</a>
            <a href="crud_empleados.php">Empleados</a>
            <a href="crud_usuarios.php">Usuarios</a>
            <a href="crud_recetas.php">Recetas Médicas</a>
            <a href="crud_ventas.php">Ventas</a>
            <a href="crud_detalle_venta.php">Detalle de Ventas</a>
        <?php elseif ($rol == 'Supervisor'): ?>
            <a href="crud_medicamentos.php">Medicamentos</a>
            <a href="crud_clientes.php">Clientes</a>
            <a href="crud_recetas.php">Recetas Médicas</a>
            <a href="crud_ventas.php">Ventas</a>
            <a href="crud_detalle_venta.php">Detalle de Ventas</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html> 