<?php
/**
 * ARQUIVO: exercises_by_category.php
 * DESCRIÇÃO: Exibe exercícios filtrados por categoria/linguagem
 * AUTOR: Sistema WebLearn
 * DATA: 2024
 */

// Inclui configuração e funções
require_once 'config.php';
require_once 'exercise_functions.php';

// Captura a categoria da URL
$category = sanitize($_GET['category'] ?? '');

// Se não tiver categoria, redireciona para página de exercícios geral
if (!$category) {
    redirect('exercises_index.php');
}

// Configuração de categorias disponíveis
$categories_config = [
    'HTML' => [
        'name' => 'HTML',
        'icon' => 'fab fa-html5',
        'color' => '#e34c26',
        'bg_class' => 'danger',
        'description' => 'HyperText Markup Language - Linguagem de marcação para estruturar páginas web'
    ],
    'CSS' => [
        'name' => 'CSS',
        'icon' => 'fab fa-css3-alt',
        'color' => '#264de4',
        'bg_class' => 'primary',
        'description' => 'Cascading Style Sheets - Linguagem para estilizar páginas web'
    ],
    'JavaScript' => [
        'name' => 'JavaScript',
        'icon' => 'fab fa-js-square',
        'color' => '#f0db4f',
        'bg_class' => 'warning',
        'description' => 'Linguagem de programação para interatividade web'
    ],
    'PHP' => [
        'name' => 'PHP',
        'icon' => 'fab fa-php',
        'color' => '#777bb3',
        'bg_class' => 'secondary',
        'description' => 'PHP: Hypertext Preprocessor - Linguagem para desenvolvimento server-side'
    ],
    'Python' => [
        'name' => 'Python',
        'icon' => 'fab fa-python',
        'color' => '#3776ab',
        'bg_class' => 'info',
        'description' => 'Linguagem de programação versátil e poderosa'
    ],
    'React' => [
        'name' => 'React',
        'icon' => 'fab fa-react',
        'color' => '#61dafb',
        'bg_class' => 'info',
        'description' => 'Biblioteca JavaScript para construção de interfaces'
    ]
];

// Verifica se a categoria existe
if (!isset($categories_config[$category])) {
    redirect('exercises_index.php');
}

$category_info = $categories_config[$category];

