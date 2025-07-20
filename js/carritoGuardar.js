    let tallaSeleccionada = '';
    const botonesTalla = document.querySelectorAll('.btn-talla');

    botonesTalla.forEach((btn) => {
      btn.addEventListener('click', () => {
        botonesTalla.forEach((b) => b.classList.remove('seleccionado'));
        btn.classList.add('seleccionado');
        tallaSeleccionada = btn.textContent;
      });
    });

    document.querySelector('.btn-sumar').addEventListener('click', () => {
      let cantidad = parseInt(document.getElementById('cantidad').value);
      document.getElementById('cantidad').value = cantidad + 1;
    });

    document.querySelector('.btn-restar').addEventListener('click', () => {
      let cantidad = parseInt(document.getElementById('cantidad').value);
      if (cantidad > 1) {
        document.getElementById('cantidad').value = cantidad - 1;
      }
    });

    document.getElementById('agregarCarrito').addEventListener('click', () => {
      const nombre = document.querySelector('.info-producto h2').textContent;
      const precio = parseFloat(document.querySelector('.precio-actual').textContent.replace('S/', '').trim());
      const cantidad = parseInt(document.getElementById('cantidad').value);
      const imagen = document.querySelector('.imagen-principal').src;

      if (!tallaSeleccionada) {
        alert('Por favor selecciona una talla.');
        return;
      }

      const producto = {
        nombre,
        precio,
        cantidad,
        talla: tallaSeleccionada,
        imagen
      };

      let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
      carrito.push(producto);
      localStorage.setItem('carrito', JSON.stringify(carrito));

      alert('Producto agregado al carrito correctamente.');
    });