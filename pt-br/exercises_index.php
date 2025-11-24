<?php
require_once 'config.php';

$title = 'Exercícios';

// Parâmetros de filtro
$category = sanitize($_GET['category'] ?? '');
$difficulty = sanitize($_GET['difficulty'] ?? '');
$search = sanitize($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;

// Buscar dados do banco
$exercises = [];
$totalResults = 0;
$conn = getDBConnection();

if ($conn) {
    $where = [];
    $params = [];
    $types = '';
    
    if ($category) {
        $where[] = "c.name = ?";
        $params[] = $category;
        $types .= 's';
    }
    
    if ($difficulty) {
        $where[] = "e.difficulty = ?";
        $params[] = $difficulty;
        $types .= 's';
    }
    
    if ($search) {
        $where[] = "(e.title LIKE ? OR e.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types .= 'ss';
    }
    
    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    
    $sql = "SELECT e.*, c.name as category_name 
            FROM exercises e 
            LEFT JOIN categories c ON e.category_id = c.id 
            $whereClause 
            ORDER BY e.created_at DESC 
            LIMIT ? OFFSET ?";
    
    $params[] = $perPage;
    $params[] = ($page - 1) * $perPage;
    $types .= 'ii';
    
    $stmt = $conn->prepare($sql);
    if ($stmt && $types) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $exercises = $result->fetch_all(MYSQLI_ASSOC);
    } elseif ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $exercises = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Contar total
    $countSql = "SELECT COUNT(*) FROM exercises e LEFT JOIN categories c ON e.category_id = c.id $whereClause";
    if ($where) {
        $countParams = array_slice($params, 0, -2);
        $countTypes = substr($types, 0, -2);
        $countStmt = $conn->prepare($countSql);
        $countStmt->bind_param($countTypes, ...$countParams);
        $countStmt->execute();
        $totalResults = $countStmt->get_result()->fetch_row()[0];
    } else {
        $totalResults = $conn->query($countSql)->fetch_row()[0];
    }
}

$totalPages = $totalResults > 0 ? ceil($totalResults / $perPage) : 1;

include 'header.php';
?>

