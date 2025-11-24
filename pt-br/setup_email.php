<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Email - WebLearn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h1 class="h3 mb-0">📧 Configurar Envio de Email</h1>
                    </div>
                    <div class="card-body">
                        
                        <div class="alert alert-info">
                            <h5>📋 Instruções para Configurar Email:</h5>
                            <ol>
                                <li><strong>Gmail:</strong> Ative a verificação em 2 etapas e gere uma "Senha de App"</li>
                                <li><strong>Outlook:</strong> Use sua senha normal ou senha de app</li>
                                <li><strong>Outros:</strong> Consulte as configurações SMTP do seu provedor</li>
                            </ol>
                        </div>

                        <h5>🔧 Editar Configurações:</h5>
                        <p>Abra o arquivo <code>pt-br/email_config.php</code> e altere:</p>
                        
                        <div class="bg-dark text-light p-3 rounded mb-3">
                            <pre><code>// Para Gmail:
$smtp_host = 'smtp.gmail.com';
$smtp_port = 587;
$smtp_user = 'seu-email@gmail.com';
$smtp_pass = 'sua-senha-de-app';

// Para Outlook:
$smtp_host = 'smtp-mail.outlook.com';
$smtp_port = 587;
$smtp_user = 'seu-email@outlook.com';
$smtp_pass = 'sua-senha';</code></pre>
                        </div>

                        <h5>🧪 Testar Envio:</h5>
                        <form id="testForm">
                            <div class="mb-3">
                                <label for="testEmail" class="form-label">Email de Teste:</label>
                                <input type="email" class="form-control" id="testEmail" required>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane"></i> Enviar Email de Teste
                            </button>
                        </form>
                        
                        <div id="result" class="mt-3"></div>

                        <hr>
                        
                        <h5>📱 Modo Desenvolvimento:</h5>
                        <p>Se não conseguir configurar email, o sistema funcionará em <strong>modo simulação</strong>:</p>
                        <ul>
                            <li>✅ Códigos aparecerão no console do navegador</li>
                            <li>✅ Funcionalidade completa sem email real</li>
                            <li>✅ Ideal para testes locais</li>
                        </ul>

                        <div class="text-center mt-4">
                            <a href="forgot_password.php" class="btn btn-primary me-2">
                                Testar Recuperação de Senha
                            </a>
                            <a href="index.php" class="btn btn-outline-secondary">
                                Voltar ao Início
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('testForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const email = document.getElementById('testEmail').value;
        const result = document.getElementById('result');
        
        result.innerHTML = '<div class="alert alert-info">Enviando...</div>';
        
        try {
            const response = await fetch('api/password_reset.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'request_code',
                    email: email
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                result.innerHTML = `
                    <div class="alert alert-success">
                        ✅ Email enviado com sucesso!
                        ${data.debug_code ? `<br><strong>Código de debug:</strong> ${data.debug_code}` : ''}
                    </div>
                `;
            } else {
                result.innerHTML = `<div class="alert alert-danger">❌ ${data.message}</div>`;
            }
        } catch (error) {
            result.innerHTML = `<div class="alert alert-danger">❌ Erro: ${error.message}</div>`;
        }
    });
    </script>
</body>
</html>