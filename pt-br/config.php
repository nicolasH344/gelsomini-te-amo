<?php
// config.php - Configurações Completas do Sistema WebLearn

// =================================================================
// CONFIGURAÇÕES DO SISTEMA
// =================================================================

// Configurações do Site
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'WebLearn - Jornada do Desenvolvedor');
}

if (!defined('SITE_URL')) {
    define('SITE_URL', 'http://localhost');
}

if (!defined('BASE_PATH')) {
    define('BASE_PATH', '');
}

// Carregar configurações do ambiente
require_once __DIR__ . '/../src/Config/Environment.php';
Environment::load();

// Configurações do Banco de Dados
define('DB_HOST', Environment::get('DB_HOST', 'localhost'));
define('DB_NAME', Environment::get('DB_NAME', 'cursinho'));
define('DB_USER', Environment::get('DB_USER', 'root'));
define('DB_PASS', Environment::get('DB_PASS', ''));
define('DB_CHARSET', 'utf8mb4');

// Configurações de erro (desabilitar em produção)
// Para depuração, você pode mudar display_errors para 1
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0); 

// Timezone
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('America/Sao_Paulo');
}

// Configurações de debug
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    ini_set('display_errors', 0);
}

// Iniciar sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Configurações padrão de sessão
if (!isset($_SESSION['theme'])) { $_SESSION['theme'] = 'purple'; }
if (!isset($_SESSION['language'])) { $_SESSION['language'] = 'pt-br'; }
if (!isset($_SESSION['accessibility_mode'])) { $_SESSION['accessibility_mode'] = false; }

// Detectar idioma pela URL
$current_path = $_SERVER['REQUEST_URI'];
if (strpos($current_path, '/en/') !== false) {
    $_SESSION['language'] = 'en';
} elseif (strpos($current_path, '/es/') !== false) {
    $_SESSION['language'] = 'es';
} elseif (strpos($current_path, '/pt-br/') !== false) {
    $_SESSION['language'] = 'pt-br';
}

// =================================================================
// >> INÍCIO: DEFINIÇÃO DE FUNÇÕES <<
// =================================================================

/**
 * Conexão com o Banco de Dados
 */
if (!function_exists('getDBConnection')) {
    function getDBConnection() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS);
            
            // Configurar PDO para lançar exceções
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
            return $pdo;
            
        } catch (PDOException $e) {
            // Tentar conexão sem senha como fallback
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $pdo = new PDO($dsn, DB_USER, '');
                
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                
                return $pdo;
                
            } catch (PDOException $e2) {
                if (DEBUG_MODE) {
                    error_log("Erro de conexão com o banco: " . $e->getMessage());
                }
                return null;
            }
        }
    }
}

/**
 * Função para sanitizar dados
 */
require_once __DIR__ . '/../src/SecurityHelper.php';

if (!function_exists('sanitize')) {
    function sanitize($data) {
        return SecurityHelper::sanitizeOutput($data);
    }
}

if (!function_exists('generateCSRFToken')) {
    function generateCSRFToken() {
        return SecurityHelper::generateCSRFToken();
    }
}

if (!function_exists('validateCSRFToken')) {
    function validateCSRFToken($token) {
        return SecurityHelper::validateCSRFToken($token);
    }
}

/**
 * Função para obter traduções
 */
