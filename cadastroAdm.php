<?php
$servername = "localhost";
$username = "root";
$password = "usbw";
$dbname = "UserDB";

// Conectando ao servidor e ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

// Garantir que a coluna 'senha' exista na tabela administradores
$checkColumnQuery = "SHOW COLUMNS FROM administradores LIKE 'senha'";
$result = $conn->query($checkColumnQuery);
if ($result->num_rows == 0) {
    $alterTableQuery = "ALTER TABLE administradores ADD COLUMN senha VARCHAR(255) NOT NULL";
    if (!$conn->query($alterTableQuery)) {
        die("Erro ao adicionar a coluna 'senha': " . $conn->error);
    }
}

// Criar tabela se não existir
$createTableQueryAdmin = "CREATE TABLE IF NOT EXISTS administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(20) NOT NULL UNIQUE,
    rg VARCHAR(20) NOT NULL,
    cep VARCHAR(10) NOT NULL,
    rua VARCHAR(100) NOT NULL,
    numero VARCHAR(10) NOT NULL,
    complemento VARCHAR(50),
    bairro VARCHAR(50) NOT NULL,
    cidade VARCHAR(50) NOT NULL,
    uf VARCHAR(2) NOT NULL,
    celular VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
)";

if (!$conn->query($createTableQueryAdmin)) {
    die("Erro ao criar a tabela de administradores: " . $conn->error);
}

