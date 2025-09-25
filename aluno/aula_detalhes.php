<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['aluno_id'])) {
    header("Location: ../login.php");
    exit();
}

$aluno_id = $_SESSION['aluno_id'];
$disciplina_id = $_GET['id'] ?? null;

if (!$disciplina_id) {
    echo "Disciplina não especificada.";
    exit();
}

// Dados do aluno
$stmt = $conn->prepare("SELECT nome, foto FROM alunos WHERE id = ?");
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$result = $stmt->get_result();
$aluno = $result->fetch_assoc();

$nome = $aluno['nome'];
$foto = !empty($aluno['foto']) && file_exists($aluno['foto']) ? $aluno['foto'] : '../img/fotos_alunos/padrao.png';

// Dados da disciplina e professor
$query = "SELECT d.*, p.nome AS professor_nome 
          FROM disciplinas d 
          JOIN professores p ON d.professor_id = p.id 
          JOIN matriculas m ON d.id = m.disciplina_id 
          WHERE m.aluno_id = ? AND d.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $aluno_id, $disciplina_id);
$stmt->execute();
$disciplina = $stmt->get_result()->fetch_assoc();

if (!$disciplina) {
    echo "Disciplina não encontrada ou não pertence ao aluno.";
    exit();
}

// Módulos da disciplina
$stmt = $conn->prepare("SELECT * FROM modulos WHERE disciplina_id = ?");
$stmt->bind_param("i", $disciplina_id);
$stmt->execute();
$modulos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Objetivos da disciplina
$stmt = $conn->prepare("SELECT descricao FROM objetivos WHERE disciplina_id = ?");
$stmt->bind_param("i", $disciplina_id);
$stmt->execute();
$objetivos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Total certificações na disciplina (independente do aluno)
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM certificacoes WHERE disciplina_id = ?");
$stmt->bind_param("i", $disciplina_id);
$stmt->execute();
$cert = $stmt->get_result()->fetch_assoc();
$total_certificacoes = $cert['total'];

// Total alunos matriculados na disciplina
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM matriculas WHERE disciplina_id = ?");
$stmt->bind_param("i", $disciplina_id);
$stmt->execute();
$matriculas = $stmt->get_result()->fetch_assoc();
$total_alunos = $matriculas['total'];

// Atividades da disciplina
$stmt = $conn->prepare("SELECT * FROM atividades WHERE disciplina_id = ?");
$stmt->bind_param("i", $disciplina_id);
$stmt->execute();
$atividades_result = $stmt->get_result();
$atividades = $atividades_result->fetch_all(MYSQLI_ASSOC);
$total_avaliacoes = $atividades_result->num_rows;

// Total de módulos e módulos concluídos pelo aluno
$total_modulos = count($modulos);
$concluidos_modulos = 0;
foreach ($modulos as $mod) {
    // Aqui assumimos que o campo 'concluido' já indica se o módulo está concluído para o aluno
    // Se for geral para disciplina, não por aluno, pode ser ajustado para relacionar com aluno
    if ($mod['concluido'] == 1) {
        $concluidos_modulos++;
    }
}
$percent_modulos = $total_modulos > 0 ? ($concluidos_modulos / $total_modulos) * 100 : 0;

// Total de atividades e atividades entregues pelo aluno
// Para saber se o aluno entregou, devemos consultar a tabela 'entregas'
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total_atividades 
    FROM atividades 
    WHERE disciplina_id = ?");
$stmt->bind_param("i", $disciplina_id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$total_atividades = $res['total_atividades'];

$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT e.atividade_id) AS entregues 
    FROM entregas e
    JOIN atividades a ON e.atividade_id = a.id
    WHERE e.aluno_id = ? AND a.disciplina_id = ? AND e.status = 'Entregue'");
