<?php
require_once 'config.php';
require_once 'database_connector.php';

// Verificar se está logado
if (!isLoggedIn()) {
    redirect('login.php');
}

$title = 'Meu Progresso';
$user = getCurrentUser();
$user_id = $user['id'];



// Obter dados do progresso usando mysqli
try {
    require_once 'database.php';
    $db = new Database();
    $conn = $db->conn;
    
    // Total de exercícios
    $result = $conn->query("SELECT COUNT(*) as total FROM exercises");
    $total_exercises = $result->fetch_assoc()['total'];
    
    // Exercícios completados
    $stmt = $conn->prepare("SELECT COUNT(*) as completed FROM user_progress WHERE user_id = ? AND status = 'completed'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $completed_exercises = $stmt->get_result()->fetch_assoc()['completed'];
    
    // Total de tutoriais
    $result = $conn->query("SELECT COUNT(*) as total FROM tutorials WHERE status = 'Publicado'");
    $total_tutorials = $result->fetch_assoc()['total'];
    
    // Tutoriais completados
    $stmt = $conn->prepare("SELECT COUNT(*) as completed FROM tutorial_progress WHERE user_id = ? AND status = 'completed'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $completed_tutorials = $stmt->get_result()->fetch_assoc()['completed'];
    
    // Atividades recentes (exercícios + tutoriais)
    $activities = [];
    
    // Exercícios recentes
    $stmt = $conn->prepare("
        SELECT e.title, up.completed_at, up.score, 'exercise' as type
        FROM user_progress up
        JOIN exercises e ON up.exercise_id = e.id
        WHERE up.user_id = ? AND up.status = 'completed'
        ORDER BY up.completed_at DESC
        LIMIT 3
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    
    // Tutoriais recentes
    $stmt = $conn->prepare("
        SELECT t.title, tp.completed_at, 100 as score, 'tutorial' as type
        FROM tutorial_progress tp
        JOIN tutorials t ON tp.tutorial_id = t.id
        WHERE tp.user_id = ? AND tp.status = 'completed'
        ORDER BY tp.completed_at DESC
        LIMIT 3
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    
    // Ordenar atividades por data
    usort($activities, function($a, $b) {
        return strtotime($b['completed_at']) - strtotime($a['completed_at']);
    });
    $activities = array_slice($activities, 0, 5);
    
    $stats = [
        'exercises_completed' => $completed_exercises,
        'exercises_total' => $total_exercises,
        'tutorials_read' => $completed_tutorials,
        'tutorials_total' => $total_tutorials,
        'hours_studied' => ($completed_exercises + $completed_tutorials) * 1.5,
        'streak_days' => min($completed_exercises + $completed_tutorials, 7)
    ];
    
    $categories = [];
    $db->closeConnection();
    
} catch (Exception $e) {
    // Fallback para dados fictícios se houver erro
    $stats = [
        'exercises_completed' => 0,
        'exercises_total' => 8,
        'tutorials_read' => 0,
        'tutorials_total' => 5,
        'hours_studied' => 0,
        'streak_days' => 0
    ];
    $categories = [];
    $activities = [];
}

$progress_percentage = $stats['exercises_total'] > 0 ? round(($stats['exercises_completed'] / $stats['exercises_total']) * 100) : 0;

include 'header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h2 mb-4">
                <i class="fas fa-chart-line text-primary"></i>
                Meu Progresso
            </h1>
        </div>
    </div>

    <!-- Estatísticas Gerais -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-dumbbell fa-2x mb-2"></i>
                    <h4><?php echo $stats['exercises_completed']; ?>/<?php echo $stats['exercises_total']; ?></h4>
                    <p class="mb-0">Exercícios</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-book fa-2x mb-2"></i>
                    <h4><?php echo $stats['tutorials_read']; ?>/<?php echo $stats['tutorials_total']; ?></h4>
                    <p class="mb-0">Tutoriais</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <h4><?php echo $stats['hours_studied']; ?>h</h4>
                    <p class="mb-0">Estudadas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-fire fa-2x mb-2"></i>
                    <h4><?php echo $stats['streak_days']; ?></h4>
                    <p class="mb-0">Dias seguidos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Progresso Geral -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar"></i>
                        Progresso Geral
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Exercícios Concluídos</span>
                            <span><?php echo $progress_percentage; ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-primary" style="width: <?php echo $progress_percentage; ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Tutoriais Lidos</span>
                            <span><?php echo round(($stats['tutorials_read'] / $stats['tutorials_total']) * 100); ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: <?php echo round(($stats['tutorials_read'] / $stats['tutorials_total']) * 100); ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progresso por Categoria -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-tags"></i>
                        Progresso por Categoria
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (empty($categories)): ?>
                        <p class="text-muted">Nenhum progresso registrado ainda.</p>
                        <a href="exercises_index.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-play me-1"></i>Começar Exercícios
                        </a>
                    <?php else: ?>
                        <?php 
                        $colors = ['bg-danger', 'bg-primary', 'bg-warning', 'bg-info', 'bg-success'];
                        $color_index = 0;
                        foreach ($categories as $category): 
                            $percentage = $category['total'] > 0 ? round(($category['completed'] / $category['total']) * 100) : 0;
                            $color = $colors[$color_index % count($colors)];
                            $color_index++;
                        ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span><?php echo ucfirst($category['category']); ?></span>
                                <span><?php echo $percentage; ?>% (<?php echo $category['completed']; ?>/<?php echo $category['total']; ?>)</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar <?php echo $color; ?>" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-trophy"></i>
                        Conquistas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-medal text-warning fa-2x me-3"></i>
                        <div>
                            <h6 class="mb-0">Primeiro Exercício</h6>
                            <small class="text-muted">Completou seu primeiro exercício</small>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-fire text-danger fa-2x me-3"></i>
                        <div>
                            <h6 class="mb-0">Sequência de 7 dias</h6>
                            <small class="text-muted">Estudou por 7 dias consecutivos</small>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <i class="fas fa-star text-primary fa-2x me-3"></i>
                        <div>
                            <h6 class="mb-0">10 Exercícios</h6>
                            <small class="text-muted">Completou 10 exercícios</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Atividade Recente -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-history"></i>
                        Atividade Recente
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (empty($activities)): ?>
                        <p class="text-muted">Nenhuma atividade recente.</p>
                        <a href="exercises_index.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-play me-1"></i>Começar Agora
                        </a>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($activities as $activity): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-<?php echo ($activity['type'] ?? 'exercise') === 'tutorial' ? 'book' : 'dumbbell'; ?> text-success me-2"></i>
                                    <?php echo ($activity['type'] ?? 'exercise') === 'tutorial' ? 'Tutorial' : 'Exercício'; ?>: <?php echo sanitize($activity['title']); ?>
                                    <?php if ($activity['score'] > 0): ?>
                                        <span class="badge bg-primary ms-2"><?php echo $activity['score']; ?> pts</span>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted">
                                    <?php 
                                    $date = new DateTime($activity['completed_at']);
                                    $now = new DateTime();
                                    $diff = $now->diff($date);
                                    
                                    if ($diff->days == 0) {
                                        echo 'Hoje';
                                    } elseif ($diff->days == 1) {
                                        echo 'Ontem';
                                    } else {
                                        echo $diff->days . ' dias atrás';
                                    }
                                    ?>
                                </small>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>