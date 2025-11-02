<?php
require_once 'config.php';
$title = 'Olvidé la Contraseña';

// Se já estiver logado, redirecionar
if (isLoggedIn()) {
    redirect('index.php');
}

include 'header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-key me-2"></i>
                        Recuperar Contraseña
                    </h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Ingresa tu email para recibir un código de verificación.
                    </p>
                    
                    <div id="alert-container"></div>
                    
                    <!-- Formulario de email -->
                    <form id="emailForm">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" id="emailBtn">
                            <i class="fas fa-paper-plane me-2"></i>
                            Enviar Código
                        </button>
                    </form>
                    
                    <!-- Formulario de código -->
                    <form id="codeForm" style="display: none;">
                        <div class="mb-3">
                            <label for="code" class="form-label">Código de Verificación</label>
                            <input type="text" class="form-control" id="code" name="code" maxlength="6" required>
                            <div class="form-text">Ingresa el código de 6 dígitos enviado a tu email.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" id="codeBtn">
                            <i class="fas fa-check me-2"></i>
                            Verificar Código
                        </button>
                    </form>
                    
                    <!-- Formulario de contraseña -->
                    <form id="passwordForm" style="display: none;">
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="newPassword" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="confirmPassword" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100" id="passwordBtn">
                            <i class="fas fa-save me-2"></i>
                            Restablecer Contraseña
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="login.php" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i>
                            Volver al Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentEmail = '';
let currentCode = '';

// Formulario de email
document.getElementById('emailForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const btn = document.getElementById('emailBtn');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando...';
    
    try {
        const response = await fetch('api/password_reset.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'request_code',
                email: email
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            currentEmail = email;
            showAlert('¡Código enviado con éxito!', 'success');
            document.getElementById('emailForm').style.display = 'none';
            document.getElementById('codeForm').style.display = 'block';
            
            if (result.debug_code) {
                console.log('Código de debug:', result.debug_code);
                showAlert(`Código de debug: ${result.debug_code}`, 'info');
            }
        } else {
            showAlert(result.message, 'danger');
        }
    } catch (error) {
        showAlert('Error de conexión', 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});

// Formulario de código
document.getElementById('codeForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const code = document.getElementById('code').value;
    const btn = document.getElementById('codeBtn');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verificando...';
    
    try {
        const response = await fetch('api/password_reset.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'verify_code',
                email: currentEmail,
                code: code
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            currentCode = code;
            showAlert('¡Código verificado!', 'success');
            document.getElementById('codeForm').style.display = 'none';
            document.getElementById('passwordForm').style.display = 'block';
        } else {
            showAlert(result.message, 'danger');
        }
    } catch (error) {
        showAlert('Error de conexión', 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});

// Formulario de contraseña
document.getElementById('passwordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const password = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (password !== confirmPassword) {
        showAlert('Las contraseñas no coinciden', 'danger');
        return;
    }
    
    if (password.length < 8) {
        showAlert('La contraseña debe tener al menos 8 caracteres', 'danger');
        return;
    }
    
    const btn = document.getElementById('passwordBtn');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Restableciendo...';
    
    try {
        const response = await fetch('api/password_reset.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'reset_password',
                email: currentEmail,
                code: currentCode,
                password: password
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('¡Contraseña restablecida con éxito!', 'success');
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
        } else {
            showAlert(result.message, 'danger');
        }
    } catch (error) {
        showAlert('Error de conexión', 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});

function showAlert(message, type) {
    const alertContainer = document.getElementById('alert-container');
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    alertContainer.innerHTML = '';
    alertContainer.appendChild(alertDiv);
}
</script>

<?php include 'footer.php'; ?>