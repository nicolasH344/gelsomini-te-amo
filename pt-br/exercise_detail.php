<?php<?php

/**require_once 'config.php';

 * Exercise Detail Page with Integrated Code Editorrequire_once 'exercise_functions.php';

 * Página Detalhada de Exercício com Editor de Código Integrado

 */$id = (int)($_GET['id'] ?? 0);

if (!$id) redirect('exercises_index.php');

require_once 'config.php';

require_once 'exercise_functions.php';$exercise = getExercise($id);

if (!$exercise) redirect('exercises_index.php');

// Validações iniciais

$id = (int)($_GET['id'] ?? 0);$title = $exercise['title'];

if (!$id) redirect('exercises_index.php');

// Processar submissão de código

// Buscar exercício$execution_result = null;

$exercise = getExercise($id);$test_results = [];

if (!$exercise) redirect('exercises_index.php');$user_code = $exercise['initial_code'] ?? '';



$title = $exercise['title'] ?? 'Exercício';if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['submit_code'])) {

// Obter código salvo ou usar padrão        $user_code = $_POST['user_code'] ?? '';

$user_code = isset($_SESSION['exercise_code_' . $id])         

    ? $_SESSION['exercise_code_' . $id]         // Simular execução e testes

    : ($exercise['initial_code'] ?? '// Comece aqui...\n');        $execution_result = [

            'success' => true,

// Incluir header            'output' => "✓ Código executado com sucesso!\n✓ Todos os testes passaram!",

include 'header.php';            'execution_time' => '0.45s',

?>            'memory_used' => '2.1MB'

        ];

<div class="container-fluid mt-4 mb-5">        

    <div class="row">        $test_results = [

        <!-- Main Content -->            ['name' => 'Teste de Sintaxe', 'passed' => true, 'message' => 'Código compilou sem erros'],

        <div class="col-lg-8">            ['name' => 'Teste de Saída', 'passed' => true, 'message' => 'Saída esperada encontrada'],

            <!-- Exercise Header -->            ['name' => 'Teste de Performance', 'passed' => true, 'message' => 'Dentro dos limites de tempo'],

            <div class="exercise-header mb-4">            ['name' => 'Teste de Casos Extremos', 'passed' => false, 'message' => 'Falha em caso de entrada vazia']

                <div class="d-flex gap-2 mb-3">        ];

                    <span class="badge bg-primary">        

                        <i class="fas fa-code me-1"></i>        $_SESSION['success'] = 'Código executado com sucesso!';

                        <?php echo strtoupper($exercise['difficulty'] ?? 'Iniciante'); ?>    }

                    </span>    

                    <span class="badge bg-success">    if (isset($_POST['reset_code'])) {

                        <i class="fas fa-graduation-cap me-1"></i>        $user_code = $exercise['initial_code'] ?? '';

                        <?php echo $exercise['category'] ?? 'Programação'; ?>    }

                    </span>    

                </div>    if (isset($_POST['save_progress'])) {

                <h1 class="exercise-title"><?php echo sanitize($title); ?></h1>        $_SESSION['success'] = 'Progresso salvo com sucesso!';

                <p class="exercise-desc"><?php echo sanitize($exercise['description'] ?? ''); ?></p>    }

            </div>}



            <!-- Content Tabs -->// Dados simulados para estatísticas

            <ul class="nav nav-tabs mb-4" role="tablist">$exercise_stats = [

                <li class="nav-item">    'attempts' => 1247,

                    <button class="nav-link active" id="instructions-tab" data-bs-toggle="tab" data-bs-target="#instructions" type="button">    'success_rate' => 68,

                        <i class="fas fa-book me-2"></i>Instruções    'avg_completion_time' => '15min',

                    </button>    'popularity' => 4.5,

                </li>    'user_attempts' => 3,

                <li class="nav-item">    'user_best_time' => '12min'

                    <button class="nav-link" id="editor-tab" data-bs-toggle="tab" data-bs-target="#editor" type="button">];

                        <i class="fas fa-code me-2"></i>Editor

                    </button>// Configuração de linguagens

                </li>$language_config = [

                <li class="nav-item">    'javascript' => [

                    <button class="nav-link" id="result-tab" data-bs-toggle="tab" data-bs-target="#result" type="button">        'icon' => 'fa-js-square',

                        <i class="fas fa-terminal me-2"></i>Resultado        'color' => '#f7df1e',

                    </button>        'name' => 'JavaScript'

                </li>    ],

                <li class="nav-item">    'html' => [

                    <button class="nav-link" id="solution-tab" data-bs-toggle="tab" data-bs-target="#solution" type="button">        'icon' => 'fa-html5',

                        <i class="fas fa-lightbulb me-2"></i>Solução        'color' => '#e34f26',

                    </button>        'name' => 'HTML5'

                </li>    ],

            </ul>    'css' => [

        'icon' => 'fa-css3-alt',

            <!-- Tab Content -->        'color' => '#1572b6',

            <div class="tab-content">        'name' => 'CSS3'

                <!-- Instructions Tab -->    ],

                <div class="tab-pane fade show active" id="instructions">    'php' => [

                    <div class="card shadow-sm border-0">        'icon' => 'fa-php',

                        <div class="card-body p-4">        'color' => '#777bb4',

                            <h5 class="mb-3"><i class="fas fa-target text-primary me-2"></i>O que fazer?</h5>        'name' => 'PHP'

                            <p class="lead"><?php echo nl2br(sanitize($exercise['instructions'] ?? 'Implemente a solução conforme os requisitos.')); ?></p>    ]

];

                            <?php if (isset($exercise['hints'])): ?>

                            <div class="alert alert-warning mt-4">// Exercícios relacionados por linguagem

                                <h6 class="alert-heading"><i class="fas fa-lightbulb me-2"></i>Dicas</h6>$related_exercises_by_language = [

                                <p class="mb-0"><?php echo nl2br(sanitize($exercise['hints'])); ?></p>    'javascript' => [

                            </div>        [

                            <?php endif; ?>            'id' => 2,

            'title' => 'Manipulação de Arrays',

                            <div class="alert alert-info mt-4">            'difficulty' => 'beginner',

                                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Como Proceder</h6>            'language' => 'javascript',

                                <ol class="mb-0">            'completion_rate' => 72

                                    <li>Leia as instruções acima</li>        ],

                                    <li>Acesse o "Editor" e escreva seu código</li>        [

                                    <li>Clique "Executar Código" para testar</li>            'id' => 3,

                                    <li>Verifique o resultado na aba "Resultado"</li>            'title' => 'Funções Assíncronas',

                                    <li>Consulte "Solução" se precisar de ajuda</li>            'difficulty' => 'intermediate',

                                </ol>            'language' => 'javascript',

                            </div>            'completion_rate' => 45

                        </div>        ],

                    </div>        [

                </div>            'id' => 4,

            'title' => 'Manipulação de DOM',

                <!-- Editor Tab -->            'difficulty' => 'beginner',

                <div class="tab-pane fade" id="editor">            'language' => 'javascript',

                    <div class="card shadow-sm border-0">            'completion_rate' => 81

                        <div class="card-header bg-light d-flex justify-content-between align-items-center">        ]

                            <span class="fw-bold"><i class="fas fa-file-code me-2"></i>Editor de Código</span>    ],

                            <div class="btn-group btn-group-sm">    'html' => [

                                <button type="button" class="btn btn-outline-secondary" onclick="copyCode()" title="Copiar">        [

                                    <i class="fas fa-copy"></i>            'id' => 5,

                                </button>            'title' => 'Formulários Avançados',

                                <button type="button" class="btn btn-outline-secondary" onclick="resetCode()" title="Resetar">            'difficulty' => 'intermediate',

                                    <i class="fas fa-redo"></i>            'language' => 'html',

                                </button>            'completion_rate' => 68

                            </div>        ],

                        </div>        [

                        <div class="card-body p-0">            'id' => 6,

                            <textarea id="codeEditor" class="code-editor" rows="20" spellcheck="false"><?php echo sanitize($user_code); ?></textarea>            'title' => 'Tabelas Responsivas',

                        </div>            'difficulty' => 'beginner',

                        <div class="card-footer bg-light">            'language' => 'html',

                            <div class="d-flex gap-2 flex-wrap">            'completion_rate' => 82

                                <button type="button" class="btn btn-success" onclick="runCode()">        ],

                                    <i class="fas fa-play me-2"></i>Executar Código        [

                                </button>            'id' => 7,

                                <button type="button" class="btn btn-info" onclick="runTests()">            'title' => 'Estrutura Semântica',

                                    <i class="fas fa-vial me-2"></i>Executar Testes            'difficulty' => 'intermediate',

                                </button>            'language' => 'html',

                                <button type="button" class="btn btn-outline-primary" onclick="saveCode()">            'completion_rate' => 59

                                    <i class="fas fa-save me-2"></i>Salvar        ]

                                </button>    ],

                            </div>    'css' => [

                        </div>        [

                    </div>            'id' => 8,

                </div>            'title' => 'Flexbox Layout',

            'difficulty' => 'beginner',

                <!-- Result Tab -->            'language' => 'css',

                <div class="tab-pane fade" id="result">            'completion_rate' => 76

                    <div class="card shadow-sm border-0">        ],

                        <div class="card-body p-4">        [

                            <div id="resultContent" class="text-center text-muted py-5">            'id' => 9,

                                <i class="fas fa-terminal fa-3x mb-3"></i>            'title' => 'Grid System',

                                <p>Clique em "Executar Código" para ver os resultados</p>            'difficulty' => 'intermediate',

                            </div>            'language' => 'css',

                        </div>            'completion_rate' => 54

                    </div>        ],

                </div>        [

            'id' => 10,

                <!-- Solution Tab -->            'title' => 'Animações CSS',

                <div class="tab-pane fade" id="solution">            'difficulty' => 'advanced',

                    <div class="card shadow-sm border-0">            'language' => 'css',

                        <div class="card-body p-4">            'completion_rate' => 41

                            <div class="alert alert-warning mb-4">        ]

                                <h6 class="alert-heading"><i class="fas fa-lightbulb me-2"></i>Antes de Ver a Solução...</h6>    ],

                                <p class="mb-0">Tente resolver o problema sozinho! É mais eficaz para aprender.</p>    'php' => [

                            </div>        [

            'id' => 11,

                            <button type="button" class="btn btn-primary btn-lg w-100 mb-4" onclick="toggleSolution()">            'title' => 'Validação de Dados',

                                <i class="fas fa-eye me-2"></i>Mostrar Solução Completa            'difficulty' => 'beginner',

                            </button>            'language' => 'php',

            'completion_rate' => 70

                            <div id="solutionContent" style="display: none;">        ],

                                <h5 class="mb-3"><i class="fas fa-code me-2"></i>Código de Solução</h5>        [

                                <div class="bg-light p-3 rounded mb-4" style="border-left: 4px solid #28a745; max-height: 400px; overflow-y: auto;">            'id' => 12,

                                    <pre id="solutionCode" class="mb-0"><code><?php echo sanitize($exercise['solution'] ?? '// Solução não disponível'); ?></code></pre>            'title' => 'Conexão com Banco',

                                </div>            'difficulty' => 'intermediate',

            'language' => 'php',

                                <h5 class="mb-3"><i class="fas fa-book me-2"></i>Explicação</h5>            'completion_rate' => 48

                                <div class="alert alert-light">        ],

                                    <p><?php echo sanitize($exercise['explanation'] ?? 'A solução segue as melhores práticas.'); ?></p>        [

                                </div>            'id' => 13,

            'title' => 'Upload de Arquivos',

                                <button type="button" class="btn btn-outline-primary" onclick="copySolution()">            'difficulty' => 'advanced',

                                    <i class="fas fa-copy me-2"></i>Copiar Solução            'language' => 'php',

                                </button>            'completion_rate' => 35

                            </div>        ]

                        </div>    ]

                    </div>];

                </div>

            </div>// Dicas por linguagem

        </div>$hints_by_language = [

    'javascript' => [

        <!-- Sidebar -->        'Use console.log() para debugar seu código e ver os valores das variáveis',

        <div class="col-lg-4">        'Lembre-se que arrays começam no índice 0',

            <!-- Exercise Info -->        'Funções podem retornar valores usando a palavra-chave return',

            <div class="card shadow-sm border-0 mb-4">        'Use let e const ao invés de var para declarar variáveis'

                <div class="card-header bg-primary text-white">    ],

                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informações</h6>    'html' => [

                </div>        'Use tags semânticas como <header>, <nav>, <main> e <footer>',

                <div class="card-body">        'Sempre adicione atributos alt em imagens para acessibilidade',

                    <div class="info-row">        'Use IDs para elementos únicos e classes para estilos reutilizáveis',

                        <span class="info-label">Dificuldade</span>        'Organize seu código com indentação adequada'

                        <span class="info-value"><?php echo $exercise['difficulty'] ?? 'Iniciante'; ?></span>    ],

                    </div>    'css' => [

                    <div class="info-row">        'Use Flexbox ou Grid para layouts responsivos modernos',

                        <span class="info-label">Categoria</span>        'Evite usar !important, organize melhor a especificidade',

                        <span class="info-value"><?php echo $exercise['category'] ?? 'Geral'; ?></span>        'Use variáveis CSS (--nome-variavel) para cores e medidas',

                    </div>        'Mobile-first: comece com estilos mobile e use media queries para desktop'

                    <div class="info-row">    ],

                        <span class="info-label">Taxa de Sucesso</span>    'php' => [

                        <span class="info-value"><?php echo rand(50, 95); ?>%</span>        'Sempre valide e sanitize dados de entrada do usuário',

                    </div>        'Use prepared statements para prevenir SQL injection',

                    <div class="info-row">        'Não exiba erros em produção, use logs ao invés',

                        <span class="info-label">Tempo Médio</span>        'Use password_hash() e password_verify() para senhas'

                        <span class="info-value"><?php echo rand(10, 60); ?> min</span>    ]

                    </div>];

                </div>

            </div>// Detectar linguagem do exercício atual

