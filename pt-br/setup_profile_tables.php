<?php
/**
 * Script para criar/atualizar tabelas necessárias para o sistema de perfil
 * Execute este arquivo apenas uma vez ou quando precisar resetar as tabelas
 */

require_once 'config.php';

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Setup - Sistema de Perfil</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        h1 { color: #4361ee; }
        .success { color: #28a745; padding: 10px; background: #d4edda; border-radius: 5px; margin: 10px 0; }
        .error { color: #dc3545; padding: 10px; background: #f8d7da; border-radius: 5px; margin: 10px 0; }
        .info { color: #0c5460; padding: 10px; background: #d1ecf1; border-radius: 5px; margin: 10px 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔧 Configuração do Sistema de Perfil</h1>
";

$conn = getDBConnection();

if (!$conn) {
    echo "<div class='error'>❌ Erro ao conectar com o banco de dados!</div>";
    echo "<p>Verifique as configurações em <code>config.php</code></p>";
    exit;
}

echo "<div class='info'>✅ Conexão com banco de dados estabelecida</div>";

try {
    // 1. Adicionar colunas de perfil à tabela users
    echo "<h2>1. Atualizando tabela 'users'</h2>";
    
    $columns = [
        "avatar VARCHAR(255) NULL COMMENT 'Caminho do arquivo de avatar'",
        "bio TEXT NULL COMMENT 'Biografia do usuário'",
        "website VARCHAR(255) NULL COMMENT 'Website pessoal'",
        "theme VARCHAR(50) DEFAULT 'light' COMMENT 'Tema preferido'",
        "language VARCHAR(10) DEFAULT 'pt' COMMENT 'Idioma preferido'",
        "notifications BOOLEAN DEFAULT 1 COMMENT 'Receber notificações'",
        "newsletter BOOLEAN DEFAULT 0 COMMENT 'Receber newsletter'"
    ];
    
    foreach ($columns as $column) {
        $columnName = explode(' ', $column)[0];
        try {
            // Verificar se coluna existe (compatível com MySQL 5.x e 8.x)
            $check = $conn->query("SHOW COLUMNS FROM users LIKE '$columnName'");
            if ($check->rowCount() == 0) {
                $conn->exec("ALTER TABLE users ADD COLUMN $column");
                echo "<div class='success'>✓ Coluna '$columnName' adicionada</div>";
            } else {
                echo "<div class='info'>ℹ Coluna '$columnName' já existe</div>";
            }
        } catch (PDOException $e) {
            echo "<div class='error'>⚠ Erro ao processar coluna '$columnName': " . $e->getMessage() . "</div>";
        }
    }
    
    // 2. Criar tabela tutorial_progress
    echo "<h2>2. Criando tabela 'tutorial_progress'</h2>";
    
    $conn->exec("
        CREATE TABLE IF NOT EXISTS tutorial_progress (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            tutorial_id INT NOT NULL,
            progress INT DEFAULT 0 COMMENT 'Progresso de 0 a 100',
            completed_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_tutorial (user_id, tutorial_id),
            INDEX idx_user_id (user_id),
            INDEX idx_tutorial_id (tutorial_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<div class='success'>✓ Tabela 'tutorial_progress' criada/verificada</div>";
    
    // 3. Criar tabela badges
    echo "<h2>3. Criando tabela 'badges'</h2>";
    
    $conn->exec("
        CREATE TABLE IF NOT EXISTS badges (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            icon VARCHAR(50) DEFAULT 'fas fa-award',
            color VARCHAR(20) DEFAULT 'primary',
            criteria_type VARCHAR(50) COMMENT 'exercises, tutorials, forum, etc',
            criteria_value INT COMMENT 'Valor necessário para conquistar',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<div class='success'>✓ Tabela 'badges' criada/verificada</div>";
    
    // 4. Criar tabela user_badges
    echo "<h2>4. Criando tabela 'user_badges'</h2>";
    
    $conn->exec("
        CREATE TABLE IF NOT EXISTS user_badges (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            badge_id INT NOT NULL,
            earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_badge (user_id, badge_id),
            INDEX idx_user_id (user_id),
            INDEX idx_badge_id (badge_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<div class='success'>✓ Tabela 'user_badges' criada/verificada</div>";
    
    // 5. Inserir badges padrão
    echo "<h2>5. Inserindo badges padrão</h2>";
    
    $check = $conn->query("SELECT COUNT(*) FROM badges")->fetchColumn();
    if ($check == 0) {
        $default_badges = [
            ['Iniciante', 'Complete seu primeiro exercício', 'fas fa-seedling', 'success', 'exercises', 1],
            ['Curioso', 'Visualize 5 tutoriais', 'fas fa-question', 'info', 'tutorials', 5],
            ['Persistente', 'Complete 10 exercícios', 'fas fa-trophy', 'warning', 'exercises', 10],
            ['Colaborador', 'Faça 5 posts no fórum', 'fas fa-hands-helping', 'primary', 'forum', 5],
            ['Dedicado', 'Complete 25 exercícios', 'fas fa-star', 'info', 'exercises', 25],
            ['Mestre', 'Complete 50 exercícios', 'fas fa-crown', 'danger', 'exercises', 50],
            ['Lenda', 'Complete 100 exercícios', 'fas fa-fire', 'dark', 'exercises', 100]
        ];
        
        $stmt = $conn->prepare("INSERT INTO badges (name, description, icon, color, criteria_type, criteria_value) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($default_badges as $badge) {
            $stmt->execute($badge);
            echo "<div class='success'>✓ Badge '{$badge[0]}' criado</div>";
        }
    } else {
        echo "<div class='info'>ℹ Badges já existem no banco ($check badges encontrados)</div>";
    }
    
    // 6. Criar diretório de uploads
    echo "<h2>6. Criando diretório de uploads</h2>";
    
    $upload_dir = __DIR__ . '/uploads/avatars/';
    if (!is_dir($upload_dir)) {
        if (mkdir($upload_dir, 0755, true)) {
            echo "<div class='success'>✓ Diretório criado: $upload_dir</div>";
        } else {
            echo "<div class='error'>❌ Erro ao criar diretório: $upload_dir</div>";
        }
    } else {
        echo "<div class='info'>ℹ Diretório já existe: $upload_dir</div>";
    }
    
    // 7. Verificar permissões
    if (is_writable($upload_dir)) {
        echo "<div class='success'>✓ Diretório de uploads está gravável</div>";
    } else {
        echo "<div class='error'>⚠ Diretório não está gravável. Execute: <code>chmod 755 $upload_dir</code></div>";
    }
    
    echo "<hr>";
    echo "<div class='success'><h2>✅ Configuração concluída com sucesso!</h2></div>";
    echo "<p>O sistema de perfil está pronto para uso. Você pode:</p>";
    echo "<ul>";
    echo "<li>✓ Fazer upload de fotos de perfil</li>";
    echo "<li>✓ Ver estatísticas reais baseadas no banco de dados</li>";
    echo "<li>✓ Ganhar badges automaticamente conforme progride</li>";
    echo "<li>✓ Personalizar biografia e website</li>";
    echo "</ul>";
    echo "<p><a href='profile.php' style='display: inline-block; padding: 10px 20px; background: #4361ee; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px;'>Ir para Meu Perfil</a></p>";
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ Erro: " . $e->getMessage() . "</div>";
}

echo "</body></html>";
?>
