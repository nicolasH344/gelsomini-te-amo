<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

$conn = getDBConnection();
$message = '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'reset_user_scores') {
        $user_id = (int)$_POST['user_id'];
        $stmt = $conn->prepare("UPDATE user_progress SET score = 0 WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Pontuação do usuário resetada!</div>';
        } else {
            $message = '<div class="alert alert-danger">Erro ao resetar pontuação.</div>';
        }
    }
    
    if ($action === 'delete_user_progress') {
        $user_id = (int)$_POST['user_id'];
        $stmt = $conn->prepare("DELETE FROM user_progress WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Progresso do usuário excluído!</div>';
        } else {
            $message = '<div class="alert alert-danger">Erro ao excluir progresso.</div>';
        }
    }
    
    if ($action === 'truncate_table') {
        $table = $_POST['table'];
        $allowed_tables = ['user_progress', 'user_achievements', 'notifications'];
        
        if (in_array($table, $allowed_tables)) {
            $conn->query("TRUNCATE TABLE $table");
            $message = '<div class="alert alert-success">Tabela ' . $table . ' limpa!</div>';
        }
    }
}

// Buscar dados
$users = $conn->query("SELECT u.id, u.username, u.first_name, u.last_name, 
                       COUNT(up.id) as total_progress,
                       SUM(up.score) as total_score
                       FROM users u 
                       LEFT JOIN user_progress up ON u.id = up.user_id 
                       GROUP BY u.id 
                       ORDER BY total_score DESC")->fetch_all(MYSQLI_ASSOC);

$tables_info = [
    'users' => $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0],
    'exercises' => $conn->query("SELECT COUNT(*) FROM exercises")->fetch_row()[0],
    'user_progress' => $conn->query("SELECT COUNT(*) FROM user_progress")->fetch_row()[0],
    'user_achievements' => $conn->query("SELECT COUNT(*) FROM user_achievements")->fetch_row()[0],
    'notifications' => $conn->query("SELECT COUNT(*) FROM notifications")->fetch_row()[0]
];

$title = 'Gerenciar Banco de Dados';
include 'header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-database"></i> Gerenciar Banco de Dados</h1>
        <a href="admin_panel.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar ao Painel
        </a>
    </div>
    
    <?php echo $message; ?>
    
    <!-- Informações das Tabelas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-table"></i> Informações das Tabelas</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($tables_info as $table => $count): ?>
                        <div class="col-md-2 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-primary"><?php echo $count; ?></h4>
                                <small><?php echo $table; ?></small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Gerenciar Usuários -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-users"></i> Gerenciar Progresso dos Usuários</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuário</th>
                            <th>Nome</th>
                            <th>Progresso Total</th>
                            <th>Pontuação Total</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                            <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo $user['total_progress']; ?> atividades</span>
                            </td>
                            <td>
                                <span class="badge bg-success"><?php echo $user['total_score'] ?? 0; ?> pontos</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-warning" onclick="resetUserScore(<?php echo $user['id']; ?>)">
                                        <i class="fas fa-undo"></i> Reset Score
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteUserProgress(<?php echo $user['id']; ?>)">
                                        <i class="fas fa-trash"></i> Excluir Progresso
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Ações de Limpeza -->
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h5><i class="fas fa-exclamation-triangle"></i> Ações Perigosas</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <button class="btn btn-outline-danger w-100" onclick="truncateTable('user_progress')">
                        <i class="fas fa-eraser"></i><br>
                        Limpar Todo Progresso
                    </button>
                </div>
                <div class="col-md-4 mb-3">
                    <button class="btn btn-outline-danger w-100" onclick="truncateTable('user_achievements')">
                        <i class="fas fa-trophy"></i><br>
                        Limpar Conquistas
                    </button>
                </div>
                <div class="col-md-4 mb-3">
                    <button class="btn btn-outline-danger w-100" onclick="truncateTable('notifications')">
                        <i class="fas fa-bell"></i><br>
                        Limpar Notificações
                    </button>
                </div>
            </div>
            
            <hr>
            
            <div class="row">
                <div class="col-md-6">
                    <button class="btn btn-info w-100" onclick="exportData()">
                        <i class="fas fa-download"></i> Exportar Dados
                    </button>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-secondary w-100" onclick="optimizeDatabase()">
                        <i class="fas fa-cogs"></i> Otimizar Banco
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function resetUserScore(userId) {
    if (!confirm('Resetar pontuação deste usuário?')) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="reset_user_scores">
        <input type="hidden" name="user_id" value="${userId}">
    `;
    document.body.appendChild(form);
    form.submit();
}

function deleteUserProgress(userId) {
    if (!confirm('EXCLUIR TODO o progresso deste usuário? Esta ação não pode ser desfeita!')) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="delete_user_progress">
        <input type="hidden" name="user_id" value="${userId}">
    `;
    document.body.appendChild(form);
    form.submit();
}

function truncateTable(table) {
    if (!confirm(`LIMPAR COMPLETAMENTE a tabela ${table}? Esta ação não pode ser desfeita!`)) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="truncate_table">
        <input type="hidden" name="table" value="${table}">
    `;
    document.body.appendChild(form);
    form.submit();
}

function exportData() {
    window.open('admin_export.php', '_blank');
}

function optimizeDatabase() {
    fetch('admin_database_api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=optimize'
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success ? 'Banco otimizado!' : 'Erro: ' + data.message);
    });
}
</script>

<?php include 'footer.php'; ?>