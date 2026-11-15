document.addEventListener('DOMContentLoaded', function () {
  var toggleBtn = document.getElementById('sidebarToggle');
  var sidebar = document.getElementById('appSidebar');
  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', function () {
      sidebar.classList.toggle('open');
    });
  }

  // Auto-dismiss alert setelah 4 detik
  document.querySelectorAll('.alert[data-autohide]').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity .4s ease';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 400);
    }, 4000);
  });
});
