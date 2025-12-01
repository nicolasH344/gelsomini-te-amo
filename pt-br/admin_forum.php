<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

$conn = getDBConnection();
$message = '';

// Criar tabelas do fórum se não existirem
$conn->query("CREATE TABLE IF NOT EXISTS forum_topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    category VARCHAR(100) DEFAULT 'geral',
    author_id INT,
    author_name VARCHAR(100),
    replies_count INT DEFAULT 0,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS forum_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    topic_id INT,
    content TEXT,
    author_id INT,
    author_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete_topic') {
        $id = (int)$_POST['id'];
        
        // Excluir respostas primeiro
        $conn->prepare("DELETE FROM forum_replies WHERE topic_id = ?")->execute([$id]);
        
        // Excluir tópico
        $stmt = $conn->prepare("DELETE FROM forum_topics WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Tópico excluído!</div>';
        } else {
            $message = '<div class="alert alert-danger">Erro ao excluir tópico.</div>';
        }
    }
    
    if ($action === 'delete_reply') {
        $id = (int)$_POST['id'];
        $topic_id = (int)$_POST['topic_id'];
        
        $stmt = $conn->prepare("DELETE FROM forum_replies WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // Atualizar contador de respostas
            $conn->prepare("UPDATE forum_topics SET replies_count = replies_count - 1 WHERE id = ?")->execute([$topic_id]);
            $message = '<div class="alert alert-success">Resposta excluída!</div>';
        } else {
            $message = '<div class="alert alert-danger">Erro ao excluir resposta.</div>';
        }
    }
}

// Buscar tópicos
$topics = [];
$result = $conn->query("SELECT * FROM forum_topics ORDER BY last_activity DESC LIMIT 20");
if ($result) {
    $topics = $result->fetch_all(MYSQLI_ASSOC);
}

// Buscar respostas recentes
$replies = [];
$result = $conn->query("SELECT r.*, t.title as topic_title FROM forum_replies r 
                       LEFT JOIN forum_topics t ON r.topic_id = t.id 
                       ORDER BY r.created_at DESC LIMIT 10");
if ($result) {
    $replies = $result->fetch_all(MYSQLI_ASSOC);
}

$title = 'Gerenciar Fórum';
include 'header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-comments"></i> Gerenciar Fórum</h1>
        <a href="admin_panel.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar ao Painel
        </a>
    </div>
    
    <?php echo $message; ?>
    
    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3><?php echo $conn->query("SELECT COUNT(*) FROM forum_topics")->fetch_row()[0]; ?></h3>
                    <p>Tópicos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3><?php echo $conn->query("SELECT COUNT(*) FROM forum_replies")->fetch_row()[0]; ?></h3>
                    <p>Respostas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3><?php echo $conn->query("SELECT COUNT(DISTINCT author_id) FROM forum_topics")->fetch_row()[0]; ?></h3>
                    <p>Usuários Ativos</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tópicos Recentes -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-list"></i> Tópicos Recentes</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Categoria</th>
                            <th>Respostas</th>
                            <th>Views</th>
                            <th>Criado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topics as $topic): ?>
                        <tr>
                            <td><?php echo $topic['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($topic['title']); ?></strong>
                                <br><small class="text-muted"><?php echo htmlspecialchars(substr($topic['content'], 0, 50)) . '...'; ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($topic['author_name']); ?></td>
                            <td><span class="badge bg-secondary"><?php echo $topic['category']; ?></span></td>
                            <td><?php echo $topic['replies_count']; ?></td>
                            <td><?php echo $topic['views']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($topic['created_at'])); ?></td>
                            <td>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Excluir este tópico?')">
                                    <input type="hidden" name="action" value="delete_topic">
                                    <input type="hidden" name="id" value="<?php echo $topic['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Respostas Recentes -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-reply"></i> Respostas Recentes</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tópico</th>
                            <th>Autor</th>
                            <th>Conteúdo</th>
                            <th>Criado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($replies as $reply): ?>
                        <tr>
                            <td><?php echo $reply['id']; ?></td>
                            <td><?php echo htmlspecialchars($reply['topic_title']); ?></td>
                            <td><?php echo htmlspecialchars($reply['author_name']); ?></td>
                            <td><?php echo htmlspecialchars(substr($reply['content'], 0, 100)) . '...'; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($reply['created_at'])); ?></td>
                            <td>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Excluir esta resposta?')">
                                    <input type="hidden" name="action" value="delete_reply">
                                    <input type="hidden" name="id" value="<?php echo $reply['id']; ?>">
                                    <input type="hidden" name="topic_id" value="<?php echo $reply['topic_id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>