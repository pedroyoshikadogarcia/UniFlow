<?php
session_start();
include '../conexao.php';

header('Content-Type: application/json');

// Verifica login
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["success" => false, "message" => "Não logado"]);
    exit();
}

$professor_id = $_SESSION['usuario_id'];

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Confere se a atividade pertence a esse professor
    $check = $conn->prepare("SELECT a.id FROM atividades a INNER JOIN disciplinas d ON a.disciplina_id = d.id WHERE a.id = ? AND d.professor_id = ?");
    $check->bind_param("ii", $id, $professor_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Atividade não pertence a este professor"]);
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM atividades WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => $conn->error]);
    }
}
