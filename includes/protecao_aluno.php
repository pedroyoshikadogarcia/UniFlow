<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['aluno_id']) || $_SESSION['tipo'] !== 'aluno') {
    header("Location: ../login.php");
    exit();
}
