<?php
require_once 'config.php';

try {
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception('Não foi possível conectar ao banco de dados');
    }
    
    echo "<h2>Configurando banco de dados...</h2>";
    
    // Criar tabelas necessárias
    $tables = [
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            is_admin BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS forum_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS forum_posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            category_id INT,
            user_id INT NOT NULL,
            title VARCHAR(200) NOT NULL,
            content TEXT NOT NULL,
            views INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES forum_categories(id),
            FOREIGN KEY (user_id) REFERENCES users(id)
        )",
        
        "CREATE TABLE IF NOT EXISTS forum_comments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            post_id INT NOT NULL,
            user_id INT NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (post_id) REFERENCES forum_posts(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )"
    ];
    
    foreach ($tables as $sql) {
        $conn->exec($sql);
        echo "✓ Tabela criada/verificada<br>";
    }
    
    // Inserir dados de teste
    $conn->exec("INSERT IGNORE INTO users (username, email, password_hash, first_name, last_name, is_admin) VALUES 
        ('admin', 'admin@test.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'Admin', 'Sistema', TRUE),
        ('usuario', 'user@test.com', '" . password_hash('123456', PASSWORD_DEFAULT) . "', 'Usuário', 'Teste', FALSE)");
    
    // Criar tabelas para novas funcionalidades
    $conn->exec("CREATE TABLE IF NOT EXISTS badges (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        icon VARCHAR(50),
        condition_type VARCHAR(50),
        condition_value INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $conn->exec("CREATE TABLE IF NOT EXISTS user_badges (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        badge_id INT NOT NULL,
        earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_badge (user_id, badge_id)
    )");
    
    // Inserir badges padrão
    $conn->exec("INSERT IGNORE INTO badges (id, name, description, icon, condition_type, condition_value) VALUES 
        (1, 'Primeiro Passo', 'Complete seu primeiro exercício', 'fas fa-baby', 'first_exercise', 1),
        (2, 'Mestre dos Exercícios', 'Complete 10 exercícios', 'fas fa-graduation-cap', 'exercise_count', 10),
        (3, 'Contribuidor do Fórum', 'Crie 5 posts no fórum', 'fas fa-comments', 'forum_posts', 5),
        (4, 'Sequência de 7 Dias', 'Complete exercícios por 7 dias seguidos', 'fas fa-fire', 'streak_days', 7),
        (5, 'Perfeccionista', 'Obtenha nota 100 em 5 exercícios', 'fas fa-star', 'perfect_scores', 5)");
    
    $conn->exec("INSERT IGNORE INTO forum_categories (id, name, description) VALUES 
        (1, 'HTML', 'Discussões sobre HTML'),
        (2, 'CSS', 'Discussões sobre CSS'),
        (3, 'JavaScript', 'Discussões sobre JavaScript'),
        (4, 'PHP', 'Discussões sobre PHP')");
    
    $conn->exec("INSERT IGNORE INTO forum_posts (id, category_id, user_id, title, content, views) VALUES 
        (1, 1, 2, 'Como começar com HTML?', 'Estou começando agora e gostaria de dicas sobre por onde começar com HTML...', 120),
        (2, 2, 2, 'Dúvida sobre CSS Grid', 'Estou tentando criar um layout com CSS Grid mas não consigo alinhar os itens...', 85),
        (3, 3, 2, 'JavaScript assíncrono', 'Alguém pode explicar melhor como funciona async/await em JavaScript?', 200)");
    
    echo "<br><strong>✅ Banco de dados configurado com sucesso!</strong><br>";
    echo "<br>Usuários de teste criados:<br>";
    echo "- Admin: admin / admin123<br>";
    echo "- Usuário: usuario / 123456<br>";
    
} catch (Exception $e) {
    echo "<strong>❌ Erro:</strong> " . $e->getMessage();
}
?>