<?php
// Incluir configurações
require_once 'config.php';

// Verificar se usuário está logado
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Você precisa estar logado para acessar esta página.';
    header('Location: login.php');
    exit;
}

// ============================================================
// BANCO DE DADOS CONECTADO
// ============================================================
// Conectar ao banco
$conn = getDBConnection();
if (!$conn) {
    die('Erro ao conectar com o banco de dados');
}

// Criar colunas se não existirem (compatível com MySQL 5.x e 8.x)
try {
    // Verificar e adicionar coluna avatar
    $check = $conn->query("SHOW COLUMNS FROM users LIKE 'avatar'");
    if ($check->rowCount() == 0) {
        $conn->exec("ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL");
    }
    
    // Verificar e adicionar coluna bio
    $check = $conn->query("SHOW COLUMNS FROM users LIKE 'bio'");
    if ($check->rowCount() == 0) {
        $conn->exec("ALTER TABLE users ADD COLUMN bio TEXT NULL");
    }
    
    // Verificar e adicionar coluna website
    $check = $conn->query("SHOW COLUMNS FROM users LIKE 'website'");
    if ($check->rowCount() == 0) {
        $conn->exec("ALTER TABLE users ADD COLUMN website VARCHAR(255) NULL");
    }
} catch (PDOException $e) {
    // Log do erro mas continua execução
    error_log("Erro ao criar colunas de perfil: " . $e->getMessage());
}

// Obter dados completos do usuário do banco
$userId = getCurrentUser()['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = 'Usuário não encontrado';
    header('Location: index.php');
    exit;
}

// ============================================================
// PROCESSAMENTO DE FORMULÁRIOS
// ============================================================
// Processar formulários
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Atualizar informações básicas
        $first_name = sanitize($_POST['first_name']);
        $last_name = sanitize($_POST['last_name']);
        $email = sanitize($_POST['email']);
        $bio = $_POST['bio'] ?? ''; // Não sanitizar muito para preservar formatação
        $website = sanitize($_POST['website']);
        
        try {
            // Verificar se email já está em uso por outro usuário
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $userId]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Este email já está em uso por outro usuário.';
            } else {
                // Atualizar no banco de dados
                $stmt = $conn->prepare("
                    UPDATE users 
                    SET first_name = ?, last_name = ?, email = ?, bio = ?, website = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$first_name, $last_name, $email, $bio, $website, $userId]);
                
                // Atualizar sessão
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                
                // Recarregar dados do usuário
                $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch();
                
                $_SESSION['success'] = 'Perfil atualizado com sucesso!';
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Erro ao atualizar perfil: ' . $e->getMessage();
        }
    }
    
    if (isset($_POST['update_preferences'])) {
        // Atualizar preferências
        $theme = sanitize($_POST['theme']);
        $language = sanitize($_POST['language']);
        $notifications = isset($_POST['notifications']) ? 1 : 0;
        $newsletter = isset($_POST['newsletter']) ? 1 : 0;
        
        try {
            // Criar colunas de preferências se não existirem
            $preference_columns = [
                'theme' => "VARCHAR(50) DEFAULT 'light'",
                'language' => "VARCHAR(10) DEFAULT 'pt'",
                'notifications' => "BOOLEAN DEFAULT 1",
                'newsletter' => "BOOLEAN DEFAULT 0"
            ];
            
            foreach ($preference_columns as $col_name => $col_def) {
                $check = $conn->query("SHOW COLUMNS FROM users LIKE '$col_name'");
                if ($check->rowCount() == 0) {
                    $conn->exec("ALTER TABLE users ADD COLUMN $col_name $col_def");
                }
            }
            
            // Atualizar no banco
            $stmt = $conn->prepare("
                UPDATE users 
                SET theme = ?, language = ?, notifications = ?, newsletter = ? 
                WHERE id = ?
            ");
            $stmt->execute([$theme, $language, $notifications, $newsletter, $userId]);
            
            // Atualizar sessão
            $_SESSION['theme'] = $theme;
            $_SESSION['language'] = $language;
            
            // Recarregar dados
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            $_SESSION['success'] = 'Preferências atualizadas com sucesso!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Erro ao atualizar preferências: ' . $e->getMessage();
        }
    }
    
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        // Processar upload de avatar
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg', 'image/webp'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (in_array($_FILES['avatar']['type'], $allowed_types) && 
            $_FILES['avatar']['size'] <= $max_size) {
            
            $upload_dir = 'uploads/avatars/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $userId . '_' . time() . '.' . $file_extension;
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $filepath)) {
                try {
                    // Remover avatar anterior se existir
                    if (!empty($user['avatar']) && file_exists($user['avatar'])) {
                        @unlink($user['avatar']);
                    }
                    
                    // Atualizar no banco de dados
                    $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                    $stmt->execute([$filepath, $userId]);
                    
                    // Recarregar dados
                    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->execute([$userId]);
                    $user = $stmt->fetch();
                    
                    $_SESSION['success'] = 'Foto de perfil atualizada com sucesso!';
                } catch (PDOException $e) {
                    $_SESSION['error'] = 'Erro ao salvar avatar no banco: ' . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = 'Erro ao fazer upload da imagem.';
            }
        } else {
            $_SESSION['error'] = 'Tipo de arquivo não permitido ou tamanho muito grande (máx. 2MB).';
        }
    }
}

