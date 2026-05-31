
/* ========================
   HERO SLIDER
======================== */
document.addEventListener('DOMContentLoaded', function () {
  let currentSlide = 0;
  const slides = document.querySelectorAll('.hero-slide');
  const dots = document.querySelectorAll('.slider-dot');
  let sliderTimer;

  function showSlide(idx) {
    slides.forEach(s => s.classList.remove('active'));
    dots.forEach(d => d.classList.remove('active'));
    currentSlide = (idx + slides.length) % slides.length;
    slides[currentSlide].classList.add('active');
    if (dots[currentSlide]) dots[currentSlide].classList.add('active');
  }

  function goToSlide(idx) {
    showSlide(idx);
    resetTimer();
  }

  function resetTimer() {
    clearInterval(sliderTimer);
    sliderTimer = setInterval(() => showSlide(currentSlide + 1), 5000);
  }
  document.getElementById('nextSlide').addEventListener('click', () => {
    showSlide(currentSlide + 1);
    resetTimer();
  });
  document.getElementById('prevSlide').addEventListener('click', () => {
    showSlide(currentSlide - 1);
    resetTimer();
  });
  resetTimer();
})