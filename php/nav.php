<?php
include './conexao.php';


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

?>
    <!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/nav.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <title>Feed - ConectaHub</title>
</head>

<body>
    <header>
        <nav>
            <div id="paginas">

                <div class="logo">
                    <img id=""src="../img/logo-conecthub-semfundo.png" alt="">
                </div>
                <ul>
                    <li><a href="feed.php">Feed</a></li>
                    <li><a href="adicionar_post.php">Adicionar Post</a></li>
                    <li><a href="chat.php">Chat</a></li>
                    <li><a href="perfil.php"><img id='foto-perfil' src='../<?php echo $foto ? $foto : "img/padrao.png"; ?>' alt='Foto de perfil'></a></li>

                </ul>
            </div>
        </nav>
    </header>

</body>

</html>