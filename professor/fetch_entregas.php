<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');
require '../conexao.php';

// Mostrar erros como JSON
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);

try {
    // Pegando o atividade_id via GET
    $atividade_id = isset($_GET['atividade_id']) ? intval($_GET['atividade_id']) : 0;
    if ($atividade_id <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "atividade_id inválido"]);
        exit;
    }

    $sql = "
        SELECT 
            a.id AS aluno_id,
            a.nome AS aluno_nome,
            e.id AS entrega_id,
            e.nota,
            e.feedback,
            e.status,
            e.data_entrega,
            " . (column_exists($conn, 'entregas', 'arquivo') ? "e.arquivo," : "NULL AS arquivo,") . "
            at.id AS atividade_id
        FROM alunos a
        JOIN matriculas m ON m.aluno_id = a.id
        JOIN atividades at ON at.disciplina_id = m.disciplina_id
        LEFT JOIN entregas e ON e.atividade_id = at.id AND e.aluno_id = a.id
        WHERE at.id = ?
        ORDER BY a.nome
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $atividade_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $entregas = [];
    while ($row = $result->fetch_assoc()) {
        $row['entregue'] = !empty($row['data_entrega']);
        $entregas[] = $row;
    }

    echo json_encode(["success" => true, "entregas" => $entregas]);

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erro no servidor",
        "error" => $e->getMessage() // <- mostra detalhe do erro SQL
    ]);
    exit;
}



// Função auxiliar: checa se coluna existe
function column_exists($conn, $table, $column) {
    $res = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $res && $res->num_rows > 0;
}
