<?php
require_once 'notifications.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = getCurrentUser()['id'];

// Adicionar notificações de teste
if ($_POST['action'] ?? '' === 'add_test') {
    addNotification($user_id, 'Bem-vindo!', 'Seja bem-vindo ao sistema de notificações interno!', 'success');
    addNotification($user_id, 'Novo exercício disponível', 'Um novo exercício de JavaScript foi adicionado.', 'info');
    addNotification($user_id, 'Conquista desbloqueada', 'Você desbloqueou a conquista "Primeiro Passo"!', 'success');
    addNotification($user_id, 'Lembrete', 'Não se esqueça de completar seus exercícios diários.', 'warning');
    
    $_SESSION['success'] = 'Notificações de teste adicionadas!';
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$title = 'Teste de Notificações';
include 'header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <h1>Sistema de Notificações</h1>
            <p class="lead">Sistema interno de notificações - sem dependências externas</p>
            
            <div class="card">
                <div class="card-body">
                    <h5>Características:</h5>
                    <ul>
                        <li>✅ Sistema 100% interno</li>
                        <li>✅ Sem dependências do Google ou terceiros</li>
                        <li>✅ Notificações em tempo real via AJAX</li>
                        <li>✅ Contador de não lidas</li>
                        <li>✅ Interface responsiva</li>
                        <li>✅ Diferentes tipos: info, success, warning, error</li>
                    </ul>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="add_test">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Adicionar Notificações de Teste
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6>Como usar</h6>
                </div>
                <div class="card-body">
                    <p><strong>1.</strong> Clique no ícone de sino no header</p>
                    <p><strong>2.</strong> Veja suas notificações</p>
                    <p><strong>3.</strong> Clique para marcar como lida</p>
                    
                    <hr>
                    
                    <h6>Para desenvolvedores:</h6>
                    <code>addNotification($user_id, $title, $message, $type);</code>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>