<?php
require_once 'config.php';

$title = 'Tutoriais';

// Dados de exemplo para tutoriais
$tutorials = [
    [
        'id' => 1,
        'title' => 'Introdução ao HTML',
        'description' => 'Aprenda os conceitos básicos do HTML e suas principais tags',
        'difficulty' => 'beginner',
        'category_name' => 'HTML',
        'reading_time' => 10
    ],
    [
        'id' => 2,
        'title' => 'CSS Fundamentos',
        'description' => 'Domine os seletores, propriedades e valores do CSS',
        'difficulty' => 'beginner',
        'category_name' => 'CSS',
        'reading_time' => 15
    ],
    [
        'id' => 3,
        'title' => 'JavaScript para Iniciantes',
        'description' => 'Primeiros passos com JavaScript: variáveis, funções e eventos',
        'difficulty' => 'beginner',
        'category_name' => 'JavaScript',
        'reading_time' => 20
    ],
    [
        'id' => 4,
        'title' => 'HTML Semântico',
        'description' => 'Aprenda a usar tags semânticas para melhor estrutura',
        'difficulty' => 'intermediate',
        'category_name' => 'HTML',
        'reading_time' => 12
    ],
    [
        'id' => 5,
        'title' => 'CSS Grid Layout',
        'description' => 'Crie layouts complexos com CSS Grid',
        'difficulty' => 'advanced',
        'category_name' => 'CSS',
        'reading_time' => 25
    ],
    [
        'id' => 6,
        'title' => 'JavaScript ES6+',
        'description' => 'Recursos modernos do JavaScript: arrow functions, destructuring e mais',
        'difficulty' => 'advanced',
        'category_name' => 'JavaScript',
        'reading_time' => 30
    ]
];

// Filtros
$category = sanitize($_GET['category'] ?? '');
$difficulty = sanitize($_GET['difficulty'] ?? '');
$search = sanitize($_GET['search'] ?? '');

// Aplicar filtros
if ($category) {
    $tutorials = array_filter($tutorials, function($tut) use ($category) {
        return strtolower($tut['category_name']) === strtolower($category);
    });
}

if ($difficulty) {
    $tutorials = array_filter($tutorials, function($tut) use ($difficulty) {
        return $tut['difficulty'] === strtolower($difficulty);
    });
}

if ($search) {
    $tutorials = array_filter($tutorials, function($tut) use ($search) {
        return stripos($tut['title'], $search) !== false || 
               stripos($tut['description'], $search) !== false;
    });
}

include 'header.php';
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="fas fa-book me-2"></i>Tutoriais</h1>
            <p class="lead">Aprenda com nossos tutoriais detalhados</p>
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

    <!-- Lista de tutoriais -->
    <div class="row">
        <?php if (empty($tutorials)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h4>Nenhum tutorial encontrado!</h4>
                    <p>Tente ajustar os filtros ou <a href="simple_tutorials.php" class="alert-link">limpar a busca</a>.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($tutorials as $tutorial): 
                $difficulty_map = [
                    'beginner' => 'Iniciante', 
                    'intermediate' => 'Intermediário', 
                    'advanced' => 'Avançado'
                ];
                $display_difficulty = $difficulty_map[$tutorial['difficulty']] ?? 'Iniciante';
            ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="badge bg-<?php 
                                echo ($tutorial['category_name'] === 'HTML') ? 'danger' : 
                                    (($tutorial['category_name'] === 'CSS') ? 'primary' : 'warning'); 
                            ?>">
                                <?php echo htmlspecialchars($tutorial['category_name']); ?>
                            </span>
                            <span class="badge bg-<?php 
                                echo ($display_difficulty === 'Iniciante') ? 'success' : 
                                    (($display_difficulty === 'Intermediário') ? 'warning' : 'danger'); 
                            ?>">
                                <?php echo htmlspecialchars($display_difficulty); ?>
                            </span>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title h5"><?php echo htmlspecialchars($tutorial['title']); ?></h3>
                            <p class="card-text flex-grow-1"><?php echo htmlspecialchars($tutorial['description']); ?></p>
                            
                            <div class="mt-auto">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo $tutorial['reading_time']; ?> min de leitura
                                </small>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <a href="tutorial_detail.php?id=<?php echo $tutorial['id']; ?>" 
                               class="btn btn-primary w-100">
                                <i class="fas fa-book-open me-1"></i> Ler Tutorial
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
                        <i class="fas fa-graduation-cap text-primary"></i> 
                        Como Usar os Tutoriais
                    </h2>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Leia com atenção e pratique os exemplos
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Faça anotações dos conceitos importantes
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Aplique o conhecimento nos exercícios
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h2 class="h5 card-title">
                        <i class="fas fa-chart-bar text-info"></i> 
                        Estatísticas
                    </h2>
                    <p class="mb-2">Total de tutoriais: <strong><?php echo count($tutorials); ?></strong></p>
                    <p class="mb-2">Tutoriais disponíveis: <strong>6</strong></p>
                    <p class="mb-0">Tempo total de leitura: <strong>112 min</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>