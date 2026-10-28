<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "usbw";
$dbname = "UserDB";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

if (!isset($_SESSION['admin_id'])) {
    header("Location: loginAdm.php");
    exit;
}

// Parâmetros da busca
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$order = 'nome';  // por enquanto fixo por nome
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

// Monta o WHERE com múltiplas palavras
$where_sql = '';
if ($search !== '') {
    $words = array_filter(explode(' ', $search));
    $where_clauses = [];
    foreach ($words as $word) {
        $word = $conn->real_escape_string($word);
        // Procura em nome e email a palavra
        $where_clauses[] = "(nome LIKE '%$word%' OR email LIKE '%$word%')";
    }
    $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
}

// Conta total para paginação
$sql_count = "SELECT COUNT(*) AS total FROM usuarios $where_sql";
$res_count = $conn->query($sql_count);
$total_items = 0;
if ($res_count) {
    $row_count = $res_count->fetch_assoc();
    $total_items = (int)$row_count['total'];
}

// Busca usuários paginados
$sql = "SELECT * FROM usuarios $where_sql ORDER BY $order ASC LIMIT $items_per_page OFFSET $offset";
$result = $conn->query($sql);

// Calcula total de páginas
$total_pages = ceil($total_items / $items_per_page);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="color-scheme" content="light dark">
    <script src="js/lightmode.js"></script>
    <link rel="stylesheet" href="css/global.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - Arcana</title>
    <link rel="stylesheet" href="css/painelAdm.css">
    <link rel="stylesheet" href="css/fontes.css">
</head>
<body class="page-admin">
    <div class="site-toolbar">
        <a class="brand" href="index.html" aria-label="Arcana — início">ARCANA</a>
        <div class="theme-controls" role="group" aria-label="Aparência">
            <button type="button" class="theme-toggle" id="lightModeToggle" aria-label="Ativar modo escuro" aria-pressed="false">Modo escuro</button>
            <button type="button" class="theme-system" data-theme-system aria-label="Usar tema do sistema" aria-pressed="true">Sistema</button>
        </div>
    </div>

<header>
    <h1>Arcana Painel</h1>
    <nav>
        <a href="cadastroAdm.php">Sair</a>
    </nav>
</header>

<div class="banner">
    <p>Usuários cadastrados no sistema</p>
</div>

<div class="content">

    <!-- Formulário de busca -->
    <form method="GET" class="search-form" action="">
        <label class="visually-hidden" for="campo-search">Pesquisar por nome ou e-mail</label>
            <input id="campo-search"
            type="text"
            name="search"
            class="search-input"
            placeholder="Pesquisar por nome ou email..."
            value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
            autocomplete="off"
        >
        <button type="submit" class="search-button">Buscar</button>
    </form>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($user = $result->fetch_assoc()): ?>
            <div class="card">
                <div class="info">
                    <h3><?= htmlspecialchars($user['nome'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="type"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <div class="actions">
                    <a href="editarUsuario.php?id=<?= urlencode($user['id']) ?>">Editar</a>
                    <a href="excluirUsuario.php?id=<?= urlencode($user['id']) ?>"
                       onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Nenhum usuário encontrado.</p>
    <?php endif; ?>

    <!-- Paginação -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
            <a
                href="?search=<?= urlencode($search) ?>&page=<?= $p ?>"
                class="<?= $p == $page ? 'active' : '' ?>"
            ><?= $p ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

</div>
</body>
</html>
