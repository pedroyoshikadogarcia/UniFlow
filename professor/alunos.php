<?php
include '../includes/protecao_professor.php';
include '../conexao.php';

$professor_id = $_SESSION['usuario_id'];

// Buscar nome e foto do professor
$sql_professor = "SELECT nome, foto FROM professores WHERE id = ?";
$stmt_prof = $conn->prepare($sql_professor);
$stmt_prof->bind_param("i", $professor_id);
$stmt_prof->execute();
$result_prof = $stmt_prof->get_result();
$professor = $result_prof->fetch_assoc();

$nome_professor = $professor['nome'] ?? 'Professor';
$foto_professor = "../img/usuario_padrao.jpg"; // padrão
if (!empty($professor['foto'])) {
    $caminho_foto = "../img/fotos_professores/" . basename($professor['foto']);
    if (file_exists($caminho_foto)) {
        $foto_professor = $caminho_foto;
    }
}

// Buscar todas as disciplinas do professor
$sql_disciplinas = "SELECT id, nome FROM disciplinas WHERE professor_id = ?";
$stmt_disc = $conn->prepare($sql_disciplinas);
$stmt_disc->bind_param("i", $professor_id);
$stmt_disc->execute();
$result_disc = $stmt_disc->get_result();
$disciplinas = $result_disc->fetch_all(MYSQLI_ASSOC);

// Preparar array com IDs das disciplinas
$disciplinas_ids = array_column($disciplinas, 'id');

$alunos = [];
if (!empty($disciplinas_ids)) {
    $in = implode(',', array_map('intval', $disciplinas_ids));

    // Buscar alunos de todas as disciplinas do professor com frequência
    $sql_alunos = "
    SELECT a.id AS aluno_id, a.nome, a.matricula, a.email, d.nome AS disciplina_nome,
           IFNULL(SUM(f.presente)/COUNT(f.id)*100, 0) AS frequencia
    FROM alunos a
    JOIN matriculas m ON a.id = m.aluno_id
    JOIN disciplinas d ON d.id = m.disciplina_id
    LEFT JOIN frequencia f ON f.matricula_id = m.id
    WHERE m.disciplina_id IN ($in)
    GROUP BY a.id, d.nome
    ORDER BY d.nome, a.nome
    ";
    $result_alunos = $conn->query($sql_alunos);
    if ($result_alunos) {
        $alunos = $result_alunos->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Gerenciar Alunos</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="../style_professor/style_alunos.css">

</head>
<body>
<div class="sidebar">
    <div class="logo">
        <a href="index.php"><img src="../img/logopreta2.png" alt="Logotipo"></a>
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

<div class="content">
    <div class="top-nav">
        <button id="theme-toggle" class="theme-button"><i class="fas fa-sun"></i></button>
        <div class="right-items">
            <a href="#" class="notification-link"><div class="notifications"><i class="fas fa-bell"></i></div></a>
            <div class="user-info">
                <a href="perfil.php" class="user-profile-link">
                    <img src="<?php echo htmlspecialchars($foto_professor); ?>" alt="Foto do Professor">
                    <span><?php echo htmlspecialchars($nome_professor); ?></span>
                </a>
            </div>
        </div>
    </div>

    <div class="dashboard-header">
        <h1>Gerenciar alunos</h1>
        <p>Data: <?php echo date("d/m/Y"); ?></p>
    </div>

    <div class="main-student-management-card">
        <div class="main-card-content">
            <div class="section-title-row">
                <h3>Alunos das Disciplinas</h3>
            </div>

            <div class="students-table-wrapper">
                <div class="table-header-row">
                    <div class="header-col student-name-header">NOME</div>
                    <div class="header-col student-matricula-header">MATRÍCULA</div>
                    <div class="header-col student-email-header">E-MAIL</div>
                    <div class="header-col student-discipline-header">DISCIPLINA</div>
                    <div class="header-col student-frequency-header">FREQUÊNCIA</div>
                </div>

                <?php if (!empty($alunos)): ?>
                    <?php foreach ($alunos as $aluno): ?>
                        <a href="#" class="student-row-link">
                            <div class="student-entry-row">
                                <div class="student-data student-name-col"><?php echo htmlspecialchars($aluno['nome']); ?></div>
                                <div class="student-data student-matricula-col"><?php echo htmlspecialchars($aluno['matricula']); ?></div>
                                <div class="student-data student-email-col"><?php echo htmlspecialchars($aluno['email']); ?></div>
                                <div class="student-data student-discipline-col"><?php echo htmlspecialchars($aluno['disciplina_nome']); ?></div>
                                <div class="student-data student-frequency-col"><?php echo round($aluno['frequencia']); ?>%</div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Nenhum aluno encontrado para suas disciplinas.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="main.js"></script>
</body>
</html>
