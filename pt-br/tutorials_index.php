<?php
require_once 'config.php';
require_once 'progress_tracker.php';
require_once 'data/tutorials.php';

$title = 'Tutoriais';

// Parâmetros de filtro
$category = sanitize($_GET['category'] ?? '');
$level = sanitize($_GET['level'] ?? '');
$search = sanitize($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;

// Buscar todos os tutoriais
$allTutorials = getTutorials();

// Aplicar filtros
$tutorials = $allTutorials;

if ($category) {
    $tutorials = array_filter($tutorials, fn($t) => strtolower($t['category']) === strtolower($category));
}

if ($level) {
    // Mapear níveis em inglês para português
    $levelMap = [
        'beginner' => 'Iniciante',
        'intermediate' => 'Intermediário',
        'advanced' => 'Avançado'
    ];
    $difficultyToMatch = $levelMap[$level] ?? $level;
    
    $tutorials = array_filter($tutorials, function($t) use ($difficultyToMatch) {
        return strcasecmp($t['difficulty'], $difficultyToMatch) === 0 || 
               strcasecmp($t['level'] ?? '', $difficultyToMatch) === 0;
    });
}

if ($search) {
    $tutorials = array_filter($tutorials, function($t) use ($search) {
        return stripos($t['title'], $search) !== false || 
               stripos($t['description'], $search) !== false ||
               stripos($t['category'], $search) !== false;
    });
}

// Paginação
$totalTutorials = count($tutorials);
$totalPages = ceil($totalTutorials / $perPage);
$offset = ($page - 1) * $perPage;
$tutorials = array_slice($tutorials, $offset, $perPage);

// Definir categorias disponíveis
$categories = [
    ['name' => 'HTML', 'slug' => 'html', 'color' => 'danger', 'icon' => 'fab fa-html5'],
    ['name' => 'CSS', 'slug' => 'css', 'color' => 'primary', 'icon' => 'fab fa-css3-alt'],
    ['name' => 'JavaScript', 'slug' => 'javascript', 'color' => 'warning', 'icon' => 'fab fa-js-square'],
    ['name' => 'PHP', 'slug' => 'php', 'color' => 'info', 'icon' => 'fab fa-php']
];

include 'header.php';
?>

<div class="container mt-4">
    <!-- Header da página -->
    <div class="tutorials-header text-center mb-5">
        <h1 class="display-4 fw-bold mb-3">
            <i class="fas fa-book-open"></i>
            Tutoriais
        </h1>
        <p class="lead" style="color: #1a1a1a;">
            Aprenda desenvolvimento web com nossos tutoriais detalhados
        </p>
        <?php if (isAdmin()): ?>
            <a href="admin.php" class="btn btn-success btn-lg rounded-pill mt-3" role="button">
                <i class="fas fa-cogs me-2"></i> Gerenciar Tutoriais
            </a>
        <?php endif; ?>
    </div>

    <!-- Filtros -->
    <div class="filters-card card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="tutorials_index.php" id="filterForm" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="category" class="form-label fw-semibold">
                        <i class="fas fa-tag text-primary me-1"></i>
                        Categoria
                    </label>
                    <select class="form-select rounded-pill" id="category" name="category" onchange="this.form.submit()">
                        <option value="">Todas as categorias</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo sanitize($cat['slug']); ?>" <?php echo $category === $cat['slug'] ? 'selected' : ''; ?>>
                                <?php echo sanitize($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="level" class="form-label fw-semibold">
                        <i class="fas fa-signal text-warning me-1"></i>
                        Nível
                    </label>
                    <select class="form-select rounded-pill" id="level" name="level" onchange="this.form.submit()">
                        <option value="">Todos os níveis</option>
                        <option value="beginner" <?php echo $level === 'beginner' ? 'selected' : ''; ?>>🌱 Iniciante</option>
                        <option value="intermediate" <?php echo $level === 'intermediate' ? 'selected' : ''; ?>>🌿 Intermediário</option>
                        <option value="advanced" <?php echo $level === 'advanced' ? 'selected' : ''; ?>>🌳 Avançado</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="search" class="form-label fw-semibold">
                        <i class="fas fa-search text-info me-1"></i>
                        Buscar
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control rounded-pill rounded-end" id="search" name="search" 
                               value="<?php echo htmlspecialchars($search); ?>"
                               placeholder="Digite palavras-chave..."
                               onkeypress="if(event.key === 'Enter') this.form.submit()">
                        <?php if ($search): ?>
                            <button type="button" class="btn btn-outline-secondary rounded-pill rounded-start" 
                                    onclick="document.getElementById('search').value=''; document.getElementById('filterForm').submit();"
                                    title="Limpar busca">
                                <i class="fas fa-times"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill mb-2">
                        <i class="fas fa-filter"></i> Aplicar
                    </button>
                    <?php if ($category || $level || $search): ?>
                        <a href="tutorials_index.php" class="btn btn-outline-secondary w-100 rounded-pill btn-sm">
                            <i class="fas fa-redo"></i> Limpar
                        </a>
                    <?php endif; ?>
                </div>
            </form>
            
            <!-- Filtros ativos -->
            <?php if ($category || $level || $search): ?>
                <div class="active-filters mt-3 pt-3 border-top">
                    <small class="text-muted fw-semibold d-block mb-2">
                        <i class="fas fa-filter me-1"></i>
                        Filtros ativos:
                    </small>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if ($category): ?>
                            <span class="badge bg-primary rounded-pill px-3 py-2">
                                Categoria: <?php echo ucfirst($category); ?>
                                <a href="?<?php 
                                    $query = $_GET;
                                    unset($query['category']);
                                    echo http_build_query($query);
                                ?>" class="text-white ms-2" title="Remover filtro">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($level): 
                            $levelNames = ['beginner' => 'Iniciante', 'intermediate' => 'Intermediário', 'advanced' => 'Avançado'];
                        ?>
                            <span class="badge bg-warning rounded-pill px-3 py-2">
                                Nível: <?php echo $levelNames[$level] ?? ucfirst($level); ?>
                                <a href="?<?php 
                                    $query = $_GET;
                                    unset($query['level']);
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
                            <?php echo $totalTutorials; ?> resultado<?php echo $totalTutorials != 1 ? 's' : ''; ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Lista de tutoriais -->
    <div class="row">
        <?php if (empty($tutorials)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h4>Nenhum tutorial encontrado</h4>
                    <p>Tente ajustar os filtros ou volte mais tarde para ver novos conteúdos.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($tutorials as $tutorial): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card tutorial-card h-100 shadow-sm border-0">
                        <div class="card-header bg-white border-0 pt-3 pb-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge rounded-pill bg-<?php 
                                    $catColors = ['HTML' => 'danger', 'CSS' => 'primary', 'JavaScript' => 'warning', 'PHP' => 'info'];
                                    echo $catColors[$tutorial['category']] ?? 'secondary';
                                ?> px-3 py-2">
                                    <i class="fas fa-code me-1"></i>
                                    <?php echo sanitize($tutorial['category']); ?>
                                </span>
                                <span class="badge rounded-pill bg-<?php 
                                    $levelMap = ['Iniciante' => 'success', 'Intermediário' => 'warning', 'Avançado' => 'danger'];
                                    echo $levelMap[$tutorial['difficulty']] ?? 'secondary'; 
                                ?> px-3 py-2">
                                    <i class="fas fa-signal me-1"></i>
                                    <?php echo sanitize($tutorial['difficulty']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column pt-2">
                            <h3 class="card-title h5 fw-bold mb-3">
                                <?php echo sanitize($tutorial['title']); ?>
                            </h3>
                            <p class="card-text flex-grow-1 mb-3" style="color: black;">
                                <?php echo sanitize($tutorial['description']); ?>
                            </p>
                            
                            <div class="tutorial-meta d-flex justify-content-between text-muted small border-top pt-3">
                                <span class="d-flex align-items-center">
                                    <i class="fas fa-clock me-2 text-primary"></i>
                                    <strong><?php echo sanitize($tutorial['duration'] ?? '30 min'); ?></strong>
                                </span>
                                <span class="d-flex align-items-center">
                                    <i class="fas fa-eye me-2 text-info"></i>
                                    <strong><?php echo number_format($tutorial['views'] ?? 0); ?></strong>
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-white border-0 pt-0 pb-3">
                            <div class="d-flex gap-2">
                                <a href="show.php?type=tutorial&id=<?php echo $tutorial['id']; ?>" 
                                   class="btn btn-primary flex-fill rounded-pill">
                                    <i class="fas fa-book-reader me-1"></i> Ler Tutorial
                                </a>
                                <button class="btn btn-outline-secondary rounded-pill px-3" 
                                        onclick="showTutorialPreview(<?php echo $tutorial['id']; ?>)"
                                        title="Pré-visualizar"
                                        aria-label="Visualizar tutorial">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Categorias populares -->
    <div class="categories-section mt-5">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="h3 fw-bold mb-2">
                    <i class="fas fa-folder-open text-primary me-2"></i>
                    Categorias Populares
                </h2>
                <p class="text-muted">Explore tutoriais por tecnologia</p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php foreach ($categories as $cat): ?>
                <div class="col-md-3 col-sm-6">
                    <a href="?category=<?php echo $cat['slug']; ?>" class="text-decoration-none">
                        <div class="category-card card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="category-icon mb-3">
                                    <i class="<?php echo $cat['icon']; ?> text-<?php echo $cat['color']; ?>"></i>
                                </div>
                                <h3 class="h5 fw-bold mb-2 text-dark"><?php echo sanitize($cat['name']); ?></h3>
                                <span class="btn btn-outline-<?php echo $cat['color']; ?> btn-sm rounded-pill">
                                    <i class="fas fa-arrow-right me-1" style="border: none"></i>
                                    Ver Tutoriais
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Informações adicionais -->
    <div class="row mt-5 g-3 g-md-4">
        <div class="col-12 col-lg-6">
            <div class="info-card card border-0 shadow-sm h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-start align-items-md-center mb-3 mb-md-4 flex-column flex-md-row">
                        <div class="icon-circle bg-success bg-opacity-10 text-success me-0 me-md-3 mb-2 mb-md-0 align-self-center">
                            <i class="fas fa-graduation-cap fa-lg fa-md-2x"></i>
                        </div>
                        <h2 class="h6 h5-md mb-0 fw-bold text-center text-md-start">Como Aproveitar os Tutoriais</h2>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-start mb-3 tip-item">
                            <div class="tip-icon me-2 me-md-3 mt-1">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <span class="tip-text">Leia com atenção e pratique os exemplos de código</span>
                        </li>
                        <li class="d-flex align-items-start mb-3 tip-item">
                            <div class="tip-icon me-2 me-md-3 mt-1">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <span class="tip-text">Faça anotações dos pontos importantes</span>
                        </li>
                        <li class="d-flex align-items-start tip-item">
                            <div class="tip-icon me-2 me-md-3 mt-1">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <span class="tip-text">Aplique o conhecimento nos exercícios práticos</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-6">
            <div class="help-card card border-0 shadow-sm h-100">
                <div class="card-body p-3 p-md-4 text-center text-md-start">
                    <div class="d-flex align-items-start align-items-md-center mb-3 mb-md-4 flex-column flex-md-row">
                        <div class="icon-circle bg-info bg-opacity-10 text-info me-0 me-md-3 mb-2 mb-md-0 align-self-center">
                            <i class="fas fa-question-circle fa-lg fa-md-2x"></i>
                        </div>
                        <h2 class="h6 h5-md mb-0 fw-bold">Precisa de Ajuda?</h2>
                    </div>
                    <p class="mb-3 mb-md-4 text-muted">
                        Tem dúvidas sobre algum tutorial? Nossa comunidade está aqui para ajudar!
                    </p>
                    <a href="forum_index.php" class="btn btn-info w-100 rounded-pill" onclick="testForumLink(event)">
                        <i class="fas fa-comments me-2"></i> Ir para o Fórum
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTutorialPreview(id) {
    // Implementar preview do tutorial
    if (id) {
        alert('Preview do tutorial ' + id);
        console.log('Tutorial preview solicitado:', id);
    } else {
        console.error('ID do tutorial não fornecido');
    }
}

function testForumLink(event) {
    // Teste do link do fórum
    console.log('Link do fórum clicado');
    
    // Verificar se a página existe
    fetch('forum_index.php', { method: 'HEAD' })
        .then(response => {
            if (!response.ok) {
                event.preventDefault();
                alert('Página do fórum ainda não está disponível.');
                console.warn('Fórum não encontrado:', response.status);
            }
        })
        .catch(error => {
            console.log('Redirecionando para o fórum...');
        });
}

// Teste de responsividade
function testResponsiveness() {
    const breakpoints = {
        mobile: 576,
        tablet: 768,
        desktop: 992
    };
    
    const width = window.innerWidth;
    let device = 'desktop';
    
    if (width < breakpoints.mobile) device = 'mobile';
    else if (width < breakpoints.tablet) device = 'tablet';
    
    console.log('Dispositivo detectado:', device, '- Largura:', width + 'px');
    return device;
}

document.addEventListener('DOMContentLoaded', function() {
    // Teste inicial
    console.log('Página de tutoriais carregada');
    testResponsiveness();
    
    // Filtros em tempo real
    const categorySelect = document.getElementById('category');
    const levelSelect = document.getElementById('level');
    
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            console.log('Filtro de categoria alterado:', this.value);
            this.closest('form').submit();
        });
    } else {
        console.warn('Seletor de categoria não encontrado');
    }

    if (levelSelect) {
        levelSelect.addEventListener('change', function() {
            console.log('Filtro de nível alterado:', this.value);
            this.closest('form').submit();
        });
    } else {
        console.warn('Seletor de nível não encontrado');
    }
    
    // Teste de hover nos cards
    const cards = document.querySelectorAll('.info-card, .help-card');
    cards.forEach((card, index) => {
        card.addEventListener('mouseenter', function() {
            console.log('Hover no card', index + 1);
        });
    });
    
    // Teste de redimensionamento
    window.addEventListener('resize', function() {
        clearTimeout(this.resizeTimeout);
        this.resizeTimeout = setTimeout(testResponsiveness, 250);
    });
});
</script>

<style>
/* Cabeçalho da página */
.tutorials-header {
    padding: 2rem 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    margin: -1.5rem -15px 2rem -15px;
    color: white;
    border-radius: 0 0 20px 20px;
}

.tutorials-header .display-4 {
    font-size: 2.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

.tutorials-header .lead {
    color: rgba(255,255,255,0.9);
    font-size: 1.1rem;
}

/* Card de filtros */
.filters-card {
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.filters-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.filters-card .form-label {
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

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

.input-group .btn-outline-secondary {
    border: 2px solid #e9ecef;
    border-left: none;
    margin-left: -1px;
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

/* Cards de tutoriais */
.tutorial-card {
    border-radius: 15px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    background: white;
}

.tutorial-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15) !important;
}

.tutorial-card .badge {
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.3px;
}

.tutorial-card .card-title {
    color: #2d3748;
    line-height: 1.4;
}

.tutorial-card .card-text {
    font-size: 0.9rem;
    line-height: 1.6;
    color: black !important; /* Garante que o texto fique preto */
}

.tutorial-card .btn {
    font-weight: 600;
    transition: all 0.3s ease;
}

.tutorial-card .btn:hover {
    transform: scale(1.05);
}

.tutorial-meta {
    margin-top: auto;
}

/* Cards de categorias */
.categories-section h2 {
    position: relative;
    display: inline-block;
}

.category-card {
    border-radius: 15px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.category-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.2) !important;
}

.category-icon {
    font-size: 3.5rem;
    transition: transform 0.3s ease;
}

.category-card:hover .category-icon {
    transform: scale(1.1);
}

.category-card:hover .btn {
    background-color: currentColor;
    color: white !important;
}

/* Cards de informação */
.info-card, .help-card {
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    min-height: 280px;
}

.info-card:hover, .help-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15) !important;
}

