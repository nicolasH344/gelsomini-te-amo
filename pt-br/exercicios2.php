<?php
// Incluir configurações
require_once 'config.php';

// Definir título da página
$title = 'Exercícios';

// Dados fictícios de exercícios para demonstração
$exercises = [
    [
        'id' => 1,
        'title' => 'Introdução ao Python',
        'description' => 'Aprenda os fundamentos da programação em Python, incluindo variáveis e tipos de dados.',
        'difficulty' => 'Iniciante',
        'category' => 'Python',
        'completed' => false
    ],
    [
        'id' => 2,
        'title' => 'Estruturas de Controle em Java',
        'description' => 'Pratique laços de repetição e condicionais em Java.',
        'difficulty' => 'Intermediário',
        'category' => 'Java',
        'completed' => false
    ],
    [
        'id' => 3,
        'title' => 'Consultas Básicas SQL',
        'description' => 'Crie e execute consultas SELECT, INSERT, UPDATE e DELETE em SQL.',
        'difficulty' => 'Iniciante',
        'category' => 'SQL',
        'completed' => false
    ],
    [
        'id' => 4,
        'title' => 'Classes e Objetos em C#',
        'description' => 'Entenda os conceitos de programação orientada a objetos com C#.',
        'difficulty' => 'Intermediário',
        'category' => 'C#',
        'completed' => false
    ],
    [
        'id' => 5,
        'title' => 'Manipulação de Listas em Python',
        'description' => 'Trabalhe com listas, tuplas e dicionários em Python.',
        'difficulty' => 'Intermediário',
        'category' => 'Python',
        'completed' => false
    ],
    [
        'id' => 6,
        'title' => 'Herança e Polimorfismo em Java',
        'description' => 'Aprofunde-se em conceitos avançados de POO em Java.',
        'difficulty' => 'Avançado',
        'category' => 'Java',
        'completed' => false
    ],
    [
        'id' => 7,
        'title' => 'Joins e Subconsultas SQL',
        'description' => 'Aprenda a combinar tabelas e usar subconsultas complexas em SQL.',
        'difficulty' => 'Avançado',
        'category' => 'SQL',
        'completed' => false
    ],
    [
        'id' => 8,
        'title' => 'Interfaces e Abstração em C#',
        'description' => 'Explore interfaces e classes abstratas para criar código flexível em C#.',
        'difficulty' => 'Avançado',
        'category' => 'C#',
        'completed' => false
    ]
];

include 'header.php';

?>

<div class="container mt-4">
    <!-- Header da página -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-tasks" aria-hidden="true"></i> Exercícios Práticos</h1>
            <p class="lead">Pratique suas habilidades com exercícios interativos</p>
        </div>
        <div class="col-md-4 text-end">
            <?php if (isAdmin()): ?>
                <a href="#" class="btn btn-success" role="button">
                    <i class="fas fa-plus" aria-hidden="true"></i> Gerenciar Exercícios
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 card-title">Filtrar Exercícios</h2>
            <form method="GET" action="exercises_index.php" class="row g-3">
                <div class="col-md-3">
                    <label for="category" class="form-label">Categoria</label>
                    <select class="form-select" id="category" name="category">
                             <option value="Python">Python</option>
                        <option value="Java">Java</option>
                        <option value="SQL">SQL</option>
                        <option value="C#">C#</option>                  </select>
                </div>
                
                <div class="col-md-3">
                    <label for="difficulty" class="form-label">Dificuldade</label>
                    <select class="form-select" id="difficulty" name="difficulty">
                        <option value="">Todas as dificuldades</option>
                        <option value="Iniciante">Iniciante</option>
                        <option value="Intermediário">Intermediário</option>
                        <option value="Avançado">Avançado</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Digite palavras-chave...">
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search" aria-hidden="true"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de exercícios -->
    <div class="row">
        <?php foreach ($exercises as $exercise): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="badge bg-<?php 
                            echo $exercise["category"] === 'Python' ? 'info' : 
                                ($exercise["category"] === 'Java' ? 'danger' : 
                                ($exercise["category"] === 'SQL' ? 'primary' : 
                                ($exercise["category"] === 'C#' ? 'success' : 'secondary'))); 
                        ?>">
                            <?php echo sanitize($exercise['category']); ?>
                        </span>
                        <span class="badge bg-<?php 
                            echo $exercise['difficulty'] === 'Iniciante' ? 'success' : 
                                ($exercise['difficulty'] === 'Intermediário' ? 'warning' : 'danger'); 
                        ?>">
                            <?php echo sanitize($exercise['difficulty']); ?>
                        </span>
                    </div>
                    
                    <div class="card-body">
                        <h3 class="card-title h5"><?php echo sanitize($exercise['title']); ?></h3>
                        <p class="card-text"><?php echo sanitize($exercise['description']); ?></p>
                        
                        <?php if ($exercise['completed']): ?>
                            <div class="alert alert-success py-2" role="alert">
                                <i class="fas fa-check-circle me-1" aria-hidden="true"></i>
                                <small>Concluído</small>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="d-flex gap-2">
                            <a href="show.php?type=exercise&id=<?php echo $exercise['id']; ?>" 
                               class="btn btn-primary btn-sm flex-fill">
                                <i class="fas fa-play" aria-hidden="true"></i> 
                                <?php echo $exercise['completed'] ? 'Revisar' : 'Começar'; ?>
                            </a>
                            <a href="show.php?type=exercise&id=<?php echo $exercise['id']; ?>&preview=1" 
                               class="btn btn-outline-secondary btn-sm"
                               aria-label="Visualizar exercício <?php echo sanitize($exercise['title']); ?>">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Paginação (simulada) -->
    <nav aria-label="Navegação de páginas dos exercícios" class="mt-4">
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
                <a class="page-link" href="exercicios2.php" aria-label="Ir para página 2">2</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#" aria-label="Ir para página 3">3</a>
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
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h2 class="h5 card-title">
                        <i class="fas fa-lightbulb text-warning" aria-hidden="true"></i> 
                        Dicas para Estudar
                    </h2>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Comece pelos exercícios de nível iniciante
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Pratique regularmente para fixar o conhecimento
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Use o fórum para tirar dúvidas
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h2 class="h5 card-title">
                        <i class="fas fa-chart-line text-info" aria-hidden="true"></i> 
                        Seu Progresso
                    </h2>
                    <p class="mb-2">Exercícios concluídos: <strong>0 de <?php echo count($exercises); ?></strong></p>
                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: 0%" 
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" 
                             aria-label="Progresso geral: 0%">
                            0%
                        </div>
                    </div>
                    <a href="progress.php" class="btn btn-info btn-sm">
                        <link rel="stylesheet" href="style.css">
                        <i class="fas fa-chart-bar" aria-hidden="true"></i> Ver Progresso Detalhado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>