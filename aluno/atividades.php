<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['aluno_id'])) {
    header("Location: ../login.php");
    exit();
}

$aluno_id = $_SESSION['aluno_id'];

// Busca dados do aluno
$stmt = $conn->prepare("SELECT nome, foto FROM alunos WHERE id = ?");
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$resAluno = $stmt->get_result()->fetch_assoc();
$aluno_nome = $resAluno['nome'];
$aluno_foto = !empty($resAluno['foto']) ? "../" . $resAluno['foto'] : "../img/fotos_alunos/padrao.png";

// Busca atividades do aluno com status, nota e arquivo
$query = "
SELECT a.id, a.titulo, a.descricao, a.data_entrega, a.tipo,
       d.nome AS disciplina, p.nome AS professor,
       e.status AS status_entrega,
       e.nota AS nota_aluno,
       e.data_entrega AS data_entrega_aluno,
       e.arquivo AS arquivo_aluno
FROM atividades a
JOIN disciplinas d ON a.disciplina_id = d.id
JOIN professores p ON d.professor_id = p.id
JOIN matriculas m ON m.disciplina_id = d.id
LEFT JOIN entregas e ON e.atividade_id = a.id AND e.aluno_id = m.aluno_id
WHERE m.aluno_id = ?
ORDER BY a.data_entrega ASC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$result = $stmt->get_result();

$atividades_abertas = [];
$atividades_concluidas = [];