// Captura parâmetros de filtro
$difficulty = sanitize($_GET['difficulty'] ?? '');
$search = sanitize($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;

// Busca exercícios da categoria
$exercises = getExercises($category, $difficulty, $search, $page, $perPage);
$totalExercises = countExercises($category, $difficulty, $search);
$totalPages = ceil($totalExercises / $perPage);

// Estatísticas da categoria
$allCategoryExercises = getExercises($category, '', '', 1, 1000);
$beginnerCount = count(array_filter($allCategoryExercises, fn($e) => $e['difficulty'] === 'Iniciante'));
$intermediateCount = count(array_filter($allCategoryExercises, fn($e) => $e['difficulty'] === 'Intermediário'));
$advancedCount = count(array_filter($allCategoryExercises, fn($e) => $e['difficulty'] === 'Avançado'));

// Define título da página
$title = "Exercícios de {$category_info['name']}";

include 'header.php';
?>

<div class="container mt-4">
    <!-- Header da Categoria -->
    <div class="category-header-card mb-4">
        <div class="category-header-background" style="background: linear-gradient(135deg, <?php echo $category_info['color']; ?> 0%, <?php echo adjustBrightness($category_info['color'], -30); ?> 100%);"></div>
        
        <div class="category-header-content">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-3">
                        <div class="category-icon-large" style="background: rgba(255,255,255,0.2);">
                            <i class="<?php echo $category_info['icon']; ?>"></i>
                        </div>
                        <div class="ms-3">
                            <h1 class="category-title mb-2">Exercícios de <?php echo $category_info['name']; ?></h1>
                            <p class="category-description mb-0"><?php echo $category_info['description']; ?></p>
                        </div>
                    </div>
                    
                    <!-- Estatísticas -->
                    <div class="category-stats-row">
                        <div class="category-stat-item">
                            <div class="stat-number"><?php echo count($allCategoryExercises); ?></div>
                            <div class="stat-text">Total de Exercícios</div>
                        </div>
                        <div class="category-stat-item">
                            <div class="stat-number"><?php echo $beginnerCount; ?></div>
                            <div class="stat-text">Iniciante</div>
                        </div>
                        <div class="category-stat-item">
                            <div class="stat-number"><?php echo $intermediateCount; ?></div>
                            <div class="stat-text">Intermediário</div>
                        </div>
                        <div class="category-stat-item">
                            <div class="stat-number"><?php echo $advancedCount; ?></div>
                            <div class="stat-text">Avançado</div>
                        </div>
                    </div>
                </div>
                
                <div class="category-actions">
                    <a href="exercises_index.php" class="btn-category-action" title="Voltar para todos os exercícios">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                
                <div class="col-md-4">
                    <label for="difficulty" class="form-label">
                        <i class="fas fa-signal"></i> Dificuldade
                    </label>
                    <select class="form-select" id="difficulty" name="difficulty">
                        <option value="">Todas as dificuldades</option>
                        <option value="Iniciante" <?php echo ($difficulty === 'Iniciante') ? 'selected' : ''; ?>>Iniciante</option>
                        <option value="Intermediário" <?php echo ($difficulty === 'Intermediário') ? 'selected' : ''; ?>>Intermediário</option>
                        <option value="Avançado" <?php echo ($difficulty === 'Avançado') ? 'selected' : ''; ?>>Avançado</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="search" class="form-label">
                        <i class="fas fa-search"></i> Buscar
                    </label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Digite palavras-chave...">
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Exercícios -->
    <?php if (empty($exercises)): ?>
        <div class="alert alert-info text-center py-5">
            <i class="fas fa-info-circle fa-3x mb-3 text-primary"></i>
            <h4 class="alert-heading">Nenhum exercício encontrado!</h4>
            <p class="mb-3">Não encontramos exercícios de <?php echo $category_info['name']; ?> com os filtros selecionados.</p>
            <a href="exercises_by_category.php?category=<?php echo urlencode($category); ?>" class="btn btn-primary">
                <i class="fas fa-redo"></i> Limpar Filtros
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($exercises as $exercise): 
                // Mapeia dificuldade para exibição
                $difficulty_display = $exercise['difficulty'];
                $difficulty_class = '';
                
                switch($exercise['difficulty']) {
                    case 'Iniciante':
                        $difficulty_class = 'success';
                        break;
                    case 'Intermediário':
                        $difficulty_class = 'warning';
                        break;
                    case 'Avançado':
                        $difficulty_class = 'danger';
                        break;
                }
            ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card exercise-card h-100 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center" 
                             style="background: linear-gradient(135deg, <?php echo $category_info['color']; ?>15 0%, <?php echo $category_info['color']; ?>05 100%); border-bottom: 2px solid <?php echo $category_info['color']; ?>;">
                            <div class="d-flex align-items-center">
                                <i class="<?php echo $category_info['icon']; ?> me-2" style="color: <?php echo $category_info['color']; ?>; font-size: 1.2rem;"></i>
                                <span class="fw-bold" style="color: <?php echo $category_info['color']; ?>;">
                                    <?php echo htmlspecialchars($category_info['name']); ?>
                                </span>
                            </div>
                            <span class="badge bg-<?php echo $difficulty_class; ?>">
                                <?php echo htmlspecialchars($difficulty_display); ?>
                            </span>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title h5 mb-3">
                                <i class="fas fa-code text-muted me-2"></i>
                                <?php echo htmlspecialchars($exercise['title']); ?>
                            </h3>
                            <p class="card-text flex-grow-1 text-muted">
                                <?php echo htmlspecialchars($exercise['description']); ?>
                            </p>
                            
                            <!-- Metadados do exercício -->
                            <div class="exercise-meta mt-3 pt-3 border-top">
                                <div class="d-flex justify-content-between text-muted small">
                                    <span>
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo htmlspecialchars($exercise['estimated_time'] ?? '15 min'); ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-star me-1 text-warning"></i>
                                        <?php echo htmlspecialchars($exercise['points'] ?? 10); ?> pts
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <div class="d-grid gap-2">
                                <a href="exercise_detail.php?id=<?php echo $exercise['id']; ?>" 
                                   class="btn btn-primary">
                                    <i class="fas fa-play me-2"></i>Começar Exercício
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Paginação -->
        <?php if ($totalPages > 1): ?>
        <nav aria-label="Navegação de páginas" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?php 
                            $query = $_GET;
                            $query['page'] = $page - 1;
                            echo http_build_query($query);
                        ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo ($i === $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?<?php 
                            $query = $_GET;
                            $query['page'] = $i;
                            echo http_build_query($query);
                        ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?php 
                            $query = $_GET;
                            $query['page'] = $page + 1;
                            echo http_build_query($query);
                        ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Dicas de Aprendizado -->
    <div class="card mt-5 mb-4 border-0 shadow-sm">
        <div class="card-body p-4">
            <h2 class="h4 mb-4">
                <i class="fas fa-lightbulb text-warning me-2"></i>
                Dicas para Aprender <?php echo $category_info['name']; ?>
            </h2>
            
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="learning-tip">
                        <div class="tip-icon-wrapper">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3 class="h6 fw-bold mt-3">Pratique Regularmente</h3>
                        <p class="text-muted small mb-0">Dedique pelo menos 30 minutos por dia para praticar exercícios.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="learning-tip">
                        <div class="tip-icon-wrapper">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <h3 class="h6 fw-bold mt-3">Construa Projetos</h3>
                        <p class="text-muted small mb-0">Aplique o conhecimento em projetos reais para consolidar o aprendizado.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="learning-tip">
                        <div class="tip-icon-wrapper">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="h6 fw-bold mt-3">Participe da Comunidade</h3>
                        <p class="text-muted small mb-0">Compartilhe dúvidas e soluções no fórum com outros estudantes.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ========================================
   HEADER DA CATEGORIA
   ======================================== */
.category-header-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    position: relative;
    overflow: hidden;
    margin-bottom: 2rem;
}

.category-header-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 100%;
    z-index: 0;
}

