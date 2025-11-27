<?php
require_once 'config.php';
require_once 'exercise_functions.php';

$title = 'Exercícios Interativos';

// Parâmetros
$category = sanitize($_GET['category'] ?? '');
$difficulty = sanitize($_GET['difficulty'] ?? '');
$exerciseId = (int)($_GET['id'] ?? 0);

// Se um exercício específico foi solicitado
if ($exerciseId > 0) {
    $exercise = getExercise($exerciseId);
    if (!$exercise) {
        header('Location: interactive_exercises.php');
        exit;
    }
}

include 'header.php';
?>

<div class="container mt-4">
    <?php if ($exerciseId > 0 && $exercise): ?>
        <!-- Exercício Individual -->
        <div class="exercise-container">
            <!-- Cabeçalho do Exercício -->
            <div class="card shadow-sm border mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h2 class="h4 mb-2 fw-bold">
                                <i class="fas fa-code me-2"></i>
                                <?php echo htmlspecialchars($exercise['title']); ?>
                            </h2>
                            <p class="text-muted mb-0"><?php echo htmlspecialchars($exercise['description']); ?></p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge bg-primary px-3 py-2">
                                <?php echo htmlspecialchars($exercise['category']); ?>
                            </span>
                            <span class="badge bg-warning px-3 py-2">
                                <?php echo htmlspecialchars($exercise['difficulty']); ?>
                            </span>
                            <span class="badge bg-info px-3 py-2">
                                <i class="fas fa-star me-1"></i><?php echo $exercise['points'] ?? 10; ?> pts
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-12 col-xl-8">
                    <!-- Editor de Código -->
                    <div class="card shadow-sm border mb-4">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-code me-2"></i>Editor de Código
                                </h6>
                                <div class="editor-controls d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="toggleFullscreen()">
                                        <i class="fas fa-expand"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="editor-toolbar d-flex justify-content-between align-items-center p-2 bg-light border-bottom">
                                <div class="d-flex gap-2">
                                    <span class="badge bg-secondary"><?php echo $exercise['category']; ?></span>
                                    <small class="text-muted">Linhas: <span id="lineCount">1</span></small>
                                </div>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-success" onclick="runCode()">
                                        <i class="fas fa-play"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" onclick="validateCode()">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="code-editor-container">
                                <textarea id="codeEditor" class="form-control border-0 code-textarea" rows="18" 
                                          placeholder="Digite seu código aqui..."><?php echo htmlspecialchars($exercise['initial_code'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                
                    <!-- Resultados em Tabs -->
                    <div class="card shadow-sm border">
                        <div class="card-header p-0">
                            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#output-pane" type="button">
                                        <i class="fas fa-terminal me-2"></i>Resultado
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#validation-pane" type="button">
                                        <i class="fas fa-check-circle me-2"></i>Validação
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="output-pane">
                                    <div id="output" class="output-panel">
                                        <div class="text-muted text-center py-5">
                                            <i class="fas fa-play-circle fa-3x mb-3 text-success"></i>
                                            <h6>Pronto para executar</h6>
                                            <p class="mb-0">Clique em "Executar" ou pressione Ctrl+Enter</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="validation-pane">
                                    <div id="validation" class="validation-panel">
                                        <div class="text-muted text-center py-5">
                                            <i class="fas fa-clipboard-check fa-3x mb-3 text-info"></i>
                                            <h6>Pronto para validar</h6>
                                            <p class="mb-0">Clique em "Validar" para verificar sua solução</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-xl-4">
                    <!-- Painel de Instruções -->
                    <div class="card shadow-sm border mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-list-ol me-2"></i>Instruções
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="instructions-content">
                                <?php echo nl2br(htmlspecialchars($exercise['instructions'] ?? '')); ?>
                            </div>
                            
                            <?php if (!empty($exercise['hints'])): ?>
                            <div class="hints-panel mt-4">
                                <h6 class="fw-bold text-warning mb-3">
                                    <i class="fas fa-lightbulb me-2"></i>Dicas:
                                </h6>
                                <div class="hints-content">
                                    <ul class="list-unstyled">
                                        <?php foreach ($exercise['hints'] as $hint): ?>
                                            <li class="mb-2">
                                                <i class="fas fa-arrow-right text-warning me-2"></i>
                                                <?php echo htmlspecialchars($hint); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                
                    <!-- Painel de Ações -->
                    <div class="card shadow-sm border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-tools me-2"></i>Ações
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-success" onclick="runCode()">
                                    <i class="fas fa-play me-1"></i>Executar
                                </button>
                                <button class="btn btn-primary" onclick="validateCode()">
                                    <i class="fas fa-check me-1"></i>Validar
                                </button>
                                <button class="btn btn-outline-secondary" onclick="resetCode()">
                                    <i class="fas fa-undo me-1"></i>Resetar
                                </button>
                                <button class="btn btn-outline-info" onclick="formatCode()">
                                    <i class="fas fa-magic me-1"></i>Formatar
                                </button>
                                
                                <?php if (isLoggedIn()): ?>
                                <button class="btn btn-outline-success" onclick="saveProgress()">
                                    <i class="fas fa-save me-2"></i>Salvar Progresso
                                </button>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mt-3 p-2 bg-light rounded">
                                <small class="text-muted d-block text-center">
                                    <i class="fas fa-keyboard me-1"></i>
                                    <strong>Atalhos:</strong> Ctrl+Enter (executar) • Ctrl+S (salvar)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    <?php else: ?>
        <!-- Cabeçalho -->
        <div class="exercises-header text-center mb-5">
            <h1 class="display-4 fw-bold mb-3">
                <i class="fas fa-dumbbell text-primary"></i>
                Exercícios Interativos
            </h1>
            <p class="lead text-muted">
                Pratique e aprimore suas habilidades com exercícios interativos
            </p>
            
            <div class="mt-4">
                <a href="interactive_exercises.php" class="btn btn-primary btn-lg me-3">
                    <i class="fas fa-laptop-code me-2"></i>Exercícios Interativos
                </a>
                <a href="exercises_index.php" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-list me-2"></i>Lista Completa
                </a>
            </div>
        </div>
        
        <!-- Filtros -->
        <div class="filters-card card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <form method="GET" action="" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="category" class="form-label fw-semibold">
                                <i class="fas fa-tag text-primary me-1"></i>
                                Categoria
                            </label>
                            <select class="form-select rounded-pill" id="category" name="category" onchange="this.form.submit()">
                                <option value="">Todas as categorias</option>
                                <option value="HTML" <?php echo ($category === 'HTML') ? 'selected' : ''; ?>>📄 HTML</option>
                                <option value="CSS" <?php echo ($category === 'CSS') ? 'selected' : ''; ?>>🎨 CSS</option>
                                <option value="JavaScript" <?php echo ($category === 'JavaScript') ? 'selected' : ''; ?>>⚡ JavaScript</option>
                                <option value="PHP" <?php echo ($category === 'PHP') ? 'selected' : ''; ?>>🐘 PHP</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="difficulty" class="form-label fw-semibold">
                                <i class="fas fa-signal text-warning me-1"></i>
                                Dificuldade
                            </label>
                            <select class="form-select rounded-pill" id="difficulty" name="difficulty" onchange="this.form.submit()">
                                <option value="">Todas as dificuldades</option>
                                <option value="Iniciante" <?php echo ($difficulty === 'Iniciante') ? 'selected' : ''; ?>>🌱 Iniciante</option>
                                <option value="Intermediário" <?php echo ($difficulty === 'Intermediário') ? 'selected' : ''; ?>>🌿 Intermediário</option>
                                <option value="Avançado" <?php echo ($difficulty === 'Avançado') ? 'selected' : ''; ?>>🌳 Avançado</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <?php if ($category || $difficulty): ?>
                                <a href="interactive_exercises.php" class="btn btn-outline-secondary w-100 rounded-pill">
                                    <i class="fas fa-redo me-2"></i>Limpar Filtros
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Lista de exercícios -->
        <div class="row">
            <?php 
            $exercises = getExercises($category, $difficulty, '', 1, 12);
            if (empty($exercises)): 
            ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <h4 class="alert-heading">Nenhum exercício encontrado!</h4>
                        <p>Tente ajustar os filtros ou <a href="interactive_exercises.php" class="alert-link">limpar a busca</a>.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($exercises as $exercise): 
                    $display_difficulty = $exercise['difficulty'] ?? 'Iniciante';
                    $category_ex = $exercise['category'] ?? 'Geral';
                    
                    // Verificar progresso do usuário
                    $completed = false;
                    if (isLoggedIn()) {
                        $user_id = getCurrentUser()['id'];
                        $conn = getDBConnection();
                        if ($conn) {
                            $stmt = $conn->prepare("SELECT status FROM user_progress WHERE user_id = ? AND exercise_id = ?");
                            $stmt->bind_param("ii", $user_id, $exercise['id']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();
                            $completed = $row && $row['status'] === 'completed';
                        }
                    }
                    
                    // Cores das categorias
                    $catColors = ['HTML' => 'danger', 'CSS' => 'primary', 'JavaScript' => 'warning', 'PHP' => 'info'];
                    $catColor = $catColors[$category_ex] ?? 'secondary';
                    
                    // Cores das dificuldades
                    $levelMap = ['Iniciante' => 'success', 'Intermediário' => 'warning', 'Avançado' => 'danger'];
                    $levelColor = $levelMap[$display_difficulty] ?? 'secondary';
                ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card exercise-card h-100 shadow-sm border-0 <?php echo $completed ? 'completed' : ''; ?>">
                            <?php if ($completed): ?>
                                <div class="completed-badge">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-header bg-white border-0 pt-3 pb-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge rounded-pill bg-<?php echo $catColor; ?> px-3 py-2">
                                        <i class="fas fa-code me-1"></i>
                                        <?php echo htmlspecialchars($category_ex); ?>
                                    </span>
                                    <span class="badge rounded-pill bg-<?php echo $levelColor; ?> px-3 py-2">
                                        <i class="fas fa-signal me-1"></i>
                                        <?php echo htmlspecialchars($display_difficulty); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-body d-flex flex-column pt-2">
                                <h3 class="card-title h5 fw-bold mb-3">
                                    <?php echo htmlspecialchars($exercise['title'] ?? 'Exercício'); ?>
                                </h3>
                                <p class="card-text text-muted flex-grow-1 mb-3">
                                    <?php echo htmlspecialchars($exercise['description'] ?? 'Descrição do exercício'); ?>
                                </p>
                            </div>
                            
                            <div class="card-footer bg-white border-0 pt-0 pb-3">
                                <div class="d-flex gap-2">
                                    <a href="?id=<?php echo $exercise['id'] ?? 1; ?>" 
                                       class="btn btn-<?php echo $completed ? 'outline-primary' : 'primary'; ?> flex-fill rounded-pill">
                                        <i class="fas fa-laptop-code me-1"></i>
                                        <?php echo $completed ? 'Revisar' : 'Interativo'; ?>
                                    </a>
                                    <?php if (isLoggedIn() && !$completed): ?>
                                    <button onclick="quickComplete(<?php echo $exercise['id'] ?? 1; ?>)" 
                                            class="btn btn-outline-success rounded-pill px-3" 
                                            title="Marcar como concluído">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// JavaScript simplificado - mantendo apenas o essencial
function runCode() {
    const code = document.getElementById('codeEditor').value;
    const output = document.getElementById('output');
    
    output.innerHTML = `
        <div class="alert alert-info">
            <strong>Executando código...</strong>
            <pre class="mt-2">${code}</pre>
        </div>`;
}

function validateCode() {
    const code = document.getElementById('codeEditor').value;
    const validation = document.getElementById('validation');
    
    if (!code.trim()) {
        validation.innerHTML = '<div class="alert alert-warning">Digite algum código primeiro.</div>';
        return;
    }
    
    validation.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Validando...</div>';
    
    const formData = new FormData();
    formData.append('validate', '1');
    formData.append('exercise_id', <?php echo $exerciseId; ?>);
    formData.append('user_code', code);
    
    fetch('exercise_validator.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        displayValidationResults(data);
        if (data.success && data.percentage >= 70) {
            updateProgress(<?php echo $exerciseId; ?>, data.percentage);
        }
    })
    .catch(error => {
        validation.innerHTML = '<div class="alert alert-danger">Erro ao validar. Tente novamente.</div>';
    });
}

function displayValidationResults(data) {
    const validation = document.getElementById('validation');
    
    let html = `
        <div class="alert alert-${data.success ? 'success' : 'warning'} mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <strong>${data.message}</strong>
                <span class="badge bg-${data.success ? 'success' : 'warning'} fs-6">
                    ${data.percentage || 0}%
                </span>
            </div>
        </div>
    `;
    
    if (data.tests) {
        html += '<div class="tests-results">';
        html += '<h6 class="fw-bold mb-3"><i class="fas fa-list-check me-2"></i>Testes:</h6>';
        
        for (const [testName, passed] of Object.entries(data.tests)) {
            html += `
                <div class="test-item d-flex justify-content-between align-items-center mb-2 p-2 rounded" 
                     style="background: ${passed ? '#d4edda' : '#f8d7da'}">
                    <span>${testName}</span>
                    <i class="fas fa-${passed ? 'check text-success' : 'times text-danger'}"></i>
                </div>
            `;
        }
        
        html += '</div>';
        
        if (data.success) {
            html += `
                <div class="mt-3 text-center">
                    <button class="btn btn-success me-2" onclick="completeExercise()">
                        <i class="fas fa-trophy me-2"></i>Marcar como Concluído
                    </button>
                    <button class="btn btn-outline-primary" onclick="validateRequiredCommands()">
                        <i class="fas fa-check-circle me-2"></i>Verificar Comandos
                    </button>
                </div>
            `;
        } else {
            html += `
                <div class="mt-3 text-center">
                    <button class="btn btn-outline-primary" onclick="validateRequiredCommands()">
                        <i class="fas fa-search me-2"></i>Verificar Comandos Obrigatórios
                    </button>
                </div>
            `;
        }
    }
    
    validation.innerHTML = html;
}

function updateProgress(exerciseId, score) {
    <?php if (isLoggedIn()): ?>
    fetch('api/update_progress.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            exercise_id: exerciseId,
            score: score,
            status: score >= 70 ? 'completed' : 'in_progress'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Progresso atualizado!', 'success');
            if (data.new_achievements) {
                showAchievements(data.new_achievements);
            }
        }
    })
    .catch(error => console.error('Erro ao atualizar progresso:', error));
    <?php endif; ?>
}

