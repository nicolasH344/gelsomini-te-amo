<?php
// Script para corrigir problemas do banco de dados
require_once 'config.php';

echo "<h2>Verificando e corrigindo banco de dados...</h2>";

try {
    $conn = getDBConnection();
    
    if (!$conn) {
        throw new Exception("Não foi possível conectar ao banco de dados");
    }
    
    echo "<p>✅ Conexão com banco estabelecida</p>";
    
    // Verificar se as tabelas existem
    $tables_to_check = ['users', 'categories', 'exercises', 'tutorials', 'forum_posts', 'forum_categories'];
    
    foreach ($tables_to_check as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows == 0) {
            echo "<p>❌ Tabela '$table' não existe</p>";
            
            // Criar tabelas básicas se não existirem
            switch ($table) {
                case 'users':
                    $sql = "CREATE TABLE `users` (
                        `id` INT NOT NULL AUTO_INCREMENT,
                        `username` VARCHAR(50) NOT NULL,
                        `email` VARCHAR(100) NOT NULL,
                        `password_hash` VARCHAR(255) NOT NULL,
                        `first_name` VARCHAR(50) NOT NULL,
                        `last_name` VARCHAR(50) NOT NULL,
                        `is_admin` TINYINT(1) NOT NULL DEFAULT 0,
                        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (`id`),
                        UNIQUE INDEX `username_UNIQUE` (`username`),
                        UNIQUE INDEX `email_UNIQUE` (`email`)
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci";
                    break;
                    
                case 'categories':
                    $sql = "CREATE TABLE `categories` (
                        `id` INT NOT NULL AUTO_INCREMENT,
                        `name` VARCHAR(50) NOT NULL,
                        `slug` VARCHAR(50) NOT NULL,
                        `description` TEXT NULL DEFAULT NULL,
                        `color` VARCHAR(7) NOT NULL DEFAULT '#6c757d',
                        `icon` VARCHAR(50) NOT NULL DEFAULT 'fas fa-code',
                        `sort_order` INT NOT NULL DEFAULT 0,
                        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (`id`),
                        UNIQUE INDEX `slug_UNIQUE` (`slug`)
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci";
                    break;
                    
                case 'exercises':
                    $sql = "CREATE TABLE `exercises` (
                        `id` INT NOT NULL AUTO_INCREMENT,
                        `title` VARCHAR(200) NOT NULL,
                        `slug` VARCHAR(200) NOT NULL,
                        `description` TEXT NOT NULL,
                        `content` LONGTEXT NOT NULL,
                        `solution` LONGTEXT NULL DEFAULT NULL,
                        `difficulty` ENUM('iniciante', 'intermediario', 'avancado') NOT NULL,
                        `category_id` INT NOT NULL,
                        `points` INT NOT NULL DEFAULT 10,
                        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (`id`),
                        UNIQUE INDEX `slug_UNIQUE` (`slug`),
                        INDEX `category_idx` (`category_id`)
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci";
                    break;
                    
                case 'tutorials':
                    $sql = "CREATE TABLE `tutorials` (
                        `id` INT NOT NULL AUTO_INCREMENT,
                        `title` VARCHAR(200) NOT NULL,
                        `slug` VARCHAR(200) NOT NULL,
                        `description` TEXT NOT NULL,
                        `content` LONGTEXT NOT NULL,
                        `difficulty` ENUM('iniciante', 'intermediario', 'avancado') NOT NULL,
                        `category_id` INT NOT NULL,
                        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (`id`),
                        UNIQUE INDEX `slug_UNIQUE` (`slug`),
                        INDEX `category_idx` (`category_id`)
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci";
                    break;
                    
                case 'forum_categories':
                    $sql = "CREATE TABLE `forum_categories` (
                        `id` INT NOT NULL AUTO_INCREMENT,
                        `name` VARCHAR(100) NOT NULL,
                        `description` TEXT NULL DEFAULT NULL,
                        `color` VARCHAR(7) NOT NULL DEFAULT '#6c757d',
                        `sort_order` INT NOT NULL DEFAULT 0,
                        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (`id`)
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci";
                    break;
                    
                case 'forum_posts':
                    $sql = "CREATE TABLE `forum_posts` (
                        `id` INT NOT NULL AUTO_INCREMENT,
                        `title` VARCHAR(200) NOT NULL,
                        `content` TEXT NOT NULL,
                        `user_id` INT NOT NULL,
                        `category_id` INT NOT NULL,
                        `views` INT NOT NULL DEFAULT 0,
                        `likes` INT NOT NULL DEFAULT 0,
                        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (`id`),
                        INDEX `user_idx` (`user_id`),
                        INDEX `category_idx` (`category_id`)
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci";
                    break;
            }
            
            if (isset($sql) && $conn->query($sql)) {
                echo "<p>✅ Tabela '$table' criada com sucesso</p>";
            } else {
                echo "<p>❌ Erro ao criar tabela '$table': " . $conn->error . "</p>";
            }
        } else {
            echo "<p>✅ Tabela '$table' existe</p>";
        }
    }
    
    // Inserir dados básicos se necessário
    $result = $conn->query("SELECT COUNT(*) as count FROM categories");
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row['count'] == 0) {
            echo "<p>Inserindo categorias básicas...</p>";
            $categories = [
                ['HTML Básico', 'html-basico', 'Fundamentos de HTML', '#e34c26'],
                ['CSS Básico', 'css-basico', 'Fundamentos de CSS', '#1572b6'],
                ['JavaScript', 'javascript', 'Fundamentos de JavaScript', '#f7df1e'],
                ['PHP', 'php', 'Fundamentos de PHP', '#777bb4']
            ];
            
            foreach ($categories as $cat) {
                $stmt = $conn->prepare("INSERT INTO categories (name, slug, description, color) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $cat[0], $cat[1], $cat[2], $cat[3]);
                $stmt->execute();
            }
            echo "<p>✅ Categorias inseridas</p>";
        }
    }
    
    // Inserir categorias do fórum se necessário
    $result = $conn->query("SELECT COUNT(*) as count FROM forum_categories");
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row['count'] == 0) {
            echo "<p>Inserindo categorias do fórum...</p>";
            $forum_categories = [
                ['Geral', 'Discussões gerais sobre programação', '#6c757d'],
                ['HTML/CSS', 'Dúvidas sobre HTML e CSS', '#e34c26'],
                ['JavaScript', 'Discussões sobre JavaScript', '#f7df1e'],
                ['Backend', 'PHP, bancos de dados e backend', '#777bb4']
            ];
            
            foreach ($forum_categories as $cat) {
                $stmt = $conn->prepare("INSERT INTO forum_categories (name, description, color) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $cat[0], $cat[1], $cat[2]);
                $stmt->execute();
            }
            echo "<p>✅ Categorias do fórum inseridas</p>";
        }
    }
    
    echo "<h3>✅ Verificação concluída!</h3>";
    echo "<p><a href='index.php'>Voltar para o site</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}
?>