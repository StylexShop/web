    // Mostrar u ocultar la contraseña
    const icono = document.getElementById('verClave');
    const inputClave = document.getElementById('clave');
    icono.addEventListener('click', function () {
      const tipo = inputClave.getAttribute('type') === 'password' ? 'text' : 'password';
      inputClave.setAttribute('type', tipo);
      icono.classList.toggle('fa-eye-slash');
    });

    // Validar inicio de sesión
    document.getElementById('formulario-login').addEventListener('submit', function (e) {
      e.preventDefault();

      const correoIngresado = document.getElementById('correo').value.trim();
      const claveIngresada = document.getElementById('clave').value.trim();

      const usuarios = JSON.parse(localStorage.getItem('usuarios')) || [];

      const usuario = usuarios.find(u => u.correo === correoIngresado && u.clave === claveIngresada);

      if (usuario) {
        // Guardar sesión activa
        localStorage.setItem('usuarioActual', JSON.stringify(usuario));
        alert('¡Inicio de sesión exitoso!');
        window.location.href = 'index.html';
      } else {
        alert('Correo o contraseña incorrectos.');
      }
    });
