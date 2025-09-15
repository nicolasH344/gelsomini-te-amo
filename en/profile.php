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
$title = 'Perfil';

include 'header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0">
                        <i class="fas fa-user" aria-hidden="true"></i> 
                        My Profile
                    </h1>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="h5">Personal Information</h3>
                            <p><strong>Name:</strong> <?php echo sanitize($user['first_name'] . ' ' . $user['last_name']); ?></p>
                            <p><strong>User:</strong> <?php echo sanitize($user['username']); ?></p>
                            <p><strong>Type:</strong> <?php echo $user['is_admin'] ? 'Administrador' : 'Usuário'; ?></p>
                        </div>
                        <div class="col-md-6">
                            <h3 class="h5">Statistics</h3>
                            <p><strong>Completed Exercises:</strong> 0</p>
                            <p><strong>Viewed Tutorials:</strong> 0</p>
                            <p><strong>Forum Posts:</strong> 0</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left" aria-hidden="true"></i> Return
                        </a>
                        <a href="progress.php" class="btn btn-info">
                            <i class="fas fa-chart-line" aria-hidden="true"></i> View Progress
                        </a>
                        <?php if ($user['is_admin']): ?>
                            <a href="#" class="btn btn-warning">
                                <i class="fas fa-cog" aria-hidden="true"></i> Administration
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

