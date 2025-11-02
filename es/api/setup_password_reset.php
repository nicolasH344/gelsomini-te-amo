<?php
require_once 'config.php';

echo "<!DOCTYPE html>
<html lang='pt-br'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Setup - Recuperação de Senha</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .warning { color: #856404; background: #fff3cd; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 4px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Configuração - Sistema de Recuperação de Senha</h1>";

try {
    // Verificar conexão com banco
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div class='success'>✅ Conexão com banco de dados estabelecida</div>";
    
    // Verificar se a tabela existe
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'password_reset_tokens'");
    
    if ($tableCheck->rowCount() > 0) {
        echo "<div class='info'>📊 Tabela password_reset_tokens já existe</div>";
        
        // Mostrar estrutura
        $structure = $pdo->query("DESCRIBE password_reset_tokens")->fetchAll(PDO::FETCH_ASSOC);
        echo "<h3>Estrutura da Tabela:</h3>
              <table>
                <tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Default</th><th>Extra</th></tr>";
        
        foreach ($structure as $column) {
            echo "<tr>
                    <td><strong>{$column['Field']}</strong></td>
                    <td>{$column['Type']}</td>
                    <td>{$column['Null']}</td>
                    <td>{$column['Key']}</td>
                    <td>{$column['Default']}</td>
                    <td>{$column['Extra']}</td>
                  </tr>";
        }
        echo "</table>";
        
    } else {
        echo "<div class='warning'>🔄 Criando tabela password_reset_tokens...</div>";
        
        // Criar tabela
        $sql = "CREATE TABLE password_reset_tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            token VARCHAR(6) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME NOT NULL,
            used TINYINT(1) DEFAULT 0,
            attempts TINYINT DEFAULT 0,
            ip_address VARCHAR(45) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        $pdo->exec($sql);
        echo "<div class='success'>✅ Tabela criada com sucesso!</div>";
        
        // Criar índices
        $pdo->exec("CREATE INDEX idx_email ON password_reset_tokens (email)");
        $pdo->exec("CREATE INDEX idx_token ON password_reset_tokens (token)");
        $pdo->exec("CREATE INDEX idx_expires ON password_reset_tokens (expires_at)");
        echo "<div class='success'>✅ Índices criados com sucesso!</div>";
    }
    
    // Teste prático
    echo "<h3>🧪 Teste do Sistema:</h3>";
    
    // Teste 1: Inserção
    $testEmail = "teste@localhost.com";
    $testToken = "123456";
    $expiresAt = date('Y-m-d H:i:s', time() + 3600);
    
    $stmt = $pdo->prepare("INSERT INTO password_reset_tokens (email, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$testEmail, $testToken, $expiresAt]);
    $testId = $pdo->lastInsertId();
    
    echo "<div class='success'>✅ Teste de inserção: OK (ID: $testId)</div>";
    
    // Teste 2: Leitura
    $stmt = $pdo->prepare("SELECT * FROM password_reset_tokens WHERE id = ?");
    $stmt->execute([$testId]);
    $testData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($testData) {
        echo "<div class='success'>✅ Teste de leitura: OK</div>";
        echo "<div class='info'>
                <strong>Dados do teste:</strong><br>
                Email: {$testData['email']}<br>
                Token: {$testData['token']}<br>
                Expira: {$testData['expires_at']}
              </div>";
    }
    
    // Teste 3: Limpeza
    $pdo->exec("DELETE FROM password_reset_tokens WHERE id = $testId");
    echo "<div class='success'>✅ Teste de limpeza: OK</div>";
    
    // Verificar tokens expirados
    $expiredCount = $pdo->query("SELECT COUNT(*) as c FROM password_reset_tokens WHERE expires_at < NOW()")->fetch(PDO::FETCH_ASSOC);
    echo "<div class='info'>🗑️ Tokens expirados no sistema: {$expiredCount['c']}</div>";
    
    echo "<div class='success' style='font-size: 1.2em; font-weight: bold;'>
            🎉 Sistema configurado com sucesso!
          </div>";
    
    echo "<div class='info'>
            <h4>📝 Próximos passos:</h4>
            <ol>
                <li>Acesse <a href='forgot_password.php'>forgot_password.php</a> para testar</li>
                <li>Verifique o log de erros do PHP para ver os códigos</li>
                <li>Implemente o envio real de email quando for para produção</li>
            </ol>
          </div>";

} catch (PDOException $e) {
    echo "<div class='error'>❌ Erro de banco de dados: " . $e->getMessage() . "</div>";
    echo "<div class='info'>💡 Dica: Verifique se o usuário do MySQL tem permissões para criar tabelas</div>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Erro: " . $e->getMessage() . "</div>";
}

echo "</div></body></html>";
?>