if (!function_exists('getTranslations')) {
    function getTranslations() {
        return [
            'pt-br' => [
                'site_title' => 'WebLearn - Jornada do Desenvolvedor',
                'home' => 'Início', 'exercises' => 'Exercícios', 'tutorials' => 'Tutoriais',
                'forum' => 'Fórum', 'profile' => 'Perfil', 'progress' => 'Progresso',
                'login' => 'Entrar', 'register' => 'Registrar', 'logout' => 'Sair',
                'settings' => 'Configurações', 'theme' => 'Tema', 'language' => 'Idioma',
                'purple_theme' => 'Roxo Moderno', 'blue_theme' => 'Azul Clássico',
                'green_theme' => 'Verde Natural', 'dark_theme' => 'Escuro',
                'portuguese' => 'Português', 'english' => 'English', 'spanish' => 'Español',
                'colorblind_mode' => 'Modo para Daltônicos',
                'learn_web_dev' => 'Aprenda Desenvolvimento Web',
                'interactive_platform' => 'Plataforma interativa para aprender programação',
                'start_now' => 'Começar Agora',
                'make_login' => 'Fazer Login',
                'continue_learning' => 'Continuar Aprendendo',
                'view_progress' => 'Ver Progresso',
                'students' => 'Estudantes',
                'why_choose_us' => 'Por que nos escolher?',
                'platform_benefits' => 'Descubra os benefícios da nossa plataforma de aprendizagem',
                'practical_exercises' => 'Exercícios Práticos',
                'practical_exercises_desc' => 'Aprenda fazendo com exercícios interativos que simulam situações reais de desenvolvimento.',
                'instant_feedback' => 'Feedback Instantâneo',
                'instant_feedback_desc' => 'Receba feedback imediato sobre seu código e aprenda com seus erros em tempo real.',
                'active_community' => 'Comunidade Ativa',
                'active_community_desc' => 'Conecte-se com outros desenvolvedores, tire dúvidas e compartilhe conhecimento.',
                'progress_tracking' => 'Acompanhamento de Progresso',
                'progress_tracking_desc' => 'Acompanhe seu progresso com estatísticas detalhadas e alcance seus objetivos.',
                'responsive_design' => 'Design Responsivo',
                'responsive_design_desc' => 'Acesse a plataforma de qualquer dispositivo, a qualquer hora e em qualquer lugar.',
                'accessibility' => 'Acessibilidade',
                'accessibility_desc' => 'Plataforma totalmente acessível, incluindo suporte para pessoas com daltonismo.',
                'learning_paths' => 'Trilhas de Aprendizado',
                'learning_paths_desc' => 'Escolha sua jornada e domine as tecnologias web',
                'beginner_path' => 'Trilha Iniciante',
                'beginner_path_desc' => 'Perfeito para iniciantes. Aprenda HTML, CSS e JavaScript do zero.',
                'advanced_path' => 'Trilha Avançada',
                'advanced_path_desc' => 'Para desenvolvedores que querem se aprofundar em tecnologias modernas.',
                'fundamentals' => 'Fundamentos',
                'basics' => 'Básico',
                'first_projects' => 'Primeiros Projetos',
                'databases' => 'Bancos de Dados',
                'deployment' => 'Deploy e DevOps',
                'start_path' => 'Iniciar Trilha',
                'what_students_say' => 'O que nossos estudantes dizem',
                'testimonials_desc' => 'Veja a experiência de quem já está na jornada',
                'testimonial_1' => 'A plataforma é incrível! Os exercícios práticos me ajudaram a consolidar meus conhecimentos de uma forma que nenhum outro curso online havia conseguido antes.',
                'testimonial_2' => 'O feedback instantâneo é um divisor de águas. Pude corrigir meus erros e evoluir muito mais rápido. Recomendo para todos!',
                'testimonial_3' => 'A comunidade é muito ativa e prestativa. Sempre que tive dúvidas, encontrei ajuda no fórum. Isso faz toda a diferença no aprendizado.',
                'student_photo' => 'Foto do estudante',
                'job_title' => 'Cargo',
                'frontend_developer' => 'Desenvolvedor Front-end',
                'fullstack_developer' => 'Estudante de Análise de Sistemas',
                'ui_designer' => 'Designer UI/UX',
                'ready_to_start' => 'Pronto para começar sua jornada?',
                'join_thousands' => 'Junte-se a milhares de desenvolvedores que já transformaram suas carreiras',
                'start_free' => 'Começar Gratuitamente',
                'view_exercises' => 'Ver Exercícios',
                'create_free_account' => 'Criar Conta Gratuita',
                'explore_content' => 'Explorar Conteúdo',
                'forum' => 'Fórum',
                'exercises' => 'Exercícios',
                'new_post' => 'Novo Post',
                'create_post' => 'Criar Post',
                'comments' => 'Comentários',
                'reply' => 'Responder',
                'category' => 'Categoria',
                'difficulty' => 'Dificuldade',
                'beginner' => 'Iniciante',
                'intermediate' => 'Intermediário',
                'advanced' => 'Avançado',
                'start_exercise' => 'Iniciar Exercício',
                'submit_code' => 'Enviar Código',
                'show_solution' => 'Ver Solução',
                'back_to_forum' => 'Voltar ao Fórum',
                'back_to_exercises' => 'Voltar aos Exercícios'
            ],
            'en' => [
                'site_title' => 'WebLearn - Developer Journey',
                'home' => 'Home', 'exercises' => 'Exercises', 'tutorials' => 'Tutorials',
                'forum' => 'Forum', 'profile' => 'Profile', 'progress' => 'Progress',
                'login' => 'Login', 'register' => 'Register', 'logout' => 'Logout',
                'settings' => 'Settings', 'theme' => 'Theme', 'language' => 'Language',
                'purple_theme' => 'Modern Purple', 'blue_theme' => 'Classic Blue',
                'green_theme' => 'Natural Green', 'dark_theme' => 'Dark',
                'portuguese' => 'Português', 'english' => 'English', 'spanish' => 'Español',
                'colorblind_mode' => 'Colorblind Mode',
                'learn_web_dev' => 'Learn Web Development',
                'interactive_platform' => 'Interactive platform to learn programming',
                'start_now' => 'Start Now',
                'make_login' => 'Login',
                'continue_learning' => 'Continue Learning',
                'view_progress' => 'View Progress',
                'students' => 'Students',
                'why_choose_us' => 'Why choose us?',
                'platform_benefits' => 'Discover the benefits of our learning platform',
                'practical_exercises' => 'Practical Exercises',
                'practical_exercises_desc' => 'Learn by doing with interactive exercises that simulate real development situations.',
                'instant_feedback' => 'Instant Feedback',
                'instant_feedback_desc' => 'Get immediate feedback on your code and learn from your mistakes in real time.',
                'active_community' => 'Active Community',
                'active_community_desc' => 'Connect with other developers, ask questions and share knowledge.',
                'progress_tracking' => 'Progress Tracking',
                'progress_tracking_desc' => 'Track your progress with detailed statistics and achieve your goals.',
                'responsive_design' => 'Responsive Design',
                'responsive_design_desc' => 'Access the platform from any device, anytime, anywhere.',
                'accessibility' => 'Accessibility',
                'accessibility_desc' => 'Fully accessible platform, including support for color blind people.',
                'learning_paths' => 'Learning Paths',
                'learning_paths_desc' => 'Choose your journey and master web technologies',
                'beginner_path' => 'Beginner Path',
                'beginner_path_desc' => 'Perfect for beginners. Learn HTML, CSS and JavaScript from scratch.',
                'advanced_path' => 'Advanced Path',
                'advanced_path_desc' => 'For developers who want to delve deeper into modern technologies.',
                'fundamentals' => 'Fundamentals',
                'basics' => 'Basic',
                'first_projects' => 'First Projects',
                'databases' => 'Databases',
                'deployment' => 'Deploy and DevOps',
                'start_path' => 'Start Path',
                'what_students_say' => 'What our students say',
                'testimonials_desc' => 'See the experience of those who are already on the journey',
                'testimonial_1' => 'The platform is amazing! The practical exercises helped me consolidate my knowledge in a way that no other online course had managed to do before.',
                'testimonial_2' => 'Instant feedback is a game changer. I was able to correct my mistakes and improve much faster. I recommend it to everyone!',
                'testimonial_3' => 'The community is very active and helpful. Whenever I had questions, I found help on the forum. This makes all the difference in learning.',
                'student_photo' => 'Student photo',
                'job_title' => 'Job Title',
                'frontend_developer' => 'Front-end Developer',
                'fullstack_developer' => 'Systems Analysis Student',
                'ui_designer' => 'UI/UX Designer',
                'ready_to_start' => 'Ready to start your journey?',
                'join_thousands' => 'Join thousands of developers who have already transformed their careers',
                'start_free' => 'Start for Free',
                'view_exercises' => 'View Exercises',
                'create_free_account' => 'Create Free Account',
                'explore_content' => 'Explore Content',
                'forum' => 'Forum',
                'exercises' => 'Exercises',
                'new_post' => 'New Post',
                'create_post' => 'Create Post',
                'comments' => 'Comments',
                'reply' => 'Reply',
                'category' => 'Category',
                'difficulty' => 'Difficulty',
                'beginner' => 'Beginner',
                'intermediate' => 'Intermediate',
                'advanced' => 'Advanced',
                'start_exercise' => 'Start Exercise',
                'submit_code' => 'Submit Code',
                'show_solution' => 'Show Solution',
                'back_to_forum' => 'Back to Forum',
                'back_to_exercises' => 'Back to Exercises'
            ],
            'es' => [
                'site_title' => 'WebLearn - Viaje del Desarrollador',
                'home' => 'Inicio', 'exercises' => 'Ejercicios', 'tutorials' => 'Tutoriales',
                'forum' => 'Foro', 'profile' => 'Perfil', 'progress' => 'Progreso',
                'login' => 'Iniciar Sesión', 'register' => 'Registrarse', 'logout' => 'Cerrar Sesión',
                'settings' => 'Configuraciones', 'theme' => 'Tema', 'language' => 'Idioma',
                'purple_theme' => 'Púrpura Moderno', 'blue_theme' => 'Azul Clásico',
                'green_theme' => 'Verde Natural', 'dark_theme' => 'Oscuro',
                'portuguese' => 'Português', 'english' => 'English', 'spanish' => 'Español',
                'colorblind_mode' => 'Modo para Daltónicos',
                'learn_web_dev' => 'Aprende Desarrollo Web',
                'interactive_platform' => 'Plataforma interactiva para aprender programación',
                'start_now' => 'Comenzar Ahora',
                'make_login' => 'Iniciar Sesión',
                'continue_learning' => 'Continuar Aprendiendo',
                'view_progress' => 'Ver Progreso',
                'students' => 'Estudiantes',
                'why_choose_us' => '¿Por qué elegirnos?',
                'platform_benefits' => 'Descubre los beneficios de nuestra plataforma de aprendizaje',
                'practical_exercises' => 'Ejercicios Prácticos',
                'practical_exercises_desc' => 'Aprende haciendo con ejercicios interactivos que simulan situaciones reales de desarrollo.',
                'instant_feedback' => 'Retroalimentación Instantánea',
                'instant_feedback_desc' => 'Recibe retroalimentación inmediata sobre tu código y aprende de tus errores en tiempo real.',
                'active_community' => 'Comunidad Activa',
                'active_community_desc' => 'Conéctate con otros desarrolladores, resuelve dudas y comparte conocimiento.',
                'progress_tracking' => 'Seguimiento de Progreso',
                'progress_tracking_desc' => 'Monitorea tu progreso con estadísticas detalladas y alcanza tus objetivos.',
                'responsive_design' => 'Diseño Responsivo',
                'responsive_design_desc' => 'Accede a la plataforma desde cualquier dispositivo, en cualquier momento y lugar.',
                'accessibility' => 'Accesibilidad',
                'accessibility_desc' => 'Plataforma totalmente accesible, incluyendo soporte para personas con daltonismo.',
                'learning_paths' => 'Rutas de Aprendizaje',
                'learning_paths_desc' => 'Elige tu camino y domina las tecnologías web',
                'beginner_path' => 'Ruta Principiante',
                'beginner_path_desc' => 'Perfecta para principiantes. Aprende HTML, CSS y JavaScript desde cero.',
                'advanced_path' => 'Ruta Avanzada',
                'advanced_path_desc' => 'Para desarrolladores que quieren profundizar en tecnologías modernas.',
                'fundamentals' => 'Fundamentos',
                'basics' => 'Básico',
                'first_projects' => 'Primeros Proyectos',
                'databases' => 'Bases de Datos',
                'deployment' => 'Deploy y DevOps',
                'start_path' => 'Iniciar Ruta',
                'what_students_say' => 'Lo que dicen nuestros estudiantes',
                'testimonials_desc' => 'Conoce la experiencia de quienes ya están en el camino',
                'testimonial_1' => '¡La plataforma es increíble! Los ejercicios prácticos me ayudaron a consolidar mis conocimientos de una forma que ningún otro curso online había logrado antes.',
                'testimonial_2' => 'La retroalimentación instantánea es un punto de inflexión. Pude corregir mis errores y evolucionar mucho más rápido. ¡Se lo recomiendo a todos!',
                'testimonial_3' => 'La comunidad es muy activa y servicial. Siempre que he tenido dudas, he encontrado ayuda en el foro. Eso marca la diferencia en el aprendizaje.',
                'student_photo' => 'Foto del estudiante',
                'job_title' => 'Cargo',
                'frontend_developer' => 'Desarrollador Front-end',
                'fullstack_developer' => 'Estudiante de Análisis de Sistemas',
                'ui_designer' => 'Diseñador UI/UX',
                'ready_to_start' => '¿Listo para comenzar tu viaje?',
                'join_thousands' => 'Únete a miles de desarrolladores que ya han transformado sus carreras',
                'start_free' => 'Comenzar Gratis',
                'view_exercises' => 'Ver Ejercicios',
                'create_free_account' => 'Crear Cuenta Gratuita',
                'explore_content' => 'Explorar Contenido',
                'forum' => 'Foro',
                'exercises' => 'Ejercicios',
                'new_post' => 'Nueva Publicación',
                'create_post' => 'Crear Publicación',
                'comments' => 'Comentarios',
                'reply' => 'Responder',
                'category' => 'Categoría',
                'difficulty' => 'Dificultad',
                'beginner' => 'Principiante',
                'intermediate' => 'Intermedio',
                'advanced' => 'Avanzado',
                'start_exercise' => 'Iniciar Ejercicio',
                'submit_code' => 'Enviar Código',
                'show_solution' => 'Ver Solución',
                'back_to_forum' => 'Volver al Foro',
                'back_to_exercises' => 'Volver a Ejercicios'
            ]
        ];
    }
}

