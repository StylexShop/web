<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $colores = $_POST['colores'];
    $estrellas = $_POST['estrellas'];
    $seccion = $_POST['seccion'];
    $categoria_general = $_POST['categoria_general'];
    $tallas = isset($_POST['tallas']) ? $_POST['tallas'] : '';

    // Procesar imágenes por color
    $imagenes_color = [];

    foreach (explode(',', $colores) as $color) {
        $color_limpio = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($color));
        for ($i = 1; $i <= 2; $i++) {
            $input_name = "imagen_{$color_limpio}_{$i}";
            if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] === UPLOAD_ERR_OK) {
                $nombre_archivo = $color_limpio . "_{$i}_" . basename($_FILES[$input_name]['name']);
                $ruta_destino = 'uploads/' . $nombre_archivo;
                move_uploaded_file($_FILES[$input_name]['tmp_name'], $ruta_destino);
                $imagenes_color[$color][$i] = $nombre_archivo;
            }
        }
    }

    $imagenes_json = json_encode($imagenes_color);

    // Asegúrate de que la tabla productos tenga una columna "imagenes" tipo TEXT
    $stmt = $conn->prepare("INSERT INTO productos 
        (nombre, descripcion, precio, tallas, colores, estrellas, imagenes, seccion, categoria_general) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdssssss", $nombre, $descripcion, $precio, $tallas, $colores, $estrellas, $imagenes_json, $seccion, $categoria_general);
    $stmt->execute();
    $stmt->close();

    header("Location: gestionar_productos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto - Stylex</title>
    <link rel="stylesheet" href="css/formulario.css">
    <link rel="icon" type="image/jpeg" href="img/logo/logo.png" />
</head>
<body>

<nav>S T Y L E X</nav>
<h2>➕ Agregar producto</h2>

<form class="formulario" action="" method="POST" enctype="multipart/form-data">
    <div class="columna">
        <input type="text" name="nombre" placeholder="Nombre del producto" required>
        <textarea name="descripcion" placeholder="Descripción del producto" required></textarea>
        <input type="number" step="0.01" name="precio" placeholder="Precio S/." required>

        <div class="checkbox-group">
            <strong>Tallas disponibles:</strong><br>
            <div id="tallas-container">
                <button type="button" class="boton-talla" data-talla="S">S</button>
                <button type="button" class="boton-talla" data-talla="M">M</button>
                <button type="button" class="boton-talla" data-talla="L">L</button>
            </div>
            <input type="hidden" name="tallas" id="input-tallas">
        </div>

        <div class="checkbox-group">
            <strong>Colores disponibles:</strong><br>
            <input type="text" id="color-input" placeholder="Escribe un color y presiona Agregar">
            <button type="button" id="agregar-color">Agregar</button>
            <div id="colores-lista"></div>
            <input type="hidden" name="colores" id="input-colores">
        </div>

        <div class="checkbox-group">
            <strong>Imágenes por color:</strong>
            <div id="imagenes-por-color"></div>
        </div>
    </div>

    <div class="columna">
        <select name="estrellas" required>
            <option value="">Estrellas</option>
            <option value="5">★★★★★</option>
            <option value="4">★★★★☆</option>
            <option value="3">★★★☆☆</option>
            <option value="2">★★☆☆☆</option>
            <option value="1">★☆☆☆☆</option>
        </select>

        <select name="seccion" required>
            <option value="">Selecciona sección</option>
            <option value="Polos">Polos</option>
            <option value="Camisas">Camisas</option>
            <option value="Poleras">Poleras</option>
            <option value="Casacas">Casacas</option>
            <option value="Buzos">Buzos</option>
            <option value="Jeans">Jeans</option>
        </select>

        <select name="categoria_general" required>
            <option value="">Categoría general</option>
            <option value="Hombre">Hombre</option>
            <option value="Mujer">Mujer</option>
            <option value="Niño">Niño</option>
        </select>

        <input type="submit" value="Guardar producto">
        <a href="gestionar_productos.php" class="boton-volver">Regresar</a>
    </div>
</form>

<script src="js/tallas.js"></script>
<script src="js/colores.js"></script>

</body>
</html>