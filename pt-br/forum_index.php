<?php
require_once 'config.php';
require_once 'forum_functions.php';

$title = 'Fórum';

// Processar criação de post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post']) && isLoggedIn()) {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Token de segurança inválido';
        redirect('forum_index.php');
    }
    
    $title_post = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    $category_id = (int)$_POST['category_id'];
    $user_id = getCurrentUser()['id'];
    
    if (createForumPost($title_post, $content, $category_id, $user_id)) {
        $_SESSION['success'] = 'Post criado com sucesso!';
        redirect('forum_index.php');
    }
}

// Parâmetros de filtro
$category = sanitize($_GET['category'] ?? '');
$search = sanitize($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));

// Buscar dados
$forum_posts = getForumPosts($category, $search, $page);
$categories = getForumCategories();

include 'header.php';
?>

<div class="container mt-4">
    <!-- Header do fórum -->
    <div class="row mb-0">
        <div class="col-md-8">
            <h1><i class="fas fa-comments mb-0" aria-hidden="true"></i> Fórum da Comunidade</h1>
            <p class="lead mb-0">Tire dúvidas, compartilhe conhecimento e conecte-se com outros desenvolvedores</p>
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
            <div class="card mb-2">
                <div class="card-body">
                    <h2 class="h5  mb-0">Filtrar Posts</h2>
                    <form method="GET" action="forum_index.php" class="row g-3">
                        <div class="col-md-4">
                            <label for="category" class="form-label">Categoria</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Todas as categorias</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo sanitize($cat['id']); ?>">
                                        <?php echo sanitize($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="search" class="form-label">Buscar</label>
                            <input type="text" class="form-control" id="search" name="search" 
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
                    <h2 class="h5 mb-2 ">Posts Recentes</h2>
                </div>
                <div class="card-body p-0">
                    <?php foreach ($forum_posts as $index => $post): ?>
                        <div class="border-bottom p-3 <?php echo $index === 0 ? '' : 'border-top'; ?>">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-<?php 
                                            $categoryColor = 'secondary';
                                            $postCategory = $post['category'] ?? 'Geral';
                                            foreach ($categories as $cat) {
                                                if (strtolower($cat['name']) === strtolower($postCategory)) {
                                                    $categoryColor = $cat['color'] ?? 'secondary';
                                                    break;
                                                }
                                            }
                                            echo $categoryColor;
                                        ?> me-2">
                                            <?php echo sanitize($postCategory); ?>
                                        </span>
                                        
                                        <?php if (isset($post['is_solved']) && $post['is_solved']): ?>
                                            <span class="badge bg-success me-2">
                                                <i class="fas fa-check" aria-hidden="true"></i> Resolvido
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <h3 class="h6 mb-2">
                                        <a href="show.php?type=forum&id=<?php echo $post['id']; ?>" 
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
                                            <?php echo sanitize($post['author'] ?? 'Usuário Anônimo'); ?>
                                        </span>
                                        <span class="me-3">
                                            <i class="fas fa-clock me-1" aria-hidden="true"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 text-end">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="text-primary fw-bold"><?php echo $post['replies']; ?></div>
                                            <small class="text-muted">Respostas</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-info fw-bold"><?php echo $post['views']; ?></div>
                                            <small class="text-muted">Visualizações</small>
                                        </div>
                                        <div class="col-4">
                                            <a href="show.php?type=forum&id=<?php echo $post['id']; ?>" 
                                               class="btn btn-outline-primary btn-sm"
                                               aria-label="Ver post <?php echo sanitize($post['title']); ?>">
                                                <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Paginação -->
            <nav aria-label="Navegação de páginas do fórum" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <span class="page-link" aria-label="Página anterior">
                            <i class="fas fa-chevron-left" aria-hidden="true"></i>
                        </span>
                    </li>
                    <li class="page-item active" aria-current="page">
                        <span class="page-link">1</span>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Ir para página 2">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Ir para página 3">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Próxima página">
                            <i class="fas fa-chevron-right" aria-hidden="true"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Estatísticas do fórum -->
            <div class="card mb-2">
                <div class="card-header">
                    <h2 class="h6 mb-2">
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

            <!-- Categorias -->
            <div class="card mb-2">
                <div class="card-header">
                    <h2 class="h6 mb-2">
                        <i class="fas fa-tags" aria-hidden="true"></i> Categorias
                    </h2>
                </div>
                <div class="card-body">
                    <?php foreach ($categories as $category): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-<?php echo $category['color']; ?>">
                                <?php echo sanitize($category['name']); ?>
                            </span>
                            <small class="text-muted">
                                <?php 
                                $count = 0;
                                foreach ($forum_posts as $post) {
                                    $postCat = $post['category'] ?? '';
                                    if (strtolower($postCat) === strtolower($category['name'])) {
                                        $count++;
                                    }
                                }
                                echo $count;
                                ?> posts
                            </small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Posts populares -->
            <div class="card">
                <div class="card-header">
                    <h2 class="h6 mb-2">
                        <i class="fas fa-fire" aria-hidden="true"></i> Posts Populares
                    </h2>
                </div>
                <div class="card-body">
                    <?php 
                    $popular_posts = array_slice($forum_posts, 0, 3);
                    foreach ($popular_posts as $post): 
                    ?>
                        <div class="mb-2">
                            <h3 class="h6 mb-2">
                                <a href="show.php?type=forum&id=<?php echo $post['id']; ?>" 
                                   class="text-decoration-none">
                                    <?php echo sanitize($post['title']); ?>
                                </a>
                            </h3>
                            <small class="text-muted">
                                <?php echo $post['views']; ?> visualizações
                            </small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Regras do fórum -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <h2 class="h5 card-title">
                        <i class="fas fa-info-circle text-info" aria-hidden="true"></i> 
                        Regras do Fórum
                    </h2>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled ">
                                <li class="" style="color: #000;">
                                    <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                                    Seja respeitoso com outros membros
                                </li>
                                <li class="" color: #000;>
                                    <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                                    Use títulos descritivos para seus posts
                                </li>
                                <li class="" color: #000;>
                                    <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                                    Pesquise antes de postar uma dúvida
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled ">
                                <li class="" color: #000;>
                                    <i class="fas fa-times text-danger me-2" aria-hidden="true"></i>
                                    Não faça spam ou posts repetitivos
                                </li>
                                <li class="" color: #000;>
                                    <i class="fas fa-times text-danger me-2" aria-hidden="true"></i>
                                    Evite linguagem ofensiva
                                </li>
                                <li class="" color: #000;>
                                    <i class="fas fa-times text-danger me-2" aria-hidden="true"></i>
                                    Não compartilhe informações pessoais
                                </li>
                            </ul>
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
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
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
