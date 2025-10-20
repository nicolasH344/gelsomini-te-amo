<?php
require_once 'config.php';

$type = sanitize($_GET['type'] ?? '');
$id = (int)($_GET['id'] ?? 0);
$preview = isset($_GET['preview']);

if (!$type || !$id) {
    redirect('index.php');
}

// Carregar dados baseado no tipo
if ($type === 'tutorial') {
    require_once 'data/tutorials.php';
    $items = getTutorials();
    $item = array_filter($items, fn($t) => $t['id'] === $id);
    $item = $item ? array_values($item)[0] : null;
    $title = $item ? $item['title'] : 'Tutorial não encontrado';
} elseif ($type === 'exercise') {
    // Dados fictícios de exercícios
    $exercises = [
        [
            'id' => 1,
            'title' => 'Estrutura Básica HTML',
            'description' => 'Aprenda a criar a estrutura básica de uma página HTML',
            'difficulty' => 'Iniciante',
            'category' => 'HTML',
            'completed' => false,
            'content' => 'Neste exercício você aprenderá a criar uma página HTML básica...'
        ]
    ];
    $item = array_filter($exercises, fn($e) => $e['id'] === $id);
    $item = $item ? array_values($item)[0] : null;
    $title = $item ? $item['title'] : 'Exercício não encontrado';
} else {
    redirect('index.php');
}

if (!$item) {
    redirect($type === 'tutorial' ? 'tutorials_index.php' : 'exercises_index.php');
}

include 'header.php';
?>

<div class="container mt-4">
    <?php if ($preview): ?>
        <div class="alert alert-info">
            <i class="fas fa-eye"></i>
            <strong>Modo Visualização:</strong> Esta é uma prévia do conteúdo.
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="h3 mb-2"><?php echo sanitize($item['title']); ?></h1>
                            <p class="text-muted mb-0"><?php echo sanitize($item['description']); ?></p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary"><?php echo sanitize($item['category']); ?></span>
                            <br>
                            <span class="badge bg-success mt-1"><?php echo sanitize($item['difficulty']); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <?php if ($type === 'tutorial'): ?>
                        <div class="d-flex justify-content-between text-muted small mb-3">
                            <span>
                                <i class="fas fa-clock"></i>
                                Duração: <?php echo sanitize($item['duration']); ?>
                            </span>
                            <span>
                                <i class="fas fa-eye"></i>
                                <?php echo number_format($item['views']); ?> visualizações
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="content">
                        <?php if ($type === 'tutorial'): ?>
                            <h4>Conteúdo do Tutorial</h4>
                            <p>Este é um tutorial sobre <strong><?php echo sanitize($item['title']); ?></strong>.</p>
                            <p><?php echo sanitize($item['description']); ?></p>
                            
                            <h5>O que você vai aprender:</h5>
                            <ul>
                                <li>Conceitos fundamentais</li>
                                <li>Exemplos práticos</li>
                                <li>Melhores práticas</li>
                                <li>Exercícios para fixação</li>
                            </ul>

                            <h5>Pré-requisitos:</h5>
                            <p>Conhecimento básico de desenvolvimento web.</p>

                        <?php else: ?>
                            <h4>Descrição do Exercício</h4>
                            <p><?php echo sanitize($item['description']); ?></p>
                            
                            <h5>Instruções:</h5>
                            <ol>
                                <li>Leia atentamente o enunciado</li>
                                <li>Implemente a solução</li>
                                <li>Teste seu código</li>
                                <li>Submeta para avaliação</li>
                            </ol>

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Atenção:</strong> Este é um exercício prático. Certifique-se de testar sua solução.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex gap-2">
                        <a href="<?php echo $type === 'tutorial' ? 'tutorials_index.php' : 'exercises_index.php'; ?>" 
                           class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        
                        <?php if (!$preview): ?>
                            <?php if ($type === 'exercise'): ?>
                                <button class="btn btn-success">
                                    <i class="fas fa-play"></i> Iniciar Exercício
                                </button>
                            <?php else: ?>
                                <button class="btn btn-primary">
                                    <i class="fas fa-bookmark"></i> Favoritar
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle"></i>
                        Informações
                    </h6>
                </div>
                <div class="card-body">
                    <p><strong>Categoria:</strong> <?php echo sanitize($item['category']); ?></p>
                    <p><strong>Dificuldade:</strong> <?php echo sanitize($item['difficulty']); ?></p>
                    
                    <?php if ($type === 'tutorial'): ?>
                        <p><strong>Duração:</strong> <?php echo sanitize($item['duration']); ?></p>
                        <p><strong>Status:</strong> 
                            <span class="badge bg-<?php echo $item['status'] === 'Publicado' ? 'success' : 'warning'; ?>">
                                <?php echo sanitize($item['status']); ?>
                            </span>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-list"></i>
                        Relacionados
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Outros <?php echo $type === 'tutorial' ? 'tutoriais' : 'exercícios'; ?> que podem interessar:</p>
                    
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action">
                            <small>Exemplo relacionado 1</small>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <small>Exemplo relacionado 2</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>