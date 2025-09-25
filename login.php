<?php
session_start();
include 'conexao.php';

$erro = '';

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($senha)) {

        //Verificar aluno
        $sql = "SELECT id, nome, senha FROM alunos WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if ($senha === trim($user['senha'])) {
                $_SESSION['aluno_id'] = $user['id'];
                $_SESSION['aluno_nome'] = $user['nome'];
                $_SESSION['tipo'] = 'aluno';
                header("Location: /uniflow/aluno/index.php");


                exit();
            } else {
                $erro = "Senha incorreta!";
            }

        } else {
            //verificar professor
            $sql = "SELECT id, nome, senha FROM professores WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if ($senha === trim($user['senha'])) { // mantém senha simples
                    $_SESSION['usuario_id'] = $user['id'];
                    $_SESSION['usuario_nome'] = $user['nome'];
                    $_SESSION['tipo'] = 'professor';
                    header("Location: professor/index.php");
                    exit();
                } else {
                    $erro = "Senha incorreta!";
                }
            } else {
                $erro = "Usuário não encontrado!";
            }
        }

    } else {
        $erro = "Preencha todos os campos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniFlow - Login</title>
    <link rel="stylesheet" href="style/login.css">
</head>
<body>
    <div class="container">
        <div class="image">
            <img src="img/a-removebg-preview.png" alt="Imagem de Login">
        </div>
        <div class="login">
            <h2>LOGIN</h2>
            <?php if (!empty($erro)) { echo "<p class='error'>$erro</p>"; } ?>
            <form action="" method="POST">
                <input type="email" name="email" placeholder="E-mail" required>
                <input type="password" name="password" placeholder="Senha" required>
                <div class="options">
                    <label class="checkbox">
                        <input type="checkbox"> Lembrar-me
                    </label>
                    <a href="inscricao.html">Esqueceu a senha?</a>
                </div>
                <button type="submit">Entrar</button>
            </form>
            <p>Não tem uma conta? <a href="#">Inscrever-se</a></p>
        </div>
    </div>
</body>
</html>
