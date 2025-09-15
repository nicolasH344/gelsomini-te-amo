<?php

if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'WebLearn - Jornada do Desenvolvedor');
}

if (!defined('SITE_URL')) {
    define('SITE_URL', 'http://localhost');
}

// Configurações de erro (desabilitar em produção)
// Para depuração, você pode mudar display_errors para 1
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0); 

// Timezone
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('America/Sao_Paulo');
}

// Iniciar sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// =================================================================
// >> INÍCIO: DEFINIÇÃO DE FUNÇÕES <<
// Todas as funções foram movidas para cá para garantir que existam antes de serem chamadas.
// =================================================================

// Função para sanitizar dados
if (!function_exists('sanitize')) {
    function sanitize($data) {
        if (is_string($data)) {
            return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
        }
        return $data;
    }
}

// Função para obter traduções
if (!function_exists('getTranslations')) {
    function getTranslations() {
        return [
            'pt-BR' => [
                'site_title' => 'WebLearn - Jornada do Desenvolvedor',
                'home' => 'Início', 'exercises' => 'Exercícios', 'tutorials' => 'Tutoriais',
                'forum' => 'Fórum', 'profile' => 'Perfil', 'progress' => 'Progresso',
                'login' => 'Entrar', 'register' => 'Registrar', 'logout' => 'Sair',
                'settings' => 'Configurações', 'theme' => 'Tema', 'language' => 'Idioma',
                'purple_theme' => 'Roxo Moderno', 'blue_theme' => 'Azul Clássico',
                'green_theme' => 'Verde Natural', 'dark_theme' => 'Escuro',
                'portuguese' => 'Português', 'english' => 'English', 'spanish' => 'Español',
                'colorblind_mode' => 'Modo para Daltônicos'
                // Adicione outras traduções aqui
            ],
            'en-US' => [
                'site_title' => 'WebLearn - Developer Journey',
                'home' => 'Home', 'exercises' => 'Exercises', 'tutorials' => 'Tutorials',
                'forum' => 'Forum', 'profile' => 'Profile', 'progress' => 'Progress',
                'login' => 'Login', 'register' => 'Register', 'logout' => 'Logout',
                'settings' => 'Settings', 'theme' => 'Theme', 'language' => 'Language',
                'purple_theme' => 'Modern Purple', 'blue_theme' => 'Classic Blue',
                'green_theme' => 'Natural Green', 'dark_theme' => 'Dark',
                'portuguese' => 'Português', 'english' => 'English', 'spanish' => 'Español',
                'colorblind_mode' => 'Colorblind Mode'
                // Add other translations here
            ],
            'es-ES' => [
                'site_title' => 'WebLearn - Viaje del Desarrollador',
                'home' => 'Inicio', 'exercises' => 'Ejercicios', 'tutorials' => 'Tutoriales',
                'forum' => 'Foro', 'profile' => 'Perfil', 'progress' => 'Progreso',
                'login' => 'Iniciar Sesión', 'register' => 'Registrarse', 'logout' => 'Cerrar Sesión',
                'settings' => 'Configuraciones', 'theme' => 'Tema', 'language' => 'Idioma',
                'purple_theme' => 'Púrpura Moderno', 'blue_theme' => 'Azul Clásico',
                'green_theme' => 'Verde Natural', 'dark_theme' => 'Oscuro',
                'portuguese' => 'Português', 'english' => 'English', 'spanish' => 'Español',
                'colorblind_mode' => 'Modo para Daltónicos'
                // Adicione outras traduções aqui
            ]
        ];
    }
}

// Função para obter texto traduzido
if (!function_exists('t')) {
    function t($key, $default = '') {
        $translations = getTranslations();
        $lang = $_SESSION['language'] ?? 'pt-BR';
        return $translations[$lang][$key] ?? $default ?: $key;
    }
}

// Função para redirecionar
if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit;
    }
}

