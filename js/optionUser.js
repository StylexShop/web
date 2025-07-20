function toggleUserContent() {
    var userDisplay = document.getElementById("userDisplay");
    userDisplay.classList.toggle("open");
}

document.getElementById("userDisplay").addEventListener("click", toggleUserContent);

document.addEventListener('DOMContentLoaded', function () {
    const logoutButton = document.querySelector('.logout');
    if (logoutButton) {
        logoutButton.addEventListener('click', function (e) {
            e.preventDefault();
            if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
                window.location.href = 'logout.php';
            }
        });
    }
});