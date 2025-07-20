
const colorInput = document.getElementById('color-input');
const agregarColorBtn = document.getElementById('agregar-color');
const coloresLista = document.getElementById('colores-lista');
const inputColores = document.getElementById('input-colores');
const imagenesPorColor = document.getElementById('imagenes-por-color');
let coloresSeleccionados = [];

function actualizarColoresHidden() {
    inputColores.value = coloresSeleccionados.join(',');
}

function crearBotonColor(color) {
    const div = document.createElement('div');
    div.className = 'color-item';
    div.textContent = color;

    const botonX = document.createElement('span');
    botonX.textContent = '×';
    botonX.onclick = () => {
        coloresSeleccionados = coloresSeleccionados.filter(c => c !== color);
        div.remove();
        document.getElementById('bloque_' + color).remove();
        actualizarColoresHidden();
    };

    div.appendChild(botonX);
    return div;
}

agregarColorBtn.addEventListener('click', () => {
    const color = colorInput.value.trim();
    if (color && !coloresSeleccionados.includes(color)) {
        coloresSeleccionados.push(color);
        const botonColor = crearBotonColor(color);
        coloresLista.appendChild(botonColor);
        actualizarColoresHidden();
        colorInput.value = '';

        // Crear inputs de imagen para el color
        const colorLimpio = color.replace(/\s+/g, '_').toLowerCase();
        const bloque = document.createElement('div');
        bloque.id = 'bloque_' + color;
        bloque.innerHTML = `
            <p><strong>${color}</strong> - Imagen 1:
                <input type="file" name="imagen_${colorLimpio}_1" accept="image/*" required>
            </p>
            <p>${color} - Imagen 2:
                <input type="file" name="imagen_${colorLimpio}_2" accept="image/*" required>
            </p>
        `;
        imagenesPorColor.appendChild(bloque);
    }
});