<?php
require_once 'config.php';
require_once 'exercise_functions.php';

$title = 'Exercícios Interativos';

// Parâmetros de filtro
$category = sanitize($_GET['category'] ?? '');
$difficulty = sanitize($_GET['difficulty'] ?? '');
$search = sanitize($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;

// Buscar exercícios
$exercises = getExercises($category, $difficulty, $search, $page, $perPage);
$totalResults = countExercises($category, $difficulty, $search);
$totalPages = $totalResults > 0 ? ceil($totalResults / $perPage) : 1;

// Estatísticas gerais
$allExercises = getExercisesData();
$totalExercises = count($allExercises);
$categories = getExerciseCategories();

include 'header.php';
?>

<!-- CSS Moderno -->
<style>
:root {
    --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --gradient-4: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    --shadow-lg: 0 20px 60px rgba(0,0,0,0.12);
    --shadow-xl: 0 30px 80px rgba(0,0,0,0.15);
}

/* Hero Header */
.exercises-hero-header {
    position: relative;
    padding: 4rem 0;
    margin: -2rem -15px 3rem -15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 0 0 40px 40px;
    overflow: hidden;
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(255,255,255,0.15) 0%, transparent 50%);
    animation: heroFloat 15s ease-in-out infinite;
}

@keyframes heroFloat {
    0%, 100% { transform: translateY(0) scale(1); }
    50% { transform: translateY(-20px) scale(1.05); }
}

.hero-content {
    position: relative;
    z-index: 2;
    color: white;
}

.hero-icon-wrapper {
    position: relative;
    display: inline-block;
}

.hero-icon-circle {
    width: 100px;
    height: 100px;
    background: rgba(255,255,255,0.2);
    border-radius: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255,255,255,0.3);
    animation: iconPulse 3s ease-in-out infinite;
    position: relative;
    z-index: 2;
}

.hero-icon-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 120px;
    height: 120px;
    background: radial-gradient(circle, rgba(255,255,255,0.4) 0%, transparent 70%);
    border-radius: 50%;
    animation: glowPulse 3s ease-in-out infinite;
    z-index: 1;
}

@keyframes iconPulse {
    0%, 100% { transform: scale(1) rotate(0deg); }
    50% { transform: scale(1.1) rotate(5deg); }
}

@keyframes glowPulse {
    0%, 100% { opacity: 0.3; transform: translate(-50%, -50%) scale(1); }
    50% { opacity: 0.6; transform: translate(-50%, -50%) scale(1.3); }
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 900;
    letter-spacing: -1px;
    text-shadow: 0 4px 20px rgba(0,0,0,0.2);
    margin-bottom: 1rem;
}

