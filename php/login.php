<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = md5($_POST['senha']); // se você mudar o cadastro para password_hash, use password_verify aqui

    // Buscar usuário no banco
    $stmt = $conn->prepare("SELECT id_usuario, nome_usuario, senha_usuario FROM usuarios WHERE email_usuario = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();

        // Verifica senha
        if ($usuario['senha_usuario'] === $senha) { // se usar password_hash, substitua por password_verify()
            // Cria sessão
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nome_usuario'] = $usuario['nome_usuario'];
            $_SESSION['email_usuario'] = $email;

            header("Location: dashboard.php"); // página após login
            exit();
        } else {
            $erro = "Senha incorreta!";
        }
    } else {
        $erro = "Usuário não encontrado!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/cadastro.css">
</head>
<body>
    <div class="container">
        <h2 class="titulo">Login</h2>
        <?php if (isset($erro)) { echo "<p style='color:red;'>$erro</p>"; } ?>
        <form action="" method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Senha:</label>
            <input type="password" name="senha" required>

            <button type="submit">Entrar</button>

            <p>Não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
        </form>
    </div>
</body>
</html>