function completeExercise() {
    <?php if (isLoggedIn()): ?>
    const userCode = document.getElementById('codeEditor').value;
    
    // Verificar comandos obrigatórios
    const missingCommands = checkRequiredCommands(userCode);
    if (missingCommands.length > 0) {
        showMissingCommandsAlert(missingCommands);
        return;
    }
    
    fetch('execute_exercise.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            exercise_id: <?php echo $exerciseId; ?>,
            user_code: userCode,
            completed: true
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Exercício concluído! +' + (data.coins_earned || 0) + ' moedas', 'success');
            if (data.new_achievements) {
                showAchievements(data.new_achievements);
            }
            setTimeout(() => location.reload(), 2000);
        } else {
            showToast('Erro: ' + (data.message || 'Falha ao completar exercício'), 'danger');
        }
    })
    .catch(error => showToast('Erro ao completar exercício', 'danger'));
    <?php else: ?>
    showToast('Faça login para marcar exercícios como concluídos', 'warning');
    <?php endif; ?>
}

function checkRequiredCommands(code) {
    const exerciseId = <?php echo $exerciseId; ?>;
    const requiredCommands = getRequiredCommandsForExercise(exerciseId);
    const missingCommands = [];
    
    requiredCommands.forEach(command => {
        if (!code.includes(command.keyword)) {
            missingCommands.push(command);
        }
    });
    
    return missingCommands;
}

