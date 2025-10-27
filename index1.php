<?php
// Define o nome da pasta base onde estarão os diretórios de idioma.
$project_dir_name = 'aims-sub2/';

// --- NOVO: Obtém o caminho base do projeto ---
// Esta variável vai capturar a parte da URL antes de "aims-sub2".
// Exemplo: Se a URL for /projetos/2025_dev/aims-sub2/, o $base_path será /projetos/2025_dev/
$script_name = $_SERVER['SCRIPT_NAME']; // Exemplo: /projetos/2025_dev/aims-sub2/index.php

// Remove o nome do arquivo 'index.php' e o nome da pasta do projeto para obter o caminho base.
$base_path = str_replace($project_dir_name . basename(__FILE__), '', $script_name);
// Garante que o caminho base termine com a pasta do projeto.
$full_project_path = rtrim($base_path, '/') . '/' . $project_dir_name;

// --- 1. DETECÇÃO DO IDIOMA (MANTIDO) ---
if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $browser_langs = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
} else {
    $browser_langs = 'en-US,en;q=0.9';
}

$lang_codes = explode(',', $browser_langs);
$primary_lang = strtolower(trim(substr($lang_codes[0], 0, 5)));
$parts = explode('-', $primary_lang);
$main_lang = $parts[0];

$target_folder = '';

// --- 2. MAPEAMENTO E TRATAMENTO DA LÍNGUA (MANTIDO) ---

// **IMPORTANTE:** Para que esta lógica funcione corretamente, suas pastas de idioma
// (ex: 'pt-br', 'en-us') devem estar dentro da pasta 'aims-sub2/' no servidor.

// Lógica de Redirecionamento (simulada para o exemplo):
if (strpos($primary_lang, 'pt') !== false) {
    $target_folder = 'pt-br';
} elseif (strpos($primary_lang, 'es') !== false) {
    $target_folder = 'es';
} else {
    // Idioma Padrão (Fallback)
    $target_folder = 'en-us';
}


// --- 3. EXCLUSÃO DE COOKIES E REDIRECIONAMENTO FINAL (ALTERADO) ---

// Excluir todos os cookies
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time()-100);
        setcookie($name, '', time()-100, '/');
    }
}

// Monta a URL de destino completa
// Exemplo: /projetos/2025_dev/aims-sub2/pt-br/
$redirect_url = $full_project_path . $target_folder . '/';

// Realiza o redirecionamento HTTP 302 (Temporário)
header('Location: ' . $redirect_url);
exit;
?>