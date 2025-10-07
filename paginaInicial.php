<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="imagex/png" href="img/logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/paginaInicial.css">
    <link rel="stylesheet" href="css/global.css">
    <title>Arcana - Onde Tudo Começa</title>
</head>
<body>
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
        <div id="clip">
            <div id="leftTop" class="corner"></div>
            <div id="rightBottom" class="corner"></div>
            <div id="rightTop" class="corner"></div>
            <div id="leftBottom" class="corner"></div>
        </div>
        <span id="rightArrow" class="arrow"></span>
        <span id="leftArrow" class="arrow"></span>
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
            setInterval(showNextText, 3000); // Change text every 3 seconds

            // Show the start button after 10 seconds
            setTimeout(() => {
                startButton.style.display = 'block';
            }, 10000); // 10000ms = 10 seconds

            // Magic sparkle trail
            document.addEventListener('mousemove', (event) => {
                const sparkCount = Math.random() * 5 + 5; // Random number of sparks (5-10)

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
            document.querySelectorAll('button').forEach(button => {
                button.addEventListener('click', (event) => {
                    pageTransition.classList.remove('hidden');
                    setTimeout(() => {
                        window.location.href = 'paginaInicial2.php'; // Replace '#' with the target URL
                    }, 600); // Match the transition duration
                });
            });
        });
    </script>
    <script src="js/lightmode.js"></script>
</body>
</html>