function getRequiredCommandsForExercise(exerciseId) {
    const requirements = {
        1: [{ keyword: 'echo', description: 'comando echo para exibir texto' }],
        2: [{ keyword: '$', description: 'variável PHP (deve começar com $)' }],
        3: [{ keyword: 'if', description: 'estrutura condicional if' }],
        4: [{ keyword: 'for', description: 'loop for' }, { keyword: 'echo', description: 'comando echo' }],
        5: [{ keyword: 'while', description: 'loop while' }],
        6: [{ keyword: 'function', description: 'declaração de função' }],
        7: [{ keyword: 'array', description: 'criação de array' }],
        8: [{ keyword: 'foreach', description: 'loop foreach' }],
        9: [{ keyword: 'class', description: 'declaração de classe' }],
        10: [{ keyword: 'new', description: 'instanciação de objeto' }],
        11: [{ keyword: 'include', description: 'inclusão de arquivo' }],
        12: [{ keyword: 'try', description: 'bloco try-catch' }, { keyword: 'catch', description: 'bloco catch' }],
        13: [{ keyword: 'SELECT', description: 'comando SQL SELECT' }],
        14: [{ keyword: 'INSERT', description: 'comando SQL INSERT' }],
        15: [{ keyword: 'UPDATE', description: 'comando SQL UPDATE' }],
        16: [{ keyword: 'DELETE', description: 'comando SQL DELETE' }],
        17: [{ keyword: 'JOIN', description: 'comando SQL JOIN' }],
        18: [{ keyword: 'session_start', description: 'inicialização de sessão' }],
        19: [{ keyword: '$_POST', description: 'superglobal $_POST' }],
        20: [{ keyword: '$_GET', description: 'superglobal $_GET' }],
        21: [{ keyword: 'filter_var', description: 'validação com filter_var' }],
        22: [{ keyword: 'preg_match', description: 'expressão regular com preg_match' }],
        23: [{ keyword: 'hash', description: 'função de hash' }],
        24: [{ keyword: 'file_get_contents', description: 'leitura de arquivo' }],
        25: [{ keyword: 'fopen', description: 'abertura de arquivo' }, { keyword: 'fwrite', description: 'escrita em arquivo' }],
        26: [{ keyword: 'curl_init', description: 'inicialização cURL' }],
        27: [{ keyword: 'json_encode', description: 'codificação JSON' }],
        28: [{ keyword: 'json_decode', description: 'decodificação JSON' }],
        29: [{ keyword: 'mail', description: 'função mail' }],
        30: [{ keyword: 'date', description: 'função date' }],
        31: [{ keyword: 'strtotime', description: 'função strtotime' }],
        32: [{ keyword: 'explode', description: 'função explode' }],
        33: [{ keyword: 'implode', description: 'função implode' }],
        34: [{ keyword: 'array_map', description: 'função array_map' }],
        35: [{ keyword: 'array_filter', description: 'função array_filter' }],
        36: [{ keyword: 'sort', description: 'função de ordenação' }]
    };
    
    return requirements[exerciseId] || [];
}

