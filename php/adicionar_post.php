<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao = $_POST['conteudo'];

    // Verifica se o arquivo foi enviado
    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['media']['tmp_name'];
        $media = file_get_contents($tmpName);

        // Prepara a query com os nomes corretos das colunas
        $stmt = $conn->prepare("INSERT INTO posts (fk_usuario_id, imagem_post, descricao_post) VALUES (?, ?, ?)");
        $null = NULL; // usado para o bind_param no campo BLOB
        $stmt->bind_param("ibs", $id_usuario, $null, $descricao);

        // Envia o conteúdo binário para o campo imagem_post
        $stmt->send_long_data(1, $media);

        if ($stmt->execute()) {
            echo "<script>alert('Post publicado com sucesso!'); window.location.href='feed.php';</script>";
        } else {
            echo "Erro ao salvar post: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Erro: Nenhum arquivo enviado ou falha no upload.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/adicionar_post.css">
    <title>Adicionar Post</title>
</head>

<body>
    <?php include('nav.php'); ?>

    <main>
        <div class="modal">

            <div class="header">Adicionar Post</div>
            <div class="container">
                <form method="POST" enctype="multipart/form-data">
    
                    <div class="form-group">
                        <label for="media">Imagem ou Vídeo:</label>
                        <input type="file" id="media" name="media" accept="image/*,video/*" required>
                    </div>
    
                    <div class="form-group">
                        <label for="conteudo">Escreva algo:</label>
                        <textarea id="conteudo" name="conteudo" placeholder="O que você está pensando?" required></textarea>
                    </div>
    
                    <div class="form-group">
                        <button type="submit">Publicar</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>

</html>