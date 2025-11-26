<?php
// Script para criar tabelas que estão faltando no banco
echo "Criando tabelas necessárias...\n";

try {
    $conn = new mysqli("localhost", "root", "momohiki", "Aims-sub2");
    
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error . "\n");
    }
    
    echo "✓ Conectado ao banco Aims-sub2\n";
    
    // Criar tabela exercises se não existir
    $sql = "CREATE TABLE IF NOT EXISTS exercises (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        content LONGTEXT,
        category VARCHAR(100),
        difficulty ENUM('Iniciante', 'Intermediário', 'Avançado') DEFAULT 'Iniciante',
        points INT DEFAULT 10,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "✓ Tabela 'exercises' criada/verificada\n";
    } else {
        echo "Erro ao criar tabela exercises: " . $conn->error . "\n";
    }
    
    // Criar tabela user_progress se não existir
    $sql = "CREATE TABLE IF NOT EXISTS user_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        exercise_id INT NOT NULL,
        status ENUM('started', 'completed') DEFAULT 'started',
        score INT DEFAULT 0,
        completed_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_exercise (user_id, exercise_id)
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "✓ Tabela 'user_progress' criada/verificada\n";
    } else {
        echo "Erro ao criar tabela user_progress: " . $conn->error . "\n";
    }
    
    // Criar tabela tutorials se não existir
    $sql = "CREATE TABLE IF NOT EXISTS tutorials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        content LONGTEXT,
        category VARCHAR(100),
        status ENUM('Rascunho', 'Publicado') DEFAULT 'Publicado',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "✓ Tabela 'tutorials' criada/verificada\n";
    } else {
        echo "Erro ao criar tabela tutorials: " . $conn->error . "\n";
    }
    
    // Criar tabela tutorial_progress se não existir
    $sql = "CREATE TABLE IF NOT EXISTS tutorial_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        tutorial_id INT NOT NULL,
        status ENUM('started', 'completed') DEFAULT 'started',
        completed_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_tutorial (user_id, tutorial_id)
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "✓ Tabela 'tutorial_progress' criada/verificada\n";
    } else {
        echo "Erro ao criar tabela tutorial_progress: " . $conn->error . "\n";
    }
    
    // Criar tabela forum_posts se não existir
    $sql = "CREATE TABLE IF NOT EXISTS forum_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        category VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "✓ Tabela 'forum_posts' criada/verificada\n";
    } else {
        echo "Erro ao criar tabela forum_posts: " . $conn->error . "\n";
    }
    
    // Inserir alguns exercícios de exemplo se a tabela estiver vazia
    $result = $conn->query("SELECT COUNT(*) as count FROM exercises");
    $count = $result->fetch_assoc()['count'];
    
    if ($count == 0) {
        echo "Inserindo exercícios de exemplo...\n";
        
        $exercises = [
            ['HTML Básico', 'Crie uma página HTML simples', 'HTML', 'Iniciante', 10],
            ['CSS Styling', 'Aplique estilos CSS a elementos', 'CSS', 'Iniciante', 15],
            ['JavaScript Básico', 'Escreva funções JavaScript', 'JavaScript', 'Intermediário', 20],
            ['PHP Introdução', 'Crie scripts PHP básicos', 'PHP', 'Intermediário', 25]
        ];
        
        foreach ($exercises as $exercise) {
            $stmt = $conn->prepare("INSERT INTO exercises (title, description, category, difficulty, points) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $exercise[0], $exercise[1], $exercise[2], $exercise[3], $exercise[4]);
            $stmt->execute();
        }
        
        echo "✓ Exercícios de exemplo inseridos\n";
    }
    
    // Inserir alguns tutoriais de exemplo se a tabela estiver vazia
    $result = $conn->query("SELECT COUNT(*) as count FROM tutorials");
    $count = $result->fetch_assoc()['count'];
    
    if ($count == 0) {
        echo "Inserindo tutoriais de exemplo...\n";
        
        $tutorials = [
            ['Introdução ao HTML', 'Aprenda os fundamentos do HTML', 'HTML'],
            ['CSS para Iniciantes', 'Guia completo de CSS', 'CSS'],
            ['JavaScript Essencial', 'Conceitos básicos de JavaScript', 'JavaScript']
        ];
        
        foreach ($tutorials as $tutorial) {
            $stmt = $conn->prepare("INSERT INTO tutorials (title, description, category, status) VALUES (?, ?, ?, 'Publicado')");
            $stmt->bind_param("sss", $tutorial[0], $tutorial[1], $tutorial[2]);
            $stmt->execute();
        }
        
        echo "✓ Tutoriais de exemplo inseridos\n";
    }
    
    echo "\n=== CONFIGURAÇÃO CONCLUÍDA ===\n";
    echo "Todas as tabelas necessárias foram criadas!\n";
    echo "O sistema de perfil e progresso agora deve funcionar corretamente.\n";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>