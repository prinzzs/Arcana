<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/conexao.php';
$userId = (int) $_SESSION['user_id'];
$stmt = $conn->prepare('SELECT email, nome FROM usuarios WHERE id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$usuario) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$message = '';
$_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = (string) ($_POST['csrf_token'] ?? '');
    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        http_response_code(403);
        exit('Solicitação inválida.');
    }

    if (isset($_POST['new_name'])) {
        $newName = trim((string) $_POST['new_name']);
        if ($newName === '' || strlen($newName) > 100) {
            $message = 'Informe um nome com até 100 caracteres.';
        } else {
            $stmt = $conn->prepare('UPDATE usuarios SET nome = ? WHERE id = ?');
            $stmt->bind_param('si', $newName, $userId);
            $stmt->execute();
            $stmt->close();
            $usuario['nome'] = $newName;
            $message = 'Nome alterado com sucesso!';
        }
    } elseif (isset($_POST['new_password']) || isset($_POST['confirm_password'])) {
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        if ($newPassword === '' || $confirmPassword === '') {
            $message = 'Preencha os dois campos de senha.';
        } elseif ($newPassword !== $confirmPassword) {
            $message = 'As senhas não coincidem!';
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
            $stmt->bind_param('si', $hashedPassword, $userId);
            $stmt->execute();
            $stmt->close();
            $message = 'Senha alterada com sucesso!';
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
    <link rel="shortcut icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../css/fontes.css">
    <link rel="stylesheet" href="../css/edit.css">
    <title>Arcana - Configurações</title>
</head>
<body class="page-settings">
    <div class="site-toolbar">
        <a class="brand" href="../index.html" aria-label="Arcana — início">ARCANA</a>
        <div class="theme-controls" role="group" aria-label="Aparência">
            <button type="button" class="theme-toggle" id="lightModeToggle" aria-label="Ativar modo escuro" aria-pressed="false">Modo escuro</button>
        </div>
    </div>

<div class="container" id="container">
    <div class="header">
        <h1>Página de Configurações</h1>
    </div>

    <?php if ($message !== ''): ?>
        <div class="message">
            <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    <?php endif; ?>

    <div class="settings-section">
        <h2>Alterar Senha</h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
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
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-field"><label for="campo-avatar">Imagem do avatar</label>
            <input id="campo-avatar" type="file" class="input-field" name="avatar" accept="image/*" required></div>
            <button class="btn" type="submit">Alterar Avatar</button>
        </form>
        <?php if (isset($_SESSION['user_avatar'])): ?>
            <img src="<?= htmlspecialchars((string) $_SESSION['user_avatar'], ENT_QUOTES, 'UTF-8') ?>" class="avatar-preview" alt="Avatar">
        <?php endif; ?>
    </div>

    <div class="settings-section">
        <h2>Alterar Nome do Jogador</h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-field"><label for="campo-new_name">Nome do jogador</label>
            <input id="campo-new_name" type="text" class="input-field" name="new_name" placeholder="Novo Nome do Jogador" value="<?= htmlspecialchars((string) $usuario['nome'], ENT_QUOTES, 'UTF-8') ?>" required></div>
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
