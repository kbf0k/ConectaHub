<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Verifica se o id do post foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: feed.php");
    exit();
}

$id_post = intval($_GET['id']);

// Verifica se o post pertence ao usuário
$stmt = $conn->prepare("SELECT id_post FROM posts WHERE id_post = ? AND fk_usuario_id = ?");
$stmt->bind_param("ii", $id_post, $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Post não encontrado ou você não tem permissão para excluir.'); window.location.href='feed.php';</script>";
    exit();
}

// Exclui o post
$stmtDelete = $conn->prepare("DELETE FROM posts WHERE id_post = ?");
$stmtDelete->bind_param("i", $id_post);

if ($stmtDelete->execute()) {
    echo "<script>alert('Post excluído com sucesso!'); window.location.href='feed.php';</script>";
} else {
    echo "<script>alert('Erro ao excluir o post: {$stmtDelete->error}'); window.location.href='feed.php';</script>";
}

$stmtDelete->close();
$conn->close();
