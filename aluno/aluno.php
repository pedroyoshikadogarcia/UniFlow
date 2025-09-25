<?php
session_start();

require_once __DIR__ . '../includes/protecao_aluno.php';
require_once '../conexao.php';


$aluno_id = $_SESSION['aluno_id'];



// Buscar dados do aluno
$sql = "SELECT * FROM alunos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$result = $stmt->get_result();
$aluno = $result->fetch_assoc();

if (!$aluno) {
    echo "Aluno não encontrado!";
    exit();
}
if (!empty($aluno['foto'])) {
    $caminho_foto = '../' . $aluno['foto'];
    if (file_exists($caminho_foto)) {
        $foto = $caminho_foto;
    }
}
$foto = $aluno['foto']; // usa direto a imagem salva no banco

// Buscar matrículas
$sql_matriculas = "SELECT id FROM matriculas WHERE aluno_id = ?";
$stmt = $conn->prepare($sql_matriculas);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$matriculas_result = $stmt->get_result();

$matricula_ids = [];
while ($row = $matriculas_result->fetch_assoc()) {
    $matricula_ids[] = $row['id'];
}

// Calcular Nota (média das notas)
$nota = 0;

if (!empty($matricula_ids)) {
    $placeholders = implode(',', array_fill(0, count($matricula_ids), '?'));
    $types = str_repeat('i', count($matricula_ids));

    $sql_notas = "SELECT AVG(nota) AS media FROM notas WHERE matricula_id IN ($placeholders)";
    $stmt = $conn->prepare($sql_notas);
    $stmt->bind_param($types, ...$matricula_ids);
    $stmt->execute();
    $result_notas = $stmt->get_result();
    $nota = $result_notas->fetch_assoc()['media'] ?? 0;
}

// Calcular Freq (percentual de frequência)
$presencas = 0;
$total_aulas = 0;
$freq = 0;

if (!empty($matricula_ids)) {
    $sql_freq = "SELECT presente FROM frequencia WHERE matricula_id IN ($placeholders)";
    $stmt = $conn->prepare($sql_freq);
    $stmt->bind_param($types, ...$matricula_ids);
    $stmt->execute();
    $result_freq = $stmt->get_result();

    while ($row = $result_freq->fetch_assoc()) {
        $total_aulas++;
        if ($row['presente']) {
            $presencas++;
        }
    }
    if ($total_aulas > 0) {
        $freq = ($presencas / $total_aulas) * 100;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Perfil do Aluno</title>
    <link rel="stylesheet" href="style/style_perfil.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body>
<div class="sidebar">
    <div class="logo">
        <a href="index.php">
            <img src="img/logopreta2-removebg-preview.png" alt="Logotipo">
        </a>
    </div>
    <a href="disciplinas.php"><i class="fas fa-book"></i> Disciplinas</a>
    <a href="notas.php"><i class="fas fa-graduation-cap"></i> Notas</a>
    <a href="calendario.php"><i class="fas fa-calendar-alt"></i> Calendário</a>
    <a href="atividades.php"><i class="fas fa-tasks"></i> Atividades</a>
    <a href="#"><i class="fas fa-envelope"></i> Mensagens</a>
    <a href="config.php"><i class="fas fa-cog"></i> Configurações</a>
</div>

<div class="content">
    <div class="top-nav">
        <button id="theme-toggle" class="theme-button"><i class="fas fa-sun"></i></button>
        <div class="right-items">
            <a href="#" class="notification-link">
                <div class="notifications"><i class="fas fa-bell"></i></div>
            </a>
            <div class="user-info">
                <img src="<?php echo htmlspecialchars($foto); ?>" alt="Foto do Aluno">
                <span><?php echo htmlspecialchars($aluno['nome']); ?></span>
            </div>
        </div>
    </div>

    <div class="top-info-container">
        <div class="student-profile">
            <div class="profile-header">
                <img src="<?php echo htmlspecialchars($foto); ?>" alt="Foto do aluno" class="profile-picture" />
                <div class="profile-info">
                    <h2 class="student-name"><?php echo htmlspecialchars($aluno['nome']); ?></h2>
                    <p class="student-email"><?php echo htmlspecialchars($aluno['email']); ?></p>
                    <div class="profile-actions">
                        <button class="edit-button"><i class="fas fa-edit"></i> Editar perfil</button>
                        <button class="settings-button"><i class="fas fa-cog"></i> Configurações</button>
                    </div>
                </div>
            </div>

            <div class="academic-info">
                <h3>Informações Acadêmicas</h3>
                <p><strong>Curso:</strong> <?php echo htmlspecialchars($aluno['curso']); ?></p>
                <p><strong>Semestre:</strong> <?php echo htmlspecialchars($aluno['semestre']); ?>º Semestre</p>
                <p><strong>Período:</strong> <?php echo htmlspecialchars($aluno['periodo']); ?></p>
                <p><strong>Status:</strong> <span class="status ativo"><?php echo htmlspecialchars($aluno['status']); ?></span></p>
                <p><strong>Nota:</strong> <?php echo number_format($nota, 2); ?></p>
                <p><strong>Freq:</strong> <?php echo number_format($freq, 2); ?>%</p>
            </div>

            <div class="quick-access">
                <h3>Acesso Rápido</h3>
                <div class="quick-buttons">
                    <button><i class="fas fa-calendar-alt"></i> Horários</button>
                    <button><i class="fas fa-file-alt"></i> Boletim</button>
                    <button><i class="fas fa-book"></i> Disciplinas</button>
                    <button><i class="fas fa-tasks"></i> Atividades</button>
                </div>
            </div>
        </div>

        <div class="profile-container">
            <div class="profile-card">
                <div class="profile-image-container">
                    <img src="<?php echo htmlspecialchars($foto); ?>" alt="Foto do Aluno" class="profile-image">
                </div>
                <h2><?php echo htmlspecialchars($aluno['nome']); ?></h2>
                <p>RA: <?php echo htmlspecialchars($aluno['matricula']); ?></p>
                <div class="info-item">
                    <strong>Curso:</strong>
                    <p><?php echo htmlspecialchars($aluno['curso']); ?></p>
                </div>
                <div class="info-item">
                    <strong>Unidade:</strong>
                    <p>Etec Adhemar Batista</p>
                </div>
                <div class="info-item">
                    <strong>Validade:</strong>
                    <p><?php echo date('d/m/Y', strtotime($aluno['validade'])); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="perfil-aluno.js"></script>
</body>
</html>
