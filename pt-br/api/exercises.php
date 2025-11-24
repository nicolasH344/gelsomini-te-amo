<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../../src/autoload.php';

use App\Models\Exercise;

session_start();

$method = $_SERVER['REQUEST_METHOD'];
$exerciseModel = new Exercise();

try {
    if ($method === 'GET') {
        $categoryId = $_GET['category'] ?? null;
        $difficulty = $_GET['difficulty'] ?? null;
        $withProgress = isset($_GET['with_progress']) && $_SESSION['user_id'] ?? false;
        
        if ($withProgress && isset($_SESSION['user_id'])) {
            $exercises = $exerciseModel->getWithProgress($_SESSION['user_id'], $categoryId, $difficulty);
        } else {
            $exercises = $exerciseModel->getByCategory($categoryId, $difficulty);
        }
        
        echo json_encode([
            'success' => true,
            'exercises' => $exercises
        ]);
        
    } elseif ($method === 'POST') {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('Usuário não autenticado');
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? '';
        
        switch ($action) {
            case 'submit_solution':
                $exerciseId = $input['exercise_id'];
                $userCode = $input['code'] ?? '';
                $timeSpent = $input['time_spent'] ?? 0;
                
                // Simple scoring system
                $score = 0;
                if (!empty($userCode)) {
                    $score = min(100, strlen($userCode) > 10 ? 80 : 50);
                }
                
                $result = $exerciseModel->updateProgress(
                    $_SESSION['user_id'],
                    $exerciseId,
                    'completed',
                    $score,
                    $timeSpent
                );
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Solução enviada com sucesso',
                        'score' => $score
                    ]);
                } else {
                    throw new Exception('Erro ao enviar solução');
                }
                break;
                
            case 'start_exercise':
                $exerciseId = $input['exercise_id'];
                
                $result = $exerciseModel->updateProgress(
                    $_SESSION['user_id'],
                    $exerciseId,
                    'started'
                );
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Exercício iniciado'
                    ]);
                } else {
                    throw new Exception('Erro ao iniciar exercício');
                }
                break;
        }
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}