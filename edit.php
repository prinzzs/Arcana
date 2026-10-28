<?php
session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redireciona para login se não estiver autenticado
    exit();
}

// Configurações do banco de dados
$servername = "localhost";
$username = "root";
$password = "usbw";
$dbname = "UserDB";

// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

// Recupera as informações do usuário
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT email, nome FROM usuarios WHERE id = ?");
if (!$stmt) {
    die("Erro na preparação da consulta: " . $conn->error);
}
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($email, $nome);
$stmt->fetch();
$stmt->close();

// Processa as alterações
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Alterar nome do usuário
    if (!empty($_POST['new_name'])) {
        $newName = htmlspecialchars($_POST['new_name']);
        $stmt = $conn->prepare("UPDATE usuarios SET nome = ? WHERE id = ?");
        $stmt->bind_param("si", $newName, $userId);
        $stmt->execute();
        $stmt->close();
        $message = "Nome alterado com sucesso!";
    }

    // Alterar senha
    if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword === $confirmPassword) {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $userId);
            $stmt->execute();
            $stmt->close();
            $message = "Senha alterada com sucesso!";
        } else {
            $message = "As senhas não coincidem!";
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
    <script src="js/lightmode.js"></script>
    <link rel="stylesheet" href="css/global.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="img/logo.png">
    <link rel="stylesheet" href="css/fontes.css">
    <link rel="stylesheet" href="css/edit.css">
    <title>Arcana - Configurações</title>
</head>
<body class="page-settings">
    <div class="site-toolbar">
        <a class="brand" href="index.php" aria-label="Arcana — início">ARCANA</a>
        <div class="theme-controls" role="group" aria-label="Aparência">
            <button type="button" class="theme-toggle" id="lightModeToggle" aria-label="Ativar modo escuro" aria-pressed="false">Modo escuro</button>
            <button type="button" class="theme-system" data-theme-system aria-label="Usar tema do sistema" aria-pressed="true">Sistema</button>
        </div>
    </div>

<div class="container" id="container">
    <div class="header">
        <h1>Página de Configurações</h1>
    </div>

    <?php if (isset($message)): ?>
        <div class="message">
            <p><?= $message ?></p>
        </div>
    <?php endif; ?>

    <div class="settings-section">
        <h2>Alterar Senha</h2>
        <form method="POST">
            <div class="form-field"><label for="campo-new_password">Nova senha</label>
            <input autocomplete="new-password" id="campo-new_password" type="password" class="input-field" name="new_password" placeholder="Nova Senha" required></div>
            <div class="form-field"><label for="campo-confirm_password">Confirmar senha</label>
            <input autocomplete="new-password" id="campo-confirm_password" type="password" class="input-field" name="confirm_password" placeholder="Confirmar Senha" required></div>
            <button class="btn" type="submit">Alterar Senha</button>
        </form>
    </div>

    <div class="settings-section">
        <h2>Alterar Avatar</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-field"><label for="campo-avatar">Imagem do avatar</label>
            <input id="campo-avatar" type="file" class="input-field" name="avatar" accept="image/*" required></div>
            <button class="btn" type="submit">Alterar Avatar</button>
        </form>
        <?php if (isset($_SESSION['user_avatar'])): ?>
            <img src="<?= $_SESSION['user_avatar'] ?>" class="avatar-preview" alt="Avatar">
        <?php endif; ?>
    </div>

    <div class="settings-section">
        <h2>Alterar Nome do Jogador</h2>
        <form method="POST">
            <div class="form-field"><label for="campo-new_name">Nome do jogador</label>
            <input id="campo-new_name" type="text" class="input-field" name="new_name" placeholder="Novo Nome do Jogador" value="<?= $nome ?>" required></div>
            <button class="btn" type="submit">Alterar Nome</button>
        </form>
    </div>

    <div class="settings-section">
        <h2>Suporte</h2>
        <ul class="support">
            <li><a href="mailto:foxescorporation@gmail.com">Enviar e-mail para Suporte</a></li>
        </ul>
    </div>

    <div class="settings-section">
        <h2>FAQ</h2>
        <ul class="faq">
            <li><a href="#faq1">Como mudar minha senha?</a></li>
            <li><a href="#faq2">Como alterar meu avatar?</a></li>
            <li><a href="#faq3">Onde encontrar ajuda?</a></li>
        </ul>
    </div>
</div>
</body>
</html>