<div class="container mt-4">
    <!-- Exibir mensagem de erro se houver -->
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Erro:</strong> <?php echo htmlspecialchars($error_message); ?>
            <br><small>Exibindo dados de demonstração. Verifique a conexão com o banco de dados.</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

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
                            <option value="PHP" <?php echo ($category === 'PHP') ? 'selected' : ''; ?>>PHP</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="difficulty" class="form-label">Dificuldade</label>
                        <select class="form-select" id="difficulty" name="difficulty">
                            <option value="">Todas as dificuldades</option>
                            <option value="Iniciante" <?php echo ($difficulty === 'Iniciante') ? 'selected' : ''; ?>>Iniciante</option>
                            <option value="Intermediário" <?php echo ($difficulty === 'Intermediário') ? 'selected' : ''; ?>>Intermediário</option>
                            <option value="Avançado" <?php echo ($difficulty === 'Avançado') ? 'selected' : ''; ?>>Avançado</option>
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
                    <h4 class="alert-heading">
                        <?php echo isset($error_message) ? 'Erro de Conexão' : 'Nenhum exercício encontrado!'; ?>
                    </h4>
                    <p>
                        <?php if (isset($error_message)): ?>
                            Não foi possível carregar os exercícios do banco de dados.
                        <?php else: ?>
                            Tente ajustar os filtros ou <a href="exercises_index.php" class="alert-link">limpar a busca</a>.
                        <?php endif; ?>
                    </p>
                    <?php if (!isset($error_message)): ?>
                        <div class="mt-3">
                            <a href="?category=HTML" class="btn btn-outline-primary btn-sm me-2">HTML</a>
                            <a href="?category=CSS" class="btn btn-outline-primary btn-sm me-2">CSS</a>
                            <a href="?category=JavaScript" class="btn btn-outline-primary btn-sm">JavaScript</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($exercises as $exercise): 
                // Mapear dificuldade do banco para exibição
                $difficulty_map_display = [
                    'beginner' => 'Iniciante', 
                    'intermediate' => 'Intermediário', 
                    'advanced' => 'Avançado'
                ];
                $display_difficulty = $difficulty_map_display[$exercise['difficulty'] ?? 'beginner'] ?? 'Iniciante';
                
                // Verificar progresso do usuário
                $completed = false;
                if (isLoggedIn()) {
                    $user_id = getCurrentUser()['id'];
                    $conn = getDBConnection();
                    if ($conn) {
                        $stmt = $conn->prepare("SELECT status FROM user_progress WHERE user_id = ? AND exercise_id = ?");
                        $stmt->bind_param("ii", $user_id, $exercise['id']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        $completed = $row && $row['status'] === 'completed';
                    }
                }
            ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="badge bg-<?php 
                                $cat_name = $exercise['category_name'] ?? 'Geral';
                                echo ($cat_name === 'HTML') ? 'danger' : 
                                    (($cat_name === 'CSS') ? 'primary' : 
                                    (($cat_name === 'JavaScript') ? 'warning' : 'info')); 
                            ?>">
                                <?php echo htmlspecialchars($cat_name); ?>
                            </span>
                            <span class="badge bg-<?php 
                                echo ($display_difficulty === 'Iniciante') ? 'success' : 
                                    (($display_difficulty === 'Intermediário') ? 'warning' : 'danger'); 
                            ?>">
                                <?php echo htmlspecialchars($display_difficulty); ?>
                            </span>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title h5"><?php echo htmlspecialchars($exercise['title'] ?? 'Exercício'); ?></h3>
                            <p class="card-text flex-grow-1"><?php echo htmlspecialchars($exercise['description'] ?? 'Descrição do exercício'); ?></p>
                            
                            <?php if ($completed): ?>
                                <div class="alert alert-success py-2 mt-auto" role="alert">
                                    <i class="fas fa-check-circle me-1" aria-hidden="true"></i>
                                    <small>Concluído</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <div class="d-flex gap-2">
                                <a href="show.php?type=exercise&id=<?php echo $exercise['id'] ?? 1; ?>" 
                                   class="btn btn-primary btn-sm flex-fill">
                                    <i class="fas fa-play" aria-hidden="true"></i> 
                                    <?php echo $completed ? 'Revisar' : 'Começar'; ?>
                                </a>
                                <?php if (isLoggedIn() && !$completed): ?>
                                <button onclick="completeExercise(<?php echo $exercise['id'] ?? 1; ?>)" 
                                        class="btn btn-success btn-sm" 
                                        title="Marcar como concluído">
                                    <i class="fas fa-check"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Paginação -->
    <?php if ($totalPages > 1): ?>
    <nav aria-label="Navegação de páginas dos exercícios" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?php 
                        $query = $_GET;
                        $query['page'] = $page - 1;
                        echo http_build_query($query);
                    ?>" aria-label="Página anterior">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link" aria-label="Página anterior">
                        <i class="fas fa-chevron-left" aria-hidden="true"></i>
                    </span>
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
                    ?>" aria-label="Próxima página">
                        <i class="fas fa-chevron-right"></i>
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
                    <?php 
                    $user_completed = 0;
                    $total_available = $totalResults > 0 ? $totalResults : count($exercises);
                    
                    if (isLoggedIn()) {
                        $user_id = getCurrentUser()['id'];
                        $conn = getDBConnection();
                        if ($conn) {
                            $stmt = $conn->prepare("SELECT COUNT(*) FROM user_progress WHERE user_id = ? AND status = 'completed'");
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $user_completed = $result->fetch_row()[0];
                        }
                    }
                    
                    $progress_percent = $total_available > 0 ? round(($user_completed / $total_available) * 100) : 0;
                    ?>
                    <p class="mb-2">Exercícios disponíveis: <strong><?php echo $total_available; ?></strong></p>
                    <p class="mb-2">Exercícios concluídos: <strong><?php echo $user_completed; ?></strong></p>
                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: <?php echo $progress_percent; ?>%" 
                             aria-valuenow="<?php echo $progress_percent; ?>" aria-valuemin="0" aria-valuemax="100" 
                             aria-label="Progresso geral: <?php echo $progress_percent; ?>%">
                            <?php echo $progress_percent; ?>%
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

<script>
function completeExercise(exerciseId) {
    if (!confirm('Marcar este exercício como concluído?')) return;
    
    fetch('api/mark_complete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'exercise_id=' + exerciseId + '&score=10'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Exercício marcado como concluído!');
            location.reload();
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        alert('Erro de conexão: ' + error);
    });
}
</script>

<?php include 'footer.php'; ?>