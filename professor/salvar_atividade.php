<?php
session_start();
include '../conexao.php';
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(0);

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["success" => false, "message" => "Não logado"]);
    exit();
}

$professor_id = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $disciplina_id = intval($_POST['disciplina_id']);
    $titulo = trim($_POST['titulo']);
    $tipo = trim($_POST['tipo']);
    $prazo = $_POST['prazo'];
    $valor = intval($_POST['valor'] ?? 0);
    $descricao = trim($_POST['descricao']);
    $arquivo_nome = null;

    // Verifica se a disciplina pertence ao professor
    $checkDisciplina = $conn->prepare("SELECT id FROM disciplinas WHERE id = ? AND professor_id = ?");
    $checkDisciplina->bind_param("ii", $disciplina_id, $professor_id);
    $checkDisciplina->execute();
    $resDisciplina = $checkDisciplina->get_result();
    if ($resDisciplina->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Disciplina não pertence a este professor"]);
        exit();
    }

    // Upload de arquivo
    if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg','jpeg','png','pdf','doc','docx','txt'];
        if (!in_array($ext, $permitidas)) {
            echo json_encode(["success" => false, "message" => "Tipo de arquivo não permitido"]);
            exit();
        }
        $arquivo_nome = uniqid() . '.' . $ext;
        $destino = "../uploads_atividades/" . $arquivo_nome;
        if (!move_uploaded_file($_FILES['arquivo']['tmp_name'], $destino)) {
            echo json_encode(["success" => false, "message" => "Erro ao enviar arquivo"]);
            exit();
        }
    }

    $stmt = $conn->prepare("INSERT INTO atividades (disciplina_id, titulo, tipo, descricao, prazo, professor_id, arquivo) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param("issssis", $disciplina_id, $titulo, $tipo, $descricao, $prazo, $professor_id, $arquivo_nome);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => $conn->error]);
    }
}
?>
