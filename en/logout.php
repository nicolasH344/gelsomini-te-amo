<?php
// Incluir configurações
require_once 'config.php';

// Processar logout
$result = processLogout();

// Iniciar nova sessão para mensagem
session_start();
$_SESSION['success'] = $result['message'];

// Redirecionar para página inicial
header('Location: index.php');
exit;
?>

