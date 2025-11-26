<?php 
// Include configurations
require_once 'config.php';

// Variable to store error message locally
$error = null;

// Process login if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token';
    } else {
        // If "Remember me" is checked, extend session cookie duration
        if (!empty($_POST['remember'])) {
            $lifetime = 60 * 60 * 24 * 30; // 30 days
            setcookie(session_name(), session_id(), time() + $lifetime, '/', '', false, true);
        }

        $result = processLogin($_POST['username'] ?? '', $_POST['password'] ?? '');
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
            header('Location: index.php');
            exit;
        } else {
            // Use local variable to display error on login page
            $error = $result['message'];
        }
    }
}

// If already logged in, redirect
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Set page title
$title = 'Login';

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

.theme-purple .bubble { background: rgba(141, 28, 247, 0.31); }
.theme-blue .bubble { background: rgba(76, 152, 238, 0.33); }
.theme-green .bubble { background: rgba(42, 182, 75, 0.54); }
.theme-dark .bubble { background: rgba(212, 159, 255, 0.54); }
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
            
            <?php // Display local error message if any ?>
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                    <?php echo sanitize($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-header">
                    <h1 class="h4 mb-4">
                        <i class="fas fa-sign-in-alt" aria-hidden="true"></i> 
                        Sign in to your account
                    </h1>
                </div>
                <div class="card-body">
                    <form method="POST" action="login.php" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <div class="mb-4">
                            <label for="username" class="form-label required">Username or Email</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="username" 
                                   name="username" 
                                   required 
                                   autocomplete="username"
                                   aria-describedby="username-help"
                                   value="<?php echo isset($_POST['username']) ? sanitize($_POST['username']) : ''; ?>">
                            <div id="username-help" class="form-text">
                                Enter your username or email address
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label required">Password</label>
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
                                        aria-label="Show/hide password">
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div id="password-help" class="form-text">
                                Enter your password
                            </div>
                        </div>
                        
                        <div class="mb-4 form-check" style="color: #ffffffd8;">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="remember" 
                                   name="remember"
                                   <?php echo isset($_POST['remember']) ? 'checked' : ''; ?>>
                            <label class="form-check-label mb-0" for="remember">
                                Remember me on this device
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-sign-in-alt" aria-hidden="true"></i> 
                            Sign In
                        </button>
                    </form>
                    
                    <hr>
                    
                    <div class="text-center">
                        <p2 class="mb-0" style="color: #181818ff;">
                            Don't have an account? 
                            <a href="register.php" class="text-decoration-none">Register here</a>
                        </p2>
                        <p class="" style="color: #000000ff;">
                            <a href="forgot-password.php" class="text-decoration-none">Forgot your password?</a>
                        </p>
                    </div>
                    
                    <div class="alert alert-info mt-3" role="alert">
                        <strong>Test accounts:</strong><br>
                        Admin: <code>admin</code> / <code>admin123</code><br>
                        User: <code>usuario</code> / <code>123456</code>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Script to show/hide password
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
                togglePassword.setAttribute('aria-label', 'Show password');
            } else {
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
                togglePassword.setAttribute('aria-label', 'Hide password');
            }
        });
    }
});
</script>

<?php include 'footer.php'; ?>