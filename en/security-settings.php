<?php
session_start();
require_once '../src/SecurityHelper.php';
require_once '../src/Config/Environment.php';
require_once '../src/Models/PasswordRecovery.php';

// Verificar se está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

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
$message = '';
$error = '';

// Verificar se já tem perguntas configuradas
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM user_security_questions WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$hasQuestions = $stmt->fetch()['count'] > 0;

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!SecurityHelper::validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Token de segurança inválido';
    } else {
        $questions = [
            'q1' => SecurityHelper::sanitizeInput($_POST['question1']),
            'a1' => SecurityHelper::sanitizeInput($_POST['answer1']),
            'q2' => SecurityHelper::sanitizeInput($_POST['question2']),
            'a2' => SecurityHelper::sanitizeInput($_POST['answer2']),
            'q3' => SecurityHelper::sanitizeInput($_POST['question3']),
            'a3' => SecurityHelper::sanitizeInput($_POST['answer3'])
        ];
        
        // Validar se todas as perguntas e respostas foram preenchidas
        $valid = true;
        foreach ($questions as $key => $value) {
            if (empty(trim($value))) {
                $valid = false;
                break;
            }
        }
        
        if (!$valid) {
            $error = 'Todas as perguntas e respostas devem ser preenchidas';
        } else {
            $result = $recovery->setSecurityQuestions($_SESSION['user_id'], $questions);
            if ($result) {
                $message = 'Perguntas de segurança configuradas com sucesso!';
                $hasQuestions = true;
            } else {
                $error = 'Erro ao salvar perguntas de segurança';
            }
        }
    }
}

// Perguntas predefinidas
$predefinedQuestions = [
    'Qual é o nome da sua primeira escola?',
    'Qual é o nome do seu primeiro animal de estimação?',
    'Em que cidade você nasceu?',
    'Qual é o nome de solteira da sua mãe?',
    'Qual é o seu filme favorito?',
    'Qual é o nome da sua rua de infância?',
    'Qual é o seu livro favorito?',
    'Qual é o nome do seu melhor amigo de infância?',
    'Qual é a sua comida favorita?',
    'Qual é o modelo do seu primeiro carro?',
    'Qual é o nome da sua banda favorita?',
    'Em que ano você se formou no ensino médio?',
    'Qual é o seu hobby favorito?',
    'Qual é o nome da empresa onde você teve seu primeiro emprego?',
    'Qual é o seu número da sorte?'
];

include 'header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        Configurar Perguntas de Segurança
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
                    
                    <?php if ($hasQuestions): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Perguntas já configuradas!</strong> Você pode atualizá-las preenchendo o formulário abaixo.
                        </div>
                    <?php endif; ?>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Importante:</strong> Estas perguntas serão usadas para recuperar sua senha. 
                        Escolha perguntas que você sempre lembrará das respostas.
                    </div>
                    
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= SecurityHelper::generateCSRFToken() ?>">
                        
                        <!-- Pergunta 1 -->
                        <div class="mb-4">
                            <h5 class="text-primary">Pergunta de Segurança 1</h5>
                            <div class="mb-2">
                                <label for="question1" class="form-label">Pergunta:</label>
                                <select class="form-select" id="question1" name="question1" required>
                                    <option value="">Selecione uma pergunta...</option>
                                    <?php foreach ($predefinedQuestions as $q): ?>
                                        <option value="<?= SecurityHelper::escapeHtml($q) ?>"><?= SecurityHelper::escapeHtml($q) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="answer1" class="form-label">Resposta:</label>
                                <input type="text" class="form-control" id="answer1" name="answer1" required>
                                <small class="text-muted">Não diferencia maiúsculas de minúsculas</small>
                            </div>
                        </div>
                        
                        <!-- Pergunta 2 -->
                        <div class="mb-4">
                            <h5 class="text-primary">Pergunta de Segurança 2</h5>
                            <div class="mb-2">
                                <label for="question2" class="form-label">Pergunta:</label>
                                <select class="form-select" id="question2" name="question2" required>
                                    <option value="">Selecione uma pergunta...</option>
                                    <?php foreach ($predefinedQuestions as $q): ?>
                                        <option value="<?= SecurityHelper::escapeHtml($q) ?>"><?= SecurityHelper::escapeHtml($q) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="answer2" class="form-label">Resposta:</label>
                                <input type="text" class="form-control" id="answer2" name="answer2" required>
                                <small class="text-muted">Não diferencia maiúsculas de minúsculas</small>
                            </div>
                        </div>
                        
                        <!-- Pergunta 3 -->
                        <div class="mb-4">
                            <h5 class="text-primary">Pergunta de Segurança 3</h5>
                            <div class="mb-2">
                                <label for="question3" class="form-label">Pergunta:</label>
                                <select class="form-select" id="question3" name="question3" required>
                                    <option value="">Selecione uma pergunta...</option>
                                    <?php foreach ($predefinedQuestions as $q): ?>
                                        <option value="<?= SecurityHelper::escapeHtml($q) ?>"><?= SecurityHelper::escapeHtml($q) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="answer3" class="form-label">Resposta:</label>
                                <input type="text" class="form-control" id="answer3" name="answer3" required>
                                <small class="text-muted">Não diferencia maiúsculas de minúsculas</small>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fas fa-save me-2"></i>
                            <?= $hasQuestions ? 'Atualizar' : 'Configurar' ?> Perguntas de Segurança
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="dashboard.php" class="text-muted">
                            <i class="fas fa-arrow-left me-1"></i>
                            Voltar ao Dashboard
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Prevenir seleção da mesma pergunta
document.addEventListener('DOMContentLoaded', function() {
    const selects = ['question1', 'question2', 'question3'];
    
    selects.forEach(selectId => {
        document.getElementById(selectId).addEventListener('change', function() {
            const selectedValue = this.value;
            const otherSelects = selects.filter(id => id !== selectId);
            
            otherSelects.forEach(otherId => {
                const otherSelect = document.getElementById(otherId);
                const options = otherSelect.querySelectorAll('option');
                
                options.forEach(option => {
                    if (option.value === selectedValue && selectedValue !== '') {
                        option.disabled = true;
                        option.style.color = '#ccc';
                    } else {
                        option.disabled = false;
                        option.style.color = '';
                    }
                });
            });
        });
    });
});
</script>

<?php include 'footer.php'; ?>