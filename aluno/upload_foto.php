<?php
session_start();

if (!isset($_SESSION['aluno_id'])) {
    die("Aluno não está logado.");
}

$aluno_id = $_SESSION['aluno_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['foto'])) {
    $foto = $_FILES['foto'];
    $nome_foto = basename($foto['name']);
    $destino = 'img/fotos_alunos/' . $nome_foto;

    if (move_uploaded_file($foto['tmp_name'], $destino)) {
        // Atualiza o caminho da foto no banco de dados
        $conn = new mysqli('localhost', 'root', '', 'uniflow');
        if ($conn->connect_error) {
            die("Falha na conexão: " . $conn->connect_error);
        }

        $sql = "UPDATE alunos SET foto = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $destino, $aluno_id);
        if ($stmt->execute()) {
            echo "Foto atualizada com sucesso!";
        } else {
            echo "Erro ao atualizar a foto.";
        }
        $stmt->close();
        $conn->close();
    } else {
        echo "Erro ao mover a foto.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Foto</title>
</head>
<body>
    <form action="upload_foto.php" method="POST" enctype="multipart/form-data">
    <label for="foto">Escolha uma foto:</label>
    <input type="file" name="foto" id="foto" required>
    <button type="submit">Enviar Foto</button>
</form>

</body>
</html>
