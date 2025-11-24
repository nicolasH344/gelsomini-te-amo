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
            'content' => 'Neste exercício você aprenderá a criar uma página HTML básica...',
            'objectives' => [
                'Criar a estrutura DOCTYPE HTML5',
                'Implementar tags semânticas',
                'Adicionar metadados essenciais'
            ],
            'technologies' => ['HTML5', 'Semântica Web'],
            'prerequisites' => ['Editor de código', 'Navegador web'],
            'estimated_time' => '30 minutos'
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
        <div class="alert alert-info d-flex align-items-center">
            <i class="fas fa-eye me-2"></i>
            <div>
                <strong>Modo Visualização:</strong> Esta é uma prévia do conteúdo. Alterações não serão salvas.
            </div>
        </div>
    <?php endif; ?>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Início</a></li>
            <li class="breadcrumb-item"><a href="<?php echo $type === 'tutorial' ? 'tutorials_index.php' : 'exercises_index.php'; ?>">
                <?php echo $type === 'tutorial' ? 'Tutoriais' : 'Exercícios'; ?>
            </a></li>
            <li class="breadcrumb-item active"><?php echo sanitize($item['title']); ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <!-- Cabeçalho do Conteúdo -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-1"><?php echo sanitize($item['title']); ?></h1>
                            <p class="mb-0 opacity-75"><?php echo sanitize($item['description']); ?></p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light text-dark me-1"><?php echo sanitize($item['category']); ?></span>
                            <span class="badge bg-<?php 
                                echo $item['difficulty'] === 'Iniciante' ? 'success' : 
                                    ($item['difficulty'] === 'Intermediário' ? 'warning' : 'danger'); 
                            ?>">
                                <?php echo sanitize($item['difficulty']); ?>
                            </span>
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
                    <?php else: ?>
                        <div class="d-flex justify-content-between text-muted small mb-3">
                            <span>
                                <i class="fas fa-stopwatch"></i>
                                Tempo estimado: <?php echo $item['estimated_time']; ?>
                            </span>
                            <span>
                                <i class="fas fa-tasks"></i>
                                <?php echo count($item['objectives']); ?> objetivos
                            </span>
                        </div>
                    <?php endif; ?>

                    <!-- Sistema de Abas -->
                    <ul class="nav nav-tabs mb-4" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#content" type="button" role="tab">
                                <i class="fas fa-file-alt me-2"></i>Conteúdo
                            </button>
                        </li>
                        <?php if ($type === 'exercise'): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#objectives" type="button" role="tab">
                                <i class="fas fa-bullseye me-2"></i>Objetivos
                            </button>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#resources" type="button" role="tab">
                                <i class="fas fa-tools me-2"></i>Recursos
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Aba de Conteúdo -->
                        <div class="tab-pane fade show active" id="content" role="tabpanel">
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

                        <!-- Aba de Objetivos (Exercícios) -->
                        <?php if ($type === 'exercise'): ?>
                        <div class="tab-pane fade" id="objectives" role="tabpanel">
                            <h4>Objetivos de Aprendizado</h4>
                            <div class="list-group">
                                <?php foreach ($item['objectives'] as $objective): ?>
                                <div class="list-group-item">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <?php echo $objective; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Aba de Recursos -->
                        <div class="tab-pane fade" id="resources" role="tabpanel">
                            <h4>Recursos de Apoio</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <i class="fab fa-js-square text-warning fa-2x mb-3"></i>
                                            <h5>Documentação</h5>
                                            <p class="text-muted">Referência oficial e guias</p>
                                            <a href="#" class="btn btn-outline-primary btn-sm">Acessar</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-code text-primary fa-2x mb-3"></i>
                                            <h5>Exemplos de Código</h5>
                                            <p class="text-muted">Projetos completos para estudo</p>
                                            <a href="#" class="btn btn-outline-primary btn-sm">Explorar</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-video text-info fa-2x mb-3"></i>
                                            <h5>Vídeo Aulas</h5>
                                            <p class="text-muted">Conteúdo em formato visual</p>
                                            <a href="#" class="btn btn-outline-primary btn-sm">Assistir</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-book text-success fa-2x mb-3"></i>
                                            <h5>Artigos Relacionados</h5>
                                            <p class="text-muted">Leituras complementares</p>
                                            <a href="#" class="btn btn-outline-primary btn-sm">Ler</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                <button class="btn btn-outline-success" onclick="completeExercise(<?php echo $item['id']; ?>)">
                                    <i class="fas fa-check"></i> Marcar como Concluído
                                </button>
                            <?php else: ?>
                                <button class="btn btn-primary">
                                    <i class="fas fa-bookmark"></i> Favoritar
                                </button>
                                <button class="btn btn-outline-primary">
                                    <i class="fas fa-share-alt"></i> Compartilhar
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Card de Informações -->
            <div class="card mb-4">
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
                    <?php else: ?>
                        <p><strong>Tempo Estimado:</strong> <?php echo $item['estimated_time']; ?></p>
                        <p><strong>Tecnologias:</strong> 
                            <?php foreach ($item['technologies'] as $tech): ?>
                                <span class="badge bg-secondary me-1"><?php echo $tech; ?></span>
                            <?php endforeach; ?>
                        </p>
                    <?php endif; ?>
                    
                    <p><strong>Avaliação:</strong> 
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star-half-alt text-warning"></i>
                        (4.5)
                    </p>
                </div>
            </div>

            <!-- Card de Progresso -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line"></i>
                        Seu Progresso
                    </h6>
                </div>
                <div class="card-body">
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: 65%"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>65% completo</span>
                        <span>15min restantes</span>
                    </div>
                </div>
            </div>

            <!-- Card de Conteúdo Relacionado -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-layer-group"></i>
                        Relacionados
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Outros <?php echo $type === 'tutorial' ? 'tutoriais' : 'exercícios'; ?> que podem interessar:</p>
                    
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fab fa-html5 text-danger me-3"></i>
                            <div>
                                <h6 class="mb-1">Introdução ao HTML5</h6>
                                <small class="text-muted">Tutorial • 25min</small>
                            </div>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fab fa-css3-alt text-primary me-3"></i>
                            <div>
                                <h6 class="mb-1">CSS Grid Layout</h6>
                                <small class="text-muted">Exercício • 15min</small>
                            </div>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fab fa-js-square text-warning me-3"></i>
                            <div>
                                <h6 class="mb-1">JavaScript Básico</h6>
                                <small class="text-muted">Tutorial • 40min</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos específicos para a página show */
