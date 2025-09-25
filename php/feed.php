<?php
include 'conexao.php';
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

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

$sqlPosts = "SELECT p.id_post, p.imagem_post, p.descricao_post, p.fk_usuario_id, u.nome_usuario, u.foto_usuario
    FROM posts p
    INNER JOIN usuarios u ON p.fk_usuario_id = u.id_usuario
    ORDER BY p.id_post DESC
";
$posts = $conn->query($sqlPosts);

$sqlPosts = "SELECT p.id_post, p.imagem_post, p.descricao_post, p.fk_usuario_id, 
           u.nome_usuario, u.foto_usuario,
           (SELECT COUNT(*) FROM curtidas c WHERE c.fk_id_post = p.id_post) AS total_curtidas,
           (SELECT COUNT(*) FROM curtidas c WHERE c.fk_id_post = p.id_post AND c.fk_id_usuario = ?) AS curtiu
    FROM posts p
    INNER JOIN usuarios u ON p.fk_usuario_id = u.id_usuario
    ORDER BY p.id_post DESC
";
$stmtPosts = $conn->prepare($sqlPosts);
$stmtPosts->bind_param("i", $id_usuario);
$stmtPosts->execute();
$posts = $stmtPosts->get_result();

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
        <?php if ($posts->num_rows > 0): ?>
            <div class="container_post">
                <?php while ($post = $posts->fetch_assoc()): ?>
                    <div class="post">
                        <div class="titulo">
                            <?php if (!empty($post['foto_usuario'])): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($post['foto_usuario']) ?>" alt="Foto de perfil" class="perfil">
                            <?php else: ?>
                                <img src="../img/default-avatar.png" alt="Foto padrão" class="perfil">
                            <?php endif; ?>
                            <p>@<?= htmlspecialchars($post['nome_usuario']) ?></p>

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
                            <div class="coracao" data-post="<?= $post['id_post'] ?>">
                                <?php if ($post['curtiu'] > 0): ?>
                                    <i class="fa-solid fa-heart liked"></i>
                                <?php else: ?>
                                    <i class="fa-regular fa-heart"></i>
                                <?php endif; ?>
                                <p><?= $post['total_curtidas'] ?> <strong>curtidas</strong></p>
                            </div>


                            <div class="legenda">
                                <h1>@<?= htmlspecialchars($post['nome_usuario']) ?></h1>
                                <p><?= nl2br(htmlspecialchars($post['descricao_post'])) ?></p>
                            </div>

                            <div class="comentario">
                                <h1>Comentar</h1>
                                <form action="processa_comentario.php" method="POST">
                                    <input type="hidden" name="id_post" value="<?= $post['id_post'] ?>">
                                    <input type="text" name="comentario" required placeholder="Escreva um comentário...">
                                    <button type="submit">Comentar</button>
                                </form>
                            </div>

                            <?php
                            $stmtComentarios = $conn->prepare("
                                SELECT c.mensagem_comentarios, u.nome_usuario, u.foto_usuario
                                FROM comentarios c
                                INNER JOIN usuarios u ON c.fk_id_usuario = u.id_usuario
                                WHERE c.fk_id_post = ?
                                ORDER BY c.id_comentario DESC
                            ");
                            $stmtComentarios->bind_param("i", $post['id_post']);
                            $stmtComentarios->execute();
                            $resultComentarios = $stmtComentarios->get_result();
                            ?>

                            <div class="lista-comentarios">
                                <?php if ($resultComentarios->num_rows > 0): ?>
                                    <?php while ($comentario = $resultComentarios->fetch_assoc()): ?>
                                        <div class="comentario-item">
                                            <?php if (!empty($comentario['foto_usuario'])): ?>
                                                <img src="data:image/jpeg;base64,<?= base64_encode($comentario['foto_usuario']) ?>" alt="Foto" class="perfil-comentario">
                                            <?php else: ?>
                                                <img src="../img/default-avatar.png" alt="Foto padrão" class="perfil-comentario">
                                            <?php endif; ?>
                                            <p>
                                                <strong>@<?= htmlspecialchars($comentario['nome_usuario']) ?>:</strong>
                                                <?= nl2br(htmlspecialchars($comentario['mensagem_comentarios'])) ?>
                                            </p>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <p class="sem-comentarios">Nenhum comentário ainda.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>Nenhum post encontrado.</p>
        <?php endif; ?>
    </main>

    <script>
        document.querySelectorAll(".coracao i").forEach(coracao => {
            coracao.addEventListener("click", function() {
                const divCoracao = this.closest(".coracao");
                const idPost = divCoracao.getAttribute("data-post");

                fetch("curtir.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "id_post=" + idPost
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === "curtiu") {
                            this.classList.remove("fa-regular");
                            this.classList.add("fa-solid", "liked");
                        } else {
                            this.classList.remove("fa-solid", "liked");
                            this.classList.add("fa-regular");
                        }
                        divCoracao.querySelector("p").innerHTML = data.total + " <strong>curtidas</strong>";
                    });
            });
        });
    </script>

</body>

</html>