<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="color-scheme" content="light dark">
    <script src="js/lightmode.js"></script>
    <link rel="stylesheet" href="css/global.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="imagex/png" href="img/logo.png">
    <title>Arcana - Mecânicas</title>
    <link rel="stylesheet" href="css/mecanica.css">
    <link rel="stylesheet" href="css/fontes.css">
</head>
<body class="page-rules">
    <div class="site-toolbar">
        <a class="brand" href="index.php" aria-label="Arcana — início">ARCANA</a>
        <div class="theme-controls" role="group" aria-label="Aparência">
            <button type="button" class="theme-toggle" id="lightModeToggle" aria-label="Ativar modo escuro" aria-pressed="false">Modo escuro</button>
            <button type="button" class="theme-system" data-theme-system aria-label="Usar tema do sistema" aria-pressed="true">Sistema</button>
        </div>
    </div>
    <header>
        <h1>ARCANA</h1>
        <nav>
            <a href="dado.php">Dados</a>
            <a href="#">Personagem</a>
            <a href="mecanica.php" aria-current="page">Mecânicas</a>
            <a href="index.php">Voltar</a>
        </nav>
    </header>

    <main>
        <section id="criacao-personagem">
            <h2>Criação de Personagem</h2>
            <p>A personalização do personagem é essencial para definir suas habilidades e função dentro do jogo.</p>

            <h3>Raças</h3>
            <ul>
                <li><strong>Humanos:</strong> Adaptáveis e versáteis, equilibram Força, Destreza e Inteligência.</li>
                <li><strong>Cyborgs:</strong> Meio humanos, meio máquinas. Ganham bônus em Destreza e Força, mas são vulneráveis à manipulação mental.</li>
                <li><strong>Umbra:</strong> Seres sobrenaturais da fenda. Possuem poderes paranormais, mas são fracos em interações sociais e vulneráveis a certas magias.</li>
                <li><strong>Androides:</strong> Criaturas artificiais criadas por corporações. Destacam-se em Inteligência e Destreza, mas têm pouco Carisma e dificuldades emocionais.</li>
            </ul>

            <h3>Classes</h3>
            <p>Em ARCANA, existem três classes principais:</p>
            <ul>
                <li><strong>Combatente:</strong> Especialista em armas e combate direto.</li>
                <li><strong>Especialista:</strong> Mestre em conhecimento, táticas e estratégia.</li>
                <li><strong>Ocultista:</strong> Pesquisador dos mistérios do Outro Lado e dos Elementos.</li>
            </ul>
        </section>

        <section id="atributos">
            <h2>Atributos</h2>
            <p>Os atributos são a base do personagem, determinando sua capacidade em diferentes desafios:</p>
            <ul>
                <li><strong>Força:</strong> Ataques físicos e resistência.</li>
                <li><strong>Agilidade:</strong> Velocidade, destreza e esquiva.</li>
                <li><strong>Percepção:</strong> Capacidade de detectar ameaças e eventos sobrenaturais.</li>
                <li><strong>Vontade:</strong> Resistência mental e imunidade a manipulações.</li>
                <li><strong>Presença:</strong> Influência social e liderança.</li>
                <li><strong>Vigor:</strong> Energia vital e resistência a danos e doenças.</li>
            </ul>
        </section>

        <section id="combate">
            <h2>Combate</h2>
            <p>O combate é baseado em turnos. Durante sua vez, você pode:</p>
            <ul>
                <li><strong>Atacar:</strong> Usar armas ou habilidades ofensivas.</li>
                <li><strong>Defender:</strong> Esquivar-se ou criar barreiras.</li>
                <li><strong>Usar Magia:</strong> Lançar feitiços ou invocar entidades, se tiver habilidades mágicas.</li>
                <li><strong>Interagir:</strong> Hackear, negociar ou manipular elementos do ambiente.</li>
            </ul>
        </section>

        <section id="interacoes">
            <h2>Interações Sociais</h2>
            <p>As interações sociais têm grande impacto na narrativa e incluem:</p>
            <ul>
                <li><strong>Persuasão:</strong> Convencer NPCs a colaborar.</li>
                <li><strong>Intimidação:</strong> Forçar ou ameaçar NPCs.</li>
                <li><strong>Enganação:</strong> Criar mentiras, disfarces e manipulações.</li>
            </ul>
            <p>O Carisma do personagem influencia a eficácia dessas interações.</p>
        </section>
    </main>

    <footer>
        <p>© 2025 RPG Adventure</p>
    </footer>
</body>
</html>
