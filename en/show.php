<?php
// Incluir configurações
require_once 'config.php';

// Obter parâmetros da URL
$type = $_GET['type'] ?? '';
$id = (int)($_GET['id'] ?? 0);
$preview = isset($_GET['preview']);

// Verificar se tipo e ID são válidos
if (empty($type) || $id <= 0) {
    header('Location: index.php');
    exit;
}

// Definir título da página baseado no tipo
switch ($type) {
    case 'exercise':
        $title = 'Exercício';
        break;
    case 'tutorial':
        $title = 'Tutorial';
        break;
    case 'forum':
        $title = 'Post do Fórum';
        break;
    default:
        header('Location: index.php');
        exit;
}

// Dados fictícios baseados no tipo
if ($type === 'exercise') {
    $exercises = [
        1 => [
            'id' => 1,
            'title' => 'Basic HTML Structure',
            'description' => 'Aprenda a criar a estrutura básica de uma página HTML',
            'difficulty' => 'Beginner',
            'category' => 'HTML',
            'content' => '<h2>Objective</h2><p>Create a basic HTML page with semantic structure.</p><h2>Instructions</h2><ol><li>Create the basic HTML5 structure</li><li>Add a header with a title</li><li>Include an introductory paragraph.</li><li>Add an unordered list</li></ol>',
            'starter_code' => '<!DOCTYPE html>\n<html lang="en">\n<head>\n    <meta charset="UTF-8">\n    <meta name="viewport" content="width=device-width, initial-scale=1.0">\n    <title>My Page</title>\n</head>\n<body>\n    <!-- Your code here -->\n</body>\n</html>',
            'solution' => '<!DOCTYPE html>\n<html lang="pt-BR">\n<head>\n    <meta charset="UTF-8">\n    <meta name="viewport" content="width=device-width, initial-scale=1.0">\n    <title>My Page</title>\n</head>\n<body>\n    <header>\n        <h1>Welcome to my website</h1>\n    </header>\n    <main>\n        <p>This is a basic HTML page.</p>\n        <ul>\n            <li>Item 1</li>\n            <li>Item 2</li>\n            <li>Item 3</li>\n        </ul>\n    </main>\n</body>\n</html>'
        ],
        2 => [
            'id' => 2,
            'title' => 'Styling with CSS',
            'description' => 'Practice basic styling with CSS',
            'difficulty' => 'Beginner',
            'category' => 'CSS',
            'content' => '<h2>Objective</h2><p>Apply basic CSS styles to an HTML page.</p><h2>Instructions</h2><ol><li>Set colors for text</li><li>Change the font</li><li>Add margin and padding</li><li>Style the list</li></ol>',
            'starter_code' => '/* Add your CSS styles here*/\nbody {\n    \n}\n\nh1 {\n    \n}\n\np {\n    \n}\n\nul {\n    \n}',
            'solution' => 'body {\n    font-family: Arial, sans-serif;\n    margin: 20px;\n    background-color: #f5f5f5;\n}\n\nh1 {\n    color: #333;\n    text-align: center;\n}\n\np {\n    color: #666;\n    line-height: 1.6;\n}\n\nul {\n    background-color: white;\n    padding: 20px;\n    border-radius: 5px;\n}'
        ]
    ];
    
    $item = $exercises[$id] ?? null;
    
} elseif ($type === 'tutorial') {
    $tutorials = [
        1 => [
            'id' => 1,
            'title' => 'Introduction to HTML5',
            'description' => 'Learn the fundamentals of HTML5 and its main tags',
            'category' => 'HTML',
            'content' => '<h2>What is HTML5??</h2><p>HTML5 is the fifth version of the HTML language, which brought many improvements and new semantic elements.</p><h2>Main Tags</h2><ul><li><code>&lt;header&gt;</code> - Page or section header</li><li><code>&lt;nav&gt;</code> - Navigation</li><li><code>&lt;main&gt;</code> - Main content</li><li><code>&lt;article&gt;</code> - Independent article</li><li><code>&lt;section&gt;</code> - Content section</li><li><code>&lt;aside&gt;</code> - Side content</li><li><code>&lt;footer&gt;</code> - Footer</li></ul><h2>Practical Example</h2><pre><code>&lt;!DOCTYPE html&gt;\n&lt;html lang="en"&gt;\n&lt;head&gt;\n    &lt;meta charset="UTF-8"&gt;\n    &lt;title&gt;Minha Página&lt;/title&gt;\n&lt;/head&gt;\n&lt;body&gt;\n    &lt;header&gt;\n        &lt;h1&gt;Título Principal&lt;/h1&gt;\n    &lt;/header&gt;\n    &lt;main&gt;\n        &lt;p&gt;Main content here&lt;/p&gt;\n    &lt;/main&gt;\n&lt;/body&gt;\n&lt;/html&gt;</code></pre>',
            'duration' => '15 min',
            'level' => 'Beginner'
        ],
        2 => [
            'id' => 2,
            'title' => 'CSS Grid Layout',
            'description' => 'Master the CSS grid system for modern layouts',
            'category' => 'CSS',
            'content' => '<h2>CSS Grid</h2><p>CSS Grid is a two-dimensional layout system that allows you to create complex layouts in a simple way.</p><h2>Basic Properties</h2><ul><li><code>display: grid</code> - Definieren Sie ein Container-Raster.</li><li><code>grid-template-columns</code> - Define the columns</li><li><code>grid-template-rows</code> - Define the lines</li><li><code>gap</code> - Spacing between items</li></ul><h2>Example</h2><pre><code>.container {\n    display: grid;\n    grid-template-columns: 1fr 2fr 1fr;\n    gap: 20px;\n}</code></pre>',
            'duration' => '25 min',
            'level' => 'Mittlerer'
        ]
    ];
    
    $item = $tutorials[$id] ?? null;
    
} elseif ($type === 'forum') {
    $forum_posts = [
        1 => [
            'id' => 1,
            'title' => 'Wie fängt man mit HTML an?',
            'content' => '<pIm just starting out and would like some tips on where to begin with HTML. Ive read a few tutorials, but I still have questions about the basic structure.</p><p>Can anyone give me some practical tips?</p>',
            'author' => 'João Silva',
            'category' => 'HTML',
            'created_at' => '2024-01-15 14:30:00',
            'replies' => [
                [
                    'author' => 'Maria Santos',
                    'content' => 'I recommend starting with the basic structure: DOCTYPE, html, head, and body. Practice a lot!',
                    'created_at' => '2024-01-15 15:00:00'
                ],
                [
                    'author' => 'Pedro Costa',
                    'content' => 'I agree with Maria. I also suggest using MDN Web Docs as a reference.',
                    'created_at' => '2024-01-15 16:30:00'
                ]
            ]
        ]
    ];
    
    $item = $forum_posts[$id] ?? null;
}

