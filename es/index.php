<?php
// Incluir configuraciones
require_once 'config.php';

// Definir título de la página
$title = t('home');

// Obtener estadísticas
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
                        <?php echo t('Viaje del desarrollador'); ?>
                    </h1>
                    <p class="lead mb-4 fade-in-up" style="animation-delay: 0.2s;">
                        <?php echo t('plataforma interactiva'); ?>
                    </p>
                    
                    <div class="d-flex flex-column flex-sm-row gap-3 mb-4 fade-in-up" style="animation-delay: 0.4s;">
                        <?php if (isLoggedIn()): ?>
                            <a href="exercises_index.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-play me-2" aria-hidden="true"></i>
                                <?php echo t('continue_learning', 'Continuar Aprendiendo'); ?>
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
                    
                    <!-- Estadísticas rápidas -->
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
                        
                        <!-- Elementos flotantes -->
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

<!-- Sección de Recursos -->
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
                        <h4 class="card-title"><?php echo t('instant_feedback', 'Retroalimentación Instantánea'); ?></h4>
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
                            <?php echo t('progress_tracking_desc', 'Monitorea tu progreso con estadísticas detalladas y alcanza tus objetivos.'); ?>
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
                            <?php echo t('responsive_design_desc', 'Accede a la plataforma desde cualquier dispositivo, en cualquier momento y lugar.'); ?>
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

<!-- Sección de Rutas de Aprendizaje -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold text-gradient">
                    <?php echo t('learning_paths', 'Rutas de Aprendizaje'); ?>
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
                            <?php echo t('beginner_path', 'Ruta Principiante'); ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <?php echo t('beginner_path_desc', 'Perfecta para principiantes. Aprende HTML, CSS y JavaScript desde cero.'); ?>
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
                            <?php echo t('advanced_path', 'Ruta Avanzada'); ?>
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
                                <?php echo t('deployment', 'Deploy y DevOps'); ?>
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

<!-- Sección de Testimonios -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold text-gradient">
                    <?php echo t('what_students_say', 'Lo que dicen nuestros estudiantes'); ?>
                </h2>
                <p class="lead text-muted">
                    <?php echo t('testimonials_desc', 'Conoce la experiencia de quienes ya están en el camino'); ?>
                </p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Testimonio 1 -->
            <div class="col-lg-4 d-flex align-items-stretch">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4 d-flex flex-column">
                        <div class="mb-3">
                            <img src="https://i.pravatar.cc/100?u=1" 
                                 alt="<?php echo t('student_photo', 'Foto del estudiante'); ?>" 
                                 class="rounded-circle border border-2 border-primary p-1">
                        </div>
                        <blockquote class="blockquote flex-grow-1">
                            <p class="mb-3">
                                "<?php echo t('testimonial_1', '¡La plataforma es increíble! Los ejercicios prácticos me ayudaron a consolidar mis conocimientos de una forma que ningún otro curso online había logrado antes.'); ?>"
                            </p>
                        </blockquote>
                        <footer class="blockquote-footer mt-auto">
                            <strong class="d-block">Joana Silva</strong>
                            <cite title="<?php echo t('job_title', 'Cargo'); ?>">
                                <?php echo t('frontend_developer', 'Desarrolladora Front-end'); ?>
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
            
            <!-- Testimonio 2 -->
            <div class="col-lg-4 d-flex align-items-stretch">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4 d-flex flex-column">
                        <div class="mb-3">
                            <img src="https://i.pravatar.cc/100?u=2" 
                                 alt="<?php echo t('student_photo', 'Foto del estudiante'); ?>" 
                                 class="rounded-circle border border-2 border-primary p-1">
                        </div>
                        <blockquote class="blockquote flex-grow-1">
                            <p class="mb-3">
                                "<?php echo t('testimonial_2', 'La retroalimentación instantánea es un punto de inflexión. Pude corregir mis errores y evolucionar mucho más rápido. ¡Se lo recomiendo a todos!'); ?>"
                            </p>
                        </blockquote>
                        <footer class="blockquote-footer mt-auto">
                            <strong class="d-block">Carlos Souza</strong>
                            <cite title="<?php echo t('job_title', 'Cargo'); ?>">
                                <?php echo t('fullstack_developer', 'Estudiante de Análisis de Sistemas'); ?>
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
            
            <!-- Testimonio 3 -->
            <div class="col-lg-4 d-flex align-items-stretch">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4 d-flex flex-column">
                        <div class="mb-3">
                            <img src="https://i.pravatar.cc/100?u=3" 
                                 alt="<?php echo t('student_photo', 'Foto del estudiante'); ?>" 
                                 class="rounded-circle border border-2 border-primary p-1">
                        </div>
                        <blockquote class="blockquote flex-grow-1">
                            <p class="mb-3">
                                "<?php echo t('testimonial_3', 'La comunidad es muy activa y servicial. Siempre que he tenido dudas, he encontrado ayuda en el foro. Eso marca la diferencia en el aprendizaje.'); ?>"
                            </p>
                        </blockquote>
                        <footer class="blockquote-footer mt-auto">
                            <strong class="d-block">Mariana Lima</strong>
                            <cite title="<?php echo t('job_title', 'Cargo'); ?>">
                                <?php echo t('ui_designer', 'Diseñadora UI/UX'); ?>
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
</section>

<!-- Call to Action -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-5 fw-bold mb-4">
                    <?php echo t('ready_to_start', '¿Listo para comenzar tu viaje?'); ?>
                </h2>
                <p class="lead mb-4">
                    <?php echo t('join_thousands', 'Únete a miles de desarrolladores que ya han transformado sus carreras con nosotros.'); ?>
                </p>
                
                <?php if (!isLoggedIn()): ?>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <a href="register.php" class="btn btn-light btn-lg">
                            <i class="fas fa-user-plus me-2" aria-hidden="true"></i>
                            <?php echo t('create_free_account', 'Crear Cuenta Gratuita'); ?>
                        </a>
                        <a href="exercises_index.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-eye me-2" aria-hidden="true"></i>
                            <?php echo t('explore_content', 'Explorar Contenido'); ?>
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

<script>
// Animación de escritura para el código
document.addEventListener('DOMContentLoaded', function() {
    const codeElement = document.getElementById('typingCode');
    const codeText = `<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Sitio</title>
</head>
<body>
    <h1>¡Hola, Mundo!</h1>
    <p>Bienvenido a tu Viaje de Desarrollador</p>
</body>
</html>`;
    
    let i = 0;
    function typeWriter() {
        if (i < codeText.length) {
            codeElement.textContent += codeText.charAt(i);
            i++;
            setTimeout(typeWriter, 50);
        } else {
            // Reiniciar después de 3 segundos
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
/* Estilos específicos de la página de inicio */
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

/* Responsividad */
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