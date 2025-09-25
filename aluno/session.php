<?php
session_start();
if (!isset($_SESSION['aluno_id'])) {
    // Removido redirecionamento para login conforme seu pedido
    die("Erro: Usuário não autenticado.");
}
?>
