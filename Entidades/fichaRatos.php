<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="imagex/png" href="img/logo.png">
    <title>Ficha - Ratos</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Entidades/fichaentidades.css">
    <link rel="stylesheet" href="../css/fontes.css">
</head>
<body>
    <header>
        <h1>Arcana</h1>
    </header>

    <div class="banner">
        Para utilizar a ficha em jogo, é necessário ir para a página da campanha, criar um combate e adicionar a ficha ao combate.
    </div>

    <div class="content">
        <div class="title">
            <h2>Ratos</h2>
            <p>VD: 10 | Animal (enxame) - médio</p>
        </div>

        <div class="hp-bar">
            <button>&lt;</button>
            <span>15 / 15</span>
            <button>&gt;</button>
        </div>

        <div class="tabs">
            <button class="active" onclick="showTab('status')">Status</button>
            <button onclick="showTab('combate')">Combate</button>
            <button onclick="showTab('descricao')">Descrição</button>
        </div>

        <div id="status" class="section active">
            <h3>Status</h3>
            <div class="attributes">
                <div>AGI<br><strong>1</strong></div>
                <div>FOR<br><strong>0</strong></div>
                <div>INT<br><strong>0</strong></div>
                <div>PRE<br><strong>1</strong></div>
                <div>VIG<br><strong>1</strong></div>
            </div>
            <p>Defesa: 13</p>
            <p>Deslocamento: 9m / 6q (Escalar/nadar 6m / 4q)</p>

            <h3>Perícias</h3>
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
                            <td>1d20+5</td>
                        </tr>
                        <tr>
                            <td>Iniciativa</td>
                            <td>1d20+5</td>
                        </tr>
                        <tr>
                            <td>Fortitude</td>
                            <td>1d20+5</td>
                        </tr>
                        <tr>
                            <td>Reflexos</td>
                            <td>1d20+5</td>
                        </tr>
                        <tr>
                            <td>Vontade</td>
                            <td>1d20</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="abc">
                <h3>Sentidos</h3>
                <p>FARO, VISÃO NA PENUMBRA.</p>
            </div>
        </div>

        <div id="combate" class="section">
            <h3>Combate</h3>
            <h4>Poderes</h4>
            <ul>
                <li>Enxame (Dano de Perfuração)</li>
                <li>Doença (Febre Hemorrágica)</li>
            </ul>
        </div>

        <div id="descricao" class="section">
            <h3>Descrição</h3>
            <p>Ratos podem ser encontrados em quase todos os lugares habitados por
            humanos. Normalmente tímidos, podem se unir em perigosos enxames
            quando movidos por fome intensa ou por misteriosas energias
            paranormais.</p>
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
            });
            document.querySelector(`.tabs button[onclick="showTab('${tabName}')"]`).classList.add('active');
        }
    </script>
</body>
</html>
