<?php
include './conexao.php';

if (!isset($_GET['id'])) {
    exit("ID não informado");
}

$id = intval($_GET['id']);

$sql = "SELECT foto_usuario FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    header("Content-Type: image/jpeg"); 
    echo $row['foto_usuario'];
} else {
    echo "Imagem não encontrada.";
}
