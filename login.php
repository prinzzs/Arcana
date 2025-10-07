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
        // Verifica se o e-mail existe no banco de dados
        $stmt = $conn->prepare("SELECT id, senha FROM usuarios WHERE email = ?");
        if (!$stmt) {
            $_SESSION['msg'] = "Erro interno ao preparar a consulta.";
            $_SESSION['msg_type'] = "error";
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $_SESSION['msg'] = "E-mail não encontrado.";
            $_SESSION['msg_type'] = "error";
        } else {
            // O e-mail foi encontrado, agora verifica a senha
            $stmt->bind_result($userId, $storedPassword);
            $stmt->fetch();

            // Verifica se a senha corresponde ao hash armazenado no banco
            if (password_verify($senha, $storedPassword)) {
                // Login bem-sucedido
                $_SESSION['user_id'] = $userId; // Armazena o ID do usuário na sessão
                $_SESSION['msg'] = "Login realizado com sucesso!";
                $_SESSION['msg_type'] = "success";
                header("Location: edit.php"); // Redireciona para uma página protegida, por exemplo
                exit;
            } else {
                $_SESSION['msg'] = "Senha incorreta.";
                $_SESSION['msg_type'] = "error";
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcana - Login</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/fontes.css">
    <link rel="stylesheet" href="css/global.css">
</head>
<body>
    <div class="container">
        <h1 id="arca">ARCANA</h1>
        <p class="subtitulo">Faça seu login</p>

        <!-- Exibe mensagens de erro ou sucesso -->
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="message <?php echo $_SESSION['msg_type']; ?>">
                <?php
                    echo $_SESSION['msg'];
                    unset($_SESSION['msg']);
                    unset($_SESSION['msg_type']);
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="email" placeholder="Email:" id="email" name="email" required>
            <input type="password" placeholder="Senha:" id="senha" name="senha" required>

            <button type="submit" class="entrar">Entrar</button>
        </form>
        
        <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se aqui</a></p>
    </div>
    <script src="js/lightmode.js"></script>
</body>
</html>
