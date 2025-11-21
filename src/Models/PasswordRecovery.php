<?php
require_once __DIR__ . '/../SecurityHelper.php';

class PasswordRecovery {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Adicionar perguntas de segurança ao usuário
    public function setSecurityQuestions($userId, $questions) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO user_security_questions (user_id, question_1, answer_1, question_2, answer_2, question_3, answer_3) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                question_1 = VALUES(question_1), answer_1 = VALUES(answer_1),
                question_2 = VALUES(question_2), answer_2 = VALUES(answer_2),
                question_3 = VALUES(question_3), answer_3 = VALUES(answer_3)
            ");
            
            return $stmt->execute([
                $userId,
                $questions['q1'], password_hash(strtolower(trim($questions['a1'])), PASSWORD_DEFAULT),
                $questions['q2'], password_hash(strtolower(trim($questions['a2'])), PASSWORD_DEFAULT),
                $questions['q3'], password_hash(strtolower(trim($questions['a3'])), PASSWORD_DEFAULT)
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Obter perguntas do usuário
    public function getSecurityQuestions($email) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u.id, u.username, sq.question_1, sq.question_2, sq.question_3 
                FROM users u 
                JOIN user_security_questions sq ON u.id = sq.user_id 
                WHERE u.email = ? AND u.is_active = 1
            ");
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Validar respostas e gerar palavra-chave
    public function validateAnswersAndGenerateKey($email, $answers) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u.id, u.username, sq.answer_1, sq.answer_2, sq.answer_3 
                FROM users u 
                JOIN user_security_questions sq ON u.id = sq.user_id 
                WHERE u.email = ? AND u.is_active = 1
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user) return false;
            
            // Verificar todas as respostas
            $valid1 = password_verify(strtolower(trim($answers['a1'])), $user['answer_1']);
            $valid2 = password_verify(strtolower(trim($answers['a2'])), $user['answer_2']);
            $valid3 = password_verify(strtolower(trim($answers['a3'])), $user['answer_3']);
            
            if ($valid1 && $valid2 && $valid3) {
                // Gerar palavra-chave temporária
                $recoveryKey = $this->generateRecoveryKey();
                $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                
                // Salvar chave temporária
                $stmt = $this->pdo->prepare("
                    INSERT INTO password_recovery_keys (user_id, recovery_key, expires_at) 
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    recovery_key = VALUES(recovery_key), 
                    expires_at = VALUES(expires_at),
                    used = 0
                ");
                
                if ($stmt->execute([$user['id'], password_hash($recoveryKey, PASSWORD_DEFAULT), $expiresAt])) {
                    return [
                        'success' => true,
                        'recovery_key' => $recoveryKey,
                        'username' => $user['username'],
                        'expires_in' => 15
                    ];
                }
            }
            
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Validar palavra-chave e permitir redefinição
    public function validateRecoveryKey($email, $recoveryKey) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u.id, u.username, prk.recovery_key, prk.expires_at, prk.used
                FROM users u 
                JOIN password_recovery_keys prk ON u.id = prk.user_id 
                WHERE u.email = ? AND u.is_active = 1
            ");
            $stmt->execute([$email]);
            $result = $stmt->fetch();
            
            if (!$result || $result['used'] == 1) return false;
            
            // Verificar se não expirou
            if (strtotime($result['expires_at']) < time()) {
                return ['error' => 'Palavra-chave expirada'];
            }
            
            // Verificar palavra-chave
            if (password_verify($recoveryKey, $result['recovery_key'])) {
                return [
                    'success' => true,
                    'user_id' => $result['id'],
                    'username' => $result['username']
                ];
            }
            
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Redefinir senha com palavra-chave
    public function resetPasswordWithKey($email, $recoveryKey, $newPassword) {
        try {
            $validation = $this->validateRecoveryKey($email, $recoveryKey);
            if (!$validation || !$validation['success']) {
                return $validation ?: false;
            }
            
            // Atualizar senha
            $stmt = $this->pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $passwordUpdated = $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $validation['user_id']]);
            
            if ($passwordUpdated) {
                // Marcar chave como usada
                $stmt = $this->pdo->prepare("UPDATE password_recovery_keys SET used = 1 WHERE user_id = ?");
                $stmt->execute([$validation['user_id']]);
                
                return ['success' => true, 'message' => 'Senha redefinida com sucesso'];
            }
            
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Gerar palavra-chave aleatória
    private function generateRecoveryKey() {
        $words = [
            'AURORA', 'BRISA', 'CRISTAL', 'DIAMANTE', 'ESTRELA', 'FLOR', 'GELO', 'HARMONIA',
            'INFINITO', 'JARDIM', 'LIBERDADE', 'MAGIA', 'NATUREZA', 'OCEANO', 'PAZ', 'QUIETUDE',
            'RAIO', 'SERENIDADE', 'TEMPESTADE', 'UNIVERSO', 'VITORIA', 'WISDOM', 'XADREZ', 'YOGA', 'ZENITH'
        ];
        
        $numbers = str_pad(random_int(100, 999), 3, '0', STR_PAD_LEFT);
        $word = $words[array_rand($words)];
        
        return $word . $numbers;
    }
    
    // Limpar chaves expiradas
    public function cleanExpiredKeys() {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM password_recovery_keys WHERE expires_at < NOW()");
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}