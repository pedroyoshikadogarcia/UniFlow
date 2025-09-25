<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'professor') {
    header('Location: ../login.php');
    exit;
}
