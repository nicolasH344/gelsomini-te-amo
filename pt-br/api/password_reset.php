<?php
// Configurações para localhost
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permite todas as origens em localhost
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Para desenvolvimento local - mostrar erros
if ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

date_default_timezone_set('America/Sao_Paulo');

// Incluir configuração
require_once '../config.php';

class PasswordResetAPI {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Gerar código numérico (mais compatível)
    private function generateVerificationCode() {
        return sprintf("%06d", rand(1, 999999));
    }
    
    // Enviar email (simulação para localhost)
    private function sendVerificationEmail($email, $code) {
        // Em localhost, apenas logamos o código
        $logMessage = "
        🎯 RECUPERAÇÃO DE SENHA - LOCALHOST
        ⏰ " . date('d/m/Y H:i:s') . "
        📧 Email: {$email}
        🔑 Código: {$code}
        🌐 URL: http://{$_SERVER['HTTP_HOST']}/forgot_password.php
        --------------------------
        ";
        
        error_log($logMessage);
        
        // Também salva em arquivo para facilitar o desenvolvimento
        file_put_contents('password_codes.log', $logMessage, FILE_APPEND | LOCK_EX);
        
        return true;
    }
    
    // Solicitar código
    public function requestResetCode($email) {
        try {
            // Validação simples de email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Por favor, insira um email válido.'];
            }
            
            // Verificar se usuário existe (adapte conforme sua tabela users)
            $stmt = $this->pdo->prepare("SELECT id, email FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Por segurança, sempre retornar sucesso mesmo se email não existir
            if (!$user) {
                return ['success' => true, 'message' => 'Se o email estiver cadastrado, você receberá um código de verificação.'];
            }
            
            // Gerar token
            $token = $this->generateVerificationCode();
            $expiresAt = date('Y-m-d H:i:s', time() + (15 * 60)); // 15 minutos
            
            // Invalidar tokens anteriores
            $stmt = $this->pdo->prepare("UPDATE password_reset_tokens SET used = 1 WHERE email = ? AND used = 0");
            $stmt->execute([$email]);
            
            // Inserir novo token
            $stmt = $this->pdo->prepare("INSERT INTO password_reset_tokens (email, token, expires_at) VALUES (?, ?, ?)");
            $result = $stmt->execute([$email, $token, $expiresAt]);
            
            if ($result) {
                // Enviar email (simulação)
                $this->sendVerificationEmail($email, $token);
                
                return [
                    'success' => true, 
                    'message' => 'Código de verificação enviado! Verifique o arquivo password_codes.log no servidor.',
                    'debug_code' => $token // Apenas para desenvolvimento
                ];
            } else {
                return ['success' => false, 'message' => 'Erro ao gerar código.'];
            }
            
        } catch (PDOException $e) {
            error_log("API Error - requestResetCode: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro no servidor: ' . $e->getMessage()];
        }
    }
    
    // Verificar código
    public function verifyCode($email, $code) {
        try {
            // Limpar código - apenas números
            $code = preg_replace('/[^0-9]/', '', $code);
            
            if (strlen($code) !== 6) {
                return ['success' => false, 'message' => 'Código deve conter 6 dígitos.'];
            }
            
            $stmt = $this->pdo->prepare("
                SELECT id, expires_at 
                FROM password_reset_tokens 
                WHERE email = ? AND token = ? AND used = 0 
                ORDER BY created_at DESC 
                LIMIT 1
            ");
            
            $stmt->execute([$email, $code]);
            $token = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$token) {
                return ['success' => false, 'message' => 'Código inválido.'];
            }
            
            // Verificar expiração
            if (strtotime($token['expires_at']) < time()) {
                return ['success' => false, 'message' => 'Código expirado.'];
            }
            
            return ['success' => true, 'message' => 'Código verificado com sucesso!'];
            
        } catch (PDOException $e) {
            error_log("API Error - verifyCode: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro no servidor.'];
        }
    }
    
    // Redefinir senha
    public function resetPassword($email, $code, $password) {
        try {
            // Verificar código primeiro
            $verifyResult = $this->verifyCode($email, $code);
            if (!$verifyResult['success']) {
                return $verifyResult;
            }
            
            // Validar senha
            if (strlen($password) < 6) {
                return ['success' => false, 'message' => 'A senha deve ter pelo menos 6 caracteres.'];
            }
            
            // Hash da senha
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Atualizar senha do usuário
            $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$hashedPassword, $email]);
            
            if ($stmt->rowCount() > 0) {
                // Marcar token como usado
                $stmt = $this->pdo->prepare("UPDATE password_reset_tokens SET used = 1 WHERE email = ? AND token = ?");
                $stmt->execute([$email, $code]);
                
                return ['success' => true, 'message' => 'Senha redefinida com sucesso!'];
            } else {
                return ['success' => false, 'message' => 'Usuário não encontrado.'];
            }
            
        } catch (PDOException $e) {
            error_log("API Error - resetPassword: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro no servidor.'];
        }
    }
}

// Processar requisição
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Dados inválidos');
        }
        
        $action = $input['action'] ?? '';
        $email = $input['email'] ?? '';
        $code = $input['code'] ?? '';
        $password = $input['password'] ?? '';
        
        $api = new PasswordResetAPI($pdo);
        
        switch ($action) {
            case 'request_code':
                $response = $api->requestResetCode($email);
                break;
                
            case 'verify_code':
                $response = $api->verifyCode($email, $code);
                break;
                
            case 'reset_password':
                $response = $api->resetPassword($email, $code, $password);
                break;
                
            default:
                $response = ['success' => false, 'message' => 'Ação não reconhecida.'];
        }
        
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido. Use POST.']);
}
?>