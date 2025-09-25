<?php
include './conexao.php';
session_start();

$sucesso = false;

if (!isset($_SESSION['id_usuario'])) {
    die("Usuário não está logado.");
}
$id = $_SESSION['id_usuario'];

$sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $usuario = $result->fetch_assoc();
} else {
    die("Erro! Usuário não consta no Banco de Dados.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"] ?? null;

    $fotoBlob = null;
    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] == 0) {
        $fotoBlob = file_get_contents($_FILES["imagem"]["tmp_name"]);
    }

    $sql = "UPDATE usuarios SET nome_usuario=?, email_usuario=?";
    $params = [$nome, $email];
    $types = "ss";

    if (!empty($senha)) {
        $sql .= ", senha_usuario=?";
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $params[] = $senhaHash;
        $types .= "s";
    }

    if ($fotoBlob) {
        $sql .= ", foto_usuario=?";
        $params[] = $fotoBlob;
        $types .= "b";
    }

    $sql .= " WHERE id_usuario=?";
    $params[] = $id;
    $types .= "i";
    $stmt = $conn->prepare($sql);

    $refs = [];
    foreach ($params as $key => $value) {
        $refs[$key] = &$params[$key];
    }
    array_unshift($refs, $types);
    call_user_func_array([$stmt, 'bind_param'], $refs);

    if ($fotoBlob) {
        $stmt->send_long_data(count($params) - 2, $fotoBlob);

        if ($stmt->execute()) {
            $sucesso = true;
            $usuario['nome_usuario'] = $nome;
            $usuario['email_usuario'] = $email;
            if ($fotoBlob)
                $usuario['foto_usuario'] = $fotoBlob;
        } else {
            echo "Erro: " . $conn->error;
        }
        $stmt->close();
    }
}

// Converte BLOB para Base64 para exibir
$foto = (isset($row['foto_usuario']) && $row['foto_usuario'])
    ? 'data:image/jpeg;base64,' . base64_encode($row['foto_usuario'])
    : 'https://media.istockphoto.com/id/1495088043/pt/vetorial/user-profile-icon-avatar-or-person-icon-profile-picture-portrait-symbol-default-portrait.jpg?s=612x612&w=0&k=20&c=S7d8ImMSfoLBMCaEJOffTVua003OAl2xUnzOsuKIwek=';

$fotoBase64 = $foto;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/logo-conecthub-semfundo.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/perfil.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Space+Grotesk:wght@300..700&display=swap"
        rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/main.js" defer></script>
    <title>Perfil</title>
</head>

<body>
    <?php include('nav.php') ?>
    <main class="main-content">
        <div class="pagina-perfil">
            <form class="informacoes" action="perfil.php" method="POST" enctype="multipart/form-data">
                <div id="imgggg">
                    <img id='user-perfil' src="<?= $fotoBase64 ?>" alt='Foto de perfil'>
                </div>

                <div class='info'>
                    <div class='nome-email'>
                        <input type="hidden" name="id" value="<?= $usuario['id_usuario'] ?>">
                        <label class="nome-acima" for="upload">IMAGEM</label>
                        <input id="upload" type="file" name="imagem" accept="image/*">
                        <label class='nome-acima'>NOME</label>
                        <input id='campo-nome' name="nome" type='text'
                            value='<?= htmlspecialchars($usuario['nome_usuario']) ?>'>
                        <label class='nome-acima'>EMAIL</label>
                        <input id='campo-email' name="email" type='email'
                            value='<?= htmlspecialchars($usuario['email_usuario']) ?>'>
                        <label class='nome-acima'>SENHA</label>
                        <input id='campo-senha' name="senha" type='password' placeholder="Nova senha">
                        <div id='mostrar'>
                            <input type='checkbox' onclick='mostrarSenha()'> Mostrar senha
                        </div>
                        <button type="submit" id="confirma">Confirmar alterações</button>
                    </div>
                </div>
            </form>

            <?php if ($sucesso): ?>
                <script>
                    Swal.fire({
                        title: "Sucesso!",
                        text: "Alterações realizadas!",
                        icon: "success",
                        confirmButtonColor: "#6A0DAD"
                    }).then(() => {
                        window.location.href = "perfil.php";
                    });
                </script>
            <?php endif; ?>

            <div class="logout-container">
                <a href="logout.php" class="logout-btn">Sair
                    <i class='bx bx-log-out'></i>
                </a>
            </div>
            <div class="amigos">
                <div id="titulo">
                    <p>Amigos</p>
                </div>
                <?php $sqlAmigos = "SELECT id_usuario, nome_usuario, email_usuario FROM usuarios WHERE id_usuario != ?";
                $stmtAmigos = $conn->prepare($sqlAmigos);
                $stmtAmigos->bind_param("i", $id);
                $stmtAmigos->execute();
                $resultAmigos = $stmtAmigos->get_result();

                if ($resultAmigos->num_rows > 0) {
                    while ($amigo = $resultAmigos->fetch_assoc()) {
                        echo "
        <div class='friend'>
            <div>
                <img id='imgAmg' src='mostrar_imagem.php?id={$amigo['id_usuario']}' alt='Foto amigo'>
            </div>
            <div id='infos'>
                <h4>{$amigo['nome_usuario']}</h4>
                <p>{$amigo['email_usuario']}</p>
            </div>
        </div>
        ";
                    }
                } else {
                    echo "<p>Nenhum amigo encontrado.</p>";
                }
                $stmtAmigos->close();
                ?>

            </div>

    </main>

    <script>
        function mostrarSenha() {
            var x = document.getElementById("campo-senha");
            x.type = x.type === "password" ? "text" : "password";
        }
    </script>
</body>

</html>