    document.getElementById('formulario-registro').addEventListener('submit', function(e) {
      e.preventDefault();

      const nombre = document.getElementById('nombre').value.trim();
      const correo = document.getElementById('correo').value.trim();
      const clave = document.getElementById('clave').value.trim();

      let usuarios = JSON.parse(localStorage.getItem('usuarios')) || [];

      const correoExistente = usuarios.some(u => u.correo === correo);
      if (correoExistente) {
        alert('Este correo ya está registrado.');
        return;
      }

      usuarios.push({ nombre, correo, clave });
      localStorage.setItem('usuarios', JSON.stringify(usuarios));

      alert('¡Registro exitoso! Ahora puedes iniciar sesión.');
      window.location.href = 'sesion.html';
    });
