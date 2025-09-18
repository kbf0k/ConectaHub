<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(403);
    exit("Usuário não logado");
}

$id_usuario = $_SESSION['id_usuario'];
$id_post = intval($_POST['id_post']);

// Verifica se já curtiu
$stmt = $conn->prepare("SELECT id_curtida FROM curtidas WHERE fk_id_usuario = ? AND fk_id_post = ?");
$stmt->bind_param("ii", $id_usuario, $id_post);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    // Se já curtiu, remove
    $stmt = $conn->prepare("DELETE FROM curtidas WHERE fk_id_usuario = ? AND fk_id_post = ?");
    $stmt->bind_param("ii", $id_usuario, $id_post);
    $stmt->execute();
    $status = "descurtiu";
} else {
    // Se não curtiu, insere
    $stmt = $conn->prepare("INSERT INTO curtidas (fk_id_usuario, fk_id_post) VALUES (?, ?)");
    $stmt->bind_param("ii", $id_usuario, $id_post);
    $stmt->execute();
    $status = "curtiu";
}

// Conta curtidas atualizadas
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM curtidas WHERE fk_id_post = ?");
$stmt->bind_param("i", $id_post);
$stmt->execute();
$resultado = $stmt->get_result()->fetch_assoc();
$total = $resultado['total'];

echo json_encode(["status" => $status, "total" => $total]);
