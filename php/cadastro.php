<?php
include 'conexao.php';
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome  = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nome_usuario, email_usuario, senha_usuario, foto_usuario) VALUES (?, ?, ?, ?)");
    
    $foto = null;

    $stmt->bind_param("sssb", $nome, $email, $senha, $foto);

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $fp = fopen($_FILES['foto']['tmp_name'], "rb");
        while (!feof($fp)) {
            $stmt->send_long_data(3, fread($fp, 8192));
        }
        fclose($fp);
    }

    if ($stmt->execute()) {
        $sucesso = true;
    } else {
        echo "Erro: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <h2 class="titulo">Cadastro</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <label>Nome:</label>
            <input type="text" name="nome" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Senha:</label>
            <input type="password" name="senha" required>

            <label>Foto de perfil:</label>
            <input type="file" name="foto" accept="image/*">

            <button type="submit">Cadastrar</button>

            <p>Já tem conta? <a href="login.php">Faça login</a></p>
        </form>
    </div>

    <?php if ($sucesso): ?>
        <script>
            Swal.fire({
                title: "Sucesso!",
                text: "Logado com sucesso!",
                icon: "success",
                confirmButtonColor: "#6A0DAD"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "login.php";
                }
            });
        </script>
    <?php endif; ?>
</body>
</html>
