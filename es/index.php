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
                        <?php echo t('Jornada_desarrollador'); ?>
                    </h1>
                    <p class="lead mb-4 fade-in-up" style="animation-delay: 0.2s;">
                        <?php echo t('plataforma_interactiva'); ?>
                    </p>
                    
                    <div class="d-flex flex-column flex-sm-row gap-3 mb-4 fade-in-up" style="animation-delay: 0.4s;">
                        <?php if (isLoggedIn()): ?>
                            <a href="exercises_index.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-play me-2" aria-hidden="true"></i>
                                <?php echo t('continue_learning', 'Seguir Aprendiendo'); ?>
                            </a>
                            <a href="progress.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-chart-line me-2" aria-hidden="true"></i>
                                <?php echo t('view_progress', 'Ver Progreso'); ?>
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
                                <small class="text-light"><?php echo t('students', 'Estudiantes'); ?></small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-item">
                                <h3 class="h4 fw-bold text-info"><?php echo $stats['total_exercises']; ?></h3>
                                <small class="text-light"><?php echo t('exercises','Ejercicios'); ?></small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-item">
                                <h3 class="h4 fw-bold text-success"><?php echo $stats['total_tutorials']; ?></h3>
                                <small class="text-light"><?php echo t('tutorials','Tutoriales'); ?></small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-item">
                                <h3 class="h4 fw-bold text-danger"><?php echo number_format($stats['total_forum_posts']); ?></h3>
                                <small class="text-light"><?php echo t('forum_posts', 'Publicaciones en el Foro'); ?></small>
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
                    <?php echo t('why_choose_us', '¿Por qué elegirnos?'); ?>
                </h2>
                <p class="lead text-muted">
                    <?php echo t('platform_benefits', 'Descubre los beneficios de nuestra plataforma de aprendizaje'); ?>
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
                        <h4 class="card-title"><?php echo t('practical_exercises', 'Ejercicios Prácticos'); ?></h4>
                        <p class="card-text text-muted">
                            <?php echo t('practical_exercises_desc', 'Aprende haciendo con ejercicios interactivos que simulan situaciones reales de desarrollo.'); ?>
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
                        <h4 class="card-title"><?php echo t('instant_feedback', 'Feedback Instantâneo'); ?></h4>
                        <p class="card-text text-muted">
                            <?php echo t('instant_feedback_desc', 'Recibe retroalimentación inmediata sobre tu código y aprende de tus errores en tiempo real.'); ?>
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
                        <h4 class="card-title"><?php echo t('active_community', 'Comunidad Activa'); ?></h4>
                        <p class="card-text text-muted">
                            <?php echo t('active_community_desc', 'Conéctate con otros desarrolladores, resuelve dudas y comparte conocimiento.'); ?>
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
                        <h4 class="card-title"><?php echo t('progress_tracking', 'Seguimiento de Progreso'); ?></h4>
                        <p class="card-text text-muted">
                            <?php echo t('progress_tracking_desc', 'Monitorea tu progreso con estadísticas detalladas y conquista tus objetivos.'); ?>
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
                        <h4 class="card-title"><?php echo t('responsive_design', 'Diseño Responsivo'); ?></h4>
                        <p class="card-text text-muted">
                            <?php echo t('responsive_design_desc', 'Accede a la plataforma desde cualquier dispositivo, a cualquier hora y en cualquier lugar.'); ?>
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
                        <h4 class="card-title"><?php echo t('accessibility', 'Accesibilidad'); ?></h4>
                        <p class="card-text text-muted">
                            <?php echo t('accessibility_desc', 'Plataforma totalmente accesible, incluyendo soporte para personas con daltonismo.'); ?>
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
                    <?php echo t('learning_paths', 'Caminos de Aprendizaje'); ?>
                </h2>
                <p class="lead text-muted">
                    <?php echo t('learning_paths_desc', 'Elige tu camino y domina las tecnologías web'); ?>
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-gradient h-100">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-seedling me-2" aria-hidden="true"></i>
                            <?php echo t('beginner_path', 'Sendero Principiante'); ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <?php echo t('beginner_path_desc', 'Perfecta para quienes están empezando. Aprende HTML, CSS y JavaScript desde cero.'); ?>
                        </p>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                                HTML5 <?php echo t('fundamentals', 'Fundamentos'); ?>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                                CSS3 & <?php echo t('responsive_design', 'Diseño Responsivo'); ?>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                                JavaScript <?php echo t('basics', 'Básico'); ?>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                                <?php echo t('first_projects', 'Primeros Proyectos'); ?>
                            </li>
                        </ul>
                        <a href="exercises_index.php?level=beginner" class="btn btn-primary">
                            <?php echo t('start_path', 'Iniciar Ruta'); ?>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card border-gradient h-100">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-rocket me-2" aria-hidden="true"></i>
                            <?php echo t('advanced_path', 'Sendero Avanzado'); ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <?php echo t('advanced_path_desc', 'Para desarrolladores que quieren profundizar en tecnologías modernas.'); ?>
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
                                <?php echo t('databases', 'Bases de Datos'); ?>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                                <?php echo t('deployment', 'Deploy e DevOps'); ?>
                            </li>
                        </ul>
                        <a href="exercises_index.php?level=advanced" class="btn btn-warning">
                            <?php echo t('start_path', 'Iniciar Ruta'); ?>
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
                    <?php echo t('testimonials', 'Testimonios'); ?>
                </h2>
                <p class="lead text-muted">
                    <?php echo t('testimonials_desc', 'Lo que dicen nuestros estudiantes'); ?>
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <img src="assets/images/testimonial1.jpg" alt="María García" class="rounded-circle" width="80" height="80">
                        </div>
                        <blockquote class="blockquote">
                            <p class="mb-3">"<?php echo t('testimonial1', 'Esta plataforma cambió mi carrera. Los ejercicios prácticos me ayudaron a conseguir mi primer trabajo como desarrolladora.'); ?>"</p>
                        </blockquote>
                        <footer class="blockquote-footer">
                            <strong>María García</strong>
                            <cite title="Source Title"><?php echo t('frontend_developer', 'Desarrolladora Frontend'); ?></cite>
                        </footer>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <img src="assets/images/testimonial2.jpg" alt="Carlos Silva" class="rounded-circle" width="80" height="80">
                        </div>
                        <blockquote class="blockquote">
                            <p class="mb-3">"<?php echo t('testimonial2', 'El feedback instantáneo y la comunidad activa hacen que aprender sea divertido y efectivo.'); ?>"</p>
                        </blockquote>
                        <footer class="blockquote-footer">
                            <strong>Carlos Silva</strong>
                            <cite title="Source Title"><?php echo t('fullstack_developer', 'Desarrollador Full Stack'); ?></cite>
                        </footer>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <img src="assets/images/testimonial3.jpg" alt="Ana Rodríguez" class="rounded-circle" width="80" height="80">
                        </div>
                        <blockquote class="blockquote">
                            <p class="mb-3">"<?php echo t('testimonial3', 'Desde cero hasta conseguir trabajo en 6 meses. Los proyectos reales me dieron la confianza que necesitaba.'); ?>"</p>
                        </blockquote>
                        <footer class="blockquote-footer">
                            <strong>Ana Rodríguez</strong>
                            <cite title="Source Title"><?php echo t('backend_developer', 'Desarrolladora Backend'); ?></cite>
                        </footer>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="display-5 fw-bold mb-4">
                    <?php echo t('ready_to_start', '¿Listo para comenzar tu viaje?'); ?>
                </h2>
                <p class="lead mb-4">
                    <?php echo t('join_thousands', 'Únete a miles de desarrolladores que ya están transformando sus carreras'); ?>
                </p>
                
                <?php if (!isLoggedIn()): ?>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <a href="register.php" class="btn btn-light btn-lg">
                            <i class="fas fa-rocket me-2" aria-hidden="true"></i>
                            <?php echo t('start_free', 'Comenzar Gratis'); ?>
                        </a>
                        <a href="exercises_index.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-eye me-2" aria-hidden="true"></i>
                            <?php echo t('view_exercises', 'Ver Ejercicios'); ?>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="exercises_index.php" class="btn btn-light btn-lg">
                        <i class="fas fa-play me-2" aria-hidden="true"></i>
                        <?php echo t('continue_learning', 'Continuar Aprendiendo'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
/* Variáveis CSS */
:root {
    --primary-color: #007bff;
    --secondary-color: #6c757d;
    --success-color: #28a745;
    --info-color: #17a2b8;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --light-color: #f8f9fa;
    --dark-color: #343a40;
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--info-color) 100%);
    min-height: 100vh;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
}

