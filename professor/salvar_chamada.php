<?php

include '../conexao.php';
include '../includes/protecao_professor.php'; // Protege acesso

$professor_id = $_SESSION['usuario_id'];

// Receber dados do form
$disciplina_id = $_POST['disciplina_id'] ?? null;
$data = $_POST['data'] ?? null;

if (!$disciplina_id || !$data) {
    die("Dados incompletos.");
}

// Buscar alunos da disciplina para validar
$sql_alunos = "SELECT m.id AS matricula_id, a.id AS aluno_id FROM alunos a
               JOIN matriculas m ON a.id = m.aluno_id
               WHERE m.disciplina_id = ?";
$stmt_alunos = $conn->prepare($sql_alunos);
$stmt_alunos->bind_param("i", $disciplina_id);
$stmt_alunos->execute();
$result_alunos = $stmt_alunos->get_result();
$alunos = $result_alunos->fetch_all(MYSQLI_ASSOC);

// Loop para atualizar ou inserir presença
foreach ($alunos as $aluno) {
    $aluno_id = $aluno['aluno_id'];
    $matricula_id = $aluno['matricula_id'];

    // Receber valor de presença do form (1 = presente, 0 = ausente)
    $presence_field = "presence-$aluno_id";
    $presente = isset($_POST[$presence_field]) ? intval($_POST[$presence_field]) : 1;

    // Verificar se já existe registro
    $sql_check = "SELECT id FROM frequencia WHERE matricula_id = ? AND data = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("is", $matricula_id, $data);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Atualiza registro existente
        $sql_update = "UPDATE frequencia SET presente = ? WHERE matricula_id = ? AND data = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("iis", $presente, $matricula_id, $data);
        $stmt_update->execute();
    } else {
        // Insere novo registro
        $sql_insert = "INSERT INTO frequencia (matricula_id, data, presente) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("isi", $matricula_id, $data, $presente);
        $stmt_insert->execute();
    }
}

// Redireciona de volta para a página de chamada da disciplina
header("Location: chamada.php?disciplina_id=$disciplina_id&msg=sucesso");
exit;
