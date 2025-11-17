<?php
// Iniciar sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir configurações
require_once 'config.php';

// Definir título da página
$title = 'Tutoriais';

// Incluir dados compartilhados de tutoriais
require_once 'data/tutorials.php';
$allTutorials = getTutorials();

// Parâmetros de filtro
$category = sanitize($_GET['category'] ?? '');
$level = sanitize($_GET['level'] ?? '');
$search = sanitize($_GET['search'] ?? '');
$showAll = isset($_GET['show_all']) && $_GET['show_all'] === '1';

// Aplicar filtros
$filteredTutorials = $showAll ? $allTutorials : array_filter($allTutorials, fn($t) => $t['status'] === 'Publicado');

// Filtrar por categoria
if ($category) {
    $filteredTutorials = array_filter($filteredTutorials, fn($t) => $t['category'] === $category);
}

// Filtrar por nível
if ($level) {
    $filteredTutorials = array_filter($filteredTutorials, fn($t) => $t['level'] === $level);
}

// Filtrar por busca
if ($search) {
    $filteredTutorials = array_filter($filteredTutorials, function($t) use ($search) {
        return stripos($t['title'], $search) !== false || 
               stripos($t['description'], $search) !== false ||
               (isset($t['topics']) && array_filter($t['topics'], fn($topic) => stripos($topic, $search) !== false));
    });
}

// Paginação
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 6;
$totalTutorials = count($filteredTutorials);
$totalPages = ceil($totalTutorials / $perPage);
$offset = ($page - 1) * $perPage;

$tutorials = array_slice($filteredTutorials, $offset, $perPage);

include 'header.php';
?>

