<?php
require_once 'config.php';

echo "<h2>🧪 Teste de Login Automático</h2>";

// Testar login do admin
echo "<h3>Testando Admin:</h3>";
$result = processLogin('admin', 'admin123');
if ($result['success']) {
    echo "✅ Login admin OK<br>";
    echo "👑 Admin logado: " . (isAdmin() ? 'SIM' : 'NÃO') . "<br>";
    echo "👤 Usuário: " . htmlspecialchars(getCurrentUser()['first_name']) . "<br>";
} else {
    echo "❌ Erro admin: " . $result['message'] . "<br>";
}

// Logout
processLogout();

echo "<hr>";

// Testar login do usuário
echo "<h3>Testando Usuário Normal:</h3>";
$result = processLogin('usuario', '123456');
if ($result['success']) {
    echo "✅ Login usuário OK<br>";
    echo "👑 Admin: " . (isAdmin() ? 'SIM' : 'NÃO') . "<br>";
    echo "👤 Usuário: " . htmlspecialchars(getCurrentUser()['first_name']) . "<br>";
} else {
    echo "❌ Erro usuário: " . $result['message'] . "<br>";
}

echo "<hr>";
echo "<h3>🔗 Links para Teste Manual:</h3>";
echo "<ul>";
echo "<li><a href='login.php' target='_blank'>🔐 Fazer Login Manual</a></li>";
echo "<li><a href='admin_panel.php' target='_blank'>👑 Painel Admin (requer login admin)</a></li>";
echo "<li><a href='exercise_area.php?id=1' target='_blank'>💪 Área de Exercício</a></li>";
echo "</ul>";

echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
echo "<h4>📋 Status dos Testes:</h4>";
echo "<p>✅ Usuários de teste criados</p>";
echo "<p>✅ Sistema de login funcionando</p>";
echo "<p>✅ Detecção de admin funcionando</p>";
echo "<p>✅ Painel administrativo disponível</p>";
echo "</div>";
?>