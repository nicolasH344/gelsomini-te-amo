<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

$title = 'Painel Administrativo';
include 'header.php';

$conn = getDBConnection();
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-cogs"></i> Painel Administrativo</h1>
                <span class="badge bg-danger fs-6">👑 Admin: <?php echo htmlspecialchars(getCurrentUser()['first_name']); ?></span>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Estatísticas -->
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>👥 Usuários</h5>
                    <h2><?php 
                        $result = $conn->query("SELECT COUNT(*) FROM users");
                        echo $result ? $result->fetch_row()[0] : 0;
                    ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>💪 Exercícios</h5>
                    <h2><?php 
                        $result = $conn->query("SELECT COUNT(*) FROM exercises");
                        echo $result ? $result->fetch_row()[0] : 0;
                    ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>📚 Tutoriais</h5>
                    <h2><?php 
                        $result = $conn->query("SELECT COUNT(*) FROM tutorials");
                        echo $result ? $result->fetch_row()[0] : 0;
                    ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>🏆 Conquistas</h5>
                    <h2><?php 
                        $result = $conn->query("SELECT COUNT(*) FROM user_achievements");
                        echo $result ? $result->fetch_row()[0] : 0;
                    ?></h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Gerenciar Usuários -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-users"></i> Usuários Recentes</h5>
                </div>
                <div class="card-body">
                    <?php
                    $result = $conn->query("SELECT username, first_name, last_name, is_admin, created_at FROM users ORDER BY created_at DESC LIMIT 5");
                    if ($result && $result->num_rows > 0):
                    ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Usuário</th>
                                    <th>Nome</th>
                                    <th>Tipo</th>
                                    <th>Criado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                    <td>
                                        <?php if ($user['is_admin']): ?>
                                            <span class="badge bg-danger">👑 Admin</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">👤 Usuário</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-muted">Nenhum usuário encontrado</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Ações Rápidas -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-tools"></i> Ações Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="admin_exercises.php" class="btn btn-success">
                            <i class="fas fa-dumbbell"></i> Gerenciar Exercícios
                        </a>
                        <a href="admin_tutorials.php" class="btn btn-info">
                            <i class="fas fa-book"></i> Gerenciar Tutoriais
                        </a>
                        <a href="admin_forum.php" class="btn btn-warning">
                            <i class="fas fa-comments"></i> Gerenciar Fórum
                        </a>
                        <a href="admin_users.php" class="btn btn-primary">
                            <i class="fas fa-users"></i> Gerenciar Usuários
                        </a>
                        <hr>
                        <button class="btn btn-secondary" onclick="sendNotificationToAll()">
                            <i class="fas fa-bell"></i> Notificar Todos
                        </button>
                        <button class="btn btn-secondary" onclick="createSampleData()">
                            <i class="fas fa-database"></i> Dados de Exemplo
                        </button>
                        <a href="admin_database.php" class="btn btn-secondary">
                            <i class="fas fa-server"></i> Gerenciar Banco
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Configurações do Sistema -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-cog"></i> Configurações do Sistema</h5>
                </div>
                <div class="card-body">
                    <form id="systemConfig">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="enableRegistration" checked>
                                    <label class="form-check-label" for="enableRegistration">
                                        Permitir novos registros
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="enableNotifications" checked>
                                    <label class="form-check-label" for="enableNotifications">
                                        Sistema de notificações ativo
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="maintenanceMode">
                                    <label class="form-check-label" for="maintenanceMode">
                                        Modo manutenção
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <button type="button" class="btn btn-primary" onclick="saveConfig()">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function sendNotificationToAll() {
    const message = prompt('Digite a mensagem para todos os usuários:');
    if (!message) return;
    
    fetch('admin_actions.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=notify_all&message=' + encodeURIComponent(message)
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success ? 'Notificação enviada!' : 'Erro: ' + data.message);
    });
}

function createSampleData() {
    if (!confirm('Criar dados de exemplo? Isso pode sobrescrever dados existentes.')) return;
    
    fetch('admin_actions.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=create_sample_data'
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success ? 'Dados criados!' : 'Erro: ' + data.message);
        if (data.success) location.reload();
    });
}

function clearCache() {
    fetch('admin_actions.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=clear_cache'
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success ? 'Cache limpo!' : 'Erro: ' + data.message);
    });
}

function saveConfig() {
    const config = {
        enableRegistration: document.getElementById('enableRegistration').checked,
        enableNotifications: document.getElementById('enableNotifications').checked,
        maintenanceMode: document.getElementById('maintenanceMode').checked
    };
    
    fetch('admin_actions.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=save_config&config=' + encodeURIComponent(JSON.stringify(config))
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success ? 'Configurações salvas!' : 'Erro: ' + data.message);
    });
}
</script>

<?php include 'footer.php'; ?>