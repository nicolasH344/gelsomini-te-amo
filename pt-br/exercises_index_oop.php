<?php
require_once 'config.php';
require_once '../src/autoload.php';

use App\Controllers\ExerciseController;

$title = 'Exercícios';
$controller = new ExerciseController();
$data = $controller->index();

extract($data);

include 'header.php';
?>

<div class="container mt-4">
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
                    <h4 class="alert-heading">Nenhum exercício encontrado!</h4>
                    <p>Tente ajustar os filtros ou <a href="exercises_index_oop.php" class="alert-link">limpar a busca</a>.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($exercises as $exercise): 
                $difficulty_map_display = [
                    'beginner' => 'Iniciante', 
                    'intermediate' => 'Intermediário', 
                    'advanced' => 'Avançado'
                ];
                $display_difficulty = $difficulty_map_display[$exercise['difficulty_level']] ?? $exercise['difficulty_level'];
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
                                <a href="exercise_detail_oop.php?id=<?php echo $exercise['id']; ?>" 
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
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>