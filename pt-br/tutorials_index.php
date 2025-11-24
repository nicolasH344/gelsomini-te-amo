<?php
require_once 'config.php';

$title = 'Tutoriais';

// Parâmetros de filtro
$category = sanitize($_GET['category'] ?? '');
$level = sanitize($_GET['level'] ?? '');
$search = sanitize($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;

// Buscar tutoriais do banco
$tutorials = [];
$categories = [];
$conn = getDBConnection();

if ($conn) {
    // Buscar categorias
    $catResult = $conn->query("SELECT * FROM categories ORDER BY name");
    if ($catResult) {
        while ($row = $catResult->fetch_assoc()) {
            $categories[] = [
                'name' => $row['name'],
                'slug' => strtolower($row['name']),
                'color' => 'primary',
                'icon' => 'fas fa-code'
            ];
        }
    }
    
    // Buscar tutoriais
    $where = [];
    $params = [];
    $types = '';
    
    if ($category) {
        $where[] = "c.name = ?";
        $params[] = $category;
        $types .= 's';
    }
    
    if ($level) {
        $where[] = "t.difficulty = ?";
        $params[] = $level;
        $types .= 's';
    }
    
    if ($search) {
        $where[] = "(t.title LIKE ? OR t.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types .= 'ss';
    }
    
    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    
    $sql = "SELECT t.*, c.name as category, 'primary' as category_color, 0 as views, '15 min' as duration
            FROM tutorials t 
            LEFT JOIN categories c ON t.category_id = c.id 
            $whereClause 
            ORDER BY t.created_at DESC 
            LIMIT ? OFFSET ?";
    
    $params[] = $perPage;
    $params[] = ($page - 1) * $perPage;
    $types .= 'ii';
    
    $stmt = $conn->prepare($sql);
    if ($stmt && $types) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $tutorials = $result->fetch_all(MYSQLI_ASSOC);
    } elseif ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $tutorials = $result->fetch_all(MYSQLI_ASSOC);
    }
}

include 'header.php';
?>

<div class="container mt-4">
    <!-- Header da página -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-book-open" aria-hidden="true"></i> Tutoriais</h1>
            <p class="lead">Aprenda desenvolvimento web com nossos tutoriais detalhados</p>
        </div>
        <div class="col-md-4 text-end">
            <?php if (isAdmin()): ?>
                <a href="admin.php" class="btn btn-success" role="button">
                    <i class="fas fa-cogs" aria-hidden="true"></i> Gerenciar Tutoriais
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 card-title">Filtrar Tutoriais</h2>
            <form method="GET" action="tutorials_index.php" class="row g-3">
                <div class="col-md-3">
                    <label for="category" class="form-label">Categoria</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Todas as categorias</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo sanitize($cat['slug']); ?>" <?php echo $category === $cat['slug'] ? 'selected' : ''; ?>>
                                <?php echo sanitize($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="level" class="form-label">Nível</label>
                    <select class="form-select" id="level" name="level">
                        <option value="">Todos os níveis</option>
                        <option value="beginner" <?php echo $level === 'beginner' ? 'selected' : ''; ?>>Iniciante</option>
                        <option value="intermediate" <?php echo $level === 'intermediate' ? 'selected' : ''; ?>>Intermediário</option>
                        <option value="advanced" <?php echo $level === 'advanced' ? 'selected' : ''; ?>>Avançado</option>
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
            </form>
        </div>
    </div>

    <!-- Lista de tutoriais -->
    <div class="row">
        <?php if (empty($tutorials)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h4>Nenhum tutorial encontrado</h4>
                    <p>Tente ajustar os filtros ou volte mais tarde para ver novos conteúdos.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($tutorials as $tutorial): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="badge bg-<?php echo $tutorial['category_color']; ?>">
                                <?php echo sanitize($tutorial['category']); ?>
                            </span>
                            <span class="badge bg-<?php 
                                $level = $tutorial['difficulty'] ?? 'beginner';
                                echo $level === 'beginner' ? 'success' : 
                                    ($level === 'intermediate' ? 'warning' : 'danger'); 
                            ?>">
                                <?php echo ucfirst($level ?? 'beginner'); ?>
                            </span>
                        </div>
                        
                        <div class="card-body">
                            <h3 class="card-title h5 mb-3"><?php echo sanitize($tutorial['title']); ?></h3>
                            <p class="card-text"><?php echo sanitize($tutorial['description']); ?></p>
                            
                            <div class="d-flex justify-content-between text-muted small">
                                <span>
                                    <i class="fas fa-clock me-1" aria-hidden="true"></i>
                                    <?php echo sanitize($tutorial['duration']); ?>
                                </span>
                                <span>
                                    <i class="fas fa-eye me-1" aria-hidden="true"></i>
                                    <?php echo number_format($tutorial['views']); ?> visualizações
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <div class="d-flex gap-2">
                                <a href="show.php?type=tutorial&id=<?php echo $tutorial['id']; ?>" 
                                   class="btn btn-primary btn-sm flex-fill">
                                    <i class="fas fa-play" aria-hidden="true"></i> Ler Tutorial
                                </a>
                                <button class="btn btn-outline-secondary btn-sm" 
                                        onclick="showTutorialPreview(<?php echo $tutorial['id']; ?>)"
                                        aria-label="Visualizar tutorial">
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Categorias populares -->
    <div class="row mt-5">
        <div class="col-12">
            <h2 class="h4 mb-4">Categorias Populares</h2>
        </div>
        
        <?php foreach ($categories as $cat): ?>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-<?php echo $cat['color']; ?> text-white">
                    <div class="card-body text-center">
                        <i class="<?php echo $cat['icon']; ?>" style="font-size: 2rem;" aria-hidden="true"></i>
                        <h3 class="h5 mt-2"><?php echo sanitize($cat['name']); ?></h3>
                        <a href="?category=<?php echo $cat['slug']; ?>" class="btn btn-light btn-sm">
                            Ver Tutoriais
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Informações adicionais -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h2 class="h5 card-title">
                        <i class="fas fa-graduation-cap text-success" aria-hidden="true"></i> 
                        Como Aproveitar os Tutoriais
                    </h2>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Leia com atenção e pratique os exemplos
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Faça anotações dos pontos importantes
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Aplique o conhecimento nos exercícios
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h2 class="h5 card-title">
                        <i class="fas fa-question-circle text-info" aria-hidden="true"></i> 
                        Precisa de Ajuda?
                    </h2>
                    <p class="mb-3">
                        Tem dúvidas sobre algum tutorial? Nossa comunidade está aqui para ajudar!
                    </p>
                    <a href="forum_index.php" class="btn btn-info btn-sm">
                        <i class="fas fa-comments" aria-hidden="true"></i> Ir para o Fórum
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTutorialPreview(id) {
    // Implementar preview do tutorial
    alert('Preview do tutorial ' + id);
}

document.addEventListener('DOMContentLoaded', function() {
    // Filtros em tempo real
    const categorySelect = document.getElementById('category');
    const levelSelect = document.getElementById('level');
    
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            this.closest('form').submit();
        });
    }

    if (levelSelect) {
        levelSelect.addEventListener('change', function() {
            this.closest('form').submit();
        });
    }
});
</script>

<?php include 'footer.php'; ?>