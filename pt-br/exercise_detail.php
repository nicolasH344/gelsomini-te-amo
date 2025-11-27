<?php
session_start();

require_once 'config.php';
require_once 'exercise_functions.php';
require_once 'learning_system.php';

// Validações iniciais
$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('exercises_index.php');

// Buscar exercício com metodologia
$exercise = getExercise($id);
if (!$exercise) redirect('exercises_index.php');

// Inicializar sistema de aprendizado
$learningSystem = null;
try {
    require_once 'database.php';
    $db = new Database();
    $learningSystem = new LearningSystem($db);
    
    if (isLoggedIn()) {
        $user_id = getCurrentUser()['id'];
        $exerciseMethodology = $learningSystem->getExerciseWithMethodology($id, $user_id);
        if ($exerciseMethodology) {
            $exercise = array_merge($exercise, $exerciseMethodology);
        }
    }
} catch (Exception $e) {
    // Continuar sem sistema adaptativo
}

$title = $exercise['title'] ?? 'Exercício';

// Processar submissão de código
$execution_result = null;
$test_results = [];

// Obter código salvo ou usar padrão
$user_code = isset($_SESSION['exercise_code_' . $id])
    ? $_SESSION['exercise_code_' . $id]
    : ($exercise['initial_code'] ?? '// Comece aqui...\n');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_code'])) {
        $user_code = $_POST['user_code'] ?? '';
        
        // Simular execução e testes
        $execution_result = [
            'success' => true,
            'output' => "✓ Código executado com sucesso!\n✓ Todos os testes passaram!",
            'execution_time' => '0.45s',
            'memory_used' => '2.1MB'
        ];
        
        $test_results = [
            ['name' => 'Teste de Sintaxe', 'passed' => true, 'message' => 'Código compilou sem erros'],
            ['name' => 'Teste de Saída', 'passed' => true, 'message' => 'Saída esperada encontrada'],
            ['name' => 'Teste de Performance', 'passed' => true, 'message' => 'Dentro dos limites de tempo'],
            ['name' => 'Teste de Casos Extremos', 'passed' => false, 'message' => 'Falha em caso de entrada vazia']
        ];
        
        $_SESSION['success'] = 'Código executado com sucesso!';
    }
    
    if (isset($_POST['reset_code'])) {
        $user_code = $exercise['initial_code'] ?? '';
    }
    
    if (isset($_POST['save_progress'])) {
        $_SESSION['success'] = 'Progresso salvo com sucesso!';
    }
}

// Dados simulados para estatísticas
$exercise_stats = [
    'attempts' => 1247,
    'success_rate' => 68,
    'avg_completion_time' => '15min',
    'popularity' => 4.5,
    'user_attempts' => 3,
    'user_best_time' => '12min'
];

// Configuração de linguagens
$language_config = [
    'javascript' => [
        'icon' => 'fa-js-square',
        'color' => '#f7df1e',
        'name' => 'JavaScript'
    ],
    'html' => [
        'icon' => 'fa-html5',
        'color' => '#e34f26',
        'name' => 'HTML5'
    ],
    'css' => [
        'icon' => 'fa-css3-alt',
        'color' => '#1572b6',
        'name' => 'CSS3'
    ],
    'php' => [
        'icon' => 'fa-php',
        'color' => '#777bb4',
        'name' => 'PHP'
    ]
];

// Exercícios relacionados por linguagem
$related_exercises_by_language = [
    'javascript' => [
        [
            'id' => 2,
            'title' => 'Manipulação de Arrays',
            'difficulty' => 'beginner',
            'language' => 'javascript',
            'completion_rate' => 72
        ],
        [
            'id' => 3,
            'title' => 'Funções Assíncronas',
            'difficulty' => 'intermediate',
            'language' => 'javascript',
            'completion_rate' => 45
        ],
        [
            'id' => 4,
            'title' => 'Manipulação de DOM',
            'difficulty' => 'beginner',
            'language' => 'javascript',
            'completion_rate' => 81
        ]
    ],
    'html' => [
        [
            'id' => 5,
            'title' => 'Formulários Avançados',
            'difficulty' => 'intermediate',
            'language' => 'html',
            'completion_rate' => 68
        ],
        [
            'id' => 6,
            'title' => 'Tabelas Responsivas',
            'difficulty' => 'beginner',
            'language' => 'html',
            'completion_rate' => 82
        ],
        [
            'id' => 7,
            'title' => 'Estrutura Semântica',
            'difficulty' => 'intermediate',
            'language' => 'html',
            'completion_rate' => 59
        ]
    ],
    'css' => [
        [
            'id' => 8,
            'title' => 'Flexbox Layout',
            'difficulty' => 'beginner',
            'language' => 'css',
            'completion_rate' => 76
        ],
        [
            'id' => 9,
            'title' => 'Grid System',
            'difficulty' => 'intermediate',
            'language' => 'css',
            'completion_rate' => 54
        ],
        [
            'id' => 10,
            'title' => 'Animações CSS',
            'difficulty' => 'advanced',
            'language' => 'css',
            'completion_rate' => 41
        ]
    ],
    'php' => [
        [
            'id' => 11,
            'title' => 'Validação de Dados',
            'difficulty' => 'beginner',
            'language' => 'php',
            'completion_rate' => 70
        ],
        [
            'id' => 12,
            'title' => 'Conexão com Banco',
            'difficulty' => 'intermediate',
            'language' => 'php',
            'completion_rate' => 48
        ],
        [
            'id' => 13,
            'title' => 'Upload de Arquivos',
            'difficulty' => 'advanced',
            'language' => 'php',
            'completion_rate' => 35
        ]
    ]
];

// Dicas por linguagem
$hints_by_language = [
    'javascript' => [
        'Use console.log() para debugar seu código e ver os valores das variáveis',
        'Lembre-se que arrays começam no índice 0',
        'Funções podem retornar valores usando a palavra-chave return',
        'Use let e const ao invés de var para declarar variáveis'
    ],
    'html' => [
        'Use tags semânticas como <header>, <nav>, <main> e <footer>',
        'Sempre adicione atributos alt em imagens para acessibilidade',
        'Use IDs para elementos únicos e classes para estilos reutilizáveis',
        'Organize seu código com indentação adequada'
    ],
    'css' => [
        'Use Flexbox ou Grid para layouts responsivos modernos',
        'Evite usar !important, organize melhor a especificidade',
        'Use variáveis CSS (--nome-variavel) para cores e medidas',
        'Mobile-first: comece com estilos mobile e use media queries para desktop'
    ],
    'php' => [
        'Sempre valide e sanitize dados de entrada do usuário',
        'Use prepared statements para prevenir SQL injection',
        'Não exiba erros em produção, use logs ao invés',
        'Use password_hash() e password_verify() para senhas'
    ]
];

// Detectar linguagem do exercício atual
// Mapeamento direto por ID de exercício (enquanto o banco não tem o campo correto)
$exercise_language_map = [
    1 => 'html',      // Minha Primeira Página HTML
    2 => 'html',      // Lista de Compras
    3 => 'html',      // Formulário de Contato
    4 => 'css',       // Estilizando Texto
    5 => 'css',       // Layout com Flexbox
    6 => 'javascript', // Olá Mundo JavaScript
    7 => 'javascript', // Calculadora Simples
    8 => 'javascript', // Manipulação de Array
    9 => 'php',       // Olá Mundo PHP
];

// Tentar detectar pela ID primeiro
$exercise_id = (int)($exercise['id'] ?? 0);
$current_language = $exercise_language_map[$exercise_id] ?? null;

// Se não encontrou por ID, tentar por category_name
if (!$current_language) {
    $category_raw = strtolower(trim($exercise['category_name'] ?? ''));
    
    // Mapear variações de nomes para as chaves padrão
    $language_mapping = [
        'javascript' => 'javascript',
        'js' => 'javascript',
        'html' => 'html',
        'html5' => 'html',
        'css' => 'css',
        'css3' => 'css',
        'php' => 'php',
        'php7' => 'php',
        'php8' => 'php',
        'python' => 'python',
        'java' => 'java'
    ];
    
    // Detectar por palavra-chave no nome da categoria
    foreach ($language_mapping as $key => $value) {
        if (strpos($category_raw, $key) !== false) {
            $current_language = $value;
            break;
        }
    }
}

// Se ainda não detectou, tentar pelo exercise_type (dados mockados)
if (!$current_language && !empty($exercise['exercise_type'])) {
    $exercise_type_raw = strtolower(trim($exercise['exercise_type']));
    $language_mapping = [
        'javascript' => 'javascript',
        'js' => 'javascript',
        'html' => 'html',
        'html5' => 'html',
        'css' => 'css',
        'css3' => 'css',
        'php' => 'php',
        'python' => 'python',
        'java' => 'java'
    ];
    
    foreach ($language_mapping as $key => $value) {
        if (strpos($exercise_type_raw, $key) !== false) {
            $current_language = $value;
            break;
        }
    }
}

// Fallback final para javascript
$current_language = $current_language ?? 'javascript';

// Selecionar exercícios e dicas da linguagem atual
$related_exercises = $related_exercises_by_language[$current_language] ?? $related_exercises_by_language['javascript'];
$current_hints = $hints_by_language[$current_language] ?? $hints_by_language['javascript'];
$current_lang_config = $language_config[$current_language] ?? $language_config['javascript'];