// Função para obter classe CSS do tema
if (!function_exists('getThemeClass')) {
    function getThemeClass() {
        $theme = $_SESSION['theme'] ?? 'purple';
        $accessibility = $_SESSION['accessibility_mode'] ?? false;
        $class = 'theme-' . $theme;
        if ($accessibility) {
            $class .= ' accessibility-mode';
        }
        return $class;
    }
}

// Funções de Autenticação e Usuário
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() { return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']); }
}
if (!function_exists('isAdmin')) {
    function isAdmin() { return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true; }
}
if (!function_exists('getCurrentUser')) {
    function getCurrentUser() {
        if (!isLoggedIn()) { return null; }
        return [
            'id' => $_SESSION['user_id'] ?? 0,
            'username' => $_SESSION['username'] ?? '',
            'first_name' => $_SESSION['first_name'] ?? '',
            'last_name' => $_SESSION['last_name'] ?? '',
            'is_admin' => $_SESSION['is_admin'] ?? false
        ];
    }
}

// Demais funções do sistema
if (!function_exists('processLogin')) {
    function processLogin($username, $password) {
        if (($username === 'admin' && $password === 'admin123') || ($username === 'usuario' && $password === '123456')) {
            $_SESSION['user_id'] = ($username === 'admin') ? 1 : 2;
            $_SESSION['username'] = $username;
            $_SESSION['first_name'] = ($username === 'admin') ? 'Administrador' : 'Usuário';
            $_SESSION['last_name'] = ($username === 'admin') ? 'Sistema' : 'Teste';
            $_SESSION['is_admin'] = ($username === 'admin');
            return ['success' => true, 'message' => 'Login realizado com sucesso!'];
        }
        return ['success' => false, 'message' => 'Usuário ou senha incorretos.'];
    }
}
if (!function_exists('processLogout')) {
    function processLogout() {
        $theme = $_SESSION['theme'] ?? 'purple';
        $language = $_SESSION['language'] ?? 'pt-BR';
        $accessibility = $_SESSION['accessibility_mode'] ?? false;
        $_SESSION = [];
        $_SESSION['theme'] = $theme;
        $_SESSION['language'] = $language;
        $_SESSION['accessibility_mode'] = $accessibility;
        return ['success' => true, 'message' => 'Logout realizado com sucesso!'];
    }
}
if (!function_exists('getStats')) {
    function getStats() {
        return ['total_users' => 1250, 'total_exercises' => 85, 'total_tutorials' => 42, 'total_forum_posts' => 3680];
    }
}

// =================================================================
// >> FIM: DEFINIÇÃO DE FUNÇÕES <<
// =================================================================


// Configurações padrão de sessão
if (!isset($_SESSION['theme'])) { $_SESSION['theme'] = 'purple'; }
if (!isset($_SESSION['language'])) { $_SESSION['language'] = 'pt-BR'; }
if (!isset($_SESSION['accessibility_mode'])) { $_SESSION['accessibility_mode'] = false; }


// >> CORREÇÃO: Processar mudanças de configuração com redirecionamento (Padrão Post-Redirect-Get)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $configChanged = false;

    if (isset($_POST['change_theme']) && isset($_POST['theme'])) {
        $_SESSION['theme'] = sanitize($_POST['theme']);
        $configChanged = true;
    }
    
    if (isset($_POST['change_language']) && isset($_POST['language'])) {
        $_SESSION['language'] = sanitize($_POST['language']);
        $configChanged = true;
    }
    
    if (isset($_POST['toggle_accessibility'])) {
        $_SESSION['accessibility_mode'] = !$_SESSION['accessibility_mode'];
        $configChanged = true;
    }

    // Se alguma configuração foi alterada, redireciona para a mesma página.
    // Isso "limpa" a requisição POST e evita que o formulário seja reenviado ao atualizar a página.
    if ($configChanged) {
        redirect($_SERVER['REQUEST_URI']);
    }
}
?>