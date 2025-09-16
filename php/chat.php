<?php
session_start();
include 'conexao.php';

$usuarioLogado = $_SESSION['id_usuario'] ?? null;
if (!$usuarioLogado) {
    header("Location: login.php");
    exit;
}

$destinatarioId = $_GET['user_id'] ?? null;
if (!$destinatarioId) {
    header("Location: dm.php");
    exit;
}

$sql = "SELECT id_usuario, nome_usuario, foto_usuario FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $destinatarioId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Usuário não encontrado.";
    exit;
}

$destinatario = $result->fetch_assoc();
$fotoDestinatario = $destinatario['foto_usuario'] 
    ? 'data:image/jpeg;base64,' . base64_encode($destinatario['foto_usuario']) 
    : 'https://via.placeholder.com/45';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Chat com <?= htmlspecialchars($destinatario['nome_usuario']) ?></title>
    <link rel="stylesheet" href="../css/chat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <?php
    include 'nav.php';
    ?>
    <div class="main">
        <div class="chat-container">
      
            <div class="chat-header">
                <img src="<?= $fotoDestinatario ?>" alt="Foto de <?= htmlspecialchars($destinatario['nome_usuario']) ?>">
                <span><?= htmlspecialchars($destinatario['nome_usuario']) ?></span>
            </div>
            
            <div class="chat-messages" id="chat-messages">
                    <!-- Mensagens serão carregadas via AJAX (long polling futuramente) -->
            </div>
      
      
            <form class="chat-input" id="chat-form">
                <input type="text" name="mensagem" id="mensagem" placeholder="Digite sua mensagem..." required>
                <button type="submit" id="btn-enviar">
                    <i class="fas fa-paper-plane">      Enviar</i>
                </button>
            </form>
        </div>
    </div>

  <script>
    const form = document.getElementById("chat-form");
    const input = document.getElementById("mensagem");
    const messagesDiv = document.getElementById("chat-messages");

    // Enviar mensagem
    form.addEventListener("submit", function(e) {
      e.preventDefault();
      const msg = input.value.trim();
      if (!msg) return;

      fetch("enviar_mensagem.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "destinatario_id=<?= $destinatarioId ?>&mensagem=" + encodeURIComponent(msg)
      })
      .then(res => res.text())
      .then(data => {
        input.value = "";
      });
    });

    // Long polling para atualizar mensagens
    function carregarMensagens() {
      fetch("buscar_mensagens.php?destinatario_id=<?= $destinatarioId ?>")
        .then(res => res.text())
        .then(data => {
          messagesDiv.innerHTML = data;
          messagesDiv.scrollTop = messagesDiv.scrollHeight;
          setTimeout(carregarMensagens, 2000); // repete a cada 2s
        });
    }
    carregarMensagens();
  </script>
</body>
</html>
