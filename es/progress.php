<?php
require_once 'config.php';

// Verificar se está logado
if (!isLoggedIn()) {
    redirect('login.php');
}

$title = 'Meu Progresso';

// Dados fictícios de progresso
$stats = [
    'exercises_completed' => 12,
    'exercises_total' => 25,
    'tutorials_read' => 8,
    'tutorials_total' => 15,
    'hours_studied' => 45,
    'streak_days' => 7
];

$progress_percentage = round(($stats['exercises_completed'] / $stats['exercises_total']) * 100);

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
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>HTML</span>
                            <span>80%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-danger" style="width: 80%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>CSS</span>
                            <span>65%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-primary" style="width: 65%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>JavaScript</span>
                            <span>45%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" style="width: 45%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <div class="d-flex justify-content-between">
                            <span>PHP</span>
                            <span>30%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" style="width: 30%"></div>
                        </div>
                    </div>
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
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Completou: Estrutura Básica HTML
                            </div>
                            <small class="text-muted">Hoje</small>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-book text-primary me-2"></i>
                                Leu: CSS Grid Layout
                            </div>
                            <small class="text-muted">Ontem</small>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Completou: Formulários HTML
                            </div>
                            <small class="text-muted">2 dias atrás</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>