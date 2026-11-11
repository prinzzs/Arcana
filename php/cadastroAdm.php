<?php
declare(strict_types=1);
require_once __DIR__ . '/conexao.php';

$mensagem = '';
$tipoMensagem = 'error';
$totalAdministradores = (int) $conn->query('SELECT COUNT(*) AS total FROM administradores')->fetch_assoc()['total'];
$chaveConfigurada = getenv('ADMIN_SETUP_KEY') ?: '';
$cadastroInicial = $totalAdministradores === 0 && $chaveConfigurada !== '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campos = ['nome', 'cpf', 'rg', 'cep', 'rua', 'numero', 'complemento', 'bairro', 'cidade', 'uf', 'celular', 'email'];
    $dados = [];
    foreach ($campos as $campo) {
        $dados[$campo] = trim((string) ($_POST[$campo] ?? ''));
    }
    $senha = (string) ($_POST['senha'] ?? '');

    if (!$cadastroInicial) {
        $mensagem = 'O cadastro público de administradores está encerrado. Faça login.';
    } elseif (!hash_equals($chaveConfigurada, (string) ($_POST['setup_key'] ?? ''))) {
        $mensagem = 'Chave de configuração inválida.';
    } elseif (in_array('', array_diff_key($dados, ['complemento' => true]), true) || $senha === '') {
        $mensagem = 'Preencha todos os campos obrigatórios.';
    } elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'Informe um e-mail válido.';
    } else {
        try {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare(
                'INSERT INTO administradores (nome, cpf, rg, cep, rua, numero, complemento, bairro, cidade, uf, celular, email, senha) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->bind_param(
                'sssssssssssss',
                $dados['nome'],
                $dados['cpf'],
                $dados['rg'],
                $dados['cep'],
                $dados['rua'],
                $dados['numero'],
                $dados['complemento'],
                $dados['bairro'],
                $dados['cidade'],
                $dados['uf'],
                $dados['celular'],
                $dados['email'],
                $senhaHash
            );
            $stmt->execute();
            $stmt->close();
            $mensagem = 'Cadastro realizado com sucesso.';
            $tipoMensagem = 'success';
            $cadastroInicial = false;
        } catch (mysqli_sql_exception $exception) {
            if ((int) $exception->getCode() === 1062) {
                $mensagem = 'CPF ou e-mail já cadastrado.';
            } else {
                error_log($exception->getMessage());
                $mensagem = 'Não foi possível concluir o cadastro. Tente novamente.';
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
    <meta name="color-scheme" content="light dark">
    <script src="../js/lightmode.js"></script>
    <link rel="stylesheet" href="../css/global.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cadastro</title>
  <link rel="stylesheet" href="../css/cadastroadm.css" />
  <link rel="stylesheet" href="../css/fontes.css">
</head>
<body class="page-auth admin-registration">
    <div class="site-toolbar">
        <a class="brand" href="../index.html" aria-label="Arcana — início">ARCANA</a>
        <div class="theme-controls" role="group" aria-label="Aparência">
            <button type="button" class="theme-toggle" id="lightModeToggle" aria-label="Ativar modo escuro" aria-pressed="false">Modo escuro</button>
        </div>
    </div>
  <div class="container">
    <h1>Cadastro</h1>
    <p class="subtitulo">Preencha seus dados</p>

    <?php if ($mensagem !== ''): ?>
      <div class="message <?= htmlspecialchars($tipoMensagem, ENT_QUOTES, 'UTF-8') ?>">
        <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <?php if ($cadastroInicial): ?>
    <form action="" method="POST" id="formCadastro" onsubmit="return TestaCPF(document.getElementById('cpf').value)">

      <div class="form-field"><label for="setup-key">Chave de configuração</label>
            <input id="setup-key" type="password" name="setup_key" autocomplete="off" required /></div>
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
    <?php else: ?>
      <a href="loginAdm.php" class="link">Ir para o login de administrador</a>
    <?php endif; ?>
  </div>

  <script>
    function TestaCPF(strCPF) {
      let Soma = 0;
      let Resto;
      strCPF = strCPF.replace(/[^\d]+/g, '');

      if (strCPF.length !== 11 || /^(\d)\1+$/.test(strCPF)) {
        alert("CPF Inválido");
        return false;
      }

      for (let i = 1; i <= 9; i++)
        Soma += parseInt(strCPF.substring(i - 1, i)) * (11 - i);

      Resto = (Soma * 10) % 11;

      if ((Resto === 10) || (Resto === 11)) Resto = 0;
      if (Resto !== parseInt(strCPF.substring(9, 10))) {
        alert("CPF Inválido");
        return false;
      }

      Soma = 0;
      for (let i = 1; i <= 10; i++)
        Soma += parseInt(strCPF.substring(i - 1, i)) * (12 - i);

      Resto = (Soma * 10) % 11;

      if ((Resto === 10) || (Resto === 11)) Resto = 0;
      if (Resto !== parseInt(strCPF.substring(10, 11))) {
        alert("CPF Inválido");
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
