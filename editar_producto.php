<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$producto = $res->fetch_assoc();
$stmt->close();

$imagenes_actuales = json_decode($producto['imagenes'], true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $tallas = $_POST['tallas'];
    $colores = $_POST['colores'];
    $estrellas = $_POST['estrellas'];
    $seccion = $_POST['seccion'];
    $categoria_general = $_POST['categoria_general'];

    $imagenes_color = $imagenes_actuales ?? [];

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

    $stmt = $conn->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, tallas=?, colores=?, estrellas=?, imagenes=?, seccion=?, categoria_general=? WHERE id=?");
    $stmt->bind_param("ssdssssssi", $nombre, $descripcion, $precio, $tallas, $colores, $estrellas, $imagenes_json, $seccion, $categoria_general, $id);
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
    <title>Editar Producto</title>
    <link rel="stylesheet" href="css/formulario.css">
</head>
<body>

    <nav>S T Y L E X</nav>

<h2>✏️ Editar Producto</h2>

<form class="formulario" method="POST" enctype="multipart/form-data">
    <div class="columna">
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
        <textarea name="descripcion" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
        <input type="number" step="0.01" name="precio" value="<?php echo $producto['precio']; ?>" required>

        <div class="checkbox-group">
            <strong>Tallas disponibles:</strong><br>
            <div id="tallas-container">
                <?php
                $tallas_actuales = explode(",", str_replace(" ", "", $producto['tallas']));
                $opciones_tallas = ["S", "M", "L"];
                foreach ($opciones_tallas as $talla) {
                    $activo = in_array($talla, $tallas_actuales) ? 'activo' : '';
                    echo "<button type='button' class='boton-talla $activo' data-talla='$talla'>$talla</button>";
                }
                ?>
            </div>
            <input type="hidden" name="tallas" id="input-tallas" value="<?php echo htmlspecialchars($producto['tallas']); ?>">
        </div>

        <div class="checkbox-group">
            <strong>Colores disponibles:</strong><br>
            <input type="text" id="color-input" placeholder="Escribe un color y presiona Agregar">
            <button type="button" id="agregar-color">Agregar</button>
            <div id="colores-lista"></div>
            <input type="hidden" name="colores" id="input-colores" value="<?php echo htmlspecialchars($producto['colores']); ?>">
        </div>

        <div class="checkbox-group">
            <strong>Imágenes por color:</strong><br>
            <div id="imagenes-por-color">
                <?php
                if (!empty($imagenes_actuales)) {
                    foreach ($imagenes_actuales as $color => $imagenesColor) {
                        $color_limpio = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($color));
                        echo "<div><strong>" . htmlspecialchars($color) . "</strong><br>";
                        for ($i = 1; $i <= 2; $i++) {
                            $nombre_existente = isset($imagenesColor[$i]) ? $imagenesColor[$i] : '';
                            if ($nombre_existente) {
                                echo "<img src='uploads/" . htmlspecialchars($nombre_existente) . "' width='70'>";
                            }
                            echo "<input type='file' name='imagen_{$color_limpio}_{$i}'>";
                        }
                        echo "</div><br>";
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <div class="columna">
        <select name="estrellas" required>
            <option value="">Estrellas</option>
            <?php
            for ($i = 5; $i >= 1; $i--) {
                $seleccionado = $producto['estrellas'] == $i ? 'selected' : '';
                echo "<option value='$i' $seleccionado>" . str_repeat("★", $i) . str_repeat("☆", 5 - $i) . "</option>";
            }
            ?>
        </select>

        <select name="seccion" required>
            <option value="">Selecciona sección</option>
            <?php
            $secciones = ["Polos", "Camisas", "Poleras", "Casacas", "Buzos", "Jeans"];
            foreach ($secciones as $sec) {
                $seleccionado = $producto['seccion'] == $sec ? 'selected' : '';
                echo "<option value='$sec' $seleccionado>$sec</option>";
            }
            ?>
        </select>

        <select name="categoria_general" required>
            <option value="">Categoría general</option>
            <?php
            $categorias = ["Hombre", "Mujer", "Niño"];
            foreach ($categorias as $cat) {
                $seleccionado = $producto['categoria_general'] == $cat ? 'selected' : '';
                echo "<option value='$cat' $seleccionado>$cat</option>";
            }
            ?>
        </select>

        <input type="submit" value="Actualizar producto">
        <a href="gestionar_productos.php" class="boton-volver">Regresar</a>
    </div>
</form>

<script src="js/tallas.js"></script>
<script src="js/colores.js"></script>

<script>
window.addEventListener("DOMContentLoaded", () => {
    const coloresGuardados = document.getElementById("input-colores").value.split(",");
    coloresGuardados.forEach(color => {
        if (color.trim()) {
            coloresSeleccionados.push(color.trim());
            const botonColor = crearBotonColor(color.trim());
            coloresLista.appendChild(botonColor);
            actualizarColoresHidden();
        }
    });
});
</script>

</body>
</html>
