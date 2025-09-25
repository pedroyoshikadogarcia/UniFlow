<?php
include '../includes/protecao_professor.php';
include '../conexao.php';

$professor_id = $_SESSION['usuario_id'];

// Buscar nome e foto do professor
$sql = "SELECT nome, foto FROM professores WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$result = $stmt->get_result();
$professor = $result->fetch_assoc();

$nome_professor = $professor['nome'] ?? 'Professor';

// Verifica se a foto existe em .jpg ou .png
$foto_professor = "../img/usuario_padrao.jpg";
if (!empty($professor['foto'])) {
    $nome_arquivo = pathinfo($professor['foto'], PATHINFO_FILENAME);
    $caminho_jpg = "../img/fotos_professores/{$nome_arquivo}.jpg";
    $caminho_png = "../img/fotos_professores/{$nome_arquivo}.png";

    if (file_exists($caminho_jpg)) {
        $foto_professor = $caminho_jpg;
    } elseif (file_exists($caminho_png)) {
        $foto_professor = $caminho_png;
    }
}

// PRÓXIMAS ATIVIDADES
$data_hoje = date('Y-m-d');
$sql_atividades = "
    SELECT a.titulo, a.prazo, d.nome AS disciplina
    FROM atividades a
    JOIN disciplinas d ON a.disciplina_id = d.id
    WHERE d.professor_id = ?
      AND a.status = 'pendente'
      AND a.prazo IS NOT NULL
      AND a.prazo >= ?
    ORDER BY a.prazo ASC
    LIMIT 5
";
$stmt_atividades = $conn->prepare($sql_atividades);
$stmt_atividades->bind_param("is", $professor_id, $data_hoje);
$stmt_atividades->execute();
$result_atividades = $stmt_atividades->get_result();

// TURMAS RECENTES
$sql_turmas = "
    SELECT d.id, d.nome, COUNT(m.aluno_id) AS total_alunos,
           (SELECT COUNT(*) FROM atividades a WHERE a.disciplina_id = d.id) AS total_atividades,
           d.horario
    FROM disciplinas d
    LEFT JOIN matriculas m ON m.disciplina_id = d.id
    WHERE d.professor_id = ?
    GROUP BY d.id
    ORDER BY d.id DESC
    LIMIT 5
";
$stmt_turmas = $conn->prepare($sql_turmas);
$stmt_turmas->bind_param("i", $professor_id);
$stmt_turmas->execute();
$result_turmas = $stmt_turmas->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Página Principal</title>
    
    <script>
        (function() {
            const currentTheme = localStorage.getItem('theme') || 'light';
            if (currentTheme === 'dark') {
                document.body.classList.add('dark-theme');
            }
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="../style_professor/style.css" />
    <link rel="stylesheet" href="../style_professor/global.css" />
</head>
<body>

    <!--SIDEBAR-->
    <div class="sidebar">
        <div class="logo">
            <a href="index.php">
                <img src="../img/logopreta2.png" alt="Logotipo" />
            </a>
        </div>
        <a href="turmas.php"><i class="fas fa-book"></i> Turmas</a>
        <a href="alunos.php"><i class="fas fa-graduation-cap"></i> Alunos</a>
        <a href="atividades.php"><i class="fas fa-tasks"></i> Atividades</a>
        <a href="notas.php"><i class="fas fa-clipboard-list"></i> Notas</a>
        <a href="chamada.php"><i class="fas fa-user-check"></i> Chamada</a>
        <a href="calendario.php"><i class="fas fa-calendar-alt"></i> Calendário</a>
        <a href="mensagem.php"><i class="fas fa-envelope"></i> Mensagens</a>
        <a href="config.php"><i class="fas fa-cog"></i> Configurações</a>
    </div>

    <!--TOPBAR-->
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
                    <a href="perfil.php" class="user-profile-link">
                        <img src="<?php echo $foto_professor; ?>" alt="Foto do Professor" />
                        <span><?php echo htmlspecialchars($nome_professor); ?></span>
                    </a>
                </div>
            </div>
        </div>

        <!--NORMAL DA PARTE INICIAL-->
        <div class="dashboard-header">
            <h1>Bem-vindo(a) Prof. <?php echo htmlspecialchars($nome_professor); ?></h1>
            <p>Data: <?php echo date("d/m/Y"); ?></p>
        </div>

        <div class="dashboard-cards">
            <!-- PRÓXIMAS ATIVIDADES -->
            <div class="card">
                <h2 class="card-title">Próximas Atividades</h2>
                <?php if ($result_atividades->num_rows > 0): ?>
                    <?php while ($atividade = $result_atividades->fetch_assoc()): ?>
                        <div class="card-content">
                            <i class="fas fa-calendar-check card-icon"></i>
                            <h3><?php echo htmlspecialchars($atividade['titulo']); ?></h3>
                            <p>Disciplina: <?php echo htmlspecialchars($atividade['disciplina']); ?> | Prazo: <?php echo date("d/m/Y", strtotime($atividade['prazo'])); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="card-content">
                        <i class="fas fa-calendar-check card-icon"></i>
                        <h3>Nenhuma atividade próxima</h3>
                        <p>Não há atividades agendadas para os próximos dias.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- TURMAS RECENTES -->
            <div class="card">
                <h2 class="card-title">Turmas Recentes</h2>
                <?php if ($result_turmas->num_rows > 0): ?>
                    <?php while ($turma = $result_turmas->fetch_assoc()): ?>
                        <div class="recent-class">
                            <div class="class-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="class-info">
                                <strong><?php echo htmlspecialchars($turma['nome']); ?></strong>
                                <p><?php echo $turma['total_alunos']; ?> alunos • <?php echo $turma['total_atividades']; ?> atividades</p>
                            </div>
                            <span class="class-period"><?php echo htmlspecialchars($turma['horario']); ?></span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Nenhuma turma registrada.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        const themeToggle = document.getElementById('theme-toggle');
        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme');
            const newTheme = document.body.classList.contains('dark-theme') ? 'dark' : 'light';
            localStorage.setItem('theme', newTheme);
        });
    </script>
</body>
</html>
