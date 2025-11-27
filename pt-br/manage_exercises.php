<?php
require_once 'config.php';
require_once 'exercise_functions.php';

// Verificar se é admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

$title = 'Gerenciar Exercícios';
$action = $_GET['action'] ?? 'list';
$exerciseId = (int)($_GET['id'] ?? 0);

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'create' || $action === 'edit') {
        $data = [
            'title' => sanitize($_POST['title']),
            'description' => sanitize($_POST['description']),
            'category' => sanitize($_POST['category']),
            'difficulty' => sanitize($_POST['difficulty']),
            'points' => (int)($_POST['points'] ?? 10),
            'estimated_time' => sanitize($_POST['estimated_time']),
            'instructions' => sanitize($_POST['instructions']),
            'initial_code' => $_POST['initial_code'] ?? '',
            'hints' => array_filter(array_map('trim', explode("\n", $_POST['hints'] ?? ''))),
            'exercise_type' => sanitize($_POST['category'])
        ];
        
        $errors = validateExerciseData($data);
        
        if (empty($errors)) {
            if ($action === 'create') {
                $newId = addExerciseData($data);
                $success = "Exercício criado com sucesso! ID: $newId";
            } else {
                updateExerciseData($exerciseId, $data);
                $success = "Exercício atualizado com sucesso!";
            }
            header('Location: manage_exercises.php?success=' . urlencode($success));
            exit;
        }
    } elseif ($action === 'delete' && $exerciseId > 0) {
        deleteExerciseData($exerciseId);
        header('Location: manage_exercises.php?success=' . urlencode('Exercício excluído com sucesso!'));
        exit;
    }
}

// Buscar exercício para edição
$exercise = null;
if ($action === 'edit' && $exerciseId > 0) {
    $exercise = getExercise($exerciseId);
    if (!$exercise) {
        header('Location: manage_exercises.php?error=' . urlencode('Exercício não encontrado!'));
        exit;
    }
}

include 'header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">
            <i class="fas fa-cogs me-2"></i>
            Gerenciar Exercícios
        </h1>
        <?php if ($action === 'list'): ?>
        <a href="?action=create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Novo Exercício
        </a>
        <?php else: ?>
        <a href="manage_exercises.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar à Lista
        </a>
        <?php endif; ?>
    </div>
    
    <!-- Mensagens -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <h6><i class="fas fa-exclamation-triangle me-2"></i>Erros encontrados:</h6>
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if ($action === 'list'): ?>
        <!-- Lista de Exercícios -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">Lista de Exercícios</h5>
                    </div>
                    <div class="col-auto">
                        <?php $stats = getExerciseStats(); ?>
                        <span class="badge bg-primary"><?php echo $stats['total']; ?> exercícios</span>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Categoria</th>
                                <th>Dificuldade</th>
                                <th>Pontos</th>
                                <th>Tempo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $exercises = getExercises('', '', '', 1, 50);
                            foreach ($exercises as $ex): 
                            ?>
                            <tr>
                                <td><strong><?php echo $ex['id']; ?></strong></td>
                                <td>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($ex['title']); ?></div>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars(substr($ex['description'], 0, 60)) . '...'; ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?php echo $ex['category']; ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $levelColors = ['Iniciante' => 'success', 'Intermediário' => 'warning', 'Avançado' => 'danger'];
                                    $color = $levelColors[$ex['difficulty']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?php echo $color; ?>"><?php echo $ex['difficulty']; ?></span>
                                </td>
                                <td><?php echo $ex['points'] ?? 10; ?></td>
                                <td><?php echo $ex['estimated_time'] ?? '15 min'; ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="interactive_exercises.php?id=<?php echo $ex['id']; ?>" 
                                           class="btn btn-outline-info" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="?action=edit&id=<?php echo $ex['id']; ?>" 
                                           class="btn btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteExercise(<?php echo $ex['id']; ?>)" 
                                                class="btn btn-outline-danger" title="Excluir">
                                            <i class="fas fa-trash"></i>
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
        
        <!-- Estatísticas -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Por Categoria</h6>
                    </div>
                    <div class="card-body">
                        <?php foreach ($stats['by_category'] as $cat => $count): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><?php echo $cat; ?></span>
                            <span class="badge bg-primary"><?php echo $count; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Por Dificuldade</h6>
                    </div>
                    <div class="card-body">
                        <?php foreach ($stats['by_difficulty'] as $diff => $count): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><?php echo $diff; ?></span>
                            <span class="badge bg-warning"><?php echo $count; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
    <?php else: ?>
        <!-- Formulário de Criação/Edição -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-<?php echo $action === 'create' ? 'plus' : 'edit'; ?> me-2"></i>
                    <?php echo $action === 'create' ? 'Novo Exercício' : 'Editar Exercício'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Título *</label>
                                <input type="text" name="title" class="form-control" required
                                       value="<?php echo htmlspecialchars($exercise['title'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Pontos</label>
                                <input type="number" name="points" class="form-control" min="1" max="100"
                                       value="<?php echo $exercise['points'] ?? 10; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descrição *</label>
                        <textarea name="description" class="form-control" rows="2" required><?php echo htmlspecialchars($exercise['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Categoria *</label>
                                <select name="category" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <?php 
                                    $categories = ['HTML', 'CSS', 'JavaScript', 'PHP'];
                                    foreach ($categories as $cat): 
                                    ?>
                                    <option value="<?php echo $cat; ?>" 
                                            <?php echo ($exercise['category'] ?? '') === $cat ? 'selected' : ''; ?>>
                                        <?php echo $cat; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Dificuldade *</label>
                                <select name="difficulty" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <?php 
                                    $difficulties = ['Iniciante', 'Intermediário', 'Avançado'];
                                    foreach ($difficulties as $diff): 
                                    ?>
                                    <option value="<?php echo $diff; ?>" 
                                            <?php echo ($exercise['difficulty'] ?? '') === $diff ? 'selected' : ''; ?>>
                                        <?php echo $diff; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Tempo Estimado</label>
                                <input type="text" name="estimated_time" class="form-control" 
                                       placeholder="ex: 15 min"
                                       value="<?php echo htmlspecialchars($exercise['estimated_time'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Instruções</label>
                        <textarea name="instructions" class="form-control" rows="4" 
                                  placeholder="Descreva o que o usuário deve fazer..."><?php echo htmlspecialchars($exercise['instructions'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Código Inicial</label>
                        <textarea name="initial_code" class="form-control code-textarea" rows="8" 
                                  placeholder="Código inicial que aparecerá no editor..."><?php echo htmlspecialchars($exercise['initial_code'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Dicas (uma por linha)</label>
                        <textarea name="hints" class="form-control" rows="4" 
                                  placeholder="Digite uma dica por linha..."><?php 
                            if (!empty($exercise['hints'])) {
                                echo htmlspecialchars(implode("\n", $exercise['hints']));
                            }
                        ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            <?php echo $action === 'create' ? 'Criar Exercício' : 'Salvar Alterações'; ?>
                        </button>
                        <a href="manage_exercises.php" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function deleteExercise(id) {
    if (confirm('Tem certeza que deseja excluir este exercício? Esta ação não pode ser desfeita.')) {
        window.location.href = `?action=delete&id=${id}`;
    }
}

// Auto-resize para textareas
document.addEventListener('DOMContentLoaded', function() {
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });
});
</script>

<style>
.code-textarea {
    font-family: 'Courier New', monospace;
    font-size: 14px;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.card {
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}
</style>

<?php include 'footer.php'; ?>