include 'header.php';
?>
<div class="container-fluid mt-4">
    <!-- Alertas -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Conteúdo Principal -->
        <div class="col-lg-8">
            <!-- Cabeçalho do Exercício -->
            <div class="exercise-header-card mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-2">
                            <span class="exercise-type-badge me-2">
                                <i class="fas fa-code me-1"></i>
                                <?php echo strtoupper($exercise['exercise_type'] ?? 'PRÁTICA'); ?>
                            </span>
                            <span class="difficulty-badge difficulty-<?php echo $exercise['difficulty_level'] ?? 'beginner'; ?>">
                                <?php 
                                $difficulty_map = [
                                    'beginner' => 'Iniciante', 
                                    'intermediate' => 'Intermediário', 
                                    'advanced' => 'Avançado'
                                ];
                                $difficulty_level = $exercise['difficulty_level'] ?? 'beginner';
                                echo $difficulty_map[$difficulty_level] ?? 'Iniciante';
                                ?>
                            </span>
                        </div>
                        <h1 class="display-5 fw-bold mb-3 exercise-title"><?php echo sanitize($exercise['title'] ?? 'Exercício'); ?></h1>
                        <p class="lead exercise-description mb-3"><?php echo sanitize($exercise['description'] ?? 'Descrição do exercício'); ?></p>
                        
                        <!-- Metadados -->
                        <div class="metadata-grid">
                            <div class="metadata-card metadata-category">
                                <div class="metadata-icon" style="color: <?php echo $current_lang_config['color']; ?>">
                                    <i class="fab <?php echo $current_lang_config['icon']; ?>"></i>
                                </div>
                                <div class="metadata-info">
                                    <span class="metadata-label">Linguagem</span>
                                    <span class="metadata-value"><?php echo $current_lang_config['name']; ?></span>
                                </div>
                            </div>
                            <div class="metadata-card metadata-time">
                                <div class="metadata-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="metadata-info">
                                    <span class="metadata-label">Tempo Médio</span>
                                    <span class="metadata-value"><?php echo $exercise_stats['avg_completion_time']; ?></span>
                                </div>
                            </div>
                            <div class="metadata-card metadata-success">
                                <div class="metadata-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="metadata-info">
                                    <span class="metadata-label">Taxa de Sucesso</span>
                                    <span class="metadata-value"><?php echo $exercise_stats['success_rate']; ?>%</span>
                                </div>
                            </div>
                            <div class="metadata-card metadata-attempts">
                                <div class="metadata-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="metadata-info">
                                    <span class="metadata-label">Tentativas</span>
                                    <span class="metadata-value"><?php echo number_format($exercise_stats['attempts'], 0, ',', '.'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="progress-ring-container">
                            <div class="progress-ring">
                                <svg width="120" height="120" viewBox="0 0 120 120">
                                    <circle class="progress-ring-bg" 
                                            stroke="rgba(255,255,255,0.2)" 
                                            stroke-width="8" 
                                            fill="transparent" 
                                            r="52" 
                                            cx="60" 
                                            cy="60">
                                    </circle>
                                    <circle class="progress-ring-circle" 
                                            stroke="url(#gradient)" 
                                            stroke-width="8" 
                                            fill="transparent" 
                                            r="52" 
                                            cx="60" 
                                            cy="60"
                                            stroke-dasharray="326.56" 
                                            stroke-dashoffset="<?php echo 326.56 * (1 - $exercise_stats['success_rate'] / 100); ?>"
                                            stroke-linecap="round">
                                    </circle>
                                    <defs>
                                        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" style="stop-color:#06d6a0;stop-opacity:1" />
                                            <stop offset="100%" style="stop-color:#1b9aaa;stop-opacity:1" />
                                        </linearGradient>
                                    </defs>
                                </svg>
                                <div class="progress-text">
                                    <span class="progress-percentage"><?php echo $exercise_stats['success_rate']; ?>%</span>
                                    <small class="progress-label">Taxa de Sucesso</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Área de Trabalho -->
            <div class="workspace-container">
                <!-- Navegação por Abas -->
                <ul class="nav nav-tabs workspace-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="instructions-tab" data-bs-toggle="tab" data-bs-target="#instructions" type="button" role="tab" aria-controls="instructions" aria-selected="true">
                            <i class="fas fa-book-open me-2"></i>
                            <span>Instruções</span>
                            <div class="tab-indicator"></div>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="editor-tab" data-bs-toggle="tab" data-bs-target="#editor" type="button" role="tab" aria-controls="editor" aria-selected="false">
                            <i class="fas fa-code me-2"></i>
                            <span>Editor</span>
                            <div class="tab-indicator"></div>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="output-tab" data-bs-toggle="tab" data-bs-target="#output" type="button" role="tab" aria-controls="output" aria-selected="false">
                            <i class="fas fa-terminal me-2"></i>
                            <span>Resultado</span>
                            <?php if ($execution_result): ?>
                                <span class="result-badge badge-<?php echo $execution_result['success'] ? 'success' : 'danger'; ?>">
                                    <i class="fas fa-<?php echo $execution_result['success'] ? 'check-circle' : 'times-circle'; ?>"></i>
                                </span>
                            <?php endif; ?>
                            <div class="tab-indicator"></div>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="solution-tab" data-bs-toggle="tab" data-bs-target="#solution" type="button" role="tab" aria-controls="solution" aria-selected="false">
                            <i class="fas fa-lightbulb me-2"></i>
                            <span>Solução</span>
                            <div class="tab-indicator"></div>
                        </button>
                    </li>
                </ul>

                <div class="tab-content workspace-content">
                    <!-- Aba Instruções -->
                    <div class="tab-pane fade show active" id="instructions" role="tabpanel" aria-labelledby="instructions-tab">
                        <div class="instructions-content">
                            <section class="instruction-section">
                                <h4 class="section-title">
                                    <i class="fas fa-target text-primary me-2"></i>
                                    Objetivo do Exercício
                                </h4>
                                <div class="instruction-card">
                                    <p><?php echo nl2br(sanitize($exercise['instructions'] ?? 'Instruções não disponíveis.')); ?></p>
                                </div>
                            </section>

                            <?php if (!empty($exercise['hints'])): ?>
                            <section class="instruction-section">
                                <h4 class="section-title">
                                    <i class="fas fa-lightbulb text-warning me-2"></i>
                                    Dicas
                                </h4>
                                <div class="hints-container">
                                    <?php 
                                    $hints_text = is_string($exercise['hints']) ? $exercise['hints'] : '';
                                    if ($hints_text) {
                                        $hints = explode("\n", $hints_text);
                                        foreach ($hints as $index => $hint): 
                                            if (trim($hint)):
                                    ?>
                                    <div class="hint-item">
                                        <div class="hint-number"><?php echo $index + 1; ?></div>
                                        <div class="hint-content"><?php echo sanitize($hint); ?></div>
                                    </div>
                                    <?php 
                                            endif;
                                        endforeach;
                                    }
                                    ?>
                                </div>
                            </section>
                            <?php endif; ?>

                            <section class="instruction-section">
                                <h4 class="section-title">
                                    <i class="fas fa-list-check text-success me-2"></i>
                                    Requisitos
                                </h4>
                                <div class="requirements-list">
                                    <div class="requirement-item" data-completed="true">
                                        <div class="requirement-checkbox">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="requirement-content">
                                            <span class="requirement-text">Implemente a função conforme especificado</span>
                                            <span class="requirement-badge">Essencial</span>
                                        </div>
                                    </div>
                                    <div class="requirement-item" data-completed="false">
                                        <div class="requirement-checkbox">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="requirement-content">
                                            <span class="requirement-text">Mantenha a complexidade O(n) ou melhor</span>
                                            <span class="requirement-badge">Performance</span>
                                        </div>
                                    </div>
                                    <div class="requirement-item" data-completed="false">
                                        <div class="requirement-checkbox">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="requirement-content">
                                            <span class="requirement-text">Trate casos extremos e entradas inválidas</span>
                                            <span class="requirement-badge">Segurança</span>
                                        </div>
                                    </div>
                                    <div class="requirement-item" data-completed="true">
                                        <div class="requirement-checkbox">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="requirement-content">
                                            <span class="requirement-text">Use nomes descritivos para variáveis</span>
                                            <span class="requirement-badge">Qualidade</span>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                    <!-- Aba Editor -->
                    <div class="tab-pane fade" id="editor" role="tabpanel" aria-labelledby="editor-tab">
                        <form method="POST" id="codeForm">
                            <div class="editor-container">
                                <div class="editor-header">
                                    <div class="editor-info">
                                        <div class="file-tab active">
                                            <i class="fab fa-js-square me-1"></i>
                                            <span class="file-name">solution.js</span>
                                            <i class="fas fa-times file-close"></i>
                                        </div>
                                        <button type="button" class="btn-new-file" title="Novo arquivo">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="editor-actions">
                                        <div class="editor-action-group">
                                            <button type="button" class="btn-editor-action" onclick="undoCode()" title="Desfazer (Ctrl+Z)">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            <button type="button" class="btn-editor-action" onclick="redoCode()" title="Refazer (Ctrl+Y)">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        </div>
                                        <div class="editor-action-group">
                                            <button type="button" class="btn-editor-action" onclick="copyCode()" title="Copiar código">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button type="button" class="btn-editor-action" onclick="formatCode()" title="Formatar (Shift+Alt+F)">
                                                <i class="fas fa-indent"></i>
                                            </button>
                                        </div>
                                        <div class="editor-action-group">
                                            <button type="button" class="btn-editor-action" onclick="toggleLineNumbers()" title="Números de linha">
                                                <i class="fas fa-list-ol"></i>
                                            </button>
                                            <button type="button" class="btn-editor-action" onclick="toggleFullscreenEditor()" title="Tela cheia (F11)">
                                                <i class="fas fa-expand"></i>
                                            </button>
                                        </div>
                                        <div class="editor-action-group">
                                            <select class="theme-select" onchange="applyEditorTheme(this.value)">
                                                <option value="default">Tema Claro</option>
                                                <option value="dark" selected>Tema Escuro</option>
                                                <option value="monokai">Monokai</option>
                                                <option value="dracula">Dracula</option>
                                                <option value="github">GitHub</option>
                                            </select>
                                            <select class="font-size-select" onchange="changeFontSize(this.value)">
                                                <option value="12">12px</option>
                                                <option value="14" selected>14px</option>
                                                <option value="16">16px</option>
                                                <option value="18">18px</option>
                                                <option value="20">20px</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="editor-main">
                                    <div class="editor-sidebar">
                                        <div class="line-numbers" id="lineNumbers"></div>
                                    </div>
                                    
                                    <div class="editor-content">
                                        <textarea class="code-editor" id="user_code" name="user_code" 
                                                  rows="20" spellcheck="false" autocomplete="off"><?php echo sanitize($user_code); ?></textarea>
                                        <div class="editor-suggestions" id="editorSuggestions"></div>
                                    </div>
                                </div>
                                
                                <div class="editor-status-bar">
                                    <div class="status-left">
                                        <span class="status-item" id="cursorInfo">
                                            <i class="fas fa-location-arrow"></i> Ln 1, Col 1
                                        </span>
                                        <span class="status-item" id="selectionInfo">
                                        </span>
                                        <span class="status-item">
                                            <i class="fas fa-file-code"></i> JavaScript
                                        </span>
                                    </div>
                                    <div class="status-right">
                                        <span class="status-item" id="lineCount">
                                            <i class="fas fa-align-left"></i> 1 linha
                                        </span>
                                        <span class="status-item">
                                            <i class="fas fa-text-width"></i> Espaços: 2
                                        </span>
                                        <span class="status-item">
                                            <i class="fas fa-font"></i> UTF-8
                                        </span>
                                        <span class="status-item" id="autoSaveStatus">
                                            <i class="fas fa-check-circle text-success"></i> Salvo
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="action-buttons mt-3">
                                <button type="button" onclick="loadRandomChallenge()" class="btn btn-warning btn-lg">
                                    <i class="fas fa-random me-2"></i>Desafio Aleatório
                                </button>
                                <button type="button" onclick="executeCodeAdaptive()" class="btn btn-success btn-lg">
                                    <i class="fas fa-play me-2"></i>Executar Código
                                    <kbd>Ctrl+Enter</kbd>
                                </button>
                                <button type="button" onclick="runTests()" class="btn btn-info">
                                    <i class="fas fa-vial me-2"></i>Executar Testes
                                </button>
                                <button type="button" onclick="saveCode()" class="btn btn-outline-primary">
                                    <i class="fas fa-save me-2"></i>Salvar Progresso
                                    <kbd>Ctrl+S</kbd>
                                </button>
                                <button type="button" onclick="resetCode()" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo me-2"></i>Resetar Código
                                </button>
                                <button type="button" onclick="downloadCode()" class="btn btn-outline-dark">
                                    <i class="fas fa-download me-2"></i>Baixar
                                </button>
                            </div>
                        </form>
                    </div>
                    <!-- Aba Resultado -->
                    <div class="tab-pane fade" id="output" role="tabpanel" aria-labelledby="output-tab">
                        <!-- Área de Output Dinâmica (inserida pelo JavaScript) -->
                        
                        <!-- Mensagem inicial quando não há execução -->
                        <div class="empty-output-state">
                            <div class="empty-state-container">
                                <div class="empty-state-icon">
                                    <svg width="120" height="120" viewBox="0 0 120 120" fill="none">
                                        <circle cx="60" cy="60" r="55" stroke="url(#gradient1)" stroke-width="3" stroke-dasharray="8 4"/>
                                        <path d="M40 60L55 75L85 45" stroke="url(#gradient2)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                                        <circle cx="60" cy="60" r="35" fill="url(#gradient3)" opacity="0.1"/>
                                        <defs>
                                            <linearGradient id="gradient1" x1="0" y1="0" x2="120" y2="120">
                                                <stop offset="0%" stop-color="#6f42c1"/>
                                                <stop offset="100%" stop-color="#e83e8c"/>
                                            </linearGradient>
                                            <linearGradient id="gradient2" x1="40" y1="45" x2="85" y2="75">
                                                <stop offset="0%" stop-color="#28a745"/>
                                                <stop offset="100%" stop-color="#20c997"/>
                                            </linearGradient>
                                            <linearGradient id="gradient3" x1="25" y1="25" x2="95" y2="95">
                                                <stop offset="0%" stop-color="#6f42c1"/>
                                                <stop offset="100%" stop-color="#8e5dd9"/>
                                            </linearGradient>
                                        </defs>
                                    </svg>
                                </div>
                                <h4 class="empty-state-title">Pronto para executar!</h4>
                                <p class="empty-state-text">
                                    Escreva seu código no editor e clique em <strong>"Executar Código"</strong> 
                                    ou pressione <kbd>Ctrl+Enter</kbd> para ver os resultados aqui.
                                </p>
                                <div class="empty-state-tips">
                                    <div class="tip-card">
                                        <div class="tip-icon">💡</div>
                                        <div class="tip-content">
                                            <strong>Dica Rápida:</strong> Use console.log() para debugar seu código
                                        </div>
                                    </div>
                                    <div class="tip-card">
                                        <div class="tip-icon">🎯</div>
                                        <div class="tip-content">
                                            <strong>Testes:</strong> Clique em "Executar Testes" para validar sua solução
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Aba Solução -->
                    <div class="tab-pane fade" id="solution" role="tabpanel" aria-labelledby="solution-tab">
                        <div class="solution-wrapper">
                            <!-- Aviso antes de revelar -->
                            <div class="solution-warning-card">
                                <div class="warning-icon">
                                    <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                                        <circle cx="30" cy="30" r="28" fill="url(#warningGradient)"/>
                                        <path d="M30 20V32M30 38V40" stroke="white" stroke-width="3" stroke-linecap="round"/>
                                        <defs>
                                            <linearGradient id="warningGradient" x1="0" y1="0" x2="60" y2="60">
                                                <stop offset="0%" stop-color="#ffc107"/>
                                                <stop offset="100%" stop-color="#fd7e14"/>
                                            </linearGradient>
                                        </defs>
                                    </svg>
                                </div>
                                <h5 class="warning-title">⚠️ Antes de ver a solução...</h5>
                                <p class="warning-text">
                                    Resolver problemas sozinho é a melhor forma de aprender! Tente diferentes abordagens 
                                    antes de consultar a solução oficial.
                                </p>
                                <div class="warning-checklist">
                                    <div class="checklist-item">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Você leu os requisitos completamente?</span>
                                    </div>
                                    <div class="checklist-item">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Tentou ao menos 3 abordagens diferentes?</span>
                                    </div>
                                    <div class="checklist-item">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Usou console.log() para debugar?</span>
                                    </div>
                                    <div class="checklist-item">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Pesquisou sobre o problema?</span>
                                    </div>
                                </div>
                                <button class="btn-reveal-solution" onclick="revealSolution()">
                                    <i class="fas fa-eye me-2"></i>
                                    Estou ciente, mostrar solução
                                </button>
                            </div>
                            
                            <!-- Conteúdo da Solução (inicialmente oculto) -->
                            <div class="solution-content-wrapper" id="solutionContent" style="display: none;">
                                <div class="solution-header">
                                    <div class="solution-badge">
                                        <i class="fas fa-lightbulb me-2"></i>
                                        Solução Oficial
                                    </div>
                                    <h4 class="solution-main-title">
                                        <?php echo sanitize($exercise['title'] ?? 'Exercício'); ?>
                                    </h4>
                                </div>

                                <!-- Código da Solução -->
                                <div class="solution-code-section">
                                    <div class="solution-code-header">
                                        <div class="code-language">
                                            <i class="fab fa-js-square" style="color: #f7df1e;"></i>
                                            <span>JavaScript</span>
                                        </div>
                                        <button class="btn-copy-solution" onclick="copySolutionCode()">
                                            <i class="fas fa-copy me-1"></i>
                                            Copiar
                                        </button>
                                    </div>
                                    <div class="solution-code-container">
                                        <pre><code class="language-javascript" id="solutionCode"><?php echo sanitize($exercise['solution'] ?? '// Solução não disponível'); ?></code></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Card de Informações -->
            <div class="info-card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações do Exercício
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="info-item info-language">
                        <div class="info-icon" style="color: <?php echo $current_lang_config['color']; ?>">
                            <i class="fab <?php echo $current_lang_config['icon']; ?>"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Linguagem</span>
                            <span class="info-value" style="color: <?php echo $current_lang_config['color']; ?>; font-weight: 600;"><?php echo $current_lang_config['name']; ?></span>
                        </div>
                    </div>
                    <div class="info-item info-category">
                        <div class="info-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Categoria</span>
                            <span class="info-value"><?php echo sanitize($exercise['category_name'] ?? 'Geral'); ?></span>
                        </div>
                    </div>
                    <div class="info-item info-difficulty">
                        <div class="info-icon">
                            <i class="fas fa-signal"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Dificuldade</span>
                            <span class="info-value"><?php echo $difficulty_map[$difficulty_level] ?? 'Iniciante'; ?></span>
                        </div>
                    </div>
                    <div class="info-item info-type">
                        <div class="info-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Tipo</span>
                            <span class="info-value"><?php echo strtoupper($exercise['exercise_type'] ?? 'PRÁTICA'); ?></span>
                        </div>
                    </div>
                    <div class="info-item info-attempts">
                        <div class="info-icon">
                            <i class="fas fa-redo"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Tentativas</span>
                            <span class="info-value"><?php echo $exercise_stats['user_attempts']; ?></span>
                        </div>
                    </div>
                    <div class="info-item info-time">
                        <div class="info-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Melhor Tempo</span>
                            <span class="info-value"><?php echo $exercise_stats['user_best_time']; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card de Progresso -->
            <div class="progress-card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Seu Progresso
                    </h6>
                </div>
                <div class="card-body">
                    <div class="progress-circle-container">
                        <svg class="progress-circle" viewBox="0 0 100 100">
                            <circle class="progress-circle-bg" cx="50" cy="50" r="45" fill="none" stroke="#e9ecef" stroke-width="6"/>
                            <circle class="progress-circle-fill" cx="50" cy="50" r="45" fill="none" stroke="url(#progressGradient)" stroke-width="6" stroke-linecap="round" stroke-dasharray="282.6" stroke-dashoffset="98.91"/>
                            <defs>
                                <linearGradient id="progressGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#06d6a0;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#1b9aaa;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="progress-circle-text">
                            <span class="progress-percentage">65%</span>
                            <small class="progress-label">Completo</small>
                        </div>
                    </div>
                    <div class="progress-details">
                        <div class="progress-detail-item">
                            <div class="detail-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="detail-content">
                                <span class="detail-label">Objetivos</span>
                                <span class="detail-value">2/3 concluídos</span>
                            </div>
                        </div>
                        <div class="progress-detail-item">
                            <div class="detail-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="detail-content">
                                <span class="detail-label">Desempenho</span>
                                <span class="detail-value">Bom</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card de Ações Rápidas -->
            <div class="actions-card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Ações Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="actions-grid">
                        <a href="exercises_index.php" class="action-btn">
                            <i class="fas fa-arrow-left"></i>
                            <span>Voltar</span>
                        </a>
                        <button class="action-btn" data-bs-toggle="modal" data-bs-target="#hintsModal">
                            <i class="fas fa-lightbulb"></i>
                            <span>Dicas</span>
                        </button>
                        <button class="action-btn" onclick="toggleFullscreen()">
                            <i class="fas fa-expand"></i>
                            <span>Tela Cheia</span>
                        </button>
                        <button class="action-btn" data-bs-toggle="modal" data-bs-target="#shareModal">
                            <i class="fas fa-share"></i>
                            <span>Compartilhar</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Card de Exercícios Relacionados -->
            <div class="related-card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-layer-group me-2"></i>
                        Próximos Exercícios
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="related-list">
                        <?php foreach ($related_exercises as $related): 
                            $related_lang = $language_config[$related['language']] ?? $language_config['javascript'];
                        ?>
                        <a href="exercise_detail.php?id=<?php echo $related['id']; ?>" class="related-item">
                            <div class="related-icon" style="color: <?php echo $related_lang['color']; ?>">
                                <i class="fab <?php echo $related_lang['icon']; ?>"></i>
                            </div>
                            <div class="related-content">
                                <h6 class="related-title"><?php echo sanitize($related['title']); ?></h6>
                                <div class="related-meta">
                                    <span class="related-badge difficulty-<?php echo $related['difficulty']; ?>">
                                        <?php echo $difficulty_map[$related['difficulty']]; ?>
                                    </span>
                                    <span class="language-badge" style="background-color: <?php echo $related_lang['color']; ?>20; color: <?php echo $related_lang['color']; ?>; padding: 2px 8px; border-radius: 4px; font-size: 11px; margin-left: 6px;">
                                        <i class="fab <?php echo $related_lang['icon']; ?> me-1"></i><?php echo $related_lang['name']; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="related-progress">
                                <div class="circular-progress">
                                    <svg viewBox="0 0 36 36" class="circular-chart">
                                        <path class="circle-bg"
                                            d="M18 2.0845
                                            a 15.9155 15.9155 0 0 1 0 31.831
                                            a 15.9155 15.9155 0 0 1 0 -31.831"
                                            fill="none"
                                            stroke="#e9ecef"
                                            stroke-width="3"
                                        />
                                        <path class="circle"
                                            d="M18 2.0845
                                            a 15.9155 15.9155 0 0 1 0 31.831
                                            a 15.9155 15.9155 0 0 1 0 -31.831"
                                            fill="none"
                                            stroke-width="3"
                                            stroke-dasharray="<?php echo $related['completion_rate']; ?>, 100"
                                            stroke-linecap="round"
                                        />
                                    </svg>
                                    <span class="percentage"><?php echo $related['completion_rate']; ?>%</span>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal de Dicas -->
<div class="modal fade" id="hintsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-lightbulb text-warning me-2"></i>
                    Dicas do Exercício
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert" style="background: linear-gradient(135deg, <?php echo $current_lang_config['color']; ?>15 0%, <?php echo $current_lang_config['color']; ?>05 100%); border-left: 4px solid <?php echo $current_lang_config['color']; ?>; border-radius: 8px;">
                    <i class="fab <?php echo $current_lang_config['icon']; ?> me-2" style="color: <?php echo $current_lang_config['color']; ?>; font-size: 20px;"></i>
                    <strong style="color: <?php echo $current_lang_config['color']; ?>">Dicas de <?php echo $current_lang_config['name']; ?></strong>
                </div>
                
                <div class="hints-list">
                    <?php foreach ($current_hints as $index => $hint): ?>
                    <div class="hint-modal-item">
                        <div class="hint-number" style="background: linear-gradient(135deg, <?php echo $current_lang_config['color']; ?> 0%, <?php echo $current_lang_config['color']; ?>CC 100%);"><?php echo $index + 1; ?></div>
                        <div class="hint-content">
                            <p><?php echo $hint; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (!empty($exercise['hints']) && is_string($exercise['hints'])): ?>
                    <hr style="margin: 1.5rem 0; border-color: <?php echo $current_lang_config['color']; ?>30;">
                    <div class="alert alert-secondary">
                        <strong><i class="fas fa-info-circle me-2"></i>Dica Específica do Exercício:</strong>
                        <div class="mt-2" style="white-space: pre-line;"><?php echo sanitize($exercise['hints']); ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --primary-color: #6f42c1;
    --secondary-color: #e83e8c;
    --success-color: #28a745;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --editor-bg: #f8f9fa;
    --editor-border: #e9ecef;
    --text-heading: #212529;
    --card-shadow: 0 8px 24px rgba(111, 66, 193, 0.12);
}

/* Header do Exercício */
.exercise-header-card {
    background: linear-gradient(135deg, #6f42c1 0%, #8e5dd9 50%, #e83e8c 100%);
    color: white;
    padding: 2.5rem;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(111, 66, 193, 0.4);
    position: relative;
    overflow: hidden;
}

.exercise-header-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
    pointer-events: none;
    animation: headerGlow 8s ease-in-out infinite;
}

@keyframes headerGlow {
    0%, 100% {
        transform: translate(0, 0);
        opacity: 0.5;
    }
    50% {
        transform: translate(-10%, -10%);
        opacity: 0.8;
    }
}

.exercise-title {
    font-size: 2.5rem;
    font-weight: 800;
    line-height: 1.2;
    text-shadow: 0 2px 20px rgba(0,0,0,0.2);
    letter-spacing: -0.5px;
    position: relative;
    z-index: 1;
    color: #ffffff;
}

.exercise-description {
    font-size: 1.1rem;
    opacity: 0.95;
    line-height: 1.6;
    font-weight: 400;
    text-shadow: 0 1px 3px rgba(0,0,0,0.1);
    position: relative;
    z-index: 1;
    color: #ffffff;
}

.exercise-type-badge {
    padding: 0.5rem 1.2rem;
    border-radius: 30px;
    font-size: 0.875rem;
    font-weight: 600;
    background: rgba(255,255,255,0.25);
    backdrop-filter: blur(10px);
    color: white;
    border: 1px solid rgba(255,255,255,0.3);
    transition: all 0.3s ease;
}

.exercise-type-badge:hover {
    background: rgba(255,255,255,0.35);
    transform: translateY(-2px);
}

.difficulty-badge {
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.difficulty-beginner { 
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}
.difficulty-intermediate { 
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: #333;
}
.difficulty-advanced { 
    background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
    color: white;
}

.metadata-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 1.5rem;
}

.metadata-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.metadata-card:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.metadata-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.metadata-category .metadata-icon {
    background: linear-gradient(135deg, #6f42c1, #8e5dd9);
    color: white;
}

.metadata-time .metadata-icon {
    background: linear-gradient(135deg, #e83e8c, #fd7e14);
    color: white;
}

.metadata-success .metadata-icon {
    background: linear-gradient(135deg, #17a2b8, #007bff);
    color: white;
}

.metadata-attempts .metadata-icon {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.metadata-info {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.metadata-label {
    font-size: 0.7rem;
    opacity: 1;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
}

.metadata-value {
    font-size: 1rem;
    font-weight: 700;
    line-height: 1;
    color: #ffffff;
}

.progress-ring-container {
    display: inline-block;
    position: relative;
}

.progress-ring {
    position: relative;
    display: inline-block;
}

.progress-ring svg {
    transform: rotate(-90deg);
    filter: drop-shadow(0 4px 12px rgba(6, 214, 160, 0.3));
}

.progress-ring-bg {
    transition: all 0.3s ease;
}

.progress-ring-circle {
    transition: stroke-dashoffset 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    animation: progressGlow 2s ease-in-out infinite;
}

@keyframes progressGlow {
    0%, 100% {
        filter: drop-shadow(0 0 5px rgba(6, 214, 160, 0.5));
    }
    50% {
        filter: drop-shadow(0 0 12px rgba(6, 214, 160, 0.8));
    }
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: white;
    pointer-events: none;
}

.progress-percentage {
    display: block;
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 0.25rem;
    text-shadow: 0 2px 8px rgba(0,0,0,0.2);
    color: #ffffff;
}

.progress-label {
    display: block;
    font-size: 0.7rem;
    font-weight: 600;
    opacity: 0.95;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: rgba(255, 255, 255, 0.9);
}

/* Área de Trabalho */
.workspace-container {
    background: white;
    border-radius: 20px;
    box-shadow: var(--card-shadow);
    overflow: hidden;
    border: 1px solid rgba(67, 97, 238, 0.1);
}

.workspace-tabs {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-bottom: 3px solid #e9ecef;
    padding: 0.5rem 1rem 0;
    display: flex;
    gap: 0.5rem;
}

.workspace-tabs .nav-item {
    margin-bottom: -3px;
}

.workspace-tabs .nav-link {
    border: none;
    padding: 1rem 2rem;
    color: #495057;
    font-weight: 600;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    border-radius: 12px 12px 0 0;
    background: transparent;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-height: 60px;
    overflow: hidden;
}

.workspace-tabs .nav-link i {
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.workspace-tabs .nav-link span {
    font-size: 0.95rem;
    letter-spacing: 0.3px;
}

.tab-indicator {
    position: absolute;
    bottom: -3px;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    transform: scaleX(0);
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.workspace-tabs .nav-link:hover {
    color: var(--primary-color);
    background: linear-gradient(135deg, rgba(67, 97, 238, 0.08) 0%, rgba(58, 12, 163, 0.05) 100%);
    transform: translateY(-2px);
}

.workspace-tabs .nav-link:hover i {
    transform: scale(1.15) rotate(5deg);
}

.workspace-tabs .nav-link:hover .tab-indicator {
    transform: scaleX(0.5);
}

.workspace-tabs .nav-link.active {
    color: var(--primary-color);
    background: white;
    box-shadow: 0 -4px 12px rgba(67, 97, 238, 0.15);
}

.workspace-tabs .nav-link.active i {
    transform: scale(1.1);
    color: var(--primary-color);
}

.workspace-tabs .nav-link.active .tab-indicator {
    transform: scaleX(1);
}

.result-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    margin-left: 0.5rem;
    animation: pulse 2s infinite;
}

.result-badge.badge-success {
    background: linear-gradient(135deg, #06d6a0, #1b9aaa);
    color: white;
    box-shadow: 0 2px 8px rgba(6, 214, 160, 0.4);
}

.result-badge.badge-danger {
    background: linear-gradient(135deg, #ef476f, #d62828);
    color: white;
    box-shadow: 0 2px 8px rgba(239, 71, 111, 0.4);
}

.workspace-content {
    padding: 2.5rem;
    min-height: 500px;
}

.tab-pane {
    animation: tabFadeIn 0.5s ease-out;
}

@keyframes tabFadeIn {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Instruções */
.instruction-section {
    margin-bottom: 2.5rem;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #212529;
    display: flex;
    align-items: center;
}

.instruction-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    padding: 1.8rem;
    border-radius: 15px;
    border-left: 5px solid var(--primary-color);
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    color: #212529;
}

.instruction-card:hover {
    transform: translateX(5px);
    box-shadow: 0 6px 20px rgba(67, 97, 238, 0.15);
}

.hints-container {
    display: grid;
    gap: 1rem;
}

.hint-item {
    display: flex;
    gap: 1rem;
    padding: 1.2rem;
    background: linear-gradient(135deg, #fff9e6 0%, #fffbf0 100%);
    border-radius: 12px;
    border-left: 5px solid #ffc107;
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.15);
    transition: all 0.3s ease;
    color: #856404;
}

.hint-item:hover {
    transform: translateX(5px);
    box-shadow: 0 6px 18px rgba(255, 193, 7, 0.25);
}

.hint-number {
    width: 30px;
    height: 30px;
    background: #ffc107;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.hint-content {
    color: #856404;
    flex: 1;
}

.requirements-list {
    display: grid;
    gap: 1rem;
}

.requirement-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.2rem 1.5rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 12px;
    border: 2px solid #e9ecef;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
    overflow: hidden;
    color: #212529;
}

.requirement-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(135deg, #06d6a0, #1b9aaa);
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.requirement-item[data-completed="true"]::before {
    transform: scaleY(1);
}

.requirement-item:hover {
    border-color: #06d6a0;
    transform: translateX(8px);
    box-shadow: 0 8px 24px rgba(6, 214, 160, 0.2);
}

.requirement-item[data-completed="true"] {
    background: linear-gradient(135deg, rgba(6, 214, 160, 0.08) 0%, rgba(27, 154, 170, 0.05) 100%);
    border-color: rgba(6, 214, 160, 0.3);
}

.requirement-checkbox {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e9ecef;
    color: transparent;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.requirement-item[data-completed="true"] .requirement-checkbox {
    background: linear-gradient(135deg, #06d6a0, #1b9aaa);
    color: white;
    box-shadow: 0 4px 12px rgba(6, 214, 160, 0.3);
}

.requirement-checkbox i {
    font-size: 1rem;
}

.requirement-content {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.requirement-text {
    font-size: 0.95rem;
    font-weight: 500;
    color: #212529;
    line-height: 1.4;
}

.requirement-item[data-completed="true"] .requirement-text {
    font-weight: 600;
    color: #155724;
}

.requirement-badge {
    padding: 0.35rem 0.85rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    background: linear-gradient(135deg, #6f42c1, #8e5dd9);
    color: white;
    white-space: nowrap;
}

/* Editor */
.editor-container {
    border: 2px solid #2d3748;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 12px 32px rgba(0,0,0,0.15);
    background: #1a202c;
}

.editor-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
    border-bottom: 1px solid #4a5568;
}

.editor-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.file-tab {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(255,255,255,0.05);
    border-radius: 8px 8px 0 0;
    color: #e2e8f0;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.file-tab.active {
    background: #1a202c;
    border-color: #4a5568;
    border-bottom-color: transparent;
}

.file-tab:hover {
    background: rgba(255,255,255,0.1);
}

.file-tab .fa-js-square {
    color: #f7df1e;
}

.file-close {
    opacity: 0;
    font-size: 0.75rem;
    margin-left: 0.5rem;
    cursor: pointer;
    transition: opacity 0.2s;
}

.file-tab:hover .file-close {
    opacity: 0.7;
}

.file-close:hover {
    opacity: 1 !important;
    color: #ef476f;
}

.btn-new-file {
    background: none;
    border: 1px dashed #4a5568;
    color: #a0aec0;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-new-file:hover {
    border-color: #6f42c1;
    color: #6f42c1;
    background: rgba(111, 66, 193, 0.1);
}

.editor-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.editor-action-group {
    display: flex;
    gap: 0.25rem;
    padding: 0.25rem;
    background: rgba(255,255,255,0.05);
    border-radius: 8px;
}

.btn-editor-action {
    background: none;
    border: none;
    color: #a0aec0;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-editor-action:hover {
    background: rgba(111, 66, 193, 0.2);
    color: #6f42c1;
}

.theme-select,
.font-size-select {
    background: rgba(255,255,255,0.05);
    border: 1px solid #4a5568;
    color: #e2e8f0;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-size: 0.75rem;
    cursor: pointer;
    outline: none;
    transition: all 0.3s ease;
}

.theme-select:hover,
.font-size-select:hover {
    border-color: #6f42c1;
    background: rgba(111, 66, 193, 0.1);
}

.theme-select option,
.font-size-select option {
    background: #1a202c;
    color: #e2e8f0;
}

.editor-main {
    display: flex;
    background: #1e1e1e;
    min-height: 500px;
    max-height: 600px;
    position: relative;
}

.editor-sidebar {
    background: #252526;
    border-right: 1px solid #3e3e42;
    user-select: none;
    overflow: hidden;
}

.line-numbers {
    padding: 1rem 0;
    text-align: right;
    color: #858585;
    font-family: 'Courier New', monospace;
    font-size: 14px;
    line-height: 1.5;
    min-width: 50px;
    padding-right: 1rem;
    padding-left: 0.5rem;
}

.line-numbers .line-number {
    display: block;
    height: 21px;
    cursor: pointer;
    transition: color 0.2s;
}

.line-numbers .line-number:hover {
    color: #c5c5c5;
}

.line-numbers .line-number.active {
    color: #fff;
    font-weight: bold;
}

.editor-content {
    flex: 1;
    position: relative;
    overflow: auto;
}

.code-editor {
    width: 100%;
    height: 100%;
    border: none;
    padding: 1rem;
    font-family: 'Fira Code', 'Consolas', 'Monaco', 'Courier New', monospace;
    font-size: 14px;
    line-height: 1.5;
    background: #1e1e1e;
    color: #d4d4d4;
    resize: none;
    outline: none;
    overflow-y: auto;
    white-space: pre;
    word-wrap: normal;
    overflow-wrap: normal;
    tab-size: 2;
}

.code-editor::selection {
    background: rgba(111, 66, 193, 0.3);
}

.editor-status-bar {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 1rem;
    background: #007acc;
    color: #ffffff;
    font-size: 0.75rem;
    font-family: 'Segoe UI', system-ui, sans-serif;
}

.status-left,
.status-right {
    display: flex;
    gap: 1.5rem;
    align-items: center;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    cursor: pointer;
    transition: opacity 0.2s;
}

.status-item:hover {
    opacity: 0.8;
}

.status-item i {
    font-size: 0.7rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.action-buttons kbd {
    background: #e9ecef;
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 0.15rem 0.4rem;
    font-size: 0.7rem;
    margin-left: 0.5rem;
    color: #495057;
    font-family: monospace;
}

.action-buttons .btn-success {
    background: linear-gradient(135deg, #06d6a0 0%, #1b9aaa 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(6, 214, 160, 0.3);
    transition: all 0.3s ease;
}

.action-buttons .btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(6, 214, 160, 0.4);
}

.action-buttons .btn-outline-primary {
    border: 2px solid var(--primary-color);
    color: var(--primary-color);
    font-weight: 600;
    transition: all 0.3s ease;
}

.action-buttons .btn-outline-primary:hover {
    background: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
}

.action-buttons .btn-outline-secondary {
    border: 2px solid #6c757d;
    color: #6c757d;
    font-weight: 600;
    transition: all 0.3s ease;
}

/* Empty State */
.empty-output-state {
    min-height: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem 2rem;
}

.empty-state-container {
    max-width: 600px;
    text-align: center;
}

.empty-state-icon {
    margin-bottom: 2rem;
    animation: floatIcon 3s ease-in-out infinite;
}

@keyframes floatIcon {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.empty-state-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #212529;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, #6f42c1, #e83e8c);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.empty-state-text {
    font-size: 1.05rem;
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 2.5rem;
}

.empty-state-text kbd {
    background: #e9ecef;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.9rem;
    border: 1px solid #dee2e6;
}

.empty-state-tips {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-top: 2rem;
}

.tip-card {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.25rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border: 2px solid #e9ecef;
    border-radius: 12px;
    text-align: left;
    transition: all 0.3s ease;
}

.tip-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    border-color: #6f42c1;
}

.tip-icon {
    font-size: 1.75rem;
    flex-shrink: 0;
}

.tip-content {
    font-size: 0.9rem;
    line-height: 1.5;
    color: #495057;
}

.tip-content strong {
    display: block;
    color: #212529;
    margin-bottom: 0.25rem;
}

/* Solução */
.solution-wrapper {
    padding: 2rem 0;
}

.solution-warning-card {
    background: linear-gradient(135deg, #fff3cd 0%, #fff8e1 100%);
    border: 2px solid #ffc107;
    border-radius: 16px;
    padding: 2.5rem;
    text-align: center;
    margin-bottom: 2rem;
    box-shadow: 0 8px 24px rgba(255, 193, 7, 0.15);
}

.warning-icon {
    margin-bottom: 1.5rem;
    display: inline-block;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.warning-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #212529;
    margin-bottom: 1rem;
}

.warning-text {
    font-size: 1.05rem;
    color: #495057;
    line-height: 1.6;
    margin-bottom: 2rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.warning-checklist {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.checklist-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f8f9fa;
    color: #495057;
    font-size: 0.95rem;
}

.checklist-item:last-child {
    border-bottom: none;
}

.checklist-item i {
    color: #ffc107;
    font-size: 1.1rem;
}

.btn-reveal-solution {
    background: linear-gradient(135deg, #6f42c1 0%, #8e5dd9 100%);
    color: white;
    border: none;
    padding: 0.875rem 2rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(111, 66, 193, 0.3);
}

.btn-reveal-solution:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(111, 66, 193, 0.4);
}

.solution-content-wrapper {
    animation: slideDown 0.5s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.solution-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.solution-badge {
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 0.5rem 1.25rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.solution-main-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #212529;
}

.solution-code-section {
    background: #1e1e1e;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 2rem;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

.solution-code-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
    border-bottom: 1px solid #4a5568;
}

.code-language {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #e2e8f0;
    font-size: 0.875rem;
    font-weight: 500;
}

.code-language i {
    font-size: 1.25rem;
}

.btn-copy-solution {
    background: rgba(255,255,255,0.1);
    color: #e2e8f0;
    border: 1px solid rgba(255,255,255,0.2);
    padding: 0.4rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-copy-solution:hover {
    background: rgba(111, 66, 193, 0.3);
    border-color: #6f42c1;
}

.solution-code-container {
    padding: 0;
}

.solution-code-container pre {
    margin: 0;
    padding: 1.5rem;
    background: #1e1e1e;
    color: #d4d4d4;
    font-family: 'Fira Code', 'Consolas', monospace;
    font-size: 14px;
    line-height: 1.6;
    overflow-x: auto;
}

/* Sidebar Cards */
.info-card, .progress-card, .actions-card, .related-card {
    background: white;
    border-radius: 20px;
    box-shadow: var(--card-shadow);
    overflow: hidden;
    border: 1px solid rgba(67, 97, 238, 0.1);
    transition: all 0.3s ease;
}

.info-card:hover, .progress-card:hover, .actions-card:hover, .related-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 32px rgba(67, 97, 238, 0.18);
}

.info-card .card-header, .progress-card .card-header, 
.actions-card .card-header, .related-card .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #e9ecef;
    padding: 1rem 1.5rem;
    font-weight: 600;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(0,0,0,0.06);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.info-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 0;
    background: linear-gradient(90deg, transparent, rgba(67, 97, 238, 0.05));
    transition: width 0.3s ease;
}

.info-item:hover::before {
    width: 100%;
}

.info-item:hover {
    background: rgba(67, 97, 238, 0.02);
    padding-left: 1.75rem;
}

.info-item:last-child {
    border-bottom: none;
}

.info-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

.info-category .info-icon {
    background: linear-gradient(135deg, #6f42c1, #8e5dd9);
    color: white;
    box-shadow: 0 4px 12px rgba(111, 66, 193, 0.3);
}

.info-difficulty .info-icon {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.info-type .info-icon {
    background: linear-gradient(135deg, #e83e8c, #fd7e14);
    color: white;
    box-shadow: 0 4px 12px rgba(232, 62, 140, 0.3);
}

.info-attempts .info-icon {
    background: linear-gradient(135deg, #007bff, #17a2b8);
    color: white;
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

.info-time .info-icon {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: white;
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
}

.info-item:hover .info-icon {
    transform: scale(1.1) rotate(5deg);
}

.info-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1rem;
    font-weight: 700;
    color: #212529;
    line-height: 1.2;
}

.progress-card .card-body {
    padding: 2rem 1.5rem;
}

.progress-circle-container {
    position: relative;
    width: 150px;
    height: 150px;
    margin: 0 auto 2rem;
}

.progress-circle {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
}

.progress-circle-fill {
    transition: stroke-dashoffset 1s cubic-bezier(0.4, 0, 0.2, 1);
    filter: drop-shadow(0 0 8px rgba(6, 214, 160, 0.4));
}

.progress-circle-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.progress-circle-text .progress-percentage {
    display: block;
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #28a745, #20c997);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.progress-circle-text .progress-label {
    display: block;
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.progress-details {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.progress-detail-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 12px;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.progress-detail-item:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    border-color: #06d6a0;
}

.detail-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.detail-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.detail-label {
    font-size: 0.7rem;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    font-size: 0.95rem;
    color: #212529;
    font-weight: 700;
}

.actions-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1.2rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border: 2px solid #e9ecef;
    border-radius: 12px;
    text-decoration: none;
    color: #495057;
    transition: all 0.3s ease;
    text-align: center;
    font-weight: 600;
    position: relative;
    overflow: hidden;
}

.action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 0;
}

.action-btn:hover::before {
    opacity: 1;
}

.action-btn i,
.action-btn span {
    position: relative;
    z-index: 1;
}

.action-btn:hover {
    color: white;
    border-color: var(--primary-color);
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
    text-decoration: none;
}

.action-btn i {
    font-size: 1.5rem;
    transition: transform 0.3s ease;
}

.action-btn:hover i {
    transform: scale(1.2);
}

.related-list {
    display: flex;
    flex-direction: column;
}

.related-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    text-decoration: none;
    color: inherit;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border-bottom: 1px solid rgba(0,0,0,0.06);
    position: relative;
    overflow: hidden;
}

.related-item:last-child {
    border-bottom: none;
}

.related-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 4px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.related-item:hover::before {
    transform: scaleY(1);
}

.related-item:hover {
    background: linear-gradient(90deg, rgba(67, 97, 238, 0.03) 0%, transparent 100%);
    padding-left: 2rem;
    text-decoration: none;
    color: inherit;
}

.related-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #ffd60a, #f48c06);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    flex-shrink: 0;
    box-shadow: 0 4px 15px rgba(255, 214, 10, 0.3);
    transition: all 0.3s ease;
}

.related-item:hover .related-icon {
    transform: rotate(-10deg) scale(1.1);
    box-shadow: 0 6px 20px rgba(255, 214, 10, 0.5);
}

.related-content {
    flex: 1;
    min-width: 0;
}

.related-title {
    margin: 0 0 0.5rem 0;
    font-size: 0.95rem;
    font-weight: 700;
    color: #212529;
    line-height: 1.3;
    transition: color 0.3s ease;
}

.related-item:hover .related-title {
    color: var(--primary-color);
}

.related-meta {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.related-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.related-badge.difficulty-beginner {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.related-badge.difficulty-intermediate {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: #333;
}

.related-badge.difficulty-advanced {
    background: linear-gradient(135deg, #dc3545, #e83e8c);
    color: white;
}

.related-progress {
    flex-shrink: 0;
}

.circular-progress {
    position: relative;
    width: 50px;
    height: 50px;
}

.circular-chart {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
}

.circle-bg {
    stroke: #e9ecef;
}

.circle {
    stroke: url(#relatedGradient);
    transition: stroke-dasharray 0.6s ease;
}

.related-item:nth-child(1) .circle {
    stroke: #06d6a0;
}

.related-item:nth-child(2) .circle {
    stroke: #4facfe;
}

.related-item:nth-child(3) .circle {
    stroke: #f093fb;
}

.percentage {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 0.7rem;
    font-weight: 700;
    color: #212529;
}

.hint-modal-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    margin-bottom: 1rem;
    background: #fff3cd;
    border-radius: 8px;
    border-left: 4px solid #ffc107;
}

/* Responsividade */
@media (max-width: 768px) {
    .exercise-header-card {
        padding: 1.5rem;
    }
    
    .metadata-grid {
        grid-template-columns: 1fr;
    }
    
    .workspace-content {
        padding: 1rem;
    }
    
    .action-buttons {
        flex-direction: column;
        align-items: stretch;
    }
    
    .actions-grid {
        grid-template-columns: 1fr;
    }
}

/* Animações */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.exercise-header-card {
    animation: fadeInUp 0.6s ease-out;
}

.workspace-container {
    animation: fadeInUp 0.7s ease-out;
}

.info-card, .progress-card, .actions-card, .related-card {
    animation: slideInRight 0.8s ease-out;
}
.achievement-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 12px;
    margin-bottom: 1rem;
    border: 2px solid #e9ecef;
    animation: slideInUp 0.5s ease-out;
}

.achievement-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #28a745, #20c997);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.achievement-content h6 {
    margin: 0 0 0.5rem 0;
    color: #212529;
    font-weight: 700;
}

.achievement-content p {
    margin: 0 0 0.5rem 0;
    color: #6c757d;
    font-size: 0.9rem;
}

.achievement-content .coins {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.coins-earned {
    animation: bounceIn 0.8s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes bounceIn {
    0% {
        opacity: 0;
        transform: scale(0.3);
    }
    50% {
        opacity: 1;
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.execution-result {
    animation: fadeInUp 0.5s ease-out;
}

.test-result-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

.test-result-item.passed {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(32, 201, 151, 0.05) 100%);
    border-left: 4px solid #28a745;
}

.test-result-item.failed {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(239, 71, 111, 0.05) 100%);
    border-left: 4px solid #dc3545;
}

.test-status {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: white;
}

.test-result-item.passed .test-status {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.test-result-item.failed .test-status {
    background: linear-gradient(135deg, #dc3545, #ef476f);
}

.test-name {
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.25rem;
}

.test-message {
    font-size: 0.9rem;
    color: #6c757d;
}

.execution-metrics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.metric {
    text-align: center;
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 8px;
}

.metric-label {
    display: block;
    font-size: 0.8rem;
    opacity: 0.9;
    margin-bottom: 0.25rem;
}

.metric-value {
    display: block;
    font-size: 1.1rem;
    font-weight: 700;
}

.output-container, .test-results {
    margin-top: 2rem;
}

.output-content {
    background: #1e1e1e;
    color: #d4d4d4;
    padding: 1.5rem;
    border-radius: 8px;
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    line-height: 1.5;
    white-space: pre-wrap;
    max-height: 300px;
    overflow-y: auto;
}

.tests-grid {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
</style>
<script>
// Detectar linguagem do exercício para JavaScript
const categoryRaw = '<?php echo strtolower(trim($exercise["category_name"] ?? "javascript")); ?>';

const languageMapping = {
    'javascript': 'javascript',
    'js': 'javascript',
    'html': 'html',
    'html5': 'html',
    'css': 'css',
    'css3': 'css',
    'php': 'php',
    'php7': 'php',
    'php8': 'php'
};

// Detectar por palavra-chave no nome da categoria
let currentLanguage = 'javascript'; // padrão
for (const [key, value] of Object.entries(languageMapping)) {
    if (categoryRaw.includes(key)) {
        currentLanguage = value;
        break;
    }
}

// Revelar solução
window.revealSolution = function() {
    const solutionContent = document.getElementById('solutionContent');
    const warningCard = document.querySelector('.solution-warning-card');
    
    if (solutionContent && warningCard) {
        warningCard.style.display = 'none';
        solutionContent.style.display = 'block';
        showToast('Solução revelada! Estude com atenção.', 'success');
    }
};

// Copiar código da solução
window.copySolutionCode = function() {
    const codeElement = document.getElementById('solutionCode');
    if (!codeElement) return;
    
    const code = codeElement.textContent;
    
    // Criar elemento temporário para copiar
    const textarea = document.createElement('textarea');
    textarea.value = code;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    
    try {
        document.execCommand('copy');
        showToast('Código copiado para área de transferência!', 'success');
        
        // Mudar texto do botão temporariamente
        const btn = event.target.closest('.btn-copy-solution');
        if (btn) {
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check me-1"></i>Copiado!';
            setTimeout(() => {
                btn.innerHTML = originalHTML;
            }, 2000);
        }
    } catch (err) {
        showToast('Erro ao copiar código', 'danger');
    }
    
    document.body.removeChild(textarea);
};

document.addEventListener('DOMContentLoaded', function() {
    // Sistema de abas aprimorado
    const tabTriggers = document.querySelectorAll('[data-bs-toggle="tab"]');
    
    tabTriggers.forEach(trigger => {
        trigger.addEventListener('shown.bs.tab', function (event) {
            // Adiciona animação ao trocar de aba
            const targetPane = document.querySelector(this.getAttribute('data-bs-target'));
            if (targetPane) {
                targetPane.style.animation = 'none';
                setTimeout(() => {
                    targetPane.style.animation = 'tabFadeIn 0.5s ease-out';
                }, 10);
            }
            
            // Salva aba ativa no localStorage
            localStorage.setItem('activeTab', this.getAttribute('data-bs-target'));
        });
        
        // Efeito de hover nos ícones
        trigger.addEventListener('mouseenter', function() {
            const icon = this.querySelector('i');
            if (icon && !this.classList.contains('active')) {
                icon.style.transform = 'scale(1.2) rotate(10deg)';
            }
        });
        
        trigger.addEventListener('mouseleave', function() {
            const icon = this.querySelector('i');
            if (icon && !this.classList.contains('active')) {
                icon.style.transform = '';
            }
        });
    });
    
    // Restaura aba ativa do localStorage
    const activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
        const tabToActivate = document.querySelector(`[data-bs-target="${activeTab}"]`);
        if (tabToActivate) {
            const tab = new bootstrap.Tab(tabToActivate);
            tab.show();
        }
    }

    // Editor de código avançado
    const codeEditor = document.getElementById('user_code');
    const lineNumbers = document.getElementById('lineNumbers');
    const themeSelect = document.querySelector('.theme-select');
    const fontSizeSelect = document.querySelector('.font-size-select');
    
    // Histórico para undo/redo
    let undoHistory = [];
    let redoHistory = [];
    let currentCode = '';
    
    if (codeEditor) {
        // Inicializar código atual
        currentCode = codeEditor.value;
        updateLineNumbers();
        updateEditorStatus();
        
        // Event listeners do editor
        codeEditor.addEventListener('input', function() {
            // Adicionar ao histórico
            if (currentCode !== this.value) {
                undoHistory.push(currentCode);
                currentCode = this.value;
                redoHistory = []; // Limpar redo ao fazer nova ação
                
                // Limitar histórico a 50 itens
                if (undoHistory.length > 50) {
                    undoHistory.shift();
                }
            }
            
            updateLineNumbers();
            updateEditorStatus();
            updateAutoSaveStatus();
        });
        
        codeEditor.addEventListener('keydown', function(e) {
            // Tab: inserir 2 espaços
            if (e.key === 'Tab') {
                e.preventDefault();
                const start = this.selectionStart;
                const end = this.selectionEnd;
                
                this.value = this.value.substring(0, start) + '  ' + this.value.substring(end);
                this.selectionStart = this.selectionEnd = start + 2;
            }
            
            // Atalhos de teclado
            if (e.ctrlKey || e.metaKey) {
                switch(e.key) {
                    case 'z': // Undo
                        e.preventDefault();
                        undoCode();
                        break;
                    case 'y': // Redo
                        e.preventDefault();
                        redoCode();
                        break;
                    case 's': // Save
                        e.preventDefault();
                        saveCode();
                        break;
                    case 'Enter': // Execute
                        e.preventDefault();
                        executeCodeAdaptive();
                        break;
                }
            }
            
            // Shift+Alt+F: Format
            if (e.shiftKey && e.altKey && e.key === 'F') {
                e.preventDefault();
                formatCode();
            }
            
            // F11: Fullscreen
            if (e.key === 'F11') {
                e.preventDefault();
                toggleFullscreenEditor();
            }
        });
        
        codeEditor.addEventListener('scroll', function() {
            // Sincronizar scroll dos números de linha
            if (lineNumbers) {
                lineNumbers.scrollTop = this.scrollTop;
            }
        });
        
        codeEditor.addEventListener('click', updateEditorStatus);
        codeEditor.addEventListener('keyup', updateEditorStatus);
    }
    
    // Atualizar números de linha
    function updateLineNumbers() {
        if (!codeEditor || !lineNumbers) return;
        
        const lines = codeEditor.value.split('\n');
        const lineCount = lines.length;
        
        let numbersHTML = '';
        for (let i = 1; i <= lineCount; i++) {
            numbersHTML += `<div class="line-number">${i}</div>`;
        }
        lineNumbers.innerHTML = numbersHTML;
    }

    function updateEditorStatus() {
        if (!codeEditor) return;
        
        const text = codeEditor.value;
        const lines = text.split('\n');
        const cursorPos = codeEditor.selectionStart;
        const linesBefore = text.substring(0, cursorPos).split('\n');
        const currentLine = linesBefore.length;
        const currentCol = linesBefore[linesBefore.length - 1].length + 1;
        
        // Atualizar posição do cursor
        const cursorInfo = document.getElementById('cursorInfo');
        if (cursorInfo) {
            cursorInfo.textContent = `Ln ${currentLine}, Col ${currentCol}`;
        }
        
        // Atualizar seleção
        const selectionInfo = document.getElementById('selectionInfo');
        if (selectionInfo) {
            const selectionLength = codeEditor.selectionEnd - codeEditor.selectionStart;
            if (selectionLength > 0) {
                selectionInfo.textContent = `${selectionLength} selecionado${selectionLength > 1 ? 's' : ''}`;
            } else {
                selectionInfo.textContent = '';
            }
        }
        
        // Atualizar contagem de linhas
        const lineCount = document.getElementById('lineCount');
        if (lineCount) {
            lineCount.textContent = `${lines.length} linhas`;
        }
    }
    
    function updateAutoSaveStatus() {
        const autoSaveStatus = document.getElementById('autoSaveStatus');
        if (autoSaveStatus) {
            autoSaveStatus.innerHTML = '<i class="fas fa-clock text-warning"></i> Salvando...';
            
            // Simular auto-save após 2 segundos
            setTimeout(() => {
                autoSaveStatus.innerHTML = '<i class="fas fa-check-circle text-success"></i> Salvo';
            }, 2000);
        }
    }
    
    // Undo
    window.undoCode = function() {
        if (undoHistory.length === 0) {
            showToast('Nada para desfazer', 'info');
            return;
        }
        
        redoHistory.push(currentCode);
        currentCode = undoHistory.pop();
        codeEditor.value = currentCode;
        updateLineNumbers();
        updateEditorStatus();
        showToast('Ação desfeita', 'success');
    };
    
    // Redo
    window.redoCode = function() {
        if (redoHistory.length === 0) {
            showToast('Nada para refazer', 'info');
            return;
        }
        
        undoHistory.push(currentCode);
        currentCode = redoHistory.pop();
        codeEditor.value = currentCode;
        updateLineNumbers();
        updateEditorStatus();
        showToast('Ação refeita', 'success');
    };
    
    // Copiar código
    window.copyCode = function() {
        if (!codeEditor) return;
        
        codeEditor.select();
        document.execCommand('copy');
        
        // Remover seleção
        window.getSelection().removeAllRanges();
        
        showToast('Código copiado!', 'success');
    };

    // Temas do editor
    if (themeSelect) {
        themeSelect.addEventListener('change', function() {
            applyEditorTheme(this.value);
        });
    }

    function applyEditorTheme(theme) {
        if (!codeEditor) return;
        
        // Remover todas as classes de tema
        codeEditor.className = codeEditor.className.replace(/theme-\w+/g, '').trim();
        
        // Adicionar nova classe de tema
        codeEditor.classList.add('code-editor', `theme-${theme}`);
        
        showToast(`Tema ${theme} aplicado`, 'info');
    }
    
    // Tamanho da fonte
    if (fontSizeSelect) {
        fontSizeSelect.addEventListener('change', function() {
            changeFontSize(this.value);
        });
    }

    window.changeFontSize = function(size) {
        if (!codeEditor) return;
        
        codeEditor.style.fontSize = size + 'px';
        showToast(`Fonte alterada para ${size}px`, 'info');
    };
    
    // Formatar código
    window.formatCode = function() {
        if (!codeEditor) return;
        
        try {
            // Formatação básica para JavaScript
            let code = codeEditor.value;
            
            // Adicionar quebras de linha após {
            code = code.replace(/\{/g, '{\n');
            
            // Adicionar quebras de linha antes de }
            code = code.replace(/\}/g, '\n}');
            
            // Remover linhas vazias extras
            code = code.replace(/\n\s*\n/g, '\n');
            
            // Indentação básica
            const lines = code.split('\n');
            let indentLevel = 0;
            const indentedLines = lines.map(line => {
                const trimmed = line.trim();
                if (trimmed === '') return '';
                
                if (trimmed.includes('}')) indentLevel--;
                const indented = '  '.repeat(Math.max(0, indentLevel)) + trimmed;
                if (trimmed.includes('{')) indentLevel++;
                
                return indented;
            });
            
            codeEditor.value = indentedLines.join('\n');
            updateLineNumbers();
            updateEditorStatus();
            showToast('Código formatado!', 'success');
        } catch (error) {
            showToast('Erro ao formatar código', 'danger');
        }
    };
    
    // Toggle números de linha
    window.toggleLineNumbers = function() {
        const sidebar = document.querySelector('.editor-sidebar');
        if (sidebar) {
            sidebar.style.display = sidebar.style.display === 'none' ? 'block' : 'none';
            showToast('Números de linha ' + (sidebar.style.display === 'none' ? 'ocultos' : 'exibidos'), 'info');
        }
    };
    
    // Toggle fullscreen
    window.toggleFullscreenEditor = function() {
        const editorContainer = document.querySelector('.editor-container');
        if (!editorContainer) return;
        
        if (!document.fullscreenElement) {
            editorContainer.requestFullscreen().then(() => {
                editorContainer.classList.add('fullscreen-editor');
                showToast('Modo tela cheia ativado (ESC para sair)', 'info');
            });
        } else {
            document.exitFullscreen().then(() => {
                editorContainer.classList.remove('fullscreen-editor');
                showToast('Modo tela cheia desativado', 'info');
            });
        }
    };
});

let hintsUsed = 0;
let startTime = Date.now();

// Executar código adaptativo
window.executeCodeAdaptive = function() {
    const code = document.getElementById('user_code').value;
    const exerciseId = <?php echo $id; ?>;

    if (!code.trim()) {
        showToast('O editor está vazio!', 'warning');
        return;
    }

    showToast('Executando código...', 'info');
    
    fetch('execute_exercise.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            exercise_id: exerciseId,
            user_code: code,
            hints_used: hintsUsed,
            start_time: Math.floor(startTime / 1000)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayExecutionResult(data);
            
            if (data.mastery_score !== undefined) {
                const masteryPercent = Math.round(data.mastery_score * 100);
                showToast(`Maestria: ${masteryPercent}% | ${data.learning_suggestion || ''}`, 'info');
            }
            
            if (data.new_achievements && data.new_achievements.length > 0) {
                showAchievements(data.new_achievements, data.coins_earned);
            }
            
            showToast(`Código executado! Score: ${data.execution_result.score}`, 'success');
        } else {
            showToast('Erro: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showToast('Erro de conexão', 'danger');
    });
};

// Executar código (compatibilidade)
window.executeCode = function() {
    const code = document.getElementById('user_code').value;
    const exerciseId = <?php echo $id; ?>;

    if (!code.trim()) {
        showToast('O editor está vazio!', 'warning');
        return;
    }

    showToast('Executando código...', 'info');
    
    // Fazer requisição para o backend
    fetch('execute_exercise.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            exercise_id: exerciseId,
            user_code: code
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayExecutionResult(data);
            
            // Mostrar conquistas se houver
            if (data.new_achievements && data.new_achievements.length > 0) {
                showAchievements(data.new_achievements, data.coins_earned);
            }
            
            showToast(`Código executado! Score: ${data.execution_result.score}`, 'success');
        } else {
            showToast('Erro: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro de conexão', 'danger');
    });
};

function displayExecutionResult(data) {
    const result = data.execution_result;
    const tests = data.test_results;
    
    const outputTab = document.getElementById('output');
    
    let testsHTML = '';
    tests.forEach(test => {
        testsHTML += `
            <div class="test-result-item ${test.passed ? 'passed' : 'failed'}">
                <div class="test-status">
                    <i class="fas fa-${test.passed ? 'check' : 'times'}"></i>
                </div>
                <div class="test-info">
                    <div class="test-name">${test.name}</div>
                    <div class="test-message">${test.message}</div>
                </div>
            </div>
        `;
    });
    
    outputTab.innerHTML = `
        <div class="execution-result">
            <div class="result-header">
                <div class="alert alert-${result.success ? 'success' : 'danger'} mb-0">
                    <h6 class="mb-3"><i class="fas fa-${result.success ? 'check' : 'times'}-circle me-2"></i>${result.success ? 'Execução Bem-sucedida!' : 'Erro na Execução!'}</h6>
                    <div class="execution-metrics">
                        <div class="metric">
                            <span class="metric-label">Tempo</span>
                            <span class="metric-value">${result.execution_time}</span>
                        </div>
                        <div class="metric">
                            <span class="metric-label">Memória</span>
                            <span class="metric-value">${result.memory_used}</span>
                        </div>
                        <div class="metric">
                            <span class="metric-label">Score</span>
                            <span class="metric-value">${result.score}/100</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="output-container">
                <h6><i class="fas fa-terminal me-2"></i>Saída do Console</h6>
                <div class="output-content">
                    <code>${result.output}</code>
                </div>
            </div>
            <div class="test-results">
                <h6><i class="fas fa-vial me-2"></i>Resultados dos Testes</h6>
                <div class="tests-grid">
                    ${testsHTML}
                </div>
            </div>
        </div>
    `;
    
    new bootstrap.Tab(document.getElementById('output-tab')).show();
}

function showAchievements(achievements, coinsEarned) {
    let achievementsHTML = '';
    
    achievements.forEach(achievement => {
        achievementsHTML += `
            <div class="achievement-item">
                <div class="achievement-icon">
                    <i class="${achievement.icon} ${achievement.color}"></i>
                </div>
                <div class="achievement-content">
                    <h6>${achievement.name}</h6>
                    <p>${achievement.description}</p>
                    <span class="coins">+${achievement.coins_reward} <i class="fas fa-coins"></i></span>
                </div>
            </div>
        `;
    });
    
    // Criar modal de conquistas
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-trophy me-2"></i>
                        Parabéns! Novas Conquistas!
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="coins-earned">
                            <i class="fas fa-coins fa-3x text-warning mb-2"></i>
                            <h4>+${coinsEarned} Moedas Ganhas!</h4>
                        </div>
                    </div>
                    <div class="achievements-list">
                        ${achievementsHTML}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                        <i class="fas fa-check me-1"></i>Continuar
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    // Remover modal após fechar
    modal.addEventListener('hidden.bs.modal', () => {
        document.body.removeChild(modal);
    });
}

// Executar testes
window.runTests = function() {
    showToast('Executando testes...', 'info');
    
    setTimeout(() => {
        executeCode(); // Por enquanto, apenas executa o código
        showToast('Testes executados!', 'success');
    }, 1000);
};

// Salvar código
window.saveCode = function() {
    const code = document.getElementById('user_code').value;
    const id = <?php echo $id; ?>;
    localStorage.setItem('exercise_code_' + id, code);
    showToast('Código salvo localmente!', 'success');
};

// Resetar código
window.resetCode = function() {
    if (confirm('Deseja resetar o código para o estado inicial?')) {
        document.getElementById('user_code').value = '<?php echo addslashes(sanitize($exercise['initial_code'] ?? '')); ?>';
        showToast('Código resetado!', 'info');
    }
};

// Download código
window.downloadCode = function() {
    const code = document.getElementById('user_code').value;
    const element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(code));
    element.setAttribute('download', 'solution.js');
    element.style.display = 'none';
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
    showToast('Código baixado!', 'success');
};

// Desafio aleatório
window.loadRandomChallenge = function() {
    showToast('Carregando desafio aleatório...', 'info');
    
    // Simular carregamento
    setTimeout(() => {
        const challenges = [
            'function soma(a, b) {\n  // Retorne a soma de a e b\n  \n}',
            'function isPar(num) {\n  // Retorne true se num for par\n  \n}',
            'function reverseString(str) {\n  // Retorne a string invertida\n  \n}'
        ];
        
        const randomChallenge = challenges[Math.floor(Math.random() * challenges.length)];
        document.getElementById('user_code').value = randomChallenge;
        showToast('Novo desafio carregado!', 'success');
    }, 1000);
};

// Toggle fullscreen geral
window.toggleFullscreen = function() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
        showToast('Modo tela cheia ativado', 'info');
    } else {
        document.exitFullscreen();
        showToast('Modo tela cheia desativado', 'info');
    }
};

// Função toast
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}

// Carregar código salvo ao inicializar
document.addEventListener('DOMContentLoaded', function() {
    const id = <?php echo $id; ?>;
    const saved = localStorage.getItem('exercise_code_' + id);
    const codeEditor = document.getElementById('user_code');
    
    if (saved && codeEditor && codeEditor.value === codeEditor.defaultValue) {
        codeEditor.value = saved;
    }
});
</script>

<?php include 'footer.php'; ?>