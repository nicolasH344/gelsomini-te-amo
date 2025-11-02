<?php
require_once 'config.php';
$title = 'Esqueci a Senha';

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
                        Recuperar Senha
                    </h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Digite seu email para receber um código de verificação.
                    </p>
                    
                    <div id="alert-container"></div>
                    
                    <!-- Formulário de email -->
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
                    
                    <!-- Formulário de código -->
                    <form id="codeForm" style="display: none;">
                        <div class="mb-3">
                            <label for="code" class="form-label">Código de Verificação</label>
                            <input type="text" class="form-control" id="code" name="code" maxlength="6" required>
                            <div class="form-text">Digite o código de 6 dígitos enviado para seu email.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" id="codeBtn">
                            <i class="fas fa-check me-2"></i>
                            Verificar Código
                        </button>
                    </form>
                    
                    <!-- Formulário de nova senha -->
                    <form id="passwordForm" style="display: none;">
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">Nova Senha</label>
                            <input type="password" class="form-control" id="newPassword" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirmar Senha</label>
                            <input type="password" class="form-control" id="confirmPassword" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100" id="passwordBtn">
                            <i class="fas fa-save me-2"></i>
                            Redefinir Senha
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="login.php" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i>
                            Voltar ao Login
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

// Formulário de email
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
            showAlert('Código enviado com sucesso!', 'success');
            document.getElementById('emailForm').style.display = 'none';
            document.getElementById('codeForm').style.display = 'block';
            
            // Mostrar código no console para debug
            if (result.debug_code) {
                console.log('Código de debug:', result.debug_code);
                showAlert(`Código de debug: ${result.debug_code}`, 'info');
            }
        } else {
            showAlert(result.message, 'danger');
        }
    } catch (error) {
        showAlert('Erro de conexão', 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});

// Formulário de código
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
            showAlert('Código verificado!', 'success');
            document.getElementById('codeForm').style.display = 'none';
            document.getElementById('passwordForm').style.display = 'block';
        } else {
            showAlert(result.message, 'danger');
        }
    } catch (error) {
        showAlert('Erro de conexão', 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});

// Formulário de senha
document.getElementById('passwordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const password = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (password !== confirmPassword) {
        showAlert('As senhas não coincidem', 'danger');
        return;
    }
    
    if (password.length < 8) {
        showAlert('A senha deve ter pelo menos 8 caracteres', 'danger');
        return;
    }
    
    const btn = document.getElementById('passwordBtn');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Redefinindo...';
    
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
            showAlert('Senha redefinida com sucesso!', 'success');
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
        } else {
            showAlert(result.message, 'danger');
        }
    } catch (error) {
        showAlert('Erro de conexão', 'danger');
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