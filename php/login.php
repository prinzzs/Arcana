<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $senha = (string) ($_POST['senha'] ?? '');

    if ($email === '' || $senha === '') {
        $_SESSION['msg'] = 'E-mail e senha são obrigatórios.';
        $_SESSION['msg_type'] = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['msg'] = 'Formato de e-mail inválido.';
        $_SESSION['msg_type'] = 'error';
    } else {
        try {
            $stmt = $conn->prepare('SELECT id, senha FROM usuarios WHERE email = ?');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 0) {
                $_SESSION['msg'] = 'E-mail ou senha incorretos.';
                $_SESSION['msg_type'] = 'error';
            } else {
                $stmt->bind_result($userId, $storedPassword);
                $stmt->fetch();
                if (password_verify($senha, $storedPassword)) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = (int) $userId;
                    $_SESSION['msg'] = 'Login realizado com sucesso!';
                    $_SESSION['msg_type'] = 'success';
                    $stmt->close();
                    header('Location: edit.php');
                    exit;
                }
                $_SESSION['msg'] = 'E-mail ou senha incorretos.';
                $_SESSION['msg_type'] = 'error';
            }
            $stmt->close();
        } catch (mysqli_sql_exception $exception) {
            error_log($exception->getMessage());
            $_SESSION['msg'] = 'Não foi possível realizar o login. Tente novamente.';
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
    <title>Arcana - Login</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/fontes.css">
</head>
<body class="page-auth">
    <div class="site-toolbar">
        <a class="brand" href="../index.html" aria-label="Arcana — início">ARCANA</a>
        <div class="theme-controls" role="group" aria-label="Aparência">
            <button type="button" class="theme-toggle" id="lightModeToggle" aria-label="Ativar modo escuro" aria-pressed="false">Modo escuro</button>
        </div>
    </div>
    <div class="container">
        <h1 id="arca">ARCANA</h1>
        <p class="subtitulo">Faça seu login</p>

        <!-- Exibe mensagens de erro ou sucesso -->
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="message <?= htmlspecialchars((string) ($_SESSION['msg_type'] ?? 'error'), ENT_QUOTES, 'UTF-8') ?>">
                <?php
                    echo htmlspecialchars((string) $_SESSION['msg'], ENT_QUOTES, 'UTF-8');
                    unset($_SESSION['msg']);
                    unset($_SESSION['msg_type']);
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-field"><label for="email">E-mail</label>
            <input autocomplete="email" type="email" placeholder="Email:" id="email" name="email" required></div>
            <div class="form-field"><label for="senha">Senha</label>
            <input autocomplete="current-password" type="password" placeholder="Senha:" id="senha" name="senha" required></div>

            <button type="submit" class="entrar">Entrar</button>
        </form>

        <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se aqui</a></p>
    </div>
</body>
</html>
