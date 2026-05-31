
/* ========================
   MULTIMEDIA FILTER
======================== */
function filterMedia(btn, type) {
  document.querySelectorAll('.media-tab').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('[data-type]').forEach(el => {
    el.style.display = (type === 'all' || el.dataset.type === type) ? '' : 'none';
  });
}
