<?php
require_once 'config.php';
require_once 'exercise_functions.php';

$title = 'Exercícios';

// Parâmetros de filtro
$category = sanitize($_GET['category'] ?? '');
$difficulty = sanitize($_GET['difficulty'] ?? '');
$search = sanitize($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;

// Buscar exercícios
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

    <!-- Cabeçalho -->
    <div class="exercises-header text-center mb-5">
        <h1 class="display-4 fw-bold mb-3">
            <i class="fas fa-dumbbell text-primary"></i>
            Exercícios Práticos
        </h1>
        <p class="lead text-muted">
            Pratique e aprimore suas habilidades com exercícios interativos
        </p>
    </div>

    <!-- Filtros -->
    <div class="filters-card card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="category" class="form-label fw-semibold">
                            <i class="fas fa-tag text-primary me-1"></i>
                            Categoria
                        </label>
                        <select class="form-select rounded-pill" id="category" name="category" onchange="this.form.submit()">
                            <option value="">Todas as categorias</option>
                            <option value="HTML" <?php echo ($category === 'HTML') ? 'selected' : ''; ?>>📄 HTML</option>
                            <option value="CSS" <?php echo ($category === 'CSS') ? 'selected' : ''; ?>>🎨 CSS</option>
                            <option value="JavaScript" <?php echo ($category === 'JavaScript') ? 'selected' : ''; ?>>⚡ JavaScript</option>
                            <option value="PHP" <?php echo ($category === 'PHP') ? 'selected' : ''; ?>>🐘 PHP</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="difficulty" class="form-label fw-semibold">
                            <i class="fas fa-signal text-warning me-1"></i>
                            Dificuldade
                        </label>
                        <select class="form-select rounded-pill" id="difficulty" name="difficulty" onchange="this.form.submit()">
                            <option value="">Todas as dificuldades</option>
                            <option value="Iniciante" <?php echo ($difficulty === 'Iniciante') ? 'selected' : ''; ?>>🌱 Iniciante</option>
                            <option value="Intermediário" <?php echo ($difficulty === 'Intermediário') ? 'selected' : ''; ?>>🌿 Intermediário</option>
                            <option value="Avançado" <?php echo ($difficulty === 'Avançado') ? 'selected' : ''; ?>>🌳 Avançado</option>
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
                        <?php if ($category || $difficulty || $search): ?>
                            <a href="exercises_index.php" class="btn btn-outline-secondary w-100 rounded-pill btn-sm">
                                <i class="fas fa-redo"></i> Limpar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Filtros ativos -->
                <?php if ($category || $difficulty || $search): ?>
                    <div class="active-filters mt-3 pt-3 border-top">
                        <small class="text-muted fw-semibold d-block mb-2">
                            <i class="fas fa-filter me-1"></i>
                            Filtros ativos:
                        </small>
                        <div class="d-flex flex-wrap gap-2">
                            <?php if ($category): ?>
                                <span class="badge bg-primary rounded-pill px-3 py-2">
                                    Categoria: <?php echo htmlspecialchars($category); ?>
                                    <a href="?<?php 
                                        $query = $_GET;
                                        unset($query['category']);
                                        echo http_build_query($query);
                                    ?>" class="text-white ms-2" title="Remover filtro">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($difficulty): ?>
                                <span class="badge bg-warning rounded-pill px-3 py-2">
                                    Dificuldade: <?php echo htmlspecialchars($difficulty); ?>
                                    <a href="?<?php 
                                        $query = $_GET;
                                        unset($query['difficulty']);
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
                                <?php echo $totalResults; ?> resultado<?php echo $totalResults != 1 ? 's' : ''; ?>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>
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
                // Exibir dificuldade
                $display_difficulty = $exercise['difficulty'] ?? 'Iniciante';
                $category = $exercise['category'] ?? 'Geral';
                
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
                
                // Cores das categorias
                $catColors = ['HTML' => 'danger', 'CSS' => 'primary', 'JavaScript' => 'warning', 'PHP' => 'info'];
                $catColor = $catColors[$category] ?? 'secondary';
                
                // Cores das dificuldades
                $levelMap = ['Iniciante' => 'success', 'Intermediário' => 'warning', 'Avançado' => 'danger'];
                $levelColor = $levelMap[$display_difficulty] ?? 'secondary';
            ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card exercise-card h-100 shadow-sm border-0 <?php echo $completed ? 'completed' : ''; ?>">
                        <?php if ($completed): ?>
                            <div class="completed-badge">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-header bg-white border-0 pt-3 pb-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge rounded-pill bg-<?php echo $catColor; ?> px-3 py-2">
                                    <i class="fas fa-code me-1"></i>
                                    <?php echo htmlspecialchars($category); ?>
                                </span>
                                <span class="badge rounded-pill bg-<?php echo $levelColor; ?> px-3 py-2">
                                    <i class="fas fa-signal me-1"></i>
                                    <?php echo htmlspecialchars($display_difficulty); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column pt-2">
                            <h3 class="card-title h5 fw-bold mb-3">
                                <?php echo htmlspecialchars($exercise['title'] ?? 'Exercício'); ?>
                            </h3>
                            <p class="card-text text-muted flex-grow-1 mb-3">
                                <?php echo htmlspecialchars($exercise['description'] ?? 'Descrição do exercício'); ?>
                            </p>
                        </div>
                        
                        <div class="card-footer bg-white border-0 pt-0 pb-3">
                            <div class="d-flex gap-2">
                                <a href="show.php?type=exercise&id=<?php echo $exercise['id'] ?? 1; ?>" 
                                   class="btn btn-<?php echo $completed ? 'outline-primary' : 'primary'; ?> flex-fill rounded-pill">
                                    <i class="fas fa-<?php echo $completed ? 'redo' : 'play'; ?> me-1"></i>
                                    <?php echo $completed ? 'Revisar' : 'Começar'; ?>
                                </a>
                                <?php if (isLoggedIn() && !$completed): ?>
                                <button onclick="completeExercise(<?php echo $exercise['id'] ?? 1; ?>)" 
                                        class="btn btn-outline-success rounded-pill px-3" 
                                        title="Marcar como concluído"
                                        aria-label="Marcar exercício como concluído">
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
    <div class="row mt-5 g-4">
        <div class="col-md-6">
            <div class="info-card card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-circle bg-warning bg-opacity-10 text-warning me-3">
                            <i class="fas fa-lightbulb fa-2x"></i>
                        </div>
                        <h2 class="h5 mb-0 fw-bold">Dicas para Estudar</h2>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-center mb-3 tip-item">
                            <div class="tip-icon me-3">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <span class="tip-text">Comece pelos exercícios de nível iniciante e evolua gradualmente</span>
                        </li>
                        <li class="d-flex align-items-center mb-3 tip-item">
                            <div class="tip-icon me-3">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <span class="tip-text">Pratique regularmente para fixar o conhecimento adquirido</span>
                        </li>
                        <li class="d-flex align-items-center mb-3 tip-item">
                            <div class="tip-icon me-3">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <span class="tip-text">Use a comunidade para compartilhar soluções e tirar dúvidas</span>
                        </li>
                        <li class="d-flex align-items-center tip-item">
                            <div class="tip-icon me-3">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <span class="tip-text">Revise exercícios concluídos para reforçar o aprendizado</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="progress-card card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-circle bg-info bg-opacity-10 text-info me-3">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <h2 class="h5 mb-0 fw-bold">Seu Progresso</h2>
                    </div>
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
                    <div class="stats-grid row g-3 mb-4">
                        <div class="col-6">
                            <div class="stat-item text-center p-3 bg-light rounded-3">
                                <div class="stat-value display-6 fw-bold text-primary"><?php echo $total_available; ?></div>
                                <div class="stat-label text-muted small">Disponíveis</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item text-center p-3 bg-light rounded-3">
                                <div class="stat-value display-6 fw-bold text-success"><?php echo $user_completed; ?></div>
                                <div class="stat-label text-muted small">Concluídos</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Progresso geral</span>
                            <span class="fw-bold text-primary"><?php echo $progress_percent; ?>%</span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar bg-gradient" role="progressbar" 
                                 style="width: <?php echo $progress_percent; ?>%" 
                                 aria-valuenow="<?php echo $progress_percent; ?>" 
                                 aria-valuemin="0" aria-valuemax="100" 
                                 aria-label="Progresso geral: <?php echo $progress_percent; ?>%">
                            </div>
                        </div>
                    </div>
                    
                    <a href="progress.php" class="btn btn-info w-100 rounded-pill">
                        <i class="fas fa-chart-bar me-2"></i>
                        Ver Progresso Detalhado
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

<style>
/* Cabeçalho da página */
.exercises-header {
    padding: 2rem 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    background: -webkit-linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    margin: -1.5rem -15px 2rem -15px;
    color: white;
    border-radius: 0 0 20px 20px;
}

.exercises-header .display-4 {
    font-size: 2.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

.exercises-header .lead {
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

/* Cards de exercícios */
.exercise-card {
    border-radius: 15px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    background: white;
}

.exercise-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15) !important;
}

.exercise-card.completed {
    border: 2px solid #28a745;
    background: linear-gradient(135deg, #fff 0%, #f0fff4 100%);
}

.completed-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #28a745;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    z-index: 10;
    animation: bounceIn 0.5s ease;
}

@keyframes bounceIn {
    0% { transform: scale(0); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.exercise-card .badge {
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.3px;
}

.exercise-card .card-title {
    color: #2d3748;
    line-height: 1.4;
}

.exercise-card .card-text {
    font-size: 0.9rem;
    line-height: 1.6;
}

.exercise-card .btn {
    font-weight: 600;
    transition: all 0.3s ease;
}

.exercise-card .btn:hover {
    transform: scale(1.05);
}

/* Cards de informação */
.info-card, .progress-card {
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.info-card:hover, .progress-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15) !important;
}

.icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.tip-item {
    padding: 0.75rem 0;
    transition: all 0.3s ease;
}

.tip-item:hover {
    transform: translateX(5px);
}

.tip-icon {
    flex-shrink: 0;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.tip-text {
    line-height: 1.6;
    color: #4a5568;
}

.stat-item {
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.stat-value {
    line-height: 1;
    margin-bottom: 0.25rem;
}

/* Barra de progresso */
.progress {
    border-radius: 10px;
    background-color: #e9ecef;
    overflow: hidden;
}

.progress-bar {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    transition: width 0.6s ease;
}

/* Estado vazio */
.alert-info {
    border-radius: 15px;
    border: none;
    background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
}

/* Paginação */
.pagination .page-link {
    border-radius: 10px;
    margin: 0 3px;
    border: none;
    color: #667eea;
    font-weight: 600;
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.pagination .page-link:hover {
    background-color: #667eea15;
    color: #667eea;
}

/* Responsividade */
@media (max-width: 768px) {
    .exercises-header .display-4 {
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
}

/* Animação de entrada */
.exercise-card {
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
.exercise-card:nth-child(1) { animation-delay: 0.1s; }
.exercise-card:nth-child(2) { animation-delay: 0.2s; }
.exercise-card:nth-child(3) { animation-delay: 0.3s; }
.exercise-card:nth-child(4) { animation-delay: 0.4s; }
.exercise-card:nth-child(5) { animation-delay: 0.5s; }
.exercise-card:nth-child(6) { animation-delay: 0.6s; }
</style>

<?php include 'footer.php'; ?>