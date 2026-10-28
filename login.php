<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "usbw";
$dbname = "UserDB";
$conn = null;

try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");
} catch (Throwable $e) {
    $_SESSION['msg'] = "Não foi possível conectar ao banco de dados. Verifique se o MySQL está iniciado no XAMPP e se usuário e senha estão corretos.";
    $_SESSION['msg_type'] = "error";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (!$conn instanceof mysqli) {
        $_SESSION['msg'] = "O login está indisponível porque não foi possível conectar ao banco de dados.";
        $_SESSION['msg_type'] = "error";
    } elseif ($email === '' || $senha === '') {
        $_SESSION['msg'] = "E-mail e senha são obrigatórios.";
        $_SESSION['msg_type'] = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['msg'] = "Formato de e-mail inválido.";
        $_SESSION['msg_type'] = "error";
    } else {
        try {
            $stmt = $conn->prepare("SELECT id, senha FROM usuarios WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 0) {
                $_SESSION['msg'] = "E-mail não encontrado.";
                $_SESSION['msg_type'] = "error";
            } else {
                $stmt->bind_result($userId, $storedPassword);
                $stmt->fetch();

                if (password_verify($senha, $storedPassword)) {
                    $_SESSION['user_id'] = $userId;
                    header("Location: edit.php");
                    exit;
                } else {
                    $_SESSION['msg'] = "Senha incorreta.";
                    $_SESSION['msg_type'] = "error";
                }
            }
            $stmt->close();
        } catch (Throwable $e) {
            $_SESSION['msg'] = "Não foi possível processar o login. Tente novamente.";
            $_SESSION['msg_type'] = "error";
        }
    }
}

if ($conn instanceof mysqli) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="color-scheme" content="light dark">
    <script src="js/lightmode.js"></script>
    <link rel="stylesheet" href="css/global.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcana - Login</title>
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
