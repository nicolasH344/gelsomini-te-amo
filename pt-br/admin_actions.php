<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$conn = getDBConnection();
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'notify_all':
        $message = $_POST['message'] ?? '';
        if (empty($message)) {
            echo json_encode(['success' => false, 'message' => 'Mensagem vazia']);
            break;
        }
        
        // Buscar todos os usuários
        $result = $conn->query("SELECT id FROM users WHERE id != " . getCurrentUser()['id']);
        $count = 0;
        
        if ($result) {
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'info')");
            $title = "Aviso do Administrador";
            
            while ($user = $result->fetch_assoc()) {
                $stmt->bind_param("iss", $user['id'], $title, $message);
                if ($stmt->execute()) $count++;
            }
        }
        
        echo json_encode(['success' => true, 'message' => "Notificação enviada para $count usuários"]);
        break;
        
    case 'create_sample_data':
        // Criar exercícios de exemplo
        $exercises = [
            ['HTML Básico', 'Crie uma página HTML simples', 1, 'beginner'],
            ['CSS Styling', 'Adicione estilos à sua página', 2, 'beginner'],
            ['JavaScript Interativo', 'Adicione interatividade', 3, 'intermediate'],
            ['PHP Backend', 'Crie um script PHP', 4, 'advanced']
        ];
        
        $stmt = $conn->prepare("INSERT IGNORE INTO exercises (title, description, category_id, difficulty, content) VALUES (?, ?, ?, ?, 'Conteúdo do exercício')");
        
        foreach ($exercises as $ex) {
            $stmt->bind_param("ssis", $ex[0], $ex[1], $ex[2], $ex[3]);
            $stmt->execute();
        }
        
        // Criar tutoriais de exemplo
        $tutorials = [
            ['Introdução ao HTML', 'Aprenda HTML do zero', 1, 'beginner'],
            ['CSS Avançado', 'Técnicas avançadas de CSS', 2, 'intermediate'],
            ['JavaScript Moderno', 'ES6+ e além', 3, 'advanced'],
            ['PHP para Web', 'Desenvolvimento web com PHP', 4, 'intermediate']
        ];
        
        $stmt = $conn->prepare("INSERT IGNORE INTO tutorials (title, description, category_id, difficulty, content) VALUES (?, ?, ?, ?, 'Conteúdo do tutorial')");
        
        foreach ($tutorials as $tut) {
            $stmt->bind_param("ssis", $tut[0], $tut[1], $tut[2], $tut[3]);
            $stmt->execute();
        }
        
        echo json_encode(['success' => true, 'message' => 'Dados de exemplo criados']);
        break;
        
    case 'clear_cache':
        // Simular limpeza de cache
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        
        // Limpar sessões antigas (opcional)
        $conn->query("DELETE FROM user_sessions WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
        
        echo json_encode(['success' => true, 'message' => 'Cache limpo']);
        break;
        
    case 'save_config':
        $config = json_decode($_POST['config'] ?? '{}', true);
        
        // Salvar configurações em arquivo ou banco
        $config_file = __DIR__ . '/admin_config.json';
        file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT));
        
        echo json_encode(['success' => true, 'message' => 'Configurações salvas']);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
}
?>