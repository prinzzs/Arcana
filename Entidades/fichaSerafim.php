<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha - Serafim Decaído</title>
    <link rel="shortcut icon" type="imagex/png" href="img/logo.png">
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
            <h2>Serafim Decaído</h2>
            <p>VD: 120 | Entidade - Grande</p>
        </div>

        <div class="hp-bar">
            <button>&lt;</button>
            <span>120 / 120</span>
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
                <div>AGI<br><strong>5</strong></div>
                <div>FOR<br><strong>6</strong></div>
                <div>INT<br><strong>3</strong></div>
                <div>PRE<br><strong>3</strong></div>
                <div>VIG<br><strong>5</strong></div>
            </div>
            <p>Defesa: 22 </p>
            <p>Deslocamento: 12m / 8q; voo 24m / 16q</p>

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
                            <td>2d+10</td>
                        </tr>
                        <tr>
                            <td>Iniciativa</td>
                            <td>2d+9</td>
                        </tr>
                        <tr>
                            <td>Fortitude</td>
                            <td>2d+10</td>
                        </tr>
                        <tr>
                            <td>Reflexos</td>
                            <td>2d+11</td>
                        </tr>
                        <tr>
                            <td>Vontade</td>
                            <td>2d+9</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="abc">
                <h3>Sentidos</h3>
                <p>Consciência sobrenatural em um raio de 30m; imune a cegueira e invisibilidade.</p>
            </div>

            <div class="abc">
                <h3>Imunidades</h3>
                <p>Imunidade a cegueira e invisibilidade.</p>
            </div>

            <div class="abc">
                <h3>Dano</h3>
                <p>Tipo: Luz e Corte</p>
            </div>

            <div class="abc">
                <h3>Condições</h3>
                <p>Atordoamento e Paralisia</p>
            </div>

            <div class="">
                <h3>Resistências</h3>
                <p>Balístico: 22</p>
                <p>Impacto: 22</p>
                <p>Perfuração: 22</p>
            </div>

            <div class="">
                <h3>Vulnerabilidades</h3>
                <p><strong>???</strong></p>
            </div>
        </div>

        <div id="combate" class="section">
            <h3>Combate</h3>
            <h4>Ações</h4>
            <ul>
                <li>Atacar</li>
                <li>Defender</li>
                <li>Movimentar</li>
            </ul>
            <h4>Poderes</h4>
            <ul>
                <li>Lâminas de Luz</li>
                <li>Rajada Ventos Divinos</li>
            </ul>
        </div>

        <div id="descricao" class="section">
            <h3>Descrição</h3>
            <p>Suas asas eram o primeiro sinal de sua decadência, o que antes tinha belas plumas radiantes
                agora são escombros carbonizados. Muitas das penas caíram, deixando apenas ossos quebrados
                e carne necrosada, retorcida em ângulos impossíveis. Chamas negras e cinzas flutuam 
                ao redor dessas asas. Seus olhos, veem mais do que qualquer visão mortal eles enxergam as
                almas, despindo as intenções e medos de quem cruzar seu caminho. <br/>

                Seus rostos possuem uma beleza angelical perfeita, mas estão sempre escondidos por máscaras
                sombrias de metal escurecido, adornadas com fissuras que deixam escapar uma luz pálida
                e perturbadora. Seus corpos, apesar de cobertos por longas vestes que arrastam no 
                chão como um manto de luto, ainda mantêm traços de sua antiga glória celestial. A pele
                é impecável, quase translúcida, mas veias negras se enraízam lentamente, como as garras
                da corrupção que tentam se infiltrar em cada parte de sua existência, prometendo consumir 
                o que resta de sua divindade.
                </p>
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
