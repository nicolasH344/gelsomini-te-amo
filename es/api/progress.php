<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../../src/autoload.php';

use App\Models\Exercise;
use App\Models\Tutorial;
use App\Models\User;

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

$userId = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Dados inválidos');
        }
        
        if (isset($input['exercise_id'])) {
            $exerciseModel = new Exercise();
            $result = $exerciseModel->updateProgress(
                $userId,
                $input['exercise_id'],
                $input['status'] ?? 'started',
                $input['score'] ?? 0,
                $input['time_spent'] ?? 0
            );
            
            if ($result) {
                $userModel = new User();
                $newBadges = $userModel->checkAndAwardBadges($userId);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Progresso atualizado',
                    'new_badges' => $newBadges
                ]);
            } else {
                throw new Exception('Erro ao atualizar progresso');
            }
            
        } elseif (isset($input['tutorial_id'])) {
            $tutorialModel = new Tutorial();
            $result = $tutorialModel->markAsRead($userId, $input['tutorial_id']);
            
            if ($result) {
                $userModel = new User();
                $newBadges = $userModel->checkAndAwardBadges($userId);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Tutorial concluído',
                    'new_badges' => $newBadges
                ]);
            } else {
                throw new Exception('Erro ao marcar tutorial');
            }
        }
        
    } elseif ($method === 'GET') {
        $userModel = new User();
        $progress = $userModel->getProgress($userId);
        $badges = $userModel->getBadges($userId);
        
        echo json_encode([
            'success' => true,
            'progress' => $progress,
            'badges' => $badges
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}