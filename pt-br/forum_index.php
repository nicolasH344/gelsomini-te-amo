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
    
    // Validações adicionais
    if (empty($title_post)) {
        $_SESSION['error'] = 'O título não pode estar vazio';
        redirect('forum_index.php');
    }
    
    if (strlen($title_post) < 5) {
        $_SESSION['error'] = 'O título deve ter no mínimo 5 caracteres';
        redirect('forum_index.php');
    }
    
    if (empty($content)) {
        $_SESSION['error'] = 'O conteúdo não pode estar vazio';
        redirect('forum_index.php');
    }
    
    if (strlen($content) < 20) {
        $_SESSION['error'] = 'O conteúdo deve ter no mínimo 20 caracteres';
        redirect('forum_index.php');
    }
    
    if (empty($category_id)) {
        $_SESSION['error'] = 'Selecione uma categoria';
        redirect('forum_index.php');
    }
    
    if (createForumPost($title_post, $content, $category_id, $user_id)) {
        $_SESSION['success'] = 'Post criado com sucesso!';
        redirect('forum_index.php');
    } else {
        $_SESSION['error'] = 'Erro ao criar o post. Por favor, tente novamente.';
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
    <!-- Mensagens de erro/sucesso -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>
    
    <!-- Header do fórum -->
    <div class="forum-header text-center mb-5">
        <h1 class="display-4 fw-bold mb-3">
            <i class="fas fa-comments"></i>
            Fórum da Comunidade
        </h1>
        <p class="lead">
            Tire dúvidas, compartilhe conhecimento e conecte-se com outros desenvolvedores
        </p>
        <?php if (isLoggedIn()): ?>
            <button type="button" class="btn btn-primary btn-lg rounded-pill mt-3" data-bs-toggle="modal" data-bs-target="#newPostModal">
                <i class="fas fa-plus me-2"></i> Novo Post
            </button>
        <?php else: ?>
            <a href="login.php" class="btn btn-primary btn-lg rounded-pill mt-3" role="button">
                <i class="fas fa-sign-in-alt me-2"></i> Faça Login para Participar
            </a>
        <?php endif; ?>
    </div>

    <div class="row">
        <!-- Conteúdo principal -->
        <div class="col-lg-8">
            <!-- Filtros e busca -->
            <div class="filters-card card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <form method="GET" action="forum_index.php" id="forumFilterForm" class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label for="category" class="form-label fw-semibold">
                                <i class="fas fa-tag text-primary me-1"></i>
                                Categoria
                            </label>
                            <select class="form-select rounded-pill" id="category" name="category" onchange="this.form.submit()">
                                <option value="">Todas as categorias</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo sanitize($cat['id']); ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo sanitize($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-5">
                            <label for="search" class="form-label fw-semibold">
                                <i class="fas fa-search text-info me-1"></i>
                                Buscar
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control rounded-pill rounded-end" id="search" name="search" 
                                       value="<?php echo htmlspecialchars($search); ?>"
                                       placeholder="Buscar posts..."
                                       onkeypress="if(event.key === 'Enter') this.form.submit()">
                                <?php if ($search): ?>
                                    <button type="button" class="btn btn-outline-secondary rounded-pill rounded-start" 
                                            onclick="document.getElementById('search').value=''; document.getElementById('forumFilterForm').submit();"
                                            title="Limpar busca">
                                        <i class="fas fa-times"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Filtros ativos -->
                    <?php if ($category || $search): ?>
                        <div class="active-filters mt-3 pt-3 border-top">
                            <small class="text-muted fw-semibold d-block mb-2">
                                <i class="fas fa-filter me-1"></i>
                                Filtros ativos:
                            </small>
                            <div class="d-flex flex-wrap gap-2">
                                <?php if ($category): 
                                    $catName = 'Categoria';
                                    foreach ($categories as $cat) {
                                        if ($cat['id'] == $category) {
                                            $catName = $cat['name'];
                                            break;
                                        }
                                    }
                                ?>
                                    <span class="badge bg-primary rounded-pill px-3 py-2">
                                        Categoria: <?php echo htmlspecialchars($catName); ?>
                                        <a href="?<?php 
                                            $query = $_GET;
                                            unset($query['category']);
                                            echo http_build_query($query);
                                        ?>" class="text-white ms-2" title="Remover filtro">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($search): ?>
                                    <span class="badge bg-info rounded-pill px-3 py-2">
                                        Busca: "<?php echo htmlspecialchars($search); ?>"
                                        <a href="?<?php 
                                            $query = $_GET;
                                            unset($query['search']);
                                            echo http_build_query($query);
                                        ?>" class="text-white ms-2" title="Remover filtro">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                <?php endif; ?>
                                
                                <span class="badge bg-secondary rounded-pill px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>
                                    <?php echo count($forum_posts); ?> resultado<?php echo count($forum_posts) != 1 ? 's' : ''; ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Lista de posts -->
            <div class="posts-card card shadow-sm border-0">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h2 class="h5 mb-0 fw-bold">
                        <i class="fas fa-fire text-danger me-2"></i>
                        Posts Recentes
                    </h2>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($forum_posts)): ?>
                        <div class="text-center p-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Nenhum post encontrado. Seja o primeiro a postar!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($forum_posts as $index => $post): ?>
                            <div class="forum-post-item border-bottom p-4 <?php echo $index === 0 ? '' : 'border-top'; ?>">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="badge rounded-pill bg-<?php 
                                                $categoryColor = 'secondary';
                                                $postCategory = $post['category'] ?? 'Geral';
                                                foreach ($categories as $cat) {
                                                    if (strtolower($cat['name']) === strtolower($postCategory)) {
                                                        $categoryColor = $cat['color'] ?? 'secondary';
                                                        break;
                                                    }
                                                }
                                                echo $categoryColor;
                                            ?> me-2 px-3 py-2">
                                                <i class="fas fa-tag me-1"></i>
                                                <?php echo sanitize($postCategory); ?>
                                            </span>
                                            
                                            <?php if (isset($post['is_solved']) && $post['is_solved']): ?>
                                                <span class="badge rounded-pill bg-success px-3 py-2">
                                                    <i class="fas fa-check me-1"></i> Resolvido
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <h3 class="h5 fw-bold mb-2">
                                            <a href="show.php?type=forum&id=<?php echo $post['id']; ?>" 
                                               class="text-decoration-none text-dark post-title-link">
                                                <?php echo sanitize($post['title']); ?>
                                            </a>
                                        </h3>
                                        
                                        <p class="text-muted mb-3">
                                            <?php echo sanitize(substr($post['content'], 0, 150)) . '...'; ?>
                                        </p>
                                        
                                        <div class="d-flex align-items-center text-muted small">
                                            <span class="me-3 d-flex align-items-center">
                                                <i class="fas fa-user-circle me-2 text-primary"></i>
                                                <strong><?php echo sanitize($post['author'] ?? 'Usuário Anônimo'); ?></strong>
                                            </span>
                                            <span class="d-flex align-items-center">
                                                <i class="fas fa-clock me-2 text-info"></i>
                                                <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="row text-center g-2">
                                            <div class="col-4">
                                                <div class="stat-box p-3 rounded-3 bg-light">
                                                    <div class="text-primary fw-bold fs-4"><?php echo $post['replies']; ?></div>
                                                    <small class="text-muted">Respostas</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="stat-box p-3 rounded-3 bg-light">
                                                    <div class="text-info fw-bold fs-4"><?php echo $post['views']; ?></div>
                                                    <small class="text-muted">Views</small>
                                                </div>
                                            </div>
                                            <div class="col-4 d-flex align-items-center justify-content-center">
                                                <a href="show.php?type=forum&id=<?php echo $post['id']; ?>" 
                                                   class="btn btn-primary rounded-pill px-4"
                                                   aria-label="Ver post <?php echo sanitize($post['title']); ?>">
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
            <div class="stats-card card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 pt-3">
                    <h2 class="h6 mb-0 fw-bold">
                        <i class="fas fa-chart-bar text-primary me-2"></i> Estatísticas
                    </h2>
                </div>
                <div class="card-body">
                    <div class="row text-center g-3">
                        <div class="col-6">
                            <div class="stat-item p-3 rounded-3 bg-light">
                                <div class="fw-bold text-primary fs-3">
                                    <?php 
                                    // Contar posts totais (sem filtro)
                                    $total_posts = getForumPosts('', '', 1);
                                    echo count($total_posts);
                                    ?>
                                </div>
                                <small class="text-muted fw-semibold">Posts</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item p-3 rounded-3 bg-light">
                                <div class="fw-bold text-success fs-3">
                                    <?php 
                                    // Contar autores únicos
                                    $authors = array();
                                    foreach ($total_posts as $post) {
                                        if (isset($post['author']) && !in_array($post['author'], $authors)) {
                                            $authors[] = $post['author'];
                                        }
                                    }
                                    echo count($authors);
                                    ?>
                                </div>
                                <small class="text-muted fw-semibold">Membros Ativos</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categorias -->
            <div class="categories-card card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 pt-3">
                    <h2 class="h6 mb-0 fw-bold">
                        <i class="fas fa-tags text-warning me-2"></i> Categorias
                    </h2>
                </div>
                <div class="card-body">
                    <?php foreach ($categories as $category): 
                        // Contar posts desta categoria em todos os posts
                        $count = 0;
                        $all_posts = getForumPosts('', '', 1);
                        foreach ($all_posts as $post) {
                            $postCat = $post['category'] ?? '';
                            if (strtolower($postCat) === strtolower($category['name'])) {
                                $count++;
                            }
                        }
                    ?>
                        <div class="category-item d-flex justify-content-between align-items-center mb-3 p-3 rounded-3">
                            <a href="?category=<?php echo $category['id']; ?>" class="text-decoration-none d-flex align-items-center">
                                <i class="fas fa-folder text-<?php echo $category['color']; ?> me-2 fs-5"></i>
                                <span class="category-name fw-semibold text-dark">
                                    <?php echo sanitize($category['name']); ?>
                                </span>
                            </a>
                            <span class="badge bg-light text-dark border px-3 py-2">
                                <strong><?php echo $count; ?></strong> posts
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Posts populares -->
            <div class="popular-card card shadow-sm border-0">
                <div class="card-header bg-white border-0 pt-3">
                    <h2 class="h6 mb-0 fw-bold">
                        <i class="fas fa-fire text-danger me-2"></i> Posts Populares
                    </h2>
                </div>
                <div class="card-body">
                    <?php 
                    $popular_posts = array_slice($forum_posts, 0, 3);
                    foreach ($popular_posts as $post): 
                    ?>
                        <div class="popular-post-item mb-3 pb-3 border-bottom">
                            <h3 class="h6 mb-2">
                                <a href="show.php?type=forum&id=<?php echo $post['id']; ?>" 
                                   class="text-decoration-none text-dark popular-post-link">
                                    <?php echo sanitize($post['title']); ?>
                                </a>
                            </h3>
                            <div class="d-flex align-items-center text-muted small">
                                <i class="fas fa-eye me-2 text-info"></i>
                                <strong><?php echo $post['views']; ?> visualizações</strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Regras do fórum -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="rules-card card shadow-lg border-0">
                <div class="card-header bg-gradient text-white text-center py-4">
                    <i class="fas fa-shield-alt fa-2x mb-3"></i>
                    <h2 class="h4 mb-0 fw-bold">Diretrizes da Comunidade</h2>
                    <p class="mb-0 mt-2 opacity-90">Mantenha nossa comunidade saudável e respeitosa</p>
                </div>
                <div class="card-body p-5">
                    <div class="row g-4">
                        <!-- Boas Práticas -->
                        <div class="col-lg-6">
                            <div class="rules-section good-practices">
                                <div class="section-header mb-4">
                                    <div class="icon-badge success mb-3">
                                        <i class="fas fa-thumbs-up fa-2x"></i>
                                    </div>
                                    <h3 class="h5 fw-bold text-success mb-2">
                                        <i class="fas fa-check-double me-2"></i>
                                        Boas Práticas
                                    </h3>
                                    <p class="text-muted small mb-0">Faça isso para ajudar a comunidade</p>
                                </div>
                                
                                <div class="rule-item-modern">
                                    <div class="rule-icon-modern success">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                    <div class="rule-content">
                                        <h4 class="rule-title">Seja Respeitoso</h4>
                                        <p class="rule-description">Trate outros membros com cortesia e empatia</p>
                                    </div>
                                </div>
                                
                                <div class="rule-item-modern">
                                    <div class="rule-icon-modern success">
                                        <i class="fas fa-lightbulb"></i>
                                    </div>
                                    <div class="rule-content">
                                        <h4 class="rule-title">Títulos Claros</h4>
                                        <p class="rule-description">Use títulos descritivos e objetivos</p>
                                    </div>
                                </div>
                                
                                <div class="rule-item-modern">
                                    <div class="rule-icon-modern success">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <div class="rule-content">
                                        <h4 class="rule-title">Pesquise Primeiro</h4>
                                        <p class="rule-description">Verifique se sua dúvida já foi respondida</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Evite -->
                        <div class="col-lg-6">
                            <div class="rules-section avoid-practices">
                                <div class="section-header mb-4">
                                    <div class="icon-badge danger mb-3">
                                        <i class="fas fa-ban fa-2x"></i>
                                    </div>
                                    <h3 class="h5 fw-bold text-danger mb-2">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Evite Fazer
                                    </h3>
                                    <p class="text-muted small mb-0">Práticas que prejudicam a comunidade</p>
                                </div>
                                
                                <div class="rule-item-modern">
                                    <div class="rule-icon-modern danger">
                                        <i class="fas fa-clone"></i>
                                    </div>
                                    <div class="rule-content">
                                        <h4 class="rule-title">Sem Spam</h4>
                                        <p class="rule-description">Evite posts repetitivos ou desnecessários</p>
                                    </div>
                                </div>
                                
                                <div class="rule-item-modern">
                                    <div class="rule-icon-modern danger">
                                        <i class="fas fa-comment-slash"></i>
                                    </div>
                                    <div class="rule-content">
                                        <h4 class="rule-title">Linguagem Apropriada</h4>
                                        <p class="rule-description">Mantenha o conteúdo profissional e respeitoso</p>
                                    </div>
                                </div>
                                
                                <div class="rule-item-modern">
                                    <div class="rule-icon-modern danger">
                                        <i class="fas fa-user-lock"></i>
                                    </div>
                                    <div class="rule-content">
                                        <h4 class="rule-title">Privacidade</h4>
                                        <p class="rule-description">Não compartilhe dados pessoais sensíveis</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer informativo -->
                    <div class="rules-footer text-center mt-5 pt-4 border-top">
                        <div class="d-flex justify-content-center align-items-center flex-wrap gap-4">
                            <div class="footer-stat">
                                <i class="fas fa-users text-primary fa-2x mb-2"></i>
                                <div class="fw-bold text-dark">Comunidade Ativa</div>
                                <small class="text-muted">Milhares de desenvolvedores</small>
                            </div>
                            <div class="footer-stat">
                                <i class="fas fa-handshake text-info fa-2x mb-2"></i>
                                <div class="fw-bold text-dark">Respeito Mútuo</div>
                                <small class="text-muted">Base da nossa comunidade</small>
                            </div>
                            <div class="footer-stat">
                                <i class="fas fa-graduation-cap text-success fa-2x mb-2"></i>
                                <div class="fw-bold text-dark">Aprendizado Contínuo</div>
                                <small class="text-muted">Compartilhe conhecimento</small>
                            </div>
                        </div>
                        <p class="text-muted mt-4 mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Dúvidas sobre as regras? Entre em contato com a moderação.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para novo post -->
<?php if (isLoggedIn()): ?>
<div class="modal fade" id="newPostModal" tabindex="-1" aria-labelledby="newPostModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="newPostModalLabel">
                    <i class="fas fa-plus me-2"></i>
                    Criar Novo Post
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form method="POST" action="forum_index.php">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="modal-body">
                    <div class="mb-4">
                        <label for="title" class="form-label fw-semibold text-dark">
                            <i class="fas fa-heading text-primary me-2"></i>
                            Título do Post
                        </label>
                        <input type="text" class="form-control form-control-lg" id="title" name="title" 
                               placeholder="Digite um título descritivo para seu post" 
                               required minlength="5" maxlength="200">
                        <small class="text-muted">Mínimo 5 caracteres</small>
                    </div>
                    
                    <div class="mb-4">
                        <label for="category_id" class="form-label fw-semibold text-dark">
                            <i class="fas fa-tag text-warning me-2"></i>
                            Categoria
                        </label>
                        <select class="form-select form-select-lg" id="category_id" name="category_id" required>
                            <option value="" disabled selected>Selecione uma categoria</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>">
                                    <?php echo sanitize($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Escolha a categoria mais adequada ao seu tópico</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label fw-semibold text-dark">
                            <i class="fas fa-edit text-info me-2"></i>
                            Conteúdo
                        </label>
                        <textarea class="form-control" id="content" name="content" rows="8" 
                                  placeholder="Descreva sua dúvida ou compartilhe seu conhecimento. Seja claro e detalhado para receber melhores respostas."
                                  required minlength="20"></textarea>
                        <small class="text-muted">Mínimo 20 caracteres. Use formatação clara e exemplos quando possível.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        Cancelar
                    </button>
                    <button type="submit" name="create_post" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-paper-plane me-2"></i>
                        Publicar Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
/* Cabeçalho do fórum */
.forum-header {
    padding: 2rem 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    margin: -1.5rem -15px 2rem -15px;
    color: white;
    border-radius: 0 0 20px 20px;
}

.forum-header .display-4 {
    font-size: 2.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

.forum-header .lead {
    color: #000000;
    font-size: 1.1rem;
}
/* Cards gerais */

.filters-card, .posts-card, .stats-card, 
.categories-card, .popular-card, .rules-card {
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.filters-card:hover, .stats-card:hover, 
.categories-card:hover, .popular-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

/* Filtros */
.filters-card .form-select,
.filters-card .form-control {
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.filters-card .form-select:focus,
.filters-card .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
}

.input-group .form-control.rounded-end {
    border-top-left-radius: 50rem !important;
    border-bottom-left-radius: 50rem !important;
}

.input-group .btn.rounded-start {
    border-top-right-radius: 50rem !important;
    border-bottom-right-radius: 50rem !important;
}

/* Filtros ativos */
.active-filters .badge {
    font-size: 0.85rem;
    font-weight: 500;
    animation: slideIn 0.3s ease;
}

.active-filters .badge a {
    text-decoration: none;
    opacity: 0.8;
    transition: opacity 0.2s ease;
}

.active-filters .badge a:hover {
    opacity: 1;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Posts do fórum */
.forum-post-item {
    transition: all 0.3s ease;
}

.forum-post-item:hover {
    background-color: #f8f9fa;
}

.post-title-link {
    transition: color 0.3s ease;
}

.post-title-link:hover {
    color: #667eea !important;
}

.stat-box {
    transition: all 0.3s ease;
}

.stat-box:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Sidebar */
.stat-item {
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.category-item {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.category-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
    border-color: #e9ecef;
}

.category-name {
    font-size: 0.95rem;
    color: #2d3748 !important;
    transition: color 0.3s ease;
}

.category-item:hover .category-name {
    color: #667eea !important;
}

.category-item .badge {
    font-size: 0.8rem;
    font-weight: 600;
}

.popular-post-item {
    transition: all 0.3s ease;
}

.popular-post-item:hover {
    transform: translateX(5px);
}

.popular-post-link {
    transition: color 0.3s ease;
    line-height: 1.6;
}

.popular-post-link:hover {
    color: #667eea !important;
}

/* Regras do fórum */
.rules-card {
    border-radius: 20px;
    overflow: hidden;
}

.rules-card .card-header.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.rules-card .card-header i.fa-2x {
    opacity: 0.9;
}

.icon-badge {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    transition: all 0.3s ease;
}

.icon-badge.success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
}

.icon-badge.danger {
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
    color: white;
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
}

.rules-section {
    height: 100%;
}

.section-header {
    text-align: center;
    position: relative;
}

.rule-item-modern {
    display: flex;
    align-items: start;
    padding: 1.25rem;
    background: #f8f9fa;
    border-radius: 15px;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.rule-item-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    background: white;
}

.rule-item-modern:hover .rule-icon-modern.success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    transform: scale(1.1) rotate(5deg);
}

.rule-item-modern:hover .rule-icon-modern.danger {
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
    transform: scale(1.1) rotate(-5deg);
}

.rule-icon-modern {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.25rem;
    transition: all 0.3s ease;
    margin-right: 1rem;
}

.rule-icon-modern.success {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
    border: 2px solid rgba(40, 167, 69, 0.2);
}

.rule-icon-modern.danger {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    border: 2px solid rgba(220, 53, 69, 0.2);
}

.rule-content {
    flex: 1;
}

.rule-title {
    font-size: 1rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.35rem;
    line-height: 1.4;
}

.rule-description {
    font-size: 0.875rem;
    color: #718096;
    margin-bottom: 0;
    line-height: 1.5;
}

.rules-footer {
    background: linear-gradient(to bottom, transparent 0%, #f8f9fa 50%);
    padding-top: 2rem;
}

.footer-stat {
    text-align: center;
    padding: 1rem;
    transition: transform 0.3s ease;
}

.footer-stat:hover {
    transform: translateY(-5px);
}

.footer-stat i {
    display: block;
}

.footer-stat .fw-bold {
    font-size: 0.95rem;
    margin-top: 0.5rem;
}

.footer-stat small {
    display: block;
    font-size: 0.8rem;
}

/* Animações para as regras */
@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.good-practices .rule-item-modern {
    animation: slideInLeft 0.5s ease;
}

.avoid-practices .rule-item-modern {
    animation: slideInRight 0.5s ease;
}

.good-practices .rule-item-modern:nth-child(2) { animation-delay: 0.1s; }
.good-practices .rule-item-modern:nth-child(3) { animation-delay: 0.2s; }
.good-practices .rule-item-modern:nth-child(4) { animation-delay: 0.3s; }

.avoid-practices .rule-item-modern:nth-child(2) { animation-delay: 0.1s; }
.avoid-practices .rule-item-modern:nth-child(3) { animation-delay: 0.2s; }
.avoid-practices .rule-item-modern:nth-child(4) { animation-delay: 0.3s; }

/* Responsividade das regras */
@media (max-width: 991px) {
    .rules-section {
        margin-bottom: 2rem;
    }
    
    .avoid-practices .rule-item-modern {
        animation: slideInLeft 0.5s ease;
    }
}

.rule-icon {
    flex-shrink: 0;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.rule-text {
    line-height: 1.6;
    color: #4a5568;
}

.rules-card ul li {
    transition: all 0.3s ease;
}

.rules-card ul li:hover {
    transform: translateX(5px);
}

/* Modal */
.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

.modal-header {
    border-bottom: 2px solid #e9ecef;
    background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
    padding: 1.5rem;
}

.modal-title {
    color: #2d3748;
    font-size: 1.25rem;
}

.modal-body {
    padding: 2rem;
}

.modal-body .form-label {
    font-size: 0.95rem;
    margin-bottom: 0.5rem;
    color: #2d3748;
}

.modal-body .form-control,
.modal-body .form-select {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    color: #2d3748;
    transition: all 0.3s ease;
}

.modal-body .form-control:focus,
.modal-body .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    color: #2d3748;
}

.modal-body .form-control::placeholder {
    color: #a0aec0;
}

.modal-body textarea.form-control {
    resize: vertical;
    min-height: 150px;
}

.modal-body small.text-muted {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: #718096;
}

.modal-footer {
    border-top: 2px solid #e9ecef;
    padding: 1.5rem;
}

.modal-footer .btn {
    font-weight: 600;
    padding: 0.75rem 2rem;
}

/* Badges */
.badge {
    font-weight: 600;
    letter-spacing: 0.3px;
}

/* Paginação */
.pagination .page-link {
    border-radius: 10px;
    margin: 0 3px;
    border: none;
    color: #667eea;
    font-weight: 600;
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.pagination .page-link:hover {
    background-color: #667eea15;
    color: #667eea;
}

/* Responsividade */
@media (max-width: 768px) {
    .forum-header .display-4 {
        font-size: 2rem;
    }
    
    .forum-post-item .row {
        flex-direction: column;
    }
    
    .forum-post-item .col-md-4 {
        margin-top: 1rem;
    }
    
    .stat-box {
        margin-bottom: 0.5rem;
    }
}

/* Animações */
.forum-post-item {
    animation: fadeInUp 0.5s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.forum-post-item:nth-child(1) { animation-delay: 0.1s; }
.forum-post-item:nth-child(2) { animation-delay: 0.2s; }
.forum-post-item:nth-child(3) { animation-delay: 0.3s; }
.forum-post-item:nth-child(4) { animation-delay: 0.4s; }
.forum-post-item:nth-child(5) { animation-delay: 0.5s; }
</style>

<?php include 'footer.php'; ?>
