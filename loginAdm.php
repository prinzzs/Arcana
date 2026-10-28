<?php
// Inicia a sessão
session_start();

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

// Verifica se a requisição é POST para processar o login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Captura e sanitiza os dados
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    if (empty($email) || empty($senha)) {
        $_SESSION['msg'] = "E-mail e senha são obrigatórios.";
        $_SESSION['msg_type'] = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['msg'] = "Formato de e-mail inválido.";
        $_SESSION['msg_type'] = "error";
    } else {
        // Verifica se o e-mail existe na tabela administradores
        $stmt = $conn->prepare("SELECT id, nome, senha FROM administradores WHERE email = ?");
        if (!$stmt) {
            $_SESSION['msg'] = "Erro interno ao preparar a consulta.";
            $_SESSION['msg_type'] = "error";
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 0) {
                $_SESSION['msg'] = "E-mail não encontrado.";
                $_SESSION['msg_type'] = "error";
            } else {
                // O e-mail foi encontrado, agora verifica a senha
                $stmt->bind_result($userId, $nome, $storedPassword);
                $stmt->fetch();

                if (password_verify($senha, $storedPassword)) {
                    // Login bem-sucedido
                    $_SESSION['admin_id'] = $userId;
                    $_SESSION['admin_nome'] = $nome;
                    $_SESSION['msg'] = "Login realizado com sucesso!";
                    $_SESSION['msg_type'] = "success";
                    header("Location: painelAdm.php"); // Redireciona para o painel do admin
                    exit;
                } else {
                    $_SESSION['msg'] = "Senha incorreta.";
                    $_SESSION['msg_type'] = "error";
                }
            }
            $stmt->close();
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
    <title>Arcana - Login Administrador</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/fontes.css">
</head>
<body class="page-auth">
    <div class="site-toolbar">
        <a class="brand" href="index.html" aria-label="Arcana — início">ARCANA</a>
        <div class="theme-controls" role="group" aria-label="Aparência">
            <button type="button" class="theme-toggle" id="lightModeToggle" aria-label="Ativar modo escuro" aria-pressed="false">Modo escuro</button>
            <button type="button" class="theme-system" data-theme-system aria-label="Usar tema do sistema" aria-pressed="true">Sistema</button>
        </div>
    </div>
    <div class="container">
        <h1 id="arca">ARCANA - ADM</h1>
        <p class="subtitulo">Login do administrador</p>

        <!-- Mensagens de erro ou sucesso -->
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="message <?php echo $_SESSION['msg_type']; ?>">
                <?php
                    echo $_SESSION['msg'];
                    unset($_SESSION['msg']);
                    unset($_SESSION['msg_type']);
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-field"><label for="campo-email">E-mail</label>
            <input autocomplete="email" id="campo-email" type="email" name="email" placeholder="Email:" required></div>
            <div class="form-field"><label for="campo-senha">Senha</label>
            <input autocomplete="current-password" id="campo-senha" type="password" name="senha" placeholder="Senha:" required></div>
            <button type="submit" class="entrar">Entrar</button>
        </form>

        <p>Não tem conta de administrador? <a href="cadastroAdm.php">Cadastre-se</a></p>
    </div>
</body>
</html>
