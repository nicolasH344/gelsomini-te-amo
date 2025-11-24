<?php
require_once 'config.php';
require_once 'gamification_functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$title = 'Conquistas';
$user = getCurrentUser();

// Dar recompensa de login diário
$loginReward = giveLoginReward($user['id']);

// Buscar dados reais do usuário
$stats = getUserStats($user['id']);
$userLevel = $stats['level'];
$userXP = $stats['xp'];
$userCoins = $stats['coins'];
$loginStreak = $stats['login_streak'] ?? 0;

// Calcular progresso para próximo nível
$currentLevelXP = ($userLevel - 1) * 100;
$nextLevelXP = $userLevel * 100;
$progressXP = $userXP - $currentLevelXP;
$neededXP = $nextLevelXP - $currentLevelXP;

// Buscar mascote ativo
$activePet = getActivePet($user['id']);

// Buscar badges do usuário
$userBadges = getUserBadges($user['id']);

include 'header.php';
?>

<style>
.pet-companion {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
    cursor: pointer;
    transition: all 0.3s ease;
}

.pet {
    width: 70px;
    height: 70px;
    background: linear-gradient(45deg, #007bff, #0056b3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    box-shadow: 0 4px 15px rgba(0,123,255,0.3);
    animation: petBounce 2s ease-in-out infinite;
}

.pet:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(0,123,255,0.5);
}

