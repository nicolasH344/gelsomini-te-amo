<?php
require_once 'config.php';
require_once 'database_connector.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$title = 'Conquistas e Badges';
$user = getCurrentUser();

// Verificar e conceder badges
global $feedbackSystem;
$feedbackSystem->checkBadges($user['id']);

// Buscar badges do usuário
$userBadges = $dbConnector->getUserBadges($user['id']);
$allBadges = $dbConnector->getAllBadges();

include 'header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-trophy me-2"></i>Suas Conquistas</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($userBadges)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-medal fa-3x text-muted mb-3"></i>
                            <h6>Nenhuma conquista ainda</h6>
                            <p class="text-muted">Complete exercícios e participe da comunidade para ganhar badges!</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($userBadges as $badge): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card border-warning">
                                        <div class="card-body text-center">
                                            <i class="<?php echo $badge['icon']; ?> fa-2x text-warning mb-2"></i>
                                            <h6 class="card-title"><?php echo sanitize($badge['name']); ?></h6>
                                            <p class="card-text small text-muted"><?php echo sanitize($badge['description']); ?></p>
                                            <small class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Conquistado em <?php echo date('d/m/Y', strtotime($badge['earned_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-target me-2"></i>Todas as Conquistas</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php 
                        $earnedBadgeIds = array_column($userBadges, 'badge_id');
                        foreach ($allBadges as $badge): 
                            $isEarned = in_array($badge['id'], $earnedBadgeIds);
                        ?>
                            <div class="col-md-6 mb-3">
                                <div class="card <?php echo $isEarned ? 'border-success' : 'border-light'; ?>">
                                    <div class="card-body text-center <?php echo $isEarned ? '' : 'opacity-50'; ?>">
                                        <i class="<?php echo $badge['icon']; ?> fa-2x <?php echo $isEarned ? 'text-success' : 'text-muted'; ?> mb-2"></i>
                                        <h6 class="card-title"><?php echo sanitize($badge['name']); ?></h6>
                                        <p class="card-text small text-muted"><?php echo sanitize($badge['description']); ?></p>
                                        <?php if ($isEarned): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Conquistado
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-lock me-1"></i>Bloqueado
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-chart-pie me-2"></i>Estatísticas</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="display-4 text-primary"><?php echo count($userBadges); ?></div>
                        <small class="text-muted">Badges Conquistados</small>
                    </div>
                    
                    <div class="progress mb-3">
                        <?php 
                        $progress = count($allBadges) > 0 ? (count($userBadges) / count($allBadges)) * 100 : 0;
                        ?>
                        <div class="progress-bar" role="progressbar" style="width: <?php echo $progress; ?>%" 
                             aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100">
                            <?php echo round($progress); ?>%
                        </div>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-bold text-success"><?php echo count($userBadges); ?></div>
                            <small class="text-muted">Conquistados</small>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-muted"><?php echo count($allBadges) - count($userBadges); ?></div>
                            <small class="text-muted">Restantes</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="fas fa-lightbulb me-2"></i>Próximas Conquistas</h6>
                </div>
                <div class="card-body">
                    <?php 
                    $nextBadges = array_filter($allBadges, function($badge) use ($earnedBadgeIds) {
                        return !in_array($badge['id'], $earnedBadgeIds);
                    });
                    $nextBadges = array_slice($nextBadges, 0, 3);
                    ?>
                    
                    <?php if (empty($nextBadges)): ?>
                        <p class="text-muted text-center">Parabéns! Você conquistou todos os badges disponíveis!</p>
                    <?php else: ?>
                        <?php foreach ($nextBadges as $badge): ?>
                            <div class="d-flex align-items-center mb-3">
                                <i class="<?php echo $badge['icon']; ?> fa-lg text-muted me-3"></i>
                                <div>
                                    <h6 class="mb-0"><?php echo sanitize($badge['name']); ?></h6>
                                    <small class="text-muted"><?php echo sanitize($badge['description']); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>