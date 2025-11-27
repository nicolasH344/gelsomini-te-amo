<?php
// Script para configurar o sistema de aprendizado adaptativo
require_once 'config.php';
require_once 'learning_system.php';

try {
    require_once 'database.php';
    $db = new Database();
    
    // Inicializar sistema de aprendizado
    $learningSystem = new LearningSystem($db);
    
    // Adicionar metodologia aos exercícios existentes
    $exercises = [
        1 => ['type' => 'guided', 'adaptive' => true],
        2 => ['type' => 'challenge', 'adaptive' => true], 
        3 => ['type' => 'project', 'adaptive' => false],
        4 => ['type' => 'guided', 'adaptive' => true],
        5 => ['type' => 'challenge', 'adaptive' => true],
        6 => ['type' => 'quiz', 'adaptive' => false],
        7 => ['type' => 'project', 'adaptive' => true],
        8 => ['type' => 'debug', 'adaptive' => true],
        9 => ['type' => 'guided', 'adaptive' => true]
    ];
    
    foreach ($exercises as $exercise_id => $config) {
        $hints = json_encode([
            'hint1' => 'Leia atentamente o enunciado',
            'hint2' => 'Teste seu código passo a passo',
            'hint3' => 'Verifique a sintaxe e lógica'
        ]);
        
        $steps = json_encode([
            'step1' => 'Analise o problema',
            'step2' => 'Planeje a solução',
            'step3' => 'Implemente o código',
            'step4' => 'Teste e refine'
        ]);
        
        $mistakes = json_encode([
            'mistake1' => 'Esquecer de declarar variáveis',
            'mistake2' => 'Não tratar casos extremos',
            'mistake3' => 'Lógica incorreta em loops'
        ]);
        
        $criteria = json_encode([
            'syntax' => 'Código sem erros de sintaxe',
            'logic' => 'Lógica correta implementada',
            'efficiency' => 'Solução eficiente'
        ]);
        
        $stmt = $db->conn->prepare("
            INSERT INTO exercise_methodology (exercise_id, learning_type, hint_system, step_by_step, common_mistakes, success_criteria, adaptive_difficulty)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            learning_type = VALUES(learning_type),
            adaptive_difficulty = VALUES(adaptive_difficulty)
        ");
        
        $adaptive = $config['adaptive'] ? 1 : 0;
        $stmt->bind_param("isssssi", $exercise_id, $config['type'], $hints, $steps, $mistakes, $criteria, $adaptive);
        $stmt->execute();
        $stmt->close();
    }
    
    echo "✅ Sistema de aprendizado configurado com sucesso!\n";
    echo "📚 Metodologias aplicadas:\n";
    echo "   - Exercícios guiados (guided)\n";
    echo "   - Desafios práticos (challenge)\n";
    echo "   - Projetos completos (project)\n";
    echo "   - Quiz interativo (quiz)\n";
    echo "   - Depuração de código (debug)\n";
    echo "🎯 Recursos ativados:\n";
    echo "   - Dificuldade adaptativa\n";
    echo "   - Sistema de dicas progressivas\n";
    echo "   - Análise de maestria\n";
    echo "   - Recomendações personalizadas\n";
    
    $db->closeConnection();
    
} catch (Exception $e) {
    echo "❌ Erro ao configurar sistema: " . $e->getMessage() . "\n";
}
?>