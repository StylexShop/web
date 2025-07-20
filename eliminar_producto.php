<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Obtener nombres de imágenes antes de eliminar el producto
    $stmt = $conn->prepare("SELECT imagenes FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $producto = $resultado->fetch_assoc();
    $stmt->close();

    if ($producto && !empty($producto['imagenes'])) {
        $imagenes = json_decode($producto['imagenes'], true);
        if (is_array($imagenes)) {
            foreach ($imagenes as $grupo) {
                foreach ($grupo as $img) {
                    $ruta = "uploads/" . $img;
                    if (file_exists($ruta)) {
                        unlink($ruta);
                    }
                }
            }
        }
    }

    // Eliminar el producto de la base de datos
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: gestionar_productos.php");
exit();
?>