.hero-subtitle {
    font-size: 1.25rem;
    opacity: 0.95;
    max-width: 600px;
    margin: 0 auto;
    text-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.hero-stats {
    display: inline-flex;
    gap: 2rem;
    padding: 2rem 3rem;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(20px);
    border-radius: 25px;
    border: 2px solid rgba(255,255,255,0.2);
    margin-top: 2rem;
}

.hero-stat-item {
    text-align: center;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}

.hero-stat-divider {
    width: 2px;
    background: rgba(255,255,255,0.3);
}

/* Cards Modernos */
.modern-exercise-card {
    background: white;
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.modern-exercise-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 25px 60px rgba(0,0,0,0.15);
}

.modern-exercise-card.completed {
    border: 3px solid #06d6a0;
}

.completed-ribbon {
    position: absolute;
    top: 20px;
    right: -35px;
    background: linear-gradient(135deg, #06d6a0 0%, #1b9aaa 100%);
    color: white;
    padding: 5px 40px;
    transform: rotate(45deg);
    box-shadow: 0 4px 12px rgba(6, 214, 160, 0.4);
    z-index: 10;
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.card-gradient-header {
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
}

.card-gradient-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
    animation: headerShine 8s ease-in-out infinite;
}

@keyframes headerShine {
    0%, 100% { transform: translateX(-50%); }
    50% { transform: translateX(50%); }
}

.category-badge-modern {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    background: rgba(255,255,255,0.25);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    color: white;
    font-weight: 700;
    font-size: 0.85rem;
    border: 1px solid rgba(255,255,255,0.3);
}

.difficulty-badge-modern {
    padding: 0.5rem 1rem;
    border-radius: 15px;
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.difficulty-iniciante {
    background: rgba(255,255,255,0.95);
    color: #06d6a0;
    box-shadow: 0 2px 8px rgba(6, 214, 160, 0.3);
}

.difficulty-intermediário {
    background: rgba(255,255,255,0.95);
    color: #f0ad4e;
    box-shadow: 0 2px 8px rgba(240, 173, 78, 0.3);
}

.difficulty-avançado {
    background: rgba(255,255,255,0.95);
    color: #ef476f;
    box-shadow: 0 2px 8px rgba(239, 71, 111, 0.3);
}

.card-body-modern {
    padding: 2rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.exercise-icon-badge {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.exercise-title-modern {
    font-size: 1.4rem;
    font-weight: 800;
    color: #1a202c;
    margin-bottom: 1rem;
    line-height: 1.3;
}

.exercise-description-modern {
    color: #718096;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 1.5rem;
    flex: 1;
}

.exercise-meta-modern {
    display: flex;
    gap: 1.5rem;
    padding-top: 1rem;
    border-top: 2px solid #f7fafc;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #718096;
    font-size: 0.9rem;
    font-weight: 600;
}

.meta-item i {
    color: #667eea;
}

.card-footer-modern {
    padding: 1.5rem 2rem;
    background: #f7fafc;
}

.btn-modern-primary {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    border: none;
    overflow: hidden;
    position: relative;
}

.btn-modern-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-modern-primary:hover::before {
    left: 100%;
}

.btn-modern-primary:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
    color: white;
}

.btn-modern-primary.btn-completed {
    background: linear-gradient(135deg, #06d6a0 0%, #1b9aaa 100%);
    box-shadow: 0 4px 15px rgba(6, 214, 160, 0.4);
}

.btn-modern-primary.btn-completed:hover {
    box-shadow: 0 8px 25px rgba(6, 214, 160, 0.5);
}

.btn-icon {
    width: 32px;
    height: 32px;
    background: rgba(255,255,255,0.2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.btn-modern-primary:hover .btn-icon {
    background: rgba(255,255,255,0.3);
    transform: translateX(5px);
}

/* Responsividade */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-stats {
        flex-direction: column;
        gap: 1rem;
    }
    
    .hero-stat-divider {
        height: 2px;
        width: 100%;
    }
}
</style>

<div class="container mt-4">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Hero Header Moderno -->
    <div class="exercises-hero-header mb-5">
        <div class="hero-background"></div>
        <div class="hero-content text-center">
            <div class="hero-icon-wrapper mb-4">
                <div class="hero-icon-circle">
                    <i class="fas fa-code"></i>
                </div>
                <div class="hero-icon-glow"></div>
            </div>
            <h1 class="hero-title">Exercícios Práticos</h1>
            <p class="hero-subtitle">
                Domine programação através da prática! Resolva desafios reais e aprimore suas habilidades.
            </p>
            
            <div class="hero-stats">
                <div class="hero-stat-item">
                    <div class="stat-number"><?php echo $totalExercises; ?></div>
                    <div class="stat-label">Exercícios</div>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat-item">
                    <div class="stat-number"><?php echo count($categories); ?></div>
                    <div class="stat-label">Linguagens</div>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat-item">
                    <div class="stat-number">3</div>
                    <div class="stat-label">Níveis</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros --><?php echo file_get_contents('exercises_index.php', false, null, strpos(file_get_contents('exercises_index.php'), '<!-- Filtros -->'), 5000); ?>

    <!-- Lista de Exercícios com Cards Modernos -->
    <div class="row">
        <?php if (empty($exercises)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center py-5" style="border-radius: 25px; border: none; background: linear-gradient(135deg, #667eea15, #764ba215);">
                    <i class="fas fa-search fa-3x mb-3" style="color: #667eea;"></i>
                    <h4 class="alert-heading">Nenhum exercício encontrado!</h4>
                    <p>Tente ajustar os filtros ou <a href="exercises_modern.php" class="alert-link">limpar a busca</a>.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($exercises as $exercise): 
                $display_difficulty = $exercise['difficulty'] ?? 'Iniciante';
                $category = $exercise['category'] ?? 'Geral';
                $completed = false; // Simulação
            ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="modern-exercise-card <?php echo $completed ? 'completed' : ''; ?>">
                        <?php if ($completed): ?>
                            <div class="completed-ribbon">
                                <i class="fas fa-trophy"></i>
                                <span>Concluído</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-gradient-header" style="background: linear-gradient(135deg, 
                            <?php echo ['HTML' => '#e34c26', 'CSS' => '#264de4', 'JavaScript' => '#f0db4f', 'PHP' => '#777bb3'][$category] ?? '#6f42c1'; ?> 0%, 
                            <?php echo ['HTML' => '#ff6b35', 'CSS' => '#4361ee', 'JavaScript' => '#ffd93d', 'PHP' => '#9b8fc1'][$category] ?? '#8e5dd9'; ?> 100%)">
                            <div class="card-header-content">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="category-badge-modern">
                                        <i class="fas fa-code me-2"></i>
                                        <span><?php echo htmlspecialchars($category); ?></span>
                                    </div>
                                    <div class="difficulty-badge-modern difficulty-<?php echo strtolower($display_difficulty); ?>">
                                        <i class="fas fa-signal me-1"></i>
                                        <?php echo htmlspecialchars($display_difficulty); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body-modern">
                            <div class="exercise-icon-badge">
                                <i class="fas fa-laptop-code"></i>
                            </div>
                            <h3 class="exercise-title-modern">
                                <?php echo htmlspecialchars($exercise['title'] ?? 'Exercício'); ?>
                            </h3>
                            <p class="exercise-description-modern">
                                <?php echo htmlspecialchars($exercise['description'] ?? 'Descrição do exercício'); ?>
                            </p>
                            
                            <div class="exercise-meta-modern">
                                <div class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?php echo $exercise['estimated_time'] ?? '15 min'; ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-star"></i>
                                    <span><?php echo $exercise['points'] ?? 10; ?> pts</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer-modern">
                            <a href="show.php?type=exercise&id=<?php echo $exercise['id'] ?? 1; ?>" 
                               class="btn-modern-primary <?php echo $completed ? 'btn-completed' : ''; ?>">
                                <span class="btn-text">
                                    <i class="fas fa-<?php echo $completed ? 'redo' : 'rocket'; ?> me-2"></i>
                                    <?php echo $completed ? 'Revisar Exercício' : 'Iniciar Desafio'; ?>
                                </span>
                                <span class="btn-icon">
                                    <i class="fas fa-arrow-right"></i>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</div>

<script>
// Animação de entrada dos cards
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.modern-exercise-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
