<?php
// Incluir configurações
require_once 'config.php';

// Definir título da página
$title = 'Exercícios';

// --- Lógica de busca e filtragem ---
$conn = getDBConnection();

// Parâmetros de filtragem
$category = sanitize($_GET['category'] ?? '');
$difficulty = sanitize($_GET['difficulty'] ?? '');
$search = sanitize($_GET['search'] ?? '');

// Paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 9; // 9 exercícios por página
$offset = ($page - 1) * $perPage;

// Construção da query SQL
$sql = "SELECT e.*, ec.name as category_name 
        FROM exercises e 
        LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
        WHERE 1=1";
$params = [];

if ($category) {
    $sql .= " AND ec.name = ?";
    $params[] = $category;
}
if ($difficulty) {
    // Mapeamento para o enum do banco
    $difficulty_map = ['Iniciante' => 'beginner', 'Intermediário' => 'intermediate', 'Avançado' => 'advanced'];
    if (array_key_exists($difficulty, $difficulty_map)) {
        $sql .= " AND e.difficulty_level = ?";
        $params[] = $difficulty_map[$difficulty];
    }
}
if ($search) {
    $sql .= " AND (e.title LIKE ? OR e.description LIKE ?)";
    $searchTerm = "%{$search}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

// Contar total de resultados para paginação
$countSql = str_replace("SELECT e.*, ec.name as category_name", "SELECT COUNT(*)", $sql);
$stmtCount = $conn->prepare($countSql);
$stmtCount->execute($params);
$totalResults = $stmtCount->fetchColumn();
$totalPages = ceil($totalResults / $perPage);

// Adicionar ordenação e limites para a busca principal
$sql .= " ORDER BY e.created_at DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;

// Executar a query principal
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<div class="container mt-4">
    <!-- Header da página -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-tasks" aria-hidden="true"></i> Exercícios Práticos</h1>
            <p class="lead">Pratique suas habilidades com exercícios interativos</p>
        </div>
        <div class="col-md-4 text-end">
            <?php if (isAdmin()): ?>
                <a href="#" class="btn btn-success" role="button">
                    <i class="fas fa-plus" aria-hidden="true"></i> Gerenciar Exercícios
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 card-title">Filtrar Exercícios</h2>
            <form method="GET" action="exercises_index.php" class="row g-3">
                <div class="col-md-3">
                    <label for="category" class="form-label">Categoria</label>
                    <select class="form-select" id="category" name="category">
                        <option value="" <?php echo !$category ? 'selected' : ''; ?>>Todas as categorias</option>
                        <option value="HTML" <?php echo $category === 'HTML' ? 'selected' : ''; ?>>HTML</option>
                        <option value="CSS" <?php echo $category === 'CSS' ? 'selected' : ''; ?>>CSS</option>
                        <option value="JavaScript" <?php echo $category === 'JavaScript' ? 'selected' : ''; ?>>JavaScript</option>
                        <option value="PHP" <?php echo $category === 'PHP' ? 'selected' : ''; ?>>PHP</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="difficulty" class="form-label">Dificuldade</label>
                    <select class="form-select" id="difficulty" name="difficulty">
                        <option value="" <?php echo !$difficulty ? 'selected' : ''; ?>>Todas as dificuldades</option>
                        <option value="Iniciante" <?php echo $difficulty === 'Iniciante' ? 'selected' : ''; ?>>Iniciante</option>
                        <option value="Intermediário" <?php echo $difficulty === 'Intermediário' ? 'selected' : ''; ?>>Intermediário</option>
                        <option value="Avançado" <?php echo $difficulty === 'Avançado' ? 'selected' : ''; ?>>Avançado</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="search" name="search" value="<?php echo sanitize($search); ?>"
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

    <!-- Lista de exercícios -->
    <div class="row">
        <?php if (empty($exercises)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h4 class="alert-heading">Nenhum exercício encontrado!</h4>
                    <p>Tente ajustar os filtros ou <a href="exercises_index.php" class="alert-link">limpar a busca</a> para ver todos os exercícios disponíveis.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($exercises as $exercise): 
                $difficulty_map_display = ['beginner' => 'Iniciante', 'intermediate' => 'Intermediário', 'advanced' => 'Avançado'];
                $display_difficulty = $difficulty_map_display[$exercise['difficulty_level']] ?? 'N/A';
                $completed = false; // Lógica de progresso do usuário viria aqui
            ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="badge badge-category-<?php echo strtolower(sanitize($exercise['category_name'])); ?>">
                                <?php echo sanitize($exercise['category_name']); ?>
                            </span>
                            <span class="badge badge-difficulty-<?php echo str_replace(' ', '-', strtolower(sanitize($display_difficulty))); ?>">
                                <?php echo sanitize($display_difficulty); ?>
                            </span>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title h5"><?php echo sanitize($exercise['title']); ?></h3>
                            <p class="card-text flex-grow-1"><?php echo sanitize($exercise['description']); ?></p>
                            
                            <?php if ($completed): ?>
                                <div class="alert alert-success py-2 mt-auto" role="alert">
                                    <i class="fas fa-check-circle me-1" aria-hidden="true"></i>
                                    <small>Concluído</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <div class="d-flex gap-2">
                                <a href="show.php?type=exercise&id=<?php echo $exercise['id']; ?>" 
                                   class="btn btn-primary btn-sm flex-fill">
                                    <i class="fas fa-play" aria-hidden="true"></i> 
                                    <?php echo $completed ? 'Revisar' : 'Começar'; ?>
                                </a>
                                <a href="show.php?type=exercise&id=<?php echo $exercise['id']; ?>&preview=1" 
                                   class="btn btn-outline-secondary btn-sm"
                                   aria-label="Visualizar exercício <?php echo sanitize($exercise['title']); ?>"
                                   title="Visualizar exercício <?php echo sanitize($exercise['title']); ?>">
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Paginação -->
    <nav aria-label="Navegação de páginas dos exercícios" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&<?php echo http_build_query($_GET, '', '&'); ?>"><i class="fas fa-chevron-left"></i></a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo http_build_query($_GET, '', '&'); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&<?php echo http_build_query($_GET, '', '&'); ?>"><i class="fas fa-chevron-right"></i></a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Informações adicionais -->
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h2 class="h5 card-title">
                        <i class="fas fa-lightbulb text-warning" aria-hidden="true"></i> 
                        Dicas para Estudar
                    </h2>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Comece pelos exercícios de nível iniciante
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Pratique regularmente para fixar o conhecimento
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
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
                        <i class="fas fa-chart-line text-info" aria-hidden="true"></i> 
                        Seu Progresso
                    </h2>
                    <p class="mb-2">Exercícios concluídos: <strong>0 de <?php echo $totalResults; ?></strong></p>
                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: 0%" 
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" 
                             aria-label="Progresso geral: 0%">
                            0%
                        </div>
                    </div>
                    <a href="progress.php" class="btn btn-info btn-sm">
                        <i class="fas fa-chart-bar" aria-hidden="true"></i> Ver Progresso Detalhado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
<?php include 'config.php' ?>
