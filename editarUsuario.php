<?php
session_start();
include('conexao.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID inválido.";
    exit;
}

$id = intval($_GET['id']);

// Se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $senha = trim($_POST['senha']);

    if (empty($nome) || empty($email)) {
        echo "Nome e email são obrigatórios.";
    } else {
        // Atualiza os dados
        if (!empty($senha)) {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?");
            $stmt->bind_param("sssi", $nome, $email, $senhaHash, $id);
        } else {
            $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nome, $email, $id);
        }

        if ($stmt->execute()) {
            header("Location: painelAdm.php");
            exit;
        } else {
            echo "Erro ao atualizar usuário: " . $stmt->error;
        }

        $stmt->close();
    }
} else {
    // Carrega os dados atuais do usuário
    $stmt = $conn->prepare("SELECT nome, email FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if (!$usuario) {
        echo "Usuário não encontrado.";
        exit;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="css/fontes.css">
    <link rel="stylesheet" href="css/cadastro.css"> <!-- Você pode reutilizar o estilo do cadastro -->
    <link rel="stylesheet" href="css/global.css">
</head>
<body>
    <div class="container">
        <h1>Editar Usuário</h1>
        <form method="POST">
            <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
            <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
            <input type="password" name="senha" placeholder="Nova senha (deixe em branco para não alterar)">
            <button type="submit" class="entrar">Salvar Alterações</button>
        </form>
        <a href="painelAdm.php" class="link">← Voltar</a>
    </div>
    <script src="js/lightmode.js"></script>
</body>
</html>
