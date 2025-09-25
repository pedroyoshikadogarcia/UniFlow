<?php
header('Content-Type: application/json');
include '../conexao.php';

$disciplina_id = $_GET['disciplina_id'] ?? null;
$mes = $_GET['mes'] ?? null;
$ano = $_GET['ano'] ?? null;

if (!$disciplina_id || !$mes || !$ano) {
    echo json_encode([]);
    exit;
}

// Buscar todos os dias que tiveram frequência na disciplina
$stmt = $conn->prepare("
    SELECT f.data
    FROM frequencia f
    JOIN matriculas m ON f.matricula_id = m.id
    WHERE m.disciplina_id = ? 
      AND MONTH(f.data) = ? 
      AND YEAR(f.data) = ?
    GROUP BY f.data
    ORDER BY f.data DESC
");
$stmt->bind_param("iii", $disciplina_id, $mes, $ano);
$stmt->execute();
$result = $stmt->get_result();

$historico = [];

while($row = $result->fetch_assoc()){
    $data = $row['data'];

    // Buscar presença de cada aluno no dia
    $stmt2 = $conn->prepare("
        SELECT m.aluno_id, f.presente
        FROM frequencia f
        JOIN matriculas m ON f.matricula_id = m.id
        WHERE m.disciplina_id = ? AND f.data = ?
    ");
    $stmt2->bind_param("is", $disciplina_id, $data);
    $stmt2->execute();
    $res2 = $stmt2->get_result();

    $presencas = [];
    while($r = $res2->fetch_assoc()){
        $presencas[$r['aluno_id']] = (int)$r['presente'];
    }

    $historico[] = [
        'data' => $data,
        'presencas' => $presencas
    ];
}

echo json_encode($historico);
