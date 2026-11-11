<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim((string) ($_POST['nome'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $senha = (string) ($_POST['senha'] ?? '');

    if ($nome === '' || $email === '' || $senha === '') {
        $_SESSION['msg'] = 'Todos os campos são obrigatórios.';
        $_SESSION['msg_type'] = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['msg'] = 'Formato de e-mail inválido.';
        $_SESSION['msg_type'] = 'error';
    } else {
        try {
            $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email = ?');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            $emailJaCadastrado = $stmt->num_rows > 0;
            $stmt->close();

            if ($emailJaCadastrado) {
                $_SESSION['msg'] = 'Este e-mail já está cadastrado.';
                $_SESSION['msg_type'] = 'error';
            } else {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $conn->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)');
                $stmt->bind_param('sss', $nome, $email, $senhaHash);
                $stmt->execute();
                $stmt->close();
                $_SESSION['msg'] = 'Cadastro realizado com sucesso!';
                $_SESSION['msg_type'] = 'success';
            }
        } catch (mysqli_sql_exception $exception) {
            error_log($exception->getMessage());
            $_SESSION['msg'] = 'Não foi possível concluir o cadastro. Tente novamente.';
            $_SESSION['msg_type'] = 'error';
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="color-scheme" content="light dark">
    <script src="../js/lightmode.js"></script>
    <link rel="stylesheet" href="../css/global.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcana - Cadastro</title>
    <link rel="shortcut icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../css/fontes.css">
    <link rel="stylesheet" href="../css/cadastro.css">
</head>
<body class="page-auth">
    <div class="site-toolbar">
        <a class="brand" href="../index.html" aria-label="Arcana — início">ARCANA</a>
        <div class="theme-controls" role="group" aria-label="Aparência">
            <button type="button" class="theme-toggle" id="lightModeToggle" aria-label="Ativar modo escuro" aria-pressed="false">Modo escuro</button>
        </div>
    </div>
    <div class="container">
        <h1>ARCANA</h1>
        <p class="subtitulo">Faça seu cadastro</p>

        <!-- Exibe a mensagem de erro/sucesso -->
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="message <?= htmlspecialchars((string) ($_SESSION['msg_type'] ?? 'error'), ENT_QUOTES, 'UTF-8') ?>">
                <?php 
                echo htmlspecialchars((string) $_SESSION['msg'], ENT_QUOTES, 'UTF-8');
                unset($_SESSION['msg']); // Limpa a mensagem após exibi-la
                unset($_SESSION['msg_type']); // Limpa o tipo da mensagem
                ?>
            </div>
        <?php endif; ?>

        <form id="formCadastro" method="POST">
            <div class="form-field"><label for="nome">Nome</label>
            <input type="text" placeholder="Nome de usuário" id="nome" name="nome" required></div>
            <div class="form-field"><label for="email">E-mail</label>
            <input autocomplete="email" type="email" placeholder="Email:" id="email" name="email" required></div>
            <div class="form-field"><label for="senha">Senha</label>
            <input autocomplete="new-password" type="password" placeholder="Senha:" id="senha" name="senha" required></div>

            <div class="termos-container">
                <input type="checkbox" id="termos" required>
                <label for="termos">Li e aceito os Termos e Condições</label>
            </div>
            <button type="submit" class="entrar">Cadastre-se</button>
        </form>

        <div class="social-container">
            <p>Entre com:</p>
            <button onclick="initGoogleLogin()" class="entre abcd">
                Google
            </button>
            <button onclick="loginWithApple()" class="entre abcd">
                Apple
            </button>
        </div>

        <a href="login.php" class="link">Já tenho uma conta</a>
    </div>

    <script src="../js/cadastro.js"></script>
</body>
</html>
