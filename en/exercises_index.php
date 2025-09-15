<?php
// Incluir configurações
require_once 'config.php';

// Definir título da página
$title = 'Exercícios';

// Dados fictícios de exercícios para demonstração
$exercises = [
    [
        'id' => 1,
        'title' => 'Basic HTML Structure',
        'description' => 'Learn how to create the basic structure of an HTML page',
        'difficulty' => 'Beginner',
        'category' => 'HTML',
        'completed' => false
    ],
    [
        'id' => 2,
        'title' => 'Styling with CSS',
        'description' => 'Practice basic styling with CSS',
        'difficulty' => 'Beginner',
        'category' => 'CSS',
        'completed' => false
    ],
    [
        'id' => 3,
        'title' => 'Interactivity with JavaScript',
        'description' => 'Add interactivity to your pages',
        'difficulty' => 'Intermediary',
        'category' => 'JavaScript',
        'completed' => false
    ],
    [
        'id' => 4,
        'title' => 'HTML forms',
        'description' => 'Create functional and accessible forms',
        'difficulty' => 'Beginner',
        'category' => 'HTML',
        'completed' => false
    ],
    [
        'id' => 5,
        'title' => 'Responsive Layout',
        'description' => 'Develop layouts that adapt to different screens',
        'difficulty' => 'Intermediary',
        'category' => 'CSS',
        'completed' => false
    ],
    [
        'id' => 6,
        'title' => 'DOM manipulation',
        'description' => 'Learn how to manipulate page elements dynamically',
        'difficulty' => 'Intermediary',
        'category' => 'JavaScript',
        'completed' => false
    ]
];

include 'header.php';
?>

<div class="container mt-4">
    <!-- Header da página -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-tasks" aria-hidden="true"></i> Practical Exercises</h1>
            <p class="lead">Practice your skills with interactive exercises</p>
        </div>
        <div class="col-md-4 text-end">
            <?php if (isAdmin()): ?>
                <a href="#" class="btn btn-success" role="button">
                    <i class="fas fa-plus" aria-hidden="true"></i> Manage Exercises
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 card-title">Filter Exercises</h2>
            <form method="GET" action="exercises_index.php" class="row g-3">
                <div class="col-md-3">
                    <label for="category" class="form-label">Categoria</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">All categories</option>
                        <option value="HTML">HTML</option>
                        <option value="CSS">CSS</option>
                        <option value="JavaScript">JavaScript</option>
                        <option value="PHP">PHP</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="difficulty" class="form-label">Difficulty</label>
                    <select class="form-select" id="difficulty" name="difficulty">
                        <option value="">All the difficulties</option>
                        <option value="Iniciante">Beginner</option>
                        <option value="Intermediário">Intermediary</option>
                        <option value="Avançado">Advanced</option>
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

    <!-- Lista de exercícios -->
    <div class="row">
        <?php foreach ($exercises as $exercise): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="badge bg-<?php 
                            echo $exercise['category'] === 'HTML' ? 'danger' : 
                                ($exercise['category'] === 'CSS' ? 'primary' : 
                                ($exercise['category'] === 'JavaScript' ? 'warning' : 'info')); 
                        ?>">
                            <?php echo sanitize($exercise['category']); ?>
                        </span>
                        <span class="badge bg-<?php 
                            echo $exercise['difficulty'] === 'Iniciante' ? 'success' : 
                                ($exercise['difficulty'] === 'Intermediário' ? 'warning' : 'danger'); 
                        ?>">
                            <?php echo sanitize($exercise['difficulty']); ?>
                        </span>
                    </div>
                    
                    <div class="card-body">
                        <h3 class="card-title h5"><?php echo sanitize($exercise['title']); ?></h3>
                        <p class="card-text"><?php echo sanitize($exercise['description']); ?></p>
                        
                        <?php if ($exercise['completed']): ?>
                            <div class="alert alert-success py-2" role="alert">
                                <i class="fas fa-check-circle me-1" aria-hidden="true"></i>
                                <small>Completed</small>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="d-flex gap-2">
                            <a href="show.php?type=exercise&id=<?php echo $exercise['id']; ?>" 
                               class="btn btn-primary btn-sm flex-fill">
                                <i class="fas fa-play" aria-hidden="true"></i> 
                                <?php echo $exercise['completed'] ? 'Revisar' : 'Start'; ?>
                            </a>
                            <a href="show.php?type=exercise&id=<?php echo $exercise['id']; ?>&preview=1" 
                               class="btn btn-outline-secondary btn-sm"
                               aria-label="Visualizar exercício <?php echo sanitize($exercise['title']); ?>">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Paginação (simulada) -->
    <nav aria-label="Navegação de páginas dos exercícios" class="mt-4">
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
                <a class="page-link" href="#" aria-label="Go to pge 3">3</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#" aria-label="Next page">
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Informações adicionais -->
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h2 class="h5 card-title">
                        <i class="fas fa-lightbulb text-warning" aria-hidden="true"></i> 
                        Tips for studying
                    </h2>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Start with beginner-level exercises
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Practice regularly to fix knowledge
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Use the forum to ask questions
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h2 class="h5 card-title">
                        <i class="fas fa-chart-line text-info" aria-hidden="true"></i> 
                        Your Progress
                    </h2>
                    <p class="mb-2">Completed exercises: <strong>0 de <?php echo count($exercises); ?></strong></p>
                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: 0%" 
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" 
                             aria-label="Progresso geral: 0%">
                            0%
                        </div>
                    </div>
                    <a href="progress.php" class="btn btn-info btn-sm">
                        <i class="fas fa-chart-bar" aria-hidden="true"></i> View Detailed Progress
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

