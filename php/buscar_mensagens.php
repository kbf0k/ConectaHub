<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    exit("Usuário não logado.");
}

$usuarioLogado = $_SESSION['id_usuario'];
$destinatarioId = $_GET['destinatario_id'] ?? null;

if (!$destinatarioId) {
    exit("Destinatário inválido.");
}

$sql = "SELECT remetente_id, mensagem, data_envio 
        FROM mensagens 
        WHERE (remetente_id = ? AND destinatario_id = ?)
           OR (remetente_id = ? AND destinatario_id = ?)
        ORDER BY data_envio ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $usuarioLogado, $destinatarioId, $destinatarioId, $usuarioLogado);
$stmt->execute();
$result = $stmt->get_result();

// Renderiza mensagens
while ($row = $result->fetch_assoc()) {
    $classe = $row['remetente_id'] == $usuarioLogado ? "sent" : "received";
    echo "<div class='message {$classe}'>" . htmlspecialchars($row['mensagem']) . "</div>";
}
$stmt->close();
