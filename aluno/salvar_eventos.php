<?php
session_start();
include '../conexao.php'; // Caminho para o seu arquivo de conexão

header('Content-Type: application/json');

// Verifica se todos os dados necessários foram enviados
if (!isset($_POST['aluno_id'], $_POST['data'], $_POST['titulo'], $_POST['tipo'], $_POST['descricao'])) {
    echo json_encode(['status' => 'error', 'message' => 'Dados incompletos.']);
    exit();
}

$aluno_id = $_POST['aluno_id'];
$data = $_POST['data'];
$titulo = $_POST['titulo'];
$tipo = $_POST['tipo'];
$descricao = $_POST['descricao'];

// Verifica se o aluno logado é o mesmo que está enviando os dados
if (!isset($_SESSION['aluno_id']) || $_SESSION['aluno_id'] != $aluno_id) {
    echo json_encode(['status' => 'error', 'message' => 'Acesso negado.']);
    exit();
}

// Prepara a instrução de inserção
$stmt = $conn->prepare("INSERT INTO calendario_eventos (aluno_id, data, titulo, tipo, descricao) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $aluno_id, $data, $titulo, $tipo, $descricao);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Evento salvo com sucesso!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar o evento: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
