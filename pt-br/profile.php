<?php
require_once 'config.php';

// Verificar se usuário está logado
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Você precisa estar logado para acessar esta página.';
    header('Location: login.php');
    exit;
}

// Obter dados do usuário
$user = getCurrentUser();

// Buscar estatísticas do usuário
$stats = [
    'exercises_completed' => 0,
    'tutorials_viewed' => 0,
    'forum_posts' => 0,
    'total_score' => 0,
    'badges_earned' => 0,
    'study_streak' => 0
];

$conn = getDBConnection();
if ($conn) {
    // Exercícios concluídos
    $stmt = $conn->prepare("SELECT COUNT(*) FROM user_progress WHERE user_id = ? AND status = 'completed'");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $stats['exercises_completed'] = $stmt->get_result()->fetch_row()[0];
    
    // Tutoriais visualizados
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tutorial_progress WHERE user_id = ? AND status = 'completed'");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $stats['tutorials_viewed'] = $stmt->get_result()->fetch_row()[0];
    
    // Posts no fórum
    $stmt = $conn->prepare("SELECT COUNT(*) FROM forum_posts WHERE user_id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $stats['forum_posts'] = $stmt->get_result()->fetch_row()[0];
    
    // Pontuação total
    $stmt = $conn->prepare("SELECT SUM(score) FROM user_progress WHERE user_id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_row();
    $stats['total_score'] = $result[0] ?? 0;
}

// Definir título da página
$title = 'Perfil';

include 'header.php';
?>

<div class="container mt-4">
    <!-- Header do perfil -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-user" aria-hidden="true"></i> Meu Perfil</h1>
            <p class="lead">Gerencie suas informações e acompanhe seu progresso</p>
        </div>
        <div class="col-md-4 text-end">
            <?php if ($user['is_admin']): ?>
                <a href="admin.php" class="btn btn-warning" role="button">
                    <i class="fas fa-cog" aria-hidden="true"></i> Administração
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Informações do usuário -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h2 class="h5 mb-0">
                        <i class="fas fa-id-card" aria-hidden="true"></i> 
                        Informações Pessoais
                    </h2>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-2x text-muted"></i>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome Completo:</label>
                        <p class="mb-1"><?php echo sanitize($user['first_name'] . ' ' . $user['last_name']); ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Usuário:</label>
                        <p class="mb-1"><?php echo sanitize($user['username']); ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de Conta:</label>
                        <span class="badge bg-<?php echo $user['is_admin'] ? 'warning' : 'info'; ?>">
                            <?php echo $user['is_admin'] ? 'Administrador' : 'Usuário'; ?>
                        </span>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label fw-bold">Membro desde:</label>
                        <p class="mb-0 text-muted">
                            <?php echo date('d/m/Y', strtotime($user['created_at'] ?? 'now')); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h2 class="h5 mb-0">
                        <i class="fas fa-chart-bar" aria-hidden="true"></i> 
                        Estatísticas de Aprendizado
                    </h2>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <div class="bg-light p-3 rounded">
                                <i class="fas fa-tasks fa-2x text-primary mb-2"></i>
                                <h3 class="h4 mb-1"><?php echo $stats['exercises_completed']; ?></h3>
                                <p class="text-muted mb-0">Exercícios Concluídos</p>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="bg-light p-3 rounded">
                                <i class="fas fa-book-open fa-2x text-info mb-2"></i>
                                <h3 class="h4 mb-1"><?php echo $stats['tutorials_viewed']; ?></h3>
                                <p class="text-muted mb-0">Tutoriais Visualizados</p>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="bg-light p-3 rounded">
                                <i class="fas fa-comments fa-2x text-warning mb-2"></i>
                                <h3 class="h4 mb-1"><?php echo $stats['forum_posts']; ?></h3>
                                <p class="text-muted mb-0">Posts no Fórum</p>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="h6 mb-2">
                                <i class="fas fa-star text-warning me-1"></i>
                                Pontuação Total
                            </h3>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-warning" role="progressbar" 
                                     style="width: <?php echo min(100, ($stats['total_score'] / 1000) * 100); ?>%">
                                    <?php echo $stats['total_score']; ?> pts
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h3 class="h6 mb-2">
                                <i class="fas fa-fire text-danger me-1"></i>
                                Sequência de Estudos
                            </h3>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-danger" role="progressbar" 
                                     style="width: <?php echo min(100, ($stats['study_streak'] / 30) * 100); ?>%">
                                    <?php echo $stats['study_streak']; ?> dias
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Atividade recente -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h2 class="h5 mb-0">
                        <i class="fas fa-history" aria-hidden="true"></i> 
                        Atividade Recente
                    </h2>
                </div>
                <div class="card-body">
                    <?php
                    $recent_activities = [];
                    if ($conn) {
                        $stmt = $conn->prepare("
                            SELECT 'exercise' as type, e.title, up.completed_at as date 
                            FROM user_progress up 
                            JOIN exercises e ON up.exercise_id = e.id 
                            WHERE up.user_id = ? AND up.status = 'completed'
                            UNION ALL
                            SELECT 'tutorial' as type, t.title, tp.completed_at as date 
                            FROM tutorial_progress tp 
                            JOIN tutorials t ON tp.tutorial_id = t.id 
                            WHERE tp.user_id = ? AND tp.status = 'completed'
                            ORDER BY date DESC LIMIT 5
                        ");
                        $stmt->bind_param("ii", $user['id'], $user['id']);
                        $stmt->execute();
                        $recent_activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                    }
                    ?>
                    
                    <?php if (empty($recent_activities)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-clock fa-3x mb-3"></i>
                            <h4>Nenhuma atividade recente</h4>
                            <p>Comece a estudar para ver suas atividades aqui!</p>
                            <div class="mt-3">
                                <a href="exercises_index.php" class="btn btn-primary me-2">
                                    <i class="fas fa-tasks"></i> Fazer Exercícios
                                </a>
                                <a href="tutorials_index.php" class="btn btn-info">
                                    <i class="fas fa-book-open"></i> Ver Tutoriais
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recent_activities as $activity): ?>
                                <div class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-<?php echo $activity['type'] === 'exercise' ? 'tasks' : 'book-open'; ?> 
                                       text-<?php echo $activity['type'] === 'exercise' ? 'primary' : 'info'; ?> me-3"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo sanitize($activity['title']); ?></h6>
                                        <small class="text-muted">
                                            <?php echo ucfirst($activity['type']); ?> concluído em 
                                            <?php echo date('d/m/Y H:i', strtotime($activity['date'])); ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check"></i>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">
                        <i class="fas fa-tools" aria-hidden="true"></i> 
                        Ações Rápidas
                    </h2>
                    
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-home" aria-hidden="true"></i> Página Inicial
                        </a>
                        <a href="progress.php" class="btn btn-info">
                            <i class="fas fa-chart-line" aria-hidden="true"></i> Ver Progresso Detalhado
                        </a>
                        <a href="exercises_index.php" class="btn btn-primary">
                            <i class="fas fa-tasks" aria-hidden="true"></i> Exercícios
                        </a>
                        <a href="tutorials_index.php" class="btn btn-success">
                            <i class="fas fa-book-open" aria-hidden="true"></i> Tutoriais
                        </a>
                        <a href="forum_index.php" class="btn btn-warning">
                            <i class="fas fa-comments" aria-hidden="true"></i> Fórum
                        </a>
                        <a href="data-management.php" class="btn btn-outline-secondary">
                            <i class="fas fa-shield-alt" aria-hidden="true"></i> Meus Dados (LGPD)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>