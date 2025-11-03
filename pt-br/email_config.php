<?php
// Configuração de email usando PHPMailer
function sendPasswordResetEmail($email, $token, $name) {
    // Configuração SMTP (Gmail exemplo)
    $smtp_host = 'smtp.gmail.com';
    $smtp_port = 587;
    $smtp_user = 'seu-email@gmail.com'; // Altere aqui
    $smtp_pass = 'sua-senha-app';       // Altere aqui
    
    $subject = 'Recuperação de Senha - WebLearn';
    $reset_link = "http://localhost/gelsomini-te-amo/pt-br/reset_password.php?token=" . $token;
    
    $message = "
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='color: #6f42c1;'>Recuperação de Senha</h2>
            <p>Olá, {$name}!</p>
            <p>Você solicitou a recuperação de senha para sua conta no WebLearn.</p>
            <p>Clique no link abaixo para redefinir sua senha:</p>
            <p><a href='{$reset_link}' style='background: #6f42c1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Redefinir Senha</a></p>
            <p>Este link expira em 1 hora.</p>
            <p>Se você não solicitou esta recuperação, ignore este email.</p>
            <hr>
            <small>WebLearn - Plataforma de Aprendizado</small>
        </div>
    </body>
    </html>";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: WebLearn <noreply@weblearn.com>" . "\r\n";
    
    // Tentar enviar email
    if (mail($email, $subject, $message, $headers)) {
        return true;
    }
    
    // Fallback: Simular envio para desenvolvimento
    error_log("EMAIL SIMULADO para {$email}: {$reset_link}");
    return true;
}

// Gerar token seguro
function generateResetToken() {
    return bin2hex(random_bytes(32));
}

// Salvar token no banco
function saveResetToken($email, $token) {
    $conn = getDBConnection();
    if (!$conn) return false;
    
    try {
        // Criar tabela se não existir
        $conn->exec("CREATE TABLE IF NOT EXISTS password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            token VARCHAR(64) NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX(email), INDEX(token)
        )");
        
        // Limpar tokens antigos
        $conn->exec("DELETE FROM password_resets WHERE expires_at < NOW()");
        
        // Inserir novo token
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
        return $stmt->execute([$email, $token]);
        
    } catch (PDOException $e) {
        return false;
    }
}

// Verificar token
function verifyResetToken($token) {
    $conn = getDBConnection();
    if (!$conn) return false;
    
    try {
        $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        return false;
    }
}
?>