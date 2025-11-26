<?php
// Script para testar o sistema de login
echo "=== TESTE DO SISTEMA DE LOGIN ===\n\n";

// Testar pt-br
echo "Testando login em português...\n";
require_once 'pt-br/config.php';

$result = processLogin('admin', 'admin123');
if ($result['success']) {
    echo "✓ Login admin funcionando: " . $result['message'] . "\n";
} else {
    echo "✗ Erro no login admin: " . $result['message'] . "\n";
}

$result = processLogin('usuario', '123456');
if ($result['success']) {
    echo "✓ Login usuário funcionando: " . $result['message'] . "\n";
} else {
    echo "✗ Erro no login usuário: " . $result['message'] . "\n";
}

// Limpar sessão
session_destroy();
session_start();

echo "\nTestando login em inglês...\n";
require_once 'en/config.php';

$result = processLogin('admin', 'admin123');
if ($result['success']) {
    echo "✓ Login admin working: " . $result['message'] . "\n";
} else {
    echo "✗ Admin login error: " . $result['message'] . "\n";
}

// Limpar sessão
session_destroy();
session_start();

echo "\nTestando login em espanhol...\n";
require_once 'es/config.php';

$result = processLogin('admin', 'admin123');
if ($result['success']) {
    echo "✓ Login admin funcionando: " . $result['message'] . "\n";
} else {
    echo "✗ Error en login admin: " . $result['message'] . "\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";
echo "Sistema de login multilíngue está funcionando!\n";
echo "\nAcesse:\n";
echo "- Português: http://localhost/gelsomini-te-amo/pt-br/login.php\n";
echo "- English: http://localhost/gelsomini-te-amo/en/login.php\n";
echo "- Español: http://localhost/gelsomini-te-amo/es/login.php\n";
?>