<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_usuario = $_SESSION['id_usuario'];
    $id_post = $_POST['id_post'];
    $comentario = trim($_POST['comentario']);

    if (!empty($comentario)) {
        $stmt = $conn->prepare("INSERT INTO comentarios (mensagem_comentarios, fk_id_usuario, fk_id_post) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $comentario, $id_usuario, $id_post);
        $stmt->execute();
    }
}

// Redireciona de volta ao feed
header("Location: feed.php");
exit();
