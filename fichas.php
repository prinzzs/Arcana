<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcana - Fichas</title>
    <link rel="shortcut icon" type="imagex/png" href="img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/fichas.css">
    <link rel="stylesheet" href="css/fontes.css">
    <link rel="stylesheet" href="css/global.css">
</head>
<body>
    <header>
        <h1>ARCANA</h1>
        <nav>
            <a href="dado.php">Dados</a>
            <a href="#">Personagem</a>
            <a href="mecanica.php">Mecânicas</a>
            <a href="index.php">Voltar</a>
        </nav>
    </header>

    <div class="banner">
        Com a evolução do site e do RPG, iremos adicionar novas entidades.
    </div>

    <div class="content">
        <div class="filters">
            <div class="tabs">
                <button class="active" data-filter="all">Todos</button>
                <button data-filter="Conhecimento">Conhecimento</button>
                <button data-filter="Energia">Energia</button>
                <button data-filter="Morte">Morte</button>
                <button data-filter="Sangue">Sangue</button>
                <button data-filter="Medo">Medo</button>
                <button data-filter="Realidade">Realidade</button>
            </div>
            <input type="text" id="search" placeholder="Buscar">
        </div>

        <div class="card" data-category="Medo">
            <img src="img/Noxlith.jpg" alt="">
            <div class="info">
                <h3>Noxlith</h3>
                <p class="type">Criatura - Médio</p>
            </div>
           <button><a href="Entidades/fichaNoxlith.php"> Ficha </a></button>
        </div>

        <div class="card" data-category="Energia">
            <img src="img/serafins.jpg" alt="">
            <div class="info">
                <h3>Serafim Decaído</h3>
                <p class="type">Entidade - Grande</p>
            </div>
            <button><a href="Entidades/fichaSerafim.php"> Ficha </a></button>
        </div>

        <div class="card" data-category="Realidade">
            <div class="info">
                <h3>Ratos</h3>
                <p class="type">Animal (enxame) - médio</p>
            </div>
            <button><a href="Entidades/fichaRatos.php"> Ficha </a></button>
        </div>
    </div>

    <script>
        const filterButtons = document.querySelectorAll('.filters .tabs button');
        const cards = document.querySelectorAll('.card');
        const searchInput = document.getElementById('search');

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                const filter = button.dataset.filter;

                cards.forEach(card => {
                    if (filter === 'all' || card.dataset.category === filter) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        searchInput.addEventListener('input', () => {
            const searchText = searchInput.value.toLowerCase();

            cards.forEach(card => {
                const cardText = card.querySelector('h3').textContent.toLowerCase();

                if (cardText.includes(searchText)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
    <script src="js/lightmode.js"></script>
</body>
</html>
