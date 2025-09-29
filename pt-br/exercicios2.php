<?php
// Incluir configurações
require_once 'config.php';

// Definir título da página
$title = 'Exercícios Avançados';

// Dados de exercícios avançados para diferentes linguagens
$advanced_exercises = [
    [
        'id' => 1,
        'title' => 'Algoritmos de Ordenação Avançados',
        'description' => 'Implemente algoritmos como QuickSort, MergeSort e HeapSort em Python',
        'difficulty' => 'Avançado',
        'category' => 'Python',
        'completed' => false,
        'estimated_time' => '2-3 horas',
        'topics' => ['Algoritmos', 'Estruturas de Dados', 'Complexidade']
    ],
    [
        'id' => 2,
        'title' => 'Sistema de Gerenciamento de Memória',
        'description' => 'Desenvolva um sistema de alocação de memória personalizado em C++',
        'difficulty' => 'Avançado',
        'category' => 'C++',
        'completed' => false,
        'estimated_time' => '4-5 horas',
        'topics' => ['Ponteiros', 'Gerenciamento de Memória', 'Performance']
    ],
    [
        'id' => 3,
        'title' => 'API RESTful com Spring Boot',
        'description' => 'Crie uma API completa com autenticação JWT e documentação Swagger',
        'difficulty' => 'Avançado',
        'category' => 'Java',
        'completed' => false,
        'estimated_time' => '3-4 horas',
        'topics' => ['Spring Boot', 'REST API', 'JWT', 'Swagger']
    ],
    [
        'id' => 4,
        'title' => 'Machine Learning com TensorFlow',
        'description' => 'Implemente uma rede neural para classificação de imagens',
        'difficulty' => 'Avançado',
        'category' => 'Python',
        'completed' => false,
        'estimated_time' => '5-6 horas',
        'topics' => ['Machine Learning', 'TensorFlow', 'Redes Neurais']
    ],
    [
        'id' => 5,
        'title' => 'Aplicação React com Redux',
        'description' => 'Desenvolva uma SPA complexa com gerenciamento de estado avançado',
        'difficulty' => 'Avançado',
        'category' => 'JavaScript',
        'completed' => false,
        'estimated_time' => '4-5 horas',
        'topics' => ['React', 'Redux', 'SPA', 'Estado Global']
    ],
    [
        'id' => 6,
        'title' => 'Microserviços com Docker',
        'description' => 'Arquitetura de microserviços usando containers Docker e Kubernetes',
        'difficulty' => 'Avançado',
        'category' => 'DevOps',
        'completed' => false,
        'estimated_time' => '6-8 horas',
        'topics' => ['Docker', 'Kubernetes', 'Microserviços', 'Orquestração']
    ],
    [
        'id' => 7,
        'title' => 'Compilador de Linguagem Simples',
        'description' => 'Construa um compilador básico para uma linguagem de programação simples',
        'difficulty' => 'Avançado',
        'category' => 'C++',
        'completed' => false,
        'estimated_time' => '8-10 horas',
        'topics' => ['Compiladores', 'Análise Léxica', 'Parsing', 'AST']
    ],
    [
        'id' => 8,
        'title' => 'Sistema Distribuído com Go',
        'description' => 'Implemente um sistema distribuído usando Go e gRPC',
        'difficulty' => 'Avançado',
        'category' => 'Go',
        'completed' => false,
        'estimated_time' => '5-6 horas',
        'topics' => ['Go', 'gRPC', 'Sistemas Distribuídos', 'Concorrência']
    ],
    [
        'id' => 9,
        'title' => 'Blockchain e Smart Contracts',
        'description' => 'Desenvolva uma blockchain simples e smart contracts em Solidity',
        'difficulty' => 'Avançado',
        'category' => 'Solidity',
        'completed' => false,
        'estimated_time' => '6-7 horas',
        'topics' => ['Blockchain', 'Smart Contracts', 'Ethereum', 'Web3']
    ],
    [
        'id' => 10,
        'title' => 'Análise de Big Data com Spark',
        'description' => 'Processe grandes volumes de dados usando Apache Spark e Scala',
        'difficulty' => 'Avançado',
        'category' => 'Scala',
        'completed' => false,
        'estimated_time' => '4-5 horas',
        'topics' => ['Big Data', 'Apache Spark', 'Scala', 'Processamento Distribuído']
    ],
    [
        'id' => 11,
        'title' => 'Game Engine em C++',
        'description' => 'Desenvolva um motor de jogos 2D básico com OpenGL',
        'difficulty' => 'Avançado',
        'category' => 'C++',
        'completed' => false,
        'estimated_time' => '10-12 horas',
        'topics' => ['Game Development', 'OpenGL', 'Graphics Programming', 'Engine Architecture']
    ],
    [
        'id' => 12,
        'title' => 'Sistema de Recomendação',
        'description' => 'Implemente um sistema de recomendação usando algoritmos de filtragem colaborativa',
        'difficulty' => 'Avançado',
        'category' => 'Python',
        'completed' => false,
        'estimated_time' => '4-5 horas',
        'topics' => ['Machine Learning', 'Sistemas de Recomendação', 'Filtragem Colaborativa']
    ]
];

