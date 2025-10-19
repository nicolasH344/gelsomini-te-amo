@@ -1,168 +1,168 @@
<?php 
// Incluir configurações
require_once 'config.php';

// Variável para armazenar a mensagem de erro localmente
$error = null;

// Processar login se formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Se "Lembrar de mim" for marcado, estende a duração do cookie da sessão
    if (!empty($_POST['remember'])) {
        $lifetime = 60 * 60 * 24 * 30; // 30 dias
        session_set_cookie_params($lifetime);
        session_regenerate_id(true);
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

// Se já estiver logado, redirecionar
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Definir título da página
$title = 'Entrar';

include 'header.php'; 
?>

<div class="container mt-5">
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
                <div class="card-header bg-primary text-white">
                <div class="card-header">
                    <h1 class="h4 mb-0">
                        <i class="fas fa-sign-in-alt" aria-hidden="true"></i> 
                        Entrar na sua conta
                    </h1>
                </div>
                <div class="card-body">
                    <form method="POST" action="login.php" novalidate>
                        <div class="mb-3">
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
                        
                        <div class="mb-3">
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
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="remember" 
                                   name="remember"
                                   <?php echo isset($_POST['remember']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="remember">
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
                        <p class="mb-2">
                            Não tem uma conta? 
                            <a href="register.php" class="text-decoration-none">Registre-se aqui</a>
                        </p>
                        <p class="mb-0">
                            <a href="#" class="text-decoration-none">Esqueceu sua senha?</a>
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