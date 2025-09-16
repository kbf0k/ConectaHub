<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    exit("Usuário não logado.");
}

$remetenteId   = $_SESSION['id_usuario'];
$destinatarioId = $_POST['destinatario_id'] ?? null;
$mensagem      = trim($_POST['mensagem'] ?? '');

if (!$destinatarioId || !$mensagem) {
    exit("Dados inválidos.");
}

$sql = "INSERT INTO mensagens (remetente_id, destinatario_id, mensagem, data_envio) 
        VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $remetenteId, $destinatarioId, $mensagem);

if ($stmt->execute()) {
    echo "Mensagem enviada";
} else {
    echo "Erro ao enviar: " . $stmt->error;
}
$stmt->close();
