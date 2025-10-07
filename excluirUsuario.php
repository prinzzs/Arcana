<?php
include('conexao.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redireciona de volta pra página que lista os usuários
        header("Location: painelAdm.php");
        exit;
    } else {
        echo "Erro ao excluir o usuário.";
    }

    $stmt->close();
} else {
    echo "ID inválido.";
}
?>