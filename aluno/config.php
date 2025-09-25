<?php
session_start();

include '../conexao.php';

if (!isset($_SESSION['aluno_id'])) {
    header("Location: ../login.php");
    exit();
}

$aluno_id = $_SESSION['aluno_id'];

// Buscar dados do aluno
$sql = "SELECT nome, email, foto FROM alunos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$result = $stmt->get_result();
$aluno = $result->fetch_assoc();

$nome  = $aluno['nome'] ?? 'Aluno';
$email = $aluno['email'] ?? 'email@exemplo.com';
$foto  = '../img/fotos_alunos/padrao.png';

if (!empty($aluno['foto']) && file_exists('../' . $aluno['foto'])) {
    $foto = '../' . $aluno['foto'];
}

// Buscar configurações do aluno
$sql = "SELECT * FROM configuracoes WHERE aluno_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $config = $result->fetch_assoc();
} else {
    // Criar registro padrão na tabela configuracoes
    $config_padrao = [
        'manter_logado' => 0,
        'corretor_automatico' => 1,
        'permissao_perfil' => 'todos',
        'notificacoes' => 1
    ];
    $config = $config_padrao;

    $sql_insert = "INSERT INTO configuracoes (aluno_id, manter_logado, corretor_automatico, permissao_perfil, notificacoes) 
                   VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("iiisi", 
        $aluno_id,
        $config_padrao['manter_logado'],
        $config_padrao['corretor_automatico'],
        $config_padrao['permissao_perfil'],
        $config_padrao['notificacoes']
    );
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Configurações</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style/config.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <a href="index.php">
            <img src="../img/logopreta2-removebg-preview.png" alt="Logotipo">
        </a>
    </div>

    <a href="#" class="nav-link active" data-target="geral-box">
        <i class="fa-solid fa-gears icon"></i> Geral
    </a>
    <a href="#" class="nav-link" data-target="aparencia-box">
        <i class="fa-solid fa-pen"></i> Aparência
    </a>
    <a href="#" class="nav-link" data-target="notificacao-box">
        <i class="fa-solid fa-bell icon"></i> Notificação
    </a>
    <a href="#" class="nav-link" data-target="seguranca-box">
        <i class="fa-solid fa-shield icon"></i> Segurança
    </a>
    <a href="#" class="nav-link" data-target="acessibilidade-box">
        <i class="fa-solid fa-eye icon"></i> Acessibilidade
    </a>
    <a href="#" class="nav-link" data-target="perfil-box">
        <i class="fa-solid fa-user"></i> Perfil
    </a>
    <a href="#" class="nav-link" data-target="mensagem-box">
        <i class="fa-solid fa-envelope"></i> Mensagens
    </a>
</div>

<div class="content">
    <div class="top-nav">
        <button id="theme-toggle" class="theme-button">
            <i class="fas fa-sun"></i>
        </button>
        <div class="right-items">
            <div class="user-info">
                <img src="<?php echo htmlspecialchars($foto); ?>" alt="Foto do Aluno">
                <span><?php echo htmlspecialchars($nome); ?></span>
            </div>
        </div>
    </div>

    <h2>Configurações</h2>

    <form action="salvar_config.php" method="POST">
        <div id="geral-box" class="content-box">
            <h2>Geral</h2>
            <input type="checkbox" name="manter_logado" <?php if($config['manter_logado']) echo 'checked'; ?>> Manter a atual conta logada<br>

            <input type="checkbox" name="corretor_automatico" <?php if($config['corretor_automatico']) echo 'checked'; ?>> Habilitar corretor automático<br>

            <label for="permissao">Permissão para visualizar seu perfil:</label>
            <select name="permissao_perfil" required>
                <option value="todos" <?php if($config['permissao_perfil']=='todos') echo 'selected'; ?>>Todos</option>
                <option value="amigos" <?php if($config['permissao_perfil']=='amigos') echo 'selected'; ?>>Amigos</option>
                <option value="alunosclasse" <?php if($config['permissao_perfil']=='alunosclasse') echo 'selected'; ?>>Alunos da mesma classe</option>
            </select>
        </div>

        <div id="notificacao-box" class="content-box" style="display: none;">
            <h2>Notificação</h2>
            <input type="checkbox" name="notificacoes" <?php if($config['notificacoes']) echo 'checked'; ?>> Receber notificações
        </div>

        <button type="submit">Salvar Configurações</button>
    </form>

    <div id="perfil-box" class="content-box" style="display: none;">
        <h2>Perfil</h2>
        <div class="profile-header">
            <img src="<?php echo htmlspecialchars($foto); ?>" alt="Foto do aluno" class="profile-picture">
            <div class="profile-info">
                <h2 class="student-name"><?php echo htmlspecialchars($nome); ?></h2>
                <p class="student-email"><?php echo htmlspecialchars($email); ?></p>
            </div>
        </div>

        <h3>Alterar Foto de Perfil</h3>
        <form action="upload_foto.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="nova_foto" accept="image/*" required>
            <button type="submit">Enviar nova foto</button>
        </form>
    </div>
</div>

<script src="config.js"></script>
</body>
</html>
