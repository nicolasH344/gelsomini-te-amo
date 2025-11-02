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

// Verificar se deve mostrar apenas publicados
$showAll = isset($_GET['show_all']) && $_GET['show_all'] === '1';
$filteredTutorials = $showAll ? $allTutorials : array_filter($allTutorials, fn($t) => $t['status'] === 'Publicado');

// Paginação
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 6; // 6 tutoriais por página sempre
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
            <h2 class="h5 card-title">Filtrar Tutoriais</h2>
            <form method="GET" action="tutorials_index.php" class="row g-3">
                <div class="col-md-3">
                    <label for="category" class="form-label">Categoria</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Todas as categorias</option>
                        <option value="HTML">HTML</option>
                        <option value="CSS">CSS</option>
                        <option value="JavaScript">JavaScript</option>
                        <option value="PHP">PHP</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="level" class="form-label">Nível</label>
                    <select class="form-select" id="level" name="level">
                        <option value="">Todos os níveis</option>
                        <option value="Iniciante">Iniciante</option>
                        <option value="Intermediário">Intermediário</option>
                        <option value="Avançado">Avançado</option>
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
                        <h3 class="card-title h5"><?php echo htmlspecialchars($tutorial['title']); ?></h3>
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
                    <a class="page-link" href="?show_all=<?php echo $showAll ? '1' : '0'; ?>&page=<?php echo $page - 1; ?>" aria-label="Página anterior">
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
                        <a class="page-link" href="?show_all=<?php echo $showAll ? '1' : '0'; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                </li>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?show_all=<?php echo $showAll ? '1' : '0'; ?>&page=<?php echo $page + 1; ?>" aria-label="Próxima página">
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
                    <p class="mb-0">2 tutoriais</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fab fa-css3-alt" style="font-size: 2rem;" aria-hidden="true"></i>
                    <h3 class="h5 mt-2">CSS3</h3>
                    <p class="mb-0">2 tutoriais</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <i class="fab fa-js-square" style="font-size: 2rem;" aria-hidden="true"></i>
                    <h3 class="h5 mt-2">JavaScript</h3>
                    <p class="mb-0">1 tutorial</p>
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
</div>

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
                    <p class="mb-3">Tem dúvidas sobre algum tutorial? Nossa comunidade está aqui para ajudar!</p>
                    <a href="forum_index.php" class="btn btn-info btn-sm">
                        <i class="fas fa-comments" aria-hidden="true"></i> Ir para o Fórum
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php';
