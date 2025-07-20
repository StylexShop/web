const botones = document.querySelectorAll('.boton-talla');
const inputTallas = document.getElementById('input-tallas');
let tallasSeleccionadas = [];

botones.forEach(boton => {
    boton.addEventListener('click', () => {
        const talla = boton.getAttribute('data-talla');
        if (tallasSeleccionadas.includes(talla)) {
            tallasSeleccionadas = tallasSeleccionadas.filter(t => t !== talla);
            boton.classList.remove('activo');
        } else {
            tallasSeleccionadas.push(talla);
            boton.classList.add('activo');
        }
        inputTallas.value = tallasSeleccionadas.join(',');
    });
});