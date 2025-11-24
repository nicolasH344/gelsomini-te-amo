@@ -1,168 +1,168 @@
<?php 
// Incluir configurações
require_once 'config.php';

// Variável para armazenar a mensagem de erro localmente
$error = null;

// Processar login se formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar CSRF token
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Token de segurança inválido';
    } else {
        // Se "Lembrar de mim" for marcado, estende a duração do cookie da sessão
        if (!empty($_POST['remember'])) {
            $lifetime = 60 * 60 * 24 * 30; // 30 dias
            SecurityHelper::setSecureCookie(session_name(), session_id(), time() + $lifetime);
        }

        $result = processLogin($_POST['username'] ?? '', $_POST['password'] ?? '');
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
            header('Location: index.php');
            exit;
        } else {
            // Usar uma variável local para exibir o erro na página de login
            $error = $result['message'];
        }
    }
}

// Se já estiver logado, redirecionar
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Definir título da página
$title = 'Entrar';

include 'header.php'; 
?>

<style>
.bubbles-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 1;
    pointer-events: none;
}

.bubble {
    position: absolute;
    bottom: -100px;
    background: rgba(74, 144, 226, 0.3);
    border-radius: 50%;
    animation: rise 14.25s infinite linear;
}

.bubble:nth-child(1) { left: 10%; width: 120px; height: 120px; animation-delay: 0s; }
.bubble:nth-child(2) { left: 20%; width: 25px; height: 25px; animation-delay: 2s; }
.bubble:nth-child(3) { left: 35%; width: 80px; height: 80px; animation-delay: 4s; }
.bubble:nth-child(4) { left: 50%; width: 150px; height: 150px; animation-delay: 0s; }
.bubble:nth-child(5) { left: 70%; width: 60px; height: 60px; animation-delay: 3s; }
.bubble:nth-child(6) { left: 80%; width: 30px; height: 30px; animation-delay: 5s; }
.bubble:nth-child(7) { left: 15%; width: 100px; height: 100px; animation-delay: 1s; }
.bubble:nth-child(8) { left: 65%; width: 40px; height: 40px; animation-delay: 6s; }
.bubble:nth-child(9) { left: 5%; width: 90px; height: 90px; animation-delay: 7s; }
.bubble:nth-child(10) { left: 25%; width: 35px; height: 35px; animation-delay: 1.5s; }
.bubble:nth-child(11) { left: 45%; width: 110px; height: 110px; animation-delay: 8s; }
.bubble:nth-child(12) { left: 75%; width: 50px; height: 50px; animation-delay: 2.5s; }
.bubble:nth-child(13) { left: 85%; width: 20px; height: 20px; animation-delay: 9s; }
.bubble:nth-child(14) { left: 55%; width: 70px; height: 70px; animation-delay: 4.5s; }



@keyframes rise {
    to {
        bottom: 100vh;
        transform: translateX(100px);
    }
}

.theme-purple .bubble { background: rgba(138, 43, 226, 0.3); }
.theme-blue .bubble { background: rgba(74, 144, 226, 0.3); }
.theme-green .bubble { background: rgba(40, 167, 69, 0.3); }
.theme-dark .bubble { background: rgba(255, 255, 255, 0.15); }
</style>

<div class="bubbles-container">
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
</div>

<div class="container mt-5" style="position: relative; z-index: 10;">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            <?php // Exibir a mensagem de erro local, se houver ?>
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                    <?php echo sanitize($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-header">
                    <h1 class="h4 mb-4">
                        <i class="fas fa-sign-in-alt" aria-hidden="true"></i> 
                        Entrar na sua conta
                    </h1>
                </div>
                <div class="card-body">
                    <form method="POST" action="login.php" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <div class="mb-4">
                            <label for="username" class="form-label required">Usuário ou Email</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="username" 
                                   name="username" 
                                   required 
                                   autocomplete="username"
                                   aria-describedby="username-help"
                                   value="<?php echo isset($_POST['username']) ? sanitize($_POST['username']) : ''; ?>">
                            <div id="username-help" class="form-text">
                                Digite seu nome de usuário ou endereço de email
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label required">Senha</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       aria-describedby="password-help">
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        id="togglePassword"
                                        aria-label="Mostrar/ocultar senha">
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div id="password-help" class="form-text">
                                Digite sua senha
                            </div>
                        </div>
                        
                        <div class="mb-4 form-check" style="color: #fff;">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="remember" 
                                   name="remember"
                                   <?php echo isset($_POST['remember']) ? 'checked' : ''; ?>>
                            <label class="form-check-label mb-0" for="remember" >
                                Lembrar de mim neste dispositivo
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-sign-in-alt" aria-hidden="true"></i> 
                            Entrar
                        </button>
                    </form>
                    
                    <hr>
                    
                    <div class="text-center">
                        <p2 class="mb-0" >
                            Não tem uma conta? 
                            <a href="register.php" class="text-decoration-none">Registre-se aqui</a>
                        </p2>
                        <p class="" style="color: #fff;">
                            <a href="esqueci-senha.php" class="text-decoration-none">Esqueceu sua senha?</a>
                        </p>
                    </div>
                    
                    <div class="alert alert-info mt-3" role="alert">
                        <strong>Contas de teste:</strong><br>
                        Admin: <code>admin</code> / <code>admin123</code><br>
                        Usuário: <code>usuario</code> / <code>123456</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Script para mostrar/ocultar senha
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    
    if (togglePassword && passwordField) {
        const toggleIcon = togglePassword.querySelector('i');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            if (type === 'password') {
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
                togglePassword.setAttribute('aria-label', 'Mostrar senha');
            } else {
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
                togglePassword.setAttribute('aria-label', 'Ocultar senha');
            }
        });
    }
});
</script>

<?php include 'footer.php'; ?>