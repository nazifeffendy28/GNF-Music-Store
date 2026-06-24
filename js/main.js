// main.js — fitur sederhana: tombol ganti tema terang/gelap

document.addEventListener('DOMContentLoaded', function () {
  var tombol = document.getElementById('tombolTema');
  if (!tombol) return;

  function setIkon() {
    var tema = document.documentElement.getAttribute('data-bs-theme');
    tombol.innerHTML = tema === 'dark'
      ? '<i class="bi bi-sun"></i>'
      : '<i class="bi bi-moon-stars"></i>';
  }
  setIkon();

  tombol.addEventListener('click', function () {
    var tema = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-bs-theme', tema);
    localStorage.setItem('tema', tema);
    setIkon();
  });
});
