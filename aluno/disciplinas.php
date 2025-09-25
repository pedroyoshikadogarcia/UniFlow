<?php
session_start();
include '../conexao.php'; 

// Verifica se aluno está logado
if (!isset($_SESSION['aluno_id'])) {
    echo "Erro: aluno não está logado.";
    exit;
}

$aluno_id = $_SESSION['aluno_id'];
$aluno_nome = $_SESSION['aluno_nome'] ?? 'Aluno';

// Buscar foto do aluno
$sql_foto = "SELECT foto FROM alunos WHERE id = ?";
$stmt_foto = $conn->prepare($sql_foto);
$stmt_foto->bind_param("i", $aluno_id);
$stmt_foto->execute();
$result_foto = $stmt_foto->get_result();
$aluno = $result_foto->fetch_assoc();

// Caminho padrão
$foto = '../img/fotos_alunos/padrao.png';

// Se tiver foto no banco e existir no servidor, atualiza o caminho
if (!empty($aluno['foto'])) {
    $caminhoFotoServidor = __DIR__ . '/../img/fotos_alunos/' . $aluno['foto'];
    if (file_exists($caminhoFotoServidor)) {
        $foto = '../img/fotos_alunos/' . $aluno['foto'];
    }
}

// Buscar disciplinas, professores e notas
$sql = "SELECT d.*, p.nome AS professor_nome
        FROM disciplinas d
        JOIN professores p ON d.professor_id = p.id
        JOIN matriculas m ON d.id = m.disciplina_id
        WHERE m.aluno_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Disciplinas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style/style_disciplinas.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <a href="index.php">
            <img src="../img/logopreta2-removebg-preview.png" alt="Logotipo">
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
                <img src="<?php echo htmlspecialchars($foto); ?>" alt="Foto do Aluno">
                <span><?php echo htmlspecialchars($aluno_nome); ?></span>
            </div>
        </div>
    </div>

    <div class="dashboard-header">
        <h1>Minhas Disciplinas</h1>
        <p>Data: <?php echo date('d/m/Y'); ?></p>
    </div>

    <div class="dashboard-cards">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>
                <a href="pagina_disciplinas.php?id=<?php echo $row['id']; ?>" class="card-link">
                    <div class="card">
                        <div class="card-info">
                            <span><?php echo htmlspecialchars($row['nome']); ?></span>
                            <p><i class="fas fa-book-open"></i> <?php echo htmlspecialchars($row['descricao']); ?></p>
                            <p><i class="fas fa-user"></i> <?php echo htmlspecialchars($row['professor_nome']); ?></p>
                            <p><i class="far fa-clock"></i> <?php echo htmlspecialchars($row['horario']); ?></p>
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['local']); ?></p>
                            <p><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($row['creditos']); ?> créditos</p>
                        </div>
                        <div class="card-details">
                            <a href="aula_detalhes.php?id=<?php echo $row['id']; ?>" class="button">Ver Detalhes</a>
                        </div>
                    </div>
                </a>
                <?php
            }
        } else {
            echo "<p>Nenhuma disciplina cadastrada para este aluno.</p>";
        }
        ?>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
