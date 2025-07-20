<?php
session_start();
include 'db.php';

// Solo el admin puede acceder
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'Admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="css/formulario.css">
    <link rel="stylesheet" href="css/tablagestion1.css">
    <link rel="icon" type="image/jpeg" href="img/logo/logo.png" />
    <style>
        .imagenes-color {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .imagenes-color strong {
            margin-top: 5px;
        }
        .imagenes-color img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            margin-right: 5px;
            border-radius: 6px;
        }
        .grupo-imagenes {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>

<nav>S T Y L E X</nav>

<h2>📋 Gestión de Productos</h2>

<a href="agregar_producto.php" class="boton boton-agregar">➕ Agregar nuevo producto</a>
<a href="Inicio.php" class="boton">↩️ Regresar al catálogo</a>

<div class="contenedor-gestion">
    <h3>🧾 Productos actuales</h3>
    <table class="tabla-productos">
        <tr>
            <th>Imágenes</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Tallas</th>
            <th>Colores</th>
            <th>Estrellas</th>
            <th>Sección</th>
            <th>Categoría</th>
            <th>Opciones</th>
        </tr>
        <?php
        $resultado = $conn->query("SELECT * FROM productos");
        while ($producto = $resultado->fetch_assoc()):
            $imagenes_html = '';
            $colores = explode(',', $producto['colores']);
            $imagenes = [];

            if (!empty($producto['imagenes'])) {
                $imagenes = json_decode($producto['imagenes'], true);
            }
        ?>
        <tr>
            <td>
                <div class="imagenes-color">
                    <?php if (!empty($imagenes) && is_array($imagenes)): ?>
                        <?php foreach ($imagenes as $color => $imagenesColor): ?>
                            <strong><?php echo ucfirst($color); ?>:</strong>
                            <div class="grupo-imagenes">
                                <?php foreach ($imagenesColor as $img): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($img); ?>" alt="">
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php elseif (!empty($producto['imagen'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="" width="80">
                    <?php else: ?>
                        <span>Sin imagen</span>
                    <?php endif; ?>
                </div>
            </td>
            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
            <td>S/ <?php echo number_format($producto['precio'], 2); ?></td>
            <td><?php echo htmlspecialchars($producto['tallas']); ?></td>
            <td><?php echo htmlspecialchars($producto['colores']); ?></td>
            <td><?php echo str_repeat('★', $producto['estrellas']) . str_repeat('☆', 5 - $producto['estrellas']); ?></td>
            <td><?php echo htmlspecialchars($producto['seccion']); ?></td>
            <td><?php echo htmlspecialchars($producto['categoria_general']); ?></td>
            <td>
                <a href="editar_producto.php?id=<?php echo $producto['id']; ?>" class="boton">✏️ Editar</a>
                <a href="eliminar_producto.php?id=<?php echo $producto['id']; ?>" class="boton" onclick="return confirm('¿Estás seguro de eliminar este producto?')">🗑️ Eliminar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
