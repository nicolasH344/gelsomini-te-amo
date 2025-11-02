<?php
require_once 'config.php';
$title = t('forgot_password', 'Esqueci a Senha');
include 'header.php';
?>

<section class="hero-section py-5 text-white position-relative">
    <div class="container py-5">
        <div class="row justify-content-center min-vh-75">
            <div class="col-lg-6 col-md-8">
                <div class="card bg-dark bg-opacity-50 border-0 shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-gradient">
                                <i class="fas fa-key me-2" aria-hidden="true"></i>
                                <?php echo t('forgot_password', 'Esqueci a Senha'); ?>
                            </h2>
                            <p class="text-light">
                                <?php echo t('enter_email_for_code', 'Digite seu email para receber um código de verificação.'); ?>
                            </p>
                        </div>

                        <form id="requestCodeForm">
                            <div class="mb-4">
                                <label for="email" class="form-label text-light">
                                    <i class="fas fa-envelope me-2" aria-hidden="true"></i>
                                    <?php echo t('email', 'Email'); ?>
                                </label>
                                <input type="email" class="form-control form-control-lg bg-dark text-light border-primary" 
                                       id="email" name="email" required 
                                       placeholder="<?php echo t('enter_your_email', 'Digite seu email'); ?>">
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fas fa-paper-plane me-2" aria-hidden="true"></i>
                                    <?php echo t('send_verification_code', 'Enviar Código de Verificação'); ?>
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <a href="login.php" class="text-warning text-decoration-none">
                                <i class="fas fa-arrow-left me-2" aria-hidden="true"></i>
                                <?php echo t('back_to_login', 'Voltar para o Login'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal para inserir código -->
<div class="modal fade" id="codeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-primary">
            <div class="modal-header border-primary">
                <h5 class="modal-title text-light">
                    <i class="fas fa-shield-alt me-2" aria-hidden="true"></i>
                    <?php echo t('verification_code', 'Código de Verificação'); ?>
                </h5>
            </div>
            <div class="modal-body">
                <p class="text-light mb-3">
                    <?php echo t('enter_verification_code', 'Digite o código de 6 dígitos enviado para seu email.'); ?>
                </p>
                
                <form id="verifyCodeForm">
                    <input type="hidden" id="verifyEmail" name="email">
                    
                    <div class="mb-3">
                        <label class="form-label text-light"><?php echo t('verification_code', 'Código de Verificação'); ?></label>
                        <div class="d-flex gap-2 justify-content-center">
                            <input type="text" class="form-control form-control-lg text-center code-input" 
                                   maxlength="1" pattern="[0-9]" required>
                            <input type="text" class="form-control form-control-lg text-center code-input" 
                                   maxlength="1" pattern="[0-9]" required>
                            <input type="text" class="form-control form-control-lg text-center code-input" 
                                   maxlength="1" pattern="[0-9]" required>
                            <input type="text" class="form-control form-control-lg text-center code-input" 
                                   maxlength="1" pattern="[0-9]" required>
                            <input type="text" class="form-control form-control-lg text-center code-input" 
                                   maxlength="1" pattern="[0-9]" required>
                            <input type="text" class="form-control form-control-lg text-center code-input" 
                                   maxlength="1" pattern="[0-9]" required>
                        </div>
                        <input type="hidden" id="fullCode" name="code" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" id="verifyBtn">
                            <i class="fas fa-check me-2" aria-hidden="true"></i>
                            <?php echo t('verify_code', 'Verificar Código'); ?>
                        </button>
                        <button type="button" class="btn btn-outline-light" id="resendCodeBtn">
                            <i class="fas fa-redo me-2" aria-hidden="true"></i>
                            <?php echo t('resend_code', 'Reenviar Código'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para nova senha -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-primary">
            <div class="modal-header border-primary">
                <h5 class="modal-title text-light">
                    <i class="fas fa-lock me-2" aria-hidden="true"></i>
                    <?php echo t('new_password', 'Nova Senha'); ?>
                </h5>
            </div>
            <div class="modal-body">
                <form id="resetPasswordForm">
                    <input type="hidden" id="resetEmail" name="email">
                    <input type="hidden" id="resetCode" name="code">
                    
                    <div class="mb-3">
                        <label for="newPassword" class="form-label text-light">
                            <?php echo t('new_password', 'Nova Senha'); ?>
                        </label>
                        <input type="password" class="form-control form-control-lg bg-dark text-light border-primary" 
                               id="newPassword" name="password" required 
                               placeholder="<?php echo t('enter_new_password', 'Digite a nova senha'); ?>">
                        <div class="form-text text-light">
                            <?php echo t('password_requirements', 'A senha deve ter pelo menos 8 caracteres.'); ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label text-light">
                            <?php echo t('confirm_password', 'Confirmar Senha'); ?>
                        </label>
                        <input type="password" class="form-control form-control-lg bg-dark text-light border-primary" 
                               id="confirmPassword" required 
                               placeholder="<?php echo t('confirm_new_password', 'Confirme a nova senha'); ?>">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg" id="resetBtn">
                            <i class="fas fa-save me-2" aria-hidden="true"></i>
                            <?php echo t('reset_password', 'Redefinir Senha'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const requestForm = document.getElementById('requestCodeForm');
    const verifyForm = document.getElementById('verifyCodeForm');
    const resetForm = document.getElementById('resetPasswordForm');
    const codeInputs = document.querySelectorAll('.code-input');
    const fullCodeInput = document.getElementById('fullCode');
    const resendBtn = document.getElementById('resendCodeBtn');

    // Configurar inputs de código
    codeInputs.forEach((input, index) => {
        input.addEventListener('input', function() {
            if (this.value.length === 1 && index < codeInputs.length - 1) {
                codeInputs[index + 1].focus();
            }
            updateFullCode();
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                codeInputs[index - 1].focus();
            }
        });
    });

    function updateFullCode() {
        const code = Array.from(codeInputs).map(input => input.value).join('');
        fullCodeInput.value = code;
    }

    // Solicitar código
    requestForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        const email = document.getElementById('email').value;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i><?php echo t("sending", "Enviando..."); ?>';

        try {
            const response = await fetch('api/password_reset.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'request_code',
                    email: email
                })
            });

            const result = await response.json();

            if (result.success) {
                document.getElementById('verifyEmail').value = email;
                const modal = new bootstrap.Modal(document.getElementById('codeModal'));
                modal.show();
                
                showAlert('<?php echo t("code_sent", "Código enviado com sucesso!"); ?>', 'success');
            } else {
                showAlert(result.message, 'error');
            }
        } catch (error) {
            showAlert('<?php echo t("connection_error", "Erro de conexão."); ?>', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // Verificar código
    verifyForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const verifyBtn = document.getElementById('verifyBtn');
        const originalText = verifyBtn.innerHTML;
        const email = document.getElementById('verifyEmail').value;
        const code = fullCodeInput.value;

        verifyBtn.disabled = true;
        verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i><?php echo t("verifying", "Verificando..."); ?>';

        try {
            const response = await fetch('api/password_reset.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'verify_code',
                    email: email,
                    code: code
                })
            });

            const result = await response.json();

            if (result.success) {
                document.getElementById('resetEmail').value = email;
                document.getElementById('resetCode').value = code;
                
                const codeModal = bootstrap.Modal.getInstance(document.getElementById('codeModal'));
                codeModal.hide();
                
                const passwordModal = new bootstrap.Modal(document.getElementById('passwordModal'));
                passwordModal.show();
            } else {
                showAlert(result.message, 'error');
            }
        } catch (error) {
            showAlert('<?php echo t("connection_error", "Erro de conexão."); ?>', 'error');
        } finally {
            verifyBtn.disabled = false;
            verifyBtn.innerHTML = originalText;
        }
    });

    // Reenviar código
    resendBtn.addEventListener('click', async function() {
        const email = document.getElementById('verifyEmail').value;
        
        resendBtn.disabled = true;
        resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i><?php echo t("sending", "Enviando..."); ?>';

        try {
            const response = await fetch('api/password_reset.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'request_code',
                    email: email
                })
            });

            const result = await response.json();

            if (result.success) {
                showAlert('<?php echo t("code_resent", "Código reenviado com sucesso!"); ?>', 'success');
                
                // Limpar inputs
                codeInputs.forEach(input => input.value = '');
                codeInputs[0].focus();
                updateFullCode();
            } else {
                showAlert(result.message, 'error');
            }
        } catch (error) {
            showAlert('<?php echo t("connection_error", "Erro de conexão."); ?>', 'error');
        } finally {
            resendBtn.disabled = false;
            resendBtn.innerHTML = '<i class="fas fa-redo me-2"></i><?php echo t("resend_code", "Reenviar Código"); ?>';
        }
    });

    // Redefinir senha
    resetForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const resetBtn = document.getElementById('resetBtn');
        const originalText = resetBtn.innerHTML;
        const email = document.getElementById('resetEmail').value;
        const code = document.getElementById('resetCode').value;
        const password = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (password !== confirmPassword) {
            showAlert('<?php echo t("passwords_dont_match", "As senhas não coincidem."); ?>', 'error');
            return;
        }

        if (password.length < 8) {
            showAlert('<?php echo t("password_too_short", "A senha deve ter pelo menos 8 caracteres."); ?>', 'error');
            return;
        }

        resetBtn.disabled = true;
        resetBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i><?php echo t("resetting", "Redefinindo..."); ?>';

        try {
            const response = await fetch('api/password_reset.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'reset_password',
                    email: email,
                    code: code,
                    password: password
                })
            });

            const result = await response.json();

            if (result.success) {
                showAlert('<?php echo t("password_reset_success", "Senha redefinida com sucesso!"); ?>', 'success');
                
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 2000);
            } else {
                showAlert(result.message, 'error');
            }
        } catch (error) {
            showAlert('<?php echo t("connection_error", "Erro de conexão."); ?>', 'error');
        } finally {
            resetBtn.disabled = false;
            resetBtn.innerHTML = originalText;
        }
    });

    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
});
</script>

<style>
.code-input {
    font-size: 1.5rem;
    font-weight: bold;
    height: 60px;
}

.code-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25);
}

.modal-content {
    backdrop-filter: blur(10px);
}

.form-control {
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25);
}

.btn {
    transition: all 0.3s ease;
}

.btn:disabled {
    opacity: 0.6;
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

.card {
    animation: fadeInUp 0.6s ease-out;
}

/* Responsividade */
@media (max-width: 768px) {
    .code-input {
        height: 50px;
        font-size: 1.2rem;
    }
    
    .card-body {
        padding: 2rem !important;
    }
}
</style>

<?php include 'footer.php'; ?>