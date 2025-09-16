<?php
// include './conexao.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];


    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] == 0) {
        $imagem = $_FILES["imagem"];
        $extensao = strtolower(pathinfo($imagem["name"], PATHINFO_EXTENSION));

        if (in_array($extensao, ["jpg", "jpeg", "png", "gif"])) {
            $novoNome = uniqid() . "." . $extensao;
            $diretorio = "img/" . $novoNome;

            if (move_uploaded_file($imagem["tmp_name"], $diretorio)) {
                if (!empty($senha)) {
                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                    $sql = "UPDATE usuarios SET nome_usuario = ?, email_usuario = ?, senha_usuario = ?, imagem_usuario = ? WHERE id_usuario = ?";
                    $stmt = $conexao->prepare($sql);
                    $stmt->bind_param("ssssi", $nome, $email, $senhaHash, $novoNome, $id);
                } else {
                    $sql = "UPDATE usuarios SET nome_usuario = ?, email_usuario = ?, imagem_usuario = ? WHERE id_usuario = ?";
                    $stmt = $conexao->prepare($sql);
                    $stmt->bind_param("sssi", $nome, $email, $novoNome, $id);
                }
            } else {
                echo "Erro ao fazer upload da imagem.";
                exit;
            }
        } else {
            echo "Formato de imagem inválido. Apenas JPG, JPEG, PNG e GIF são permitidos.";
            exit;
        }
    } else {
        if (!empty($senha)) {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nome_usuario = ?, email_usuario = ?, senha_usuario = ? WHERE id_usuario = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("sssi", $nome, $email, $senhaHash, $id);
        } else {
            $sql = "UPDATE usuarios SET nome_usuario = ?, email_usuario = ? WHERE id_usuario = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ssi", $nome, $email, $id);
        }
    }

    if ($stmt->execute()) {
        header("Location: perfil.php?id=".$id);
        exit;
    } else {
        echo "Erro ao atualizar informações: " . $conexao->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/HW-icon.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/perfil.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./main.js" defer></script>
    <title>Perfil</title>
</head>
<body>
<main class="main-content">
    <div class="pagina-perfil">
        <div id="fundo">
            <img id="banner-histweb" src="img/HistWebWhite.svg" alt="">
        </div>
        <form class="informacoes" action="perfil.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
            <img id='user-perfil' src='img/<?php echo $usuario['imagem_usuario']; ?>' alt=''>
            
            <div class='info'>
                <div class='nome-email'>
                    <input type="hidden" name="id" value="<?php echo $usuario['id_usuario']; ?>">
                    <label class="nome-acima" for="upload">IMAGEM</label>
                    <input id="upload" type="file" name="imagem" accept="image/*">
                    <label class='nome-acima'>NOME</label>
                    <input id='campo-nome' name="nome" type='text' value='<?php echo $usuario['nome_usuario']; ?>'>
                    <label class='nome-acima'>EMAIL</label>
                    <input id='campo-email' name="email" type='email' value='<?php echo $usuario['email_usuario']; ?>'>
                    <label class='nome-acima'>SENHA</label>
                    <input id='campo-senha' name="senha" type='password' placeholder="Nova senha">
                    <div id='mostrar'>
                        <input type='checkbox' onclick='mostrarSenha()'> Mostrar senha
                    </div>
                    <button type="submit" id="confirma">Confirmar alterações</button>
                </div>
            </div>
        </form>

        <div class="exclusao">
            <p class='nome-acima'>EXCLUIR SUA CONTA</p>
            <!-- <?php echo "<a href='./exclusao.php?id=". $_SESSION['id'] ."'><button id='excluir-conta'>Excluir conta</button></a>"; ?> -->
        </div>
        
        <div id="amigos">
            <p>Amigos</p>

        </div>
    </div>
    
</main>
<script>
    function mostrarSenha() {
        var x = document.getElementById("campo-senha");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>
</body>
</html>