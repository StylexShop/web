    <?php
    session_start();
    include 'db.php';

    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $is_admin = ($username === 'Admin');
    } else {
        $username = 'Usuario Desconocido';
        $is_admin = false;
    }
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Catálogo - Hombre</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" href="css/optionUser.css" />
        <link rel="stylesheet" href="css/navbar.css" />
        <link rel="stylesheet" href="css/footer.css" />
        <link rel="stylesheet" href="css/cart.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
        <link rel="icon" type="image/jpeg" href="img/logo/logo.png" />

        <style>
            body {
                font-family: 'Inter', sans-serif;
                margin: 0;
                background-color: #f0f0f0;
            }
            h2 {
                text-align: center;
                margin-top: 40px;
                font-size: 28px;
                color: #333;
            }
            .contenedor-catalogo {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 24px;
                padding: 40px;
                max-width: 1400px;
                margin: 0 auto;
                background-color: #f5f5f5;
            }
            .tarjeta-producto {
                background-color: #fff;
                width: 260px;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                display: flex;
                flex-direction: column;
                transition: transform 0.2s ease;
            }
            .tarjeta-producto:hover {
                transform: translateY(-5px);
            }
            .tarjeta-producto img {
                width: 100%;
                height: 320px;
                object-fit: cover;
            }
            .tarjeta-producto h3 {
                font-size: 16px;
                font-weight: bold;
                margin: 12px 0 4px;
                padding: 0 12px;
                text-align: center;
            }
            .estrellas {
                font-size: 16px;
                color: #f7d300;
                text-align: center;
                margin-bottom: 8px;
            }
            .tarjeta-producto p {
                font-size: 14px;
                color: #333;
                margin: 4px 0;
                padding: 0 12px;
                text-align: center;
            }
            .tarjeta-producto a,
            .tarjeta-producto button {
                background-color: black;
                color: white;
                border: none;
                border-radius: 6px;
                padding: 8px 14px;
                margin: 6px;
                text-align: center;
                font-weight: bold;
                cursor: pointer;
                text-decoration: none;
                display: block;
                width: calc(100% - 24px);
            }
            .tarjeta-producto a:hover,
            .tarjeta-producto button:hover {
                background-color: #f7d300;
                color: black;
            }
            .tarjeta-producto form {
                margin: 0;
                text-align: center;
            }
        </style>
    </head>
    <body>

    <header class="topbar">
        <div class="logo">
            <img src="img/Logo/logolarge.png" alt="Stylex Logo" />
        </div>
        <div class="search-container">
            <input type="text" placeholder="¿Qué estás buscando?" />
            <i class="fas fa-search"></i>
        </div>
    </header>

    <div class="user-display" id="userDisplay">
        <div class="user-avatar"></div>
        <div class="user-text">
            <span><?php echo $username; ?></span>
            <small>
                <?php
                echo $username === 'Usuario Desconocido' ? 'Invitado' : ($is_admin ? 'Administrador' : 'Usuario');
                ?>
            </small>
        </div>
        <div class="user-content">
            <?php if ($username === 'Usuario Desconocido'): ?>
                <a class="action-button login" href="login.php">👤 INICIAR SESIÓN</a>
            <?php else: ?>
                <a class="action-button" href="carrito.php">🛒 VER CARRITO</a>
                <?php if ($is_admin): ?>
                    <a class="action-button" href="gestionar_productos.php">📋 GESTIONAR</a>
                <?php endif; ?>
                <a class="action-button logout" href="logout.php">🔒 CERRAR SESIÓN</a>
            <?php endif; ?>
        </div>
    </div>

    <nav class="menubar">
        <a href="Inicio.php">Inicio</a>
        <a href="catalogoMujer.php">Mujer</a>
        <a href="catalogohombre.php">Hombre</a>
        <a href="catalogoNiños.php">Infantil</a>
        <a href="contacto.html">Ayuda</a>
    </nav>

    <?php
    $secciones = ["Polos", "Camisas", "Jeans", "Poleras", "Casacas", "Buzos"];
    foreach ($secciones as $seccion):
    ?>
        <h2>Catálogo de <?php echo htmlspecialchars($seccion); ?> para Hombres</h2>
        <div class="contenedor-catalogo">
        <?php
        $stmt = $conn->prepare("SELECT * FROM productos WHERE categoria_general = 'Hombre' AND seccion = ?");
        $stmt->bind_param("s", $seccion);
        $stmt->execute();
        $resultado = $stmt->get_result();
        while ($p = $resultado->fetch_assoc()):
            $imagenes = !empty($p['imagenes']) ? json_decode($p['imagenes'], true) : [];
            $primera_imagen = '';

            if (!empty($imagenes) && is_array($imagenes)) {
                foreach ($imagenes as $grupoColor => $imagenesColor) {
                    foreach ($imagenesColor as $img) {
                        $primera_imagen = $img;
                        break 2;
                    }
                }
            }
        ?>
            <div class="tarjeta-producto">
                <?php if ($primera_imagen): ?>
                    <img src="uploads/<?php echo htmlspecialchars($primera_imagen); ?>" alt="<?php echo htmlspecialchars($p['nombre']); ?>">
                <?php else: ?>
                    <img src="img/placeholder.jpg" alt="Sin imagen">
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($p['nombre']); ?></h3>
                <div class="estrellas"><?php echo str_repeat("★", $p['estrellas']) . str_repeat("☆", 5 - $p['estrellas']); ?></div>
                <p><?php echo htmlspecialchars($p['descripcion']); ?></p>
                <p><strong>Tallas:</strong> <?php echo htmlspecialchars($p['tallas']); ?></p>
                <p><strong>Colores:</strong> <?php echo htmlspecialchars($p['colores']); ?></p>
                <p><strong>Precio:</strong> S/. <?php echo number_format($p['precio'], 2); ?></p>
                <a href="detalle_producto.php?id=<?php echo $p['id']; ?>">Ver más</a>
                <form method="POST" action="comprar.php">
                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                </form>
            </div>
        <?php endwhile; ?>
        </div>
    <?php endforeach; ?>

    <footer class="footer-stylex">
        <div class="footer-container">
            <p>&copy; 2025 <strong>Stylex Ropa</strong> | Moda que inspira</p>
            <nav class="footer-nav">
                <a href="PoliticadePrivacidad.html">Política de privacidad</a>
                <a href="TerminosyCondiciones.html">Términos y condiciones</a>
            </nav>
        </div>
    </footer>

    <script src="js/optionUser.js"></script>
    <script src="js/slider.js"></script>
    </body>
    </html>
