<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

$professor_id = $_SESSION['usuario_id'];

// Buscar dados do professor
$stmt = $conn->prepare("SELECT nome, foto FROM professores WHERE id = ?");
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$resProf = $stmt->get_result()->fetch_assoc();
$professor_nome = $resProf['nome'];
$professor_foto = !empty($resProf['foto']) ? "../" . $resProf['foto'] : "../img/fotos_professores/padrao.png";

// Buscar disciplinas/turmas do professor
$query = "
    SELECT 
        d.id AS disciplina_id,
        d.nome AS disciplina_nome,
        COUNT(DISTINCT m.aluno_id) AS total_alunos,
        COUNT(DISTINCT a.id) AS total_atividades
    FROM disciplinas d
    LEFT JOIN matriculas m ON d.id = m.disciplina_id
    LEFT JOIN atividades a ON d.id = a.disciplina_id
    WHERE d.professor_id = ?
    GROUP BY d.id
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$result = $stmt->get_result();

$disciplinas = [];
while ($row = $result->fetch_assoc()) {
    $disciplinas[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Turmas - Professor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="../style_professor/style_turmas.css" />
    <link rel="stylesheet" href="../style/global.css" />
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <a href="index.php">
                <img src="../img/logopreta2.png" alt="Logotipo" />
            </a>
        </div>
        <a href="turmas.php" class="active"><i class="fas fa-book"></i> Turmas</a>
        <a href="alunos.php"><i class="fas fa-graduation-cap"></i> Alunos</a>
        <a href="atividades.php"><i class="fas fa-tasks"></i> Atividades</a>
        <a href="notas.php"><i class="fas fa-clipboard-list"></i> Notas</a>
        <a href="chamada.php"><i class="fas fa-user-check"></i> Chamada</a>
        <a href="calendario.php"><i class="fas fa-calendar-alt"></i> Calendário</a>
        <a href="mensagem.php"><i class="fas fa-envelope"></i> Mensagens</a>
        <a href="config.php"><i class="fas fa-cog"></i> Configurações</a>
    </div>

    <!-- Conteúdo principal -->
    <div class="content" id="turmas-section">

        <!-- Barra superior -->
        <div class="top-nav">
            <button id="theme-toggle" class="theme-button"><i class="fas fa-sun"></i></button>
            <div class="right-items">
                <a href="#" class="notification-link">
                    <div class="notifications"><i class="fas fa-bell"></i></div>
                </a>
                <div class="user-info">
                    <a href="perfil.php" class="user-profile-link">
                        <img src="<?php echo $professor_foto; ?>" alt="Foto do Professor" />
                        <span><?php echo htmlspecialchars($professor_nome); ?></span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Cabeçalho -->
        <div class="dashboard-header">
            <h1>Gerenciamento de Turmas</h1>
            <p>Data: <?php echo date("d/m/Y"); ?></p>
        </div>

        <!-- Lista de turmas -->
        <div class="turmas-container">
            <?php if (empty($disciplinas)): ?>
                <p>Nenhuma turma encontrada para este professor.</p>
            <?php else: ?>
                <?php foreach ($disciplinas as $disc): ?>
                    <a href="aula_detalhes.php?disciplina_id=<?php echo $disc['disciplina_id']; ?>" class="turma-card-link">
                        <div class="turma-card">
                            <div class="turma-header">
                                <div class="icon-wrapper"><i class="fas fa-users"></i></div>
                                <div class="turma-text">
                                    <h2><?php echo htmlspecialchars($disc['disciplina_nome']); ?></h2>
                                    <p>Turma</p>
                                </div>
                            </div>
                            <div class="turma-stats">
                                <div>
                                    <strong><?php echo $disc['total_alunos']; ?></strong>
                                    <span>ALUNOS</span>
                                </div>
                                <div>
                                    <strong><?php echo $disc['total_atividades']; ?></strong>
                                    <span>ATIVIDADES</span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>

    <script src="main.js"></script>

</body>
</html>
