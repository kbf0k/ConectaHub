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
$stmt = $conn->prepare("SELECT * FROM posts WHERE id_post = ? AND fk_usuario_id = ?");
$stmt->bind_param("ii", $id_post, $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Post não encontrado ou você não tem permissão para editar.";
    exit();
}

$post = $result->fetch_assoc();

// Se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao = $_POST['descricao_post'];

    // Verifica se um novo arquivo foi enviado
    if (isset($_FILES['imagem_post']) && $_FILES['imagem_post']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['imagem_post']['tmp_name'];
        $media = file_get_contents($tmpName);

        $stmtUpdate = $conn->prepare("UPDATE posts SET descricao_post = ?, imagem_post = ? WHERE id_post = ?");
        $null = NULL;
        $stmtUpdate->bind_param("sbi", $descricao, $null, $id_post);
        $stmtUpdate->send_long_data(1, $media);
    } else {
        // Só atualiza a descrição
        $stmtUpdate = $conn->prepare("UPDATE posts SET descricao_post = ? WHERE id_post = ?");
        $stmtUpdate->bind_param("si", $descricao, $id_post);
    }

    if ($stmtUpdate->execute()) {
        echo "<script>alert('Post atualizado com sucesso!'); window.location.href='feed.php';</script>";
        exit();
    } else {
        echo "Erro ao atualizar post: " . $stmtUpdate->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/adicionar_post.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Editar Post</title>
</head>

<body>
    <?php include('nav.php'); ?>

    <div class="back-btn-container">
        <button class="back-btn" onclick="history.back()">
            <i class="fas fa-arrow-left"></i> Voltar
        </button>
    </div>
    <main>
        <div class="modal">
            <div class="header">Editar Post</div>
            <div class="container">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="imagem_post">Imagem do post:</label>
                        <?php if (!empty($post['imagem_post'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($post['imagem_post']) ?>" alt="Post"
                                style="max-width: 300px; display:block; margin-bottom:10px;">
                        <?php endif; ?>
                        <input type="file" id="imagem_post" name="imagem_post" accept="image/*,video/*">
                    </div>

                    <div class="form-group">
                        <label for="descricao_post">Descrição:</label>
                        <textarea id="descricao_post" name="descricao_post"
                            required><?= htmlspecialchars($post['descricao_post']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit">Atualizar Post</button>
                        <a href="feed.php" class="btn-cancel">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>

</html>