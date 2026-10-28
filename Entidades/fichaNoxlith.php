<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="color-scheme" content="light dark">
    <script src="../js/lightmode.js"></script>
    <link rel="stylesheet" href="../css/global.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="imagex/png" href="../img/logo.png">
    <title>Ficha - Noxlith</title>
    <link rel="stylesheet" href="../Entidades/fichaentidades.css">
    <link rel="stylesheet" href="../css/fontes.css">
</head>
<body class="page-entity">
    <div class="site-toolbar">
        <a class="brand" href="../index.php" aria-label="Arcana — início">ARCANA</a>
        <div class="theme-controls" role="group" aria-label="Aparência">
            <button type="button" class="theme-toggle" id="lightModeToggle" aria-label="Ativar modo escuro" aria-pressed="false">Modo escuro</button>
            <button type="button" class="theme-system" data-theme-system aria-label="Usar tema do sistema" aria-pressed="true">Sistema</button>
        </div>
    </div>
    <header>
        <h1>Arcana</h1>
    <nav aria-label="Navegação da ficha"><a href="../fichas.php">← Todas as entidades</a></nav>
    </header>

    <div class="banner">
        Para utilizar a ficha em jogo, é necessário ir para a página da campanha, criar um combate e adicionar a ficha ao combate.
    </div>

    <div class="content">
        <div class="title">
            <h2>Noxlith</h2>
            <p>VD: 90 | Criatura - Médio</p>
        </div>

        <div class="hp-bar">
            <button>&lt;</button>
            <span>90 / 90</span>
            <button>&gt;</button>
        </div>

        <div class="tabs">
            <button type="button" class="active" aria-pressed="true" aria-controls="status" onclick="showTab('status')">Status</button>
            <button type="button" aria-pressed="false" aria-controls="combate" onclick="showTab('combate')">Combate</button>
            <button type="button" aria-pressed="false" aria-controls="descricao" onclick="showTab('descricao')">Descrição</button>
        </div>

        <div id="status" class="section active">
            <h3>Status</h3>
            <div class="attributes">
                <div>AGI<br><strong>4</strong></div>
                <div>FOR<br><strong>5</strong></div>
                <div>INT<br><strong>3</strong></div>
                <div>PRE<br><strong>4</strong></div>
                <div>VIG<br><strong>4</strong></div>
            </div>
            <p>Defesa: 20</p>
            <p>Deslocamento: 15m / 10q</p>

            <h3 id="periciastexto">Perícias</h3>
            <div class="pericias">
                <table>
                    <thead>
                        <tr>
                            <th>Perícia</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Percepção</td>
                            <td>2d+12</td>
                        </tr>
                        <tr>
                            <td>Iniciativa</td>
                            <td>2d+8</td>
                        </tr>
                        <tr>
                            <td>Fortitude</td>
                            <td>2d+8</td>
                        </tr>
                        <tr>
                            <td>Reflexos</td>
                            <td>2d+9</td>
                        </tr>
                        <tr>
                            <td>Vontade</td>
                            <td>2d+10</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="abc">
                <h3>Sentidos</h3>
                <p>Detecta emoções e presença de seres vivos em um raio de 24m.</p>
            </div>

            <div class="abc">
                <h3>Imunidades</h3>
                <p>Imunidade ao Fogo, Medo e Mente.</p>
            </div>

            <div class="abc">
                <h3>Condições</h3>
                <p>Atordoamento e Paralisia</p>
            </div>

            <div class="abc">
                <h3>Resistências</h3>
                <p>Balístico: 20</p>
                <p>Impacto: 20</p>
                <p>Perfuração: 20</p>
            </div>

            <div class="abc">
                <h3>Vulnerabilidades</h3>
                <p><strong>Morte</strong></p>
            </div>
        </div>

        <div id="combate" class="section">
            <h3>Combate</h3>
            <h4>Ações</h4>
            <ul>
                <li>Atacar Corpo a Corpo: Forma Sólida</li>
                <li>Atacar à Distância: Estilhaço de Medo</li>
            </ul>
            <h4>Poderes</h4>
            <ul>
                <li>Forma Sólida: 2d+8 (Dano Corte ou Esmagamento)</li>
                <li>Estilhaço de Medo: 2d+7 (Dano Mental, Alcance 18m)</li>
            </ul>
        </div>

        <div id="descricao" class="section">
            <h3>Descrição</h3>
            <p>Noxlith é uma manifestação do horror primordial, sua forma sólida é uma massa viscosa e cinza, parecendo um óleo misturado com sangue coagulado. Seus olhos são dois poços de luz amarela, enquanto sua boca grotesca está cheia de dentes irregulares que se fundem à sua própria gosma. Ele se desloca pelas sombras e emite um cheiro de podridão.</p>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            const tabs = document.querySelectorAll('.section');
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });
            document.getElementById(tabName).classList.add('active');

            const buttons = document.querySelectorAll('.tabs button');
            buttons.forEach(button => {
                button.classList.remove('active');
                button.setAttribute('aria-pressed', 'false');
            });
            document.querySelector(`.tabs button[onclick="showTab('${tabName}')"]`).classList.add('active');
            document.querySelector(`[aria-controls="${tabName}"]`).setAttribute('aria-pressed', 'true');
        }
    </script>
</body>
</html>