include 'header.php';
?>

<div class="container mt-4">
    <!-- Header da página -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-rocket" aria-hidden="true"></i> Exercícios Avançados</h1>
            <p class="lead">Desafie-se com exercícios complexos de múltiplas linguagens de programação</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="d-flex gap-2 justify-content-end">
                <a href="exercises_index.php" class="btn btn-outline-primary" role="button">
                    <i class="fas fa-arrow-left" aria-hidden="true"></i> Exercícios Básicos
                </a>
                <?php if (isAdmin()): ?>
                    <a href="#" class="btn btn-success" role="button">
                        <i class="fas fa-plus" aria-hidden="true"></i> Gerenciar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary"><?php echo count($advanced_exercises); ?></h3>
                    <p class="text-muted mb-0">Exercícios Disponíveis</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">8</h3>
                    <p class="text-muted mb-0">Linguagens</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">0</h3>
                    <p class="text-muted mb-0">Concluídos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info">60+</h3>
                    <p class="text-muted mb-0">Horas de Conteúdo</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 card-title">Filtrar Exercícios Avançados</h2>
            <form method="GET" action="advanced_exercises.php" class="row g-3">
                <div class="col-md-3">
                    <label for="category" class="form-label">Linguagem/Tecnologia</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Todas as linguagens</option>
                        <option value="Python">Python</option>
                        <option value="Java">Java</option>
                        <option value="C++">C++</option>
                        <option value="JavaScript">JavaScript</option>
                        <option value="Go">Go</option>
                        <option value="Scala">Scala</option>
                        <option value="Solidity">Solidity</option>
                        <option value="DevOps">DevOps</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="difficulty" class="form-label">Dificuldade</label>
                    <select class="form-select" id="difficulty" name="difficulty">
                        <option value="">Todas as dificuldades</option>
                        <option value="Avançado" selected>Avançado</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Digite palavras-chave...">
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-primary w-100" onclick="filterExercises()">
                        <i class="fas fa-search" aria-hidden="true"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de exercícios -->
    <div class="row">
        <?php foreach ($advanced_exercises as $exercise): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm exercise-card" 
                     data-category="<?php echo $exercise['category']; ?>" 
                     data-difficulty="<?php echo $exercise['difficulty']; ?>">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="badge bg-<?php 
                            echo $exercise['category'] === 'Python' ? 'success' : 
                                ($exercise['category'] === 'Java' ? 'danger' : 
                                ($exercise['category'] === 'C++' ? 'primary' : 
                                ($exercise['category'] === 'JavaScript' ? 'warning' : 
                                ($exercise['category'] === 'Go' ? 'info' :
                                ($exercise['category'] === 'Scala' ? 'secondary' :
                                ($exercise['category'] === 'Solidity' ? 'dark' : 'light')))))); 
                        ?>">
                            <?php echo sanitize($exercise['category']); ?>
                        </span>
                        <span class="badge bg-danger">
                            <?php echo sanitize($exercise['difficulty']); ?>
                        </span>
                    </div>
                    
                    <div class="card-body">
                        <h3 class="card-title h5"><?php echo sanitize($exercise['title']); ?></h3>
                        <p class="card-text"><?php echo sanitize($exercise['description']); ?></p>
                        
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Tempo estimado: <?php echo sanitize($exercise['estimated_time']); ?>
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <?php foreach ($exercise['topics'] as $topic): ?>
                                <span class="badge bg-light text-dark me-1 mb-1">
                                    <?php echo sanitize($topic); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if ($exercise['completed']): ?>
                            <div class="alert alert-success py-2" role="alert">
                                <i class="fas fa-check-circle me-1" aria-hidden="true"></i>
                                <small>Concluído</small>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="d-flex gap-2">
                            <a href="show.php?type=advanced_exercise&id=<?php echo $exercise['id']; ?>" 
                               class="btn btn-primary btn-sm flex-fill">
                                <i class="fas fa-play" aria-hidden="true"></i> 
                                <?php echo $exercise['completed'] ? 'Revisar' : 'Começar'; ?>
                            </a>
                            <a href="show.php?type=advanced_exercise&id=<?php echo $exercise['id']; ?>&preview=1" 
                               class="btn btn-outline-secondary btn-sm"
                               aria-label="Visualizar exercício <?php echo sanitize($exercise['title']); ?>">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </a>
                            <button class="btn btn-outline-info btn-sm" 
                                    onclick="showExerciseDetails(<?php echo $exercise['id']; ?>)"
                                    aria-label="Detalhes do exercício">
                                <i class="fas fa-info-circle" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Paginação (simulada) -->
    <nav aria-label="Navegação de páginas dos exercícios avançados" class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item disabled">
                <span class="page-link" aria-label="Página anterior">
                    <i class="fas fa-chevron-left" aria-hidden="true"></i>
                </span>
            </li>
            <li class="page-item active" aria-current="page">
                <span class="page-link">1</span>
            </li>
            <li class="page-item">
                <a class="page-link" href="#" aria-label="Ir para página 2">2</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#" aria-label="Próxima página">
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Informações adicionais -->
    <div class="row mt-5">
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h2 class="h5 card-title">
                        <i class="fas fa-graduation-cap text-primary" aria-hidden="true"></i> 
                        Pré-requisitos
                    </h2>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Conhecimento sólido em programação
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Experiência com estruturas de dados
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Familiaridade com algoritmos
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h2 class="h5 card-title">
                        <i class="fas fa-chart-line text-info" aria-hidden="true"></i> 
                        Seu Progresso
                    </h2>
                    <p class="mb-2">Exercícios concluídos: <strong>0 de <?php echo count($advanced_exercises); ?></strong></p>
                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: 0%" 
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" 
                             aria-label="Progresso geral: 0%">
                            0%
                        </div>
                    </div>
                    <a href="progress.php" class="btn btn-info btn-sm">
                        <i class="fas fa-chart-bar" aria-hidden="true"></i> Ver Progresso Detalhado
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h2 class="h5 card-title">
                        <i class="fas fa-trophy text-warning" aria-hidden="true"></i> 
                        Certificações
                    </h2>
                    <p class="mb-2">Complete exercícios para ganhar certificados</p>
                    <div class="mb-3">
                        <small class="text-muted">Próximo certificado: Python Avançado (3/5 exercícios)</small>
                    </div>
                    <a href="certificates.php" class="btn btn-warning btn-sm">
                        <i class="fas fa-medal" aria-hidden="true"></i> Ver Certificados
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalhes do exercício -->
<div class="modal fade" id="exerciseDetailsModal" tabindex="-1" aria-labelledby="exerciseDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exerciseDetailsModalLabel">Detalhes do Exercício</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="exerciseDetailsContent">
                <!-- Conteúdo será carregado dinamicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="startExerciseBtn">Começar Exercício</button>
            </div>
        </div>
    </div>
