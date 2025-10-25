document.addEventListener('DOMContentLoaded', function() {

    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            // Opcional: guardar el estado en localStorage para recordarlo
            if (sidebar.classList.contains('collapsed')) {
                localStorage.setItem('sidebarState', 'collapsed');
            } else {
                localStorage.removeItem('sidebarState');
            }
        });
    }

    // Opcional: comprobar el estado del sidebar al cargar la página
    if (localStorage.getItem('sidebarState') === 'collapsed') {
        sidebar.classList.add('collapsed');
    }

    // Lógica para confirmación de cierre de sesión con SweetAlert2
    const logoutButton = document.querySelector('a[href*="action=logout"]');
    if(logoutButton) {
        logoutButton.addEventListener('click', function(e) {
            e.preventDefault();
            const logoutUrl = this.href;

            Swal.fire({
                title: '¿Estás seguro?',
                text: "Se cerrará tu sesión actual.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar',
                background: '#1e1e1e',
                color: '#e0e0e0'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = logoutUrl;
                }
            });
        });
    }

});