// Inserir dados se o formulário for enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $rg = $_POST['rg'];
    $cep = $_POST['cep'];
    $rua = $_POST['rua'];
    $numero = $_POST['numero'];
    $complemento = $_POST['complemento'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $uf = $_POST['uf'];
    $celular = $_POST['celular']; 
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Segurança

    if (!empty($cpf) && !empty($email) && !empty($celular)) {
        // Preparar e executar a query
        $stmtAdmin = $conn->prepare("INSERT INTO administradores (nome, cpf, rg, cep, rua, numero, complemento, bairro, cidade, uf, celular, email, senha) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtAdmin->bind_param("sssssssssssss", $nome, $cpf, $rg, $cep, $rua, $numero, $complemento, $bairro, $cidade, $uf, $celular, $email, $senha);

        if ($stmtAdmin->execute()) {
            echo "<script>alert('Cadastro realizado com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar: " . $stmtAdmin->error . "');</script>";
        }

        $stmtAdmin->close();
    } else {
        echo "<script>alert('Por favor, preencha todos os campos obrigatórios.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
    <meta name="color-scheme" content="light dark">
    <script src="js/lightmode.js"></script>
    <link rel="stylesheet" href="css/global.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cadastro</title>
  <link rel="stylesheet" href="css/cadastroadm.css" />
  <link rel="stylesheet" href="css/fontes.css">
</head>
<body class="page-auth admin-registration">
    <div class="site-toolbar">
        <a class="brand" href="index.php" aria-label="Arcana — início">ARCANA</a>
        <div class="theme-controls" role="group" aria-label="Aparência">
            <button type="button" class="theme-toggle" id="lightModeToggle" aria-label="Ativar modo escuro" aria-pressed="false">Modo escuro</button>
            <button type="button" class="theme-system" data-theme-system aria-label="Usar tema do sistema" aria-pressed="true">Sistema</button>
        </div>
    </div>
  <div class="container">
    <h1>Cadastro</h1>
    <p class="subtitulo">Preencha seus dados</p>

    <form action="" method="POST" id="formCadastro">

      <div class="form-field"><label for="campo-nome">Nome</label>
            <input id="campo-nome" type="text" name="nome" placeholder="Nome" required /></div>
      <div class="form-field"><label for="cpf">CPF</label>
            <input type="text" name="cpf" id="cpf" placeholder="CPF" required onblur="TestaCPF(this.value)" /></div>
      <div class="form-field"><label for="campo-rg">RG</label>
            <input id="campo-rg" type="text" name="rg" placeholder="RG" required /></div>
      <div class="form-field"><label for="cep">CEP</label>
            <input type="text" name="cep" id="cep" placeholder="CEP" required onblur="pesquisacep(this.value)" /></div>

      <div class="form-row">
        <div class="form-field field-street"><label for="rua">Rua</label>
            <input type="text" name="rua" id="rua" placeholder="Rua" required /></div>
        <div class="form-field field-short"><label for="campo-numero">Número</label>
            <input id="campo-numero" type="text" name="numero" placeholder="Nº" required /></div>
      </div>

      <div class="form-field"><label for="campo-complemento">Complemento</label>
            <input id="campo-complemento" type="text" name="complemento" placeholder="Complemento" /></div>

      <div class="form-row">
        <div class="form-field"><label for="bairro">Bairro</label>
            <input type="text" name="bairro" id="bairro" placeholder="Bairro" required /></div>
        <div class="form-field"><label for="cidade">Cidade</label>
            <input type="text" name="cidade" id="cidade" placeholder="Cidade" required /></div>
        <div class="form-field field-short"><label for="uf">UF</label>
            <input type="text" name="uf" id="uf" placeholder="UF" maxlength="2" required /></div>
      </div>

      <div class="form-field"><label for="celular">Celular</label>
            <input type="text" name="celular" id="celular" placeholder="Tel/Cel" required /></div>
      <div class="form-field"><label for="campo-email">E-mail</label>
            <input autocomplete="email" id="campo-email" type="email" name="email" placeholder="Email" required /></div>
      <div class="form-field"><label for="senha">Senha</label>
            <input autocomplete="new-password" type="password" name="senha" id="senha" placeholder="Senha" required /></div>

      <div class="checkbox-container">
        <input type="checkbox" id="ver_senha">
        <label for="ver_senha">Ver senha</label>
      </div>

      <a href="loginAdm.php" class="link">Já tenho uma conta</a>
      <button type="submit">Cadastrar</button>

    </form>
  </div>

  <script>
    function TestaCPF(strCPF) {
      var Soma;
      var Resto;
      Soma = 0;
      strCPF = strCPF.replace(/[^\d]+/g, '');

      if (strCPF.length !== 11 || /^(\d)\1+$/.test(strCPF)) {
        alert("CPF Inválido");
        event.preventDefault(); // Impede o envio do formulário
        return false;
      }

      for (i = 1; i <= 9; i++)
        Soma += parseInt(strCPF.substring(i - 1, i)) * (11 - i);

      Resto = (Soma * 10) % 11;

      if ((Resto === 10) || (Resto === 11)) Resto = 0;
      if (Resto !== parseInt(strCPF.substring(9, 10))) {
        alert("CPF Inválido");
        event.preventDefault(); // Impede o envio do formulário
        return false;
      }

      Soma = 0;
      for (i = 1; i <= 10; i++)
        Soma += parseInt(strCPF.substring(i - 1, i)) * (12 - i);

      Resto = (Soma * 10) % 11;

      if ((Resto === 10) || (Resto === 11)) Resto = 0;
      if (Resto !== parseInt(strCPF.substring(10, 11))) {
        alert("CPF Inválido");
        event.preventDefault(); // Impede o envio do formulário
        return false;
      }

      return true;
    }

    function limpa_formulário_cep() {
      document.getElementById('rua').value = "";
      document.getElementById('bairro').value = "";
      document.getElementById('cidade').value = "";
      document.getElementById('uf').value = "";
    }

    function meu_callback(conteudo) {
      if (!("erro" in conteudo)) {
        document.getElementById('rua').value = conteudo.logradouro;
        document.getElementById('bairro').value = conteudo.bairro;
        document.getElementById('cidade').value = conteudo.localidade;
        document.getElementById('uf').value = conteudo.uf;
      } else {
        limpa_formulário_cep();
        alert("CEP não encontrado.");
      }
    }

    function pesquisacep(valor) {
      var cep = valor.replace(/\D/g, '');

      if (cep != "") {
        var validacep = /^[0-9]{8}$/;

        if (validacep.test(cep)) {
          document.getElementById('rua').value = "...";
          document.getElementById('bairro').value = "...";
          document.getElementById('cidade').value = "...";
          document.getElementById('uf').value = "...";

          var script = document.createElement('script');
          script.src = 'https://viacep.com.br/ws/' + cep + '/json/?callback=meu_callback';
          document.body.appendChild(script);
        } else {
          limpa_formulário_cep();
          alert("Formato de CEP inválido.");
        }
      } else {
        limpa_formulário_cep();
      }
    }

    // Ver senha
    document.getElementById("ver_senha").addEventListener("change", function () {
      const senhaInput = document.getElementById("senha");
      senhaInput.type = this.checked ? "text" : "password";
    });
  </script>
</body>
</html>
