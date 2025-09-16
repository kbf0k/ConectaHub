<?php
// include './conexao.php';
session_start();
// include './id_verify.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/HW-icon.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/perfil.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="./main.js" defer></script>
    <title>História na Web - Exclusão de usuário</title>
</head>
<body id="body-exclusao">
    <nav class="sidebar">
        <div>
            <div class="topo">
                <div class="logo">
                    <img id="logo-nav" src="img/HistWeb.png" alt="">
                </div>
                <i class="bx bx-menu" id="but-menu"></i>
            </div>
            <ul>
                <li>
                    <a href="index.php?id=<?php echo $_SESSION['id'] ?>">
                        <i class="bx bx-home-alt"></i>
                        <span class="item-nav">Início</span>
                    </a>
                </li>
                <li>
                    <a href="glossario.php?id=<?php echo $_SESSION['id'] ?>">
                        <i class="bx bx-book"></i>
                        <span class="item-nav">Glossário</span>
                    </a>
                </li>
                <li>
                    <a href='perfil.php?id=<?php echo $_SESSION['id'] ?>'>
                        <i class="bx bx-user"></i>
                        <span class="item-nav">Perfil</span>
                    </a>
                </li>
            </ul>
        </div>
        <?php

        if ($_SESSION['nome'] != '') {
            echo "<div class='usuario'>";
            echo    "<img id='user-def-nav' src='img/" . $usuario['imagem_usuario'] . "' alt=''>";
            echo    "<div class='subclass-usuario'>";
            echo        "<p class='user-nome'>" . $usuario['nome_usuario'] ."</p>";
            echo        "<p id='user-nivel-acesso'>" . $usuario['tipo_usuario'] . "</p>";
            echo    "</div>";
            echo    "<div id='botao-acoes'>";
            if ($_SESSION['nome'] != '') {
                echo "<a href='logout.php'><button id='nav-sair'>Sair</button></a>";
            }
            echo    "</div>";
            echo "</div>";
        }

        if ($_SESSION['nome'] == '') {
            echo "<a href='login.php'><button id='nav-entrar'>Entrar</button></a>";
        }
        ?>
    </nav>
    <main class="main-content">
        <form id="form-exclusao" action="" method="POST">
            <h1 id="h1-exclusao">Excluir sua conta</h1>
            <h2 id="h2-exclusao">Insira sua senha para confirmar a exclusão da conta</h2>
            <input type="password" id="input-exclusao" name="senha">
            <div id='checkbox'>
                <input type='checkbox' onclick='mostrarSenha()'> Mostrar senha
            </div>

            <?php
            if (!isset($_SESSION['id'])) {
                header('Location: login.php');
                exit();
            }
            
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $senha_inserida = $_POST["senha"];
                $id_usuario = $_SESSION['id'];

                $query = "SELECT * FROM usuarios WHERE id_usuario = ?";
                $stmt = mysqli_prepare($conexao, $query);
                mysqli_stmt_bind_param($stmt, "i", $id_usuario);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if ($result->num_rows > 0) {
                    $usuario = mysqli_fetch_assoc($result);
            
                    if (password_verify($senha_inserida, $usuario['senha_usuario'])) {
                        $query = "DELETE FROM usuarios WHERE id_usuario= ?"; 
                        $stmt = $conexao->prepare($query);
                        $stmt->bind_param("i", $id);

                        if ($stmt->execute()) {
                            header('Location: logout.php?exc=1');
                        } else {
                            echo "Erro ao excluir usuário: ".$conexao->error;
                        }

                        $stmt->close();

                    } else {
                        echo "<p style='color: red;'>Senha incorreta</p>";
                    }
                } else {
                    echo "<p style='color: red;'>Usuário não encontrado</p>";
                }
            }
            ?>

            <div id="div-exclusao">
                <button id="confirmar" type="submit">Excluir conta</button>
            </div>
            <p id="p-exclusao">ATENÇÃO: Ao clicar em "Excluir conta" sua conta será excluída permanentemente!</p>
        </form>
    </main>
</body>

<script>
    function mostrarSenha() {
        var x = document.getElementById("input-exclusao");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>

<style>
    #form-exclusao{
        display: flex;
        width: 500px;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        background-color: rgb(75, 75, 75);
        box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        border-radius: 10px;
        padding: 15px;
    }
    #h1-exclusao{
        color: white;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 36px;
        margin-top: 15px;
    }
    #h2-exclusao{
        color: rgb(210, 210, 210);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 17px;
        font-weight: 400;
        margin-bottom: 15px
    }
    #input-exclusao{
        text-align: center;
        border-radius: 5px;
        border: none;
        background-color: rgb(42, 42, 42);
        color: rgba(255, 255, 255, 0.803);
        width: 330px;
        height: 40px;
        margin-bottom: 5px;
        padding-left: 10px;
        padding-right: 10px;
        font-size: 22px;
    }
    #checkbox{
        color: white;
    }
    #confirmar{
        background-color: #ff3232;
        border: none;
        width: 120px;
        border-radius: 5px;
        height: 40px;
        color: white;
        font-weight: 500;
        font-size: 15px;
        transition: all 0.3s;
        cursor: pointer;
        margin-bottom: 15px;
        margin-top: 17px;
    }

    #confirmar:hover{
        background-color: #ff5c5c;
    }
    #p-exclusao{
        width: 350px;
        text-align: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-weight: 600;
        color: rgb(190, 190, 190);
        margin-bottom: 15px;
    }
</style>

</html>