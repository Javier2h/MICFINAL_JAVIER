<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
$rol = $_SESSION['rol'];
if (!in_array($rol, ['Administrador', 'Desarrollador', 'Supervisor'])) {
    die("No tienes permisos para acceder.");
}
$solo_lectura = ($rol == 'Supervisor');
?>
<!DOCTYPE html>
<html>
<head>
    <title>CRUD Medicamentos (API)</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f4; }
        .container { max-width: 1400px; margin: 0 auto; background-color: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 12px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #4CAF50; color: white; }
        tr:hover { background-color: #f5f5f5; }
        .btn { padding: 6px 10px; text-decoration: none; border-radius: 3px; margin: 2px; font-size: 11px; }
        .btn-edit { background-color: #2196F3; color: white; }
        .btn-delete { background-color: #f44336; color: white; }
        .btn-back { background-color: #666; color: white; }
        .btn-search { background-color: #4CAF50; color: white; }
        .btn-clear { background-color: #ff9800; color: white; }
        form { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        input[type="text"], input[type="number"], input[type="date"], select { width: 100%; padding: 8px; margin: 5px 0; border: 1px solid #ddd; border-radius: 3px; }
        input[type="submit"] { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; }
        input[type="submit"]:hover { background-color: #45a049; }
        .form-row { display: flex; gap: 15px; }
        .form-group { flex: 1; }
        .stock-bajo { color: red; font-weight: bold; }
        .stock-medio { color: orange; }
        .search-form { background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .search-form h3 { margin-top: 0; color: #333; }
        .search-buttons { display: flex; gap: 10px; margin-top: 10px; }
        .search-buttons input[type="submit"] { padding: 8px 15px; }
        .search-buttons a { padding: 8px 15px; text-decoration: none; border-radius: 3px; }
        .results-info { background-color: #e8f5e8; padding: 10px; border-radius: 3px; margin-bottom: 15px; }
        .hidden { display: none; }
    </style>
</head>
<body>
<div class="container">
    <h2>Gestión de Medicamentos (API)</h2>
    <a href="index.php" class="btn btn-back">Volver al Menú Principal</a>
    
    <!-- Formulario de búsqueda -->
    <div class="search-form">
        <h3>Buscar Medicamentos</h3>
        <form id="form-busqueda">
            <div class="form-row">
                <div class="form-group">
                    <label>Buscar por nombre, principio activo, presentación, proveedor o categoría:</label>
                    <input type="text" name="buscar" id="buscar" placeholder="Ingrese término de búsqueda...">
                </div>
                <div class="form-group">
                    <label>Filtrar por Proveedor:</label>
                    <select name="filtro_proveedor" id="filtro_proveedor">
                        <option value="0">Todos los proveedores</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Filtrar por Categoría:</label>
                    <select name="filtro_categoria" id="filtro_categoria">
                        <option value="0">Todas las categorías</option>
                    </select>
                </div>
            </div>
            <div class="search-buttons">
                <input type="submit" value="Buscar" class="btn-search">
                <a href="#" class="btn-clear" id="limpiar-filtros">Limpiar Filtros</a>
            </div>
        </form>
    </div>

    <div id="info-resultados" class="results-info hidden"></div>

    <table id="tabla-medicamentos">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Principio Activo</th>
            <th>Presentación</th>
            <th>Fecha Caducidad</th>
            <th>Stock</th>
            <th>Precio Unit.</th>
            <th>Proveedor</th>
            <th>Categoría</th>
            <?php if (!$solo_lectura): ?><th>Acciones</th><?php endif; ?>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
    
    <?php if (!$solo_lectura): ?>
    <h3 id="form-title">Agregar Medicamento</h3>
    <form id="form-medicamento">
        <input type="hidden" name="id_medicamento" id="id_medicamento">
        <div class="form-row">
            <div class="form-group">
                <label>Nombre del Medicamento:</label>
                <input type="text" name="nombre_medicamento" id="nombre_medicamento" required>
            </div>
            <div class="form-group">
                <label>Principio Activo:</label>
                <input type="text" name="principio_activo" id="principio_activo" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Presentación:</label>
                <input type="text" name="presentacion" id="presentacion" required>
            </div>
            <div class="form-group">
                <label>Fecha de Caducidad:</label>
                <input type="date" name="fecha_caducidad" id="fecha_caducidad" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Stock:</label>
                <input type="number" name="stock" id="stock" min="0" required>
            </div>
            <div class="form-group">
                <label>Precio Unitario:</label>
                <input type="number" step="0.01" name="precio_unitario" id="precio_unitario" min="0" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Proveedor:</label>
                <select name="id_proveedor" id="id_proveedor" required></select>
            </div>
            <div class="form-group">
                <label>Categoría:</label>
                <select name="id_categoria" id="id_categoria" required></select>
            </div>
        </div>
        <input type="submit" value="Guardar">
        <input type="button" value="Cancelar" id="cancelar-edicion" class="btn btn-clear hidden">
    </form>
    <?php endif; ?>
</div>
<script>
const API_BASE = 'api_gateway.php/medicamentos';

// Cargar proveedores y categorías para los selects
async function cargarProveedoresYCategorias() {
    // Proveedores
    let respProv = await fetch('api_gateway.php/proveedores');
    let dataProv = await respProv.json();
    let provSel = document.getElementById('id_proveedor');
    let filtroProvSel = document.getElementById('filtro_proveedor');
    if (dataProv.data) {
        dataProv.data.forEach(p => {
            let opt = document.createElement('option');
            opt.value = p.id_proveedor;
            opt.textContent = p.nombre_proveedor;
            provSel && provSel.appendChild(opt.cloneNode(true));
            filtroProvSel && filtroProvSel.appendChild(opt);
        });
    }
    // Categorías
    let respCat = await fetch('api_gateway.php/categorias');
    let dataCat = await respCat.json();
    let catSel = document.getElementById('id_categoria');
    let filtroCatSel = document.getElementById('filtro_categoria');
    if (dataCat.data) {
        dataCat.data.forEach(c => {
            let opt = document.createElement('option');
            opt.value = c.id_categoria;
            opt.textContent = c.nombre_categoria;
            catSel && catSel.appendChild(opt.cloneNode(true));
            filtroCatSel && filtroCatSel.appendChild(opt);
        });
    }
}

// Cargar medicamentos
async function cargarMedicamentos() {
    let params = new URLSearchParams();
    let buscar = document.getElementById('buscar').value;
    let filtro_proveedor = document.getElementById('filtro_proveedor').value;
    let filtro_categoria = document.getElementById('filtro_categoria').value;
    if (buscar) params.append('buscar', buscar);
    if (filtro_proveedor && filtro_proveedor !== '0') params.append('filtro_proveedor', filtro_proveedor);
    if (filtro_categoria && filtro_categoria !== '0') params.append('filtro_categoria', filtro_categoria);
    let resp = await fetch(API_BASE + '?' + params.toString());
    let data = await resp.json();
    let tbody = document.querySelector('#tabla-medicamentos tbody');
    tbody.innerHTML = '';
    let total = 0;
    if (data.data) {
        data.data.forEach(row => {
            let tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${row.id_medicamento}</td>
                <td>${row.nombre_medicamento}</td>
                <td>${row.principio_activo}</td>
                <td>${row.presentacion}</td>
                <td>${row.fecha_caducidad}</td>
                <td class="${row.stock < 10 ? 'stock-bajo' : (row.stock < 50 ? 'stock-medio' : '')}">${row.stock}</td>
                <td>$${parseFloat(row.precio_unitario).toFixed(2)}</td>
                <td>${row.nombre_proveedor || ''}</td>
                <td>${row.nombre_categoria || ''}</td>
                <?php if (!$solo_lectura): ?>
                <td>
                    <button class="btn btn-edit" onclick="editarMedicamento(${encodeURIComponent(JSON.stringify(row))})">Editar</button>
                    <button class="btn btn-delete" onclick="eliminarMedicamento(${row.id_medicamento})">Eliminar</button>
                </td>
                <?php endif; ?>
            `;
            tbody.appendChild(tr);
            total++;
        });
    }
    let info = document.getElementById('info-resultados');
    if (buscar || (filtro_proveedor && filtro_proveedor !== '0') || (filtro_categoria && filtro_categoria !== '0')) {
        info.textContent = `Se encontraron ${total} medicamento(s)`;
        info.classList.remove('hidden');
    } else {
        info.classList.add('hidden');
    }
}

// Crear o actualizar medicamento
async function guardarMedicamento(e) {
    e.preventDefault();
    let id = document.getElementById('id_medicamento').value;
    let datos = {
        nombre_medicamento: document.getElementById('nombre_medicamento').value,
        principio_activo: document.getElementById('principio_activo').value,
        presentacion: document.getElementById('presentacion').value,
        fecha_caducidad: document.getElementById('fecha_caducidad').value,
        stock: document.getElementById('stock').value,
        precio_unitario: document.getElementById('precio_unitario').value,
        id_proveedor: document.getElementById('id_proveedor').value,
        id_categoria: document.getElementById('id_categoria').value
    };
    let method = id ? 'PUT' : 'POST';
    if (id) datos.id_medicamento = id;
    let resp = await fetch(API_BASE, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
    });
    let res = await resp.json();
    if (res.success) {
        document.getElementById('form-medicamento').reset();
        document.getElementById('id_medicamento').value = '';
        document.getElementById('form-title').textContent = 'Agregar Medicamento';
        document.getElementById('cancelar-edicion').classList.add('hidden');
        cargarMedicamentos();
    } else {
        alert('Error al guardar');
    }
}

// Eliminar medicamento
async function eliminarMedicamento(id) {
    if (!confirm('¿Estás seguro de eliminar este medicamento?')) return;
    let resp = await fetch(API_BASE + '?id=' + id, { method: 'DELETE' });
    let res = await resp.json();
    if (res.success) cargarMedicamentos();
    else alert('Error al eliminar');
}

// Editar medicamento (cargar datos en el formulario)
function editarMedicamento(rowStr) {
    let row = JSON.parse(decodeURIComponent(rowStr));
    document.getElementById('id_medicamento').value = row.id_medicamento;
    document.getElementById('nombre_medicamento').value = row.nombre_medicamento;
    document.getElementById('principio_activo').value = row.principio_activo;
    document.getElementById('presentacion').value = row.presentacion;
    document.getElementById('fecha_caducidad').value = row.fecha_caducidad;
    document.getElementById('stock').value = row.stock;
    document.getElementById('precio_unitario').value = row.precio_unitario;
    document.getElementById('id_proveedor').value = row.id_proveedor;
    document.getElementById('id_categoria').value = row.id_categoria;
    document.getElementById('form-title').textContent = 'Editar Medicamento';
    document.getElementById('cancelar-edicion').classList.remove('hidden');
}

document.getElementById('form-busqueda').onsubmit = function(e) {
    e.preventDefault();
    cargarMedicamentos();
};
document.getElementById('limpiar-filtros').onclick = function(e) {
    e.preventDefault();
    document.getElementById('buscar').value = '';
    document.getElementById('filtro_proveedor').value = '0';
    document.getElementById('filtro_categoria').value = '0';
    cargarMedicamentos();
};
<?php if (!$solo_lectura): ?>
document.getElementById('form-medicamento').onsubmit = guardarMedicamento;
document.getElementById('cancelar-edicion').onclick = function() {
    document.getElementById('form-medicamento').reset();
    document.getElementById('id_medicamento').value = '';
    document.getElementById('form-title').textContent = 'Agregar Medicamento';
    document.getElementById('cancelar-edicion').classList.add('hidden');
};
<?php endif; ?>

// Inicialización
cargarProveedoresYCategorias().then(cargarMedicamentos);
</script>
</body>
</html> 