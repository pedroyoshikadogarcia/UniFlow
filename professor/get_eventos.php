<?php
session_start();
include '../conexao.php'; // Caminho para o seu arquivo de conexão

header('Content-Type: application/json');

if (!isset($_GET['aluno_id']) || !isset($_GET['month']) || !isset($_GET['year'])) {
    echo json_encode(['status' => 'error', 'message' => 'Parâmetros inválidos.']);
    exit();
}

$aluno_id = $_GET['aluno_id'];
$month = $_GET['month'];
$year = $_GET['year'];

// Verifica se o aluno logado é o mesmo que está requisitando os dados
if ($_SESSION['aluno_id'] != $aluno_id) {
    echo json_encode(['status' => 'error', 'message' => 'Acesso negado.']);
    exit();
}

// Prepara a consulta para buscar eventos do aluno logado no mês/ano específicos
$stmt = $conn->prepare("SELECT id, data AS data_evento, titulo, tipo, descricao FROM calendario_eventos WHERE aluno_id = ? AND MONTH(data) = ? AND YEAR(data) = ?");
$stmt->bind_param("iii", $aluno_id, $month, $year);
$stmt->execute();
$result = $stmt->get_result();

$eventos = [];
while ($row = $result->fetch_assoc()) {
    $eventos[] = $row;
}

echo json_encode($eventos);

$stmt->close();
$conn->close();
?>