// ============================================================
// ESTATÍSTICAS REAIS DO BANCO DE DADOS
// ============================================================
// Buscar estatísticas reais do banco de dados
try {
    // Verificar se tabela user_progress existe
    $table_exists = $conn->query("SHOW TABLES LIKE 'user_progress'")->rowCount() > 0;
    
    // Exercícios completados
    if ($table_exists) {
        $stmt = $conn->prepare("
            SELECT COUNT(*) as completed 
            FROM user_progress 
            WHERE user_id = ? AND status = 'completed'
        ");
        $stmt->execute([$userId]);
        $exercises_completed = $stmt->fetchColumn();
    } else {
        $exercises_completed = 0;
    }
    
    // Tutoriais visualizados
    $table_exists = $conn->query("SHOW TABLES LIKE 'tutorial_progress'")->rowCount() > 0;
    if ($table_exists) {
        $stmt = $conn->prepare("
            SELECT COUNT(DISTINCT tutorial_id) as viewed 
            FROM tutorial_progress 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $tutorials_viewed = $stmt->fetchColumn() ?: 0;
    } else {
        $tutorials_viewed = 0;
    }
    
    // Posts no fórum
    $table_exists = $conn->query("SHOW TABLES LIKE 'forum_posts'")->rowCount() > 0;
    if ($table_exists) {
        $stmt = $conn->prepare("
            SELECT COUNT(*) as posts 
            FROM forum_posts 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $forum_posts = $stmt->fetchColumn() ?: 0;
    } else {
        $forum_posts = 0;
    }
    
    // Comentários no fórum
    $table_exists = $conn->query("SHOW TABLES LIKE 'forum_comments'")->rowCount() > 0;
    if ($table_exists) {
        $stmt = $conn->prepare("
            SELECT COUNT(*) as comments 
            FROM forum_comments 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $forum_comments = $stmt->fetchColumn() ?: 0;
    } else {
        $forum_comments = 0;
    }
    
    // Badges conquistados
    $table_exists = $conn->query("SHOW TABLES LIKE 'user_badges'")->rowCount() > 0;
    if ($table_exists) {
        $stmt = $conn->prepare("
            SELECT COUNT(*) as badges 
            FROM user_badges 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $badges_earned = $stmt->fetchColumn() ?: 0;
    } else {
        $badges_earned = 0;
    }
    
    // Pontos totais
    if ($conn->query("SHOW TABLES LIKE 'user_progress'")->rowCount() > 0) {
        $stmt = $conn->prepare("
            SELECT COALESCE(SUM(score), 0) as total_score 
            FROM user_progress 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $total_score = $stmt->fetchColumn();
    } else {
        $total_score = 0;
    }
    
    // Calcular nível e XP
    $xp = $total_score;
    $level = floor($xp / 500) + 1; // Cada nível precisa de 500 XP
    $xp_current_level = $xp % 500;
    $next_level_xp = 500;
    
    $user_stats = [
        'exercises_completed' => $exercises_completed,
        'tutorials_viewed' => $tutorials_viewed,
        'forum_posts' => $forum_posts + $forum_comments,
        'badges_earned' => $badges_earned,
        'hours_studied' => ceil($exercises_completed * 0.5), // Estimativa
        'current_streak' => 0, // TODO: Implementar sistema de streak
        'level' => $level,
        'xp' => $xp_current_level,
        'next_level_xp' => $next_level_xp
    ];
    
} catch (PDOException $e) {
    // Fallback para dados vazios em caso de erro
    $user_stats = [
        'exercises_completed' => 0,
        'tutorials_viewed' => 0,
        'forum_posts' => 0,
        'badges_earned' => 0,
        'hours_studied' => 0,
        'current_streak' => 0,
        'level' => 1,
        'xp' => 0,
        'next_level_xp' => 500
    ];
}

// ============================================================
// BADGES REAIS DO BANCO DE DADOS
// ============================================================
// Buscar badges reais do banco
try {
    // Criar tabela de badges se não existir
    $conn->exec("
        CREATE TABLE IF NOT EXISTS badges (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            icon VARCHAR(50) DEFAULT 'fas fa-award',
            color VARCHAR(20) DEFAULT 'primary',
            criteria_type VARCHAR(50),
            criteria_value INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $conn->exec("
        CREATE TABLE IF NOT EXISTS user_badges (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            badge_id INT NOT NULL,
            earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_badge (user_id, badge_id)
        )
    ");
    
    // Inserir badges padrão se não existirem
    $check = $conn->query("SELECT COUNT(*) FROM badges")->fetchColumn();
    if ($check == 0) {
        $default_badges = [
            ['Iniciante', 'Complete seu primeiro exercício', 'fas fa-seedling', 'success', 'exercises', 1],
            ['Curioso', 'Visualize 5 tutoriais', 'fas fa-question', 'info', 'tutorials', 5],
            ['Persistente', 'Complete 10 exercícios', 'fas fa-trophy', 'warning', 'exercises', 10],
            ['Colaborador', 'Faça 5 posts no fórum', 'fas fa-hands-helping', 'primary', 'forum', 5],
            ['Dedicado', 'Complete 25 exercícios', 'fas fa-star', 'info', 'exercises', 25],
            ['Mestre', 'Complete 50 exercícios', 'fas fa-crown', 'danger', 'exercises', 50],
            ['Lenda', 'Complete 100 exercícios', 'fas fa-fire', 'dark', 'exercises', 100]
        ];
        
        $stmt = $conn->prepare("INSERT INTO badges (name, description, icon, color, criteria_type, criteria_value) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($default_badges as $badge) {
            $stmt->execute($badge);
        }
    }
    
    // Verificar e conceder badges automaticamente
    $all_badges = $conn->query("SELECT * FROM badges ORDER BY criteria_value ASC")->fetchAll();
    foreach ($all_badges as $badge) {
        $earned = false;
        
        if ($badge['criteria_type'] === 'exercises') {
            $earned = $user_stats['exercises_completed'] >= $badge['criteria_value'];
        } elseif ($badge['criteria_type'] === 'tutorials') {
            $earned = $user_stats['tutorials_viewed'] >= $badge['criteria_value'];
        } elseif ($badge['criteria_type'] === 'forum') {
            $earned = $user_stats['forum_posts'] >= $badge['criteria_value'];
        }
        
        if ($earned) {
            // Tentar inserir badge (ignora se já existe)
            try {
                $stmt = $conn->prepare("INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)");
                $stmt->execute([$userId, $badge['id']]);
            } catch (PDOException $e) {
                // Ignora erro de duplicata
            }
        }
    }
    
    // Buscar badges do usuário
    $earned_badges = $conn->prepare("
        SELECT badge_id FROM user_badges WHERE user_id = ?
    ");
    $earned_badges->execute([$userId]);
    $earned_badge_ids = array_column($earned_badges->fetchAll(), 'badge_id');
    
    // Formatar badges para exibição
    $user_badges = [];
    foreach ($all_badges as $badge) {
        $user_badges[] = [
            'name' => $badge['name'],
            'icon' => $badge['icon'],
            'color' => $badge['color'],
            'earned' => in_array($badge['id'], $earned_badge_ids),
            'description' => $badge['description']
        ];
    }
    
} catch (PDOException $e) {
    // Fallback para badges vazios
    $user_badges = [
        ['name' => 'Iniciante', 'icon' => 'fas fa-seedling', 'color' => 'success', 'earned' => false, 'description' => 'Complete seu primeiro exercício'],
        ['name' => 'Curioso', 'icon' => 'fas fa-question', 'color' => 'info', 'earned' => false, 'description' => 'Visualize 5 tutoriais']
    ];
}

// Definir título da página
$title = 'Perfil';

include 'header.php';
?>

<div class="container mt-4">
    <!-- Alertas -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-4 mb-4">
            <!-- Card do Perfil -->
            <div class="card profile-card shadow-sm">
                <div class="card-body text-center">
                    <!-- Avatar -->
                    <div class="avatar-container mb-3">
                        <div class="avatar-wrapper position-relative d-inline-block">
                            <img src="<?php echo !empty($user['avatar']) ? $user['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['first_name'] . '+' . $user['last_name']) . '&size=200&background=random'; ?>" 
                                 alt="Avatar" 
                                 class="avatar-img rounded-circle shadow">
                            <button class="btn btn-primary btn-sm avatar-edit-btn" data-bs-toggle="modal" data-bs-target="#avatarModal">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Informações do Usuário -->
                    <h3 class="h5 mb-1"><?php echo sanitize($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                    <p class="text-muted mb-2">@<?php echo sanitize($user['username']); ?></p>
                    
                    <!-- Nível e XP -->
                    <div class="level-badge mb-3">
                        <span class="badge bg-primary">Nível <?php echo $user_stats['level']; ?></span>
                        <div class="progress mt-1" style="height: 6px;">
                            <div class="progress-bar bg-warning" 
                                 style="width: <?php echo ($user_stats['xp'] / $user_stats['next_level_xp']) * 100; ?>%">
                            </div>
                        </div>
                        <small class="text-muted"><?php echo $user_stats['xp']; ?>/<?php echo $user_stats['next_level_xp']; ?> XP</small>
                    </div>
                    
                    <!-- Status -->
                    <div class="status-indicator online mb-3">
                        <i class="fas fa-circle text-success me-1"></i>
                        <span class="text-muted">Online</span>
                    </div>
                </div>
            </div>

            <!-- Card de Estatísticas Rápidas -->
            <div class="card stats-card mt-4 shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Estatísticas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-icon text-primary">
                                <i class="fas fa-dumbbell"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $user_stats['exercises_completed']; ?></div>
                                <div class="stat-label">Exercícios</div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon text-success">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $user_stats['tutorials_viewed']; ?></div>
                                <div class="stat-label">Tutoriais</div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon text-info">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $user_stats['forum_posts']; ?></div>
                                <div class="stat-label">Posts</div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon text-warning">
                                <i class="fas fa-medal"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-value"><?php echo $user_stats['badges_earned']; ?></div>
                                <div class="stat-label">Conquistas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card de Conquistas -->
            <div class="card badges-card mt-4 shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-award me-2"></i>
                        Conquistas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="badges-grid">
                        <?php foreach ($user_badges as $badge): ?>
                            <div class="badge-item <?php echo $badge['earned'] ? 'earned' : 'locked'; ?>" 
                                 data-bs-toggle="tooltip" 
                                 title="<?php echo htmlspecialchars($badge['description'] ?? $badge['name']); ?>">
                                <div class="badge-icon <?php echo $badge['earned'] ? 'bg-' . $badge['color'] : 'bg-secondary'; ?>">
                                    <i class="<?php echo $badge['icon']; ?>"></i>
                                </div>
                                <div class="badge-name"><?php echo $badge['name']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conteúdo Principal -->
        <div class="col-lg-8">
            <!-- Navegação por Abas -->
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
                                <i class="fas fa-user-edit me-2"></i>Informações
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="preferences-tab" data-bs-toggle="tab" data-bs-target="#preferences" type="button" role="tab">
                                <i class="fas fa-cog me-2"></i>Preferências
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                                <i class="fas fa-shield-alt me-2"></i>Segurança
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab">
                                <i class="fas fa-chart-line me-2"></i>Atividade
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content" id="profileTabsContent">
                        <!-- Aba Informações -->
                        <div class="tab-pane fade show active" id="info" role="tabpanel">
                            <form method="POST" action="">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">Nome</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" 
                                               value="<?php echo sanitize($user['first_name']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">Sobrenome</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" 
                                               value="<?php echo sanitize($user['last_name']); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label">Nome de Usuário</label>
                                    <input type="text" class="form-control" id="username" 
                                           value="<?php echo sanitize($user['username']); ?>" disabled>
                                    <div class="form-text">Nome de usuário não pode ser alterado.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo sanitize($user['email']); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Biografia</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="3" 
                                              placeholder="Conte um pouco sobre você..."><?php echo isset($user['bio']) ? sanitize($user['bio']) : ''; ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="url" class="form-control" id="website" name="website" 
                                           value="<?php echo isset($user['website']) ? sanitize($user['website']) : ''; ?>" 
                                           placeholder="https://exemplo.com">
                                </div>
                                
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Salvar Alterações
                                </button>
                            </form>
                        </div>
                        
                        <!-- Aba Preferências -->
                        <div class="tab-pane fade" id="preferences" role="tabpanel">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="theme" class="form-label">Tema</label>
                                    <select class="form-select" id="theme" name="theme">
                                        <option value="light" <?php echo (isset($user['theme']) && $user['theme'] === 'light') ? 'selected' : ''; ?>>Claro</option>
                                        <option value="dark" <?php echo (isset($user['theme']) && $user['theme'] === 'dark') ? 'selected' : ''; ?>>Escuro</option>
                                        <option value="auto" <?php echo (!isset($user['theme']) || $user['theme'] === 'auto') ? 'selected' : ''; ?>>Automático</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="language" class="form-label">Idioma</label>
                                    <select class="form-select" id="language" name="language">
                                        <option value="pt" <?php echo (isset($user['language']) && $user['language'] === 'pt') ? 'selected' : ''; ?>>Português</option>
                                        <option value="en" <?php echo (isset($user['language']) && $user['language'] === 'en') ? 'selected' : ''; ?>>English</option>
                                        <option value="es" <?php echo (isset($user['language']) && $user['language'] === 'es') ? 'selected' : ''; ?>>Español</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="notifications" name="notifications" 
                                               <?php echo (isset($user['notifications']) && $user['notifications']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="notifications">
                                            Receber notificações por e-mail
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter" 
                                               <?php echo (isset($user['newsletter']) && $user['newsletter']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="newsletter">
                                            Receber newsletter
                                        </label>
                                    </div>
                                </div>
                                
                                <button type="submit" name="update_preferences" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Salvar Preferências
                                </button>
                            </form>
                        </div>
                        
                        <!-- Aba Segurança -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <div class="mb-4">
                                <h6 class="mb-3">Alterar Senha</h6>
                                <form>
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Senha Atual</label>
                                        <input type="password" class="form-control" id="current_password">
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">Nova Senha</label>
                                        <input type="password" class="form-control" id="new_password">
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirmar Nova Senha</label>
                                        <input type="password" class="form-control" id="confirm_password">
                                    </div>
                                    <button type="button" class="btn btn-primary" id="changePasswordBtn">
                                        <i class="fas fa-key me-2"></i>Alterar Senha
                                    </button>
                                </form>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="mb-3">Sessões Ativas</h6>
                                <div class="session-list">
                                    <div class="session-item">
                                        <div class="session-info">
                                            <strong>Este dispositivo</strong>
                                            <small class="text-muted">Navegador: Chrome • SO: Windows • Último acesso: Hoje</small>
                                        </div>
                                        <span class="badge bg-success">Atual</span>
                                    </div>
                                    <div class="session-item">
                                        <div class="session-info">
                                            <strong>Dispositivo móvel</strong>
                                            <small class="text-muted">Navegador: Safari • SO: iOS • Último acesso: 2 dias atrás</small>
                                        </div>
                                        <button class="btn btn-sm btn-outline-danger">Encerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Aba Atividade -->
                        <div class="tab-pane fade" id="activity" role="tabpanel">
                            <div class="activity-timeline">
                                <div class="activity-item">
                                    <div class="activity-icon bg-success">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="activity-content">
                                        <h6>Exercício concluído: Estrutura HTML</h6>
                                        <span class="text-muted">Hoje, 14:30</span>
                                    </div>
                                </div>
                                
                                <div class="activity-item">
                                    <div class="activity-icon bg-info">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div class="activity-content">
                                        <h6>Tutorial visualizado: CSS Grid</h6>
                                        <span class="text-muted">Ontem, 16:45</span>
                                    </div>
                                </div>
                                
                                <div class="activity-item">
                                    <div class="activity-icon bg-warning">
                                        <i class="fas fa-comment"></i>
                                    </div>
                                    <div class="activity-content">
                                        <h6>Post no fórum: Dúvida sobre JavaScript</h6>
                                        <span class="text-muted">2 dias atrás</span>
                                    </div>
                                </div>
                                
                                <div class="activity-item">
                                    <div class="activity-icon bg-primary">
                                        <i class="fas fa-medal"></i>
                                    </div>
                                    <div class="activity-content">
                                        <h6>Conquista desbloqueada: Persistente</h6>
                                        <span class="text-muted">3 dias atrás</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Card de Progresso -->
            <div class="card progress-card mt-4 shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-tasks me-2"></i>
                        Meu Progresso
                    </h6>
                </div>
                <div class="card-body">
                    <div class="progress-stats">
                        <div class="progress-item">
                            <div class="progress-info">
                                <span class="progress-label">HTML/CSS</span>
                                <span class="progress-value">75%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: 75%"></div>
                            </div>
                        </div>
                        
                        <div class="progress-item">
                            <div class="progress-info">
                                <span class="progress-label">JavaScript</span>
                                <span class="progress-value">45%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" style="width: 45%"></div>
                            </div>
                        </div>
                        
                        <div class="progress-item">
                            <div class="progress-info">
                                <span class="progress-label">PHP</span>
                                <span class="progress-value">30%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: 30%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Upload de Avatar -->
<div class="modal fade" id="avatarModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alterar Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="avatarForm" method="POST" enctype="multipart/form-data">
                    <div class="text-center mb-3">
                        <div class="avatar-preview mb-3">
                            <img id="avatarPreview" src="<?php echo !empty($user['avatar']) ? $user['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['first_name'] . '+' . $user['last_name']) . '&size=200&background=random'; ?>" 
                                 alt="Preview" class="rounded-circle shadow" style="width: 150px; height: 150px; object-fit: cover;">
                        </div>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*" style="display: none;">
                        <button type="button" class="btn btn-outline-primary w-100" onclick="document.getElementById('avatar').click()">
                            <i class="fas fa-upload me-2"></i>Escolher Imagem
                        </button>
                        <div class="form-text mt-2">
                            Formatos: JPG, PNG, GIF, WEBP (máx. 2MB)
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('avatarForm').submit()">Salvar</button>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --primary-color: #4361ee;
    --secondary-color: #3a0ca3;
    --success-color: #4cc9f0;
    --border-radius: 12px;
    --shadow: 0 5px 20px rgba(0,0,0,0.08);
}

/* Estilos do Perfil */
.profile-card {
    border-radius: var(--border-radius);
    border: none;
}

.avatar-container {
    position: relative;
}

.avatar-img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border: 4px solid white;
    box-shadow: var(--shadow);
}

.avatar-edit-btn {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.level-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.75rem;
    border-radius: var(--border-radius);
}

/* Estatísticas */
.stats-card, .badges-card, .progress-card {
    border: none;
    border-radius: var(--border-radius);
}

.stats-grid {
    display: grid;
    gap: 1rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 40px;
    height: 40px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-heading);
    line-height: 1;
}

.stat-label {
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

/* Conquistas */
.badges-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.badge-item {
    text-align: center;
    padding: 0.75rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.badge-item.earned {
    background: #f8f9fa;
}

.badge-item.locked {
    opacity: 0.5;
}

.badge-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem;
    color: white;
    font-size: 1.25rem;
}

.badge-name {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--text-heading);
}

/* Navegação por Abas */
.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 500;
    padding: 1rem 1.5rem;
    transition: all 0.3s ease;
    background: white;
    border-radius: 8px 8px 0 0;
    margin-right: 0.25rem;
}

.nav-tabs .nav-link.active {
    color: var(--primary-color);
    border-bottom: 3px solid var(--primary-color);
    background: white;
}

.nav-tabs .nav-link:hover {
    color: var(--primary-color);
    border-color: transparent;
    background: white;
}

/* Sessões */
.session-list {
    display: grid;
    gap: 1rem;
}

.session-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.session-info strong {
    display: block;
    margin-bottom: 0.25rem;
}

/* Timeline de Atividade */
.activity-timeline {
    position: relative;
}

.activity-timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.5rem;
    position: relative;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    z-index: 1;
}

.activity-content h6 {
    margin-bottom: 0.25rem;
    color: var(--text-heading);
}

/* Progresso */
.progress-stats {
    display: grid;
    gap: 1.5rem;
}

.progress-item {
    display: grid;
    gap: 0.5rem;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.progress-label {
    font-weight: 500;
    color: var(--text-heading);
}

.progress-value {
    font-weight: 600;
    color: var(--primary-color);
}

/* Modal de Avatar */
.avatar-preview {
    border: 2px dashed #dee2e6;
    border-radius: 50%;
    padding: 1rem;
    display: inline-block;
}

/* Responsividade */
@media (max-width: 768px) {
    .badges-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .stats-grid {
        grid-template-columns: 1fr 1fr;
    }
    
    .nav-tabs .nav-link {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
    
    .session-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}

/* Animações */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.profile-card, .stats-card, .badges-card {
    animation: fadeInUp 0.6s ease-out;
}

/* Scroll personalizado */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 3px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips do Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Preview do avatar antes do upload
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatarPreview');
    
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Alteração de senha (simulação)
    const changePasswordBtn = document.getElementById('changePasswordBtn');
    if (changePasswordBtn) {
        changePasswordBtn.addEventListener('click', function() {
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (!currentPassword || !newPassword || !confirmPassword) {
                showAlert('Por favor, preencha todos os campos.', 'warning');
                return;
            }
            
            if (newPassword !== confirmPassword) {
                showAlert('As senhas não coincidem.', 'error');
                return;
            }
            
            if (newPassword.length < 6) {
                showAlert('A senha deve ter pelo menos 6 caracteres.', 'warning');
                return;
            }
            
            // Simular alteração de senha
            showAlert('Senha alterada com sucesso!', 'success');
            document.getElementById('current_password').value = '';
            document.getElementById('new_password').value = '';
            document.getElementById('confirm_password').value = '';
        });
    }
    
    // Função para mostrar alertas
    function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Inserir antes do primeiro card
        const container = document.querySelector('.container.mt-4');
        container.insertBefore(alertDiv, container.firstChild);
        
        // Remover após 5 segundos
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 5000);
    }
    
    // Efeito de hover nas conquistas
    const badgeItems = document.querySelectorAll('.badge-item.earned');
    badgeItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });
    
    // Atualizar tema em tempo real
    const themeSelect = document.getElementById('theme');
    if (themeSelect) {
        themeSelect.addEventListener('change', function() {
            const selectedTheme = this.value;
            // Aqui você implementaria a mudança de tema
            console.log('Tema alterado para:', selectedTheme);
        });
    }
});
</script>

<?php include 'footer.php'; ?>