<?php 
// Incluir configurações
require_once 'config.php';

// Processar registro se formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = processRegister($_POST);
    
    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
        header('Location: login.php');
        exit;
    } else {
        $_SESSION['error'] = $result['message'];
        $_SESSION['form_data'] = $_POST; // Manter dados do formulário
    }
}

// Se já estiver logado, redirecionar
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Definir título da página
$title = 'Criar Conta';

include 'header.php'; 
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h1 class="h4 mb-0">
                        <i class="fas fa-user-plus" aria-hidden="true"></i> 
                        Create New Account
                    </h1>
                </div>
                <div class="card-body">
                    <form method="POST" action="register.php" novalidate>
                        <fieldset>
                            <legend class="visually-hidden">Ipersonal information</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label required">Name</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="first_name" 
                                               name="first_name" 
                                               required
                                               autocomplete="given-name"
                                               aria-describedby="first_name-help"
                                               value="<?php echo isset($_SESSION['form_data']['first_name']) ? sanitize($_SESSION['form_data']['first_name']) : ''; ?>">
                                        <div id="first_name-help" class="form-text">
                                        Enter your first name
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="last_name" class="form-label required">Last name</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="last_name" 
                                               name="last_name" 
                                               required
                                               autocomplete="family-name"
                                               aria-describedby="last_name-help"
                                               value="<?php echo isset($_SESSION['form_data']['last_name']) ? sanitize($_SESSION['form_data']['last_name']) : ''; ?>">
                                        <div id="last_name-help" class="form-text">
                                        Enter your last name
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        
                        <fieldset>
                            <legend class="visually-hidden">Access information</legend>
                            <div class="mb-3">
                                <label for="username" class="form-label required">Nome de Usuário</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="username" 
                                       name="username" 
                                       required
                                       autocomplete="username"
                                       aria-describedby="username-help"
                                       pattern="[a-zA-Z0-9_]{3,}"
                                       value="<?php echo isset($_SESSION['form_data']['username']) ? sanitize($_SESSION['form_data']['username']) : ''; ?>">
                                <div id="username-help" class="form-text">
                                Only letters, numbers, and underscores. Minimum 3 characters.
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
                                       value="<?php echo isset($_SESSION['form_data']['email']) ? sanitize($_SESSION['form_data']['email']) : ''; ?>">
                                <div id="email-help" class="form-text">
                                Please enter a valid email address
                                </div>
                            </div>
                        </fieldset>
                        
                        <fieldset>
                            <legend class="visually-hidden">Set password</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label required">Password</label>
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
                                                    aria-label="Mostrar/ocultar senha">
                                                <i class="fas fa-eye" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        <div id="password-help" class="form-text">
                                        Minimum 6 characters. Use a combination of letters, numbers, and symbols.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label required">Confirm Password</label>
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
                                                    aria-label="Show/hide password confirmation">
                                                <i class="fas fa-eye" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        <div id="confirm_password-help" class="form-text">
                                        Please enter the same password again.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="terms" 
                                   name="terms"
                                   required
                                   aria-describedby="terms-help">
                            <label class="form-check-label" for="terms">
                            I agree to the terms of use and privacy policy
                            </label>
                            <div id="terms-help" class="form-text">
                            You must accept the terms to create an account.
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100 mb-3">
                            <i class="fas fa-user-plus" aria-hidden="true"></i> 
                            Create Account
                        </button>
                    </form>
                    
                    <hr>
                    
                    <div class="text-center">
                        <p class="mb-0">
                        Already have an account?
                            <a href="login.php" class="text-decoration-none">Log in here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Scripts para mostrar/ocultar senhas e validação
document.addEventListener('DOMContentLoaded', function() {
    // Toggle para senha
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    
    // Toggle para confirmação de senha
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPasswordField = document.getElementById('confirm_password');
    
    // Função para alternar visibilidade da senha
    function togglePasswordVisibility(button, field) {
        if (button && field) {
            const icon = button.querySelector('i');
            
            button.addEventListener('click', function() {
                const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
                field.setAttribute('type', type);
                
                if (type === 'password') {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    button.setAttribute('aria-label', 'Mostrar senha');
                } else {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                    button.setAttribute('aria-label', 'Ocultar senha');
                }
            });
        }
    }
    
    togglePasswordVisibility(togglePassword, passwordField);
    togglePasswordVisibility(toggleConfirmPassword, confirmPasswordField);
    
    // Validação de confirmação de senha
    function validatePasswordMatch() {
        if (confirmPasswordField && passwordField) {
            if (confirmPasswordField.value && passwordField.value !== confirmPasswordField.value) {
                confirmPasswordField.setCustomValidity('As senhas não coincidem');
            } else {
                confirmPasswordField.setCustomValidity('');
            }
        }
    }
    
    if (passwordField) passwordField.addEventListener('input', validatePasswordMatch);
    if (confirmPasswordField) confirmPasswordField.addEventListener('input', validatePasswordMatch);
});
</script>

<?php 
// Limpar dados do formulário da sessão após exibição
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>

<?php include 'footer.php'; ?>

