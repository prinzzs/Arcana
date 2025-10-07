<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" type="imagex/png" href="img/logo.png">
    <title>Arcana - Rolagem de Dados</title>
    <link rel="stylesheet" href="css/dados.css">
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

    <div class="abcde">
    <div class="botao-container">
        <button class="botao" onclick="rolarDados(1, 20)">Rolar d20</button>
        <button class="botao" onclick="rolarDados(1, 2)">Rolar d2</button>
        <button class="botao" onclick="rolarDados(1, 6)">Rolar d6</button>
        <button class="botao" onclick="rolarDados(2, 2)">2x d2</button>
        <button class="botao" onclick="rolarDados(2, 20)">2x d20</button>
        <button class="botao" onclick="rolarDados(2, 6)">2x d6</button>
        <button class="botao" onclick="rolarDados(3, 2)">3x d2</button>
        <button class="botao" onclick="rolarDados(3, 20)">3x d20</button>
        <button class="botao" onclick="rolarDados(3, 6)">3x d6</button>
    </div>


    <div id="resultado" class="resultado"></div>
</div>
    <script>
        function rolarDados(quantidade, faces) {
            let resultados = [];
            let soma = 0;

            for (let i = 0; i < quantidade; i++) {
                let resultado = Math.floor(Math.random() * faces) + 1;
                resultados.push(resultado);
                soma += resultado;
            }

            const resultadoElemento = document.getElementById('resultado');
            resultadoElemento.innerHTML = 
                `Resultado dos ${quantidade} dados de ${faces} faces:<br>
                <span>${resultados.join(', ')}</span><br>
                Soma: <span>${soma}</span>`;

            resultadoElemento.classList.add('mostrar');

            resultadoElemento.scrollIntoView({ behavior: 'smooth' });
        }

        function voltar() {
            window.history.back();
        }
    </script>
    <script src="js/lightmode.js"></script>
</body>
</html>
