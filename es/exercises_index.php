<?php
require_once 'config.php';
require_once 'exercise_functions.php';

$title = 'Ejercicios';

// Parâmetros de filtro
$category = sanitize($_GET['category'] ?? '');
$difficulty = sanitize($_GET['difficulty'] ?? '');
$search = sanitize($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;

// Buscar dados
$exercises = getExercises($category, $difficulty, $search, $page, $perPage);
$totalResults = countExercises($category, $difficulty, $search);
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
                            <option value="">Todas las categorías</option>
                            <option value="HTML" <?php echo ($category === 'HTML') ? 'selected' : ''; ?>>HTML</option>
                            <option value="CSS" <?php echo ($category === 'CSS') ? 'selected' : ''; ?>>CSS</option>
                            <option value="JavaScript" <?php echo ($category === 'JavaScript') ? 'selected' : ''; ?>>JavaScript</option>
                            <option value="PHP" <?php echo ($category === 'PHP') ? 'selected' : ''; ?>>PHP</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="difficulty" class="form-label">Dificuldade</label>
                        <select class="form-select" id="difficulty" name="difficulty">
                            <option value="">Todas las dificultades</option>
                            <option value="Iniciante" <?php echo ($difficulty === 'Iniciante') ? 'selected' : ''; ?>>Principiante</option>
                            <option value="Intermediário" <?php echo ($difficulty === 'Intermediário') ? 'selected' : ''; ?>>Intermedio</option>
                            <option value="Avançado" <?php echo ($difficulty === 'Avançado') ? 'selected' : ''; ?>>Avanzado</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Escribe palabras clave...">
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
                $display_difficulty = $difficulty_map_display[$exercise['difficulty_level']] ?? $exercise['difficulty_level'];
                
                // Verificar progresso do usuário (simulado)
                $completed = false;
            ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="badge bg-<?php 
                                echo ($exercise['category_name'] === 'HTML') ? 'danger' : 
                                    (($exercise['category_name'] === 'CSS') ? 'primary' : 
                                    (($exercise['category_name'] === 'JavaScript') ? 'warning' : 'info')); 
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
                            
                            <?php if ($completed): ?>
                                <div class="alert alert-success py-2 mt-auto" role="alert">
                                    <i class="fas fa-check-circle me-1" aria-hidden="true"></i>
                                    <small>Concluído</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <div class="d-flex gap-2">
                                <a href="exercise_detail.php?id=<?php echo $exercise['id']; ?>" 
                                   class="btn btn-primary btn-sm flex-fill">
                                    <i class="fas fa-play" aria-hidden="true"></i> 
                                    <?php echo $completed ? 'Revisar' : 'Começar'; ?>
                                </a>
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
                    <p class="mb-2">Exercícios disponíveis: <strong><?php echo $totalResults > 0 ? $totalResults : count($exercises); ?></strong></p>
                    <p class="mb-2">Exercícios concluídos: <strong>0</strong></p>
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