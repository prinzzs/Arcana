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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - Arcana</title>
    <link rel="stylesheet" href="css/painelAdm.css">
    <link rel="stylesheet" href="css/fontes.css">
    <link rel="stylesheet" href="css/global.css">
    <style>
            .actions {
                margin-top: 10px;
            }
            .actions a {
                margin-right: 10px;
                color: #6c1ee6;
                text-decoration: none;
            }
            .actions a:hover {
                text-decoration: underline;
            }
            .search-form {
                margin-bottom: 20px;
            }
            .search-input {
                padding: 8px;
                width: 300px;
                max-width: 100%;
                border-radius: 5px;
                border: 1px solid #ccc;
            }
            .search-button {
                padding: 8px 15px;
                border-radius: 5px;
                border: none;
                background-color: #6c1ee6;
                color: var(--septenary-color);
                cursor: pointer;
            }
            .search-button:hover {
                background-color: #0056b3;
            }
            .pagination {
                margin-top: 20px;
            }
            .pagination a {
                margin: 0 5px;
                padding: 7px 12px;
                border-radius: 4px;
                background-color: #eee;
                color: #333;
                text-decoration: none;
                font-weight: bold;
            }
            .pagination a.active {
                background-color: #6c1ee6;
                color: var(--septenary-color);
            }
            .pagination a:hover {
                background-color: #0056b3;
                color: var(--septenary-color);
            }
            
            .search-form {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    margin-bottom: 30px;
}

.search-input {
    flex: 1 1 300px;
    padding: 10px 15px;
    font-size: 1rem;
    border: 1px solid #6c1ee6;
    border-radius: 8px;
    outline: none;
    transition: all 0.3s ease;
    background-color: var(--septenary-color);
    color: #333;
}

.search-input:focus {
    border-color: #6c1ee6;
    box-shadow: 0 0 5px #6c1ee6;
}

.search-button {
    padding: 10px 20px;
    font-size: 1rem;
    border: none;
    border-radius: 8px;
    background-color: #6c1ee6;
    color: var(--septenary-color);
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.search-button:hover {
    background-color: #6c1ee6;
}
 
    </style>
</head>
<body>

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
        <input 
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
    <script src="js/lightmode.js"></script>
</body>
</html>
