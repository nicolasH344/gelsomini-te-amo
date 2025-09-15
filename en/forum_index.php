<?php
// Incluir configurações
require_once 'config.php';

// Definir título da página
$title = 'Fórum';

// Dados fictícios de posts do fórum para demonstração
$forum_posts = [
    [
        'id' => 1,
        'title' => 'How do I get started with HTML?',
        'content' => 'Im just starting out and would like tips on where to start with HTML...',
        'author' => 'João Silva',
        'category' => 'HTML',
        'replies' => 5,
        'views' => 120,
        'created_at' => '2024-01-15 14:30:00',
        'is_solved' => true
    ],
    [
        'id' => 2,
        'title' => 'Question about CSS Grid',
        'content' => 'Im trying to create a layout with CSS Grid but I cant align the items...',
        'author' => 'Maria Santos',
        'category' => 'CSS',
        'replies' => 3,
        'views' => 85,
        'created_at' => '2024-01-14 09:15:00',
        'is_solved' => false
    ],
    [
        'id' => 3,
        'title' => 'Asynchronous JavaScript - async/await',
        'content' => 'Can someone explain better how async/await works in JavaScript?',
        'author' => 'Pedro Costa',
        'category' => 'JavaScript',
        'replies' => 8,
        'views' => 200,
        'created_at' => '2024-01-13 16:45:00',
        'is_solved' => true
    ],
    [
        'id' => 4,
        'title' => 'PHP and MySQL - Connection',
        'content' => 'Im having trouble connecting PHP with MySQL. Can anyone help?',
        'author' => 'Ana Oliveira',
        'category' => 'PHP',
        'replies' => 12,
        'views' => 350,
        'created_at' => '2024-01-12 11:20:00',
        'is_solved' => true
    ],
    [
        'id' => 5,
        'title' => 'Mobile responsiveness',
        'content' => 'My website doesnt look good on mobile devices. Any tips for improving responsiveness?',
        'author' => 'Carlos Ferreira',
        'category' => 'CSS',
        'replies' => 6,
        'views' => 180,
        'created_at' => '2024-01-11 13:10:00',
        'is_solved' => false
    ]
];

// Categorias do fórum
$categories = [
    ['id' => 'html', 'name' => 'HTML', 'color' => 'danger'],
    ['id' => 'css', 'name' => 'CSS', 'color' => 'primary'],
    ['id' => 'javascript', 'name' => 'JavaScript', 'color' => 'warning'],
    ['id' => 'php', 'name' => 'PHP', 'color' => 'info']
];

include 'header.php';
?>

<div class="container mt-4">
    <!-- Header do fórum -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-comments" aria-hidden="true"></i> Community Forum</h1>
            <p class="lead">Ask questions, share knowledge, and connect with other developers</p>
        </div>
        <div class="col-md-4 text-end">
            <?php if (isLoggedIn()): ?>
                <a href="#" class="btn btn-primary" role="button">
                    <i class="fas fa-plus" aria-hidden="true"></i> New Post
                </a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary" role="button">
                    <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Login to Participate
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
                    <h2 class="h5 card-title">Filter Posts</h2>
                    <form method="GET" action="forum_index.php" class="row g-3">
                        <div class="col-md-4">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo sanitize($cat['id']); ?>">
                                        <?php echo sanitize($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Search posts...">
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
                    <h2 class="h5 mb-0">Recent Posts</h2>
                </div>
                <div class="card-body p-0">
                    <?php foreach ($forum_posts as $index => $post): ?>
                        <div class="border-bottom p-3 <?php echo $index === 0 ? '' : 'border-top'; ?>">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-<?php 
                                            $categoryColor = 'secondary';
                                            foreach ($categories as $cat) {
                                                if (strtolower($cat['name']) === strtolower($post['category'])) {
                                                    $categoryColor = $cat['color'];
                                                    break;
                                                }
                                            }
                                            echo $categoryColor;
                                        ?> me-2">
                                            <?php echo sanitize($post['category']); ?>
                                        </span>
                                        
                                        <?php if ($post['is_solved']): ?>
                                            <span class="badge bg-success me-2">
                                                <i class="fas fa-check" aria-hidden="true"></i> Solved
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
                                            <?php echo sanitize($post['author']); ?>
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
                                            <small class="text-muted">Answers</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-info fw-bold"><?php echo $post['views']; ?></div>
                                            <small class="text-muted">Views</small>
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
                        <a class="page-link" href="#" aria-label="Go to page 2">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Go to page 3">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Next page">
                            <i class="fas fa-chevron-right" aria-hidden="true"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Estatísticas do fórum -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h6 mb-0">
                        <i class="fas fa-chart-bar" aria-hidden="true"></i> Statistics
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
                            <small class="text-muted">Active Members</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categorias -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h6 mb-0">
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
                                    if (strtolower($post['category']) === strtolower($category['name'])) {
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
                    <h2 class="h6 mb-0">
                        <i class="fas fa-fire" aria-hidden="true"></i> Popular Posts
                    </h2>
                </div>
                <div class="card-body">
                    <?php 
                    $popular_posts = array_slice($forum_posts, 0, 3);
                    foreach ($popular_posts as $post): 
                    ?>
                        <div class="mb-3">
                            <h3 class="h6 mb-1">
                                <a href="show.php?type=forum&id=<?php echo $post['id']; ?>" 
                                   class="text-decoration-none">
                                    <?php echo sanitize($post['title']); ?>
                                </a>
                            </h3>
                            <small class="text-muted">
                                <?php echo $post['views']; ?> Views
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
                        Forum Rules
                    </h2>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                                    Be respectful to other members
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                                    Use descriptive titles for your posts
                                </li>
                                <li class="mb-0">
                                    <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                                    Research before posting a question
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-times text-danger me-2" aria-hidden="true"></i>
                                    Don't spam or repetitive posts
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-times text-danger me-2" aria-hidden="true"></i>
                                    Evite linguagem ofensiva
                                </li>
                                <li class="mb-0">
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

<?php include 'footer.php'; ?>

