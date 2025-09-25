<?php
session_start();
include '../conexao.php';
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["success" => false, "message" => "Não logado"]);
    exit();
}

$professor_id = $_SESSION['usuario_id'];

try {
    $sql = "SELECT a.id, a.titulo, a.tipo, a.descricao, a.prazo, a.arquivo, d.nome AS disciplina
            FROM atividades a
            INNER JOIN disciplinas d ON a.disciplina_id = d.id
            WHERE a.professor_id = ?
            ORDER BY a.prazo ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $professor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $atividades = [];
    while ($row = $result->fetch_assoc()) {
        $atividades[] = $row;
    }

    echo json_encode(["success" => true, "atividades" => $atividades]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