function showMissingCommandsAlert(missingCommands) {
    const commandsList = missingCommands.map(cmd => `• ${cmd.description}`).join('\n');
    const message = `⚠️ Comandos obrigatórios não encontrados:\n\n${commandsList}\n\nAdicione estes comandos ao seu código antes de completar o exercício.`;
    
    // Criar modal de alerta personalizado
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Comandos Obrigatórios Faltando
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Para completar este exercício, você precisa usar os seguintes comandos:</p>
                    <ul class="list-unstyled">
                        ${missingCommands.map(cmd => `<li class="mb-2"><i class="fas fa-code text-warning me-2"></i><strong>${cmd.description}</strong></li>`).join('')}
                    </ul>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Dica:</strong> Adicione estes comandos ao seu código e tente novamente.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        <i class="fas fa-code me-2"></i>Continuar Codificando
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    modal.addEventListener('hidden.bs.modal', () => {
        document.body.removeChild(modal);
    });
}

function validateRequiredCommands() {
    const userCode = document.getElementById('codeEditor').value;
    const missingCommands = checkRequiredCommands(userCode);
    
    if (missingCommands.length === 0) {
        showToast('✅ Todos os comandos obrigatórios foram encontrados!', 'success');
    } else {
        showMissingCommandsAlert(missingCommands);
    }
}

