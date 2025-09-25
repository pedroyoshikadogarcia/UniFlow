<?php
header('Content-Type: application/json');
include 'conexao.php';

$disciplina_id = $_GET['disciplina_id'] ?? null;
$data = $_GET['data'] ?? null;

if (!$disciplina_id || !$data) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT 
        a.id AS aluno_id,
        a.nome,
        a.matricula,
        COALESCE(f.presente, 0) AS presente
    FROM matriculas m
    INNER JOIN alunos a 
        ON m.aluno_id = a.id
    LEFT JOIN frequencia f 
        ON f.matricula_id = m.id 
        AND f.data = ?
    WHERE m.disciplina_id = ?
    ORDER BY a.nome
");
$stmt->bind_param("si", $data, $disciplina_id);
$stmt->execute();
$result = $stmt->get_result();

$alunos = [];
while ($row = $result->fetch_assoc()) {
    $alunos[] = $row;
}

echo json_encode($alunos);
