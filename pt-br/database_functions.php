<?php
// Funções integradas com o banco de dados real

require_once __DIR__ . '/../src/autoload.php';

use App\Models\Exercise;
use App\Models\Tutorial;
use App\Models\Forum;
use App\Models\Chat;
use App\Models\User;

// Substituir funções simuladas por funções reais do banco

function processLogin($username, $password) {
    try {
        $userModel = new User();
        $user = $userModel->authenticate($username, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            return ['success' => true, 'message' => 'Login realizado com sucesso!'];
        }
        
        return ['success' => false, 'message' => 'Usuário ou senha incorretos.'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Erro no sistema: ' . $e->getMessage()];
    }
}

function processRegister($data) {
    try {
        // Validações
        if (empty($data['first_name']) || empty($data['last_name']) || 
            empty($data['username']) || empty($data['email']) || 
            empty($data['password'])) {
            return ['success' => false, 'message' => 'Preencha todos os campos obrigatórios'];
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email inválido'];
        }
        
        if (strlen($data['password']) < 6) {
            return ['success' => false, 'message' => 'A senha deve ter no mínimo 6 caracteres'];
        }
        
        $userModel = new User();
        $result = $userModel->register($data);
        
        if ($result) {
            return ['success' => true, 'message' => 'Conta criada com sucesso!'];
        }
        
        return ['success' => false, 'message' => 'Erro ao criar conta'];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
    }
}

function getStats() {
    try {
        $conn = new mysqli("localhost", "root", "momohiki", "Aims-sub2");
        
        if ($conn->connect_error) {
            throw new Exception("Erro de conexão: " . $conn->connect_error);
        }
        
        // Contar usuários ativos
        $userCount = 0;
        $stmt = $conn->query("SELECT COUNT(*) as total FROM users WHERE is_active = 1");
        if ($stmt) {
            $userResult = $stmt->fetch_assoc();
            $userCount = $userResult ? $userResult['total'] : 0;
        }
        
        // Contar exercícios (usar dados simulados se tabela não existir)
        $exerciseCount = 85;
        $stmt = $conn->query("SELECT COUNT(*) as total FROM exercises WHERE is_active = 1");
        if ($stmt) {
            $exerciseResult = $stmt->fetch_assoc();
            $exerciseCount = $exerciseResult ? $exerciseResult['total'] : 85;
        }
        
        // Contar tutoriais (usar dados simulados se tabela não existir)
        $tutorialCount = 42;
        $stmt = $conn->query("SELECT COUNT(*) as total FROM tutorials WHERE is_active = 1");
        if ($stmt) {
            $tutorialResult = $stmt->fetch_assoc();
            $tutorialCount = $tutorialResult ? $tutorialResult['total'] : 42;
        }
        
        // Contar posts do fórum (usar dados simulados se tabela não existir)
        $forumCount = 3680;
        $stmt = $conn->query("SELECT COUNT(*) as total FROM forum_posts");
        if ($stmt) {
            $forumResult = $stmt->fetch_assoc();
            $forumCount = $forumResult ? $forumResult['total'] : 3680;
        }
        
        $conn->close();
        
        return [
            'total_users' => $userCount,
            'total_exercises' => $exerciseCount,
            'total_tutorials' => $tutorialCount,
            'total_forum_posts' => $forumCount
        ];
        
    } catch (Exception $e) {
        // Fallback para dados simulados
        return [
            'total_users' => 1250,
            'total_exercises' => 85,
            'total_tutorials' => 42,
            'total_forum_posts' => 3680
        ];
    }
}

// Funções do fórum movidas para forum_functions.php para evitar duplicação

// Função getExercises movida para exercise_functions.php para evitar duplicação

// Função getTutorials movida para tutorial_functions.php para evitar duplicação

function getUserProgress($userId) {
    try {
        $userModel = new User();
        return $userModel->getProgress($userId);
    } catch (Exception $e) {
        return [
            'exercises_completed' => 0,
            'tutorials_completed' => 0,
            'total_points' => 0,
            'badges_earned' => 0
        ];
    }
}

function getUserBadges($userId) {
    try {
        $userModel = new User();
        return $userModel->getBadges($userId);
    } catch (Exception $e) {
        return [];
    }
}