<?php
// Detectar idioma preferido do navegador
$browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'pt', 0, 2);

// Mapear idiomas suportados
$supported_langs = ['pt', 'en', 'es'];
$lang_folders = [
    'pt' => 'pt-br',
    'en' => 'en', 
    'es' => 'es'
];

// Verificar se o idioma é suportado
if (!in_array($browser_lang, $supported_langs)) {
    $browser_lang = 'pt'; // Padrão português
}

$folder = $lang_folders[$browser_lang];

// Redirecionar para a pasta do idioma
header("Location: $folder/");
exit;
?>