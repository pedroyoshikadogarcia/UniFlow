<?php
session_start();
include '../conexao.php';

// Verifica se o professor está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];

// Busca disciplinas do professor para popular o select do modal
$disciplinas = [];
$sql = "SELECT id, nome FROM disciplinas WHERE professor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $disciplinas[] = $row;
}

// determina foto do professor (jpg ou png) - verifica existência
$fotoProf = "../img/fotos_professores/{$usuario_id}.jpg";
if (!file_exists($fotoProf)) {
    $alt = "../img/fotos_professores/{$usuario_id}.png";
    if (file_exists($alt)) $fotoProf = $alt;
    else $fotoProf = "../img/fotos_professores/default.png"; // fallback (coloque uma padrão)
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gestão de Atividades</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
    <link rel="stylesheet" href="../style_professor/style_atividades.css" />
    <link rel="stylesheet" href="../global.css" />
</head>
<body>
<div class="sidebar">
    <div class="logo">
        <a href="index.php">
            <img src="../img/logopreta2.png" alt="Logo"/>
        </a>
    </div>
    <a href="turmas.php"><i class="fas fa-book"></i> Turmas</a>
    <a href="alunos.php"><i class="fas fa-graduation-cap"></i> Alunos</a>
    <a href="#" class="active"><i class="fas fa-tasks"></i> Atividades</a>
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
            <a href="#" class="notification-link">
                <div class="notifications"><i class="fas fa-bell"></i></div>
            </a>
            <div class="user-info">
                <a href="perfil.php" class="user-profile-link">
                    <img src="<?php echo $fotoProf; ?>" alt="Foto do Professor" />
                    <span><?php echo htmlspecialchars($usuario_nome); ?></span>
                </a>
            </div>
        </div>
    </div>

    <div id="activitiesSection" class="main-section show">
        <div class="dashboard-header">
            <h1>Gestão de Atividades</h1>
            <p>Data: <?php echo date('d/m/Y'); ?></p>
        </div>

        <div class="section-title-row">
            <h3>
                Suas Disciplinas e Atividades
                <span class="activities-count" id="activitiesCount">0 ATIVIDADE(S)</span>
            </h3>
            <button id="openAddActivity" class="add-activity-button">
                <i class="fas fa-plus"></i> Nova Atividade
            </button>
        </div>

        <div class="activities-list-container" id="activitiesListContainer">
            <!-- JS irá popular -->
        </div>
    </div>

    <!-- seção entregas dos alunos -->
    <div id="submissionsSection" class="main-section">
        <div class="submissions-content">
            <div class="modal-header">
                <h2 id="activityTitleSubmissions"></h2>
                <button id="backToActivitiesButton" class="action-button back-button">
                    <i class="fas fa-arrow-left"></i> Voltar
                </button>
            </div>
            <div class="submissions-summary">
                <p><strong>Total de Alunos:</strong> <span id="totalStudents"></span></p>
                <p><strong>Entregas Recebidas:</strong> <span id="submittedCount"></span></p>
                <p><strong>Pendentes:</strong> <span id="pendingCount"></span></p>
            </div>
            <div class="students-submissions-list" id="studentsSubmissionsList"></div>
            <div class="overall-actions">
                <button class="add-activity-button" id="saveAllNotesBtn">Salvar Todas as Notas</button>
            </div>
        </div>
    </div>

    <!-- detalhe das entregas dos alunos -->
    <div id="studentDetailsSection" class="main-section">
        <div class="student-details-content">
            <div class="modal-header">
                <h2 id="studentSubmissionTitle"></h2>
                <button id="backToSubmissionsButton" class="action-button back-button">
                    <i class="fas fa-arrow-left"></i> Voltar à Lista
                </button>
            </div>

            <div class="activity-enunciado-section">
                <h3>Enunciado da Atividade</h3>
                <p id="activityEnunciadoDetail"></p>
                <p id="professorFileLink">
                    <i class="fas fa-file-alt"></i> <a href="#" target="_blank" id="professorFileLinkHref"></a> (Arquivo do Professor)
                </p>
            </div>

            <div class="student-submission-section">
                <h3>Entrega do Aluno</h3>
                <div id="submissionContent"></div>

                <div class="grade-and-feedback-section">
                    <h4>Nota e Feedback</h4>
                    <div class="grade-input-wrapper">
                        <label for="studentFinalGrade">Nota Final:</label>
                        <input type="number" id="studentFinalGrade" min="0" max="10" />
                    </div>
                    <div class="feedback-wrapper">
                        <label for="professorFeedback">Feedback:</label>
                        <textarea id="professorFeedback" placeholder="Deixe seu feedback aqui..."></textarea>
                    </div>
                    <button class="add-activity-button save-student-grade-button">Salvar Nota e Feedback</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal: adicionar nova atividade + -->
    <div id="addActivityModal" class="modal-overlay">
        <div class="modal-content-new-activity">
            <div class="modal-header">
                <h2>Adicionar Nova Atividade</h2>
                <button id="closeAddActivityModal" class="action-button back-button">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>

            <!-- Formulário com título, tipo, data, valor, descrição e anexos -->
            <form id="newActivityForm" enctype="multipart/form-data" method="POST" action="salvar_atividade.php">
                <div class="form-group">
                    <label for="newActivityDisciplina">Disciplina:</label>
                    <select id="newActivityDisciplina" name="disciplina_id" required>
                        <option value="">Selecione a Disciplina</option>
                        <?php foreach($disciplinas as $d): ?>
                            <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="newActivityTitle">Título da Atividade:</label>
                    <input type="text" id="newActivityTitle" name="titulo" required />
                </div>
                <div class="form-group">
                    <label for="newActivityType">Tipo de Atividade:</label>
                    <select id="newActivityType" name="tipo" required>
                        <option value="">Selecione o Tipo</option>
                        <option value="trabalho">Trabalho</option>
                        <option value="atividade">Atividade</option>
                        <option value="prova">Prova</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="newActivityDeadline">Prazo:</label>
                    <input type="date" id="newActivityDeadline" name="prazo" required />
                </div>
                <div class="form-group">
                    <label for="newActivityValue">Valor (pontos):</label>
                    <input type="number" id="newActivityValue" name="valor" min="0" required />
                </div>
                <div class="form-group">
                    <label for="newActivityEnunciado">Enunciado / Descrição:</label>
                    <textarea id="newActivityEnunciado" name="descricao" rows="5" required></textarea>
                </div>

                <div class="form-group file-upload-group">
                    <label for="newActivityFiles">Anexar Arquivos:</label>
                    <div class="custom-file-input-wrapper">
                        <input type="file" id="newActivityFiles" name="arquivo" />
                        <label for="newActivityFiles" class="custom-file-upload-button">
                            <i class="fas fa-paperclip"></i> Escolher Arquivo
                        </label>
                    </div>
                    <div class="file-type-icons">
                        <span class="file-type-info"><i class="fas fa-image"></i> Mídia</span>
                        <span class="file-type-info"><i class="fas fa-link"></i> Links</span>
                        <span class="file-type-info"><i class="fas fa-file-alt"></i> Docs</span>
                    </div>
                    <button type="button" id="uploadFilesButton" class="action-button upload-files-button">
                        <i class="fas fa-upload"></i> Enviar Anexo (simulação)
                    </button>
                </div>

                <button type="submit" class="add-activity-button save-new-activity-button">
                    <i class="fas fa-save"></i> Salvar Nova Atividade
                </button>
            </form>
        </div>
    </div>
</div>

<script src="atividade.js"></script>
</body>
</html>