// Mapeamento direto por ID de exercício (enquanto o banco não tem o campo correto)

            <!-- Progress -->$exercise_language_map = [

            <div class="card shadow-sm border-0 mb-4">    1 => 'html',      // Minha Primeira Página HTML

                <div class="card-header bg-success text-white">    2 => 'html',      // Lista de Compras

                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Seu Progresso</h6>    3 => 'html',      // Formulário de Contato

                </div>    4 => 'css',       // Estilizando Texto

                <div class="card-body text-center">    5 => 'css',       // Layout com Flexbox

                    <div class="progress mb-3">    6 => 'javascript', // Olá Mundo JavaScript

                        <div class="progress-bar bg-success" style="width: 65%;">65%</div>    7 => 'javascript', // Calculadora Simples

                    </div>    8 => 'javascript', // Manipulação de Array

                    <p class="text-muted mb-0">2 de 3 objetivos concluídos</p>    9 => 'php',       // Olá Mundo PHP

                </div>];

            </div>

// Tentar detectar pela ID primeiro

            <!-- Quick Actions -->$exercise_id = (int)($exercise['id'] ?? 0);

            <div class="card shadow-sm border-0">$current_language = $exercise_language_map[$exercise_id] ?? null;

                <div class="card-header bg-info text-white">

                    <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Ações Rápidas</h6>// Se não encontrou por ID, tentar por category_name

                </div>if (!$current_language) {

                <div class="card-body">    $category_raw = strtolower(trim($exercise['category_name'] ?? ''));

                    <a href="exercises_index.php" class="btn btn-outline-primary w-100 mb-2">    

                        <i class="fas fa-arrow-left me-2"></i>Voltar    // Mapear variações de nomes para as chaves padrão

                    </a>    $language_mapping = [

                    <button type="button" class="btn btn-outline-secondary w-100 mb-2" onclick="shareExercise()">        'javascript' => 'javascript',

                        <i class="fas fa-share me-2"></i>Compartilhar        'js' => 'javascript',

                    </button>        'html' => 'html',

                    <button type="button" class="btn btn-outline-warning w-100">        'html5' => 'html',

                        <i class="fas fa-star me-2"></i>Favoritar        'css' => 'css',

                    </button>        'css3' => 'css',

                </div>        'php' => 'php',

            </div>        'php7' => 'php',

        </div>        'php8' => 'php',

    </div>        'python' => 'python',

</div>        'java' => 'java'

    ];

<style>    

.exercise-header {    // Detectar por palavra-chave no nome da categoria

    background: linear-gradient(135deg, #6f42c1 0%, #8e5dd9 100%);    foreach ($language_mapping as $key => $value) {

    color: white;        if (strpos($category_raw, $key) !== false) {

    padding: 2rem;            $current_language = $value;

    border-radius: 15px;            break;

    box-shadow: 0 8px 24px rgba(111, 66, 193, 0.25);        }

}    }

}

.exercise-title {

    font-size: 2rem;// Se ainda não detectou, tentar pelo exercise_type (dados mockados)

    font-weight: 700;if (!$current_language && !empty($exercise['exercise_type'])) {

    margin-bottom: 1rem;    $exercise_type_raw = strtolower(trim($exercise['exercise_type']));

    color: white;    $language_mapping = [

}        'javascript' => 'javascript',

        'js' => 'javascript',

.exercise-desc {        'html' => 'html',

    font-size: 1.1rem;        'html5' => 'html',

    opacity: 0.95;        'css' => 'css',

    line-height: 1.6;        'css3' => 'css',

    color: rgba(255, 255, 255, 0.9);        'php' => 'php',

}        'python' => 'python',

        'java' => 'java'

.code-editor {    ];

    width: 100%;    

    min-height: 400px;    foreach ($language_mapping as $key => $value) {

    padding: 1rem;        if (strpos($exercise_type_raw, $key) !== false) {

    border: none;            $current_language = $value;

    border-radius: 0;            break;

    font-family: 'Courier New', Courier, monospace;        }

    font-size: 14px;    }

    line-height: 1.6;}

    background: #1e1e1e;

    color: #d4d4d4;// Fallback final para javascript

    resize: vertical;$current_language = $current_language ?? 'javascript';

    outline: none;

    tab-size: 2;// Selecionar exercícios e dicas da linguagem atual

}$related_exercises = $related_exercises_by_language[$current_language] ?? $related_exercises_by_language['javascript'];

$current_hints = $hints_by_language[$current_language] ?? $hints_by_language['javascript'];

.code-editor::selection {$current_lang_config = $language_config[$current_language] ?? $language_config['javascript'];

    background: rgba(111, 66, 193, 0.3);

    color: #d4d4d4;include 'header.php';

}?>



.info-row {<div class="container-fluid mt-4">

    display: flex;    <!-- Alertas -->

    flex-direction: column;    <?php if (isset($_SESSION['success'])): ?>

    padding: 0.75rem 0;        <div class="alert alert-success alert-dismissible fade show" role="alert">

    border-bottom: 1px solid #f0f0f0;            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>

}            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>

.info-row:last-child {    <?php endif; ?>

    border-bottom: none;

}    <div class="row">

        <!-- Conteúdo Principal -->

.info-label {        <div class="col-lg-8">

    font-size: 0.75rem;            <!-- Cabeçalho do Exercício -->

    font-weight: 600;            <div class="exercise-header-card mb-4">

    color: #6c757d;                <div class="row align-items-center">

    text-transform: uppercase;                    <div class="col-md-8">

    letter-spacing: 0.5px;                        <div class="d-flex align-items-center mb-2">

}                            <span class="exercise-type-badge me-2">

                                <i class="fas fa-code me-1"></i>

.info-value {                                <?php echo strtoupper($exercise['exercise_type'] ?? 'PRÁTICA'); ?>

    font-size: 1rem;                            </span>

    font-weight: 700;                            <span class="difficulty-badge difficulty-<?php echo $exercise['difficulty_level'] ?? 'beginner'; ?>">

    color: #1a1a1a;                                <?php 

}                                $difficulty_map = [

                                    'beginner' => 'Iniciante', 

.nav-tabs .nav-link {                                    'intermediate' => 'Intermediário', 

    border-bottom: 3px solid transparent;                                    'advanced' => 'Avançado'

    transition: all 0.3s ease;                                ];

    color: #1a1a1a;                                $difficulty_level = $exercise['difficulty_level'] ?? 'beginner';

}                                echo $difficulty_map[$difficulty_level] ?? 'Iniciante';

                                ?>

.nav-tabs .nav-link:hover {                            </span>

    border-bottom-color: #6f42c1;                        </div>

    color: #6f42c1;                        <h1 class="display-5 fw-bold mb-3 exercise-title"><?php echo sanitize($exercise['title'] ?? 'Exercício'); ?></h1>

}                        <p class="lead exercise-description mb-3"><?php echo sanitize($exercise['description'] ?? 'Descrição do exercício'); ?></p>

                        

.nav-tabs .nav-link.active {                        <!-- Metadados -->

    border-bottom-color: #6f42c1;                        <div class="metadata-grid">

    color: #6f42c1;                            <div class="metadata-card metadata-category">

    background: transparent;                                <div class="metadata-icon" style="color: <?php echo $current_lang_config['color']; ?>">

}                                    <i class="fab <?php echo $current_lang_config['icon']; ?>"></i>

                                </div>

.tab-content {                                <div class="metadata-info">

    animation: fadeIn 0.3s ease-out;                                    <span class="metadata-label">Linguagem</span>

}                                    <span class="metadata-value"><?php echo $current_lang_config['name']; ?></span>

                                </div>

@keyframes fadeIn {                            </div>

    from { opacity: 0; transform: translateY(10px); }                            <div class="metadata-card metadata-time">

    to { opacity: 1; transform: translateY(0); }                                <div class="metadata-icon">

}                                    <i class="fas fa-clock"></i>

                                </div>

