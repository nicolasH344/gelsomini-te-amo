<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

$conn = getDBConnection();
$message = '';

// Criar tabela de exercícios se não existir
$conn->query("CREATE TABLE IF NOT EXISTS exercises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    difficulty ENUM('Iniciante', 'Intermediário', 'Avançado') DEFAULT 'Iniciante',
    category VARCHAR(100) DEFAULT 'HTML',
    content TEXT,
    solution TEXT,
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
        $solution = trim($_POST['solution']);
        
        $stmt = $conn->prepare("INSERT INTO exercises (title, description, difficulty, category, content, solution) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $title, $description, $difficulty, $category, $content, $solution);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Exercício criado com sucesso!</div>';
        } else {
            $message = '<div class="alert alert-danger">Erro ao criar exercício.</div>';
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM exercises WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Exercício excluído!</div>';
        } else {
            $message = '<div class="alert alert-danger">Erro ao excluir exercício.</div>';
        }
    }
}

// Buscar exercícios
$exercises = [];
$result = $conn->query("SELECT * FROM exercises ORDER BY created_at DESC");
if ($result) {
    $exercises = $result->fetch_all(MYSQLI_ASSOC);
}

$title = 'Gerenciar Exercícios';
include 'header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-dumbbell"></i> Gerenciar Exercícios</h1>
        <a href="admin_panel.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar ao Painel
        </a>
    </div>
    
    <?php echo $message; ?>
    
    <!-- Formulário para criar exercício -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-plus"></i> Novo Exercício</h5>
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
                    <label class="form-label">Conteúdo do Exercício</label>
                    <textarea name="content" class="form-control" rows="5" placeholder="Instruções e código inicial..."></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Solução</label>
                    <textarea name="solution" class="form-control" rows="5" placeholder="Código da solução..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Criar Exercício
                </button>
            </form>
        </div>
    </div>
    
    <!-- Lista de exercícios -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-list"></i> Exercícios Existentes (<?php echo count($exercises); ?>)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($exercises)): ?>
                <p class="text-muted">Nenhum exercício encontrado.</p>
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
                            <?php foreach ($exercises as $exercise): ?>
                            <tr>
                                <td><?php echo $exercise['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($exercise['title']); ?></strong>
                                    <?php if ($exercise['description']): ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars(substr($exercise['description'], 0, 50)) . '...'; ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?php echo $exercise['category']; ?></span>
                                </td>
                                <td>
                                    <?php
                                    $colors = ['Iniciante' => 'success', 'Intermediário' => 'warning', 'Avançado' => 'danger'];
                                    $color = $colors[$exercise['difficulty']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?php echo $color; ?>"><?php echo $exercise['difficulty']; ?></span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($exercise['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="viewExercise(<?php echo $exercise['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="editExercise(<?php echo $exercise['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Excluir este exercício?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $exercise['id']; ?>">
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

<!-- Modal para visualizar exercício -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Visualizar Exercício</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewContent">
                <!-- Conteúdo carregado via JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
function viewExercise(id) {
    fetch('admin_exercise_api.php?action=get&id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const exercise = data.exercise;
                document.getElementById('viewContent').innerHTML = `
                    <h6>Título:</h6>
                    <p>${exercise.title}</p>
                    
                    <h6>Descrição:</h6>
                    <p>${exercise.description || 'Sem descrição'}</p>
                    
                    <h6>Categoria: <span class="badge bg-primary">${exercise.category}</span></h6>
                    <h6>Dificuldade: <span class="badge bg-secondary">${exercise.difficulty}</span></h6>
                    
                    <h6>Conteúdo:</h6>
                    <pre class="bg-light p-3">${exercise.content || 'Sem conteúdo'}</pre>
                    
                    <h6>Solução:</h6>
                    <pre class="bg-light p-3">${exercise.solution || 'Sem solução'}</pre>
                `;
                new bootstrap.Modal(document.getElementById('viewModal')).show();
            }
        });
}

function editExercise(id) {
    // Implementar edição se necessário
    alert('Funcionalidade de edição em desenvolvimento');
}
</script>

<?php include 'footer.php'; ?>