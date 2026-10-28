<?php
// Inicia a sessão
session_start();

// Configurações do banco de dados
$servername = "localhost";
$username = "root";
$password = "usbw";
$dbname = "UserDB";

// Conectar ao banco de dados com tratamento de erro
try {
    $conn = new mysqli($servername, $username, $password);
    if ($conn->connect_error) {
        throw new Exception("Erro ao conectar ao banco de dados: " . $conn->connect_error);
    }

    // Verifica se o banco de dados existe e cria se necessário
    $dbCheckQuery = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'";
    $result = $conn->query($dbCheckQuery);

    if ($result->num_rows == 0) {
        $createDbQuery = "CREATE DATABASE $dbname";
        if (!$conn->query($createDbQuery)) {
            throw new Exception("Erro ao criar banco de dados: " . $conn->error);
        }
        $_SESSION['msg'] = "Banco de dados '$dbname' criado com sucesso.";
        $_SESSION['msg_type'] = "success";
    }

    $conn->select_db($dbname);
    $conn->set_charset("utf8mb4");


    // Criação da tabela 'usuarios' se não existir
    $createTableQuery = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL
    )";

    if (!$conn->query($createTableQuery)) {
        throw new Exception("Erro ao criar tabela 'usuarios': " . $conn->error);
    }

} catch (Exception $e) {
    $_SESSION['msg'] = $e->getMessage();
    $_SESSION['msg_type'] = "error";
}

// Verifica se a requisição é POST para processar o cadastro
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Captura e sanitiza os dados
    $nome = trim($_POST['nome']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    if (empty($nome) || empty($email) || empty($senha)) {
        $_SESSION['msg'] = "Todos os campos são obrigatórios.";
        $_SESSION['msg_type'] = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['msg'] = "Formato de email inválido.";
        $_SESSION['msg_type'] = "error";
    } else {
        // Verifica se o email já está cadastrado
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        if (!$stmt) {
            $_SESSION['msg'] = "Erro interno ao preparar a consulta.";
            $_SESSION['msg_type'] = "error";
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['msg'] = "Este e-mail já está cadastrado.";
            $_SESSION['msg_type'] = "error";
        } else {
            // Hash da senha para segurança
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            // Insere o usuário no banco de dados
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            if (!$stmt) {
                $_SESSION['msg'] = "Erro ao preparar a inserção.";
                $_SESSION['msg_type'] = "error";
            }

            $stmt->bind_param("sss", $nome, $email, $senhaHash);
            if ($stmt->execute()) {
                $_SESSION['msg'] = "Cadastro realizado com sucesso!";
                $_SESSION['msg_type'] = "success";
            } else {
                $_SESSION['msg'] = "Erro ao cadastrar usuário: " . $stmt->error;
                $_SESSION['msg_type'] = "error";
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
    <title>Arcana - Cadastro</title>
    <link rel="shortcut icon" type="imagex/png" href="img/logo.png">
    <link rel="stylesheet" href="css/fontes.css">
    <link rel="stylesheet" href="css/cadastro.css">
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
        <h1>ARCANA</h1>
        <p class="subtitulo">Faça seu cadastro</p>

        <!-- Exibe a mensagem de erro/sucesso -->
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="message <?php echo $_SESSION['msg_type']; ?>">
                <?php 
                echo $_SESSION['msg']; 
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

    <script src="js/cadastro.js"></script>
</body>
</html>