.card {                                <div class="metadata-info">

    transition: all 0.3s ease;                                    <span class="metadata-label">Tempo Médio</span>

}                                    <span class="metadata-value"><?php echo $exercise_stats['avg_completion_time']; ?></span>

                                </div>

.card:hover {                            </div>

    transform: translateY(-2px);                            <div class="metadata-card metadata-success">

}                                <div class="metadata-icon">

                                    <i class="fas fa-chart-line"></i>

.btn {                                </div>

    transition: all 0.3s ease;                                <div class="metadata-info">

}                                    <span class="metadata-label">Taxa de Sucesso</span>

                                    <span class="metadata-value"><?php echo $exercise_stats['success_rate']; ?>%</span>

.btn:hover {                                </div>

    transform: translateY(-1px);                            </div>

}                            <div class="metadata-card metadata-attempts">

                                <div class="metadata-icon">

@media (max-width: 768px) {                                    <i class="fas fa-users"></i>

    .exercise-title { font-size: 1.5rem; }                                </div>

    .code-editor { min-height: 250px; }                                <div class="metadata-info">

}                                    <span class="metadata-label">Tentativas</span>

</style>                                    <span class="metadata-value"><?php echo number_format($exercise_stats['attempts'], 0, ',', '.'); ?></span>

                                </div>

<script>                            </div>

// Code Editor Functions                        </div>

function runCode() {                    </div>

    const code = document.getElementById('codeEditor').value;                    <div class="col-md-4 text-end">

    const resultContent = document.getElementById('resultContent');                        <div class="progress-ring-container">

                                <div class="progress-ring">

    if (!code.trim()) {                                <svg width="120" height="120" viewBox="0 0 120 120">

        notify('O editor está vazio!', 'warning');                                    <circle class="progress-ring-bg" 

        return;                                            stroke="rgba(255,255,255,0.2)" 

    }                                            stroke-width="8" 

                                                fill="transparent" 

    try {                                            r="52" 

        let output = '';                                            cx="60" 

        const originalLog = console.log;                                            cy="60">

        console.log = function(...args) {                                    </circle>

            output += args.join(' ') + '\n';                                    <circle class="progress-ring-circle" 

        };                                            stroke="url(#gradient)" 

                                                    stroke-width="8" 

        eval(code);                                            fill="transparent" 

        console.log = originalLog;                                            r="52" 

                                                    cx="60" 

        resultContent.innerHTML = `                                            cy="60"

            <div class="alert alert-success mb-0">                                            stroke-dasharray="326.56" 

                <h6 class="mb-3"><i class="fas fa-check-circle me-2"></i>Sucesso!</h6>                                            stroke-dashoffset="<?php echo 326.56 * (1 - $exercise_stats['success_rate'] / 100); ?>"

                <pre style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin: 0;">                                            stroke-linecap="round">

<code>${output || 'Nenhuma saída'}</code></pre>                                    </circle>

            </div>                                    <defs>

        `;                                        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">

                                                    <stop offset="0%" style="stop-color:#06d6a0;stop-opacity:1" />

        new bootstrap.Tab(document.getElementById('result-tab')).show();                                            <stop offset="100%" style="stop-color:#1b9aaa;stop-opacity:1" />

        notify('Código executado!', 'success');                                        </linearGradient>

    } catch (error) {                                    </defs>

        resultContent.innerHTML = `                                </svg>

            <div class="alert alert-danger mb-0">                                <div class="progress-text">

                <h6 class="mb-3"><i class="fas fa-times-circle me-2"></i>Erro!</h6>                                    <span class="progress-percentage"><?php echo $exercise_stats['success_rate']; ?>%</span>

                <p class="mb-2"><strong>${error.name}:</strong> ${error.message}</p>                                    <small class="progress-label">Taxa de Sucesso</small>

                <pre style="background: #fff5f5; padding: 1rem; border-radius: 8px; margin: 0; font-size: 0.85rem;">                                </div>

<code>${error.stack}</code></pre>                            </div>

            </div>                        </div>

        `;                    </div>

                        </div>

        new bootstrap.Tab(document.getElementById('result-tab')).show();            </div>

        notify('Erro na execução!', 'danger');

    }            <!-- Área de Trabalho -->

}            <div class="workspace-container">

                <!-- Navegação por Abas -->

function runTests() {                <ul class="nav nav-tabs workspace-tabs" role="tablist">

    notify('Testes não disponíveis nesta versão', 'info');                    <li class="nav-item" role="presentation">

}                        <button class="nav-link active" id="instructions-tab" data-bs-toggle="tab" data-bs-target="#instructions" type="button" role="tab" aria-controls="instructions" aria-selected="true">

                            <i class="fas fa-book-open me-2"></i>

function saveCode() {                            <span>Instruções</span>

    const code = document.getElementById('codeEditor').value;                            <div class="tab-indicator"></div>

    const id = <?php echo $id; ?>;                        </button>

    localStorage.setItem('exercise_code_' + id, code);                    </li>

    notify('Código salvo!', 'success');                    <li class="nav-item" role="presentation">

}                        <button class="nav-link" id="editor-tab" data-bs-toggle="tab" data-bs-target="#editor" type="button" role="tab" aria-controls="editor" aria-selected="false">

                            <i class="fas fa-code me-2"></i>

function copyCode() {                            <span>Editor</span>

    const code = document.getElementById('codeEditor');                            <div class="tab-indicator"></div>

    code.select();                        </button>

    document.execCommand('copy');                    </li>

    notify('Código copiado!', 'success');                    <li class="nav-item" role="presentation">

}                        <button class="nav-link" id="output-tab" data-bs-toggle="tab" data-bs-target="#output" type="button" role="tab" aria-controls="output" aria-selected="false">

                            <i class="fas fa-terminal me-2"></i>

function resetCode() {                            <span>Resultado</span>

    if (confirm('Deseja resetar o código?')) {                            <?php if ($execution_result): ?>

        document.getElementById('codeEditor').value = '<?php echo addslashes(sanitize($exercise['initial_code'] ?? '')); ?>';                                <span class="result-badge badge-<?php echo $execution_result['success'] ? 'success' : 'danger'; ?>">

        notify('Código resetado!', 'info');                                    <i class="fas fa-<?php echo $execution_result['success'] ? 'check-circle' : 'times-circle'; ?>"></i>

    }                                </span>

}                            <?php endif; ?>

                            <div class="tab-indicator"></div>

function toggleSolution() {                        </button>

    const content = document.getElementById('solutionContent');                    </li>

    content.style.display = content.style.display === 'none' ? 'block' : 'none';                    <li class="nav-item" role="presentation">

    if (content.style.display === 'block') {                        <button class="nav-link" id="solution-tab" data-bs-toggle="tab" data-bs-target="#solution" type="button" role="tab" aria-controls="solution" aria-selected="false">

        notify('Solução revelada!', 'success');                            <i class="fas fa-lightbulb me-2"></i>

    }                            <span>Solução</span>

}                            <div class="tab-indicator"></div>

                        </button>

function copySolution() {                    </li>

    const code = document.getElementById('solutionCode').textContent;                </ul>

    const textarea = document.createElement('textarea');

    textarea.value = code;                <div class="tab-content workspace-content">

    document.body.appendChild(textarea);                    <!-- Aba Instruções -->

    textarea.select();                    <div class="tab-pane fade show active" id="instructions" role="tabpanel" aria-labelledby="instructions-tab">

    document.execCommand('copy');                        <div class="instructions-content">

    document.body.removeChild(textarea);                            <section class="instruction-section">

    notify('Solução copiada!', 'success');                                <h4 class="section-title">

}                                    <i class="fas fa-target text-primary me-2"></i>

                                    Objetivo do Exercício

function shareExercise() {                                </h4>

    notify('Compartilhamento não disponível nesta versão', 'info');                                <div class="instruction-card">

}                                    <p><?php echo nl2br(sanitize($exercise['instructions'] ?? 'Instruções não disponíveis.')); ?></p>

                                </div>

