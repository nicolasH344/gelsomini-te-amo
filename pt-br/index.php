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
                            </div>
                            <div class="floating-icon animation-delay-1" style="top: 20%; right: 15%;">
                                <i class="fab fa-css3-alt text-info fa-2x"></i>
                            </div>
                            <div class="floating-icon animation-delay-2" style="bottom: 30%; left: 5%;">
                                <i class="fab fa-js-square text-warning fa-2x"></i>
                            </div>
                            <div class="floating-icon animation-delay-3" style="bottom: 10%; right: 10%;">
                                <i class="fab fa-php text-primary fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Seção de Recursos -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold text-gradient">
                    <?php echo t('why_choose_us', 'Por que nos escolher?'); ?>
                </h2>
                <p class="lead">
                    <?php echo t('platform_benefits', 'Descubra os benefícios da nossa plataforma de aprendizado'); ?>
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3-2">
                            <i class="fas fa-code fa-3x text-primary"></i>
                        </div>
                        <h4 class="card-title"><?php echo t('practical_exercises', 'Exercícios Práticos'); ?></h4>
                        <p class="card-text">
                            <?php echo t('practical_exercises_desc', 'Aprenda fazendo com exercícios interativos que simulam situações reais de desenvolvimento.'); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3-3">
                            <i class="fas fa-bolt fa-3x text-warning"></i>
                        </div>
                        <h4 class="card-title"><?php echo t('instant_feedback', 'Feedback Instantâneo'); ?></h4>
                        <p class="card-text">
                            <?php echo t('instant_feedback_desc', 'Receba feedback imediato sobre seu código e aprenda com seus erros em tempo real.'); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3-4">
                            <i class="fas fa-users fa-3x text-success"></i>
                        </div>
                        <h4 class="card-title"><?php echo t('active_community', 'Comunidade Ativa'); ?></h4>
                        <p class="card-text">
                            <?php echo t('active_community_desc', 'Conecte-se com outros desenvolvedores, tire dúvidas e compartilhe conhecimento.'); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3-5">
                            <i class="fas fa-chart-line fa-3x text-info"></i>
                        </div>
                        <h4 class="card-title"><?php echo t('progress_tracking', 'Acompanhamento de Progresso'); ?></h4>
                        <p class="card-text">
                            <?php echo t('progress_tracking_desc', 'Monitore seu progresso com estatísticas detalhadas e conquiste seus objetivos.'); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3-6">
                            <i class="fas fa-mobile-alt fa-3x text-danger"></i>
                        </div>
                        <h4 class="card-title"><?php echo t('responsive_design', 'Design Responsivo'); ?></h4>
                        <p class="card-text">
                            <?php echo t('responsive_design_desc', 'Acesse a plataforma de qualquer dispositivo, a qualquer hora e em qualquer lugar.'); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3-7">
                            <i class="fas fa-universal-access fa-3x text-secondary"></i>
                        </div>
                        <h4 class="card-title"><?php echo t('accessibility', 'Acessibilidade'); ?></h4>
                        <p class="card-text">
                            <?php echo t('accessibility_desc', 'Plataforma totalmente acessível, incluindo suporte para pessoas com daltonismo.'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Seção de Trilhas de Aprendizado -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold text-gradient">
                    <?php echo t('learning_paths', 'Trilhas de Aprendizado'); ?>
                </h2>
                <p class="lead">
                    <?php echo t('learning_paths_desc', 'Escolha sua jornada e domine as tecnologias web'); ?>
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card path-card path-card-beginner h-100">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-seedling me-2" aria-hidden="true"></i>
                            <?php echo t('beginner_path', 'Trilha Iniciante'); ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <?php echo t('beginner_path_desc', 'Perfeita para quem está começando. Aprenda HTML, CSS e JavaScript do zero.'); ?>
                        </p>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check me-2" aria-hidden="true"></i>
                                HTML5 <?php echo t('fundamentals', 'Fundamentos'); ?>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check me-2" aria-hidden="true"></i>
                                CSS3 & <?php echo t('responsive_design', 'Design Responsivo'); ?>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check me-2" aria-hidden="true"></i>
                                JavaScript <?php echo t('basics', 'Básico'); ?>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check me-2" aria-hidden="true"></i>
                                <?php echo t('first_projects', 'Primeiros Projetos'); ?>
                            </li>
                        </ul>
                        <a href="exercises_index.php?level=beginner" class="btn">
                            <?php echo t('start_path', 'Iniciar Trilha'); ?>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card path-card path-card-advanced h-100">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-rocket me-2" aria-hidden="true"></i>
                            <?php echo t('advanced_path', 'Trilha Avançada'); ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <?php echo t('advanced_path_desc', 'Para desenvolvedores que querem se aprofundar em tecnologias modernas.'); ?>
                        </p>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check me-2" aria-hidden="true"></i>
                                React & Vue.js
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check me-2" aria-hidden="true"></i>
                                Node.js & Express
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check me-2" aria-hidden="true"></i>
                                <?php echo t('databases', 'Bancos de Dados'); ?>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check me-2" aria-hidden="true"></i>
                                <?php echo t('deployment', 'Deploy e DevOps'); ?>
                            </li>
                        </ul>
                        <a href="exercises_index.php?level=advanced" class="btn">
                            <?php echo t('start_path', 'Iniciar Trilha'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


    
        
      <section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold text-gradient">
                    <?php echo t('testimonials', 'O que nossos alunos dizem'); ?>
                </h2>
                <p class="lead">
                    <?php echo t('testimonials_desc', 'Histórias de sucesso de quem já passou por aqui'); ?>
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Depoimento 1 -->
            <div class="col-lg-4 d-flex align-items-stretch">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4 d-flex flex-column">
                        <div class="mb-3">
                            <img src="https://i.pravatar.cc/100?u=1" 
                                 alt="<?php echo t('student_photo', 'Foto do estudante' ); ?>" 
                                 class="rounded-circle border border-2 border-primary p-1">
                        </div>
                        <blockquote class="blockquote flex-grow-1">
                            <p class="mb-3">
                                "<?php echo t('testimonial_1', 'A plataforma é incrível! Os exercícios práticos me ajudaram a consolidar o conhecimento de uma forma que nenhum curso online tinha conseguido antes.'); ?>"
                            </p>
                        </blockquote>
                        <footer class="blockquote-footer mt-auto">
                            <strong class="d-block">Joana Silva</strong>
                            <cite title="<?php echo t('job_title', 'Cargo'); ?>">
                                <?php echo t('frontend_developer', 'Desenvolvedora Front-end'); ?>
                            </cite>
                        </footer>
                        <div class="mt-3 text-warning">
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <i class="fas fa-star" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Depoimento 2 -->
            <div class="col-lg-4 d-flex align-items-stretch">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4 d-flex flex-column">
                        <div class="mb-3">
                            <img src="https://i.pravatar.cc/100?u=2" 
                                 alt="<?php echo t('student_photo', 'Foto do estudante' ); ?>" 
                                 class="rounded-circle border border-2 border-primary p-1">
                        </div>
                        <blockquote class="blockquote flex-grow-1">
                            <p class="mb-3">
                                "<?php echo t('testimonial_2', 'O feedback instantâneo é um divisor de águas. Consegui corrigir meus erros e evoluir muito mais rápido. Recomendo a todos!'); ?>"
                            </p>
                        </blockquote>
                        <footer class="blockquote-footer mt-auto">
                            <strong class="d-block">Carlos Souza</strong>
                            <cite title="<?php echo t('job_title', 'Cargo'); ?>">
                                <?php echo t('fullstack_developer', 'Estudante de Análise de Sistemas'); ?>
                            </cite>
                        </footer>
                        <div class="mt-3 text-warning">
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <i class="fas fa-star-half-alt" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Depoimento 3 -->
            <div class="col-lg-4 d-flex align-items-stretch">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4 d-flex flex-column">
                        <div class="mb-3">
                            <img src="https://i.pravatar.cc/100?u=3" 
                                 alt="<?php echo t('student_photo', 'Foto do estudante' ); ?>" 
                                 class="rounded-circle border border-2 border-primary p-1">
                        </div>
                        <blockquote class="blockquote flex-grow-1">
                            <p class="mb-3">
                                "<?php echo t('testimonial_3', 'A comunidade é muito ativa e prestativa. Sempre que tive dúvidas, encontrei ajuda no fórum. Isso faz toda a diferença no aprendizado.'); ?>"
                            </p>
                        </blockquote>
                        <footer class="blockquote-footer mt-auto">
                            <strong class="d-block">Mariana Lima</strong>
                            <cite title="<?php echo t('job_title', 'Cargo'); ?>">
                                <?php echo t('ui_designer', 'Designer UI/UX'); ?>
                            </cite>
                        </footer>
                        <div class="mt-3 text-warning">
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <i class="far fa-star" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



<!-- Call to Action -->
<section class="py-5 bg-primary text-white">
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