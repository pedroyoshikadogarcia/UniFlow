<?php
session_start();
include '../conexao.php';
header('Content-Type: application/json');

if(!isset($_SESSION['usuario_id'])) {
    echo json_encode(["success"=>false,"message"=>"Não logado"]);
    exit();
}

$professor_id = $_SESSION['usuario_id'];

if($_SERVER['REQUEST_METHOD']==='POST'){
    $entrega_id = intval($_POST['entrega_id']);
    $nota = floatval($_POST['nota']);
    $feedback = trim($_POST['feedback']);

    // Confere se entrega pertence a atividade do professor
    $check = $conn->prepare("
        SELECT e.id FROM entregas e
        INNER JOIN atividades a ON e.atividade_id = a.id
        INNER JOIN disciplinas d ON a.disciplina_id = d.id
        WHERE e.id=? AND d.professor_id=?
    ");
    $check->bind_param("ii",$entrega_id,$professor_id);
    $check->execute();
    $res = $check->get_result();
    if($res->num_rows===0){
        echo json_encode(["success"=>false,"message"=>"Entrega não pertence a este professor"]);
        exit();
    }

    $stmt = $conn->prepare("UPDATE entregas SET nota=?, feedback=? WHERE id=?");
    $stmt->bind_param("dsi",$nota,$feedback,$entrega_id);
    if($stmt->execute()){
        echo json_encode(["success"=>true]);
    } else {
        echo json_encode(["success"=>false,"message"=>$conn->error]);
    }
}
