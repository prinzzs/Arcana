<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: loginAdm.php');
    exit;
}

require_once __DIR__ . '/conexao.php';
$_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    exit('ID inválido.');
}

$stmt = $conn->prepare('SELECT nome, email FROM usuarios WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$usuario) {
    http_response_code(404);
    exit('Usuário não encontrado.');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = (string) ($_POST['csrf_token'] ?? '');
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        http_response_code(403);
        exit('Solicitação inválida.');
    }

    $nome = trim((string) ($_POST['nome'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $senha = (string) ($_POST['senha'] ?? '');

    if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Informe um nome e um e-mail válidos.';
        $usuario['nome'] = $nome;
        $usuario['email'] = $email;
    } else {
        try {
            if ($senha !== '') {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $conn->prepare('UPDATE usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?');
                $stmt->bind_param('sssi', $nome, $email, $senhaHash, $id);
            } else {
                $stmt = $conn->prepare('UPDATE usuarios SET nome = ?, email = ? WHERE id = ?');
                $stmt->bind_param('ssi', $nome, $email, $id);
            }

            $stmt->execute();
            $stmt->close();
            header('Location: painelAdm.php');
            exit;
        } catch (mysqli_sql_exception $exception) {
            error_log($exception->getMessage());
            $message = (int) $exception->getCode() === 1062
                ? 'Esse e-mail já pertence a outro usuário.'
                : 'Não foi possível atualizar o usuário.';
            $usuario['nome'] = $nome;
            $usuario['email'] = $email;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="color-scheme" content="light dark">
    <script src="../js/lightmode.js"></script>
    <link rel="stylesheet" href="../css/global.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
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
        <h1>Editar Usuário</h1>
        <?php if ($message !== ''): ?>
            <div class="message error"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-field"><label for="campo-nome">Nome</label>
            <input id="campo-nome" type="text" name="nome" value="<?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>" required></div>
            <div class="form-field"><label for="campo-email">E-mail</label>
            <input autocomplete="email" id="campo-email" type="email" name="email" value="<?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>" required></div>
            <div class="form-field"><label for="campo-senha">Senha</label>
            <input autocomplete="new-password" id="campo-senha" type="password" name="senha" placeholder="Nova senha (deixe em branco para não alterar)"></div>
            <button type="submit" class="entrar">Salvar Alterações</button>
        </form>
        <a href="painelAdm.php" class="link">← Voltar</a>
    </div>
</body>
</html>
