document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.getElementById('lightModeToggle');
  const body = document.body;

  // Aplica o dark mode se estiver ativado
  if (localStorage.getItem('lightMode') === 'enabled') {
    body.classList.add('light-mode');
  } else {
  }

  // Lida com o clique no botão
  toggleBtn.addEventListener('click', () => {
    body.classList.toggle('light-mode');

    if (body.classList.contains('light-mode')) {
      localStorage.setItem('lightMode', 'enabled');
    } else {
      localStorage.removeItem('lightMode');
    }
  });
});
