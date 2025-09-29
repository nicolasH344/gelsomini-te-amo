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
                    <p class="lead mb-4 fade-in-up" style="animation-delay: 0.2s;">
                        <?php echo t('interactive_platform'); ?>
                    </p>
                    
                    <div class="d-flex flex-column flex-sm-row gap-3 mb-4 fade-in-up" style="animation-delay: 0.4s;">
                        <?php if (isLoggedIn()): ?>
                            <a href="exercises_index.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-play me-2" aria-hidden="true"></i>
                                <?php echo t('continue_learning', 'Keep Learning'); ?>
                            </a>
                            <a href="progress.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-chart-line me-2" aria-hidden="true"></i>
                                <?php echo t('view_progress', 'View Progress'); ?>
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
                    <div class="row text-center fade-in-up" style="animation-delay: 0.6s;">
                        <div class="col-6 col-md-3">
                            <div class="stat-item">
                                <h3 class="h4 fw-bold text-warning"><?php echo number_format($stats['total_users']); ?></h3>
                                <small class="text-light"><?php echo t('students', 'Students'); ?></small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-item">
                                <h3 class="h4 fw-bold text-info"><?php echo $stats['total_exercises']; ?></h3>
                                <small class="text-light"><?php echo t('exercises','Exercises'); ?></small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-item">
                                <h3 class="h4 fw-bold text-success"><?php echo $stats['total_tutorials']; ?></h3>
                                <small class="text-light"><?php echo t('tutorials','Tutorials'); ?></small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-item">
                                <h3 class="h4 fw-bold text-danger"><?php echo number_format($stats['total_forum_posts']); ?></h3>
                                <small class="text-light"><?php echo t('forum_posts', 'Forum posts'); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="hero-image text-center fade-in-up" style="animation-delay: 0.8s;">
                    <div class="position-relative">
                        <!-- Código animado -->
                        <div class="code-animation bg-dark rounded-3 p-4 shadow-lg">
                            <div class="d-flex align-items-center mb-3">
                                <div class="d-flex gap-2">
                                    <div class="bg-danger rounded-circle" style="width: 12px; height: 12px;"></div>
                                    <div class="bg-warning rounded-circle" style="width: 12px; height: 12px;"></div>
                                    <div class="bg-success rounded-circle" style="width: 12px; height: 12px;"></div>
                                </div>
                                <small class="text-muted ms-3">index.html</small>
                            </div>
                            <pre class="text-start text-light mb-0" style="font-size: 0.9rem;"><code id="typingCode"></code></pre>
                        </div>
                        
                        <!-- Elementos flutuantes -->
                        <div class="floating-elements">
                            <div class="floating-icon" style="top: 10%; left: 10%; animation-delay: 0s;">
                                <i class="fab fa-html5 text-warning fa-2x"></i>
                            </div>
                            <div class="floating-icon" style="top: 20%; right: 15%; animation-delay: 1s;">
                                <i class="fab fa-css3-alt text-info fa-2x"></i>
                            </div>
                            <div class="floating-icon" style="bottom: 30%; left: 5%; animation-delay: 2s;">
                                <i class="fab fa-js-square text-warning fa-2x"></i>
                            </div>
                            <div class="floating-icon" style="bottom: 10%; right: 10%; animation-delay: 3s;">
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
                    <?php echo t('why_choose_us', 'Why choose us?'); ?>
                </h2>
                <p class="lead text-muted">
                    <?php echo t('platform_benefits', 'Discover the benefits of our learning platform'); ?>
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-code fa-3x text-primary"></i>
                        </div>
                        <h4 class="card-title"><?php echo t('practical_exercises', 'Practical Exercises'); ?></h4>
                        <p class="card-text text-muted">
                            <?php echo t('practical_exercises_desc', 'Learn by doing with interactive exercises that simulate real-life development situations.'); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-bolt fa-3x text-warning"></i>
                        </div>
                        <h4 class="card-title"><?php echo t('instant_feedback', 'Instant Feedback'); ?></h4>
                        <p class="card-text text-muted">
                            <?php echo t('instant_feedback_desc', 'Get immediate feedback on your code and learn from your mistakes in real time.'); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-users fa-3x text-success"></i>
                        </div>
                        <h4 class="card-title"><?php echo t('active_community', 'Active Community'); ?></h4>
                        <p class="card-text text-muted">
                            <?php echo t('active_community_desc', 'Connect with other developers, ask questions, and share knowledge.'); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-chart-line fa-3x text-info"></i>
                        </div>
                        <h4 class="card-title"><?php echo t('progress_tracking', 'Progress Monitoring'); ?></h4>
                        <p class="card-text text-muted">
                            <?php echo t('progress_tracking_desc', 'Track your progress with detailed statistics and achieve your goals.'); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-mobile-alt fa-3x text-danger"></i>
                        </div>
                        <h4 class="card-title"><?php echo t('responsive_design', 'Responsive Design'); ?></h4>
                        <p class="card-text text-muted">
                            <?php echo t('responsive_design_desc', 'Access the platform from any device, anytime, anywhere.'); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-universal-access fa-3x text-secondary"></i>
                        </div>
                        <h4 class="card-title"><?php echo t('accessibility', 'Accessibility'); ?></h4>
                        <p class="card-text text-muted">
                            <?php echo t('accessibility_desc', 'Fully accessible platform, including support for people with color blindness.'); ?>
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
                    <?php echo t('learning_paths', 'Learning Paths'); ?>
                </h2>
                <p class="lead text-muted">
                    <?php echo t('learning_paths_desc', 'Choose your journey and master web technologies'); ?>
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-gradient h-100">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-seedling me-2" aria-hidden="true"></i>
                            <?php echo t('beginner_path', 'Beginner Trail'); ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <?php echo t('beginner_path_desc', 'Perfect for beginners. Learn HTML, CSS, and JavaScript from scratch.'); ?>
                        </p>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                                HTML5 <?php echo t('fundamentals', 'Fundamentals'); ?>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                                CSS3 & <?php echo t('responsive_design', 'Responsive Design'); ?>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                                JavaScript <?php echo t('basics', 'Basic'); ?>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                                <?php echo t('first_projects', 'First Projects'); ?>
                            </li>
                        </ul>
                        <a href="exercises_index.php?level=beginner" class="btn btn-primary">
                            <?php echo t('start_path', 'Start Trail'); ?>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card border-gradient h-100">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-rocket me-2" aria-hidden="true"></i>
                            <?php echo t('advanced_path', 'Advanced Trail'); ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <?php echo t('advanced_path_desc', 'For developers who want to delve deeper into modern technologies.'); ?>
                        </p>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                                React & Vue.js
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                                Node.js & Express
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                                <?php echo t('databases', 'Databases'); ?>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                                <?php echo t('deployment', 'Deploy and DevOps'); ?>
                            </li>
                        </ul>
                        <a href="exercises_index.php?level=advanced" class="btn btn-warning">
                            <?php echo t('start_path', 'Start Trail'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Seção de Depoimentos -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold text-gradient">
                    <?php echo t('testimonials', 'Testimonials'); ?>
                </h2>
                <p class="lead text-muted">
                    <?php echo t('testimonials_desc', 'See what our students say about the platform'); ?>
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <img src="https://via.placeholder.com/80x80/6f42c1/ffffff?text=M" 
                                 alt="<?php echo t('student_photo', 'Photo of the student'); ?>" 
                                 class="rounded-circle">
                        </div>
                        <blockquote class="blockquote">
                            <p class="mb-3">
                                "<?php echo t('testimonial_1', 'The platform helped me get my first job as a developer. The practical exercises made all the difference!'); ?>"
                            </p>
                        </blockquote>
                        <footer class="blockquote-footer">
                            <strong>Maria Silva</strong>
                            <cite title="<?php echo t('job_title', 'Position'); ?>">
                                <?php echo t('frontend_developer', 'Front-end developer'); ?>
                            </cite>
                        </footer>
                        <div class="mt-2">
                            <i class="fas fa-star text-warning" aria-hidden="true"></i>
                            <i class="fas fa-star text-warning" aria-hidden="true"></i>
                            <i class="fas fa-star text-warning" aria-hidden="true"></i>
                            <i class="fas fa-star text-warning" aria-hidden="true"></i>
                            <i class="fas fa-star text-warning" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <img src="https://via.placeholder.com/80x80/e83e8c/ffffff?text=J" 
                                 alt="<?php echo t('student_photo', 'Student photo'); ?>" 
                                 class="rounded-circle">
                        </div>
                        <blockquote class="blockquote">
                            <p class="mb-3">
                                "<?php echo t('testimonial_2', 'Excellent platform! The instant feedback helped me learn much faster. I recommend it to everyone!'); ?>"
                            </p>
                        </blockquote>
                        <footer class="blockquote-footer">
                            <strong>João Santos</strong>
                            <cite title="<?php echo t('job_title', 'Position'); ?>">
                                <?php echo t('fullstack_developer', 'Full-Stack Developer'); ?>
                            </cite>
                        </footer>
                        <div class="mt-2">
                            <i class="fas fa-star text-warning" aria-hidden="true"></i>
                            <i class="fas fa-star text-warning" aria-hidden="true"></i>
                            <i class="fas fa-star text-warning" aria-hidden="true"></i>
                            <i class="fas fa-star text-warning" aria-hidden="true"></i>
                            <i class="fas fa-star text-warning" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <img src="https://via.placeholder.com/80x80/fd7e14/ffffff?text=A" 
                                 alt="<?php echo t('student_photo', 'Student photo'); ?>" 
                                 class="rounded-circle">
                        </div>
                        <blockquote class="blockquote">
                            <p class="mb-3">
                                "<?php echo t('testimonial_3', 'The community is amazing! I always find help on the forum and learn from other developers.'); ?>"
                            </p>
                        </blockquote>
                        <footer class="blockquote-footer">
                            <strong>Ana Costa</strong>
                            <cite title="<?php echo t('job_title', 'Position'); ?>">
                                <?php echo t('ui_designer', 'UI/UX Designer'); ?>
                            </cite>
                        </footer>
                        <div class="mt-2">
                            <i class="fas fa-star text-warning" aria-hidden="true"></i>
                            <i class="fas fa-star text-warning" aria-hidden="true"></i>
                            <i class="fas fa-star text-warning" aria-hidden="true"></i>
                            <i class="fas fa-star text-warning" aria-hidden="true"></i>
                            <i class="fas fa-star text-warning" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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
                                "<?php echo t('testimonial_1', 'The platform is incredible! The practical exercises helped me consolidate my knowledge in a way that no other online course had managed to do before.'); ?>"
                            </p>
                        </blockquote>
                        <footer class="blockquote-footer mt-auto">
                            <strong class="d-block">Joana Silva</strong>
                            <cite title="<?php echo t('job_title', 'Cargo'); ?>">
                                <?php echo t('frontend_developer', 'Front-end Developer'); ?>
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
                                "<?php echo t('testimonial_2', 'Instant feedback is a game changer. I was able to correct my mistakes and improve much faster. I recommend it to everyone!'); ?>"
                            </p>
                        </blockquote>
                        <footer class="blockquote-footer mt-auto">
                            <strong class="d-block">Carlos Souza</strong>
                            <cite title="<?php echo t('job_title', 'Cargo'); ?>">
                                <?php echo t('fullstack_developer', 'Systems Analysis Student'); ?>
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
                                "<?php echo t('testimonial_3', 'The community is very active and helpful. Whenever I had questions, I found help on the forum. That makes all the difference in learning.'); ?>"
                            </p>
                        </blockquote>
                        <footer class="blockquote-footer mt-auto">
                            <strong class="d-block">Mariana Lima</strong>
                            <cite title="<?php echo t('job_title', 'Cargo'); ?>">
                                <?php echo t('ui_designer', 'UI/UX Designer'); ?>
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
                    <?php echo t('ready_to_start', 'Are you ready to start your journey?'); ?>
                </h2>
                <p class="lead mb-4">
                    <?php echo t('join_thousands', 'Join thousands of developers who have already transformed their careers with us.'); ?>
                </p>
                
                <?php if (!isLoggedIn()): ?>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <a href="register.php" class="btn btn-light btn-lg">
                            <i class="fas fa-user-plus me-2" aria-hidden="true"></i>
                            <?php echo t('create_free_account', 'Create Free Account'); ?>
                        </a>
                        <a href="exercises_index.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-eye me-2" aria-hidden="true"></i>
                            <?php echo t('explore_content', 'Explore Content'); ?>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="exercises_index.php" class="btn btn-light btn-lg">
                        <i class="fas fa-play me-2" aria-hidden="true"></i>
                        <?php echo t('continue_learning', 'keep Learning'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
// Animação de digitação para o código
document.addEventListener('DOMContentLoaded', function() {
    const codeElement = document.getElementById('typingCode');
    const codeText = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Site</title>
</head>
<body>
    <h1>Hi, World!</h1>
    <p>Welcome to your Developer Journey</p>
</body>
</html>`;
    
    let i = 0;
    function typeWriter() {
        if (i < codeText.length) {
            codeElement.textContent += codeText.charAt(i);
            i++;
            setTimeout(typeWriter, 50);
        } else {
            // Reiniciar após 3 segundos
            setTimeout(() => {
                codeElement.textContent = '';
                i = 0;
                typeWriter();
            }, 3000);
        }
    }
    
    typeWriter();
});
</script>

<style>
/* Estilos específicos da página inicial */
.hero-section {
    min-height: 100vh;
    display: flex;
    align-items: center;
}

.min-vh-75 {
    min-height: 75vh;
}

.stat-item {
    padding: 1rem;
    border-radius: 0.5rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    margin-bottom: 1rem;
}

.code-animation {
    max-width: 400px;
    margin: 0 auto;
    font-family: 'Courier New', monospace;
}

.floating-elements {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
}

.floating-icon {
    position: absolute;
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}

.feature-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 50%;
    color: white;
}

.card:hover .feature-icon {
    transform: scale(1.1);
    transition: transform 0.3s ease;
}

/* Responsividade */
@media (max-width: 768px) {
    .hero-section {
        min-height: auto;
        padding: 3rem 0;
    }
    
    .display-4 {
        font-size: 2.5rem;
    }
    
    .floating-elements {
        display: none;
    }
    
    .code-animation {
        font-size: 0.8rem;
    }
}
</style>

<?php include 'footer.php'; ?>

