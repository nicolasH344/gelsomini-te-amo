<?php
// Incluir configurações
require_once 'config.php';

// Obter parâmetros da URL
$type = $_GET['type'] ?? '';
$id = (int)($_GET['id'] ?? 0);
$preview = isset($_GET['preview']);

// Verificar se tipo e ID são válidos
if (!in_array($type, ['exercise', 'tutorial', 'forum']) || $id <= 0) {
    header('Location: index.php');
    exit;
}

// Definir título da página baseado no tipo
switch ($type) {
    case 'exercise': // O nome do tipo na URL
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

// Busca o item do banco de dados usando a nova função centralizada
$item = fetchItemFromDatabase($type, $id);

// Se item não encontrado, redirecionar
if (!$item) {
    // Adiciona uma mensagem de erro para o usuário
    $_SESSION['error'] = 'O conteúdo solicitado não foi encontrado.';
    header('Location: index.php');
    exit;
}

include 'header.php'; // O body tag está dentro deste arquivo
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
                    <div class="card-header">
                        <h1 class="h5 mb-0">
                            <i class="fas fa-tasks" aria-hidden="true"></i> 
                            <?php echo sanitize($item['title']); ?>
                        </h1>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge bg-<?php echo $item['difficulty_level'] === 'beginner' ? 'success' : 'warning'; ?> me-2">
                                <?php echo sanitize(ucfirst($item['difficulty_level'])); ?>
                            </span>
                            <span class="badge bg-secondary">
                                <?php echo sanitize($item['category']); ?>
                            </span>
                        </div>
                        
                        <div class="exercise-content">
                            <?php echo $item['instructions']; // Usando a coluna 'instructions' do DB ?>
                        </div>
                        
                        <?php if (!$preview): ?>
                            <div class="mt-4">
                                <button class="btn btn-success me-2" onclick="runCode()">
                                    <i class="fas fa-play" aria-hidden="true"></i> Executar
                                </button>
                                <button class="btn btn-info me-2" onclick="showSolution()">
                                    <i class="fas fa-lightbulb" aria-hidden="true"></i> Ver Solução
                                </button>
                                <button class="btn btn-warning" onclick="resetCode()">
                                    <i class="fas fa-undo" aria-hidden="true"></i> Resetar
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
                            <textarea id="codeEditor" class="form-control" rows="15" style="font-family: monospace;"><?php echo sanitize($item['initial_code']); ?></textarea>
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
                        <strong>Modo Visualização:</strong> Para praticar este exercício, 
                        <a href="show.php?type=exercise&id=<?php echo $id; ?>">clique aqui</a>.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    <?php elseif ($type === 'tutorial'): ?>
        <!-- Layout para tutoriais -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <article class="card">
                    <div class="card-header" style="background: var(--info-color);">
                        <h1 class="h4 mb-0">
                            <i class="fas fa-book" aria-hidden="true"></i> 
                            <?php echo sanitize($item['title']); ?>
                        </h1>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <span class="badge bg-<?php echo $item['difficulty_level'] === 'beginner' ? 'success' : 'warning'; ?> me-2">
                                <?php echo sanitize(ucfirst($item['difficulty_level'])); ?>
                            </span>
                            <span class="badge bg-secondary me-2">
                                <?php echo sanitize($item['category'] ?? 'Geral'); ?>
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
                                    <i class="fas fa-share" aria-hidden="true"></i> Compartilhar
                                </button>
                            </div>
                            <div>
                                <a href="exercises_index.php" class="btn btn-primary">
                                    <i class="fas fa-tasks" aria-hidden="true"></i> Praticar Exercícios
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
                    <div class="card-header" style="background: var(--warning-color); color: var(--text-primary);">
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
                            <h2 class="h6 mb-0">Sua Resposta</h2>
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
                        <a href="login.php">Faça login</a> para participar da discussão.
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h6 mb-0">Posts Relacionados</h2>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Nenhum post relacionado encontrado.</p>
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
    const solution = <?php echo json_encode($item['solution_code']); ?>;
    document.getElementById('codeEditor').value = solution;
    runCode();
}

function resetCode() {
    const starterCode = <?php echo json_encode($item['initial_code']); ?>;
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
