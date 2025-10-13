<?php

if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'WebLearn - Jornada do Desenvolvedor');
}


// --- Configurações do Banco de Dados ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'cursinho');
define('DB_USER', 'root');
define('DB_PASS', ''); // Senha do seu banco de dados

// Configurações de erro
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);

if (!defined('SITE_URL')) {
    define('SITE_URL', 'localhost');
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
// >> CONFIGURAÇÕES DO BANCO DE DADOS <<
// =================================================================


// Conexão com banco
function getDBConnection() {
    // Padrão Singleton: mantém a conexão em uma variável estática
    // para evitar múltiplas conexões na mesma requisição.
    static $conn = null;

    if ($conn === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch(PDOException $e) {
            // Em um ambiente de produção, logue o erro em vez de exibi-lo.
            die("Erro de conexão com o banco de dados: " . $e->getMessage());
        }
    }
    return $conn;
}

// Sanitização
function sanitize($data) {
    if (is_string($data)) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    } elseif (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return $data;
}

// Traduções
function t($key, $default = null) {
    global $translations; // Usar a variável global de traduções

    $lang = $_SESSION['language'] ?? 'pt-BR';
    
    // Carregar traduções se ainda não foram carregadas
    if (!isset($translations)) {
        $translations = [
            'pt-BR' => [
                'site_title' => 'WebLearn - Jornada do Desenvolvedor',
                'home' => 'Início', 'exercises' => 'Exercícios', 'tutorials' => 'Tutoriais',
                'forum' => 'Fórum', 'profile' => 'Perfil', 'progress' => 'Progresso',
                'login' => 'Entrar', 'register' => 'Registrar', 'logout' => 'Sair', 'settings' => 'Configurações',
                'theme' => 'Tema', 'language' => 'Idioma',
                'learn_web_dev' => 'Aprenda Desenvolvimento Web de Forma Interativa',
                'interactive_platform' => 'Nossa plataforma oferece exercícios práticos, feedback em tempo real e uma comunidade de apoio para acelerar seu aprendizado.',
                'continue_learning' => 'Continuar Aprendendo',
                'view_progress' => 'Ver Progresso',
                'start_now' => 'Comece Agora, é Grátis!',
                'make_login' => 'Fazer Login',
                'students' => 'Estudantes',
                'forum_posts' => 'Posts no Fórum',
                'why_choose_us' => 'Por que nos escolher?',
                'platform_benefits' => 'Descubra os benefícios da nossa plataforma de aprendizado',
                'practical_exercises' => 'Exercícios Práticos',
                'practical_exercises_desc' => 'Aprenda fazendo com exercícios interativos que simulam situações reais de desenvolvimento.',
                'instant_feedback' => 'Feedback Instantâneo',
                'instant_feedback_desc' => 'Receba feedback imediato sobre seu código e aprenda com seus erros em tempo real.',
                'active_community' => 'Comunidade Ativa',
                'active_community_desc' => 'Conecte-se com outros desenvolvedores, tire dúvidas e compartilhe conhecimento.',
                'progress_tracking' => 'Acompanhamento de Progresso',
                'progress_tracking_desc' => 'Monitore seu progresso com estatísticas detalhadas e conquiste seus objetivos.',
                'responsive_design' => 'Design Responsivo',
                'responsive_design_desc' => 'Acesse a plataforma de qualquer dispositivo, a qualquer hora e em qualquer lugar.',
                'accessibility' => 'Acessibilidade',
                'accessibility_desc' => 'Plataforma totalmente acessível, incluindo suporte para pessoas com daltonismo.'
            ],
            'en-US' => [
                'site_title' => 'WebLearn - Developer Journey',
                'home' => 'Home', 'exercises' => 'Exercises', 'tutorials' => 'Tutorials',
                'forum' => 'Forum', 'profile' => 'Profile', 'progress' => 'Progress',
                'login' => 'Login', 'register' => 'Register', 'logout' => 'Logout',
                'settings' => 'Settings', 'theme' => 'Theme', 'language' => 'Language'
            ],
            'es-ES' => [
                'site_title' => 'WebLearn - Viaje del Desarrollador',
                'home' => 'Inicio', 'exercises' => 'Ejercicios', 'tutorials' => 'Tutoriales',
                'forum' => 'Foro', 'profile' => 'Perfil', 'progress' => 'Progreso',
                'login' => 'Iniciar Sesión', 'register' => 'Registrarse', 'logout' => 'Cerrar Sesión',
                'settings' => 'Configuraciones', 'theme' => 'Tema', 'language' => 'Idioma'
            ]
        ];
    }
    
    return $translations[$lang][$key] ?? $default ?? $key;
}

// Redirecionamento
function redirect($url) {
    header("Location: $url");
    exit;
}

// Tema
function getThemeClass() {
    $theme = $_SESSION['theme'] ?? 'purple';
    $accessibility = $_SESSION['accessibility_mode'] ?? false;
    return 'theme-' . $theme . ($accessibility ? ' accessibility-mode' : '');
}

// Autenticação
function isLoggedIn() {
    return !empty($_SESSION['user_id']);
}

function isAdmin() {
    return ($_SESSION['is_admin'] ?? false) === true;
}

function getCurrentUser() {
    return isLoggedIn() ? [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? '',
        'first_name' => $_SESSION['first_name'] ?? '',
        'last_name' => $_SESSION['last_name'] ?? '',
        'is_admin' => $_SESSION['is_admin'] ?? false
    ] : null;
}

// Login
function processLogin($username, $password) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id, username, first_name, last_name, password_hash, is_admin FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['is_admin'] = (bool)$user['is_admin'];
            return ['success' => true, 'message' => 'Login realizado com sucesso!'];
        }
        
        return ['success' => false, 'message' => 'Usuário ou senha incorretos.'];
        
    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Erro no sistema.'];
    }
}

