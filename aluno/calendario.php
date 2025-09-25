<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['aluno_id'])) {
    header("Location: ../login.php");
    exit();
}

$aluno_id = $_SESSION['aluno_id'];

// Buscar nome e foto do aluno logado
$stmt = $conn->prepare("SELECT nome, foto FROM alunos WHERE id = ?");
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$result = $stmt->get_result();
$aluno = $result->fetch_assoc();

$nome_aluno = $aluno['nome'];
$foto_aluno = !empty($aluno['foto']) 
    ? "../img/fotos_alunos/" . $aluno['foto'] 
    : "../img/fotos_alunos/padrao.png";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Calendário de Atividades</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
      integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />

    <link rel="stylesheet" href="../style/calendario.css" />
    <style>
        /* Ajustes básicos do calendário */
        #calendar-placeholder {
            max-width: 100%;
            margin: 20px auto;
            background: #fff;
            border-radius: 10px;
            padding: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .fc-event {
            font-size: 14px;
            padding: 2px 4px;
            border-radius: 5px;
            cursor: pointer;
        }
        .fc-day-today {
            background-color: #e9f5ff !important;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo">
            <a href="index.php">
                <img src="../img/logopreta2.png" alt="Logotipo">
            </a>
        </div>
        <a href="index.php">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="disciplinas.php">
            <i class="fas fa-book"></i> Disciplinas
        </a>
        <a href="notas.php">
            <i class="fas fa-graduation-cap"></i> Notas
        </a>
        <a href="calendario.php" class="active">
            <i class="fas fa-calendar-alt"></i> Calendário
        </a>
        <a href="atividades.php">
            <i class="fas fa-tasks"></i> Atividades
        </a>
        <a href="mensagens.php">
            <i class="fas fa-envelope"></i> Mensagens
        </a>
        <a href="config.php">
            <i class="fas fa-cog"></i> Configurações
        </a>
    </div>

    <div class="content">
        <div class="top-nav">
            <button id="theme-toggle" class="theme-button">
                <i class="fas fa-sun"></i>
            </button>
            <div class="right-items">
                <a href="#" class="notification-link">
                    <div class="notifications">
                        <i class="fas fa-bell"></i>
                    </div>
                </a>
                <div class="user-info">
                    <a href="perfil.php">
                        <img src="<?php echo $foto_aluno; ?>" alt="Foto do Aluno">
                    </a>
                    <span><?php echo htmlspecialchars($nome_aluno); ?></span>
                </div>
            </div>
        </div>

        <div class="dashboard-header">
            <h1>Calendário</h1>
        </div>

        <div id="dynamic-calendar-container" class="main-section show">
            <div id="calendar-placeholder"></div>
        </div>
    </div>

    <script>
        const alunoId = <?php echo json_encode($aluno_id); ?>;
    </script>

    <script src="calendario.js"></script>
    <script src="main.js"></script>
</body>
</html>