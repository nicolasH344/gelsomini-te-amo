<?php
// Iniciar sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir configurações
require_once 'config.php';

// Definir título da página
$title = 'Tutoriais';

// Dados fictícios de tutoriais para demonstração
$tutorials = [
    [
        'id' => 1,
        'title' => 'Introduction to HTML5',
        'description' => 'Learn the fundamentals of HTML5 and its main tags',
        'category' => 'HTML',
        'duration' => '15 min',
        'level' => 'Beginner',
        'views' => 1250
    ],
    [
        'id' => 2,
        'title' => 'CSS Grid Layout',
        'description' => 'Master the CSS grid system for modern layouts',
        'category' => 'CSS',
        'duration' => '25 min',
        'level' => 'Intermediary',
        'views' => 890
    ],
    [
        'id' => 3,
        'title' => 'JavaScript ES6+',
        'description' => 'Discover the modern features of JavaScript',
        'category' => 'JavaScript',
        'duration' => '30 min',
        'level' => 'Intermediary',
        'views' => 2100
    ],
    [
        'id' => 4,
        'title' => 'Accessible Forms',
        'description' => 'Create forms that work for all users',
        'category' => 'HTML',
        'duration' => '20 min',
        'level' => 'Intermediary',
        'views' => 650
    ],
    [
        'id' => 5,
        'title' => 'Flexbox in Practice',
        'description' => 'Learn how to use Flexbox for flexible layouts',
        'category' => 'CSS',
        'duration' => '18 min',
        'level' => 'Beginner',
        'views' => 1800
    ],
    [
        'id' => 6,
        'title' => 'PHP Básico',
        'description' => 'Primeiros passos com PHP para desenvolvimento web',
        'category' => 'PHP',
        'duration' => '35 min',
        'level' => 'Beginner',
        'views' => 980
    ]
];

include 'header.php';

?>

<div class="container mt-4">
    <!-- Header da página -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-book-open" aria-hidden="true"></i> Tutorials</h1>
            <p class="lead">Learn web development with our detailed tutorials</p>
        </div>
        <div class="col-md-4 text-end">
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <a href="admin.php" class="btn btn-success" role="button">
                    <i class="fas fa-plus" aria-hidden="true"></i> Differentiate Tutorials
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 card-title">Filter Tutorials</h2>
            <form method="GET" action="tutorials_index.php" class="row g-3">
                <div class="col-md-3">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">All categories</option>
                        <option value="HTML">HTML</option>
                        <option value="CSS">CSS</option>
                        <option value="JavaScript">JavaScript</option>
                        <option value="PHP">PHP</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="level" class="form-label">Level</label>
                    <select class="form-select" id="level" name="level">
                        <option value="">All levels</option>
                        <option value="Iniciante">Beginner</option>
                        <option value="Intermediário">Intermediary</option>
                        <option value="Avançado">Forward</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Enter keywords...">
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search" aria-hidden="true"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de tutoriais -->
    <div class="row">
        <?php foreach ($tutorials as $tutorial): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="badge bg-<?php 
                            echo $tutorial['category'] === 'HTML' ? 'danger' : 
                                ($tutorial['category'] === 'CSS' ? 'primary' : 
                                ($tutorial['category'] === 'JavaScript' ? 'warning' : 'info')); 
                        ?>">
                            <?php echo htmlspecialchars($tutorial['category']); ?>
                        </span>
                        <span class="badge bg-<?php 
                            echo $tutorial['level'] === 'Iniciante' ? 'success' : 
                                ($tutorial['level'] === 'Intermediário' ? 'warning' : 'danger'); 
                        ?>">
                            <?php echo htmlspecialchars($tutorial['level']); ?>
                        </span>
                    </div>
                    
                    <div class="card-body">
                        <h3 class="card-title h5"><?php echo htmlspecialchars($tutorial['title']); ?></h3>
                        <p class="card-text"><?php echo htmlspecialchars($tutorial['description']); ?></p>
                        
                        <div class="d-flex justify-content-between text-muted small">
                            <span>
                                <i class="fas fa-clock me-1" aria-hidden="true"></i>
                                <?php echo htmlspecialchars($tutorial['duration']); ?>
                            </span>
                            <span>
                                <i class="fas fa-eye me-1" aria-hidden="true"></i>
                                <?php echo number_format($tutorial['views']); ?> Views
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="d-flex gap-2">
                            <a href="show.php?type=tutorial&id=<?php echo $tutorial['id']; ?>" 
                               class="btn btn-primary btn-sm flex-fill">
                                <i class="fas fa-play" aria-hidden="true"></i> Read Tutorial
                            </a>
                            <a href="show.php?type=tutorial&id=<?php echo $tutorial['id']; ?>&preview=1" 
                               class="btn btn-outline-secondary btn-sm"
                               aria-label="Visualizar tutorial <?php echo htmlspecialchars($tutorial['title']); ?>">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Paginação (simulada) -->
    <nav aria-label="Navegação de páginas dos tutoriais" class="mt-4">
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

    <!-- Categorias populares -->
    <div class="row mt-5">
        <div class="col-12">
            <h2 class="h4 mb-4">Popular Categories</h2>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="fab fa-html5" style="font-size: 2rem;" aria-hidden="true"></i>
                    <h3 class="h5 mt-2">HTML5</h3>
                    <p class="mb-0">2 tutorials</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fab fa-css3-alt" style="font-size: 2rem;" aria-hidden="true"></i>
                    <h3 class="h5 mt-2">CSS3</h3>
                    <p class="mb-0">2 tutorials</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <i class="fab fa-js-square" style="font-size: 2rem;" aria-hidden="true"></i>
                    <h3 class="h5 mt-2">JavaScript</h3>
                    <p class="mb-0">1 tutorials</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fab fa-php" style="font-size: 2rem;" aria-hidden="true"></i>
                    <h3 class="h5 mt-2">PHP</h3>
                    <p class="mb-0">1 tutorial</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações adicionais -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h2 class="h5 card-title">
                        <i class="fas fa-graduation-cap text-success" aria-hidden="true"></i> 
                        How to Get the Most Out of Tutorials
                    </h2>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Read carefully and practice the examples.
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Take notes on the important points.
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Apply your knowledge in the exercises
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h2 class="h5 card-title">
                        <i class="fas fa-question-circle text-info" aria-hidden="true"></i> 
                        Need assistance?
                    </h2>
                    <p class="mb-3">Do you have questions about any tutorial? Our community is here to help!</p>
                    <a href="forum_index.php" class="btn btn-info btn-sm">
                        <i class="fas fa-comments" aria-hidden="true"></i> Go to the Forum
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php';
