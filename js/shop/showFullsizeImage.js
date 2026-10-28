const showFullsizeImage = () => {
  const fullsizeImage = document.getElementById('fullsize-image-container');
  fullsizeImage.classList.toggle('show');
};

window.onload = document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    const fullsizeImage = document.getElementById('fullsize-image-container');
    if (!fullsizeImage.classList.contains('show')) return;
    fullsizeImage.classList.toggle('show');
  }
});
