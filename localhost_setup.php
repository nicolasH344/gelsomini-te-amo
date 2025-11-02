<?php
echo "<h2>Configuração para Localhost</h2>";
echo "<p>Para usar o sistema no localhost, acesse:</p>";
echo "<ul>";
echo "<li><strong>Português:</strong> <a href='http://localhost/gelsomini-te-amo/pt-br/'>http://localhost/gelsomini-te-amo/pt-br/</a></li>";
echo "<li><strong>English:</strong> <a href='http://localhost/gelsomini-te-amo/en/'>http://localhost/gelsomini-te-amo/en/</a></li>";
echo "<li><strong>Español:</strong> <a href='http://localhost/gelsomini-te-amo/es/'>http://localhost/gelsomini-te-amo/es/</a></li>";
echo "<li><strong>Auto-detect:</strong> <a href='http://localhost/gelsomini-te-amo/'>http://localhost/gelsomini-te-amo/</a></li>";
echo "</ul>";

echo "<h3>Setup do Banco de Dados</h3>";
echo "<p>Execute o setup em qualquer idioma:</p>";
echo "<ul>";
echo "<li><a href='pt-br/setup_database.php'>Setup PT-BR</a></li>";
echo "<li><a href='en/setup_database.php'>Setup EN</a></li>";
echo "<li><a href='es/setup_database.php'>Setup ES</a></li>";
echo "</ul>";

echo "<h3>Usuários de Teste</h3>";
echo "<ul>";
echo "<li><strong>Admin:</strong> admin / admin123</li>";
echo "<li><strong>Usuário:</strong> usuario / 123456</li>";
echo "</ul>";

echo "<h3>Novas Funcionalidades</h3>";
echo "<ul>";
echo "<li><strong>Chat:</strong> <a href='pt-br/chat.php'>Chat em Tempo Real</a></li>";
echo "<li><strong>Colaboração:</strong> <a href='pt-br/collaborative_exercise.php?id=1'>Exercícios Colaborativos</a></li>";
echo "<li><strong>GitHub:</strong> <a href='pt-br/github_integration.php'>Integração GitHub</a></li>";
echo "<li><strong>Mentoria:</strong> <a href='pt-br/mentorship.php'>Sistema de Mentoria</a></li>";
echo "<li><strong>Conquistas:</strong> <a href='pt-br/badges.php'>Badges e Conquistas</a></li>";
echo "</ul>";

echo "<h3>APIs Disponíveis</h3>";
echo "<ul>";
echo "<li>Chat: <code>api/chat_messages.php</code></li>";
echo "<li>Usuários Online: <code>api/online_users.php</code></li>";
echo "<li>Código Colaborativo: <code>api/collaborative_save.php</code></li>";
echo "<li>Chat de Exercícios: <code>api/exercise_chat.php</code></li>";
echo "<li>Recuperação de Senha: <code>api/password_reset.php</code></li>";
echo "</ul>";
?>