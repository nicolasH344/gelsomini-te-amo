<?php
require_once '../config.php';
require_once '../email_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch ($action) {
    case 'request_code':
        $email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
        
        if (!$email) {
            echo json_encode(['success' => false, 'message' => 'Email inválido']);
            exit;
        }
        
        // Verificar se email existe
        $conn = getDBConnection();
        if (!$conn) {
            echo json_encode(['success' => false, 'message' => 'Erro de conexão']);
            exit;
        }
        
        try {
            $stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Gerar código de 6 dígitos seguro
                $code = sprintf('%06d', random_int(100000, 999999));
                
                // Salvar código no banco
                if (saveResetCode($email, $code)) {
                    $name = $user['first_name'] . ' ' . $user['last_name'];
                    
                    // Tentar enviar email
                    if (sendResetCodeEmail($email, $code, $name)) {
                        echo json_encode(['success' => true, 'message' => 'Código enviado']);
                    } else {
                        // Para desenvolvimento, mostrar código
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Código enviado',
                            'debug_code' => $code
                        ]);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erro interno']);
                }
            } else {
                // Não revelar se email existe
                echo json_encode(['success' => true, 'message' => 'Se o email existir, o código será enviado']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erro interno']);
        }
        break;
        
    case 'verify_code':
        $email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $code = $input['code'] ?? '';
        
        if (!$email || !$code) {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
            exit;
        }
        
        if (verifyResetCode($email, $code)) {
            echo json_encode(['success' => true, 'message' => 'Código válido']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Código inválido ou expirado']);
        }
        break;
        
    case 'reset_password':
        $email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $code = $input['code'] ?? '';
        $password = $input['password'] ?? '';
        
        if (!$email || !$code || strlen($password) < 8) {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
            exit;
        }
        
        if (resetUserPassword($email, $code, $password)) {
            echo json_encode(['success' => true, 'message' => 'Senha redefinida']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao redefinir senha']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
}

// Funções auxiliares
function saveResetCode($email, $code) {
    $conn = getDBConnection();
    if (!$conn) return false;
    
    try {
        $conn->exec("CREATE TABLE IF NOT EXISTS password_codes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            code VARCHAR(6) NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX(email)
        )");
        
        $conn->exec("DELETE FROM password_codes WHERE expires_at < NOW()");
        
        $stmt = $conn->prepare("INSERT INTO password_codes (email, code, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE))");
        return $stmt->execute([$email, $code]);
    } catch (PDOException $e) {
        return false;
    }
}

function verifyResetCode($email, $code) {
    $conn = getDBConnection();
    if (!$conn) return false;
    
    try {
        $stmt = $conn->prepare("SELECT id FROM password_codes WHERE email = ? AND code = ? AND expires_at > NOW()");
        $stmt->execute([$email, $code]);
        return $stmt->fetchColumn() !== false;
    } catch (PDOException $e) {
        return false;
    }
}

function resetUserPassword($email, $code, $password) {
    if (!verifyResetCode($email, $code)) return false;
    
    $conn = getDBConnection();
    if (!$conn) return false;
    
    try {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        $result = $stmt->execute([$hash, $email]);
        
        if ($result) {
            $conn->prepare("DELETE FROM password_codes WHERE email = ?")->execute([$email]);
        }
        
        return $result;
    } catch (PDOException $e) {
        return false;
    }
}

function sendResetCodeEmail($email, $code, $name) {
    $subject = 'Código de Recuperação - WebLearn';
    $message = "
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='color: #6f42c1;'>Código de Recuperação</h2>
            <p>Olá, {$name}!</p>
            <p>Seu código de verificação é:</p>
            <div style='background: #f8f9fa; padding: 20px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 3px; margin: 20px 0;'>
                {$code}
            </div>
            <p>Este código expira em 15 minutos.</p>
            <p>Se você não solicitou esta recuperação, ignore este email.</p>
        </div>
    </body>
    </html>";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: WebLearn <noreply@weblearn.com>\r\n";
    
    return mail($email, $subject, $message, $headers);
}
?>