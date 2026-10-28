<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="color-scheme" content="light dark">
    <script src="js/lightmode.js"></script>
    <link rel="stylesheet" href="css/global.css">
    <link rel="shortcut icon" type="imagex/png" href="img/logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/paginaInicial.css">
    <title>Arcana - Onde Tudo Começa</title>
    <link rel="stylesheet" href="css/fontes.css">
</head>
<body class="page-story">
    <div class="site-toolbar">
        <a class="brand" href="index.php" aria-label="Arcana — início">ARCANA</a>
        <div class="theme-controls" role="group" aria-label="Aparência">
            <button type="button" class="theme-toggle" id="lightModeToggle" aria-label="Ativar modo escuro" aria-pressed="false">Modo escuro</button>
            <button type="button" class="theme-system" data-theme-system aria-label="Usar tema do sistema" aria-pressed="true">Sistema</button>
        </div>
    </div>
    <div class="page-transition hidden" id="page-transition"></div>



    <div class="text-container" aria-live="polite" aria-atomic="true">
        <div class="text">A cidade, sempre imersa em tensão, segue seu curso habitual.</div>
        <div class="text">Mas então, um som ecoa, cortando a tranquilidade.</div>
        <div class="text">A sirene... um aviso que ninguém quer ouvir.</div>
        <div class="text">O que vem depois é incerto, mas todos sabem o que está por vir.</div>
        <div class="text">O caos está se aproximando, e a cidade jamais será a mesma.</div>
    </div>


    <button id="start-button">
        JOGAR
    </button>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const texts = document.querySelectorAll('.text');
            const startButton = document.getElementById('start-button');
            const pageTransition = document.getElementById('page-transition');
            let currentIndex = 0;

            // Function to show the next text
            function showNextText() {
                texts.forEach((text, index) => {
                    text.classList.toggle('visible', index === currentIndex);
                });
                currentIndex = (currentIndex + 1) % texts.length;
            }

            // Initially show the first text
            showNextText();
            if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) setInterval(showNextText, 5000); // Alterna a narrativa a cada cinco segundos.


            // Magic sparkle trail
            document.addEventListener('mousemove', (event) => {
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                const sparkCount = 2;

                for (let i = 0; i < sparkCount; i++) {
                    const spark = document.createElement('div');
                    spark.className = 'spark';

                    const angle = Math.random() * 2 * Math.PI;
                    spark.style.setProperty('--dx', Math.cos(angle));
                    spark.style.setProperty('--dy', Math.sin(angle));

                    spark.style.left = `${event.clientX}px`;
                    spark.style.top = `${event.clientY}px`;

                    document.body.appendChild(spark);

                    setTimeout(() => {
                        spark.remove();
                    }, 1500);
                }
            });

            // Add transition effect when navigating away
            document.querySelectorAll('#start-button').forEach(button => {
                button.addEventListener('click', (event) => {
                    pageTransition.classList.remove('hidden');
                    setTimeout(() => {
                        window.location.href = 'paginaInicial2.php'; // Replace '#' with the target URL
                    }, 600); // Match the transition duration
                });
            });
        });
    </script>
</body>
</html>
