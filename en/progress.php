<?php
// Incluir configurações
require_once 'config.php';

// Verificar se usuário está logado
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Você precisa estar logado para acessar esta página.';
    header('Location: login.php');
    exit;
}

// Obter dados do usuário
$user = getCurrentUser();

// Definir título da página
$title = 'Meu Progresso';

include 'header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h1 class="h4 mb-0">
                        <i class="fas fa-chart-line" aria-hidden="true"></i> 
                        My Learning Progress
                    </h1>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <i class="fas fa-tasks text-primary" style="font-size: 2rem;" aria-hidden="true"></i>
                                    <h3 class="h4 mt-2">0</h3>
                                    <p class="text-muted">Completed Exercises</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 text-center mb-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <i class="fas fa-book text-success" style="font-size: 2rem;" aria-hidden="true"></i>
                                    <h3 class="h4 mt-2">0</h3>
                                    <p class="text-muted">Tutorials Read</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 text-center mb-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <i class="fas fa-comments text-warning" style="font-size: 2rem;" aria-hidden="true"></i>
                                    <h3 class="h4 mt-2">0</h3>
                                    <p class="text-muted">Forum posts</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 text-center mb-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <i class="fas fa-trophy text-danger" style="font-size: 2rem;" aria-hidden="true"></i>
                                    <h3 class="h4 mt-2">0</h3>
                                    <p class="text-muted">Achievements</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h2 class="h5">Progress through Technology</h2>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="h6">HTML5</h3>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 0%" 
                                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" aria-label="Progresso em HTML5: 0%">
                                    0%
                                </div>
                            </div>
                            
                            <h3 class="h6">CSS3</h3>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 0%" 
                                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" aria-label="Progresso em CSS3: 0%">
                                    0%
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h3 class="h6">JavaScript</h3>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" 
                                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" aria-label="Progresso em JavaScript: 0%">
                                    0%
                                </div>
                            </div>
                            
                            <h3 class="h6">PHP</h3>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-info" role="progressbar" style="width: 0%" 
                                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" aria-label="Progresso em PHP: 0%">
                                    0%
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-4" role="alert">
                        <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
                        <strong>Tip:</strong> Start by doing some exercises to see your progress here!
                    </div>
                    
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="exercises_index.php" class="btn btn-primary">
                            <i class="fas fa-play" aria-hidden="true"></i> Start Exercises
                        </a>
                        <a href="tutorials_index.php" class="btn btn-success">
                            <i class="fas fa-book" aria-hidden="true"></i> Views Tutorials
                        </a>
                        <a href="profile.php" class="btn btn-secondary">
                            <i class="fas fa-user" aria-hidden="true"></i> Back to Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

