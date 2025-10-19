@@ -1,110 +1,110 @@
<?php
// Incluir configurações
require_once 'config.php';

// Definir título da página
$title = t('home');

// Obter estatísticas
$stats = getStats();

include 'header.php';
?>

<!-- Hero Section -->
<section class="hero-section py-5 text-white position-relative">
    <div class="container py-5">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="display-4 fw-bold mb-4 fade-in-up">
                        <?php echo t('learn_web_dev'); ?>
                    </h1>
                    <p class="lead mb-4 fade-in-up animation-delay-2">
                        <?php echo t('interactive_platform'); ?>
                    </p>
                    
                    <div class="d-flex flex-column flex-sm-row gap-3 mb-4 fade-in-up animation-delay-4">
                        <?php if (isLoggedIn()): ?>
                            <a href="exercises_index.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-play me-2" aria-hidden="true"></i>
                                <?php echo t('continue_learning', 'Continuar Aprendendo'); ?>
                            </a>
                            <a href="progress.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-chart-line me-2" aria-hidden="true"></i>
                                <?php echo t('view_progress', 'Ver Progresso'); ?>
                            </a>
                        <?php else: ?>
                            <a href="register.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-rocket me-2" aria-hidden="true"></i>
                                <?php echo t('start_now'); ?>
                            </a>
                            <a href="login.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-sign-in-alt me-2" aria-hidden="true"></i>
                                <?php echo t('make_login'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Estatísticas rápidas -->
                    <div class="row text-center fade-in-up animation-delay-6">
                        <div class="col-6 col-md-3">
                            <div class="stat-item">
                                <h3 class="h4 fw-bold stat-value-1"><?php echo number_format($stats['total_users']); ?></h3>
                                <small class="text-light"><?php echo t('students', 'Estudantes'); ?></small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-item">
                                <h3 class="h4 fw-bold stat-value-2"><?php echo $stats['total_exercises']; ?></h3>
                                <small class="text-light"><?php echo t('exercises'); ?></small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-item">
                                <h3 class="h4 fw-bold stat-value-3"><?php echo $stats['total_tutorials']; ?></h3>
                                <small class="text-light"><?php echo t('tutorials'); ?></small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-item">
                                <h3 class="h4 fw-bold stat-value-4"><?php echo number_format($stats['total_forum_posts']); ?></h3>
                                <small class="text-light"><?php echo t('forum_posts', 'Posts no Fórum'); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="hero-image text-center fade-in-up animation-delay-8">
                    <div class="position-relative">
                        <!-- Código animado -->
                        <div class="code-animation bg-dark rounded-3 p-4 shadow-lg">
                            <div class="d-flex align-items-center mb-3-1">
                                <div class="d-flex gap-2">
                                    <div class="bg-danger rounded-circle" style="width: 12px; height: 12px;"></div>
                                    <div class="bg-warning rounded-circle" style="width: 12px; height: 12px;"></div>
                                    <div class="bg-success rounded-circle" style="width: 12px; height: 12px;"></div>
                                </div>
                                <small class="text-muted ms-3">index.html</small>
                            </div>
                            <pre class="text-start text-light mb-0"><code id="typingCode"></code></pre>
                        </div>
                        
                        <!-- Elementos flutuantes -->
                        <div class="floating-elements">
                            <div class="floating-icon" style="top: 10%; left: 10%;">
                                <i class="fab fa-html5 text-warning fa-2x"></i>
                                <i class="fab fa-html5 fa-2x" style="color: var(--danger-color);"></i>
                            </div>
                            <div class="floating-icon animation-delay-1" style="top: 20%; right: 15%;">
                                <i class="fab fa-css3-alt text-info fa-2x"></i>
                                <i class="fab fa-css3-alt fa-2x" style="color: var(--primary-color);"></i>
                            </div>
                            <div class="floating-icon animation-delay-2" style="bottom: 30%; left: 5%;">
                                <i class="fab fa-js-square text-warning fa-2x"></i>
                                <i class="fab fa-js-square fa-2x" style="color: var(--warning-color);"></i>
                            </div>
                            <div class="floating-icon animation-delay-3" style="bottom: 10%; right: 10%;">
                                <i class="fab fa-php text-primary fa-2x"></i>
                                <i class="fab fa-php fa-2x" style="color: var(--info-color);"></i>
                            </div>
                        </div>
                    </div>


@@ -133,7 +133,7 @@ include 'header.php';
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3-2">
                            <i class="fas fa-code fa-3x text-primary"></i>
                            <i class="fas fa-code fa-3x"></i>
                        </div>
                        <h4 class="card-title"><?php echo t('practical_exercises', 'Exercícios Práticos'); ?></h4>
                        <p class="card-text">

@@ -147,7 +147,7 @@ include 'header.php';
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3-3">
                            <i class="fas fa-bolt fa-3x text-warning"></i>
                            <i class="fas fa-bolt fa-3x"></i>
                        </div>
                        <h4 class="card-title"><?php echo t('instant_feedback', 'Feedback Instantâneo'); ?></h4>
                        <p class="card-text">

@@ -161,7 +161,7 @@ include 'header.php';
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3-4">
                            <i class="fas fa-users fa-3x text-success"></i>
                            <i class="fas fa-users fa-3x"></i>
                        </div>
                        <h4 class="card-title"><?php echo t('active_community', 'Comunidade Ativa'); ?></h4>
                        <p class="card-text">

@@ -175,7 +175,7 @@ include 'header.php';
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3-5">
                            <i class="fas fa-chart-line fa-3x text-info"></i>
                            <i class="fas fa-chart-line fa-3x"></i>
                        </div>
                        <h4 class="card-title"><?php echo t('progress_tracking', 'Acompanhamento de Progresso'); ?></h4>
                        <p class="card-text">

@@ -189,7 +189,7 @@ include 'header.php';
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3-6">
                            <i class="fas fa-mobile-alt fa-3x text-danger"></i>
                            <i class="fas fa-mobile-alt fa-3x"></i>
                        </div>
                        <h4 class="card-title"><?php echo t('responsive_design', 'Design Responsivo'); ?></h4>
                        <p class="card-text">

@@ -203,7 +203,7 @@ include 'header.php';
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3-7">
                            <i class="fas fa-universal-access fa-3x text-secondary"></i>
                            <i class="fas fa-universal-access fa-3x"></i>
                        </div>
                        <h4 class="card-title"><?php echo t('accessibility', 'Acessibilidade'); ?></h4>
                        <p class="card-text">


@@ -345,7 +345,7 @@ include 'header.php';
                                <?php echo t('frontend_developer', 'Desenvolvedora Front-end'); ?>
                            </cite>
                        </footer>
                        <div class="mt-3 text-warning">
                        <div class="mt-3" style="color: var(--warning-color);">
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <i class="fas fa-star" aria-hidden="true"></i>


@@ -376,7 +376,7 @@ include 'header.php';
                                <?php echo t('fullstack_developer', 'Estudante de Análise de Sistemas'); ?>
                            </cite>
                        </footer>
                        <div class="mt-3 text-warning">
                        <div class="mt-3" style="color: var(--warning-color);">
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <i class="fas fa-star" aria-hidden="true"></i>


@@ -407,7 +407,7 @@ include 'header.php';
                                <?php echo t('ui_designer', 'Designer UI/UX'); ?>
                            </cite>
                        </footer>
                        <div class="mt-3 text-warning">
                        <div class="mt-3" style="color: var(--warning-color);">
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <i class="fas fa-star" aria-hidden="true"></i>

@@ -423,37 +423,37 @@


<!-- Call to Action -->
<section class="py-5 bg-primary text-white">
<section id="cta-section" class="py-5 bg-primary text-white">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-5 fw-bold mb-4">
                    <?php echo t('ready_to_start', 'Pronto para começar sua jornada?'); ?>
                </h2>
                <p class="lead mb-4">
                    <?php echo t('join_thousands', 'Junte-se a milhares de desenvolvedores que já transformaram suas carreiras conosco.'); ?>
                </p>
                
                <?php if (!isLoggedIn()): ?>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <a href="register.php" class="btn btn-light btn-lg">
                            <i class="fas fa-user-plus me-2" aria-hidden="true"></i>
                            <?php echo t('create_free_account', 'Criar Conta Gratuita'); ?>
                        </a>
                        <a href="exercises_index.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-eye me-2" aria-hidden="true"></i>
                            <?php echo t('explore_content', 'Explorar Conteúdo'); ?>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="exercises_index.php" class="btn btn-light btn-lg">
                        <i class="fas fa-play me-2" aria-hidden="true"></i>
                        <?php echo t('continue_learning', 'Continuar Aprendendo'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>