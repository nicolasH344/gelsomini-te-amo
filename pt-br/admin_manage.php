<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

$section = $_GET['section'] ?? 'exercises';
$title = 'Gerenciar ' . ucfirst($section);
include 'header.php';

$conn = getDBConnection();
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-cogs"></i> Gerenciar Conteúdo</h1>
                <a href="admin_panel.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar ao Painel
                </a>
            </div>
        </div>
    </div>
    
    <!-- Navegação -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?php echo $section === 'exercises' ? 'active' : ''; ?>" 
               href="?section=exercises">💪 Exercícios</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $section === 'tutorials' ? 'active' : ''; ?>" 
               href="?section=tutorials">📚 Tutoriais</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $section === 'forum' ? 'active' : ''; ?>" 
               href="?section=forum">💬 Fórum</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $section === 'achievements' ? 'active' : ''; ?>" 
               href="?section=achievements">🏆 Conquistas</a>
        </li>
    </ul>
    
    <?php if ($section === 'exercises'): ?>
        <!-- Gerenciar Exercícios -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5>Lista de Exercícios</h5>
                        <button class="btn btn-primary btn-sm" onclick="addExercise()">
                            <i class="fas fa-plus"></i> Novo Exercício
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="exercisesList">Carregando...</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Estatísticas</h5>
                    </div>
                    <div class="card-body">
                        <p>Total: <strong><?php 
                            $result = $conn->query("SELECT COUNT(*) FROM exercises");
                            echo $result ? $result->fetch_row()[0] : 0;
                        ?></strong></p>
                        <p>Concluídos: <strong><?php 
                            $result = $conn->query("SELECT COUNT(DISTINCT exercise_id) FROM exercise_progress WHERE status = 'completed'");
                            echo $result ? $result->fetch_row()[0] : 0;
                        ?></strong></p>
                    </div>
                </div>
            </div>
        </div>
        
    <?php elseif ($section === 'tutorials'): ?>
        <!-- Gerenciar Tutoriais -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5>Lista de Tutoriais</h5>
                        <button class="btn btn-primary btn-sm" onclick="addTutorial()">
                            <i class="fas fa-plus"></i> Novo Tutorial
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="tutorialsList">Carregando...</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Estatísticas</h5>
                    </div>
                    <div class="card-body">
                        <p>Total: <strong><?php 
                            $result = $conn->query("SELECT COUNT(*) FROM tutorials");
                            echo $result ? $result->fetch_row()[0] : 0;
                        ?></strong></p>
                        <p>Lidos: <strong><?php 
                            $result = $conn->query("SELECT COUNT(DISTINCT tutorial_id) FROM tutorial_progress WHERE status = 'completed'");
                            echo $result ? $result->fetch_row()[0] : 0;
                        ?></strong></p>
                    </div>
                </div>
            </div>
        </div>
        
    <?php elseif ($section === 'forum'): ?>
        <!-- Gerenciar Fórum -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5>Posts Recentes</h5>
                        <button class="btn btn-primary btn-sm" onclick="addForumCategory()">
                            <i class="fas fa-plus"></i> Nova Categoria
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="forumList">
                            <?php
                            $result = $conn->query("SELECT * FROM forum_posts ORDER BY created_at DESC LIMIT 10");
                            if ($result && $result->num_rows > 0):
                            ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Título</th>
                                            <th>Autor</th>
                                            <th>Data</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($post = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($post['title']); ?></td>
                                            <td>Usuário #<?php echo $post['user_id']; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-danger" onclick="deleteForumPost(<?php echo $post['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <p class="text-muted">Nenhum post encontrado</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Estatísticas do Fórum</h5>
                    </div>
                    <div class="card-body">
                        <p>Posts: <strong><?php 
                            $result = $conn->query("SELECT COUNT(*) FROM forum_posts");
                            echo $result ? $result->fetch_row()[0] : 0;
                        ?></strong></p>
                        <p>Comentários: <strong><?php 
                            $result = $conn->query("SELECT COUNT(*) FROM forum_comments");
                            echo $result ? $result->fetch_row()[0] : 0;
                        ?></strong></p>
                    </div>
                </div>
            </div>
        </div>
        
    <?php elseif ($section === 'achievements'): ?>
        <!-- Gerenciar Conquistas -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5>Conquistas dos Usuários</h5>
                        <button class="btn btn-primary btn-sm" onclick="resetAchievements()">
                            <i class="fas fa-redo"></i> Recalcular Conquistas
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="achievementsList">
                            <?php
                            $result = $conn->query("SELECT ua.*, u.username FROM user_achievements ua 
                                                   JOIN users u ON ua.user_id = u.id 
                                                   ORDER BY ua.earned_at DESC LIMIT 20");
                            if ($result && $result->num_rows > 0):
                            ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Usuário</th>
                                            <th>Conquista</th>
                                            <th>Data</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($achievement = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($achievement['username']); ?></td>
                                            <td><?php echo htmlspecialchars($achievement['achievement_type']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($achievement['earned_at'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-danger" onclick="deleteAchievement(<?php echo $achievement['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <p class="text-muted">Nenhuma conquista encontrada</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Estatísticas de Conquistas</h5>
                    </div>
                    <div class="card-body">
                        <p>Total: <strong><?php 
                            $result = $conn->query("SELECT COUNT(*) FROM user_achievements");
                            echo $result ? $result->fetch_row()[0] : 0;
                        ?></strong></p>
                        <p>Usuários com conquistas: <strong><?php 
                            $result = $conn->query("SELECT COUNT(DISTINCT user_id) FROM user_achievements");
                            echo $result ? $result->fetch_row()[0] : 0;
                        ?></strong></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal para Exercícios -->
<div class="modal fade" id="exerciseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exercício</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="exerciseForm">
                    <input type="hidden" id="exerciseId">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control" id="exerciseTitle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" id="exerciseDescription" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dificuldade</label>
                        <select class="form-control" id="exerciseDifficulty">
                            <option value="beginner">Iniciante</option>
                            <option value="intermediate">Intermediário</option>
                            <option value="advanced">Avançado</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveExercise()">Salvar</button>
            </div>
        </div>
    </div>
</div>

<script>
function loadExercises() {
    fetch('admin_content.php?action=list&type=exercises')
    .then(response => response.json())
    .then(data => {
        const list = document.getElementById('exercisesList');
        if (data.success && data.items.length > 0) {
            list.innerHTML = data.items.map(item => `
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <strong>${item.title}</strong><br>
                        <small class="text-muted">${item.description}</small>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-warning" onclick="editExercise(${item.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteExercise(${item.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        } else {
            list.innerHTML = '<p class="text-muted">Nenhum exercício encontrado</p>';
        }
    });
}

function addExercise() {
    document.getElementById('exerciseId').value = '';
    document.getElementById('exerciseTitle').value = '';
    document.getElementById('exerciseDescription').value = '';
    document.getElementById('exerciseDifficulty').value = 'beginner';
    new bootstrap.Modal(document.getElementById('exerciseModal')).show();
}

function saveExercise() {
    const data = {
        action: 'save',
        type: 'exercise',
        id: document.getElementById('exerciseId').value,
        title: document.getElementById('exerciseTitle').value,
        description: document.getElementById('exerciseDescription').value,
        difficulty: document.getElementById('exerciseDifficulty').value
    };
    
    fetch('admin_content.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('exerciseModal')).hide();
            loadExercises();
        } else {
            alert('Erro: ' + data.message);
        }
    });
}

function deleteExercise(id) {
    if (!confirm('Deletar exercício?')) return;
    
    fetch('admin_content.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action: 'delete', type: 'exercise', id: id})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) loadExercises();
        else alert('Erro: ' + data.message);
    });
}

function deleteForumPost(id) {
    if (!confirm('Deletar post?')) return;
    
    fetch('admin_content.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action: 'delete', type: 'forum_post', id: id})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) location.reload();
        else alert('Erro: ' + data.message);
    });
}

function resetAchievements() {
    if (!confirm('Recalcular todas as conquistas? Isso pode demorar.')) return;
    
    fetch('admin_content.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action: 'reset_achievements'})
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success ? 'Conquistas recalculadas!' : 'Erro: ' + data.message);
        if (data.success) location.reload();
    });
}

// Carregar dados ao inicializar
document.addEventListener('DOMContentLoaded', function() {
    const section = '<?php echo $section; ?>';
    if (section === 'exercises') {
        loadExercises();
    }
});
</script>

<?php include 'footer.php'; ?>