<div class="container mt-4">
    <!-- Header da página -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-book-open" aria-hidden="true"></i> Tutoriais</h1>
            <p class="lead">Aprenda desenvolvimento web com nossos tutoriais detalhados</p>
        </div>
        <div class="col-md-4 text-end">
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <div class="btn-group me-2" role="group">
                    <a href="?show_all=<?php echo $showAll ? '0' : '1'; ?>" 
                       class="btn btn-<?php echo $showAll ? 'warning' : 'outline-warning'; ?>">
                        <i class="fas fa-<?php echo $showAll ? 'eye-slash' : 'eye'; ?>"></i>
                        <?php echo $showAll ? 'Ocultar Rascunhos' : 'Mostrar Todos'; ?>
                    </a>
                </div>
                <a href="admin.php" class="btn btn-success" role="button">
                    <i class="fas fa-cogs" aria-hidden="true"></i> Gerenciar Tutoriais
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 card-title2">Filtrar Tutoriais</h2>
            <form method="GET" action="tutorials_index.php" class="row g-3">
                <?php if ($showAll): ?>
                    <input type="hidden" name="show_all" value="1">
                <?php endif; ?>
                
                <div class="col-md-3">
                    <label for="category" class="form-label">Categoria</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Todas as categorias</option>
                        <option value="HTML" <?php echo $category === 'HTML' ? 'selected' : ''; ?>>HTML</option>
                        <option value="CSS" <?php echo $category === 'CSS' ? 'selected' : ''; ?>>CSS</option>
                        <option value="JavaScript" <?php echo $category === 'JavaScript' ? 'selected' : ''; ?>>JavaScript</option>
                        <option value="PHP" <?php echo $category === 'PHP' ? 'selected' : ''; ?>>PHP</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="level" class="form-label">Nível</label>
                    <select class="form-select" id="level" name="level">
                        <option value="">Todos os níveis</option>
                        <option value="Iniciante" <?php echo $level === 'Iniciante' ? 'selected' : ''; ?>>Iniciante</option>
                        <option value="Intermediário" <?php echo $level === 'Intermediário' ? 'selected' : ''; ?>>Intermediário</option>
                        <option value="Avançado" <?php echo $level === 'Avançado' ? 'selected' : ''; ?>>Avançado</option>
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
                        <div>
                            <span class="badge bg-<?php 
                                echo $tutorial['category'] === 'HTML' ? 'danger' : 
                                    ($tutorial['category'] === 'CSS' ? 'primary' : 
                                    ($tutorial['category'] === 'JavaScript' ? 'warning' : 'info')); 
                            ?>">
                                <?php echo htmlspecialchars($tutorial['category']); ?>
                            </span>
                            <?php if ($showAll && $tutorial['status'] === 'Rascunho'): ?>
                                <span class="badge bg-secondary ms-1">Rascunho</span>
                            <?php endif; ?>
                        </div>
                        <span class="badge bg-<?php 
                            echo $tutorial['level'] === 'Iniciante' ? 'success' : 
                                ($tutorial['level'] === 'Intermediário' ? 'warning' : 'danger'); 
                        ?>">
                            <?php echo htmlspecialchars($tutorial['level']); ?>
                        </span>
                    </div>
                    
                    <div class="card-body">
                        <h3 class="card-title h5 mb-4"><?php echo htmlspecialchars($tutorial['title']); ?></h3>
                        <p class="card-text"><?php echo htmlspecialchars($tutorial['description']); ?></p>
                        
                        <div class="d-flex justify-content-between text-muted small">
                            <span>
                                <i class="fas fa-clock me-1" aria-hidden="true"></i>
                                <?php echo htmlspecialchars($tutorial['duration']); ?>
                            </span>
                            <span>
                                <i class="fas fa-eye me-1" aria-hidden="true"></i>
                                <?php echo number_format($tutorial['views']); ?> visualizações
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="d-flex gap-2">
                            <a href="show.php?type=tutorial&id=<?php echo $tutorial['id']; ?>" 
                               class="btn btn-primary btn-sm flex-fill">
                                <i class="fas fa-play" aria-hidden="true"></i> Ler Tutorial
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

    <!-- Paginação -->
    <?php if ($totalPages > 1): ?>
    <nav aria-label="Navegação de páginas dos tutoriais" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?show_all=<?php echo $showAll ? '1' : '0'; ?>&page=<?php echo $page - 1; ?>&category=<?php echo urlencode($category); ?>&level=<?php echo urlencode($level); ?>&search=<?php echo urlencode($search); ?>" aria-label="Página anterior">
                        <i class="fas fa-chevron-left" aria-hidden="true"></i>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link" aria-label="Página anterior">
                        <i class="fas fa-chevron-left" aria-hidden="true"></i>
                    </span>
                </li>
            <?php endif; ?>
            
            <?php 
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $page + 2);
            
            for ($i = $startPage; $i <= $endPage; $i++): 
            ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <?php if ($i === $page): ?>
                        <span class="page-link" aria-current="page"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a class="page-link" href="?show_all=<?php echo $showAll ? '1' : '0'; ?>&page=<?php echo $i; ?>&category=<?php echo urlencode($category); ?>&level=<?php echo urlencode($level); ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                </li>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?show_all=<?php echo $showAll ? '1' : '0'; ?>&page=<?php echo $page + 1; ?>&category=<?php echo urlencode($category); ?>&level=<?php echo urlencode($level); ?>&search=<?php echo urlencode($search); ?>" aria-label="Próxima página">
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link" aria-label="Próxima página">
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                    </span>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>

    <!-- Categorias populares -->
    <div class="row mt-5">
        <div class="col-12">
            <h2 class="h4 mb-4">Categorias Populares</h2>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="fab fa-html5" style="font-size: 2rem;" aria-hidden="true"></i>
                    <h3 class="h5 mt-2">HTML5</h3>
                    <p class="mb-0"><?php echo count(array_filter($allTutorials, fn($t) => $t['category'] === 'HTML')); ?> tutoriais</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fab fa-css3-alt" style="font-size: 2rem;" aria-hidden="true"></i>
                    <h3 class="h5 mt-2">CSS3</h3>
                    <p class="mb-0"><?php echo count(array_filter($allTutorials, fn($t) => $t['category'] === 'CSS')); ?> tutoriais</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <i class="fab fa-js-square" style="font-size: 2rem; color: #fff;" aria-hidden="true"></i>
                    <h3 class="h5 mt-2" style="color: #fff;">JavaScript</h3>
                    <p class="mb-0"><?php echo count(array_filter($allTutorials, fn($t) => $t['category'] === 'JavaScript')); ?> tutorial</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fab fa-php" style="font-size: 2rem;" aria-hidden="true"></i>
                    <h3 class="h5 mt-2">PHP</h3>
                    <p class="mb-0"><?php echo count(array_filter($allTutorials, fn($t) => $t['category'] === 'PHP')); ?> tutorial</p>
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
                        Como Aproveitar os Tutoriais
                    </h2>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Leia com atenção e pratique os exemplos
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Faça anotações dos pontos importantes
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
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
                        <i class="fas fa-question-circle text-info" aria-hidden="true"></i> 
                        Precisa de Ajuda?
                    </h2>
                    <li class="mb-0"    >
                    Tem dúvidas sobre algum tutorial? Nossa comunidade está aqui para ajudar!
                    </li>
                    <a href="forum_index.php" class="btn btn-info btn-sm">
                        <i class="fas fa-comments" aria-hidden="true"></i> Ir para o Fórum
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos customizados para tutoriais usando variáveis CSS do tema */

.tech-gradient {
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.typing-effect {
    border-right: 2px solid var(--primary-color);
    animation: typing 3s steps(40) infinite;
}

.cursor-blink {
    animation: blink 1s infinite;
    color: var(--primary-color);
}

@keyframes typing {
    0%, 50% { border-color: var(--primary-color); }
    51%, 100% { border-color: transparent; }
}

@keyframes blink {
    0%, 50% { opacity: 1; }
    51%, 100% { opacity: 0; }
}

.terminal-card {
    background: var(--bg-light);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow);
}