function notify(msg, type = 'info') {                            </section>

    const alert = document.createElement('div');

    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;                            <?php if (!empty($exercise['hints'])): ?>

    alert.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';                            <section class="instruction-section">

    alert.innerHTML = `${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;                                <h4 class="section-title">

    document.body.appendChild(alert);                                    <i class="fas fa-lightbulb text-warning me-2"></i>

    setTimeout(() => alert.remove(), 3000);                                    Dicas

}                                </h4>

                                <div class="hints-container">

// Support Tab key in editor                                    <?php 

document.addEventListener('DOMContentLoaded', function() {                                    $hints = explode("\n", $exercise['hints']);

    const id = <?php echo $id; ?>;                                    foreach ($hints as $index => $hint): 

    const saved = localStorage.getItem('exercise_code_' + id);                                        if (trim($hint)):

    if (saved && document.getElementById('codeEditor').value === document.getElementById('codeEditor').defaultValue) {                                    ?>

        document.getElementById('codeEditor').value = saved;                                    <div class="hint-item">

    }                                        <div class="hint-number"><?php echo $index + 1; ?></div>

                                            <div class="hint-content"><?php echo sanitize($hint); ?></div>

    document.getElementById('codeEditor').addEventListener('keydown', function(e) {                                    </div>

        if (e.key === 'Tab') {                                    <?php 

            e.preventDefault();                                        endif;

            const start = this.selectionStart;                                    endforeach; 

            const end = this.selectionEnd;                                    ?>

            this.value = this.value.substring(0, start) + '\t' + this.value.substring(end);                                </div>

            this.selectionStart = this.selectionEnd = start + 1;                            </section>

        }                            <?php endif; ?>

    });

});                            <section class="instruction-section">

</script>                                <h4 class="section-title">

                                    <i class="fas fa-list-check text-success me-2"></i>

<?php include 'footer.php'; ?>                                    Requisitos

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

                            <section class="instruction-section">
                                <h4 class="section-title">
                                    <i class="fas fa-vial text-info me-2"></i>
                                    Casos de Teste
                                    <span class="test-summary">3 de 3 passando</span>
                                </h4>
                                <div class="test-cases">
                                    <div class="test-case test-passed" onclick="toggleTestDetails(this)">
                                        <div class="test-case-header">
                                            <div class="test-info">
                                                <div class="test-icon">
                                                    <i class="fas fa-check-circle"></i>
                                                </div>
                                                <div class="test-title-group">
                                                    <span class="test-case-name">Caso Básico</span>
                                                    <span class="test-case-desc">Teste com array de números positivos</span>
                                                </div>
                                            </div>
                                            <div class="test-status">
                                                <span class="badge badge-success-custom">
                                                    <i class="fas fa-check me-1"></i>Passa
                                                </span>
                                                <i class="fas fa-chevron-down toggle-icon"></i>
                                            </div>
                                        </div>
                                        <div class="test-case-content">
                                            <div class="test-io-grid">
                                                <div class="test-io-item">
                                                    <label class="test-label">Entrada</label>
                                                    <code class="test-code">[1, 2, 3, 4, 5]</code>
                                                </div>
                                                <div class="test-io-item">
                                                    <label class="test-label">Saída Esperada</label>
                                                    <code class="test-code success">15</code>
                                                </div>
                                            </div>
                                            <div class="test-details">
                                                <div class="test-metric">
                                                    <i class="fas fa-clock"></i>
                                                    <span>Tempo: <strong>0.02ms</strong></span>
                                                </div>
                                                <div class="test-metric">
                                                    <i class="fas fa-memory"></i>
                                                    <span>Memória: <strong>1.2KB</strong></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="test-case test-partial" onclick="toggleTestDetails(this)">
                                        <div class="test-case-header">
                                            <div class="test-info">
                                                <div class="test-icon">
                                                    <i class="fas fa-exclamation-circle"></i>
                                                </div>
                                                <div class="test-title-group">
                                                    <span class="test-case-name">Array Vazio</span>
                                                    <span class="test-case-desc">Verificação de casos extremos</span>
                                                </div>
                                            </div>
                                            <div class="test-status">
                                                <span class="badge badge-warning-custom">
                                                    <i class="fas fa-minus-circle me-1"></i>Parcial
                                                </span>
                                                <i class="fas fa-chevron-down toggle-icon"></i>
                                            </div>
                                        </div>
                                        <div class="test-case-content">
                                            <div class="test-io-grid">
                                                <div class="test-io-item">
                                                    <label class="test-label">Entrada</label>
                                                    <code class="test-code">[]</code>
                                                </div>
                                                <div class="test-io-item">
                                                    <label class="test-label">Saída Esperada</label>
                                                    <code class="test-code warning">0</code>
                                                </div>
                                            </div>
                                            <div class="test-warning">
                                                <i class="fas fa-info-circle"></i>
                                                <span>Verifique o tratamento de arrays vazios</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="test-case test-passed" onclick="toggleTestDetails(this)">
                                        <div class="test-case-header">
                                            <div class="test-info">
                                                <div class="test-icon">
                                                    <i class="fas fa-check-circle"></i>
                                                </div>
                                                <div class="test-title-group">
                                                    <span class="test-case-name">Números Negativos</span>
                                                    <span class="test-case-desc">Soma com valores negativos</span>
                                                </div>
                                            </div>
                                            <div class="test-status">
                                                <span class="badge badge-success-custom">
                                                    <i class="fas fa-check me-1"></i>Passa
                                                </span>
                                                <i class="fas fa-chevron-down toggle-icon"></i>
                                            </div>
                                        </div>
                                        <div class="test-case-content">
                                            <div class="test-io-grid">
                                                <div class="test-io-item">
                                                    <label class="test-label">Entrada</label>
                                                    <code class="test-code">[-1, 2, -3, 4]</code>
                                                </div>
                                                <div class="test-io-item">
                                                    <label class="test-label">Saída Esperada</label>
                                                    <code class="test-code success">2</code>
                                                </div>
                                            </div>
                                            <div class="test-details">
                                                <div class="test-metric">
                                                    <i class="fas fa-clock"></i>
                                                    <span>Tempo: <strong>0.03ms</strong></span>
                                                </div>
                                                <div class="test-metric">
                                                    <i class="fas fa-memory"></i>
                                                    <span>Memória: <strong>1.4KB</strong></span>
                                                </div>
                                            </div>
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
                                <button type="button" onclick="executeCode()" class="btn btn-success btn-lg">
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
                                        <pre><code class="language-javascript" id="solutionCode">// Exemplo de solução otimizada
function somaArray(arr) {
  // Usa reduce para somar todos os elementos
  return arr.reduce((acc, num) => acc + num, 0);
}

// Teste da função
console.log(somaArray([1, 2, 3, 4, 5])); // 15</code></pre>
                                    </div>
                                </div>

                                <!-- Explicação Passo a Passo -->
                                <div class="solution-explanation-section">
                                    <h5 class="explanation-title">
                                        <i class="fas fa-book-open me-2"></i>
                                        Explicação Passo a Passo
                                    </h5>
                                    
                                    <div class="explanation-steps">
                                        <div class="explanation-step">
                                            <div class="step-number">1</div>
                                            <div class="step-content">
                                                <h6 class="step-title">Análise do Problema</h6>
                                                <p class="step-description">
                                                    Primeiro, identificamos que precisamos percorrer todos os elementos 
                                                    do array e acumular seus valores. O método <code>reduce()</code> é 
                                                    perfeito para essa tarefa.
                                                </p>
                                            </div>
                                        </div>

                                        <div class="explanation-step">
                                            <div class="step-number">2</div>
                                            <div class="step-content">
                                                <h6 class="step-title">Implementação</h6>
                                                <p class="step-description">
                                                    Usamos <code>arr.reduce((acc, num) => acc + num, 0)</code> onde:
                                                    <br>• <code>acc</code> é o acumulador (soma parcial)
                                                    <br>• <code>num</code> é o elemento atual
                                                    <br>• <code>0</code> é o valor inicial
                                                </p>
                                            </div>
                                        </div>

                                        <div class="explanation-step">
                                            <div class="step-number">3</div>
                                            <div class="step-content">
                                                <h6 class="step-title">Complexidade</h6>
                                                <p class="step-description">
                                                    <strong>Tempo:</strong> O(n) - percorre o array uma vez
                                                    <br><strong>Espaço:</strong> O(1) - usa memória constante
                                                </p>
                                            </div>
                                        </div>

                                        <div class="explanation-step">
                                            <div class="step-number">4</div>
                                            <div class="step-content">
                                                <h6 class="step-title">Alternativas</h6>
                                                <p class="step-description">
                                                    Outras abordagens válidas incluem usar um loop <code>for</code> 
                                                    ou <code>forEach()</code>, mas <code>reduce()</code> é mais 
                                                    funcional e conciso.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Conceitos Importantes -->
                                <div class="solution-concepts-section">
                                    <h5 class="concepts-title">
                                        <i class="fas fa-graduation-cap me-2"></i>
                                        Conceitos Importantes
                                    </h5>
                                    
                                    <div class="concepts-grid">
                                        <div class="concept-card">
                                            <div class="concept-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                                                <i class="fas fa-stream"></i>
                                            </div>
                                            <h6 class="concept-name">Array.reduce()</h6>
                                            <p class="concept-description">
                                                Método que reduz um array a um único valor através de uma função acumuladora.
                                            </p>
                                        </div>

                                        <div class="concept-card">
                                            <div class="concept-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                                                <i class="fas fa-arrow-right"></i>
                                            </div>
                                            <h6 class="concept-name">Arrow Functions</h6>
                                            <p class="concept-description">
                                                Sintaxe concisa para criar funções anônimas em JavaScript.
                                            </p>
                                        </div>

                                        <div class="concept-card">
                                            <div class="concept-icon" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                                                <i class="fas fa-layer-group"></i>
                                            </div>
                                            <h6 class="concept-name">Acumuladores</h6>
                                            <p class="concept-description">
                                                Variável que armazena o resultado parcial durante iterações.
                                            </p>
                                        </div>

                                        <div class="concept-card">
                                            <div class="concept-icon" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                                                <i class="fas fa-code-branch"></i>
                                            </div>
                                            <h6 class="concept-name">Programação Funcional</h6>
                                            <p class="concept-description">
                                                Paradigma que usa funções puras e evita mutações de estado.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recursos Adicionais -->
                                <div class="solution-resources-section">
                                    <h5 class="resources-title">
                                        <i class="fas fa-link me-2"></i>
                                        Recursos para Aprender Mais
                                    </h5>
                                    
                                    <div class="resources-list">
                                        <a href="#" class="resource-item" target="_blank">
                                            <div class="resource-icon">📚</div>
                                            <div class="resource-content">
                                                <strong>MDN Web Docs - Array.reduce()</strong>
                                                <span>Documentação oficial sobre o método reduce</span>
                                            </div>
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>

                                        <a href="#" class="resource-item" target="_blank">
                                            <div class="resource-icon">🎥</div>
                                            <div class="resource-content">
                                                <strong>JavaScript Array Methods</strong>
                                                <span>Tutorial em vídeo sobre métodos de array</span>
                                            </div>
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>

                                        <a href="#" class="resource-item" target="_blank">
                                            <div class="resource-icon">💻</div>
                                            <div class="resource-content">
                                                <strong>Exercícios Práticos</strong>
                                                <span>Mais exercícios similares para praticar</span>
                                            </div>
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
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
                
                <?php if ($exercise['hints']): ?>
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

<!-- Modal de Tema do Editor -->
<div class="modal fade" id="themeModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tema do Editor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="theme-options">
                    <div class="theme-option active" data-theme="default">
                        <div class="theme-preview light-theme"></div>
                        <span>Claro</span>
                    </div>
                    <div class="theme-option" data-theme="dark">
                        <div class="theme-preview dark-theme"></div>
                        <span>Escuro</span>
                    </div>
                    <div class="theme-option" data-theme="monokai">
                        <div class="theme-preview monokai-theme"></div>
                        <span>Monokai</span>
                    </div>
                </div>
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

.metadata-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
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

@keyframes celebrate {
    0% {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.5) rotate(0deg);
    }
    50% {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1.5) rotate(180deg);
    }
    100% {
        opacity: 0;
        transform: translate(-50%, -50%) scale(2) rotate(360deg);
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

.test-cases {
    display: grid;
    gap: 1rem;
}

.test-summary {
    margin-left: auto;
    padding: 0.35rem 1rem;
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.3px;
}

.test-case {
    border: 2px solid #e9ecef;
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    background: white;
    cursor: pointer;
}

.test-case.test-passed {
    border-color: rgba(6, 214, 160, 0.3);
}

.test-case.test-partial {
    border-color: rgba(255, 193, 7, 0.3);
}

.test-case.test-failed {
    border-color: rgba(239, 71, 111, 0.3);
}

.test-case:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,0.12);
}

.test-case.test-passed:hover {
    border-color: #06d6a0;
    box-shadow: 0 12px 32px rgba(6, 214, 160, 0.2);
}

.test-case.test-partial:hover {
    border-color: #ffc107;
    box-shadow: 0 12px 32px rgba(255, 193, 7, 0.2);
}

.test-case-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-bottom: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.test-case:hover .test-case-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #fafbfc 100%);
}

.test-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1;
}

.test-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.test-passed .test-icon {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.test-partial .test-icon {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: white;
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
}

.test-failed .test-icon {
    background: linear-gradient(135deg, #dc3545, #e83e8c);
    color: white;
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.test-case:hover .test-icon {
    transform: scale(1.1) rotate(-5deg);
}

.test-title-group {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.test-case-name {
    font-weight: 700;
    font-size: 1rem;
    color: #212529;
    line-height: 1.2;
}

.test-case-desc {
    font-size: 0.8rem;
    color: #6c757d;
    font-weight: 500;
}

.test-status {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.badge-success-custom {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
}

.badge-warning-custom {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: #333;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
}

.badge-danger-custom {
    background: linear-gradient(135deg, #dc3545, #e83e8c);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
}

.toggle-icon {
    color: #6c757d;
    font-size: 0.9rem;
    transition: transform 0.3s ease;
}

.test-case.expanded .toggle-icon {
    transform: rotate(180deg);
}

.test-case-content {
    padding: 1.5rem;
    background: white;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), padding 0.4s ease;
}

.test-case.expanded .test-case-content {
    max-height: 500px;
    border-top: 2px solid #f0f0f0;
}

.test-io-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1rem;
}

.test-io-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.test-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #6c757d;
    letter-spacing: 0.5px;
}

.test-code {
    padding: 0.75rem 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    color: #212529;
    border: 2px solid #e9ecef;
    font-weight: 600;
}

.test-code.success {
    background: rgba(40, 167, 69, 0.1);
    border-color: rgba(40, 167, 69, 0.3);
    color: #155724;
}

.test-code.warning {
    background: rgba(255, 193, 7, 0.1);
    border-color: rgba(255, 193, 7, 0.3);
    color: #856404;
}

.test-code.error {
    background: rgba(220, 53, 69, 0.1);
    border-color: rgba(220, 53, 69, 0.3);
    color: #721c24;
}

.test-details {
    display: flex;
    gap: 2rem;
    padding: 1rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 8px;
    margin-top: 1rem;
}

.test-metric {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #6c757d;
}

.test-metric i {
    color: var(--primary-color);
}

.test-metric strong {
    color: #212529;
}

.test-warning {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    border-radius: 8px;
    margin-top: 1rem;
    font-size: 0.9rem;
    color: #856404;
}

.test-warning i {
    font-size: 1.2rem;
    color: #ffc107;
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

/* Temas do Editor */
.code-editor.theme-default {
    background: #ffffff;
    color: #24292e;
}

.code-editor.theme-dark {
    background: #1e1e1e;
    color: #d4d4d4;
}

.code-editor.theme-monokai {
    background: #272822;
    color: #f8f8f2;
}

.code-editor.theme-dracula {
    background: #282a36;
    color: #f8f8f2;
}

.code-editor.theme-github {
    background: #ffffff;
    color: #24292e;
}

.editor-suggestions {
    position: absolute;
    background: #252526;
    border: 1px solid #3e3e42;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    max-height: 200px;
    overflow-y: auto;
    display: none;
    z-index: 1000;
}

.suggestion-item {
    padding: 0.5rem 1rem;
    cursor: pointer;
    color: #d4d4d4;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    transition: background 0.2s;
}

.suggestion-item:hover {
    background: #2a2d2e;
}

.suggestion-item.active {
    background: #094771;
}

.suggestion-icon {
    margin-right: 0.5rem;
    color: #6f42c1;
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

/* ========================================
   ABA RESULTADO - EMPTY STATE
   ======================================== */

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

/* ========================================
   ABA SOLUÇÃO
   ======================================== */

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

/* Código da Solução */
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

/* Explicação Passo a Passo */
.solution-explanation-section {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.explanation-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #212529;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
}

.explanation-steps {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.explanation-step {
    display: flex;
    gap: 1.25rem;
    align-items: flex-start;
}

.step-number {
    flex-shrink: 0;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6f42c1, #8e5dd9);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
    box-shadow: 0 4px 12px rgba(111, 66, 193, 0.3);
}

.step-content {
    flex: 1;
}

.step-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.5rem;
}

.step-description {
    color: #495057;
    line-height: 1.6;
    margin: 0;
}

.step-description code {
    background: #f8f9fa;
    padding: 0.15rem 0.4rem;
    border-radius: 4px;
    color: #e83e8c;
    font-size: 0.9em;
}

/* Conceitos Importantes */
.solution-concepts-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border: 2px solid #e9ecef;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.concepts-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #212529;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
}

.concepts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.25rem;
}

.concept-card {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.3s ease;
}

.concept-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    border-color: #6f42c1;
}

.concept-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.concept-name {
    font-size: 1rem;
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.5rem;
}

.concept-description {
    font-size: 0.875rem;
    color: #6c757d;
    line-height: 1.5;
    margin: 0;
}

/* Recursos Adicionais */
.solution-resources-section {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 16px;
    padding: 2rem;
}

.resources-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #212529;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
}

.resources-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.resource-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    text-decoration: none;
    color: #212529;
    transition: all 0.3s ease;
}

.resource-item:hover {
    background: white;
    border-color: #6f42c1;
    transform: translateX(8px);
    box-shadow: 0 4px 15px rgba(111, 66, 193, 0.15);
}

.resource-icon {
    font-size: 1.75rem;
    flex-shrink: 0;
}

.resource-content {
    flex: 1;
}

.resource-content strong {
    display: block;
    font-size: 1rem;
    color: #212529;
    margin-bottom: 0.25rem;
}

.resource-content span {
    font-size: 0.875rem;
    color: #6c757d;
}

.resource-item i {
    color: #6c757d;
    font-size: 0.875rem;
}

.action-buttons .btn-outline-secondary {
    border: 2px solid #6c757d;
    transition: all 0.3s ease;
}

.action-buttons .btn-outline-secondary:hover {
    transform: translateY(-2px);
}

/* Resultados */
.execution-result {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
}

.result-header {
    padding: 1rem 1.5rem;
}

.execution-metrics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.metric {
    text-align: center;
}

.metric-label {
    display: block;
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.metric-value {
    display: block;
    font-size: 1.125rem;
    font-weight: 600;
    color: #212529;
}

.output-container, .test-results {
    padding: 1.5rem;
}

.output-content {
    background: #1e1e1e;
    color: #d4d4d4;
    padding: 1rem;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    overflow-x: auto;
}

.tests-grid {
    display: grid;
    gap: 0.75rem;
}

.test-result-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 8px;
}

.test-result-item.passed {
    background: #d4edda;
    border-left: 4px solid #28a745;
}

.test-result-item.failed {
    background: #f8d7da;
    border-left: 4px solid #dc3545;
}

.test-status {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.test-result-item.passed .test-status {
    background: #28a745;
}

.test-result-item.failed .test-status {
    background: #dc3545;
}

.test-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.test-message {
    font-size: 0.875rem;
    color: #6c757d;
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

/* Progress Card */
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

/* Modais */
.theme-options {
    display: grid;
    gap: 1rem;
}

.theme-option {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.theme-option.active {
    border-color: var(--primary-color);
    background: #f8f9fa;
}

.theme-preview {
    width: 40px;
    height: 40px;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.light-theme { background: linear-gradient(45deg, #f8f9fa 50%, #e9ecef 50%); }
.dark-theme { background: linear-gradient(45deg, #1e1e1e 50%, #2d2d2d 50%); }
.monokai-theme { background: linear-gradient(45deg, #272822 50%, #3e3d32 50%); }

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
    
    .execution-metrics {
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

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
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

.hint-number, .test-status {
    animation: pulse 2s infinite;
}
</style>

<script>
// ========================================
// SISTEMA DE DESAFIOS ALEATÓRIOS (GLOBAL)
// ========================================

// Desafios organizados por linguagem/tipo
const challengesByLanguage = {
    javascript: [
        {
            id: 1,
            title: "Soma de Dois Números",
            description: "Crie uma função que retorna a soma de dois números.",
            difficulty: "Fácil",
            language: "javascript",
            template: "function soma(a, b) {\n  // Seu código aqui\n  \n}",
            tests: [
                { input: [2, 3], expected: 5, description: "soma(2, 3) deve retornar 5" },
                { input: [10, 20], expected: 30, description: "soma(10, 20) deve retornar 30" },
                { input: [-5, 5], expected: 0, description: "soma(-5, 5) deve retornar 0" },
                { input: [0, 0], expected: 0, description: "soma(0, 0) deve retornar 0" }
            ],
            solution: "function soma(a, b) {\n  return a + b;\n}"
        },
        {
            id: 2,
            title: "Número Par ou Ímpar",
            description: "Crie uma função que retorna 'par' se o número for par e 'ímpar' se for ímpar.",
            difficulty: "Fácil",
            language: "javascript",
            template: "function parOuImpar(numero) {\n  // Seu código aqui\n  \n}",
            tests: [
                { input: [4], expected: "par", description: "parOuImpar(4) deve retornar 'par'" },
                { input: [7], expected: "ímpar", description: "parOuImpar(7) deve retornar 'ímpar'" },
                { input: [0], expected: "par", description: "parOuImpar(0) deve retornar 'par'" },
                { input: [101], expected: "ímpar", description: "parOuImpar(101) deve retornar 'ímpar'" }
            ],
            solution: "function parOuImpar(numero) {\n  return numero % 2 === 0 ? 'par' : 'ímpar';\n}"
        },
        {
            id: 3,
            title: "Reverter String",
            description: "Crie uma função que reverte uma string.",
            difficulty: "Médio",
            language: "javascript",
            template: "function reverterString(str) {\n  // Seu código aqui\n  \n}",
            tests: [
                { input: ["hello"], expected: "olleh", description: "reverterString('hello') deve retornar 'olleh'" },
                { input: ["JavaScript"], expected: "tpircSavaJ", description: "reverterString('JavaScript') deve retornar 'tpircSavaJ'" },
                { input: [""], expected: "", description: "reverterString('') deve retornar ''" },
                { input: ["12345"], expected: "54321", description: "reverterString('12345') deve retornar '54321'" }
            ],
            solution: "function reverterString(str) {\n  return str.split('').reverse().join('');\n}"
        },
        {
            id: 4,
            title: "Maior Número em Array",
            description: "Crie uma função que retorna o maior número de um array.",
            difficulty: "Médio",
            language: "javascript",
            template: "function maiorNumero(arr) {\n  // Seu código aqui\n  \n}",
            tests: [
                { input: [[1, 5, 3, 9, 2]], expected: 9, description: "maiorNumero([1, 5, 3, 9, 2]) deve retornar 9" },
                { input: [[-1, -5, -3]], expected: -1, description: "maiorNumero([-1, -5, -3]) deve retornar -1" },
                { input: [[42]], expected: 42, description: "maiorNumero([42]) deve retornar 42" },
                { input: [[10, 20, 30, 25]], expected: 30, description: "maiorNumero([10, 20, 30, 25]) deve retornar 30" }
            ],
            solution: "function maiorNumero(arr) {\n  return Math.max(...arr);\n}"
        },
        {
            id: 5,
            title: "Fibonacci",
            description: "Crie uma função que retorna o n-ésimo número da sequência de Fibonacci.",
            difficulty: "Difícil",
            language: "javascript",
            template: "function fibonacci(n) {\n  // Seu código aqui\n  \n}",
            tests: [
                { input: [0], expected: 0, description: "fibonacci(0) deve retornar 0" },
                { input: [1], expected: 1, description: "fibonacci(1) deve retornar 1" },
                { input: [6], expected: 8, description: "fibonacci(6) deve retornar 8" },
                { input: [10], expected: 55, description: "fibonacci(10) deve retornar 55" }
            ],
            solution: "function fibonacci(n) {\n  if (n <= 1) return n;\n  let a = 0, b = 1;\n  for (let i = 2; i <= n; i++) {\n    [a, b] = [b, a + b];\n  }\n  return b;\n}"
        }
    ],
    
    html: [
        {
            id: 101,
            title: "Card de Perfil",
            description: "Crie um card HTML com imagem, nome, descrição e botão.",
            difficulty: "Fácil",
            language: "html",
            template: "<!-- Crie um card de perfil -->\n<div class=\"profile-card\">\n  <!-- Seu código aqui -->\n  \n</div>",
            hint: "Use tags: img, h2, p, button",
            solution: '<div class="profile-card">\n  <img src="avatar.jpg" alt="Avatar">\n  <h2>João Silva</h2>\n  <p>Desenvolvedor Web</p>\n  <button>Ver Perfil</button>\n</div>'
        },
        {
            id: 102,
            title: "Formulário de Contato",
            description: "Crie um formulário com nome, email e mensagem.",
            difficulty: "Fácil",
            language: "html",
            template: "<!-- Crie um formulário de contato -->\n<form>\n  <!-- Seu código aqui -->\n  \n</form>",
            hint: "Use input, textarea, label e button",
            solution: '<form>\n  <label>Nome:\n    <input type="text" name="nome" required>\n  </label>\n  <label>Email:\n    <input type="email" name="email" required>\n  </label>\n  <label>Mensagem:\n    <textarea name="mensagem"></textarea>\n  </label>\n  <button type="submit">Enviar</button>\n</form>'
        },
        {
            id: 103,
            title: "Lista de Navegação",
            description: "Crie uma barra de navegação com 4 links.",
            difficulty: "Fácil",
            language: "html",
            template: "<!-- Crie uma navegação -->\n<nav>\n  <!-- Seu código aqui -->\n  \n</nav>",
            hint: "Use nav, ul, li e a",
            solution: '<nav>\n  <ul>\n    <li><a href="#home">Home</a></li>\n    <li><a href="#sobre">Sobre</a></li>\n    <li><a href="#servicos">Serviços</a></li>\n    <li><a href="#contato">Contato</a></li>\n  </ul>\n</nav>'
        },
        {
            id: 104,
            title: "Tabela de Dados",
            description: "Crie uma tabela com cabeçalhos e 3 linhas de dados.",
            difficulty: "Médio",
            language: "html",
            template: "<!-- Crie uma tabela -->\n<table>\n  <!-- Seu código aqui -->\n  \n</table>",
            hint: "Use table, thead, tbody, tr, th, td",
            solution: '<table>\n  <thead>\n    <tr>\n      <th>Nome</th>\n      <th>Email</th>\n      <th>Status</th>\n    </tr>\n  </thead>\n  <tbody>\n    <tr>\n      <td>João</td>\n      <td>joao@email.com</td>\n      <td>Ativo</td>\n    </tr>\n    <tr>\n      <td>Maria</td>\n      <td>maria@email.com</td>\n      <td>Ativo</td>\n    </tr>\n    <tr>\n      <td>Pedro</td>\n      <td>pedro@email.com</td>\n      <td>Inativo</td>\n    </tr>\n  </tbody>\n</table>'
        },
        {
            id: 105,
            title: "Estrutura Semântica",
            description: "Crie uma página com header, main, aside e footer.",
            difficulty: "Médio",
            language: "html",
            template: "<!-- Crie estrutura semântica -->\n<!DOCTYPE html>\n<html>\n  <!-- Seu código aqui -->\n  \n</html>",
            hint: "Use tags semânticas HTML5",
            solution: '<!DOCTYPE html>\n<html lang="pt-BR">\n<head>\n  <meta charset="UTF-8">\n  <title>Página Semântica</title>\n</head>\n<body>\n  <header>\n    <h1>Meu Site</h1>\n  </header>\n  <main>\n    <article>\n      <h2>Conteúdo Principal</h2>\n      <p>Texto do artigo...</p>\n    </article>\n  </main>\n  <aside>\n    <h3>Barra Lateral</h3>\n  </aside>\n  <footer>\n    <p>&copy; 2025</p>\n  </footer>\n</body>\n</html>'
        }
    ],
    
    css: [
        {
            id: 201,
            title: "Centralizar Elemento",
            description: "Centralize horizontal e verticalmente um elemento dentro de um container.",
            difficulty: "Fácil",
            language: "css",
            template: ".container {\n  /* Seu código aqui */\n  \n}\n\n.box {\n  width: 200px;\n  height: 200px;\n}",
            hint: "Use flexbox ou grid",
            solution: ".container {\n  display: flex;\n  justify-content: center;\n  align-items: center;\n  min-height: 100vh;\n}\n\n.box {\n  width: 200px;\n  height: 200px;\n}"
        },
        {
            id: 202,
            title: "Botão com Gradiente",
            description: "Crie um botão com gradiente roxo/rosa e efeito hover.",
            difficulty: "Fácil",
            language: "css",
            template: ".btn {\n  /* Seu código aqui */\n  \n}",
            hint: "Use linear-gradient e transition",
            solution: ".btn {\n  background: linear-gradient(135deg, #6f42c1, #e83e8c);\n  color: white;\n  padding: 12px 24px;\n  border: none;\n  border-radius: 8px;\n  cursor: pointer;\n  transition: all 0.3s ease;\n}\n\n.btn:hover {\n  transform: translateY(-2px);\n  box-shadow: 0 4px 12px rgba(111, 66, 193, 0.4);\n}"
        },
        {
            id: 203,
            title: "Card Responsivo",
            description: "Crie um card que muda de layout em telas pequenas.",
            difficulty: "Médio",
            language: "css",
            template: ".card {\n  /* Seu código aqui */\n  \n}",
            hint: "Use media queries",
            solution: ".card {\n  display: flex;\n  gap: 1rem;\n  padding: 1.5rem;\n  border-radius: 12px;\n  box-shadow: 0 2px 8px rgba(0,0,0,0.1);\n}\n\n@media (max-width: 768px) {\n  .card {\n    flex-direction: column;\n  }\n}"
        },
        {
            id: 204,
            title: "Grid de 3 Colunas",
            description: "Crie um grid responsivo de 3 colunas que se adapta.",
            difficulty: "Médio",
            language: "css",
            template: ".grid {\n  /* Seu código aqui */\n  \n}",
            hint: "Use CSS Grid com auto-fit",
            solution: ".grid {\n  display: grid;\n  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));\n  gap: 1.5rem;\n  padding: 2rem;\n}"
        },
        {
            id: 205,
            title: "Animação de Loading",
            description: "Crie um spinner de loading animado.",
            difficulty: "Difícil",
            language: "css",
            template: ".spinner {\n  /* Seu código aqui */\n  \n}",
            hint: "Use @keyframes e animation",
            solution: ".spinner {\n  width: 50px;\n  height: 50px;\n  border: 4px solid #f3f3f3;\n  border-top: 4px solid #6f42c1;\n  border-radius: 50%;\n  animation: spin 1s linear infinite;\n}\n\n@keyframes spin {\n  0% { transform: rotate(0deg); }\n  100% { transform: rotate(360deg); }\n}"
        }
    ],
    
    php: [
        {
            id: 301,
            title: "Validar Email",
            description: "Crie uma função que valida se um email é válido.",
            difficulty: "Fácil",
            language: "php",
            template: "/* PHP */\nfunction validarEmail($email) {\n  // Seu código aqui\n  \n}",
            hint: "Use filter_var com FILTER_VALIDATE_EMAIL",
            solution: "/* PHP */\nfunction validarEmail($email) {\n  return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;\n}"
        },
        {
            id: 302,
            title: "Sanitizar String",
            description: "Crie uma função que remove tags HTML de uma string.",
            difficulty: "Fácil",
            language: "php",
            template: "/* PHP */\nfunction sanitizar($texto) {\n  // Seu código aqui\n  \n}",
            hint: "Use strip_tags ou htmlspecialchars",
            solution: "/* PHP */\nfunction sanitizar($texto) {\n  return htmlspecialchars(strip_tags($texto), ENT_QUOTES, 'UTF-8');\n}"
        },
        {
            id: 303,
            title: "Gerar Senha Aleatória",
            description: "Crie uma função que gera uma senha aleatória de N caracteres.",
            difficulty: "Médio",
            language: "php",
            template: "/* PHP */\nfunction gerarSenha($tamanho = 8) {\n  // Seu código aqui\n  \n}",
            hint: "Use random_bytes ou str_shuffle",
            solution: "/* PHP */\nfunction gerarSenha($tamanho = 8) {\n  $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';\n  return substr(str_shuffle($chars), 0, $tamanho);\n}"
        },
        {
            id: 304,
            title: "Conectar ao Banco",
            description: "Crie uma função PDO para conectar ao MySQL.",
            difficulty: "Médio",
            language: "php",
            template: "/* PHP */\nfunction conectarBanco() {\n  // Seu código aqui\n  \n}",
            hint: "Use PDO com try-catch",
            solution: "/* PHP */\nfunction conectarBanco() {\n  try {\n    $pdo = new PDO(\n      'mysql:host=localhost;dbname=banco;charset=utf8',\n      'usuario',\n      'senha',\n      [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]\n    );\n    return $pdo;\n  } catch (PDOException $e) {\n    die('Erro: ' . $e->getMessage());\n  }\n}"
        },
        {
            id: 305,
            title: "Upload de Arquivo Seguro",
            description: "Crie uma função segura para upload de imagens.",
            difficulty: "Difícil",
            language: "php",
            template: "/* PHP */\nfunction uploadImagem($file) {\n  // Seu código aqui\n  \n}",
            hint: "Valide tipo, tamanho e use move_uploaded_file",
            solution: "/* PHP */\nfunction uploadImagem($file) {\n  $allowed = ['image/jpeg', 'image/png', 'image/gif'];\n  $maxSize = 5 * 1024 * 1024; // 5MB\n  \n  if (!in_array($file['type'], $allowed)) {\n    return ['success' => false, 'error' => 'Tipo inválido'];\n  }\n  \n  if ($file['size'] > $maxSize) {\n    return ['success' => false, 'error' => 'Arquivo muito grande'];\n  }\n  \n  $ext = pathinfo($file['name'], PATHINFO_EXTENSION);\n  $newName = uniqid() . '.' . $ext;\n  $dest = 'uploads/' . $newName;\n  \n  if (move_uploaded_file($file['tmp_name'], $dest)) {\n    return ['success' => true, 'file' => $newName];\n  }\n  \n  return ['success' => false, 'error' => 'Falha no upload'];\n}"
        }
    ]
};

// Detectar linguagem do exercício atual
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
let currentChallenge = null;

// Carregar desafio aleatório baseado na linguagem
window.loadRandomChallenge = function() {
    const languageChallenges = challengesByLanguage[currentLanguage] || challengesByLanguage.javascript;
    const randomIndex = Math.floor(Math.random() * languageChallenges.length);
    currentChallenge = languageChallenges[randomIndex];
    
    console.log('Desafio carregado:', currentChallenge.title, '| Linguagem:', currentLanguage);
    
    // Atualizar título do desafio
    showChallengeModal(currentChallenge);
    
    // Carregar template no editor
    const codeEditor = document.getElementById('user_code');
    if (codeEditor) {
        codeEditor.value = currentChallenge.template;
        
        // Atualizar linha numbers e status
        if (typeof updateLineNumbers === 'function') updateLineNumbers();
        if (typeof updateEditorStatus === 'function') updateEditorStatus();
    }
    
    showToast(`Desafio ${currentLanguage.toUpperCase()} carregado: ${currentChallenge.title}`, 'success');
};

// Modal de desafio
function showChallengeModal(challenge) {
    // Ícones e cores por linguagem
    const languageConfig = {
        javascript: { icon: 'fa-js-square', color: '#f7df1e', name: 'JavaScript' },
        html: { icon: 'fa-html5', color: '#e34f26', name: 'HTML5' },
        css: { icon: 'fa-css3-alt', color: '#1572b6', name: 'CSS3' },
        php: { icon: 'fa-php', color: '#777bb4', name: 'PHP' }
    };
    
    const langConfig = languageConfig[challenge.language] || languageConfig.javascript;
    
    const modalHTML = `
        <div class="modal fade" id="challengeModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" style="background: linear-gradient(135deg, #6f42c1 0%, #8e5dd9 100%);">
                    <div class="modal-header border-0">
                        <h5 class="modal-title text-white">
                            <i class="fas fa-trophy me-2"></i>${challenge.title}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="background: white; border-radius: 0 0 15px 15px;">
                        <div class="mb-3 d-flex align-items-center gap-2">
                            <span class="badge bg-${challenge.difficulty === 'Fácil' ? 'success' : challenge.difficulty === 'Médio' ? 'warning' : 'danger'}">
                                ${challenge.difficulty}
                            </span>
                            <span class="badge" style="background-color: ${langConfig.color};">
                                <i class="fab ${langConfig.icon} me-1"></i>${langConfig.name}
                            </span>
                        </div>
                        <p class="lead">${challenge.description}</p>
                        
                        ${challenge.tests ? `
                            <h6 class="mt-4 mb-3">Testes que serão executados:</h6>
                            <ul class="list-group">
                                ${challenge.tests.map(test => `
                                    <li class="list-group-item">
                                        <i class="fas fa-check-circle text-muted me-2"></i>${test.description}
                                    </li>
                                `).join('')}
                            </ul>
                            <div class="alert alert-info mt-4 mb-0">
                                <i class="fas fa-lightbulb me-2"></i>
                                <strong>Dica:</strong> Execute os testes para verificar se sua solução está correta!
                            </div>
                        ` : ''}
                        
                        ${challenge.hint ? `
                            <div class="alert alert-warning mt-4 mb-0">
                                <i class="fas fa-lightbulb me-2"></i>
                                <strong>Dica:</strong> ${challenge.hint}
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal anterior se existir
    const existingModal = document.getElementById('challengeModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Adicionar novo modal
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('challengeModal'));
    modal.show();
}

// Função toast (caso não esteja definida)
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
                        executeCode();
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
        
        // Atualizar cores do container principal
        const editorMain = document.querySelector('.editor-main');
        if (editorMain) {
            const themes = {
                default: { bg: '#ffffff', sidebar: '#f5f5f5' },
                dark: { bg: '#1e1e1e', sidebar: '#252526' },
                monokai: { bg: '#272822', sidebar: '#1e1f1c' },
                dracula: { bg: '#282a36', sidebar: '#21222c' },
                github: { bg: '#ffffff', sidebar: '#f6f8fa' }
            };
            
            const colors = themes[theme] || themes.default;
            editorMain.style.background = colors.bg;
            
            const sidebar = document.querySelector('.editor-sidebar');
            if (sidebar) {
                sidebar.style.background = colors.sidebar;
            }
        }
        
        showToast(`Tema "${theme}" aplicado`, 'success');
    }
    
    // Tamanho da fonte
    if (fontSizeSelect) {
        fontSizeSelect.addEventListener('change', function() {
            changeFontSize(this.value);
        });
    }
    
    function changeFontSize(size) {
        if (!codeEditor) return;
        
        codeEditor.style.fontSize = size + 'px';
        
        if (lineNumbers) {
            lineNumbers.style.fontSize = size + 'px';
        }
        
        showToast(`Tamanho da fonte: ${size}px`, 'success');
    }
    
    // Toggle números de linha
    window.toggleLineNumbers = function() {
        const sidebar = document.querySelector('.editor-sidebar');
        if (!sidebar) return;
        
        if (sidebar.style.display === 'none') {
            sidebar.style.display = 'block';
            showToast('Números de linha ativados', 'success');
        } else {
            sidebar.style.display = 'none';
            showToast('Números de linha desativados', 'info');
        }
    };
    
    // Toggle fullscreen
    window.toggleFullscreenEditor = function() {
        const container = document.querySelector('.editor-container');
        if (!container) return;
        
        if (!document.fullscreenElement) {
            container.requestFullscreen().then(() => {
                container.style.borderRadius = '0';
                showToast('Modo tela cheia ativado (F11 para sair)', 'info');
            }).catch(err => {
                showToast(`Erro ao entrar em tela cheia: ${err.message}`, 'danger');
            });
        } else {
            document.exitFullscreen().then(() => {
                container.style.borderRadius = '16px';
                showToast('Modo tela cheia desativado', 'info');
            });
        }
    };
    
    // Auto-save status
    let autoSaveTimeout;
    function updateAutoSaveStatus() {
        const autoSaveStatus = document.getElementById('autoSaveStatus');
        if (!autoSaveStatus) return;
        
        clearTimeout(autoSaveTimeout);
        autoSaveStatus.innerHTML = '<i class="fas fa-circle text-warning"></i> Salvando...';
        
        autoSaveTimeout = setTimeout(() => {
            autoSaveStatus.innerHTML = '<i class="fas fa-check-circle text-success"></i> Salvo';
        }, 1000);
    }
    
    // Salvar código
    window.saveCode = function() {
        if (!codeEditor) return;
        
        // Simular salvamento (aqui você faria uma requisição AJAX)
        localStorage.setItem('exercise_code_<?php echo $exercise_id; ?>', codeEditor.value);
        showToast('Código salvo com sucesso!', 'success');
        updateAutoSaveStatus();
    };
    
    // Executar código
    window.executeCode = function() {
        if (!codeEditor) return;
        
        const code = codeEditor.value;
        if (!code.trim()) {
            showToast('Editor vazio! Digite algum código primeiro.', 'warning');
            return;
        }
        
        showToast('Executando código...', 'info');
        
        // Criar área de output se não existir
        let outputArea = document.getElementById('codeOutput');
        if (!outputArea) {
            const outputTab = document.querySelector('#output');
            if (outputTab) {
                outputArea = document.createElement('div');
                outputArea.id = 'codeOutput';
                outputArea.className = 'code-output-area';
                outputArea.style.cssText = 'background: #1e1e1e; color: #d4d4d4; padding: 1.5rem; border-radius: 12px; font-family: monospace; min-height: 200px; margin-top: 1rem;';
                outputTab.insertBefore(outputArea, outputTab.firstChild);
            }
        }
        
        // Executar código em ambiente isolado
        try {
            // Capturar console.log
            const logs = [];
            const originalLog = console.log;
            console.log = function(...args) {
                logs.push(args.map(arg => 
                    typeof arg === 'object' ? JSON.stringify(arg, null, 2) : String(arg)
                ).join(' '));
                originalLog.apply(console, args);
            };
            
            // Executar código
            const startTime = performance.now();
            const result = eval(code);
            const endTime = performance.now();
            const executionTime = (endTime - startTime).toFixed(2);
            
            // Restaurar console.log
            console.log = originalLog;
            
            // Mostrar resultado
            let output = '<div style="color: #4ade80; font-weight: bold; margin-bottom: 1rem;">✓ Execução bem-sucedida!</div>';
            output += `<div style="color: #9ca3af; margin-bottom: 0.5rem;">Tempo de execução: ${executionTime}ms</div>`;
            
            if (logs.length > 0) {
                output += '<div style="margin-top: 1rem; border-top: 1px solid #374151; padding-top: 1rem;">';
                output += '<div style="color: #60a5fa; margin-bottom: 0.5rem;">📝 Console Output:</div>';
                logs.forEach(log => {
                    output += `<div style="color: #d4d4d4; margin-left: 1rem; margin-bottom: 0.25rem;">→ ${log}</div>`;
                });
                output += '</div>';
            }
            
            if (result !== undefined) {
                output += '<div style="margin-top: 1rem; border-top: 1px solid #374151; padding-top: 1rem;">';
                output += '<div style="color: #60a5fa; margin-bottom: 0.5rem;">↩️ Valor Retornado:</div>';
                output += `<div style="color: #fbbf24; margin-left: 1rem;">${typeof result === 'object' ? JSON.stringify(result, null, 2) : result}</div>`;
                output += '</div>';
            }
            
            if (outputArea) {
                outputArea.innerHTML = output;
            }
            
            showToast('Código executado com sucesso!', 'success');
            
            // Mudar para aba de saída
            const outputTabButton = document.querySelector('#output-tab');
            if (outputTabButton) {
                const tab = new bootstrap.Tab(outputTabButton);
                tab.show();
            }
            
        } catch (error) {
            console.log = console.log || originalLog;
            
            const errorOutput = `
                <div style="color: #ef4444; font-weight: bold; margin-bottom: 1rem;">✗ Erro na execução!</div>
                <div style="color: #9ca3af; margin-bottom: 0.5rem;">Tipo: ${error.name}</div>
                <div style="background: #991b1b; color: #fecaca; padding: 1rem; border-radius: 8px; border-left: 4px solid #ef4444;">
                    <div style="font-weight: bold; margin-bottom: 0.5rem;">Mensagem de Erro:</div>
                    <div>${error.message}</div>
                    ${error.stack ? `<div style="margin-top: 1rem; font-size: 0.85rem; color: #fca5a5;">Stack Trace:<br>${error.stack.replace(/\n/g, '<br>')}</div>` : ''}
                </div>
            `;
            
            if (outputArea) {
                outputArea.innerHTML = errorOutput;
            }
            
            showToast(`Erro: ${error.message}`, 'danger');
            
            // Mudar para aba de saída para ver o erro
            const outputTabButton = document.querySelector('#output-tab');
            if (outputTabButton) {
                const tab = new bootstrap.Tab(outputTabButton);
                tab.show();
            }
        }
    };
    
    // Executar testes
    window.runTests = function() {
        if (!codeEditor) return;
        
        const code = codeEditor.value;
        if (!code.trim()) {
            showToast('Editor vazio! Digite algum código primeiro.', 'warning');
            return;
        }
        
        if (!currentChallenge) {
            showToast('Nenhum desafio carregado! Clique em "Desafio Aleatório" primeiro.', 'warning');
            return;
        }
        
        showToast('Executando testes...', 'info');
        
        try {
            // Executar o código para definir a função
            eval(code);
            
            // Pegar o nome da função do desafio
            const functionMatch = currentChallenge.template.match(/function (\w+)/);
            if (!functionMatch) {
                throw new Error('Não foi possível identificar o nome da função');
            }
            const functionName = functionMatch[1];
            
            // Verificar se a função foi definida
            if (typeof window[functionName] !== 'function') {
                throw new Error(`A função ${functionName} não foi definida`);
            }
            
            // Executar todos os testes
            let passedTests = 0;
            let failedTests = 0;
            let testResults = [];
            
            currentChallenge.tests.forEach((test, index) => {
                try {
                    const startTime = performance.now();
                    const result = window[functionName](...test.input);
                    const endTime = performance.now();
                    const executionTime = (endTime - startTime).toFixed(3);
                    
                    const passed = JSON.stringify(result) === JSON.stringify(test.expected);
                    
                    if (passed) {
                        passedTests++;
                    } else {
                        failedTests++;
                    }
                    
                    testResults.push({
                        index: index + 1,
                        description: test.description,
                        passed: passed,
                        expected: test.expected,
                        received: result,
                        time: executionTime
                    });
                } catch (error) {
                    failedTests++;
                    testResults.push({
                        index: index + 1,
                        description: test.description,
                        passed: false,
                        error: error.message
                    });
                }
            });
            
            // Mostrar resultados
            showTestResults(testResults, passedTests, failedTests);
            
            if (failedTests === 0) {
                showToast(`🎉 Parabéns! Todos os ${passedTests} testes passaram!`, 'success');
                celebrateSuccess();
            } else {
                showToast(`${passedTests} testes passaram, ${failedTests} falharam`, 'warning');
            }
            
        } catch (error) {
            showToast(`Erro ao executar testes: ${error.message}`, 'danger');
        }
    };
    
    // Mostrar resultados dos testes
    function showTestResults(results, passed, failed) {
        let outputArea = document.getElementById('codeOutput');
        if (!outputArea) {
            const outputTab = document.querySelector('#output');
            if (outputTab) {
                outputArea = document.createElement('div');
                outputArea.id = 'codeOutput';
                outputArea.className = 'code-output-area';
                outputArea.style.cssText = 'background: #1e1e1e; color: #d4d4d4; padding: 1.5rem; border-radius: 12px; font-family: monospace; min-height: 200px; margin-top: 1rem;';
                outputTab.insertBefore(outputArea, outputTab.firstChild);
            }
        }
        
        const totalTests = passed + failed;
        const successRate = ((passed / totalTests) * 100).toFixed(1);
        
        let output = `
            <div style="margin-bottom: 1.5rem;">
                <div style="color: ${failed === 0 ? '#4ade80' : '#fbbf24'}; font-weight: bold; font-size: 1.2rem; margin-bottom: 0.5rem;">
                    ${failed === 0 ? '✓' : '⚠'} Resultados dos Testes
                </div>
                <div style="color: #9ca3af;">
                    <span style="color: #4ade80;">✓ ${passed} passou${passed !== 1 ? 'm' : ''}</span>
                    ${failed > 0 ? `<span style="color: #ef4444; margin-left: 1rem;">✗ ${failed} falhou/falharam</span>` : ''}
                    <span style="margin-left: 1rem;">Taxa de sucesso: ${successRate}%</span>
                </div>
            </div>
        `;
        
        results.forEach(result => {
            const bgColor = result.passed ? '#065f46' : '#991b1b';
            const borderColor = result.passed ? '#4ade80' : '#ef4444';
            const textColor = result.passed ? '#d1fae5' : '#fecaca';
            
            output += `
                <div style="background: ${bgColor}; border-left: 4px solid ${borderColor}; padding: 1rem; margin-bottom: 1rem; border-radius: 8px;">
                    <div style="color: ${textColor}; font-weight: bold; margin-bottom: 0.5rem;">
                        ${result.passed ? '✓' : '✗'} Teste ${result.index}: ${result.description}
                    </div>
                    ${result.error ? `
                        <div style="color: #fca5a5; margin-top: 0.5rem;">
                            Erro: ${result.error}
                        </div>
                    ` : `
                        <div style="color: #9ca3af; font-size: 0.9rem; margin-top: 0.5rem;">
                            ${result.passed ? '' : `
                                <div>Esperado: <span style="color: #60a5fa;">${JSON.stringify(result.expected)}</span></div>
                                <div>Recebido: <span style="color: #fbbf24;">${JSON.stringify(result.received)}</span></div>
                            `}
                            ${result.time ? `<div style="margin-top: 0.25rem;">Tempo: ${result.time}ms</div>` : ''}
                        </div>
                    `}
                </div>
            `;
        });
        
        if (outputArea) {
            outputArea.innerHTML = output;
        }
        
        // Mudar para aba de saída
        const outputTabButton = document.querySelector('#output-tab');
        if (outputTabButton) {
            const tab = new bootstrap.Tab(outputTabButton);
            tab.show();
        }
    }
    
    // Celebração de sucesso
    function celebrateSuccess() {
        // Animação de confete (simples)
        const celebration = document.createElement('div');
        celebration.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 4rem;
            z-index: 9999;
            animation: celebrate 1s ease-out;
        `;
        celebration.innerHTML = '🎉';
        document.body.appendChild(celebration);
        
        setTimeout(() => {
            celebration.remove();
        }, 1000);
    }
    
    // Download do código
    window.downloadCode = function() {
        if (!codeEditor) return;
        
        const blob = new Blob([codeEditor.value], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'solution.js';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        showToast('Código baixado!', 'success');
    };
    
    // Restaurar código salvo
    const savedCode = localStorage.getItem('exercise_code_<?php echo $exercise_id; ?>');
    if (savedCode && codeEditor && !codeEditor.value.trim()) {
        codeEditor.value = savedCode;
        updateLineNumbers();
        updateEditorStatus();
    }

    // Temas do editor
    const themeOptions = document.querySelectorAll('.theme-option');
    themeOptions.forEach(option => {
        option.addEventListener('click', function() {
            themeOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            
            const theme = this.dataset.theme;
            applyEditorTheme(theme);
        });
    });

    function applyEditorTheme(theme) {
        const editor = document.querySelector('.editor-wrapper');
        if (!editor) return;
        
        editor.style.backgroundColor = getThemeColor(theme, 'bg');
        editor.style.color = getThemeColor(theme, 'text');
        
        // Aqui você implementaria a mudança completa do tema do editor
        console.log('Tema aplicado:', theme);
    }

    function getThemeColor(theme, type) {
        const themes = {
            default: { bg: '#f8f9fa', text: '#212529' },
            dark: { bg: '#1e1e1e', text: '#d4d4d4' },
            monokai: { bg: '#272822', text: '#f8f8f2' }
        };
        return themes[theme]?.[type] || themes.default[type];
    }

    // Funções do editor
    window.formatCode = function() {
        // Simulação de formatação de código
        if (codeEditor) {
            const formatted = codeEditor.value
                .replace(/\t/g, '  ')
                .replace(/\n\s*\n/g, '\n\n');
            codeEditor.value = formatted;
            showToast('Código formatado!', 'success');
        }
    };

    window.resetCode = function() {
        if (codeEditor && confirm('Tem certeza que deseja resetar o código?')) {
            codeEditor.value = `<?php echo sanitize($exercise['initial_code'] ?? ''); ?>`;
            showToast('Código resetado!', 'info');
        }
    };

    window.toggleFullscreen = function() {
        const elem = document.documentElement;
        if (!document.fullscreenElement) {
            elem.requestFullscreen().catch(err => {
                console.log(`Erro ao tentar entrar em tela cheia: ${err.message}`);
            });
        } else {
            document.exitFullscreen();
        }
    };

    // Função para mostrar notificações
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

    // Atalhos de teclado para abas (Ctrl + 1-4)
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey) {
            const tabMap = {
                '1': '#instructions-tab',
                '2': '#editor-tab',
                '3': '#output-tab',
                '4': '#solution-tab'
            };
            
            const tabSelector = tabMap[e.key];
            if (tabSelector) {
                e.preventDefault();
                const tabButton = document.querySelector(tabSelector);
                if (tabButton) {
                    const tab = new bootstrap.Tab(tabButton);
                    tab.show();
                    showToast(`Navegado para: ${tabButton.textContent.trim()}`, 'info');
                }
            }
        }
    });
    
    // Indicador visual de atalhos (tooltip)
    const tabButtons = document.querySelectorAll('.workspace-tabs .nav-link');
    tabButtons.forEach((btn, index) => {
        btn.setAttribute('title', `Ctrl + ${index + 1}`);
        btn.setAttribute('data-bs-toggle', 'tooltip');
        btn.setAttribute('data-bs-placement', 'top');
    });
    
    // Inicializar tooltips do Bootstrap
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Inicializar status do editor
    updateEditorStatus();
    
    // Funcionalidade de toggle para requisitos
    const requirementItems = document.querySelectorAll('.requirement-item');
    requirementItems.forEach(item => {
        item.addEventListener('click', function() {
            const isCompleted = this.getAttribute('data-completed') === 'true';
            this.setAttribute('data-completed', !isCompleted);
            
            // Animação de feedback
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 200);
        });
    });
    
    // Funcionalidade de toggle para casos de teste
    window.toggleTestDetails = function(testCase) {
        testCase.classList.toggle('expanded');
        
        // Animação suave
        const content = testCase.querySelector('.test-case-content');
        if (testCase.classList.contains('expanded')) {
            content.style.padding = '1.5rem';
        } else {
            setTimeout(() => {
                content.style.padding = '0 1.5rem';
            }, 400);
        }
    };
    
    // Expandir primeiro caso de teste por padrão
    const firstTestCase = document.querySelector('.test-case');
    if (firstTestCase) {
        setTimeout(() => {
            toggleTestDetails(firstTestCase);
        }, 500);
    }
});

// Função global para toggle de teste (necessária para onclick inline)
function toggleTestDetails(testCase) {
    testCase.classList.toggle('expanded');
    
    const content = testCase.querySelector('.test-case-content');
    if (testCase.classList.contains('expanded')) {
        content.style.padding = '1.5rem';
    } else {
        setTimeout(() => {
            content.style.padding = '0 1.5rem';
        }, 400);
    }
}

// Garantir que as abas do Bootstrap funcionem
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar todas as abas
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const tab = new bootstrap.Tab(this);
            tab.show();
        });
    });
});

</script>

<?php include 'footer.php'; ?>