<?php
session_start();
include 'conexao.php';

$erro = null;
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmaSenha = $_POST['confirma_senha'] ?? '';

    if (!$email) {
        $erro = "Digite seu e-mail!";
    } elseif ($novaSenha !== $confirmaSenha) {
        $erro = "As senhas não coincidem!";
    } else {
        $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email_usuario = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            $senhaHash = md5($novaSenha);
            $stmt = $conn->prepare("UPDATE usuarios SET senha_usuario = ? WHERE id_usuario = ?");
            $stmt->bind_param("si", $senhaHash, $usuario['id_usuario']);
            if ($stmt->execute()) {
                $sucesso = true;
            } else {
                $erro = "Erro ao atualizar a senha.";
            }
        } else {
            $erro = "E-mail não cadastrado!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recuperar Senha</title>
<link rel="stylesheet" href="../css/cadastro.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container">
    <h2 class="titulo">Recuperar Senha</h2>

    <?php if ($erro) echo "<p style='color:red;'>$erro</p>"; ?>

    <form method="POST">
        <label>E-mail:</label>
        <input type="email" name="email" required>

        <label>Nova senha:</label>
        <input type="password" name="nova_senha" required>

        <label>Confirmar nova senha:</label>
        <input type="password" name="confirma_senha" required>

        <button type="submit">Atualizar Senha</button>
    </form>

    <p><a href="login.php">Voltar ao login</a></p>
</div>

<?php if ($sucesso): ?>
<script>
    Swal.fire({
        title: "Senha atualizada!",
        text: "Sua senha foi redefinida com sucesso.",
        icon: "success",
        confirmButtonColor: "#6A0DAD"
    }).then(() => {
        window.location.href = "login.php";
    });
</script>
<?php endif; ?>

</body>
</html>
