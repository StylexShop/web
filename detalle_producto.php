<?php
session_start();
include 'db.php';

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$p = $res->fetch_assoc();
$stmt->close();

$imagenes = json_decode($p['imagenes'], true);
$colores = explode(',', $p['colores']);
$tallas = explode(',', $p['tallas']);

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Usuario Desconocido';
$is_admin = ($username === 'Admin');
$primerColor = trim($colores[0]);
$primerImagen = !empty($imagenes[$primerColor]) ? reset($imagenes[$primerColor]) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo htmlspecialchars($p['nombre']); ?></title>
  <link rel="stylesheet" href="css/productoo.css" />
  <link rel="stylesheet" href="css/footer.css" />
  <link rel="stylesheet" href="css/optionUser.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="icon" type="image/jpeg" href="img/logo/logo.png" />
</head>
<body>

<nav class="navbar"><div class="logo">S T Y L E X</div></nav>

<header>
  <div class="user-display" id="userDisplay">
    <div class="user-avatar"></div>
    <div class="user-text">
      <span><?php echo htmlspecialchars($username); ?></span>
      <small><?php echo $username === 'Usuario Desconocido' ? 'Invitado' : ($is_admin ? 'Administrador' : 'Usuario'); ?></small>
    </div>
    <div class="user-content">
      <?php if ($username === 'Usuario Desconocido'): ?>
        <a class="action-button login" href="login.php">👤 INICIAR SESIÓN</a>
      <?php else: ?>
        <a class="action-button" href="cart.php">🛒 VER CARRITO</a>
        <?php if ($is_admin): ?>
          <a class="action-button" href="gestionar_productos.php">📋 GESTIONAR</a>
        <?php endif; ?>
        <a class="action-button logout" href="logout.php">🔒 CERRAR SESIÓN</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<div class="contenedor-producto">
  <div class="galeria-imagenes">
    <div class="zoom-container">
      <img id="imagenPrincipal" class="imagen-principal" src="uploads/<?php echo htmlspecialchars($primerImagen); ?>" alt="<?php echo htmlspecialchars($p['nombre']); ?>">
    </div>
    <div id="miniaturas" class="miniaturas-container"></div>
  </div>

  <div class="info-producto">
    <h2><?php echo htmlspecialchars($p['nombre']); ?></h2>
    <p><strong>Código:</strong> <?php echo $p['id']; ?></p>
    <p><strong>Sección:</strong> <?php echo htmlspecialchars($p['seccion']); ?></p>
    <p><strong>Categoría:</strong> <?php echo htmlspecialchars($p['categoria_general']); ?></p>
    <p class="precio-actual">S/ <?php echo $p['precio']; ?></p>

    <div class="opcion-color">
      <p>Colores disponibles:</p>
      <?php foreach ($colores as $index => $color): ?>
        <button class="btn-color <?php echo $index === 0 ? 'seleccionado' : ''; ?>" data-color="<?php echo trim($color); ?>"><?php echo htmlspecialchars(trim($color)); ?></button>
      <?php endforeach; ?>
    </div>

    <div class="opcion-talla">
      <p>Tallas disponibles:</p>
      <?php foreach ($tallas as $talla): ?>
        <button class="btn-talla"><?php echo strtoupper(htmlspecialchars(trim($talla))); ?></button>
      <?php endforeach; ?>
    </div>

<form method="POST" action="carrito.php" onsubmit="return validarSeleccion();">
    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
    <input type="hidden" name="cantidad" id="cantidadInput" value="1">
    <input type="hidden" name="color" id="colorSeleccionado" value="<?php echo trim($colores[0]); ?>">
    <input type="hidden" name="talla" id="tallaSeleccionada" value="">
    <button type="submit" class="boton-carrito">AGREGAR AL CARRITO</button>
</form>

    <br><br>
    <details>
      <summary>Descripción</summary>
      <p><?php echo nl2br(htmlspecialchars($p['descripcion'])); ?></p>
    </details>

    <div class="estrellas"><?php echo str_repeat("★", $p['estrellas']) . str_repeat("☆", 5 - $p['estrellas']); ?></div>
  </div>
</div>

<footer class="footer-stylex">
  <div class="footer-container">
    <p>&copy; 2025 <strong>Stylex Ropa</strong> | Moda que inspira</p>
    <nav class="footer-nav">
      <a href="PoliticadePrivacidad.html">Política de privacidad</a>
      <a href="TerminosyCondiciones.html">Términos y condiciones</a>
    </nav>
  </div>
</footer>

<script>
const imagenesPorColor = <?php echo json_encode($imagenes); ?>;
const imagenPrincipal = document.getElementById('imagenPrincipal');
const miniaturasContainer = document.getElementById('miniaturas');

function cargarMiniaturas(color) {
  miniaturasContainer.innerHTML = '';

  if (imagenesPorColor[color]) {
    const imagenes = Object.values(imagenesPorColor[color]);
    if (imagenes.length > 0) {
      imagenPrincipal.src = 'uploads/' + imagenes[0];
    }

    imagenes.forEach(imgNombre => {
      const miniatura = document.createElement('img');
      miniatura.src = 'uploads/' + imgNombre;
      miniatura.classList.add('miniatura-img');
      miniatura.addEventListener('click', () => {
        imagenPrincipal.src = 'uploads/' + imgNombre;
      });
      miniaturasContainer.appendChild(miniatura);
    });
  }
}

window.addEventListener('DOMContentLoaded', () => {
  const primerColor = document.querySelector('.btn-color')?.getAttribute('data-color');
  if (primerColor) {
    cargarMiniaturas(primerColor);
    document.getElementById('colorSeleccionado').value = primerColor;
  }
});

document.querySelectorAll('.btn-color').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.btn-color').forEach(b => b.classList.remove('seleccionado'));
    this.classList.add('seleccionado');
    const color = this.getAttribute('data-color');
    cargarMiniaturas(color);
    document.getElementById('colorSeleccionado').value = color;
  });
});

document.querySelectorAll('.btn-talla').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.btn-talla').forEach(b => b.classList.remove('seleccionado'));
    this.classList.add('seleccionado');
    document.getElementById('tallaSeleccionada').value = this.textContent.trim();
  });
});
</script>

<script src="js/optionUser.js"></script>
</body>
</html>