.min-vh-75 {
    min-height: 75vh;
}

/* Animações */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
    opacity: 0;
}

/* Gradiente de texto */
.text-gradient {
    background: linear-gradient(135deg, var(--primary-color), var(--info-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Bordas com gradiente */
.border-gradient {
    border: 2px solid;
    border-image: linear-gradient(135deg, var(--primary-color), var(--info-color)) 1;
}

/* Estatísticas */
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

<script>
// Animação de digitação para o código
document.addEventListener('DOMContentLoaded', function() {
    const codeElement = document.getElementById('typingCode');
    if (codeElement) {
        const codeText = `<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Primera Página</title>
</head>
<body>
    <h1>¡Hola, Mundo!</h1>
    <p>¡Bienvenido al desarrollo web!</p>
</body>
</html>`;
        
        let i = 0;
        const speed = 50; // velocidade de digitação em ms
        
        function typeWriter() {
            if (i < codeText.length) {
                codeElement.innerHTML += codeText.charAt(i);
                i++;
                setTimeout(typeWriter, speed);
            } else {
                // Reiniciar a animação após 3 segundos
                setTimeout(() => {
                    codeElement.innerHTML = '';
                    i = 0;
                    typeWriter();
                }, 3000);
            }
        }
        
        typeWriter();
    }
});

// Animação suave para elementos que entram na viewport
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in-up');
        }
    });
}, observerOptions);

// Observar todos os elementos com a classe 'animate-on-scroll'
document.querySelectorAll('.card, .feature-icon').forEach(el => {
    observer.observe(el);
});
</script>

<?php include 'footer.php'; ?>
