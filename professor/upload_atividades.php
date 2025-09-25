<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['aluno_id'])) {
    header("Location: ../login.php");
    exit();
}

$aluno_id = $_SESSION['aluno_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo'])) {
    $atividade_id = intval($_POST['atividade_id']);
    $arquivo = $_FILES['arquivo'];

    // Pasta onde os uploads vão ficar
    $uploadDir = "../uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $nomeArquivo = time() . "_" . basename($arquivo['name']);
    $caminhoFinal = $uploadDir . $nomeArquivo;

    if (move_uploaded_file($arquivo['tmp_name'], $caminhoFinal)) {
        $caminhoBanco = "uploads/" . $nomeArquivo; // sem "../"

        // Se já existe entrega, atualiza
        $stmt = $conn->prepare("SELECT id FROM entregas WHERE atividade_id = ? AND aluno_id = ?");
        $stmt->bind_param("ii", $atividade_id, $aluno_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE entregas 
                                    SET arquivo = ?, status = 'Entregue', data_entrega = NOW() 
                                    WHERE atividade_id = ? AND aluno_id = ?");
            $stmt->bind_param("sii", $caminhoBanco, $atividade_id, $aluno_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO entregas (atividade_id, aluno_id, arquivo, status, data_entrega) 
                                    VALUES (?, ?, ?, 'Entregue', NOW())");
            $stmt->bind_param("iis", $atividade_id, $aluno_id, $caminhoBanco);
        }

        if ($stmt->execute()) {
            header("Location: atividades.php?success=1");
            exit();
        } else {
            die("Erro ao salvar no banco: " . $conn->error);
        }
    } else {
        die("Erro no upload do arquivo.");
    }
}
?>
