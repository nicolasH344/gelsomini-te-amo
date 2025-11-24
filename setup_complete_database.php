<?php
// Setup completo do banco de dados Cursinho
require_once 'src/Config/Environment.php';

Environment::load();

$host = Environment::get('DB_HOST', 'localhost');
$dbname = Environment::get('DB_NAME', 'cursinho');
$username = Environment::get('DB_USER', 'root');
$password = Environment::get('DB_PASS', '');

try {
    // Conectar ao MySQL (sem especificar database)
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Conectado ao MySQL com sucesso!\n";
    
    // Ler e executar o schema SQL
    $sqlFile = __DIR__ . '/database_schema.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("Arquivo database_schema.sql não encontrado!");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Executar o SQL
    $pdo->exec($sql);
    
    echo "Banco de dados 'cursinho' criado com sucesso!\n";
    echo "Tabelas criadas:\n";
    echo "- users (usuários)\n";
    echo "- categories (categorias)\n";
    echo "- exercises (exercícios)\n";
    echo "- tutorials (tutoriais)\n";
    echo "- user_progress (progresso dos usuários)\n";
    echo "- badges (conquistas)\n";
    echo "- user_badges (conquistas dos usuários)\n";
    echo "- forum_categories (categorias do fórum)\n";
    echo "- forum_posts (posts do fórum)\n";
    echo "- forum_comments (comentários do fórum)\n";
    echo "- chat_rooms (salas de chat)\n";
    echo "- chat_messages (mensagens do chat)\n";
    echo "- user_sessions (sessões de usuários)\n";
    echo "- collaborative_sessions (sessões colaborativas)\n";
    echo "- collaborative_participants (participantes das sessões)\n";
    echo "\nDados iniciais inseridos:\n";
    echo "- Usuário admin (admin/admin123)\n";
    echo "- 8 categorias de programação\n";
    echo "- 6 categorias do fórum\n";
    echo "- 3 salas de chat\n";
    echo "- 9 badges do sistema\n";
    echo "- 3 exercícios básicos\n";
    echo "- 3 tutoriais básicos\n";
    echo "\nSetup concluído! O sistema está pronto para uso.\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
}
?>