.terminal-header {
    background: var(--gradient-primary);
    padding: 12px 16px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 12px;
    color: var(--text-light);
}

.terminal-buttons {
    display: flex;
    gap: 6px;
}

.terminal-btn {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.terminal-btn.red { background: #ff5f56; }
.terminal-btn.yellow { background: #ffbd2e; }
.terminal-btn.green { background: #27ca3f; }

.terminal-title {
    color: var(--text-light);
    font-size: 0.9rem;
}

.terminal-body {
    padding: 20px;
}

.terminal-label {
    color: var(--primary-color);
    font-size: 0.9rem;
    font-weight: 600;
    display: block;
    margin-bottom: 8px;
}

.terminal-input {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    color: var(--text-primary);
}

.terminal-input:focus {
    background: var(--bg-primary);
    border-color: var(--primary-color);
    color: var(--text-primary);
    box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
}

.btn-terminal {
    background: var(--primary-color);
    border: 1px solid var(--primary-color);
    color: var(--text-light);
    font-weight: 600;
}

.btn-terminal:hover {
    background: var(--primary-dark);
    border-color: var(--primary-dark);
    color: var(--text-light);
}

.status-bar {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 12px;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.status-label {
    color: var(--text-muted);
    font-size: 0.8rem;
    font-weight: 600;
}

.status-value {
    color: var(--primary-color);
    font-weight: bold;
}

.code-card {
    background: var(--bg-light);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius-lg);
    transition: var(--transition);
    height: 100%;
    box-shadow: var(--shadow-sm);
}

.code-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-color);
}

.code-header {
    background: var(--gradient-primary);
    padding: 12px 16px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 12px;
    color: var(--text-light);
}

.code-buttons {
    display: flex;
    gap: 6px;
}

.code-btn {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.code-btn.red { background: #ff5f56; }
.code-btn.yellow { background: #ffbd2e; }
.code-btn.green { background: #27ca3f; }

.code-title {
    color: var(--text-light);
    font-size: 0.9rem;
}

.code-body {
    padding: 16px;
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
}

.code-line {
    display: flex;
    margin-bottom: 4px;
    line-height: 1.5;
}

.line-number {
    color: var(--text-muted);
    width: 30px;
    text-align: right;
    margin-right: 16px;
    user-select: none;
}

.keyword { color: var(--danger-color); }
.class-name { color: var(--accent-color); }
.property { color: var(--primary-color); }
.string { color: var(--info-color); }
.number { color: var(--primary-color); }
.comment { color: var(--text-muted); font-style: italic; }

.code-footer {
    padding: 12px 16px;
    border-top: 1px solid var(--border-color);
    background: var(--bg-primary);
}

.btn-outline-terminal {
    background: transparent;
    border: 1px solid var(--border-color);
    color: var(--text-primary);
}

.btn-outline-terminal:hover {
    background: var(--bg-secondary);
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.tech-stack-card {
    background: var(--bg-light);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius-lg);
    padding: 20px;
    text-align: center;
    transition: var(--transition);
    cursor: pointer;
    box-shadow: var(--shadow-sm);
}

.tech-stack-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.html-card:hover { border-color: #e34c26; }
.css-card:hover { border-color: #1572b6; }
.js-card:hover { border-color: #f7df1e; }
.php-card:hover { border-color: #777bb4; }

.tech-icon {
    font-size: 3rem;
    margin-bottom: 16px;
}

.html-card .tech-icon { color: #e34c26; }
.css-card .tech-icon { color: #1572b6; }
.js-card .tech-icon { color: #f7df1e; }
.php-card .tech-icon { color: #777bb4; }

.tech-progress {
    background: var(--bg-secondary);
    height: 4px;
    border-radius: 2px;
    margin: 12px 0;
    overflow: hidden;
}

.tech-progress .progress-bar {
    height: 100%;
    background: var(--gradient-primary);
    border-radius: 2px;
    transition: width 2s ease;
}

.error-card {
    background: var(--bg-light);
    border: 1px solid var(--danger-color);
    border-radius: var(--border-radius-lg);
    padding: 40px;
    text-align: center;
}

.error-header {
    color: var(--danger-color);
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 16px;
}

.error-body h3 {
    color: var(--text-heading);
    margin-bottom: 12px;
}

.error-body p {
    color: var(--text-muted);
    margin-bottom: 20px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtros em tempo real
    const categorySelect = document.getElementById('category');
    const levelSelect = document.getElementById('level');
    
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            this.closest('form').submit();
        });
    }

    if (levelSelect) {
        levelSelect.addEventListener('change', function() {
            this.closest('form').submit();
        });
    }

    // Busca com debounce
    const searchInput = document.getElementById('search');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 3 || this.value.length === 0) {
                    this.closest('form').submit();
                }
            }, 500);
        });
    }
});
</script>

<?php include 'footer.php'; ?>