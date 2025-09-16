<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Consulta foto do usuário logado
$stmt = $conn->prepare("SELECT foto_usuario, nome_usuario FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

$foto = null;
$nomeUsuario = "Usuário";
if ($resultado->num_rows > 0) {
    $linha = $resultado->fetch_assoc();
    $foto = $linha['foto_usuario'];
    $nomeUsuario = $linha['nome_usuario'];
}

// Consulta todos os posts com JOIN para pegar dados do dono do post
$sqlPosts = "SELECT p.id_post, p.imagem_post, p.descricao_post, p.fk_usuario_id, u.nome_usuario, u.foto_usuario
    FROM posts p
    INNER JOIN usuarios u ON p.fk_usuario_id = u.id_usuario
    ORDER BY p.id_post DESC
";
$posts = $conn->query($sqlPosts);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/feed.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Feed - ConectaHub</title>
</head>

<body>
    <?php include('nav.php'); ?>

    <main>
        <div class="container_post">
            <?php if ($posts->num_rows > 0): ?>
                <?php while ($post = $posts->fetch_assoc()): ?>
                    <div class="post">
                        <div class="titulo">
                            <?php if (!empty($post['foto_usuario'])): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($post['foto_usuario']) ?>" alt="Foto de perfil" class="perfil">
                            <?php else: ?>
                                <img src="../img/default-avatar.png" alt="Foto padrão" class="perfil">
                            <?php endif; ?>
                            <p>@<?= htmlspecialchars($post['nome_usuario']) ?></p>

                            <!-- Botões de editar/excluir apenas para o dono do post -->
                            <?php if ($post['fk_usuario_id'] == $id_usuario): ?>
                                <div class="acoes_post">
                                    <a href="editar_post.php?id=<?= $post['id_post'] ?>" class="btn-editar">
                                        <i class="fa-solid fa-pen"></i> Editar
                                    </a>
                                    <a href="excluir_post.php?id=<?= $post['id_post'] ?>" class="btn-excluir" onclick="return confirm('Tem certeza que deseja excluir este post?');">
                                        <i class="fa-solid fa-trash"></i> Excluir
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="imagem">
                            <?php if (!empty($post['imagem_post'])): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($post['imagem_post']) ?>" alt="Post">
                            <?php endif; ?>
                        </div>

                        <div class="interacoes">
                            <div class="coracao">
                                <i class="fa-regular fa-heart"></i>
                                <p>0 <strong>curtidas</strong></p>
                            </div>
                            <div class="legenda">
                                <h1>@<?= htmlspecialchars($post['nome_usuario']) ?></h1>
                                <p><?= nl2br(htmlspecialchars($post['descricao_post'])) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nenhum post encontrado.</p>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>