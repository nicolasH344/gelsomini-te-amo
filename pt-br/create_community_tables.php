<?php
/**
 * Script de criação de tabelas para o sistema de comunidade
 * Execute este arquivo uma vez para criar as tabelas necessárias
 */

require_once 'config.php';

try {
    $conn = getDBConnection();
    
    // Tabela de discussões
    $conn->exec("
        CREATE TABLE IF NOT EXISTS discussions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            content_type VARCHAR(20) NOT NULL,
            content_id INT NOT NULL,
            user_id INT NOT NULL,
            username VARCHAR(100) NOT NULL,
            message TEXT NOT NULL,
            created_at DATETIME NOT NULL,
            INDEX idx_content (content_type, content_id),
            INDEX idx_user (user_id),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Tabela de likes em discussões
    $conn->exec("
        CREATE TABLE IF NOT EXISTS discussion_likes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            discussion_id INT NOT NULL,
            user_id INT NOT NULL,
            created_at DATETIME NOT NULL,
            UNIQUE KEY unique_like (discussion_id, user_id),
            INDEX idx_discussion (discussion_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Tabela de respostas (replies)
    $conn->exec("
        CREATE TABLE IF NOT EXISTS discussion_replies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            discussion_id INT NOT NULL,
            user_id INT NOT NULL,
            username VARCHAR(100) NOT NULL,
            message TEXT NOT NULL,
            created_at DATETIME NOT NULL,
            INDEX idx_discussion (discussion_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Tabela de soluções da comunidade
    $conn->exec("
        CREATE TABLE IF NOT EXISTS community_solutions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            content_type VARCHAR(20) NOT NULL,
            content_id INT NOT NULL,
            user_id INT NOT NULL,
            username VARCHAR(100) NOT NULL,
            title VARCHAR(255) NOT NULL,
            code TEXT NOT NULL,
            language VARCHAR(50) DEFAULT 'javascript',
            created_at DATETIME NOT NULL,
            INDEX idx_content (content_type, content_id),
            INDEX idx_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Tabela de likes em soluções
    $conn->exec("
        CREATE TABLE IF NOT EXISTS solution_likes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            solution_id INT NOT NULL,
            user_id INT NOT NULL,
            created_at DATETIME NOT NULL,
            UNIQUE KEY unique_like (solution_id, user_id),
            INDEX idx_solution (solution_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    echo "✅ Tabelas criadas com sucesso!<br><br>";
    echo "Tabelas criadas:<br>";
    echo "- discussions (discussões)<br>";
    echo "- discussion_likes (curtidas em discussões)<br>";
    echo "- discussion_replies (respostas)<br>";
    echo "- community_solutions (soluções compartilhadas)<br>";
    echo "- solution_likes (curtidas em soluções)<br>";
    
} catch (PDOException $e) {
    echo "❌ Erro ao criar tabelas: " . $e->getMessage();
}
