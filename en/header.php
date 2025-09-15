<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language'] ?? 'pt-BR'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo t('site_description', 'Interactive platform for learning web development with practical exercises and real-time feedback.'); ?>">
    <meta name="keywords" content="desenvolvimento web, HTML, CSS, JavaScript, PHP, exercícios, tutoriais, programação">
    <meta name="author" content="WebLearn">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo isset($title) ? sanitize($title) . ' - ' . t('site_title') : t('site_title'); ?>">
    <meta property="og:description" content="<?php echo t('site_description', 'Interactive platform for learning web development'); ?>">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="<?php echo isset($title) ? sanitize($title) . ' - ' . t('site_title') : t('site_title'); ?>">
    <meta property="twitter:description" content="<?php echo t('site_description', 'Interactive platform for learning web development'); ?>">
    
    <title><?php echo isset($title) ? sanitize($title) . ' - ' . t('site_title') : t('site_title'); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../style.css">
    
    <!-- Preload critical resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="<?php echo getThemeClass(); ?>" id="body">
    <!-- Skip to main content for screen readers -->
    <a href="#main-content" class="visually-hidden-focusable btn btn-primary position-absolute top-0 start-0 m-2" style="z-index: 9999;">
        <?php echo t('skip_to_content', 'Skip to main content'); ?>
    </a>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" role="navigation" aria-label="<?php echo t('main_navigation', 'Main navigation'); ?>">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php" aria-label="<?php echo t('home_page', 'Home'); ?>">
                <i class="fas fa-code me-2" aria-hidden="true"></i>
                <?php echo t('site_title'); ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="<?php echo t('toggle_navigation', 'Toggle navigation'); ?>">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>" 
                           href="index.php" 
                           <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'aria-current="page"' : ''; ?>>
                            <i class="fas fa-home me-1" aria-hidden="true"></i>
                            <?php echo t('home'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'exercises_index.php' ? 'active' : ''; ?>" 
                           href="exercises_index.php"
                           <?php echo basename($_SERVER['PHP_SELF']) === 'exercises_index.php' ? 'aria-current="page"' : ''; ?>>
                            <i class="fas fa-tasks me-1" aria-hidden="true"></i>
                            <?php echo t('exercises'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'tutorials_index.php' ? 'active' : ''; ?>" 
                           href="tutorials_index.php"
                           <?php echo basename($_SERVER['PHP_SELF']) === 'tutorials_index.php' ? 'aria-current="page"' : ''; ?>>
                            <i class="fas fa-book me-1" aria-hidden="true"></i>
                            <?php echo t('tutorials'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'forum_index.php' ? 'active' : ''; ?>" 
                           href="forum_index.php"
                           <?php echo basename($_SERVER['PHP_SELF']) === 'forum_index.php' ? 'aria-current="page"' : ''; ?>>
                            <i class="fas fa-comments me-1" aria-hidden="true"></i>
                            <?php echo t('forum'); ?>
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <?php $user = getCurrentUser(); ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false" 
                               aria-label="<?php echo t('user_menu', 'Menu do usuário'); ?>">
                                <i class="fas fa-user me-1" aria-hidden="true"></i>
                                <?php echo sanitize($user['first_name']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="profile.php">
                                        <i class="fas fa-user me-2" aria-hidden="true"></i>
                                        <?php echo t('profile'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="progress.php">
                                        <i class="fas fa-chart-line me-2" aria-hidden="true"></i>
                                        <?php echo t('progress'); ?>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="toggleSettings()">
                                        <i class="fas fa-cog me-2" aria-hidden="true"></i>
                                        <?php echo t('settings'); ?>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="logout.php">
                                        <i class="fas fa-sign-out-alt me-2" aria-hidden="true"></i>
                                        <?php echo t('logout'); ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt me-1" aria-hidden="true"></i>
                                <?php echo t('login'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                <i class="fas fa-user-plus me-1" aria-hidden="true"></i>
                                <?php echo t('register'); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Botão de configurações sempre visível -->
                    <li class="nav-item">
                        <button class="nav-link btn btn-link" onclick="toggleSettings()" 
                                aria-label="<?php echo t('settings'); ?>" title="<?php echo t('settings'); ?>">
                            <i class="fas fa-palette" aria-hidden="true"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Seletor de Tema e Configurações -->
    <div class="theme-selector collapsed" id="themeSelector" role="dialog" aria-labelledby="settingsTitle" aria-hidden="true">
        <button class="theme-toggle" onclick="toggleSettings()" aria-label="<?php echo t('toggle_settings', 'Switch settings'); ?>">
            <i class="fas fa-palette" aria-hidden="true"></i>
        </button>
        
        <div class="p-3">
            <h5 id="settingsTitle" class="mb-3">
                <i class="fas fa-cog me-2" aria-hidden="true"></i>
                <?php echo t('settings'); ?>
            </h5>
            
            <form method="POST" action="" id="settingsForm">
                <!-- Seletor de Tema -->
                <div class="mb-3">
                    <label class="form-label fw-semibold"><?php echo t('theme'); ?></label>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="theme-option theme-purple <?php echo $_SESSION['theme'] === 'purple' ? 'active' : ''; ?>" 
                                onclick="changeTheme('purple')" 
                                aria-label="<?php echo t('purple_theme'); ?>"
                                title="<?php echo t('purple_theme'); ?>"></button>
                        <button type="button" class="theme-option theme-blue <?php echo $_SESSION['theme'] === 'blue' ? 'active' : ''; ?>" 
                                onclick="changeTheme('blue')" 
                                aria-label="<?php echo t('blue_theme'); ?>"
                                title="<?php echo t('blue_theme'); ?>"></button>
                        <button type="button" class="theme-option theme-green <?php echo $_SESSION['theme'] === 'green' ? 'active' : ''; ?>" 
                                onclick="changeTheme('green')" 
                                aria-label="<?php echo t('green_theme'); ?>"
                                title="<?php echo t('green_theme'); ?>"></button>
                        <button type="button" class="theme-option theme-dark <?php echo $_SESSION['theme'] === 'dark' ? 'active' : ''; ?>" 
                                onclick="changeTheme('dark')" 
                                aria-label="<?php echo t('dark_theme'); ?>"
                                title="<?php echo t('dark_theme'); ?>"></button>
                    </div>
                </div>
                
                <!-- Seletor de Idioma -->
                <div class="mb-3">
                    <label for="languageSelect" class="form-label fw-semibold"><?php echo t('language'); ?></label>
                    <select class="form-select form-select-sm" id="languageSelect" name="language" onchange="changeLanguage(this.value)">
                        <option value="pt-BR" <?php echo $_SESSION['language'] === 'pt-BR' ? 'selected' : ''; ?>>
                            🇧🇷 <?php echo t('portuguese'); ?>
                        </option>
                        <option value="en-US" <?php echo $_SESSION['language'] === 'en-US' ? 'selected' : ''; ?>>
                            🇺🇸 <?php echo t('english'); ?>
                        </option>
                        <option value="es-ES" <?php echo $_SESSION['language'] === 'es-ES' ? 'selected' : ''; ?>>
                            🇪🇸 <?php echo t('spanish'); ?>
                        </option>
                    </select>
                </div>
                
                <!-- Modo Acessibilidade -->
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="accessibilityMode" 
                               <?php echo $_SESSION['accessibility_mode'] ? 'checked' : ''; ?>
                               onchange="toggleAccessibility()">
                        <label class="form-check-label fw-semibold" for="accessibilityMode">
                            <i class="fas fa-universal-access me-1" aria-hidden="true"></i>
                            <?php echo t('colorblind_mode'); ?>
                        </label>
                    </div>
                    <small class="text-muted">
                        <?php echo t('colorblind_help', 'Enables colors and patterns optimized for people with color blindness'); ?>
                    </small>
                </div>
                
                <input type="hidden" name="change_theme" value="1">
                <input type="hidden" name="theme" id="themeInput" value="<?php echo $_SESSION['theme']; ?>">
                <input type="hidden" name="change_language" value="1">
                <input type="hidden" name="toggle_accessibility" value="1" id="accessibilityInput">
            </form>
        </div>
    </div>

    <!-- Overlay para fechar configurações -->
    <div class="settings-overlay" id="settingsOverlay" onclick="closeSettings()" style="display: none;"></div>

    <!-- Main Content -->
    <main id="main-content" role="main" style="padding-top: 80px;">
        
        <!-- Mensagens de Feedback -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2" aria-hidden="true"></i>
                    <?php echo sanitize($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo t('close', 'Close'); ?>"></button>
                </div>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2" aria-hidden="true"></i>
                    <?php echo sanitize($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo t('close', 'Close'); ?>"></button>
                </div>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['warning'])): ?>
            <div class="container mt-3">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                    <?php echo sanitize($_SESSION['warning']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo t('close', 'Close'); ?>"></button>
                </div>
            </div>
            <?php unset($_SESSION['warning']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['info'])): ?>
            <div class="container mt-3">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
                    <?php echo sanitize($_SESSION['info']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo t('close', 'Close'); ?>"></button>
                </div>
            </div>
            <?php unset($_SESSION['info']); ?>
        <?php endif; ?>

    <script>
        // Funções para controle de tema e configurações
        let settingsOpen = false;

        function toggleSettings() {
            const selector = document.getElementById('themeSelector');
            const overlay = document.getElementById('settingsOverlay');
            
            if (settingsOpen) {
                closeSettings();
            } else {
                openSettings();
            }
        }

        function openSettings() {
            const selector = document.getElementById('themeSelector');
            const overlay = document.getElementById('settingsOverlay');
            
            selector.classList.remove('collapsed');
            selector.setAttribute('aria-hidden', 'false');
            overlay.style.display = 'block';
            settingsOpen = true;
            
            // Focus no primeiro elemento
            const firstInput = selector.querySelector('button, input, select');
            if (firstInput) firstInput.focus();
        }

        function closeSettings() {
            const selector = document.getElementById('themeSelector');
            const overlay = document.getElementById('settingsOverlay');
            
            selector.classList.add('collapsed');
            selector.setAttribute('aria-hidden', 'true');
            overlay.style.display = 'none';
            settingsOpen = false;
        }

        function changeTheme(theme) {
            // Atualizar visual imediatamente
            document.body.className = document.body.className.replace(/theme-\w+/g, '');
            document.body.classList.add('theme-' + theme);
            
            // Atualizar botões ativos
            document.querySelectorAll('.theme-option').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector('.theme-option.theme-' + theme).classList.add('active');
            
            // Enviar para servidor
            document.getElementById('themeInput').value = theme;
            
            // Criar formulário temporário para envio
            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';
            
            const themeInput = document.createElement('input');
            themeInput.name = 'theme';
            themeInput.value = theme;
            
            const changeThemeInput = document.createElement('input');
            changeThemeInput.name = 'change_theme';
            changeThemeInput.value = '1';
            
            form.appendChild(themeInput);
            form.appendChild(changeThemeInput);
            document.body.appendChild(form);
            form.submit();
        }

        function changeLanguage(language) {
            // Criar formulário temporário para envio
            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';
            
            const langInput = document.createElement('input');
            langInput.name = 'language';
            langInput.value = language;
            
            const changeLangInput = document.createElement('input');
            changeLangInput.name = 'change_language';
            changeLangInput.value = '1';
            
            form.appendChild(langInput);
            form.appendChild(changeLangInput);
            document.body.appendChild(form);
            form.submit();
        }

        function toggleAccessibility() {
            const checkbox = document.getElementById('accessibilityMode');
            
            // Atualizar visual imediatamente
            if (checkbox.checked) {
                document.body.classList.add('accessibility-mode');
            } else {
                document.body.classList.remove('accessibility-mode');
            }
            
            // Criar formulário temporário para envio
            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';
            
            const accessInput = document.createElement('input');
            accessInput.name = 'toggle_accessibility';
            accessInput.value = '1';
            
            form.appendChild(accessInput);
            document.body.appendChild(form);
            form.submit();
        }

        // Fechar configurações com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && settingsOpen) {
                closeSettings();
            }
        });

        // Aplicar tema inicial
        document.addEventListener('DOMContentLoaded', function() {
            const currentTheme = '<?php echo $_SESSION['theme']; ?>';
            const accessibilityMode = <?php echo $_SESSION['accessibility_mode'] ? 'true' : 'false'; ?>;
            
            document.body.classList.add('theme-' + currentTheme);
            if (accessibilityMode) {
                document.body.classList.add('accessibility-mode');
            }
        });
    </script>

    <style>
        .settings-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            backdrop-filter: blur(2px);
        }
        
        .theme-selector {
            transition: transform 0.3s ease;
        }
        
        @media (max-width: 768px) {
            .theme-selector {
                width: 90%;
                max-width: 300px;
                left: 50%;
                transform: translateX(-50%);
            }
            
            .theme-selector.collapsed {
                transform: translateX(-50%) translateY(calc(100% - 40px));
            }
        }
    </style>

