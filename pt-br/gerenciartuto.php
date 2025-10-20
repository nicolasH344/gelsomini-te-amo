<?php
require_once 'config.php';

// Verificar se é admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$title = 'Administração - Tutoriais';

// Dados fictícios de tutoriais
$tutorials = [
    [
        'id' => 1,
        'title' => 'Introdução ao HTML',
        'description' => 'Aprenda os fundamentos do HTML',
        'category' => 'HTML',
        'difficulty' => 'Iniciante',
        'status' => 'Publicado',
        'created_at' => '2024-01-15'
    ],
    [
        'id' => 2,
        'title' => 'CSS Básico',
        'description' => 'Estilização com CSS',
        'category' => 'CSS',
        'difficulty' => 'Iniciante',
        'status' => 'Rascunho',
        'created_at' => '2024-01-20'
    ],
    [
        'id' => 3,
        'title' => 'JavaScript Essencial',
        'description' => 'Programação com JavaScript',
        'category' => 'JavaScript',
        'difficulty' => 'Intermediário',
        'status' => 'Publicado',
        'created_at' => '2024-01-25'
    ]
];

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete':
                // Lógica para deletar tutorial
                break;
            case 'toggle_status':
                // Lógica para alterar status
                break;
        }
        redirect('admin.php');
    }
}

include 'header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">
                    <i class="fas fa-cogs text-primary"></i>
                    Administração - Tutoriais
                </h1>
                <a href="admin_tutorial_form.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Novo Tutorial
                </a>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Total</h5>
                            <h3><?php echo count($tutorials); ?></h3>
                        </div>
                        <i class="fas fa-book fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Publicados</h5>
                            <h3><?php echo count(array_filter($tutorials, fn($t) => $t['status'] === 'Publicado')); ?></h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Rascunhos</h5>
                            <h3><?php echo count(array_filter($tutorials, fn($t) => $t['status'] === 'Rascunho')); ?></h3>
                        </div>
                        <i class="fas fa-edit fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Categorias</h5>
                            <h3><?php echo count(array_unique(array_column($tutorials, 'category'))); ?></h3>
                        </div>
                        <i class="fas fa-tags fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Tutoriais -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i>
                        Gerenciar Tutoriais
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Categoria</th>
                                    <th>Dificuldade</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tutorials as $tutorial): ?>
                                <tr>
                                    <td><?php echo $tutorial['id']; ?></td>
                                    <td>
                                        <strong><?php echo sanitize($tutorial['title']); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo sanitize($tutorial['description']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo sanitize($tutorial['category']); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $tutorial['difficulty'] === 'Iniciante' ? 'success' : ($tutorial['difficulty'] === 'Intermediário' ? 'warning' : 'danger'); ?>">
                                            <?php echo sanitize($tutorial['difficulty']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $tutorial['status'] === 'Publicado' ? 'success' : 'warning'; ?>">
                                            <?php echo sanitize($tutorial['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($tutorial['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="admin_tutorial_form.php?id=<?php echo $tutorial['id']; ?>" 
                                               class="btn btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Tem certeza?')">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <input type="hidden" name="id" value="<?php echo $tutorial['id']; ?>">
                                                <button type="submit" class="btn btn-outline-warning" title="Alterar Status">
                                                    <i class="fas fa-toggle-on"></i>
                                                </button>
                                            </form>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $tutorial['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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
    </div>
</div>

<?php include 'footer.php'; ?>