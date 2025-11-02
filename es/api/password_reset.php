<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch ($action) {
    case 'request_code':
        handleRequestCode($input);
        break;
    case 'verify_code':
        handleVerifyCode($input);
        break;
    case 'reset_password':
        handleResetPassword($input);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
}

function handleRequestCode($input) {
    $email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
    
    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'Email inválido']);
        return;
    }
    
    $conn = getDBConnection();
    if ($conn) {
        // Verificar se o email existe
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Gerar código de 6 dígitos
            $code = sprintf('%06d', mt_rand(0, 999999));
            $expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            
            // Criar tabela se não existir
            $conn->exec("CREATE TABLE IF NOT EXISTS password_reset_codes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                code VARCHAR(6) NOT NULL,
                expires_at TIMESTAMP NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )");
            
            // Salvar código no banco
            $stmt = $conn->prepare("INSERT INTO password_reset_codes (user_id, code, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $code, $expires_at]);
            
            // Simular envio de email
            $reset_link = "http://localhost/" . getCurrentLanguageFolder() . "/reset_password.php?code=" . $code;
            error_log("Reset code for $email: $code");
            error_log("Reset link: $reset_link");
        }
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Se o email existir em nossa base, você receberá um código de verificação.',
        'debug_code' => isset($code) ? $code : null
    ]);
}

function handleVerifyCode($input) {
    $email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $code = $input['code'] ?? '';
    
    if (!$email || !$code) {
        echo json_encode(['success' => false, 'message' => 'Email e código são obrigatórios']);
        return;
    }
    
    $conn = getDBConnection();
    if ($conn) {
        $stmt = $conn->prepare("SELECT prc.* FROM password_reset_codes prc 
                               JOIN users u ON prc.user_id = u.id 
                               WHERE u.email = ? AND prc.code = ? AND prc.expires_at > NOW()");
        $stmt->execute([$email, $code]);
        $reset = $stmt->fetch();
        
        if ($reset) {
            echo json_encode(['success' => true, 'message' => 'Código verificado com sucesso']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Código inválido ou expirado']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados']);
    }
}

function handleResetPassword($input) {
    $email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $code = $input['code'] ?? '';
    $password = $input['password'] ?? '';
    
    if (!$email || !$code || !$password) {
        echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios']);
        return;
    }
    
    if (strlen($password) < 8) {
        echo json_encode(['success' => false, 'message' => 'A senha deve ter pelo menos 8 caracteres']);
        return;
    }
    
    $conn = getDBConnection();
    if ($conn) {
        // Verificar código novamente
        $stmt = $conn->prepare("SELECT prc.user_id FROM password_reset_codes prc 
                               JOIN users u ON prc.user_id = u.id 
                               WHERE u.email = ? AND prc.code = ? AND prc.expires_at > NOW()");
        $stmt->execute([$email, $code]);
        $reset = $stmt->fetch();
        
        if ($reset) {
            // Atualizar senha
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$password_hash, $reset['user_id']]);
            
            // Remover códigos usados
            $stmt = $conn->prepare("DELETE FROM password_reset_codes WHERE user_id = ?");
            $stmt->execute([$reset['user_id']]);
            
            echo json_encode(['success' => true, 'message' => 'Senha redefinida com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Código inválido ou expirado']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados']);
    }
}
?>