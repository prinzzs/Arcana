<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="imagex/png" href="img/logo.png">
    <title>Arcana - Inicio</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/fontes.css">
    <link rel="stylesheet" href="css/global.css">
</head>
<body>
    <div class="container">
        <!-- Seção Hero -->
        <section class="hero">
            <h1 class="title">Arcana: <span>rpg</span></h1>
            <a href="cadastro.php"><button class="button">jogar</button></a>       
            <div class="scroll-down">Desça para explorar</div>
        </section>

        <!-- Seção Linha do Tempo -->
        <section class="timeline">
            <h2 class="section-title">Níveis de Arcana</h2>
            <div class="timeline-cards">
                <div class="card">
                    <img src="img/submundo.jpg">
                    <h3>Nível I</h3>
                    <p>Submundo</p>
                </div>
                <div class="card">
                    <img src="img/ruas.jpg">
                    <h3>Nível II</h3>
                    <p>Nível Das Ruas</p>
                </div>
                <div class="card">
                    <img src="img/altissimo.jpg">
                    <h3>Nível III</h3>
                    <p>Altissimo</p>
                </div>
            </div>
        </section>

    <!-- Seção Características do Personagem -->
    
    <section class="character">
    <h2 class="section-title">A história...</h2>
        <div class="character-content">   
            <!-- Botão e conteúdo para Lore -->
            <button class="toggle-btn" onclick="toggleContent('loreContent')">Mostrar Lore</button>
            <div class="hidden-content" id="loreContent">
                <div class="text">
                    <h3>Lore</h3>
                    <p style="font-size: x-large;">Há 50 anos, Arcana sofreu um evento cataclísmico conhecido como <strong>O Blecaute</strong>. Durante um experimento envolvendo a fusão de dimensões,
                        a Spectra Dynamics abriu acidentalmente uma fenda paranormal. Essa anomalia liberou criaturas sobrenaturais, conhecidas como <strong>Umbra</strong>,
                        e deixou um rastro de energia paranormal que transformou para sempre o equilíbrio entre tecnologia e sobrenatural. Desde então,
                        as fronteiras entre o natural e o paranormal se tornaram quase inexistentes.</p>
                </div>
            </div>

            <!-- Botão e conteúdo para Níveis -->
            <button class="toggle-btn" onclick="toggleContent('niveisContent')">Ocultar Níveis</button>
            <div class="hidden-content active" id="niveisContent">
                <div class="text">
                    <h3>Níveis</h3>
                    <p style="font-size: x-large;">
                        <strong>Submundo: </strong> Nessa parte de Arcana tem várias redes de labirintos, túneis e esgotos abandonados, lar dos marginalizados e esquecidos pela sociedade.
                        Os habitantes enfrentam uma luta constante contra os perigos do ambiente hostil e a ameaça permanente das entidades Umbra, que se escondem nas sombras.
                        Lá é o polo da fenda. Apesar disso, grupos de rebeldes se organizam no Submundo, por ser um lugar onde as pessoas da elite se recusam a ir.
                        <br><br>
                        <strong>Nível das Ruas: </strong> é uma parte mais segura, marcado pela decadência de arranha-céus e bairros em ruínas, mercados clandestinos e gangues dominantes. 
                        Humanos, androides e cyborgs coexistem em meio à agitação, enquanto rumores de atividades paranormais surgem em alguns bairros populares.
                        As gangues desempenham um papel duplo: algumas são fantoches das corporações mais ricas com o fim de investigar a fenda e tentar entender a Umbra,
                        mas algumas resistem e tentam proteger os mais vulneráveis.
                        <br><br>
                        <strong>Altíssimos: </strong> A camada mais elevada da cidade, onde as elites e corporações vivem em luxo desmedido. Torres de vidro que atravessam as nuvens abrigam
                        os poderosos que governam Arcana por meio de megacorporações como a Spectra Dynamics, famosa por suas pesquisas em tecnologia paranormal e segredos perigosos.
                        Entretanto, nem todos no Altíssimo são leais à ordem vigente: dissidentes e agentes secretos operam nas sombras, buscando expor as megacorporações ou mesmo derrubá-las.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Função para alternar a visibilidade do conteúdo
        function toggleContent(sectionId) {
            const content = document.getElementById(sectionId);
            content.classList.toggle("active");

            // Altera o texto do botão conforme o estado da seção
            const button = document.querySelector(`[onclick="toggleContent('${sectionId}')"]`);
            if (content.classList.contains("active")) {
                button.innerText = sectionId === "loreContent" ? "Ocultar Lore" : "Ocultar Níveis";
            } else {
                button.innerText = sectionId === "loreContent" ? "Mostrar Lore" : "Mostrar Níveis";
            }
        }

        // Garante que ambos os conteúdos comecem ocultos ao carregar a página
        window.onload = function() {
            document.getElementById("loreContent").classList.remove("active");
            document.getElementById("niveisContent").classList.remove("active");
        }
    </script>



        <!-- Seção Wallpapers Animados -->
        <section class="wallpapers">
            <h2 class="section-title">No nosso site</h2>
            <div class="wallpapers-grid">
                <a href=""><div class="wallpaper-card">Criar Personagem</div></a>
                <a href="fichas.php"><div class="wallpaper-card">Ficha Das Entidades</div></a>
                <a href="dado.php"><div class="wallpaper-card">Rolagem De Dados</div></a>
                <a href="CadastroAdm.php"><div class="wallpaper-card">Painel de adm's</div></a>
            </div>
        </section>

        <!-- Rodapé -->
        <footer>
            <p>©2023 Wolves Club Studio. Todos os direitos reservados.</p>
        </footer>

        <div class="fab" id="lightModeToggle">
            <ion-icon name="moon-outline"></ion-icon>
        </div>
    </div>
    <script src="js/lightmode.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
