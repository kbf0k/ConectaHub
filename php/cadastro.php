<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome  = $_POST['nome'];
    $email = $_POST['email'];
    $senha = md5($_POST['senha']);

    $stmt = $conn->prepare("INSERT INTO usuarios (nome_usuario, email_usuario, senha_usuario, foto_usuario) VALUES (?, ?, ?, ?)");
    
    $foto = null;

    $stmt->bind_param("sssb", $nome, $email, $senha, $foto);

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $fp = fopen($_FILES['foto']['tmp_name'], "rb");
        while (!feof($fp)) {
            $stmt->send_long_data(3, fread($fp, 8192)); // 3 = índice do quarto parâmetro
        }
        fclose($fp);
    }

    if ($stmt->execute()) {
        echo "Usuário cadastrado com sucesso!";
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
</body>
</html>
