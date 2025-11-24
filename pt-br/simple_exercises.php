<?php
require_once 'config.php';

$title = 'Exercícios';

// Dados de exemplo para exercícios
$exercises = [
    [
        'id' => 1,
        'title' => 'HTML Básico - Primeira Página',
        'description' => 'Crie sua primeira página HTML com título e parágrafo',
        'difficulty' => 'beginner',
        'category_name' => 'HTML',
        'points' => 10
    ],
    [
        'id' => 2,
        'title' => 'CSS - Estilizando Texto',
        'description' => 'Aprenda a estilizar texto com CSS básico',
        'difficulty' => 'beginner',
        'category_name' => 'CSS',
        'points' => 15
    ],
    [
        'id' => 3,
        'title' => 'JavaScript - Variáveis',
        'description' => 'Trabalhe com variáveis e tipos de dados em JavaScript',
        'difficulty' => 'intermediate',
        'category_name' => 'JavaScript',
        'points' => 20
    ],
    [
        'id' => 4,
        'title' => 'HTML - Formulários',
        'description' => 'Crie formulários interativos com HTML',
        'difficulty' => 'intermediate',
        'category_name' => 'HTML',
        'points' => 18
    ],
    [
        'id' => 5,
        'title' => 'CSS - Layout Flexbox',
        'description' => 'Domine layouts flexíveis com Flexbox',
        'difficulty' => 'advanced',
        'category_name' => 'CSS',
        'points' => 25
    ],
    [
        'id' => 6,
        'title' => 'JavaScript - DOM Manipulation',
        'description' => 'Manipule elementos da página com JavaScript',
        'difficulty' => 'advanced',
        'category_name' => 'JavaScript',
        'points' => 30
    ]
];

// Filtros
$category = sanitize($_GET['category'] ?? '');
$difficulty = sanitize($_GET['difficulty'] ?? '');
$search = sanitize($_GET['search'] ?? '');

// Aplicar filtros
if ($category) {
    $exercises = array_filter($exercises, function($ex) use ($category) {
        return strtolower($ex['category_name']) === strtolower($category);
    });
}

if ($difficulty) {
    $exercises = array_filter($exercises, function($ex) use ($difficulty) {
        return $ex['difficulty'] === strtolower($difficulty);
    });
}

if ($search) {
    $exercises = array_filter($exercises, function($ex) use ($search) {
        return stripos($ex['title'], $search) !== false || 
               stripos($ex['description'], $search) !== false;
    });
}

include 'header.php';
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="fas fa-tasks me-2"></i>Exercícios Práticos</h1>
            <p class="lead">Pratique suas habilidades com exercícios interativos</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <form method="GET" action="">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="category" class="form-label">Categoria</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Todas as categorias</option>
                            <option value="HTML" <?php echo ($category === 'HTML') ? 'selected' : ''; ?>>HTML</option>
                            <option value="CSS" <?php echo ($category === 'CSS') ? 'selected' : ''; ?>>CSS</option>
                            <option value="JavaScript" <?php echo ($category === 'JavaScript') ? 'selected' : ''; ?>>JavaScript</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="difficulty" class="form-label">Dificuldade</label>
                        <select class="form-select" id="difficulty" name="difficulty">
                            <option value="">Todas as dificuldades</option>
                            <option value="beginner" <?php echo ($difficulty === 'beginner') ? 'selected' : ''; ?>>Iniciante</option>
                            <option value="intermediate" <?php echo ($difficulty === 'intermediate') ? 'selected' : ''; ?>>Intermediário</option>
                            <option value="advanced" <?php echo ($difficulty === 'advanced') ? 'selected' : ''; ?>>Avançado</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Digite palavras-chave...">
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de exercícios -->
    <div class="row">
        <?php if (empty($exercises)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h4>Nenhum exercício encontrado!</h4>
                    <p>Tente ajustar os filtros ou <a href="simple_exercises.php" class="alert-link">limpar a busca</a>.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($exercises as $exercise): 
                $difficulty_map = [
                    'beginner' => 'Iniciante', 
                    'intermediate' => 'Intermediário', 
                    'advanced' => 'Avançado'
                ];
                $display_difficulty = $difficulty_map[$exercise['difficulty']] ?? 'Iniciante';
            ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="badge bg-<?php 
                                echo ($exercise['category_name'] === 'HTML') ? 'danger' : 
                                    (($exercise['category_name'] === 'CSS') ? 'primary' : 'warning'); 
                            ?>">
                                <?php echo htmlspecialchars($exercise['category_name']); ?>
                            </span>
                            <span class="badge bg-<?php 
                                echo ($display_difficulty === 'Iniciante') ? 'success' : 
                                    (($display_difficulty === 'Intermediário') ? 'warning' : 'danger'); 
                            ?>">
                                <?php echo htmlspecialchars($display_difficulty); ?>
                            </span>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title h5"><?php echo htmlspecialchars($exercise['title']); ?></h3>
                            <p class="card-text flex-grow-1"><?php echo htmlspecialchars($exercise['description']); ?></p>
                            
                            <div class="mt-auto">
                                <small class="text-muted">
                                    <i class="fas fa-star me-1"></i>
                                    <?php echo $exercise['points']; ?> pontos
                                </small>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <a href="exercise_detail.php?id=<?php echo $exercise['id']; ?>" 
                               class="btn btn-primary w-100">
                                <i class="fas fa-play me-1"></i> Começar Exercício
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Informações adicionais -->
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h2 class="h5 card-title">
                        <i class="fas fa-lightbulb text-warning"></i> 
                        Dicas para Estudar
                    </h2>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Comece pelos exercícios de nível iniciante
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Pratique regularmente para fixar o conhecimento
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Use o fórum para tirar dúvidas
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h2 class="h5 card-title">
                        <i class="fas fa-chart-line text-info"></i> 
                        Estatísticas
                    </h2>
                    <p class="mb-2">Total de exercícios: <strong><?php echo count($exercises); ?></strong></p>
                    <p class="mb-2">Exercícios disponíveis: <strong>6</strong></p>
                    <p class="mb-0">Categorias: <strong>3</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>