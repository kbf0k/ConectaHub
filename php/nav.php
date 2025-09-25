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

$fotoBase64 = (!empty($foto)) 
    ? 'data:image/jpeg;base64,' . base64_encode($foto) 
    : 'https://media.istockphoto.com/id/1495088043/pt/vetorial/user-profile-icon-avatar-or-person-icon-profile-picture-portrait-symbol-default-portrait.jpg?s=612x612&w=0&k=20&c=S7d8ImMSfoLBMCaEJOffTVua003OAl2xUnzOsuKIwek=';

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
                    <img src="../img/logo-conecthub-semfundo.png" alt="Logo">
                </div>

                <div class="menu-toggle" id="hamburger">
                    <i class='bx bx-menu'></i>
                </div>

                <ul id="nav-links">
                    <li><a href="feed.php">Feed</a></li>
                    <li><a href="adicionar_post.php">Adicionar Post</a></li>
                    <li><a href="chat.php">Chat</a></li>
                    <li>
                        <a href="perfil.php">
                            <img id="foto-perfil" src="<?= $fotoBase64 ?>" alt="Foto de perfil">
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

</body>
<script>
    const hamburger = document.getElementById('hamburger');
    const navLinks = document.getElementById('nav-links');

    hamburger.addEventListener('click', () => {
        navLinks.classList.toggle('active');
    });
</script>


</html>