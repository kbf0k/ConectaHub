<?php
session_start();
include 'conexao.php';

$usuarioLogado = $_SESSION['id_usuario'] ?? null;

if (!$usuarioLogado) {
    header("Location: login.php");
    exit;
}

$sql = "SELECT id_usuario, nome_usuario, foto_usuario FROM usuarios WHERE id_usuario != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuarioLogado);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Mensagens</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #6A0DAD, #9B59B6, #E6E6FA);
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
      padding: 20px;
    }
    .container {
      background: white;
      width: 400px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
      overflow: hidden;
    }
    .header {
      background: #6A0DAD;
      color: white;
      padding: 15px;
      font-size: 18px;
      text-align: center;
    }
    .user-list {
      list-style: none;
      margin: 0;
      padding: 0;
      max-height: 500px;
      overflow-y: auto;
    }
    .user-item {
      display: flex;
      align-items: center;
      padding: 12px;
      border-bottom: 1px solid #eee;
      cursor: pointer;
      transition: background 0.2s;
    }
    .user-item:hover {
      background: #f5f5f5;
    }
    .user-item img {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      margin-right: 12px;
      object-fit: cover;
    }
    .user-item span {
      font-size: 16px;
      font-weight: 500;
      color: #333;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">Mensagens</div>
    <ul class="user-list">
      <?php while ($row = $result->fetch_assoc()): ?>
        <li class="user-item" onclick="window.location.href='chat.php?user_id=<?=$row['id']?>'">
          <img src="<?=$row['foto_usuario']?>" alt="Foto de <?=$row['nome_usuario']?>">
          <span><?=$row['nome_usuario']?></span>
        </li>
      <?php endwhile; ?>
    </ul>
  </div>
</body>
</html>