.nav-tabs .nav-link {
    color: #6c757d;
    font-weight: 500;
    border: none;
    padding: 0.75rem 1.5rem;
}

.nav-tabs .nav-link.active {
    color: var(--primary-color);
    border-bottom: 3px solid var(--primary-color);
    background: transparent;
}

.nav-tabs .nav-link:hover {
    border-color: transparent;
    color: var(--primary-color);
}

.card {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.list-group-item {
    border: 1px solid #dee2e6;
    margin-bottom: 0.5rem;
    border-radius: 0.375rem;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.75em;
    font-weight: 600;
}

.progress {
    background-color: #e9ecef;
    border-radius: 0.375rem;
}

.progress-bar {
    border-radius: 0.375rem;
}

.btn {
    border-radius: 0.375rem;
    font-weight: 500;
}

.alert {
    border-radius: 0.375rem;
    border: none;
}

/* Responsividade */
@media (max-width: 768px) {
    .nav-tabs .nav-link {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
    
    .card-footer .btn {
        margin-bottom: 0.5rem;
    }
    
    .card-footer .d-flex {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sistema de abas
    const tabTriggers = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            // Atualiza estado ativo
            tabTriggers.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Animação de progresso
    const progressBar = document.querySelector('.progress-bar');
    if (progressBar) {
        setTimeout(() => {
            progressBar.style.transition = 'width 2s ease-in-out';
        }, 500);
    }
});

function completeExercise(exerciseId) {
    if (!confirm('Marcar este exercício como concluído?')) return;
    
    // Simulação de requisição AJAX
    fetch('complete_exercise.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'exercise_id=' + exerciseId + '&score=10'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Exercício marcado como concluído!');
            location.reload();
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        alert('Erro de conexão: ' + error);
    });
}
</script>

<?php include 'footer.php'; ?>