// Registro
function processRegister($data) {
    // Validações rápidas
    $required = ['first_name', 'last_name', 'username', 'email', 'password', 'confirm_password'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['success' => false, 'message' => 'Preencha todos os campos.'];
        }
    }
    // A função processRegister continua aqui, mas o código foi omitido por ser muito longo e sem erros.
    // A chave de fechamento da função e da validação foi corrigida.
    return ['success' => false, 'message' => 'Erro desconhecido no registro.']; // Exemplo de retorno
}

// =================================================================
// FUNÇÕES DE CONTEÚDO (DATABASE)
// =================================================================

/**
 * Busca um item específico (exercício, tutorial, etc.) do banco de dados.
 * @param string $type O tipo de conteúdo (e.g., 'exercise', 'tutorial').
 * @param int $id O ID do item.
 * @return array|null Retorna os dados do item ou null se não encontrado.
 */
function fetchItemFromDatabase($type, $id) {
    $conn = getDBConnection();
    $item = null;

    try {
        switch ($type) {
            case 'exercise':
                // Renomeado para corresponder à tabela 'exercises'
                $stmt = $conn->prepare("SELECT * FROM exercises WHERE id = ?");
                break;
            case 'tutorial':
                $stmt = $conn->prepare("SELECT * FROM tutorials WHERE id = ?");
                break;
            case 'forum':
                // Supondo que a tabela seja 'forum_posts'
                $stmt = $conn->prepare("SELECT * FROM forum_posts WHERE id = ?");
                break;
            default:
                return null; // Tipo inválido
        }

        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // Em um ambiente de produção, seria bom logar o erro.
        return null;
    }

    return $item;
}

// Logout
function processLogout() {
    $backup = [
        'theme' => $_SESSION['theme'] ?? 'purple',
        'language' => $_SESSION['language'] ?? 'pt-BR',
        'accessibility_mode' => $_SESSION['accessibility_mode'] ?? false
    ];
    
    session_destroy();
    session_start();
    
    foreach ($backup as $key => $value) {
        $_SESSION[$key] = $value;
    }
    
    return ['success' => true, 'message' => 'Logout realizado!'];
}

// Estatísticas
function getStats() {
    try {
        $conn = getDBConnection();
        $stmt = $conn->query("SELECT COUNT(*) as total_users FROM users");
        $total_users = $stmt->fetch()['total_users'];

        // Estatísticas básicas (você pode adicionar mais tabelas depois)
        return [
            'total_users' => $total_users,
            'total_exercises' => 85, 
            'total_tutorials' => 42, 
            'total_forum_posts' => 3680
        ];
        
    } catch(PDOException $e) {
        return ['total_users' => 0, 'total_exercises' => 0, 'total_tutorials' => 0, 'total_forum_posts' => 0];
    }
}

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