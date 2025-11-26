<?php 
// Incluir configuraciones
require_once 'config.php';

// Variable para almacenar el mensaje de error localmente
$error = null;

// Procesar registro si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = processRegister($_POST);
    
    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
        header('Location: login.php');
        exit;
    } else {
        // Usar variable local para mostrar error en la página
        $error = $result['message'];
    }
}

// Si ya está logueado, redirigir
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Definir título de la página
$title = 'Crear Cuenta';

include 'header.php'; 
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            
            <?php // Mostrar mensaje de error local si existe ?>
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                    <?php echo sanitize($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h1 class="h4 mb-0">
                        <i class="fas fa-user-plus" aria-hidden="true"></i> 
                        Crear Nueva Cuenta
                    </h1>
                </div>
                <div class="card-body">
                    <form method="POST" action="register.php" novalidate>
                        <fieldset>
                            <legend class="visually-hidden">Información Personal</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-0">
                                        <label for="first_name" class="form-label required">Nombre</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="first_name" 
                                               name="first_name" 
                                               required
                                               autocomplete="given-name"
                                               aria-describedby="first_name-help"
                                               value="<?php echo isset($_POST['first_name']) ? sanitize($_POST['first_name']) : ''; ?>">
                                        <div id="first_name-help" class="form-text">
                                            Ingresa tu primer nombre
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-0">
                                        <label for="last_name" class="form-label required">Apellido</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="last_name" 
                                               name="last_name" 
                                               required
                                               autocomplete="family-name"
                                               aria-describedby="last_name-help"
                                               value="<?php echo isset($_POST['last_name']) ? sanitize($_POST['last_name']) : ''; ?>">
                                        <div id="last_name-help" class="form-text">
                                            Ingresa tu apellido
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        
                        <fieldset>
                            <legend class="visually-hidden">Información de Acceso</legend>
                            <div class="mb-0">
                                <label for="username" class="form-label required">Nombre de Usuario</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="username" 
                                       name="username" 
                                       required
                                       autocomplete="username"
                                       aria-describedby="username-help"
                                       pattern="[a-zA-Z0-9_]{3,}"
                                       value="<?php echo isset($_POST['username']) ? sanitize($_POST['username']) : ''; ?>">
                                <div id="username-help" class="form-text">
                                    Solo letras, números y guión bajo. Mínimo 3 caracteres.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label required">Email</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       required
                                       autocomplete="email"
                                       aria-describedby="email-help"
                                       value="<?php echo isset($_POST['email']) ? sanitize($_POST['email']) : ''; ?>">
                                <div id="email-help" class="form-text mb-0">
                                    Ingresa una dirección de email válida
                                </div>
                            </div>
                        </fieldset>
                        
                        <fieldset>
                            <legend class="visually-hidden">Definir Contraseña</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-0">
                                        <label for="password" class="form-label required">Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password" 
                                                   name="password" 
                                                   required
                                                   autocomplete="new-password"
                                                   aria-describedby="password-help"
                                                   minlength="6">
                                            <button class="btn btn-outline-secondary" 
                                                    type="button" 
                                                    id="togglePassword"
                                                    aria-label="Mostrar/ocultar contraseña">
                                                <i class="fas fa-eye" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        <div id="password-help" class="form-text">
                                            Mínimo 6 caracteres
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-0">
                                        <label for="confirm_password" class="form-label required">Confirmar Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="confirm_password" 
                                                   name="confirm_password" 
                                                   required
                                                   autocomplete="new-password"
                                                   aria-describedby="confirm_password-help">
                                            <button class="btn btn-outline-secondary" 
                                                    type="button" 
                                                    id="toggleConfirmPassword"
                                                    aria-label="Mostrar/ocultar confirmación de contraseña">
                                                <i class="fas fa-eye" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        <div id="confirm_password-help" class="form-text">
                                            Ingresa la misma contraseña nuevamente
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        
                        <div class="mb-0 form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="terms" 
                                   name="terms"
                                   required
                                   aria-describedby="terms-help"
                                   <?php echo isset($_POST['terms']) ? 'checked' : ''; ?>>
                            <label class="form-check-label required mb-0" for="terms">
                                Acepto los términos de uso
                            </label>
                            <div id="terms-help" class="form-text">
                                Es necesario aceptar los términos para crear una cuenta
                            </div>
                        </div>
                        
                        <div class="mb-0 form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="lgpd_consent" 
                                   name="lgpd_consent"
                                   required
                                   aria-describedby="lgpd-help"
                                   <?php echo isset($_POST['lgpd_consent']) ? 'checked' : ''; ?>>
                            <label class="form-check-label required mb-0" for="lgpd_consent">
                                Acepto la <a href="politica-privacidad.php" class="text-decoration-none" target="_blank">Política de Privacidad</a>
                            </label>
                            <div id="lgpd-help" class="form-text">
                                Consentimiento para el tratamiento de datos personales según las regulaciones de privacidad
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100 mb-3">
                            <i class="fas fa-user-plus" aria-hidden="true"></i> 
                            Crear Cuenta
                        </button>
                    </form>
                    
                    <hr>
                    
                    <div class="text-center">
                        <p class="mb-0">
                            ¿Ya tienes una cuenta? 
                            <a href="login.php" class="text-decoration-none">Inicia sesión aquí</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Scripts para mostrar/ocultar contraseñas y validación
document.addEventListener('DOMContentLoaded', function() {
    // Toggle para contraseña
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    
    // Toggle para confirmación de contraseña
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPasswordField = document.getElementById('confirm_password');
    
    // Función para alternar visibilidad de contraseña
    function togglePasswordVisibility(button, field) {
        if (button && field) {
            const icon = button.querySelector('i');
            
            button.addEventListener('click', function() {
                const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
                field.setAttribute('type', type);
                
                if (type === 'password') {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    button.setAttribute('aria-label', 'Mostrar contraseña');
                } else {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                    button.setAttribute('aria-label', 'Ocultar contraseña');
                }
            });
        }
    }
    
    togglePasswordVisibility(togglePassword, passwordField);
    togglePasswordVisibility(toggleConfirmPassword, confirmPasswordField);
    
    // Validación de confirmación de contraseña
    function validatePasswordMatch() {
        if (confirmPasswordField && passwordField) {
            if (confirmPasswordField.value && passwordField.value !== confirmPasswordField.value) {
                confirmPasswordField.setCustomValidity('Las contraseñas no coinciden');
            } else {
                confirmPasswordField.setCustomValidity('');
            }
        }
    }
    
    if (passwordField) passwordField.addEventListener('input', validatePasswordMatch);
    if (confirmPasswordField) confirmPasswordField.addEventListener('input', validatePasswordMatch);
});
</script>

<?php include 'footer.php'; ?>