@keyframes petBounce {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.pet-speech {
    position: absolute;
    bottom: 80px;
    right: 0;
    background: #333;
    color: white;
    padding: 8px 12px;
    border-radius: 10px;
    font-size: 0.8rem;
    white-space: nowrap;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s ease;
    max-width: 200px;
    white-space: normal;
}

.pet-speech.show {
    opacity: 1;
    transform: translateY(0);
}

.pet-speech::after {
    content: '';
    position: absolute;
    bottom: -5px;
    right: 20px;
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 5px solid #333;
}

.coin-display {
    background: linear-gradient(45deg, #ffd700, #ffed4e);
    color: #333;
    border-radius: 20px;
    padding: 5px 12px;
    font-weight: bold;
    display: inline-block;
}

.reward-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.5s ease;
}

.reward-notification.show {
    opacity: 1;
    transform: translateX(0);
}
</style>

<div class="container mt-4">
    <?php if ($loginReward): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-gift me-2"></i>
        <strong>Recompensa de Login!</strong> 
        Você ganhou <span class="badge bg-info">+<?php echo $loginReward['xp']; ?> XP</span> 
        e <span class="badge bg-warning">+<?php echo $loginReward['coins']; ?> moedas</span>!
        <?php if ($loginReward['streak'] > 1): ?>
            <br><small>Sequência de login: <?php echo $loginReward['streak']; ?> dias! 🔥</small>
        <?php endif; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <?php if (empty($stats)): ?>
    <div class="alert alert-warning alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Sistema de Gamificação não instalado!</strong> 
        <a href="setup_gamification.php" class="btn btn-sm btn-warning ms-2">
            <i class="fas fa-cog me-1"></i>Instalar Sistema
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Header da página -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-trophy" aria-hidden="true"></i> Conquistas</h1>
            <p class="lead">Acompanhe seu progresso e desbloqueie recompensas especiais</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="coin-display">
                <i class="fas fa-coins me-1"></i><?php echo $userCoins; ?> moedas
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Progresso do usuário -->
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="h5 mb-0">
                        <i class="fas fa-user me-2"></i>Seu Progresso
                    </h2>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <div>
                                <div class="h3 mb-0">Nv.<?php echo $userLevel; ?></div>
                                <small>Nível</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar" style="width: <?php echo ($progressXP / $neededXP) * 100; ?>%"></div>
                    </div>
                    <small class="text-muted"><?php echo $progressXP; ?> / <?php echo $neededXP; ?> XP para o próximo nível</small>
                    
                    <hr>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 text-primary"><?php echo $userXP; ?></div>
                            <small class="text-muted">XP Total</small>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-success"><?php echo count($userBadges); ?></div>
                            <small class="text-muted">Conquistas</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mascote -->
            <div class="card shadow mt-4">
                <div class="card-header bg-info text-white">
                    <h2 class="h6 mb-0">
                        <i class="fas fa-heart me-2"></i>Seu Mascote
                    </h2>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="<?php echo $activePet['icon']; ?> fa-3x text-primary"></i>
                    </div>
                    <h6><?php echo $activePet['name']; ?></h6>
                    <small class="text-muted">Mascote Ativo</small>
                    
                    <hr>
                    
                    <button class="btn btn-outline-primary btn-sm" onclick="changePet()">
                        <i class="fas fa-sync me-1"></i>Trocar Mascote
                    </button>
                </div>
            </div>
        </div>

        <!-- Conquistas -->
        <div class="col-md-8 mb-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h2 class="h5 mb-0">
                        <i class="fas fa-trophy me-2"></i>Suas Conquistas
                    </h2>
                </div>
                <div class="card-body">
                    <?php if (empty($userBadges)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-medal fa-3x text-muted mb-3"></i>
                            <h5>Nenhuma conquista ainda</h5>
                            <p class="text-muted">Complete exercícios e tutoriais para ganhar badges!</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="exercises_index.php" class="btn btn-primary">
                                    <i class="fas fa-tasks me-1"></i>Exercícios
                                </a>
                                <a href="tutorials_index.php" class="btn btn-info">
                                    <i class="fas fa-book-open me-1"></i>Tutoriais
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($userBadges as $badge): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card border-warning">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <i class="<?php echo $badge['icon']; ?> fa-2x text-warning me-3"></i>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1"><?php echo sanitize($badge['name']); ?></h6>
                                                    <p class="text-muted small mb-1"><?php echo sanitize($badge['description']); ?></p>
                                                    <small class="text-success">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?php echo date('d/m/Y', strtotime($badge['earned_at'])); ?>
                                                    </small>
                                                </div>
                                                <?php if (!($badge['reward_claimed'] ?? false)): ?>
                                                    <button class="btn btn-warning btn-sm" onclick="claimReward(<?php echo $badge['id']; ?>)">
                                                        <i class="fas fa-gift"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Loja de Recompensas -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h2 class="h5 mb-0">
                        <i class="fas fa-store me-2"></i>Loja de Recompensas
                    </h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-cat fa-2x text-primary mb-2"></i>
                                    <h6>Gato Programador</h6>
                                    <p class="text-muted small">Mascote fofo que ama código</p>
                                    <div class="coin-display small mb-2">200 moedas</div>
                                    <button class="btn btn-primary btn-sm" onclick="buyItem('pet', 'cat', 200)">
                                        <i class="fas fa-shopping-cart me-1"></i>Comprar
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-dragon fa-2x text-danger mb-2"></i>
                                    <h6>Dragão Código</h6>
                                    <p class="text-muted small">Mascote épico e poderoso</p>
                                    <div class="coin-display small mb-2">500 moedas</div>
                                    <button class="btn btn-info btn-sm" onclick="buyItem('pet', 'dragon', 500)">
                                        <i class="fas fa-shopping-cart me-1"></i>Comprar
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-palette fa-2x text-success mb-2"></i>
                                    <h6>Tema Escuro</h6>
                                    <p class="text-muted small">Tema elegante para o site</p>
                                    <div class="coin-display small mb-2">150 moedas</div>
                                    <button class="btn btn-success btn-sm" onclick="buyItem('theme', 'dark', 150)">
                                        <i class="fas fa-shopping-cart me-1"></i>Comprar
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-star fa-2x text-warning mb-2"></i>
                                    <h6>XP Boost 2x</h6>
                                    <p class="text-muted small">Dobra XP por 24 horas</p>
                                    <div class="coin-display small mb-2">300 moedas</div>
                                    <button class="btn btn-warning btn-sm" onclick="buyItem('boost', 'xp2x', 300)">
                                        <i class="fas fa-shopping-cart me-1"></i>Comprar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Desafios Diários -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="h5 mb-0">
                        <i class="fas fa-calendar-day me-2"></i>Desafios Diários
                    </h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-tasks fa-2x text-success mb-2"></i>
                                    <h6>Complete 3 Exercícios</h6>
                                    <div class="progress mb-2">
                                        <div class="progress-bar bg-success" style="width: 66%"></div>
                                    </div>
                                    <small class="text-muted">2/3 concluídos</small>
                                    <div class="mt-2">
                                        <span class="badge bg-success">+100 XP</span>
                                        <div class="coin-display small d-inline-block ms-1">+50</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-book-open fa-2x text-info mb-2"></i>
                                    <h6>Leia 2 Tutoriais</h6>
                                    <div class="progress mb-2">
                                        <div class="progress-bar bg-info" style="width: 50%"></div>
                                    </div>
                                    <small class="text-muted">1/2 concluídos</small>
                                    <div class="mt-2">
                                        <span class="badge bg-info">+75 XP</span>
                                        <div class="coin-display small d-inline-block ms-1">+30</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-comments fa-2x text-warning mb-2"></i>
                                    <h6>Participe do Fórum</h6>
                                    <div class="progress mb-2">
                                        <div class="progress-bar bg-warning" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted">0/1 concluídos</small>
                                    <div class="mt-2">
                                        <span class="badge bg-warning">+50 XP</span>
                                        <div class="coin-display small d-inline-block ms-1">+25</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mascote Flutuante -->
<div class="pet-companion" onclick="petTalk()">
    <div class="pet">
        <i class="<?php echo $activePet['icon']; ?>"></i>
    </div>
    <div class="pet-speech" id="petSpeech">
        Olá! Continue estudando para ganhar mais XP!
    </div>
</div>

<!-- Notificação de Recompensa -->
<div class="reward-notification alert alert-success" id="rewardNotification">
    <i class="fas fa-gift me-2"></i>
    <span id="rewardText">Recompensa coletada!</span>
</div>

<!-- Modal de Compra -->
<div class="modal fade" id="purchaseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-shopping-cart fa-3x text-primary mb-3"></i>
                <h5 id="itemName">Item</h5>
                <p id="itemDescription">Descrição do item</p>
                <div class="coin-display mb-3" id="itemPrice">0 moedas</div>
                <p class="text-muted">Você tem <span class="coin-display"><?php echo $userCoins; ?> moedas</span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="confirmPurchase()">Comprar</button>
            </div>
        </div>
    </div>
</div>

<script>
let petMessages = [
    "Continue assim! Você está indo muito bem! 💪",
    "Que tal fazer mais um exercício? 📚", 
    "Você está quase subindo de nível! 🚀",
    "Lembre-se de descansar também! 😴",
    "Seus badges estão ficando incríveis! ⭐"
];

let currentItem = null;

function petTalk() {
    const speech = document.getElementById('petSpeech');
    const randomMessage = petMessages[Math.floor(Math.random() * petMessages.length)];
    speech.textContent = randomMessage;
    speech.classList.add('show');
    
    setTimeout(() => {
        speech.classList.remove('show');
    }, 3000);
}

function claimReward(badgeId) {
    // Simular coleta de recompensa
    showRewardNotification('Recompensa coletada! +50 XP e +25 moedas!');
    
    // Atualizar interface (simulado)
    setTimeout(() => {
        location.reload();
    }, 2000);
}

function buyItem(type, item, price) {
    const userCoins = <?php echo $userCoins; ?>;
    
    if (price > userCoins) {
        showInsufficientCoinsAlert(price, userCoins);
        return;
    }
    
    currentItem = {type, item, price};
    
    // Configurar modal
    const itemNames = {
        'cat': 'Gato Programador',
        'dragon': 'Dragão Código', 
        'dark': 'Tema Escuro',
        'xp2x': 'XP Boost 2x'
    };
    
    document.getElementById('itemName').textContent = itemNames[item];
    document.getElementById('itemPrice').textContent = price + ' moedas';
    
    $('#purchaseModal').modal('show');
}

function confirmPurchase() {
    if (!currentItem) return;
    
    $('#purchaseModal').modal('hide');
    
    // Simular compra
    showRewardNotification('Item comprado com sucesso!');
    
    if (currentItem.type === 'pet') {
        setTimeout(() => {
            petTalk();
        }, 1000);
    }
}

function changePet() {
    const pets = [
        {name: 'CodeBot', icon: 'fas fa-robot'},
        {name: 'WebCat', icon: 'fas fa-cat'},
        {name: 'DevDog', icon: 'fas fa-dog'}
    ];
    
    const randomPet = pets[Math.floor(Math.random() * pets.length)];
    
    // Atualizar mascote na interface
    document.querySelector('.pet i').className = randomPet.icon;
    showRewardNotification('Mascote alterado para ' + randomPet.name + '!');
}

function showRewardNotification(message) {
    const notification = document.getElementById('rewardNotification');
    document.getElementById('rewardText').textContent = message;
    notification.classList.add('show');
    
    setTimeout(() => {
        notification.classList.remove('show');
    }, 4000);
}

function showInsufficientCoinsAlert(needed, current) {
    const alertHtml = `
        <div class="alert alert-warning alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" 
             id="insufficientCoinsAlert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x text-warning me-3"></i>
                <div>
                    <h6 class="alert-heading mb-1">Moedas Insuficientes!</h6>
                    <p class="mb-1">Você precisa de <span class="coin-display">${needed} moedas</span></p>
                    <small class="text-muted">Você tem apenas <span class="coin-display">${current} moedas</span></small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Remove alert anterior se existir
    const existingAlert = document.getElementById('insufficientCoinsAlert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Adiciona novo alert
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    
    // Remove automaticamente após 5 segundos
    setTimeout(() => {
        const alert = document.getElementById('insufficientCoinsAlert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}

// Interação automática do mascote
setInterval(() => {
    if (Math.random() > 0.8) {
        petTalk();
    }
}, 45000);

// Simular conquista de badge aleatória
setTimeout(() => {
    if (Math.random() > 0.6) {
        showRewardNotification('🎉 Novo badge desbloqueado: "Explorador"!');
    }
}, 10000);

// Atualizar progresso dos desafios diários
function updateDailyProgress() {
    const challenges = document.querySelectorAll('.progress-bar');
    challenges.forEach((bar, index) => {
        const currentWidth = parseInt(bar.style.width);
        if (currentWidth < 100 && Math.random() > 0.8) {
            const newWidth = Math.min(100, currentWidth + 25);
            bar.style.width = newWidth + '%';
            
            if (newWidth === 100) {
                showRewardNotification('🎯 Desafio diário concluído!');
            }
        }
    });
}

setInterval(updateDailyProgress, 30000);
</script>

<?php include 'footer.php'; ?>