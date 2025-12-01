<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

$conn = getDBConnection();
$message = '';

// Criar tabela de tutoriais se não existir
$conn->query("CREATE TABLE IF NOT EXISTS tutorials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    difficulty ENUM('Iniciante', 'Intermediário', 'Avançado') DEFAULT 'Iniciante',
    category VARCHAR(100) DEFAULT 'HTML',
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $difficulty = $_POST['difficulty'];
        $category = $_POST['category'];
        $content = trim($_POST['content']);
        
        $stmt = $conn->prepare("INSERT INTO tutorials (title, description, difficulty, category, content) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $description, $difficulty, $category, $content);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Tutorial criado com sucesso!</div>';
        } else {
            $message = '<div class="alert alert-danger">Erro ao criar tutorial.</div>';
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM tutorials WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Tutorial excluído!</div>';
        } else {
            $message = '<div class="alert alert-danger">Erro ao excluir tutorial.</div>';
        }
    }
}

// Buscar tutoriais
$tutorials = [];
$result = $conn->query("SELECT * FROM tutorials ORDER BY created_at DESC");
if ($result) {
    $tutorials = $result->fetch_all(MYSQLI_ASSOC);
}

$title = 'Gerenciar Tutoriais';
include 'header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-book"></i> Gerenciar Tutoriais</h1>
        <a href="admin_panel.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar ao Painel
        </a>
    </div>
    
    <?php echo $message; ?>
    
    <!-- Formulário para criar tutorial -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-plus"></i> Novo Tutorial</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="action" value="create">
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Categoria</label>
                            <select name="category" class="form-select">
                                <option value="HTML">HTML</option>
                                <option value="CSS">CSS</option>
                                <option value="JavaScript">JavaScript</option>
                                <option value="PHP">PHP</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Dificuldade</label>
                            <select name="difficulty" class="form-select">
                                <option value="Iniciante">Iniciante</option>
                                <option value="Intermediário">Intermediário</option>
                                <option value="Avançado">Avançado</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Conteúdo do Tutorial</label>
                    <textarea name="content" class="form-control" rows="10" placeholder="Conteúdo completo do tutorial..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Criar Tutorial
                </button>
            </form>
        </div>
    </div>
    
    <!-- Lista de tutoriais -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-list"></i> Tutoriais Existentes (<?php echo count($tutorials); ?>)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($tutorials)): ?>
                <p class="text-muted">Nenhum tutorial encontrado.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Categoria</th>
                                <th>Dificuldade</th>
                                <th>Criado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tutorials as $tutorial): ?>
                            <tr>
                                <td><?php echo $tutorial['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($tutorial['title']); ?></strong>
                                    <?php if ($tutorial['description']): ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars(substr($tutorial['description'], 0, 50)) . '...'; ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo $tutorial['category']; ?></span>
                                </td>
                                <td>
                                    <?php
                                    $colors = ['Iniciante' => 'success', 'Intermediário' => 'warning', 'Avançado' => 'danger'];
                                    $color = $colors[$tutorial['difficulty']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?php echo $color; ?>"><?php echo $tutorial['difficulty']; ?></span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($tutorial['created_at'])); ?></td>
                                <td>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Excluir este tutorial?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $tutorial['id']; ?>">
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
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>