function showAchievements(achievements) {
    achievements.forEach(achievement => {
        showToast(`🏆 Nova conquista: ${achievement.name}!`, 'success');
    });
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px; border-radius: 8px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 4000);
}

function quickComplete(exerciseId) {
    if (!confirm('Marcar este exercício como concluído?')) return;
    
    fetch('execute_exercise.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            exercise_id: exerciseId,
            user_code: '// Marcado como concluído',
            quick_complete: true
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Exercício concluído! +' + (data.coins_earned || 0) + ' moedas', 'success');
            if (data.new_achievements) {
                showAchievements(data.new_achievements);
            }
            setTimeout(() => location.reload(), 2000);
        } else {
            showToast('Erro: ' + data.message, 'danger');
        }
    })
    .catch(error => showToast('Erro de conexão', 'danger'));
}

function resetCode() {
    if (confirm('Tem certeza que deseja resetar o código?')) {
        document.getElementById('codeEditor').value = '<?php echo addslashes($exercise['initial_code'] ?? ''); ?>';
    }
}

function formatCode() {
    alert('Formatação em desenvolvimento...');
}

function saveProgress() {
    alert('Faça login para salvar seu progresso.');
}

function toggleFullscreen() {
    const textarea = document.getElementById('codeEditor');
    if (textarea.requestFullscreen) {
        textarea.requestFullscreen();
    }
}

