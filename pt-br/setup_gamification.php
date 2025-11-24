<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    die('Acesso negado. Apenas administradores podem executar este script.');
}

$conn = getDBConnection();
if (!$conn) {
    die('Erro de conexão com o banco de dados.');
}

$sql = file_get_contents('setup_gamification.sql');
$statements = explode(';', $sql);

$success = 0;
$errors = [];

foreach ($statements as $statement) {
    $statement = trim($statement);
    if (empty($statement)) continue;
    
    if ($conn->query($statement)) {
        $success++;
    } else {
        $errors[] = $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Setup Sistema de Gamificação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="mb-0">Setup Sistema de Gamificação</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($success > 0): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Sucesso!</strong> <?php echo $success; ?> comandos executados.
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-warning">
                                <strong>Avisos:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <div class="alert alert-info">
                            <h5>Sistema de Gamificação Instalado!</h5>
                            <ul>
                                <li>✅ Sistema de XP e níveis</li>
                                <li>✅ Badges e conquistas</li>
                                <li>✅ Recompensas de login diário</li>
                                <li>✅ Sistema de mascotes</li>
                                <li>✅ Sequência de login</li>
                            </ul>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="badges.php" class="btn btn-success">
                                <i class="fas fa-trophy me-1"></i> Ver Conquistas
                            </a>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-home me-1"></i> Início
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>