$stmt->bind_param("ii", $aluno_id, $disciplina_id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$atividades_entregues = $res['entregues'];

$percent_atividades = $total_atividades > 0 ? ($atividades_entregues / $total_atividades) * 100 : 0;

// Progresso geral: média simples entre módulos e atividades
$progresso_geral = ($percent_modulos + $percent_atividades) / 2;

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($disciplina['nome']); ?> - Detalhes</title>
    <link rel="stylesheet" href="../style/aula_detalhes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <a href="index.php"><img src="../img/logopreta2-removebg-preview.png" alt="Logotipo"></a>
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
            <div class="user-info">
                <img src="<?php echo $foto; ?>" alt="Foto do Aluno">
                <span><?php echo htmlspecialchars($nome); ?></span>
            </div>
        </div>
    </div>

    <div class="aula-box">
        <div class="aula-info">
            <h2><?php echo htmlspecialchars($disciplina['nome']); ?></h2>
            <span><?php echo htmlspecialchars($disciplina['descricao']); ?></span>
        </div>

        <div class="info-aula">
            <div class="info-item">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Professor:</span> <?php echo htmlspecialchars($disciplina['professor_nome']); ?>
            </div>
            <div class="info-item">
                <i class="fas fa-clock"></i>
                <span>Horário:</span> <?php echo htmlspecialchars($disciplina['horario']); ?>
            </div>
            <div class="info-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>Sala:</span> <?php echo htmlspecialchars($disciplina['local']); ?>
            </div>
        </div>
    </div>

    <div class="selecao-info">
        <span id="visao-geral-tab" class="active">Visão Geral</span>
        <span id="modulos-tab">Módulos</span>
        <span id="atividades-tab">Atividades</span>
        <span id="progresso-tab">Progresso</span>
    </div>

    <div class="conteudo-abas">
        <div id="visao-geral-content" class="tab-content active">
            <div class="geral-info">
                <div class="box-modulo">
                    <div class="geral-modulo">
                        <i class="fa-solid fa-book-open"></i>
                        <span class="num"> <?php echo $total_modulos; ?></span>
                        <span class="text">Módulos</span>
                    </div>
                </div>
                <div class="geral-certificacoes">
                    <div class="box-certificacoes">
                        <div class="geral-certificacoes">
                            <i class="fa-solid fa-medal"></i>
                            <span class="num-certi"><?php echo $total_certificacoes; ?></span>
                            <span class="text-certi">Certificações</span>
                        </div>
                    </div>
                </div>
                <div class="geral-alunos">
                    <div class="box-alunos">
                        <div class="geral-alunos">
                            <i class="fa-solid fa-users"></i>
                            <span class="num-aluno"><?php echo $total_alunos; ?></span>
                            <span class="text-aluno">Alunos</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="info-obj">
                <div class="geral-obj">
                    <h2>Objetivos de aprendizagem</h2>
                    <div class="obj-text">
                        <?php foreach ($objetivos as $obj): ?>
                            <span><?php echo htmlspecialchars($obj['descricao']); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="info-visao">
                    <h2>Informações da Disciplina</h2>
                    <div class="info-text">
    <div>
        <span class="as">Créditos</span>
        <span class="bs"><?php echo $disciplina['creditos']; ?></span>
    </div>
    <div>
        <span class="as">Avaliações</span>
        <span class="bs"><?php echo $total_avaliacoes; ?></span>
    </div>
    <div>
        <span class="as">Categoria</span>
        <span class="bs"><?php echo htmlspecialchars($disciplina['categoria'] ?? 'Não informada'); ?></span>
    </div>
</div>

                </div>
            </div>
        </div>

        <div id="modulos-content" class="tab-content">
            <?php foreach ($modulos as $modulo): ?>
                <div class="modulo1">
                    <div class="modulo-main">
                        <i class="fa-solid fa-circle-check"></i>
                        <h2><?php echo htmlspecialchars($modulo['titulo']); ?></h2>
                    </div>
                    <span class="modulo-horario"><?php echo $modulo['duracao']; ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="atividades-content" class="tab-content">
            <?php foreach ($atividades as $atividade): ?>
                <div class="atividade1">
                    <div class="atividade-main">
                        <i class="fa-solid fa-file-lines"></i>
                        <h2><?php echo htmlspecialchars($atividade['titulo']); ?></h2>
                    </div>
                    <span class="atividade-data">Prazo: <br> <?php echo $atividade['prazo'] ? date('d/m/Y', strtotime($atividade['prazo'])) : 'Não informado'; ?></span>
                    <span class="<?php echo strtolower($atividade['status']) === 'entregue' ? 'status-atividade-entregue' : 'status-atividade-pendente'; ?>">
                        <?php echo ucfirst($atividade['status']); ?>
                    </span>
                    <?php if (strtolower($atividade['status']) === 'entregue' && $atividade['nota'] !== null): ?>
                        <span class="nota-atividade">NOTA: <br> <?php echo $atividade['nota']; ?></span>
                    <?php endif; ?>
                    <button class="detalhes-atividade">Ver Detalhes</button>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="progresso-content" class="tab-content">
            <h2>Seu progresso</h2>
            <div class="progress-item">
                <span>Progresso geral</span>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: <?php echo round($progresso_geral, 2); ?>%;"></div>
                </div>
                <span class="progress-value"><?php echo round($progresso_geral, 2); ?>%</span>
            </div>
            <div class="progress-item">
                <span>Módulos concluídos</span>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: <?php echo round($percent_modulos, 2); ?>%;"></div>
                </div>
                <span class="progress-value"><?php echo $concluidos_modulos . "/" . $total_modulos; ?></span>
            </div>
            <div class="progress-item">
                <span>Atividades entregues</span>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: <?php echo round($percent_atividades, 2); ?>%;"></div>
                </div>
                <span class="progress-value"><?php echo $atividades_entregues . "/" . $total_atividades; ?></span>
            </div>
        </div>
    </div>
</div>

<script src="aula_detalhes.js"></script>
</body>
</html>
