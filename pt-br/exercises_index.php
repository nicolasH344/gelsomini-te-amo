<?php
// Incluir configurações
require_once 'config.php';

// Verificar se as funções necessárias existem
if (!function_exists('getDBConnection') || !function_exists('sanitize')) {
    die("Erro: Funções essenciais não foram carregadas. Verifique o arquivo config.php");
}

// Definir título da página
$title = 'Exercícios';

// Ativar display de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Variáveis para os dados
$exercises = [];
$totalResults = 0;
$totalPages = 1;
$page = 1;

// Parâmetros de filtragem com valores padrão
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$difficulty = isset($_GET['difficulty']) ? sanitize($_GET['difficulty']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Paginação com validação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$perPage = 9;
$offset = ($page - 1) * $perPage;

try {
    // --- Lógica de busca e filtragem ---
    $conn = getDBConnection();

    // Verificar se a conexão foi estabelecida
    if (!$conn) {
        throw new Exception("Não foi possível conectar ao banco de dados. Verifique as credenciais em config.php");
    }

    // Construção da query SQL base
    $sql = "SELECT e.*, ec.name as category_name 
            FROM exercises e 
            LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
            WHERE 1=1";
    $params = [];

    // Filtro de categoria
    if (!empty($category)) {
        $sql .= " AND ec.name = ?";
        $params[] = $category;
    }

    // Filtro de dificuldade
    if (!empty($difficulty)) {
        $difficulty_map = [
            'Iniciante' => 'beginner', 
            'Intermediário' => 'intermediate', 
            'Avançado' => 'advanced'
        ];
        
        if (array_key_exists($difficulty, $difficulty_map)) {
            $sql .= " AND e.difficulty_level = ?";
            $params[] = $difficulty_map[$difficulty];
        }
    }

    // Filtro de busca
    if (!empty($search)) {
        $sql .= " AND (e.title LIKE ? OR e.description LIKE ?)";
        $searchTerm = "%{$search}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    // Query para contar total
    $countSql = "SELECT COUNT(*) as total FROM exercises e 
                 LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
                 WHERE 1=1";
    
    $countParams = [];
    
    if (!empty($category)) {
        $countSql .= " AND ec.name = ?";
        $countParams[] = $category;
    }
    
    if (!empty($difficulty)) {
        $difficulty_map = [
            'Iniciante' => 'beginner', 
            'Intermediário' => 'intermediate', 
            'Avançado' => 'advanced'
        ];
        if (array_key_exists($difficulty, $difficulty_map)) {
            $countSql .= " AND e.difficulty_level = ?";
            $countParams[] = $difficulty_map[$difficulty];
        }
    }
    
    if (!empty($search)) {
        $countSql .= " AND (e.title LIKE ? OR e.description LIKE ?)";
        $searchTerm = "%{$search}%";
        $countParams[] = $searchTerm;
        $countParams[] = $searchTerm;
    }

    // Executar contagem
    $stmtCount = $conn->prepare($countSql);
    $stmtCount->execute($countParams);
    $result = $stmtCount->fetch(PDO::FETCH_ASSOC);
    $totalResults = $result['total'] ?? 0;
    $totalPages = $totalResults > 0 ? ceil($totalResults / $perPage) : 1;

    // Query principal com ordenação e limites
    $sql .= " ORDER BY e.created_at DESC LIMIT ? OFFSET ?";
    
    // Adicionar parâmetros de paginação
    $params[] = $perPage;
    $params[] = $offset;

    // Executar query principal
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Erro específico do PDO
    $error_message = "Erro no banco de dados: " . $e->getMessage();
    error_log("PDO Error: " . $e->getMessage());
    
    // Carregar exercícios de exemplo em caso de erro
    $exercises = [
        [
            'id' => 1,
            'title' => 'Estrutura Básica HTML',
            'description' => 'Aprenda a criar a estrutura básica de uma página HTML',
            'difficulty_level' => 'beginner',
            'category_name' => 'HTML'
        ],
        [
            'id' => 2,
            'title' => 'Estilização com CSS',
            'description' => 'Pratique estilização básica com CSS',
            'difficulty_level' => 'beginner',
            'category_name' => 'CSS'
        ],
        [
            'id' => 3,
            'title' => 'Interatividade com JavaScript',
            'description' => 'Adicione interatividade às suas páginas',
            'difficulty_level' => 'intermediate',
            'category_name' => 'JavaScript'
        ],
        [
            'id' => 4,
            'title' => 'Formulários HTML',
            'description' => 'Crie formulários funcionais e acessíveis',
            'difficulty_level' => 'beginner',
            'category_name' => 'HTML'
        ],
        [
            'id' => 5,
            'title' => 'Layout Responsivo',
            'description' => 'Desenvolva layouts que se adaptam a diferentes telas',
            'difficulty_level' => 'intermediate',
            'category_name' => 'CSS'
        ],
        [
            'id' => 6,
            'title' => 'Manipulação do DOM',
            'description' => 'Aprenda a manipular elementos da página dinamicamente',
            'difficulty_level' => 'intermediate',
            'category_name' => 'JavaScript'
        ]
    ];
    
} catch (Exception $e) {
    // Erro geral
    $error_message = $e->getMessage();
    error_log("General Error: " . $e->getMessage());
}

include 'header.php';
?>

<div class="container mt-4">
    <!-- Exibir mensagem de erro se houver -->
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Erro:</strong> <?php echo htmlspecialchars($error_message); ?>
            <br><small>Exibindo dados de demonstração. Verifique a conexão com o banco de dados.</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <form method="GET" action="">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="category" class="form-label">Categoria</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Todas as categorias</option>
                            <option value="HTML" <?php echo ($category === 'HTML') ? 'selected' : ''; ?>>HTML</option>
                            <option value="CSS" <?php echo ($category === 'CSS') ? 'selected' : ''; ?>>CSS</option>
                            <option value="JavaScript" <?php echo ($category === 'JavaScript') ? 'selected' : ''; ?>>JavaScript</option>
                            <option value="PHP" <?php echo ($category === 'PHP') ? 'selected' : ''; ?>>PHP</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="difficulty" class="form-label">Dificuldade</label>
                        <select class="form-select" id="difficulty" name="difficulty">
                            <option value="">Todas as dificuldades</option>
                            <option value="Iniciante" <?php echo ($difficulty === 'Iniciante') ? 'selected' : ''; ?>>Iniciante</option>
                            <option value="Intermediário" <?php echo ($difficulty === 'Intermediário') ? 'selected' : ''; ?>>Intermediário</option>
                            <option value="Avançado" <?php echo ($difficulty === 'Avançado') ? 'selected' : ''; ?>>Avançado</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Digite palavras-chave...">
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search" aria-hidden="true"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de exercícios -->
    <div class="row">
        <?php if (empty($exercises)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h4 class="alert-heading">
                        <?php echo isset($error_message) ? 'Erro de Conexão' : 'Nenhum exercício encontrado!'; ?>
                    </h4>
                    <p>
                        <?php if (isset($error_message)): ?>
                            Não foi possível carregar os exercícios do banco de dados.
                        <?php else: ?>
                            Tente ajustar os filtros ou <a href="exercises_index.php" class="alert-link">limpar a busca</a>.
                        <?php endif; ?>
                    </p>
                    <?php if (!isset($error_message)): ?>
                        <div class="mt-3">
                            <a href="?category=HTML" class="btn btn-outline-primary btn-sm me-2">HTML</a>
                            <a href="?category=CSS" class="btn btn-outline-primary btn-sm me-2">CSS</a>
                            <a href="?category=JavaScript" class="btn btn-outline-primary btn-sm">JavaScript</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($exercises as $exercise): 
                // Mapear dificuldade do banco para exibição
                $difficulty_map_display = [
                    'beginner' => 'Iniciante', 
                    'intermediate' => 'Intermediário', 
                    'advanced' => 'Avançado'
                ];
                $display_difficulty = $difficulty_map_display[$exercise['difficulty_level']] ?? $exercise['difficulty_level'];
                
                // Verificar progresso do usuário (simulado)
                $completed = false;
            ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="badge bg-<?php 
                                echo ($exercise['category_name'] === 'HTML') ? 'danger' : 
                                    (($exercise['category_name'] === 'CSS') ? 'primary' : 
                                    (($exercise['category_name'] === 'JavaScript') ? 'warning' : 'info')); 
                            ?>">
                                <?php echo htmlspecialchars($exercise['category_name']); ?>
                            </span>
                            <span class="badge bg-<?php 
                                echo ($display_difficulty === 'Iniciante') ? 'success' : 
                                    (($display_difficulty === 'Intermediário') ? 'warning' : 'danger'); 
                            ?>">
                                <?php echo htmlspecialchars($display_difficulty); ?>
                            </span>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title h5"><?php echo htmlspecialchars($exercise['title']); ?></h3>
                            <p class="card-text flex-grow-1"><?php echo htmlspecialchars($exercise['description']); ?></p>
                            
                            <?php if ($completed): ?>
                                <div class="alert alert-success py-2 mt-auto" role="alert">
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
                                    <?php echo $completed ? 'Revisar' : 'Começar'; ?>
                                </a>
                                <a href="show.php?type=exercise&id=<?php echo $exercise['id']; ?>&preview=1" 
                                   class="btn btn-outline-secondary btn-sm"
                                   aria-label="Visualizar exercício <?php echo htmlspecialchars($exercise['title']); ?>"
                                   title="Visualizar exercício">
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Paginação -->
    <?php if ($totalPages > 1): ?>
    <nav aria-label="Navegação de páginas dos exercícios" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?php 
                        $query = $_GET;
                        $query['page'] = $page - 1;
                        echo http_build_query($query);
                    ?>" aria-label="Página anterior">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link" aria-label="Página anterior">
                        <i class="fas fa-chevron-left" aria-hidden="true"></i>
                    </span>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($i === $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?<?php 
                        $query = $_GET;
                        $query['page'] = $i;
                        echo http_build_query($query);
                    ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?php 
                        $query = $_GET;
                        $query['page'] = $page + 1;
                        echo http_build_query($query);
                    ?>" aria-label="Próxima página">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link" aria-label="Próxima página">
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                    </span>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>

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
                    <p class="mb-2">Exercícios disponíveis: <strong><?php echo $totalResults > 0 ? $totalResults : count($exercises); ?></strong></p>
                    <p class="mb-2">Exercícios concluídos: <strong>0</strong></p>
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
    </div>
</div>

<?php include 'footer.php'; ?>