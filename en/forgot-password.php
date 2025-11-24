<?php
session_start();
require_once '../src/SecurityHelper.php';
require_once '../src/Config/Environment.php';
require_once '../src/Models/PasswordRecovery.php';

Environment::load();

// Conectar ao banco
try {
    $host = Environment::get('DB_HOST', 'localhost');
    $dbname = Environment::get('DB_NAME', 'cursinho');
    $username = Environment::get('DB_USER', 'root');
    $password = Environment::get('DB_PASS', '');
    
    $pdo = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die('Erro de conexão: ' . $e->getMessage());
}

$recovery = new PasswordRecovery($pdo);
$step = $_GET['step'] ?? 1;
$message = '';
$error = '';

// Processar formulários
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!SecurityHelper::validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Token de segurança inválido';
    } else {
        switch ($step) {
            case 1: // Verificar email
                $email = SecurityHelper::sanitizeInput($_POST['email']);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $questions = $recovery->getSecurityQuestions($email);
                    if ($questions) {
                        $_SESSION['recovery_email'] = $email;
                        $_SESSION['recovery_questions'] = $questions;
                        header('Location: esqueci-senha.php?step=2');
                        exit;
                    } else {
                        $error = 'Email não encontrado ou sem perguntas de segurança configuradas';
                    }
                } else {
                    $error = 'Email inválido';
                }
                break;
                
            case 2: // Validar respostas
                if (!isset($_SESSION['recovery_email'])) {
                    header('Location: esqueci-senha.php');
                    exit;
                }
                
                $answers = [
                    'a1' => SecurityHelper::sanitizeInput($_POST['answer1']),
                    'a2' => SecurityHelper::sanitizeInput($_POST['answer2']),
                    'a3' => SecurityHelper::sanitizeInput($_POST['answer3'])
                ];
                
                $result = $recovery->validateAnswersAndGenerateKey($_SESSION['recovery_email'], $answers);
                if ($result && $result['success']) {
                    $_SESSION['recovery_key'] = $result['recovery_key'];
                    $_SESSION['recovery_username'] = $result['username'];
                    header('Location: esqueci-senha.php?step=3');
                    exit;
                } else {
                    $error = 'Uma ou mais respostas estão incorretas';
                }
                break;
                
            case 3: // Redefinir senha
                if (!isset($_SESSION['recovery_email']) || !isset($_SESSION['recovery_key'])) {
                    header('Location: esqueci-senha.php');
                    exit;
                }
                
                $keyInput = SecurityHelper::sanitizeInput($_POST['recovery_key']);
                $newPassword = $_POST['new_password'];
                $confirmPassword = $_POST['confirm_password'];
                
                if ($newPassword !== $confirmPassword) {
                    $error = 'Senhas não coincidem';
                } elseif (strlen($newPassword) < 6) {
                    $error = 'Senha deve ter pelo menos 6 caracteres';
                } else {
                    $result = $recovery->resetPasswordWithKey($_SESSION['recovery_email'], $keyInput, $newPassword);
                    if ($result && $result['success']) {
                        // Limpar sessão
                        unset($_SESSION['recovery_email'], $_SESSION['recovery_key'], $_SESSION['recovery_questions'], $_SESSION['recovery_username']);
                        $message = 'Senha redefinida com sucesso! Você pode fazer login agora.';
                        $step = 4; // Tela de sucesso
                    } else {
                        $error = $result['error'] ?? 'Palavra-chave inválida ou expirada';
                    }
                }
                break;
        }
    }
}

include 'header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-key me-2"></i>
                        Recuperar Senha
                    </h4>
                </div>
                <div class="card-body">
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?= SecurityHelper::escapeHtml($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= SecurityHelper::escapeHtml($message) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($step == 1): ?>
                        <!-- Passo 1: Inserir Email -->
                        <p class="text-muted mb-4">Digite seu email para iniciar a recuperação de senha:</p>
                        
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?= SecurityHelper::generateCSRFToken() ?>">
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-arrow-right me-2"></i>
                                Continuar
                            </button>
                        </form>
                        
                    <?php elseif ($step == 2): ?>
                        <!-- Passo 2: Perguntas de Segurança -->
                        <p class="text-muted mb-4">Responda às perguntas de segurança para <strong><?= SecurityHelper::escapeHtml($_SESSION['recovery_questions']['username']) ?></strong>:</p>
                        
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?= SecurityHelper::generateCSRFToken() ?>">
                            
                            <div class="mb-3">
                                <label class="form-label"><?= SecurityHelper::escapeHtml($_SESSION['recovery_questions']['question_1']) ?></label>
                                <input type="text" class="form-control" name="answer1" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><?= SecurityHelper::escapeHtml($_SESSION['recovery_questions']['question_2']) ?></label>
                                <input type="text" class="form-control" name="answer2" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><?= SecurityHelper::escapeHtml($_SESSION['recovery_questions']['question_3']) ?></label>
                                <input type="text" class="form-control" name="answer3" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-check me-2"></i>
                                Validar Respostas
                            </button>
                        </form>
                        
                    <?php elseif ($step == 3): ?>
                        <!-- Passo 3: Palavra-chave e Nova Senha -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Sua palavra-chave temporária é:</strong>
                            <div class="mt-2 p-3 bg-light border rounded text-center">
                                <h3 class="text-primary mb-0 font-monospace"><?= SecurityHelper::escapeHtml($_SESSION['recovery_key']) ?></h3>
                            </div>
                            <small class="text-muted">Esta chave expira em 15 minutos</small>
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?= SecurityHelper::generateCSRFToken() ?>">
                            
                            <div class="mb-3">
                                <label for="recovery_key" class="form-label">Digite a palavra-chave acima:</label>
                                <input type="text" class="form-control font-monospace" id="recovery_key" name="recovery_key" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Nova Senha:</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" minlength="6" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirmar Nova Senha:</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6" required>
                            </div>
                            
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-save me-2"></i>
                                Redefinir Senha
                            </button>
                        </form>
                        
                    <?php elseif ($step == 4): ?>
                        <!-- Passo 4: Sucesso -->
                        <div class="text-center">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            <h3 class="mt-3 text-success">Senha Redefinida!</h3>
                            <p class="text-muted">Sua senha foi alterada com sucesso.</p>
                            <a href="login.php" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Fazer Login
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($step < 4): ?>
                        <div class="text-center mt-4">
                            <a href="login.php" class="text-muted">
                                <i class="fas fa-arrow-left me-1"></i>
                                Voltar ao Login
                            </a>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>