// Contador de linhas
document.addEventListener('DOMContentLoaded', function() {
    const codeEditor = document.getElementById('codeEditor');
    const lineCount = document.getElementById('lineCount');
    
    if (codeEditor && lineCount) {
        codeEditor.addEventListener('input', function() {
            const lines = this.value.split('\n').length;
            lineCount.textContent = lines;
        });
        
        // Tab para indentação
        codeEditor.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                e.preventDefault();
                const start = this.selectionStart;
                const end = this.selectionEnd;
                this.value = this.value.substring(0, start) + '  ' + this.value.substring(end);
                this.selectionStart = this.selectionEnd = start + 2;
            }
        });
    }
});
</script>

<style>
/* Cabeçalho da página */
.exercises-header {
    padding: 2rem 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    margin: -1.5rem -15px 2rem -15px;
    color: white;
    border-radius: 0 0 20px 20px;
}

.exercises-header .display-4 {
    font-size: 2.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    color: #1a1a1a !important;
    font-weight: 700 !important;
}

.exercises-header .lead {
    color: #ffffffff !important;
    font-size: 1.1rem;
    font-weight: 600 !important;
}

/* Card de filtros */
.filters-card {
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.filters-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.filters-card .form-select {
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.filters-card .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
}

/* Cards de exercícios */
.exercise-card {
    border-radius: 15px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    background: white;
}

.exercise-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15) !important;
}

.exercise-card.completed {
    border: 2px solid #28a745;
    background: linear-gradient(135deg, #fff 0%, #f0fff4 100%);
}

.completed-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #28a745;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    z-index: 10;
    animation: bounceIn 0.5s ease;
}

@keyframes bounceIn {
    0% { transform: scale(0); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.exercise-card .badge {
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.3px;
}

.exercise-card .card-title {
    color: #2d3748;
    line-height: 1.4;
}

.exercise-card .card-text {
    font-size: 0.9rem;
    line-height: 1.6;
    color: #6c757d !important;
}

.exercise-card .btn {
    font-weight: 600;
    transition: all 0.3s ease;
}

.exercise-card .btn:hover {
    transform: scale(1.05);
}

/* Editor e painéis */
.exercise-container {
    max-width: 1400px;
    margin: 0 auto;
}

.code-textarea {
    font-family: 'Courier New', monospace;
    font-size: 14px;
    line-height: 1.5;
    resize: vertical;
    background: #f8f9fa;
    border: none;
    min-height: 300px;
    width: 100%;
    padding: 1rem;
}

.code-textarea:focus {
    outline: none;
    background: #fff;
}

.output-panel, .validation-panel {
    min-height: 200px;
    background: #f8f9fa;
    border-radius: 4px;
    padding: 1rem;
}

.nav-tabs .nav-link {
    border-radius: 4px 4px 0 0;
    border: none;
    color: #6c757d;
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    background: white;
    color: #007bff;
    border-bottom: 2px solid #007bff;
}

.instructions-content {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 4px;
    border-left: 4px solid #007bff;
    line-height: 1.6;
}

/* Responsividade */
@media (max-width: 768px) {
    .exercises-header .display-4 {
        font-size: 2rem;
    }
    
    .code-textarea {
        min-height: 200px;
        font-size: 12px;
    }
    
    .filters-card .row > div {
        margin-bottom: 0.5rem;
    }
}
</style>

<?php include 'footer.php'; ?>