.category-header-content {
    position: relative;
    z-index: 1;
    padding: 2rem;
}

.category-icon-large {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255,255,255,0.3);
}

.category-title {
    font-size: 2rem;
    font-weight: 800;
    color: white;
    margin: 0;
    text-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.category-description {
    font-size: 1rem;
    color: rgba(255,255,255,0.95);
    text-shadow: 0 1px 5px rgba(0,0,0,0.1);
}

.category-stats-row {
    display: flex;
    gap: 2rem;
    margin-top: 1.5rem;
    flex-wrap: wrap;
}

.category-stat-item {
    text-align: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: 800;
    color: white;
    line-height: 1;
    text-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.stat-text {
    font-size: 0.85rem;
    color: rgba(255,255,255,0.9);
    margin-top: 0.5rem;
    text-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.category-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-category-action {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255,255,255,0.3);
    color: white;
    font-size: 1.1rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-category-action:hover {
    background: rgba(255,255,255,0.35);
    transform: scale(1.1);
    color: white;
}

/* ========================================
   CARDS DE EXERCÍCIO
   ======================================== */
.exercise-card {
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
}

.exercise-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.12) !important;
}

.exercise-meta {
    background: #f9fafb;
    border-radius: 8px;
    padding: 0.75rem;
}

/* ========================================
   DICAS DE APRENDIZADO
   ======================================== */
.learning-tip {
    text-align: center;
    padding: 1.5rem;
    background: #f9fafb;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.learning-tip:hover {
    background: #f3f4f6;
    transform: translateY(-3px);
}

.tip-icon-wrapper {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
</style>

<?php
// Função auxiliar para ajustar brilho de cor
function adjustBrightness($hex, $steps) {
    // Remove o # se presente
    $hex = str_replace('#', '', $hex);
    
    // Converte para RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Ajusta o brilho
    $r = max(0, min(255, $r + $steps));
    $g = max(0, min(255, $g + $steps));
    $b = max(0, min(255, $b + $steps));
    
    // Converte de volta para hex
    return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT)
               . str_pad(dechex($g), 2, '0', STR_PAD_LEFT)
               . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}

include 'footer.php';
?>
