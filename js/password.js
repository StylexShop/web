    const verClave = document.getElementById('verClave');
    const inputClave = document.getElementById('password');

    verClave.addEventListener('click', function () {
      const tipo = inputClave.getAttribute('type') === 'password' ? 'text' : 'password';
      inputClave.setAttribute('type', tipo);
      this.classList.toggle('fa-eye-slash');
    });