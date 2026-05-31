const navToggle = document.getElementById('navToggle');

if (navToggle) {
  navToggle.addEventListener('click', function () {

    const links = document.getElementById('navLinks');

    if (links) {
      links.classList.toggle('open');

      const icon = this.querySelector('i');

      if (icon) {
        icon.className = links.classList.contains('open')
          ? 'bi bi-x-lg'
          : 'bi bi-list';
      }
    }

  });
}