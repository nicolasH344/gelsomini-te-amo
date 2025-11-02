<?php
require_once 'config.php';

// Verificar se é admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Incluir dados compartilhados
require_once 'data/tutorials.php';
$tutorials = getTutorials();

$isEdit = isset($_GET['id']);
$tutorial = null;

if ($isEdit) {
    $id = (int)$_GET['id'];
    $tutorial = array_filter($tutorials, fn($t) => $t['id'] === $id);
    $tutorial = $tutorial ? array_values($tutorial)[0] : null;
    
    if (!$tutorial) {
        redirect('admin.php');
    }
    
    $title = 'Editar Tutorial';
} else {
    $title = 'Novo Tutorial';
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => sanitize($_POST['title']),
        'description' => sanitize($_POST['description']),
        'category' => sanitize($_POST['category']),
        'difficulty' => sanitize($_POST['difficulty']),
        'duration' => sanitize($_POST['duration']),
        'status' => sanitize($_POST['status'])
    ];
    
    if ($isEdit) {
        updateTutorial($tutorial['id'], $data);
    } else {
        addTutorial($data);
    }
    
    redirect('admin.php');
}

include 'header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">
                    <i class="fas fa-<?php echo $isEdit ? 'edit' : 'plus'; ?> text-primary"></i>
                    <?php echo $title; ?>
                </h1>
                <a href="admin.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-form"></i>
                        Dados do Tutorial
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Título *</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo $tutorial ? sanitize($tutorial['title']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Duração</label>
                                    <input type="text" class="form-control" id="duration" name="duration" 
                                           value="<?php echo $tutorial ? sanitize($tutorial['duration']) : ''; ?>" 
                                           placeholder="ex: 15 min">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição *</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $tutorial ? sanitize($tutorial['description']) : ''; ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Categoria *</label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Selecione...</option>
                                        <option value="HTML" <?php echo ($tutorial && $tutorial['category'] === 'HTML') ? 'selected' : ''; ?>>HTML</option>
                                        <option value="CSS" <?php echo ($tutorial && $tutorial['category'] === 'CSS') ? 'selected' : ''; ?>>CSS</option>
                                        <option value="JavaScript" <?php echo ($tutorial && $tutorial['category'] === 'JavaScript') ? 'selected' : ''; ?>>JavaScript</option>
                                        <option value="PHP" <?php echo ($tutorial && $tutorial['category'] === 'PHP') ? 'selected' : ''; ?>>PHP</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="difficulty" class="form-label">Dificuldade *</label>
                                    <select class="form-select" id="difficulty" name="difficulty" required>
                                        <option value="">Selecione...</option>
                                        <option value="Iniciante" <?php echo ($tutorial && $tutorial['difficulty'] === 'Iniciante') ? 'selected' : ''; ?>>Iniciante</option>
                                        <option value="Intermediário" <?php echo ($tutorial && $tutorial['difficulty'] === 'Intermediário') ? 'selected' : ''; ?>>Intermediário</option>
                                        <option value="Avançado" <?php echo ($tutorial && $tutorial['difficulty'] === 'Avançado') ? 'selected' : ''; ?>>Avançado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="Rascunho" <?php echo ($tutorial && $tutorial['status'] === 'Rascunho') ? 'selected' : ''; ?>>Rascunho</option>
                                        <option value="Publicado" <?php echo ($tutorial && $tutorial['status'] === 'Publicado') ? 'selected' : ''; ?>>Publicado</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Conteúdo do Tutorial</label>
                            <textarea class="form-control" id="content" name="content" rows="10" 
                                      placeholder="Digite o conteúdo completo do tutorial aqui..."></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i>
                                <?php echo $isEdit ? 'Atualizar' : 'Criar'; ?> Tutorial
                            </button>
                            <a href="admin.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle"></i>
                        Informações
                    </h6>
                </div>
                <div class="card-body">
                    <?php if ($isEdit): ?>
                        <p><strong>ID:</strong> <?php echo $tutorial['id']; ?></p>
                        <p><strong>Criado em:</strong> <?php echo date('d/m/Y', strtotime($tutorial['created_at'])); ?></p>
                        <p><strong>Visualizações:</strong> <?php echo number_format($tutorial['views']); ?></p>
                    <?php endif; ?>
                    
                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-lightbulb"></i>
                            <strong>Dica:</strong> Use markdown para formatar o conteúdo do tutorial.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>