<?php
require_once 'config.php';
require_once 'achievements_system.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$title = 'Conquistas';
$user = getCurrentUser();
$user_id = $user['id'];

try {
    require_once 'database.php';
    $db = new Database();
    $achievementsSystem = new AchievementsSystem($db);
    
    $userAchievements = $achievementsSystem->getUserAchievements($user_id);
    $allAchievements = $achievementsSystem->getAllAchievements();
    $totalCoins = $achievementsSystem->getUserCoins($user_id);
    
    $earnedIds = array_column($userAchievements, 'id');
    
    $db->closeConnection();
} catch (Exception $e) {
    $userAchievements = [];
    $allAchievements = [];
    $totalCoins = 0;
    $earnedIds = [];
}

include 'header.php';
?>

<style>
.achievements-hero {
    background: linear-gradient(135deg, #6f42c1 0%, #8e5dd9 50%, #e83e8c 100%);
    color: white;
    padding: 3rem 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.achievements-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: heroGlow 8s ease-in-out infinite;
}

.hero-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
    position: relative;
    z-index: 1;
}

.hero-stat {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.hero-stat-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.hero-stat-value {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.hero-stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.achievement-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    border: 2px solid transparent;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.achievement-card.earned {
    border-color: #28a745;
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.05) 0%, rgba(32, 201, 151, 0.02) 100%);
}

.achievement-card.earned::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(135deg, #28a745, #20c997);
}

.achievement-card:not(.earned) {
    opacity: 0.6;
    filter: grayscale(0.3);
}

.achievement-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 32px rgba(0,0,0,0.15);
}

.achievement-header {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 1rem;
}

.achievement-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    background: linear-gradient(135deg, #6c757d, #495057);
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
    transition: all 0.3s ease;
}

.achievement-card.earned .achievement-icon {
    background: linear-gradient(135deg, #28a745, #20c997);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
    animation: pulse 2s ease-in-out infinite;
}

.achievement-info h5 {
    margin: 0 0 0.5rem 0;
    color: #212529;
    font-weight: 700;
}

.achievement-info p {
    margin: 0;
    color: #6c757d;
    line-height: 1.5;
}

.achievement-reward {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e9ecef;
}

.coins-reward {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: white;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
}

.earned-date {
    font-size: 0.8rem;
    color: #28a745;
    font-weight: 600;
}

.progress-section {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}

.progress-bar-custom {
    height: 12px;
    border-radius: 6px;
    background: #e9ecef;
    overflow: hidden;
    margin-top: 1rem;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #28a745, #20c997);
    border-radius: 6px;
    transition: width 1s ease-out;
    position: relative;
}

.progress-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s ease-in-out infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@keyframes heroGlow {
    0%, 100% { transform: translate(0, 0); opacity: 0.5; }
    50% { transform: translate(-10%, -10%); opacity: 0.8; }
}

.filter-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
    background: white;
    padding: 0.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.filter-tab {
    flex: 1;
    padding: 0.75rem 1rem;
    border: none;
    background: transparent;
    border-radius: 8px;
    font-weight: 600;
    color: #6c757d;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-tab.active {
    background: linear-gradient(135deg, #6f42c1, #8e5dd9);
    color: white;
    box-shadow: 0 4px 12px rgba(111, 66, 193, 0.3);
}

.achievements-grid {
    display: grid;
    gap: 1.5rem;
}

@media (max-width: 768px) {
    .hero-stats {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .achievement-header {
        flex-direction: column;
        text-align: center;
    }
    
    .filter-tabs {
        flex-direction: column;
    }
}
</style>

<div class="achievements-hero">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">
            <i class="fas fa-trophy me-3"></i>
            Suas Conquistas
        </h1>
        <p class="lead mb-4">Acompanhe seu progresso e celebre suas vitórias!</p>
        
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="hero-stat-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="hero-stat-value"><?php echo count($userAchievements); ?></div>
                <div class="hero-stat-label">Conquistas Desbloqueadas</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="hero-stat-value"><?php echo $totalCoins; ?></div>
                <div class="hero-stat-label">Moedas Ganhas</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="hero-stat-value"><?php echo count($allAchievements) > 0 ? round((count($userAchievements) / count($allAchievements)) * 100) : 0; ?>%</div>
                <div class="hero-stat-label">Progresso Total</div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4">
    <!-- Barra de Progresso Geral -->
    <div class="progress-section">
        <h4 class="mb-3">
            <i class="fas fa-chart-line text-primary me-2"></i>
            Progresso Geral
        </h4>
        <div class="d-flex justify-content-between align-items-center">
            <span>Conquistas Desbloqueadas</span>
            <span class="fw-bold"><?php echo count($userAchievements); ?>/<?php echo count($allAchievements); ?></span>
        </div>
        <div class="progress-bar-custom">
            <div class="progress-fill" style="width: <?php echo count($allAchievements) > 0 ? (count($userAchievements) / count($allAchievements)) * 100 : 0; ?>%"></div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filter-tabs">
        <button class="filter-tab active" onclick="filterAchievements('all')">
            <i class="fas fa-list me-1"></i>Todas
        </button>
        <button class="filter-tab" onclick="filterAchievements('earned')">
            <i class="fas fa-check me-1"></i>Desbloqueadas
        </button>
        <button class="filter-tab" onclick="filterAchievements('locked')">
            <i class="fas fa-lock me-1"></i>Bloqueadas
        </button>
    </div>

    <!-- Lista de Conquistas -->
    <div class="achievements-grid">
        <?php foreach ($allAchievements as $achievement): 
            $isEarned = in_array($achievement['id'], $earnedIds);
            $earnedData = null;
            
            if ($isEarned) {
                foreach ($userAchievements as $userAch) {
                    if ($userAch['id'] == $achievement['id']) {
                        $earnedData = $userAch;
                        break;
                    }
                }
            }
        ?>
        <div class="achievement-card <?php echo $isEarned ? 'earned' : 'locked'; ?>" data-filter="<?php echo $isEarned ? 'earned' : 'locked'; ?>">
            <div class="achievement-header">
                <div class="achievement-icon">
                    <i class="<?php echo $achievement['icon']; ?>"></i>
                </div>
                <div class="achievement-info flex-grow-1">
                    <h5><?php echo sanitize($achievement['name']); ?></h5>
                    <p><?php echo sanitize($achievement['description']); ?></p>
                </div>
                <?php if ($isEarned): ?>
                    <div class="achievement-status">
                        <i class="fas fa-check-circle text-success fa-2x"></i>
                    </div>
                <?php else: ?>
                    <div class="achievement-status">
                        <i class="fas fa-lock text-muted fa-2x"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="achievement-reward">
                <div class="coins-reward">
                    <i class="fas fa-coins"></i>
                    <span><?php echo $achievement['coins_reward']; ?> moedas</span>
                </div>
                <?php if ($isEarned && $earnedData): ?>
                    <div class="earned-date">
                        <i class="fas fa-calendar me-1"></i>
                        Desbloqueada em <?php echo date('d/m/Y', strtotime($earnedData['earned_at'])); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function filterAchievements(filter) {
    const cards = document.querySelectorAll('.achievement-card');
    const tabs = document.querySelectorAll('.filter-tab');
    
    // Atualizar tabs
    tabs.forEach(tab => tab.classList.remove('active'));
    event.target.classList.add('active');
    
    // Filtrar cards
    cards.forEach(card => {
        if (filter === 'all') {
            card.style.display = 'block';
        } else {
            if (card.dataset.filter === filter) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        }
    });
}

// Animação de entrada
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.achievement-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

<?php include 'footer.php'; ?>