while ($row = $result->fetch_assoc()) {
    if ($row['status_entrega'] === 'Entregue') {
        $atividades_concluidas[] = $row;
    } else {
        $atividades_abertas[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atividades - Portal do Aluno</title>
    <link rel="stylesheet" href="../style/atividades.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <a href="index.php"><img src="../img/logopreta2.png" alt="Logotipo"></a>
    </div>
    <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="disciplinas.php"><i class="fas fa-book"></i> Disciplinas</a>
    <a href="notas.php"><i class="fas fa-graduation-cap"></i> Notas</a>
    <a href="calendario.php"><i class="fas fa-calendar-alt"></i> Calendário</a>
    <a href="atividades.php"><i class="fas fa-tasks"></i> Atividades</a>
    <a href="mensagens.php"><i class="fas fa-envelope"></i> Mensagens</a>
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
                <a href="perfil.php"><img src="<?php echo $aluno_foto; ?>" alt="Foto do Aluno"></a>
                <span><?php echo htmlspecialchars($aluno_nome); ?></span>
            </div>
        </div>
    </div>

    <div class="header-with-button">
        <h1>Minhas Atividades</h1>
    </div>

    <div class="activities-container">
        <!-- Atividades Abertas -->
        <div class="activity-section">
            <h2>Abertas</h2>
            <div class="activity-list">
                <?php if (empty($atividades_abertas)): ?>
                    <p>Nenhuma atividade aberta no momento.</p>
                <?php else: ?>
                    <?php foreach ($atividades_abertas as $atividade): ?>
                        <a href="#"
                           class="activity-link open"
                           data-id="<?php echo $atividade['id']; ?>"
                           data-title="<?php echo htmlspecialchars($atividade['titulo']); ?>"
                           data-discipline="<?php echo htmlspecialchars($atividade['disciplina']); ?>"
                           data-professor="<?php echo htmlspecialchars($atividade['professor']); ?>"
                           data-duedate="<?php echo date('d/m/Y', strtotime($atividade['data_entrega'])); ?>"
                           data-status="aberta"
                           data-description="<?php echo htmlspecialchars($atividade['descricao']); ?>"
                           data-tag="<?php echo htmlspecialchars($atividade['tipo']); ?>"
                           data-file="<?php echo !empty($atividade['arquivo_aluno']) ? '../' . $atividade['arquivo_aluno'] : ''; ?>">
                            <div class="activity-item">
                                <div class="activity-icon"><i class="far fa-clock"></i></div>
                                <div class="activity-details">
                                    <div class="activity-header">
                                        <h3><?php echo htmlspecialchars($atividade['titulo']); ?></h3>
                                        <span class="activity-tag"><?php echo htmlspecialchars($atividade['tipo']); ?></span>
                                    </div>
                                    <p><?php echo htmlspecialchars($atividade['disciplina']); ?> • Prof. <?php echo htmlspecialchars($atividade['professor']); ?></p>
                                </div>
                                <div class="activity-date">
                                    <?php echo date('d/m/Y', strtotime($atividade['data_entrega'])); ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Atividades Concluídas -->
        <div class="activity-section">
            <h2>Concluídas</h2>
            <div class="activity-list">
                <?php if (empty($atividades_concluidas)): ?>
                    <p>Nenhuma atividade concluída ainda.</p>
                <?php else: ?>
                    <?php foreach ($atividades_concluidas as $atividade): ?>
                        <a href="#"
                           class="activity-link completed"
                           data-id="<?php echo $atividade['id']; ?>"
                           data-title="<?php echo htmlspecialchars($atividade['titulo']); ?>"
                           data-discipline="<?php echo htmlspecialchars($atividade['disciplina']); ?>"
                           data-professor="<?php echo htmlspecialchars($atividade['professor']); ?>"
                           data-duedate="Concluído em <?php echo date('d/m/Y', strtotime($atividade['data_entrega_aluno'])); ?>"
                           data-status="concluida"
                           data-description="<?php echo htmlspecialchars($atividade['descricao']); ?>"
                           data-tag="<?php echo htmlspecialchars($atividade['tipo']); ?>"
                           data-grade="<?php echo $atividade['nota_aluno'] ?? '-'; ?>"
                           data-file="<?php echo !empty($atividade['arquivo_aluno']) ? '../' . $atividade['arquivo_aluno'] : ''; ?>">
                            <div class="activity-item">
                                <div class="activity-icon completed-icon"><i class="fas fa-check-circle"></i></div>
                                <div class="activity-details">
                                    <div class="activity-header">
                                        <h3><?php echo htmlspecialchars($atividade['titulo']); ?></h3>
                                        <span class="activity-tag"><?php echo htmlspecialchars($atividade['tipo']); ?></span>
                                    </div>
                                    <p><?php echo htmlspecialchars($atividade['disciplina']); ?> • Prof. <?php echo htmlspecialchars($atividade['professor']); ?></p>
                                </div>
                                <div class="activity-date">
                                    Concluído em <?php echo date('d/m/Y', strtotime($atividade['data_entrega_aluno'])); ?>
                                    <?php if ($atividade['nota_aluno'] !== null): ?>
                                        • Nota: <?php echo number_format($atividade['nota_aluno'], 2); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de detalhes -->
<div id="modalOverlay" class="modal-overlay">
    <div class="modal-content">
        <span class="close-button" id="closeModalButton">&times;</span>
        <h2 id="modalTitle"></h2>
        <div id="modalBodyContent"></div>
    </div>
</div>

<!-- Modal para upload -->
<dialog id="uploadDialog" class="upload-dialog">
    <span class="close-button" id="closeUploadBtn">&times;</span>
    <h3>Entregar Atividade</h3>
    <p>Selecione o arquivo para a entrega.</p>
    <form id="uploadForm" method="POST" enctype="multipart/form-data" action="upload_atividade.php">
        <input type="hidden" name="atividade_id" id="atividadeIdInput">
        <label for="fileInput" class="upload-label">
            <i class="fas fa-cloud-upload-alt"></i>
            <p>Arraste e solte o arquivo ou clique para selecionar</p>
        </label>
        <input type="file" id="fileInput" name="arquivo" required style="display: none;">
        <p id="fileNameDisplay" class="file-name"></p>
        <div class="dialog-actions">
            <button type="button" id="cancelUploadBtn">Cancelar</button>
            <button type="submit" id="submitUploadBtn">Enviar</button>
        </div>
    </form>
</dialog>

<script src="atividades.js"></script>
</body>
</html>
