<?php
require_once 'config.php';

$exercise_id = (int)($_GET['id'] ?? 1);
$title = 'Área de Exercício';

// Buscar dados do exercício
$conn = getDBConnection();
$exercise = null;
$user_progress = null;

if ($conn) {
    $stmt = $conn->prepare("SELECT e.*, c.name as category FROM exercises e LEFT JOIN categories c ON e.category_id = c.id WHERE e.id = ?");
    $stmt->bind_param("i", $exercise_id);
    $stmt->execute();
    $exercise = $stmt->get_result()->fetch_assoc();
    
    // Se usuário logado, buscar progresso
    if (isLoggedIn()) {
        $user_id = getCurrentUser()['id'];
        $stmt = $conn->prepare("SELECT * FROM exercise_progress WHERE user_id = ? AND exercise_id = ?");
        $stmt->bind_param("ii", $user_id, $exercise_id);
        $stmt->execute();
        $user_progress = $stmt->get_result()->fetch_assoc();
    }
}

include 'header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2><?php echo htmlspecialchars($exercise['title'] ?? 'Exercício'); ?></h2>
                    <?php if (isLoggedIn()): ?>
                        <div class="user-status">
                            <span class="badge bg-success">
                                <i class="fas fa-user"></i> Logado como <?php echo htmlspecialchars(getCurrentUser()['first_name']); ?>
                            </span>
                        </div>
                    <?php else: ?>
                        <div class="user-status">
                            <span class="badge bg-warning">
                                <i class="fas fa-exclamation-triangle"></i> Não logado
                            </span>
                            <a href="login.php" class="btn btn-sm btn-primary ms-2">Fazer Login</a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="card-body">
                    <p><?php echo htmlspecialchars($exercise['description'] ?? 'Descrição do exercício'); ?></p>
                    
                    <?php if ($user_progress): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Status: <strong><?php echo $user_progress['status'] === 'completed' ? 'Concluído' : 'Em progresso'; ?></strong>
                            <?php if ($user_progress['score'] > 0): ?>
                                | Score: <strong><?php echo $user_progress['score']; ?></strong>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="exercise-content">
                        <h4>Código:</h4>
                        <textarea id="codeEditor" class="form-control" rows="10" placeholder="Digite seu código aqui..."><?php 
                            if ($user_progress && $user_progress['status'] === 'completed') {
                                echo "// Exercício já concluído!\n// Você pode revisar ou tentar novamente.";
                            } else {
                                echo "// Escreva seu código aqui\n";
                            }
                        ?></textarea>
                        
                        <div class="mt-3">
                            <?php if (isLoggedIn()): ?>
                                <button id="runCode" class="btn btn-primary">
                                    <i class="fas fa-play"></i> Executar
                                </button>
                                <button id="submitCode" class="btn btn-success">
                                    <i class="fas fa-check"></i> Submeter
                                </button>
                                <?php if (!$user_progress || $user_progress['status'] !== 'completed'): ?>
                                    <button id="markComplete" class="btn btn-warning">
                                        <i class="fas fa-flag-checkered"></i> Marcar como Concluído
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-lock"></i> 
                                    <strong>Faça login</strong> para salvar seu progresso e ganhar conquistas!
                                    <a href="login.php" class="btn btn-sm btn-primary ms-2">Login</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div id="output" class="mt-3" style="display: none;">
                        <h5>Resultado:</h5>
                        <pre id="outputContent" class="bg-dark text-light p-3 rounded"></pre>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <?php if (isLoggedIn()): ?>
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-line"></i> Seu Progresso</h5>
                    </div>
                    <div class="card-body" id="progressCard">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="fas fa-trophy"></i> Conquistas</h5>
                    </div>
                    <div class="card-body" id="achievementsCard">
                        <p class="text-muted">Complete exercícios para desbloquear conquistas!</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-user-plus fa-3x text-muted mb-3"></i>
                        <h5>Crie sua conta</h5>
                        <p class="text-muted">Faça login para:</p>
                        <ul class="list-unstyled text-start">
                            <li><i class="fas fa-check text-success"></i> Salvar progresso</li>
                            <li><i class="fas fa-check text-success"></i> Ganhar conquistas</li>
                            <li><i class="fas fa-check text-success"></i> Acompanhar estatísticas</li>
                        </ul>
                        <a href="login.php" class="btn btn-primary">Fazer Login</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
const exerciseId = <?php echo $exercise_id; ?>;
const isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;

document.addEventListener('DOMContentLoaded', function() {
    if (isLoggedIn) {
        loadProgress();
        startExercise();
    }
    
    document.getElementById('runCode')?.addEventListener('click', runCode);
    document.getElementById('submitCode')?.addEventListener('click', submitCode);
    document.getElementById('markComplete')?.addEventListener('click', markComplete);
});

function startExercise() {
    fetch('exercise_handler.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=start&exercise_id=' + exerciseId
    });
}

function runCode() {
    const code = document.getElementById('codeEditor').value;
    const output = document.getElementById('output');
    const outputContent = document.getElementById('outputContent');
    
    // Simular execução
    outputContent.textContent = 'Código executado:\n' + code + '\n\nResultado: OK';
    output.style.display = 'block';
}

function submitCode() {
    const code = document.getElementById('codeEditor').value;
    if (!code.trim()) {
        alert('Digite algum código primeiro!');
        return;
    }
    
    // Simular avaliação e dar score baseado no tamanho do código
    const score = Math.min(Math.max(code.length / 10, 5), 20);
    
    fetch('exercise_handler.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=complete&exercise_id=' + exerciseId + '&score=' + Math.round(score)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Exercício submetido! Score: ' + data.score);
            if (data.achievements.length > 0) {
                alert('Parabéns! Novas conquistas: ' + data.achievements.join(', '));
            }
            loadProgress();
            location.reload();
        }
    });
}

function markComplete() {
    if (!confirm('Marcar exercício como concluído?')) return;
    
    fetch('exercise_handler.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=complete&exercise_id=' + exerciseId + '&score=10'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Exercício concluído!');
            if (data.achievements.length > 0) {
                alert('Novas conquistas: ' + data.achievements.join(', '));
            }
            location.reload();
        }
    });
}

function loadProgress() {
    fetch('exercise_handler.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get_progress&exercise_id=' + exerciseId
    })
    .then(response => response.json())
    .then(data => {
        const progressCard = document.getElementById('progressCard');
        if (data.progress) {
            progressCard.innerHTML = `
                <p><strong>Status:</strong> ${data.progress.status === 'completed' ? 'Concluído' : 'Em progresso'}</p>
                <p><strong>Score:</strong> ${data.progress.score || 0}</p>
                ${data.progress.completed_at ? '<p><strong>Concluído em:</strong> ' + new Date(data.progress.completed_at).toLocaleDateString() + '</p>' : ''}
            `;
        } else {
            progressCard.innerHTML = '<p class="text-muted">Nenhum progresso ainda</p>';
        }
    });
}
</script>

<?php include 'footer.php'; ?>