.icon-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.tip-item {
    padding: 0.5rem 0;
    transition: all 0.3s ease;
}

.tip-item:hover {
    transform: translateX(3px);
}

.tip-icon {
    flex-shrink: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.tip-text {
    line-height: 1.5;
    color: #4a5568;
    font-size: 0.9rem;
}

/* Animações */
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

.tutorial-card {
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

/* Delay de animação para cada card */
.tutorial-card:nth-child(1) { animation-delay: 0.1s; }
.tutorial-card:nth-child(2) { animation-delay: 0.2s; }
.tutorial-card:nth-child(3) { animation-delay: 0.3s; }
.tutorial-card:nth-child(4) { animation-delay: 0.4s; }
.tutorial-card:nth-child(5) { animation-delay: 0.5s; }
.tutorial-card:nth-child(6) { animation-delay: 0.6s; }

/* Responsividade */
@media (max-width: 768px) {
    .tutorials-header .display-4 {
        font-size: 2rem;
    }
    
    .filters-card .row > div {
        margin-bottom: 0.5rem;
    }
    
    .icon-circle {
        width: 50px;
        height: 50px;
    }
    
    .icon-circle i {
        font-size: 1.5rem !important;
    }
    
    .category-icon {
        font-size: 2.5rem;
    }
}

/* Efeito hover para cards de categoria - Cores individuais */
.category-card:hover .btn-outline-danger { /* HTML - Vermelho */
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: white !important;
}

.category-card:hover .btn-outline-primary { /* CSS - Azul */
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
    color: white !important;
}

.category-card:hover .btn-outline-warning { /* JavaScript - Amarelo/Laranja */
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
    color: black !important;
}

.category-card:hover .btn-outline-info { /* PHP - Azul Claro */
    background-color: #0dcaf0 !important;
    border-color: #0dcaf0 !important;
    color: white !important;
}
/* Efeito hover para cards de categoria - Apenas CSS fica roxo */
.category-card:hover .btn-outline-primary { /* CSS - Roxo */
    background-color: #6f42c1 !important;
    color: white !important;
}

/* Remove as bordas de todos os botões de categoria no hover */
.category-card:hover .btn {
    border: none !important;
}
</style>

<script src="tutorial_progress.js"></script>
<?php include 'footer.php'; ?>