<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['aluno_id'])) {
    header("Location: login.php");
    exit();
}

$aluno_id = $_SESSION['aluno_id'];

// Receber dados do formulário (checkbox = 1 se marcado, 0 se não)
$manter_logado = isset($_POST['manter_logado']) ? 1 : 0;
$corretor_automatico = isset($_POST['corretor_automatico']) ? 1 : 0;
$permissao_perfil = $_POST['permissao_perfil'] ?? 'todos';
$notificacoes = isset($_POST['notificacoes']) ? 1 : 0;

// Verificar se já existe configuração para o aluno
$check_sql = "SELECT id FROM configuracoes WHERE aluno_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $aluno_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    // Já existe → UPDATE
    $sql = "UPDATE configuracoes 
            SET manter_logado = ?, corretor_automatico = ?, permissao_perfil = ?, notificacoes = ? 
            WHERE aluno_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisii", $manter_logado, $corretor_automatico, $permissao_perfil, $notificacoes, $aluno_id);
} else {
    // Não existe → INSERT
    $sql = "INSERT INTO configuracoes (aluno_id, manter_logado, corretor_automatico, permissao_perfil, notificacoes) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiisi", $aluno_id, $manter_logado, $corretor_automatico, $permissao_perfil, $notificacoes);
}

if ($stmt->execute()) {
    $_SESSION['msg'] = "Configurações atualizadas com sucesso!";
} else {
    $_SESSION['msg'] = "Erro ao atualizar configurações.";
}

header("Location: config.php");
exit();