/**
 * Função para obter texto traduzido
 */
if (!function_exists('t')) {
    function t($key, $default = '') {
        $translations = getTranslations();
        $lang = $_SESSION['language'] ?? 'pt-br';
        return $translations[$lang][$key] ?? $default ?: $key;
    }

    function getCurrentLanguageFolder() {
        $lang = $_SESSION['language'] ?? 'pt-br';
        return $lang;
    }

    function getLanguageUrl($target_lang) {
        $current_lang = getCurrentLanguageFolder();
        $current_url = $_SERVER['REQUEST_URI'];
        return str_replace("/$current_lang/", "/$target_lang/", $current_url);
    }
}

/**
 * Função para redirecionar
 */
if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit;
    }
}

/**
 * Função para obter classe CSS do tema
 */
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

/**
 * Função para processar registro de usuário
 */
if (!function_exists('processRegister')) {
    function processRegister($data) {
        // Validações básicas
        if (empty($data['first_name']) || empty($data['last_name']) || 
            empty($data['username']) || empty($data['email']) || 
            empty($data['password']) || empty($data['confirm_password'])) {
            return ['success' => false, 'message' => 'Preencha todos os campos obrigatórios'];
        }
        
        // Verificar consentimento LGPD
        if (!isset($data['lgpd_consent']) || $data['lgpd_consent'] !== 'on') {
            return ['success' => false, 'message' => 'Você deve aceitar a Política de Privacidade (LGPD)'];
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email inválido'];
        }
        
        if (strlen($data['password']) < 6) {
            return ['success' => false, 'message' => 'A senha deve ter no mínimo 6 caracteres'];
        }
        
        if ($data['password'] !== $data['confirm_password']) {
            return ['success' => false, 'message' => 'As senhas não coincidem'];
        }
        
        if (!isset($data['terms']) || $data['terms'] !== 'on') {
            return ['success' => false, 'message' => 'Você deve aceitar os termos de uso'];
        }
        
        // Tentar conectar com banco de dados
        $conn = getDBConnection();
        
        if ($conn) {
            // MODO REAL: Com banco de dados
            try {
                // Verificar se username já existe
                $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
                $stmt->execute([$data['username']]);
                if ($stmt->fetch()) {
                    return ['success' => false, 'message' => 'Nome de usuário já está em uso'];
                }
                
                // Verificar se email já existe
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$data['email']]);
                if ($stmt->fetch()) {
                    return ['success' => false, 'message' => 'Email já está cadastrado'];
                }
                
                // Inserir novo usuário
                $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, password_hash, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
                $stmt->execute([
                    $data['first_name'],
                    $data['last_name'],
                    $data['username'],
                    $data['email'],
                    $password_hash
                ]);
                
                return ['success' => true, 'message' => 'Conta criada com sucesso! Faça login para continuar.'];
                
            } catch(PDOException $e) {
                return ['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()];
            }
        } else {
            // Modo simulação (para desenvolvimento)
            return ['success' => true, 'message' => 'Conta criada com sucesso! (Modo simulação)'];
        }
    }
}

// =================================================================
// >> FIM: DEFINIÇÃO DE FUNÇÕES <<
// =================================================================

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