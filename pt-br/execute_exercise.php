<?php
session_start();
require_once 'config.php';
require_once 'achievements_system.php';
require_once 'learning_system.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit;
}

$user = getCurrentUser();
$user_id = $user['id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$exercise_id = (int)($input['exercise_id'] ?? 0);
$user_code = $input['user_code'] ?? '';
$quick_complete = $input['quick_complete'] ?? false;
$hints_used = (int)($input['hints_used'] ?? 0);
$start_time = $input['start_time'] ?? time();
$time_spent = time() - $start_time;

if (!$exercise_id || (!$user_code && !$quick_complete)) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

try {
    require_once 'database.php';
    $db = new Database();
    $achievementsSystem = new AchievementsSystem($db);
    $learningSystem = new LearningSystem($db);
    
    // Simular execução do código com metodologia adaptativa
    $exercise_data = $learningSystem->getExerciseWithMethodology($exercise_id, $user_id);
    
    $success_rate = $quick_complete ? 1.0 : (strlen($user_code) > 20 ? 0.9 : 0.7);
    $is_success = $quick_complete || (rand(1, 100) <= ($success_rate * 100));
    
    $execution_result = [
        'success' => $is_success,
        'output' => $is_success ? 
            "✓ Código executado com sucesso!\n✓ Todos os testes passaram!" :
            "✗ Alguns testes falharam\n✓ Sintaxe correta\n✗ Lógica precisa ser ajustada",
        'execution_time' => '0.45s',
        'memory_used' => '2.1MB',
        'score' => $is_success ? rand(80, 100) : rand(30, 60)
    ];
    
    // Simular testes
    $test_results = [
        ['name' => 'Teste de Sintaxe', 'passed' => true, 'message' => 'Código compilou sem erros'],
        ['name' => 'Teste de Saída', 'passed' => true, 'message' => 'Saída esperada encontrada'],
        ['name' => 'Teste de Performance', 'passed' => true, 'message' => 'Dentro dos limites de tempo'],
        ['name' => 'Teste de Casos Extremos', 'passed' => rand(0, 1) == 1, 'message' => 'Verificação de casos extremos']
    ];
    
    // Atualizar progresso adaptativo
    $mastery_score = $learningSystem->updateProgress($user_id, $exercise_id, $is_success, $time_spent, $hints_used);
    
    // Completar exercício e verificar conquistas apenas se bem-sucedido
    $newAchievements = [];
    if ($is_success) {
        $newAchievements = $achievementsSystem->completeExercise($user_id, $exercise_id, $execution_result['score']);
    }
    
    // Obter moedas atuais
    $totalCoins = $achievementsSystem->getUserCoins($user_id);
    
    $response = [
        'success' => true,
        'execution_result' => $execution_result,
        'test_results' => $test_results,
        'new_achievements' => $newAchievements,
        'total_coins' => $totalCoins,
        'coins_earned' => array_sum(array_column($newAchievements, 'coins_reward')),
        'mastery_score' => $mastery_score
    ];
    
    // Adicionar feedback de aprendizado
    if ($mastery_score < 0.5) {
        $response['learning_suggestion'] = 'Tente revisar os conceitos básicos antes de continuar.';
    } elseif ($mastery_score < 0.8) {
        $response['learning_suggestion'] = 'Bom progresso! Pratique mais para dominar completamente.';
    } else {
        $response['learning_suggestion'] = 'Excelente! Você dominou este conceito.';
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno: ' . $e->getMessage()
    ]);
}
?>