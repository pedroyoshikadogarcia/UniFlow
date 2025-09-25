<?php
session_start();

require_once __DIR__ . '/../includes/protecao_aluno.php';
require_once __DIR__ . '/../conexao.php';

$aluno_id = $_SESSION['aluno_id'];
$aluno_nome = $_SESSION['aluno_nome'];

// Caminho padrão da foto
$foto = '../img/fotos_alunos/padrao.png';

// Recuperar foto do banco
$sql = "SELECT foto FROM alunos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$result = $stmt->get_result();
$aluno = $result->fetch_assoc();

// Se houver foto cadastrada e o arquivo existir, usa a foto do aluno
if (!empty($aluno['foto'])) {
    $caminho_foto = '../' . $aluno['foto'];
    if (file_exists($caminho_foto)) {
        $foto = $caminho_foto;
    }
}

// Buscar disciplinas, professores, notas e frequência corretamente
$sql_disciplinas = "
    SELECT d.id AS disciplina_id, d.nome AS disciplina_nome, d.descricao, p.nome AS professor_nome, d.horario, d.local, d.creditos
    FROM matriculas m
    JOIN disciplinas d ON m.disciplina_id = d.id
    JOIN professores p ON d.professor_id = p.id
    WHERE m.aluno_id = ?
";
$stmt = $conn->prepare($sql_disciplinas);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$result = $stmt->get_result();
$disciplinas = $result->fetch_all(MYSQLI_ASSOC);

// Para cada disciplina, calcular nota média e frequência corretamente
foreach ($disciplinas as &$d) {
    // Média das notas
    $sql_nota = "SELECT AVG(nota) AS media_nota FROM notas n
                 JOIN matriculas m ON n.matricula_id = m.id
                 WHERE m.aluno_id = ? AND m.disciplina_id = ?";
    $stmt = $conn->prepare($sql_nota);
    $stmt->bind_param("ii", $aluno_id, $d['disciplina_id']);
    $stmt->execute();
    $res_nota = $stmt->get_result()->fetch_assoc();
    $d['media_nota'] = $res_nota['media_nota'];

    // Frequência
    $sql_freq = "SELECT SUM(presente) AS presencas, COUNT(*) AS total_aulas
                 FROM frequencia f
                 JOIN matriculas m ON f.matricula_id = m.id
                 WHERE m.aluno_id = ? AND m.disciplina_id = ?";
    $stmt = $conn->prepare($sql_freq);
    $stmt->bind_param("ii", $aluno_id, $d['disciplina_id']);
    $stmt->execute();
    $res_freq = $stmt->get_result()->fetch_assoc();
    $d['frequencia'] = $res_freq['total_aulas'] > 0 ? ($res_freq['presencas'] / $res_freq['total_aulas']) * 100 : 0;
}
unset($d); // quebra referência

// ----------------------------------------------------
// NOVO CÓDIGO PARA AS PRÓXIMAS ATIVIDADES
// ----------------------------------------------------

try {
    $sql_atividades = "SELECT data, titulo, tipo FROM calendario_eventos WHERE aluno_id = ? AND data >= CURDATE() ORDER BY data ASC LIMIT 5";
    $stmt_atividades = $conn->prepare($sql_atividades);
    if ($stmt_atividades) {
        $stmt_atividades->bind_param("i", $aluno_id);
        $stmt_atividades->execute();
        $result_atividades = $stmt_atividades->get_result();
        $proximas_atividades = $result_atividades->fetch_all(MYSQLI_ASSOC);
        $stmt_atividades->close();
    } else {
        throw new Exception($conn->error);
    }
} catch (Exception $e) {
    error_log("Erro ao buscar atividades: " . $e->getMessage());
    $proximas_atividades = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>UniFlow - Home</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="sidebar">
    <div class="logo">
        <img src="../img/logopreta2-removebg-preview.png" alt="Logotipo">
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
            <a href="#" class="notification-link"><div class="notifications"><i class="fas fa-bell"></i></div></a>
            <div class="user-info">
                <img src="<?php echo htmlspecialchars($foto); ?>" alt="Foto do Aluno">
                <span><?php echo htmlspecialchars($aluno_nome); ?></span>
                <a href="logout.php">Sair</a>
            </div>
        </div>
    </div>

    <div class="dashboard-header">
        <h1>Bem vindo(a), <?php echo htmlspecialchars($aluno_nome); ?>!</h1>
        <p>Data: <?php echo date("d/m/Y"); ?></p>
    </div>

    <div class="dashboard-cards">
        <div class="card">
            <i class="fas fa-book-open"></i>
            <div class="card-info">
                <span>Disciplinas</span>
                <p><?php echo count($disciplinas); ?></p>
            </div>
        </div>

        <div class="card">
            <i class="fas fa-check-circle"></i>
            <div class="card-info">
                <span>Média Geral</span>
                <p>
                    <?php
                    $total = 0; $qtd = 0;
                    foreach($disciplinas as $d) {
                        if ($d['media_nota'] !== null) { $total += $d['media_nota']; $qtd++; }
                    }
                    echo $qtd ? number_format($total/$qtd, 1) : 'N/A';
                    ?>
                </p>
            </div>
        </div>

        <div class="card">
            <i class="fas fa-chart-bar"></i>
            <div class="card-info">
                <span>Frequência</span>
                <p>
                    <?php
                    $totalFreq = 0; $countFreq = 0;
                    foreach($disciplinas as $d) {
                        $totalFreq += $d['frequencia'];
                        $countFreq++;
                    }
                    echo $countFreq ? round($totalFreq/$countFreq). '%' : 'N/A';
                    ?>
                </p>
            </div>
        </div>

        <div class="card">
            <i class="fas fa-exclamation-triangle"></i>
            <div class="card-info">
                <span>Pendentes</span>
                <p>3</p>
            </div>
        </div>
    </div>
    
    <div class="dashboard-table-container next-activities">
        <div class="dashboard-header-table">
            <h1>Próximas Atividades</h1>
        </div>
        <table>
            <tbody>
                <?php if (!empty($proximas_atividades)): ?>
                    <?php foreach ($proximas_atividades as $atividade): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($atividade['titulo']); ?></strong>
                                <br>
                                <span class="data-atividade"><?php echo date('d/m/Y', strtotime($atividade['data'])); ?></span>
                            </td>
                            <td>
                                <span class="tag-atividade <?php echo htmlspecialchars($atividade['tipo']); ?>">
                                    <?php echo htmlspecialchars(ucfirst(str_replace('type-', '', $atividade['tipo']))); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td>Nenhuma atividade futura encontrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="dashboard-table-container">
        <div class="dashboard-header-table"><h1>Minhas Disciplinas</h1></div>
        <table>
            <thead>
                <tr>
                    <th>Disciplina</th>
                    <th>Professor</th>
                    <th>Nota</th>
                    <th>Frequência</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($disciplinas as $d): ?>
                <tr>
                    <td><?php echo htmlspecialchars($d['disciplina_nome']); ?></td>
                    <td><?php echo htmlspecialchars($d['professor_nome']); ?></td>
                    <td><?php echo $d['media_nota'] !== null ? number_format($d['media_nota'],1) : 'N/A'; ?></td>
                    <td><?php echo round($d['frequencia']).'%'; ?></td>
                    <td class="status">
                        <?php
                        if ($d['media_nota'] >= 7 && $d['frequencia'] >= 75) echo "Aprovado";
                        elseif ($d['media_nota'] >= 5) echo "Exame";
                        else echo "Reprovado";
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>