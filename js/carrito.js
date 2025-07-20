  let carritoItems = [];

  function cargarCarritoDesdeLocalStorage() {
    const carritoGuardado = localStorage.getItem('carrito');
    if (carritoGuardado) {
      carritoItems = JSON.parse(carritoGuardado);
    }
  }

  function guardarCarrito() {
    localStorage.setItem('carrito', JSON.stringify(carritoItems));
  }

  function actualizarContadorCarrito() {
    const totalItems = carritoItems.reduce((total, item) => total + item.cantidad, 0);
    const contador = document.querySelector('.cart-count');
    if (contador) contador.textContent = totalItems;
  }

  function mostrarCarrito() {
    const itemsDiv = document.getElementById('carrito-items');
    const vacioDiv = document.getElementById('carrito-vacio');
    const resumenDiv = document.getElementById('resumen-carrito');

    if (carritoItems.length === 0) {
      vacioDiv.style.display = 'block';
      itemsDiv.style.display = 'none';
      resumenDiv.style.display = 'none';
      actualizarContadorCarrito();
      return;
    }

    vacioDiv.style.display = 'none';
    itemsDiv.style.display = 'block';
    resumenDiv.style.display = 'block';
    itemsDiv.innerHTML = '';

    carritoItems.forEach((item, index) => {
      const itemDiv = document.createElement('div');
      itemDiv.className = 'item-carrito';
      itemDiv.innerHTML = `
        <img src="${item.imagen}" class="item-imagen">
        <div class="item-detalles">
          <div class="item-nombre">${item.nombre}</div>
          <div class="item-descripcion">Talla: ${item.talla}</div>
          <div class="item-precio">S/ ${item.precio.toFixed(2)}</div>
        </div>
        <div class="item-cantidad">
          <button class="cantidad-btn" onclick="cambiarCantidad(${index}, -1)">-</button>
          <input type="number" class="cantidad-input" value="${item.cantidad}" min="1" onchange="actualizarCantidad(${index}, this.value)">
          <button class="cantidad-btn" onclick="cambiarCantidad(${index}, 1)">+</button>
        </div>
        <div class="item-total">S/ ${(item.precio * item.cantidad).toFixed(2)}</div>
        <button class="btn-eliminar" onclick="eliminarItem(${index})">
          <i class="fas fa-trash"></i>
        </button>
      `;
      itemsDiv.appendChild(itemDiv);
    });

    actualizarResumen();
    actualizarContadorCarrito();
  }

  function cambiarCantidad(index, cambio) {
    carritoItems[index].cantidad += cambio;
    if (carritoItems[index].cantidad <= 0) {
      eliminarItem(index);
    } else {
      guardarCarrito();
      mostrarCarrito();
    }
  }

  function actualizarCantidad(index, nuevaCantidad) {
    const nueva = parseInt(nuevaCantidad);
    if (!isNaN(nueva) && nueva > 0) {
      carritoItems[index].cantidad = nueva;
      guardarCarrito();
      mostrarCarrito();
    } else {
      eliminarItem(index);
    }
  }

  function eliminarItem(index) {
    carritoItems.splice(index, 1);
    guardarCarrito();
    mostrarCarrito();
  }

  function vaciarCarrito() {
    if (confirm('¿Vaciar el carrito completo?')) {
      carritoItems = [];
      localStorage.removeItem('carrito');
      mostrarCarrito();
    }
  }

  function actualizarResumen() {
    const subtotal = carritoItems.reduce((acc, item) => acc + item.precio * item.cantidad, 0);
    const envio = subtotal > 100 ? 0 : 15;
    const total = subtotal + envio;

    document.getElementById('subtotal').textContent = `S/ ${subtotal.toFixed(2)}`;
    document.getElementById('envio').textContent = envio === 0 ? 'GRATIS' : `S/ ${envio.toFixed(2)}`;
    document.getElementById('descuento').textContent = 'S/ 0.00';
    document.getElementById('total').textContent = `S/ ${total.toFixed(2)}`;
  }

  function procederCheckout() {
    if (carritoItems.length === 0) {
      alert('Tu carrito está vacío');
    } else {
      alert('Gracias por tu compra. Función en desarrollo.');
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    cargarCarritoDesdeLocalStorage();
    mostrarCarrito();
  });
  
