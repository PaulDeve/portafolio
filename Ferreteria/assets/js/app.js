// assets/js/app.js
document.addEventListener('DOMContentLoaded', function(){
  // Page entrance animation
  const appContainer = document.querySelector('.app-container');
  if (appContainer) setTimeout(()=> appContainer.classList.add('visible'), 60);

  // Reveal on scroll using IntersectionObserver
  // Auto-assign reveal-on-scroll to some common blocks
  document.querySelectorAll('.card-summary').forEach(el=>{ if (!el.classList.contains('reveal-on-scroll')) el.classList.add('reveal-on-scroll'); });
  const reveals = document.querySelectorAll('.reveal-on-scroll');
  if ('IntersectionObserver' in window && reveals.length) {
    const obs = new IntersectionObserver((entries)=>{
      entries.forEach(e=>{
        if (e.isIntersecting) { e.target.classList.add('show'); obs.unobserve(e.target); }
      });
    }, { threshold: 0.12 });
    reveals.forEach(r=> obs.observe(r));
  } else {
    // Fallback: show all
    reveals.forEach(r=> r.classList.add('show'));
  }

  // Confirmación genérica para enlaces con data-confirm
  document.querySelectorAll('[data-confirm]').forEach(function(el){
    el.addEventListener('click', function(e){
      e.preventDefault();
      const href = this.getAttribute('href');
      const mensaje = this.getAttribute('data-confirm') || '¿Estás seguro?';
      if (!href) return;
      Swal.fire({
        title: 'Confirmar',
        text: mensaje,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location = href;
        }
      });
    });
  });

  // Button pressed effect: add .pressed while mousedown/touch
  document.querySelectorAll('button, .btn').forEach(btn=>{
    btn.addEventListener('mousedown', ()=> btn.classList.add('pressed'));
    btn.addEventListener('mouseup', ()=> btn.classList.remove('pressed'));
    btn.addEventListener('mouseleave', ()=> btn.classList.remove('pressed'));
    btn.addEventListener('touchstart', ()=> btn.classList.add('pressed'));
    btn.addEventListener('touchend', ()=> btn.classList.remove('pressed'));
  });
});

// Función para mostrar toast
function showToast(icon, title){
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
  });
  Toast.fire({
    icon: icon,
    title: title
  });
}

// Sidebar toggle for mobile
document.addEventListener('click', function(e){
  const btn = document.getElementById('btnToggleSidebar');
  const sidebar = document.getElementById('sidebar');
  if (!btn || !sidebar) return;
  if (e.target.closest && e.target.closest('#btnToggleSidebar')) {
    sidebar.classList.toggle('show');
    document.body.classList.toggle('sidebar-open');
    return;
  }
  // click outside sidebar to close
  if (sidebar.classList.contains('show') && !e.target.closest('#sidebar')) {
    sidebar.classList.remove('show'); document.body.classList.remove('sidebar-open');
  }
});
