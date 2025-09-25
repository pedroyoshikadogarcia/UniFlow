<?php
session_start();

// Proteção do aluno
require_once __DIR__ . '/../includes/protecao_aluno.php';
require_once __DIR__ . '/../conexao.php';

$aluno_id = $_SESSION['aluno_id'];
$aluno_nome = $_SESSION['aluno_nome'] ?? 'Aluno';

// Recuperar informações adicionais do aluno, como foto
$sql = "SELECT foto, email FROM alunos WHERE id = ?";
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
$email = $aluno['email'] ?? 'email@exemplo.com';

// Consulta para buscar as disciplinas e notas do aluno
$sql = "
SELECT d.*, p.nome AS professor_nome, m.id AS matricula_id
FROM disciplinas d
JOIN professores p ON d.professor_id = p.id
JOIN matriculas m ON d.id = m.disciplina_id
WHERE m.aluno_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$disciplinas = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minhas Notas</title>
    <link rel="stylesheet" href="../style/style_notas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        <h1>Minhas Notas</h1>
        <p>Data: <?php echo date('d/m/Y'); ?></p>
    </div>

    <div class="dashboard-cards">
        <?php
        if ($disciplinas->num_rows > 0) {
            while ($disciplina = $disciplinas->fetch_assoc()) {

                // Consulta notas por matrícula
                $sqlNotas = "SELECT avaliacao, nota FROM notas WHERE matricula_id = ?";
                $stmtNotas = $conn->prepare($sqlNotas);
                $stmtNotas->bind_param("i", $disciplina['matricula_id']);
                $stmtNotas->execute();
                $notas = $stmtNotas->get_result();
        ?>
            <div class="card">
                <div class="card-info">
                    <span><?php echo htmlspecialchars($disciplina['nome']); ?></span>
                    <p><i class="fas fa-book-open"></i> <?php echo htmlspecialchars($disciplina['descricao']); ?></p>
                    <p><i class="fas fa-user"></i> <?php echo htmlspecialchars($disciplina['professor_nome']); ?></p>
                    <p><i class="far fa-clock"></i> <?php echo htmlspecialchars($disciplina['horario']); ?></p>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($disciplina['local']); ?></p>
                    <p><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($disciplina['creditos']); ?> créditos</p>
                    <h4>Notas:</h4>
                    <ul>
                        <?php
                        if ($notas->num_rows > 0) {
                            while($nota = $notas->fetch_assoc()) {
                                echo "<li>".htmlspecialchars($nota['avaliacao']).": <strong>".htmlspecialchars($nota['nota'])."</strong></li>";
                            }
                        } else {
                            echo "<li>Sem notas cadastradas</li>";
                        }
                        ?>
                    </ul>
                </div>
                <div class="card-details">
                    <a href="detalhes_disciplina.php?id=<?php echo $disciplina['id']; ?>" class="button">Ver Detalhes</a>
                </div>
            </div>
        <?php
            }
        } else {
            echo "<p>Nenhuma disciplina encontrada.</p>";
        }
        ?>
    </div>
</div>


<script src="script.js"></script>
</body>
</html>
