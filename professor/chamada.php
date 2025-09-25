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
$sql_disciplinas = "SELECT id, nome FROM disciplinas WHERE professor_id = ? ORDER BY nome ASC";
$stmt_disc = $conn->prepare($sql_disciplinas);
$stmt_disc->bind_param("i", $professor_id);
$stmt_disc->execute();
$result_disciplinas = $stmt_disc->get_result();
$disciplinas = $result_disciplinas->fetch_all(MYSQLI_ASSOC);

// Definir disciplina selecionada (GET ou primeira da lista)
$disciplina_id = $_GET['disciplina_id'] ?? ($disciplinas[0]['id'] ?? null);

// Nome da disciplina selecionada
$nome_turma = 'Turma Desconhecida';
foreach($disciplinas as $disc) {
    if ((string)$disc['id'] === (string)$disciplina_id) {
        $nome_turma = $disc['nome'];
        break;
    }
}

// Data atual
$data_atual = date('Y-m-d');

// Buscar alunos da disciplina e presença para o dia atual
$alunos = [];
if ($disciplina_id) {
    $sql_alunos = "
    SELECT a.id AS aluno_id, a.nome, a.matricula,
           IFNULL(f.presente, 1) AS presente
    FROM alunos a
    JOIN matriculas m ON a.id = m.aluno_id
    LEFT JOIN frequencia f ON f.matricula_id = m.id AND f.data = ?
    WHERE m.disciplina_id = ?
    ORDER BY a.nome ASC
    ";
    $stmt_alunos = $conn->prepare($sql_alunos);
    $stmt_alunos->bind_param("si", $data_atual, $disciplina_id);
    $stmt_alunos->execute();
    $result_alunos = $stmt_alunos->get_result();
    $alunos = $result_alunos->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Lista de Presença</title>

<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<link rel="stylesheet" href="../style_professor/chamada.css">
<link rel="stylesheet" href="../style_professor/global.css">
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
    <a href="chamada.php" class="active"><i class="fas fa-user-check"></i> Chamada</a>
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
        <h1 id="main-title">Lista de Presença</h1>
        <p id="current-date-display">Data: <?php echo date('d/m/Y', strtotime($data_atual)); ?></p>

        <!-- Dropdown de disciplinas -->
        <form method="GET" id="form-disciplina">
            <label for="disciplina_id">Disciplina:</label>
            <select name="disciplina_id" id="disciplina_id" onchange="document.getElementById('form-disciplina').submit()">
                <?php foreach($disciplinas as $disc): ?>
                    <option value="<?php echo $disc['id']; ?>" <?php echo ($disc['id'] == $disciplina_id) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($disc['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="attendance-list-container">
        <div id="attendance-card-date" class="attendance-card-date" style="display:none;"></div>

        <form action="salvar_chamada.php" method="POST" id="form-chamada">
            <input type="hidden" name="disciplina_id" id="hidden-disciplina-id" value="<?php echo htmlspecialchars((string)$disciplina_id); ?>">
            <input type="hidden" name="data" id="hidden-data" value="<?php echo htmlspecialchars($data_atual); ?>">

            <table>
                <thead>
                    <tr>
                        <th>MATRÍCULA</th>
                        <th>NOME DO ALUNO</th>
                        <th>PRESENTE</th>
                        <th>AUSENTE</th>
                    </tr>
                </thead>
                <tbody id="attendance-table-body">
                    <?php if ($disciplina_id && !empty($alunos)): ?>
                        <?php foreach($alunos as $aluno): 
                            $presenteChecked = $aluno['presente'] ? 'checked' : '';
                            $ausenteChecked = !$aluno['presente'] ? 'checked' : '';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($aluno['matricula']); ?></td>
                            <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                            <td><input type="radio" name="presence-<?php echo (int)$aluno['aluno_id']; ?>" value="1" class="presence-radio" <?php echo $presenteChecked; ?>></td>
                            <td><input type="radio" name="presence-<?php echo (int)$aluno['aluno_id']; ?>" value="0" class="presence-radio" <?php echo $ausenteChecked; ?>></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php elseif (!$disciplina_id): ?>
                        <tr><td colspan="4">Você ainda não possui disciplinas cadastradas.</td></tr>
                    <?php else: ?>
                        <tr><td colspan="4">Nenhum aluno encontrado para esta disciplina.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="submit-button-container">
                <button type="button" id="history-button" class="history-button">
                    <i class="fas fa-history"></i> Histórico
                </button>
                <button type="submit" class="submit-attendance-button">
                    <i class="fas fa-check-double"></i> Enviar
                </button>
            </div>
        </form>

        <div class="back-to-history-container" style="display:none;">
            <button id="back-to-history-button" class="back-button">
                <i class="fas fa-arrow-left"></i> Voltar à Lista
            </button>
        </div>
    </div>
</div>

<div id="history-modal" class="modal">
    <div class="modal-content history-modal-content">
        <span class="close-button">&times;</span>
        <div class="history-header">
            <h2>Histórico de Presença - <?php echo htmlspecialchars($nome_turma); ?></h2>
        </div>

        <div class="history-view">
            <div class="month-selector">
                <button id="prev-month">&laquo;</button>
                <span id="current-month-year"><?php echo strftime('%B %Y'); ?></span>
                <button id="next-month">&raquo;</button>
            </div>
            <div id="history-list" class="history-list">
                <!-- JS irá preencher via AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
const DISCIPLINA_ID = '<?php echo $disciplina_id; ?>'; // Disponível para o JS
</script>

<script src="chamada.js"></script>
</body>
</html>