// Se item não encontrado, redirecionar
if (!$item) {
    header('Location: index.php');
    exit;
}

include 'header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item">
                        <a href="<?php echo $type; ?>_index.php">
                            <?php echo ucfirst($type === 'forum' ? 'Fórum' : ($type === 'tutorial' ? 'Tutoriais' : 'Exercícios')); ?>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?php echo sanitize($item['title']); ?>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <?php if ($type === 'exercise'): ?>
        <!-- Layout para exercícios -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h1 class="h5 mb-0">
                            <i class="fas fa-tasks" aria-hidden="true"></i> 
                            <?php echo sanitize($item['title']); ?>
                        </h1>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge bg-<?php echo $item['difficulty'] === 'Iniciante' ? 'success' : 'warning'; ?> me-2">
                                <?php echo sanitize($item['difficulty']); ?>
                            </span>
                            <span class="badge bg-secondary">
                                <?php echo sanitize($item['category']); ?>
                            </span>
                        </div>
                        
                        <div class="exercise-content">
                            <?php echo $item['content']; ?>
                        </div>
                        
                        <?php if (!$preview): ?>
                            <div class="mt-4">
                                <button class="btn btn-success me-2" onclick="runCode()">
                                    <i class="fas fa-play" aria-hidden="true"></i> Execute
                                </button>
                                <button class="btn btn-info me-2" onclick="showSolution()">
                                    <i class="fas fa-lightbulb" aria-hidden="true"></i> View Solution
                                </button>
                                <button class="btn btn-warning" onclick="resetCode()">
                                    <i class="fas fa-undo" aria-hidden="true"></i> Reset
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <?php if (!$preview): ?>
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h6 mb-0">Code Editor</h2>
                        </div>
                        <div class="card-body">
                            <textarea id="codeEditor" class="form-control" rows="15" style="font-family: monospace;"><?php echo sanitize($item['starter_code']); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h2 class="h6 mb-0">Result</h2>
                        </div>
                        <div class="card-body">
                            <iframe id="result" style="width: 100%; height: 300px; border: 1px solid #ddd;"></iframe>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
                        <strong>Modo Visualização:</strong> To practice this exercise, 
                        <a href="show.php?type=exercise&id=<?php echo $id; ?>">click here</a>.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    <?php elseif ($type === 'tutorial'): ?>
        <!-- Layout para tutoriais -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <article class="card">
                    <div class="card-header bg-info text-white">
                        <h1 class="h4 mb-0">
                            <i class="fas fa-book" aria-hidden="true"></i> 
                            <?php echo sanitize($item['title']); ?>
                        </h1>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <span class="badge bg-<?php echo $item['level'] === 'Iniciante' ? 'success' : 'warning'; ?> me-2">
                                <?php echo sanitize($item['level']); ?>
                            </span>
                            <span class="badge bg-secondary me-2">
                                <?php echo sanitize($item['category']); ?>
                            </span>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-clock me-1" aria-hidden="true"></i>
                                <?php echo sanitize($item['duration']); ?>
                            </span>
                        </div>
                        
                        <div class="tutorial-content">
                            <?php echo $item['content']; ?>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button class="btn btn-success me-2">
                                    <i class="fas fa-thumbs-up" aria-hidden="true"></i> Useful
                                </button>
                                <button class="btn btn-outline-secondary">
                                    <i class="fas fa-share" aria-hidden="true"></i> Share
                                </button>
                            </div>
                            <div>
                                <a href="exercises_index.php" class="btn btn-primary">
                                    <i class="fas fa-tasks" aria-hidden="true"></i> Practice Exercises
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>

    <?php elseif ($type === 'forum'): ?>
        <!-- Layout para posts do fórum -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h1 class="h5 mb-0">
                            <i class="fas fa-comments" aria-hidden="true"></i> 
                            <?php echo sanitize($item['title']); ?>
                        </h1>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge bg-secondary me-2">
                                <?php echo sanitize($item['category']); ?>
                            </span>
                            <small class="text-muted">
                                Por <?php echo sanitize($item['author']); ?> em 
                                <?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?>
                            </small>
                        </div>
                        
                        <div class="post-content">
                            <?php echo $item['content']; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Respostas -->
                <?php if (isset($item['replies']) && !empty($item['replies'])): ?>
                    <div class="mt-4">
                        <h2 class="h5">Answers (<?php echo count($item['replies']); ?>)</h2>
                        
                        <?php foreach ($item['replies'] as $reply): ?>
                            <div class="card mt-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <strong><?php echo sanitize($reply['author']); ?></strong>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y H:i', strtotime($reply['created_at'])); ?>
                                        </small>
                                    </div>
                                    <p class="mb-0"><?php echo sanitize($reply['content']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Formulário de resposta -->
                <?php if (isLoggedIn()): ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h2 class="h6 mb-0">Your Answer</h2>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="mb-3">
                                    <textarea class="form-control" rows="4" placeholder="Enter your response..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-reply" aria-hidden="true"></i> Reply
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
                        <a href="login.php">Please log in</a> to participate in the discussion.
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Related Posts</h2>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">No related posts found.</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if ($type === 'exercise' && !$preview): ?>
<script>
function runCode() {
    const code = document.getElementById('codeEditor').value;
    const result = document.getElementById('result');
    const doc = result.contentDocument || result.contentWindow.document;
    doc.open();
    doc.write(code);
    doc.close();
}

function showSolution() {
    const solution = <?php echo json_encode($item['solution']); ?>;
    document.getElementById('codeEditor').value = solution;
    runCode();
}

function resetCode() {
    const starterCode = <?php echo json_encode($item['starter_code']); ?>;
    document.getElementById('codeEditor').value = starterCode;
    const result = document.getElementById('result');
    const doc = result.contentDocument || result.contentWindow.document;
    doc.open();
    doc.write('');
    doc.close();
}

// Executar código inicial
document.addEventListener('DOMContentLoaded', function() {
    runCode();
});
</script>
<?php endif; ?>

<?php include 'footer.php'; ?>

