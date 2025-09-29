<?php
// Iniciar sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir configurações
require_once 'config.php';

// Definir título da página
$title = 'Tutoriales';

// Dados fictícios de tutoriais para demonstração
$tutorials = [
    [
        'id' => 1,
        'title' => 'Introducción a HTML5',
        'description' => 'Aprenda los fundamentos de HTML5 y sus principales etiquetas.',
        'category' => 'HTML',
        'duration' => '15 min',
        'level' => 'Principiante',
        'views' => 1250
    ],
    [
        'id' => 2,
        'title' => 'CSS Grid Layout',
        'description' => 'Domina el sistema de cuadrículas CSS para diseños modernos',
        'category' => 'CSS',
        'duration' => '25 min',
        'level' => 'Intermedio',
        'views' => 890
    ],
    [
        'id' => 3,
        'title' => 'JavaScript ES6+',
        'description' => 'Conoce las modernas funcionalidades de JavaScript',
        'category' => 'JavaScript',
        'duration' => '30 min',
        'level' => 'Intermedio',
        'views' => 2100
    ],
    [
        'id' => 4,
        'title' => 'Formularios accesibles',
        'description' => 'Cree formularios que funcionen para todos los usuarios.',
        'category' => 'HTML',
        'duration' => '20 min',
        'level' => 'Intermedio',
        'views' => 650
    ],
    [
        'id' => 5,
        'title' => 'Flexbox en la práctica',
        'description' => 'Aprenda a utilizar Flexbox para diseños flexibles',
        'category' => 'CSS',
        'duration' => '18 min',
        'level' => 'Principiante',
        'views' => 1800
    ],
    [
        'id' => 6,
        'title' => 'PHP Básico',
        'description' => 'Primeros pasos con PHP para el desarrollo web',
        'category' => 'PHP',
        'duration' => '35 min',
        'level' => 'Principiante',
        'views' => 980
    ]
];

include 'header.php';

?>

<div class="container mt-4">
    <!-- Header da página -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-book-open" aria-hidden="true"></i> Tutoriales</h1>
            <p class="lead">Aprenda desarrollo web con nuestros tutoriales detallados.</p>
        </div>
        <div class="col-md-4 text-end">
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <a href="admin.php" class="btn btn-success" role="button">
                    <i class="fas fa-plus" aria-hidden="true"></i> Administrar tutoriales
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 card-title">Filtrar tutoriales</h2>
            <form method="GET" action="tutorials_index.php" class="row g-3">
                <div class="col-md-3">
                    <label for="category" class="form-label">Categoría</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Todas las categorías</option>
                        <option value="HTML">HTML</option>
                        <option value="CSS">CSS</option>
                        <option value="JavaScript">JavaScript</option>
                        <option value="PHP">PHP</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="level" class="form-label">Nível</label>
                    <select class="form-select" id="level" name="level">
                        <option value="">Todos los niveles</option>
                        <option value="Iniciante">Principiante</option>
                        <option value="Intermediário">Intermedio</option>
                        <option value="Avançado">Avanzado</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Digite palavras-chave...">
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search" aria-hidden="true"></i> Filtrar
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
                                <?php echo number_format($tutorial['views']); ?> visualizaciones
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="d-flex gap-2">
                            <a href="show.php?type=tutorial&id=<?php echo $tutorial['id']; ?>" 
                               class="btn btn-primary btn-sm flex-fill">
                                <i class="fas fa-play" aria-hidden="true"></i> Leer tutorial
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

    <!-- Categorias populares -->
    <div class="row mt-5">
        <div class="col-12">
            <h2 class="h4 mb-4">Categorías populares</h2>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="fab fa-html5" style="font-size: 2rem;" aria-hidden="true"></i>
                    <h3 class="h5 mt-2">HTML5</h3>
                    <p class="mb-0">2 tutoriales</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fab fa-css3-alt" style="font-size: 2rem;" aria-hidden="true"></i>
                    <h3 class="h5 mt-2">CSS3</h3>
                    <p class="mb-0">2  tutoriales</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <i class="fab fa-js-square" style="font-size: 2rem;" aria-hidden="true"></i>
                    <h3 class="h5 mt-2">JavaScript</h3>
                    <p class="mb-0">1  tutorial</p>
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
                        Cómo aprovechar los tutoriales
                    </h2>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Lee con atención y practica con los ejemplos.
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Tome notas de los puntos importantes.
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Aplique los conocimientos en los ejercicios.
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
                               ¿Necesita ayuda?
                    </h2>
                    <p class="mb-3">¿Tienes dudas sobre algún tutorial? ¡Nuestra comunidad está aquí para ayudarte!</p>
                    <a href="forum_index.php" class="btn btn-info btn-sm">
                        <i class="fas fa-comments" aria-hidden="true"></i> Ir al foro
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php';
