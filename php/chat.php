<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Chat</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #6A0DAD, #9B59B6, #E6E6FA);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .chat {
      width: 400px;
      height: 500px;
      background: white;
      border-radius: 10px;
      padding: 15px;
      display: flex;
      flex-direction: column;
    }
    #mensagens {
      flex: 1;
      overflow-y: auto;
      margin-bottom: 10px;
    }
    .msg {
      padding: 5px 10px;
      margin: 5px 0;
      border-radius: 8px;
      max-width: 80%;
    }
    .enviada { background: #9B59B6; color: white; align-self: flex-end; }
    .recebida { background: #E6E6FA; color: #333; align-self: flex-start; }
    #form {
      display: flex;
    }
    #mensagem {
      flex: 1;
      padding: 8px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    button {
      background: #6A0DAD;
      color: white;
      border: none;
      padding: 8px 12px;
      border-radius: 5px;
      margin-left: 5px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="chat">
    <div id="mensagens"></div>
    <form id="form">
      <input type="text" id="mensagem" placeholder="Digite uma mensagem..." required>
      <button type="submit">Enviar</button>
    </form>
  </div>

  <script>
    const remetente_id = 1; // mock - ID logado
    const destinatario_id = 2; // mock - ID do outro usuário
    let ultimoId = 0;

    async function carregarMensagens() {
      try {
        const res = await fetch(`receber.php?remetente_id=${remetente_id}&destinatario_id=${destinatario_id}&ultimo_id=${ultimoId}`);
        const data = await res.json();
        if (data.length > 0) {
          data.forEach(msg => {
            const div = document.createElement("div");
            div.classList.add("msg");
            div.classList.add(msg.remetente_id == remetente_id ? "enviada" : "recebida");
            div.textContent = msg.mensagem;
            document.getElementById("mensagens").appendChild(div);
            ultimoId = msg.id;
          });
          document.getElementById("mensagens").scrollTop = document.getElementById("mensagens").scrollHeight;
        }
      } catch (e) {
        console.error(e);
      } finally {
        carregarMensagens(); // chama de novo (long polling loop)
      }
    }

    document.getElementById("form").addEventListener("submit", async (e) => {
      e.preventDefault();
      const msg = document.getElementById("mensagem").value;
      if (msg.trim() === "") return;

      await fetch("enviar.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `remetente_id=${remetente_id}&destinatario_id=${destinatario_id}&mensagem=${encodeURIComponent(msg)}`
      });
      document.getElementById("mensagem").value = "";
    });

    carregarMensagens();
  </script>
</body>
</html>
