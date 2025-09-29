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
        $title = 'Ejercicio';
        break;
    case 'tutorial':
        $title = 'Tutorial';
        break;
    case 'forum':
        $title = 'Publicación del foro';
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
            'title' => 'Estructura básica HTML',
            'description' => 'Aprenda a crear la estructura básica de una página HTML.',
            'difficulty' => 'Principiante',
            'category' => 'HTML',
            'content' => '<h2>Objetivo</h2><p>Crear una página HTML básica con estructura semántica.</p><h2>Instrucciones</h2><ol><li>Crear la estructura básica de HTML5</li><li>Añadir un encabezado con título</li><li>Incluir un párrafo de introducción</li><li>Añadir una lista no ordenada</li></ol>',
            'starter_code' => '<!DOCTYPE html>\n<html lang="es-MX">\n<head>\n    <meta charset="UTF-8">\n    <meta name="viewport" content="width=device-width, initial-scale=1.0">\ n    <title>Mi página</title>\n</head>\n<body>\n    <!-- Su código aquí -->\n</body>\n</html>',
            'solution' => '<!DOCTYPE html>\n<html lang="es-MX">\n<head>\n    <meta charset="UTF-8">\n    <meta name="viewport" content="width=device-width, initial-scale=1.0">\ n    <title>Mi página</title>\n</head>\n<body>\n    <header>\n        <h1>Bienvenido a mi sitio web</h1>\n    </header>\n       <main>\n        <p>Esta es una página HTML básica. </p>\n        <ul>\n            <li>Elemento 1</li>\n            <li>Elemento 2</li>\n            <li>Elemento 3</li>\n        </ul>\n    </main>\n</body>\n</html>'
        ],
        2 => [
            'id' => 2,
            'title' => 'Estilización con CSS',
            'description' => 'Practique la estilización básica con CSS.',
            'difficulty' => 'Principiante',
            'category' => 'CSS',
            'content' => '<h2>Objetivo</h2><p>Aplicar estilos CSS básicos a una página HTML.</p><h2>Instrucciones</h2><ol><li>Definir colores para el texto</li><li>Cambiar la fuente</li><li>Añadir margen y relleno</li><li>Estilizar la lista</li></ol>',
            'starter_code' => '/* Agregue aquí sus estilos CSS */\nbody {\n    \n}\n\nh1 {\n    \n}\n\np {\n    \n}\n\nul {\n    \n}',
            'solution' => 'body {\n    font-family: Arial, sans-serif;\n    margin: 20px;\n    background-color: #f5f5f5;\n}\n\nh1 {\n    color: #333;\n    text-align: center;\n}\n\np {\n    color: #666;\n    line-height: 1.6;\n}\n\nul {\n    background-color: white;\n    padding: 20px;\n    border-radius: 5px;\n}'
        ]
    ];
    
    $item = $exercises[$id] ?? null;
    
} elseif ($type === 'tutorial') {
    $tutorials = [
        1 => [
            'id' => 1,
            'title' => 'Introducción a HTML5',
            'description' => 'Aprenda los fundamentos de HTML5 y sus principales etiquetas.',
            'category' => 'HTML',
            'content' => '<h2>¿Qué es HTML5?</h2><p>HTML5 es la quinta versión del lenguaje HTML, que ha incorporado muchas mejoras y nuevos elementos semánticos. </p><h2>Etiquetas principales</h2><ul><li><code>&lt;header&gt;</code> - Encabezado de la página o sección</li><li><code>&lt;nav&gt;</code> - Navegación</li><li><code>&lt;main&gt;</code> - Contenido principal</li><li><code>&lt;article&gt;</code> - Artículo independiente</li><li><code>&lt;section&gt;</code> - Sección de contenido</li><li><code>&lt;aside&gt;</code> - Contenido lateral</li><li><code>&lt;footer&gt;</code> - Pie de página</li></ul><h2>Ejemplo práctico</h2><pre><code>&lt;!DOCTYPE html&gt;\n&lt;html lang="es-ES"&gt;\n&lt;head&gt;\n    &lt;meta charset="UTF-8"&gt;\n    &lt;title&gt; Mi página&lt;/title&gt;\n&lt;/head&gt;\n&lt;body&gt;\n    &lt;header&gt;\n        &lt;h1&gt;Título principal&lt;/h1&gt;\n    &lt;/header&gt;\n    &lt;main&gt;\n        &lt;p&gt; Contenido principal aquí&lt;/p&gt;\n    &lt;/main&gt;\n&lt;/body&gt;\n&lt;/html&gt;</code></pre>',
            'duration' => '15 min',
            'level' => 'Principiante'
        ],
        2 => [
            'id' => 2,
            'title' => 'CSS Grid Layout',
            'description' => 'Domina el sistema de cuadrículas CSS para diseños modernos',
            'category' => 'CSS',
            'content' => '<h2>CSS Grid</h2><p>CSS Grid es un sistema de diseño bidimensional que permite crear diseños complejos de forma sencilla. </p><h2>Propiedades básicas</h2><ul><li><code>display: grid</code>: define un contenedor de cuadrícula. </li><li><code>grid-template-columns</code>: define las columnas. </li><li><code>grid-template-rows</code>: define las filas. </li><li><code>gap</code> - Espacio entre elementos</li></ul><h2>Ejemplo</h2><pre><code>.container {\n    display: grid;\n    grid-template-columns: 1fr 2fr 1fr;\n    gap: 20px;\n}</code></pre>',
            'duration' => '25 min',
            'level' => 'Intermedio'
        ]
    ];
    
    $item = $tutorials[$id] ?? null;
    
} elseif ($type === 'forum') {
    $forum_posts = [
        1 => [
            'id' => 1,
            'title' => '¿Cómo empezar con HTML?',
            'content' => '<p>Estoy empezando ahora y me gustaría recibir consejos sobre por dónde empezar con HTML. He leído algunos tutoriales, pero todavía tengo dudas sobre la estructura básica.</p><p>¿Alguien puede darme algunos consejos prácticos?</p>',
            'author' => 'João Silva',
            'category' => 'HTML',
            'created_at' => '2024-01-15 14:30:00',
            'replies' => [
                [
                    'author' => 'Maria Santos',
                    'content' => 'Recomiendo empezar con la estructura básica: DOCTYPE, html, head y body. ¡Practique mucho!',
                    'created_at' => '2024-01-15 15:00:00'
                ],
                [
                    'author' => 'Pedro Costa',
                    'content' => 'Estoy de acuerdo con María. También sugiero utilizar MDN Web Docs como referencia.',
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
                    <li class="breadcrumb-item"><a href="index.php">Início</a></li>
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
                                    <i class="fas fa-play" aria-hidden="true"></i> Ejecutar
                                </button>
                                <button class="btn btn-info me-2" onclick="showSolution()">
                                    <i class="fas fa-lightbulb" aria-hidden="true"></i> Ver solución
                                </button>
                                <button class="btn btn-warning" onclick="resetCode()">
                                    <i class="fas fa-undo" aria-hidden="true"></i> Restablecer
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
                            <h2 class="h6 mb-0">Editor de Código</h2>
                        </div>
                        <div class="card-body">
                            <textarea id="codeEditor" class="form-control" rows="15" style="font-family: monospace;"><?php echo sanitize($item['starter_code']); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h2 class="h6 mb-0">Resultado</h2>
                        </div>
                        <div class="card-body">
                            <iframe id="result" style="width: 100%; height: 300px; border: 1px solid #ddd;"></iframe>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
                        <strong>Modo Visualización:</strong> Para practicar este ejercicio, 
                        <a href="show.php?type=exercise&id=<?php echo $id; ?>">haga clic aquí</a>.
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
                                    <i class="fas fa-thumbs-up" aria-hidden="true"></i> Útil
                                </button>
                                <button class="btn btn-outline-secondary">
                                    <i class="fas fa-share" aria-hidden="true"></i> Compartir
                                </button>
                            </div>
                            <div>
                                <a href="exercises_index.php" class="btn btn-primary">
                                    <i class="fas fa-tasks" aria-hidden="true"></i> Practicar ejercicio
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
                        <h2 class="h5">Respostas (<?php echo count($item['replies']); ?>)</h2>
                        
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
                            <h2 class="h6 mb-0">Su respuesta</h2>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="mb-3">
                                    <textarea class="form-control" rows="4" placeholder="Digite sua resposta..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-reply" aria-hidden="true"></i> Responder
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
                        <a href="login.php">Faça login</a> para participar en el debate.
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Artículos relacionados</h2>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">No se han encontrado entradas relacionadas.</p>
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

