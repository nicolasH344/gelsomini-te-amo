<?php
require_once 'config.php';

$title = 'Conquistas';

// Buscar badges do usuário
$user_badges = [];
$available_badges = [];
$conn = getDBConnection();

if ($conn && isLoggedIn()) {
    $user_id = getCurrentUser()['id'];
    
    // Badges do usuário
    $stmt = $conn->prepare("SELECT b.*, ub.earned_at FROM user_badges ub 
                           JOIN badges b ON ub.badge_id = b.id 
                           WHERE ub.user_id = ? ORDER BY ub.earned_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_badges = $result->fetch_all(MYSQLI_ASSOC);
    
    // Todos os badges disponíveis
    $available_badges = $conn->query("SELECT * FROM badges ORDER BY name")->fetch_all(MYSQLI_ASSOC);
}

include 'header.php';
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-trophy text-warning"></i> Conquistas</h1>
            <p class="lead">Acompanhe seu progresso e desbloqueie novas conquistas</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3><?php echo count($user_badges); ?></h3>
                    <small>Conquistas Desbloqueadas</small>
                </div>
            </div>
        </div>
    </div>

    <?php if (!isLoggedIn()): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Faça login</strong> para ver suas conquistas e progresso.
            <a href="login.php" class="btn btn-primary btn-sm ms-2">Fazer Login</a>
        </div>
    <?php endif; ?>

    <!-- Conquistas Desbloqueadas -->
    <?php if (!empty($user_badges)): ?>
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="h4 mb-3"><i class="fas fa-medal text-success"></i> Suas Conquistas</h2>
        </div>
        <?php foreach ($user_badges as $badge): ?>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <div class="badge-icon mb-3">
                        <i class="fas fa-trophy fa-3x text-warning"></i>
                    </div>
                    <h5 class="card-title"><?php echo htmlspecialchars($badge['name']); ?></h5>
                    <p class="card-text text-muted"><?php echo htmlspecialchars($badge['description']); ?></p>
                    <small class="text-success">
                        <i class="fas fa-check-circle"></i>
                        Desbloqueado em <?php echo date('d/m/Y', strtotime($badge['earned_at'])); ?>
                    </small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Todas as Conquistas Disponíveis -->
    <div class="row">
        <div class="col-12">
            <h2 class="h4 mb-3"><i class="fas fa-list"></i> Todas as Conquistas</h2>
        </div>
        
        <?php if (empty($available_badges)): ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Nenhuma conquista disponível no momento. Volte mais tarde!
            </div>
        </div>
        <?php else: ?>
            <?php 
            $earned_badge_ids = array_column($user_badges, 'id');
            foreach ($available_badges as $badge): 
                $is_earned = in_array($badge['id'], $earned_badge_ids);
            ?>
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card <?php echo $is_earned ? 'border-success' : 'border-secondary'; ?>">
                    <div class="card-body text-center">
                        <div class="badge-icon mb-3">
                            <i class="fas fa-trophy fa-3x <?php echo $is_earned ? 'text-warning' : 'text-muted'; ?>"></i>
                        </div>
                        <h5 class="card-title"><?php echo htmlspecialchars($badge['name']); ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($badge['description']); ?></p>
                        
                        <?php if ($is_earned): ?>
                            <span class="badge bg-success">
                                <i class="fas fa-check"></i> Desbloqueado
                            </span>
                        <?php else: ?>
                            <span class="badge bg-secondary">
                                <i class="fas fa-lock"></i> Bloqueado
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Dicas para Desbloquear -->
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h3 class="h5 card-title">
                        <i class="fas fa-lightbulb text-warning"></i> 
                        Como Desbloquear Conquistas
                    </h3>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Complete exercícios e tutoriais
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Participe ativamente do fórum
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Mantenha uma sequência de estudos
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h3 class="h5 card-title">
                        <i class="fas fa-chart-line text-info"></i> 
                        Seu Progresso
                    </h3>
                    <?php
                    $total_badges = count($available_badges);
                    $earned_badges = count($user_badges);
                    $progress_percent = $total_badges > 0 ? round(($earned_badges / $total_badges) * 100) : 0;
                    ?>
                    <p>Conquistas desbloqueadas: <strong><?php echo $earned_badges; ?>/<?php echo $total_badges; ?></strong></p>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-warning" role="progressbar" 
                             style="width: <?php echo $progress_percent; ?>%" 
                             aria-valuenow="<?php echo $progress_percent; ?>" 
                             aria-valuemin="0" aria-valuemax="100">
                            <?php echo $progress_percent; ?>%
                        </div>
                    </div>
                    <a href="progress.php" class="btn btn-info btn-sm">
                        <i class="fas fa-chart-bar"></i> Ver Progresso Detalhado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>