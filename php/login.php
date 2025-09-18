<?php
session_start();
include 'conexao.php';

$sucesso = false;
$erro = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha']; 

    $stmt = $conn->prepare("SELECT id_usuario, nome_usuario, senha_usuario FROM usuarios WHERE email_usuario = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();

        if (password_verify($senha, $usuario['senha_usuario'])) { 
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nome_usuario'] = $usuario['nome_usuario'];
            $_SESSION['email_usuario'] = $email;

            $sucesso = true;
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <p><a href="recuperar_senha.php" class="esqueceu-senha">Esqueceu a senha?</a></p>
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
                    window.location.href = "feed.php";
                }
            });
        </script>
    <?php endif; ?>
</body>
</html>
