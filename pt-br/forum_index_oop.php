<?php
require_once 'config.php';
require_once '../src/autoload.php';

use App\Controllers\ForumController;

$title = 'Fórum';
$controller = new ForumController();
$data = $controller->index();

extract($data);
$forum_posts = $posts;
$categories = $categories;

include 'header.php';
?>

<div class="container mt-4">
    <!-- Header do fórum -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-comments" aria-hidden="true"></i> Fórum da Comunidade</h1>
            <p class="lead">Tire dúvidas, compartilhe conhecimento e conecte-se com outros desenvolvedores</p>
        </div>
        <div class="col-md-4 text-end">
            <?php if (isLoggedIn()): ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newPostModal">
                    <i class="fas fa-plus" aria-hidden="true"></i> Novo Post
                </button>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary" role="button">
                    <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Faça Login para Participar
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Conteúdo principal -->
        <div class="col-lg-8">
            <!-- Filtros e busca -->
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h5 card-title">Filtrar Posts</h2>
                    <form method="GET" action="forum_index_oop.php" class="row g-3">
                        <div class="col-md-4">
                            <label for="category" class="form-label">Categoria</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Todas as categorias</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo sanitize($cat['name']); ?>" <?php echo ($category === $cat['name']) ? 'selected' : ''; ?>>
                                        <?php echo sanitize($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="search" class="form-label">Buscar</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   placeholder="Buscar posts...">
                        </div>
                        
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fas fa-search" aria-hidden="true"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de posts -->
            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0">Posts Recentes</h2>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($forum_posts)): ?>
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-comments fa-3x mb-3"></i>
                            <h5>Nenhum post encontrado</h5>
                            <p>Seja o primeiro a criar um post!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($forum_posts as $index => $post): ?>
                            <div class="border-bottom p-3">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-primary me-2">
                                                <?php echo sanitize($post['category_name'] ?? 'Geral'); ?>
                                            </span>
                                        </div>
                                        
                                        <h3 class="h6 mb-2">
                                            <a href="forum_post_oop.php?id=<?php echo $post['id']; ?>" 
                                               class="text-decoration-none">
                                                <?php echo sanitize($post['title']); ?>
                                            </a>
                                        </h3>
                                        
                                        <p class="text-muted small mb-2">
                                            <?php echo sanitize(substr($post['content'], 0, 100)) . '...'; ?>
                                        </p>
                                        
                                        <div class="d-flex align-items-center text-muted small">
                                            <span class="me-3">
                                                <i class="fas fa-user me-1" aria-hidden="true"></i>
                                                <?php echo sanitize($post['username'] ?? 'Usuário'); ?>
                                            </span>
                                            <span class="me-3">
                                                <i class="fas fa-clock me-1" aria-hidden="true"></i>
                                                <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4 text-end">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="text-primary fw-bold"><?php echo $post['replies'] ?? 0; ?></div>
                                                <small class="text-muted">Respostas</small>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-info fw-bold"><?php echo $post['views'] ?? 0; ?></div>
                                                <small class="text-muted">Visualizações</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h6 mb-0">
                        <i class="fas fa-chart-bar" aria-hidden="true"></i> Estatísticas
                    </h2>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-bold text-primary"><?php echo count($forum_posts); ?></div>
                            <small class="text-muted">Posts</small>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-success">15</div>
                            <small class="text-muted">Membros Ativos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para novo post -->
<?php if (isLoggedIn()): ?>
<div class="modal fade" id="newPostModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>
                    Criar Novo Post
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Categoria</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>">
                                    <?php echo sanitize($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Conteúdo</label>
                        <textarea class="form-control" id="content" name="content" rows="6" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="create_post" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>
                        Publicar Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'footer.php'; ?>