</div>

<script>
// Função para mostrar detalhes do exercício
function showExerciseDetails(exerciseId) {
    const exercises = <?php echo json_encode($advanced_exercises); ?>;
    const exercise = exercises.find(ex => ex.id === exerciseId);
    
    if (exercise) {
        const content = `
            <div class="row">
                <div class="col-md-8">
                    <h4>${exercise.title}</h4>
                    <p class="lead">${exercise.description}</p>
                    
                    <h6>Tópicos Abordados:</h6>
                    <div class="mb-3">
                        ${exercise.topics.map(topic => `<span class="badge bg-primary me-1">${topic}</span>`).join('')}
                    </div>
                    
                    <h6>Objetivos de Aprendizado:</h6>
                    <ul>
                        <li>Aplicar conceitos avançados de ${exercise.category}</li>
                        <li>Resolver problemas complexos de programação</li>
                        <li>Implementar soluções eficientes e escaláveis</li>
                        <li>Seguir boas práticas de desenvolvimento</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Informações</h6>
                            <p><strong>Linguagem:</strong> ${exercise.category}</p>
                            <p><strong>Dificuldade:</strong> ${exercise.difficulty}</p>
                            <p><strong>Tempo estimado:</strong> ${exercise.estimated_time}</p>
                            <p><strong>Status:</strong> ${exercise.completed ? 'Concluído' : 'Não iniciado'}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('exerciseDetailsContent').innerHTML = content;
        document.getElementById('startExerciseBtn').onclick = () => {
            window.location.href = `show.php?type=advanced_exercise&id=${exerciseId}`;
        };
        
        new bootstrap.Modal(document.getElementById('exerciseDetailsModal')).show();
    }
}
</script>

<?php include 'footer.php'; ?>