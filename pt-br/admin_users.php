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
    
    if ($action === 'toggle_admin') {
        $user_id = (int)$_POST['user_id'];
        $is_admin = (int)$_POST['is_admin'];
        
        $stmt = $conn->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
        $stmt->bind_param("ii", $is_admin, $user_id);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Status de admin alterado!</div>';
        } else {
            $message = '<div class="alert alert-danger">Erro ao alterar status.</div>';
        }
    }
    
    if ($action === 'delete_user') {
        $user_id = (int)$_POST['user_id'];
        
        // Excluir progresso do usuário primeiro
        $conn->prepare("DELETE FROM user_progress WHERE user_id = ?")->execute([$user_id]);
        $conn->prepare("DELETE FROM user_achievements WHERE user_id = ?")->execute([$user_id]);
        $conn->prepare("DELETE FROM notifications WHERE user_id = ?")->execute([$user_id]);
        
        // Excluir usuário
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Usuário excluído!</div>';
        } else {
            $message = '<div class="alert alert-danger">Erro ao excluir usuário.</div>';
        }
    }
}

// Buscar usuários
$users = [];
$result = $conn->query("SELECT u.*, 
                        COUNT(up.id) as total_progress,
                        SUM(up.score) as total_score
                        FROM users u 
                        LEFT JOIN user_progress up ON u.id = up.user_id 
                        GROUP BY u.id 
                        ORDER BY u.created_at DESC");
if ($result) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
}

$title = 'Gerenciar Usuários';
include 'header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-users"></i> Gerenciar Usuários</h1>
        <a href="admin_panel.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar ao Painel
        </a>
    </div>
    
    <?php echo $message; ?>
    
    <!-- Lista de usuários -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-list"></i> Usuários Cadastrados (<?php echo count($users); ?>)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuário</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Progresso</th>
                            <th>Pontuação</th>
                            <th>Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                            <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <?php if ($user['is_admin']): ?>
                                    <span class="badge bg-danger">👑 Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">👤 Usuário</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo $user['total_progress'] ?? 0; ?></span>
                            </td>
                            <td>
                                <span class="badge bg-success"><?php echo $user['total_score'] ?? 0; ?></span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-warning" onclick="toggleAdmin(<?php echo $user['id']; ?>, <?php echo $user['is_admin'] ? 0 : 1; ?>)">
                                        <i class="fas fa-crown"></i>
                                    </button>
                                    <?php if ($user['id'] != getCurrentUser()['id']): ?>
                                    <button class="btn btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAdmin(userId, isAdmin) {
    const action = isAdmin ? 'tornar admin' : 'remover admin';
    if (!confirm(`Deseja ${action} este usuário?`)) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="toggle_admin">
        <input type="hidden" name="user_id" value="${userId}">
        <input type="hidden" name="is_admin" value="${isAdmin}">
    `;
    document.body.appendChild(form);
    form.submit();
}

function deleteUser(userId) {
    if (!confirm('EXCLUIR este usuário? Esta ação não pode ser desfeita!')) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="delete_user">
        <input type="hidden" name="user_id" value="${userId}">
    `;
    document.body.appendChild(form);
    form.submit();
}
</script>

<?php include 'footer.php'; ?>