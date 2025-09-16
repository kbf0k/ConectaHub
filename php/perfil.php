<?php
include './conexao.php';
session_start();

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
                    $sql = "UPDATE usuarios 
                            SET nome_usuario = ?, email_usuario = ?, senha_usuario = ?, foto_usuario = ? 
                            WHERE id_usuario = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssi", $nome, $email, $senhaHash, $novoNome, $id);
                } else {
                    $sql = "UPDATE usuarios 
                            SET nome_usuario = ?, email_usuario = ?, foto_usuario = ? 
                            WHERE id_usuario = ?";
                    $stmt = $conn->prepare($sql);
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
            $sql = "UPDATE usuarios 
                    SET nome_usuario = ?, email_usuario = ?, senha_usuario = ? 
                    WHERE id_usuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $nome, $email, $senhaHash, $id);
        } else {
            $sql = "UPDATE usuarios 
                    SET nome_usuario = ?, email_usuario = ? 
                    WHERE id_usuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $nome, $email, $id);
        }
    }

    if ($stmt->execute()) {
        header("Location: perfil.php");
        exit;
    } else {
        echo "Erro ao atualizar informações: " . $conn->error;
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/main.js" defer></script>
    <title>Perfil</title>
</head>
<body>
    <?php include('nav.php') ?>
    <main class="main-content">
        <div class="pagina-perfil">
            <div id="fundo">
                <img id="banner-histweb" src="img/HistWebWhite.svg" alt="">
            </div>
            <form class="informacoes" action="perfil.php" method="POST" enctype="multipart/form-data">
                <img id='user-perfil' src='mostrar_imagem.php?id=<?php echo $usuario['id_usuario']; ?>' alt='Foto de perfil'>
                
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
            
            <div class="amigos">
                <div id="titulo">
                    <p>Amigos</p>
                </div>
                <div class="friend">
                    <div>
                        <img id="imgAmg" src="../img/logo-conecthub.jpg" alt="">
                    </div>
                    <div id="infos">
                        <h4>kaiquepheio</h4>
                        <p>kaique@gmail.com</p>
                    </div>
                </div>
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
