<?php
/**
 * ARQUIVO: show.php
 * DESCRIÇÃO: Exibe detalhes completos de um tutorial ou exercício
 * AUTOR: Sistema WebLearn
 * DATA: 2024
 */

// Inclui arquivo de configuração com funções auxiliares (sanitize, redirect, etc)
require_once 'config.php';

// Inclui conexão com banco de dados para buscar exercícios
require_once 'database_connector.php';

// Captura e sanitiza o tipo de conteúdo da URL (tutorial ou exercise)
$type = sanitize($_GET['type'] ?? '');

// Captura e converte o ID para inteiro (proteção contra SQL injection)
$id = (int)($_GET['id'] ?? 0);

// Verifica se está em modo de prévia (para administradores)
$preview = isset($_GET['preview']);

// VALIDAÇÃO: Se não tiver tipo OU id, redireciona para página inicial
if (!$type || !$id) {
    redirect('index.php');
}

// ========================================
// CARREGAR DADOS BASEADO NO TIPO
// ========================================

if ($type === 'tutorial') {
    // TUTORIAIS: Carrega do arquivo JSON via função getTutorials()
    require_once 'data/tutorials.php';
    $items = getTutorials(); // Busca todos os tutoriais
    
    // Filtra o tutorial específico pelo ID usando arrow function
    $item = array_filter($items, fn($t) => $t['id'] === $id);
    
    // Converte para array indexado e pega o primeiro (ou null se não encontrar)
    $item = $item ? array_values($item)[0] : null;
    
    // Define título da página
    $title = $item ? $item['title'] : 'Tutorial não encontrado';
    
    // ENRIQUECIMENTO DE DADOS: Adiciona campos extras se não existirem
    if ($item) {
        // Operador ?? retorna valor à direita se à esquerda for null
        $item['author'] = $item['author'] ?? 'Equipe WebLearn';
        $item['last_updated'] = $item['created_at'] ?? date('Y-m-d');
        
        // Valores fixos para demonstração
        $item['rating'] = 4.8; // Avaliação em estrelas
        $item['rating_count'] = rand(50, 200); // Número aleatório de avaliações
        $item['downloads'] = rand(100, 1000); // Downloads simulados
        $item['tags'] = $item['topics'] ?? []; // Tags para busca
    }
    
} elseif ($type === 'exercise') {
    // EXERCÍCIOS: Busca do banco de dados MySQL via PDO
    // Parâmetros: categoria='', dificuldade='', busca='', página=1, limite=100
    $exercises = $dbConnector->getExercises('', '', '', 1, 100);
    
    // Filtra exercício específico pelo ID
    $item = array_filter($exercises, fn($e) => $e['id'] === $id);
    $item = $item ? array_values($item)[0] : null;
    $title = $item ? $item['title'] : 'Exercício não encontrado';
    
    // ENRIQUECIMENTO: Adiciona campos padrão para exercícios
    if ($item) {
        // Array de objetivos de aprendizagem
        $item['objectives'] = $item['objectives'] ?? [
            'Implementar a solução proposta',
            'Seguir as melhores práticas',
            'Testar o código desenvolvido'
        ];
        
        // Tecnologias usadas (cria array com categoria se não existir)
        $item['technologies'] = $item['technologies'] ?? [$item['category']];
        
        // Pré-requisitos necessários
        $item['prerequisites'] = $item['prerequisites'] ?? ['Conhecimentos básicos de ' . $item['category']];
        
        // Tempo estimado para completar
        $item['estimated_time'] = $item['estimated_time'] ?? '30 minutos';
        
        // Estatísticas
        $item['rating'] = $item['rating'] ?? 4.5;
        $item['rating_count'] = $item['rating_count'] ?? rand(20, 100);
        $item['author'] = $item['author'] ?? 'Equipe WebLearn';
        $item['last_updated'] = $item['created_at'] ?? date('Y-m-d');
        $item['downloads'] = $item['downloads'] ?? rand(50, 500);
        
        // Tags para categorização (categoria + dificuldade)
        $item['tags'] = $item['tags'] ?? [$item['category'], $item['difficulty']];
    }
    
} else {
    // VALIDAÇÃO: Se tipo não for tutorial nem exercise, redireciona
    redirect('index.php');
}

// VALIDAÇÃO FINAL: Se não encontrou o item, redireciona para lista apropriada
if (!$item) {
    // Operador ternário: condição ? valor_se_true : valor_se_false
    redirect($type === 'tutorial' ? 'tutorials_index.php' : 'exercises_index.php');
}

// ========================================
// SISTEMA DE PROGRESSO DO USUÁRIO
// ========================================

// Inicializa variáveis de progresso
$user_progress = 0; // Percentual de conclusão (0-100)
$time_remaining = 'Não iniciado'; // Status em texto

// Verifica se usuário está autenticado (função do config.php)
if (isLoggedIn()) {
    // Obtém conexão PDO com banco de dados
    $conn = getDBConnection();
    
    // Se tem conexão E é tutorial (exercícios tem sistema próprio)
    if ($conn && $type === 'tutorial') {
        try {
            // QUERY PREPARADA: Previne SQL Injection
            // ? são placeholders que serão substituídos com segurança
            $stmt = $conn->prepare("SELECT progress FROM tutorial_progress WHERE user_id = ? AND tutorial_id = ?");
            
            // Executa query substituindo os ? pelos valores
            $stmt->execute([getCurrentUser()['id'], $id]);
            
            // Busca resultado (retorna array ou false)
            $progress_data = $stmt->fetch();
            
            if ($progress_data) {
                // Converte progresso para inteiro
                $user_progress = (int)$progress_data['progress'];
                
                // Define status baseado no progresso
                $time_remaining = $user_progress >= 100 ? 'Concluído' : 'Em andamento';
            }
            
        } catch (PDOException $e) {
            // TRATAMENTO DE ERRO: Silencia erro se tabela não existir
            // Não exibe erro para não quebrar a página
        }
    }
}

// ========================================
// RENDERIZAÇÃO DA PÁGINA
// ========================================

// Inclui o cabeçalho HTML (navbar, links CSS, etc)
include 'header.php';
?>

<!-- CONTAINER PRINCIPAL Bootstrap -->
<div class="container mt-4">
    
    <!-- ALERTA DE PRÉVIA: Só aparece se ?preview=1 na URL -->
    <?php if ($preview): ?>
        <div class="alert alert-info d-flex align-items-center">
            <i class="fas fa-eye me-2"></i>
            <div>
                <strong>Modo Visualização:</strong> Esta é uma prévia do conteúdo. Alterações não serão salvas.
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- Cabeçalho do Conteúdo -->
            <div class="content-header-card mb-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2 flex-wrap">
                            <span class="content-type-badge <?php echo $type === 'tutorial' ? 'tutorial' : 'exercise'; ?> me-2">
                                <i class="fas fa-<?php echo $type === 'tutorial' ? 'book' : 'dumbbell'; ?> me-1"></i>
                                <?php echo $type === 'tutorial' ? 'Tutorial' : 'Exercício'; ?>
                            </span>
                            <span class="difficulty-badge difficulty-<?php echo strtolower($item['difficulty']); ?> me-2">
                                <?php echo sanitize($item['difficulty']); ?>
                            </span>
                            <?php if (isset($item['rating'])): ?>
                            <span class="rating-badge me-2">
                                <i class="fas fa-star text-warning"></i>
                                <?php echo $item['rating']; ?> (<?php echo $item['rating_count']; ?>)
                            </span>
                            <?php endif; ?>
                            <?php if (isset($item['downloads'])): ?>
                            <span class="downloads-badge">
                                <i class="fas fa-download"></i>
                                <?php echo $item['downloads']; ?> downloads
                            </span>
                            <?php endif; ?>
                        </div>
                        <h1 class="display-6 fw-bold mb-3"><?php echo sanitize($item['title']); ?></h1>
                        <p class="lead text-muted mb-4"><?php echo sanitize($item['description']); ?></p>
                        
                        <!-- Tags -->
                        <?php if (isset($item['tags'])): ?>
                        <div class="tags-container mb-3">
                            <?php foreach ($item['tags'] as $tag): ?>
                                <span class="tag"><?php echo $tag; ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Metadados -->
                        <div class="metadata-grid">
                            <div class="metadata-item">
                                <i class="fas fa-tag"></i>
                                <span><?php echo sanitize($item['category']); ?></span>
                            </div>
                            <?php if ($type === 'tutorial'): ?>
                            <div class="metadata-item">
                                <i class="fas fa-clock"></i>
                                <span><?php echo sanitize($item['duration']); ?></span>
                            </div>
                            <div class="metadata-item">
                                <i class="fas fa-eye"></i>
                                <span><?php echo number_format($item['views']); ?> visualizações</span>
                            </div>
                            <?php else: ?>
                            <div class="metadata-item">
                                <i class="fas fa-stopwatch"></i>
                                <span><?php echo $item['estimated_time']; ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="metadata-item">
                                <i class="fas fa-user"></i>
                                <span><?php echo isset($item['author']) ? $item['author'] : 'Equipe CodeLearn'; ?></span>
                            </div>
                            <div class="metadata-item">
                                <i class="fas fa-calendar"></i>
                                <span><?php echo isset($item['last_updated']) ? date('d/m/Y', strtotime($item['last_updated'])) : 'Atualizado recentemente'; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="header-actions">
                        <button class="btn btn-icon" title="Favoritar">
                            <i class="far fa-heart"></i>
                        </button>
                        <button class="btn btn-icon" title="Compartilhar">
                            <i class="fas fa-share-alt"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Conteúdo Principal -->
            <div class="main-content-card">
                <div class="content-navigation">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#content" type="button" role="tab">
                                <i class="fas fa-file-alt me-2"></i>Conteúdo
                            </button>
                        </li>
                        <?php if ($type === 'exercise'): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#objectives" type="button" role="tab">
                                <i class="fas fa-bullseye me-2"></i>Objetivos
                            </button>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#resources" type="button" role="tab">
                                <i class="fas fa-tools me-2"></i>Recursos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#community" type="button" role="tab">
                                <i class="fas fa-users me-2"></i>Comunidade
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content mt-4">
                        <!-- Aba de Conteúdo -->
                        <div class="tab-pane fade show active" id="content" role="tabpanel">
                            <?php if ($type === 'tutorial'): ?>
                                <section class="content-section">
                                    <h3 class="section-title">📚 Conteúdo do Tutorial</h3>
                                    <div class="tutorial-content">
                                        <div class="content-block">
                                            <h4>Sobre este tutorial</h4>
                                            <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                                        </div>
                                        
                                        <?php if (isset($item['topics']) && !empty($item['topics'])): ?>
                                        <div class="content-block">
                                            <h4>Tópicos Abordados</h4>
                                            <ul class="topics-list">
                                                <?php foreach ($item['topics'] as $topic): ?>
                                                    <li><i class="fas fa-check-circle text-success me-2"></i><?php echo htmlspecialchars($topic); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($item['learning_outcomes']) && !empty($item['learning_outcomes'])): ?>
                                        <div class="content-block">
                                            <h4>O que você vai aprender</h4>
                                            <div class="learning-objectives">
                                                <?php foreach ($item['learning_outcomes'] as $outcome): ?>
                                                <div class="objective-card">
                                                    <i class="fas fa-lightbulb text-warning"></i>
                                                    <div>
                                                        <p><?php echo htmlspecialchars($outcome); ?></p>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($item['prerequisites']) && !empty($item['prerequisites'])): ?>
                                        <div class="content-block">
                                            <h4>Pré-requisitos</h4>
                                            <div class="prerequisites-grid">
                                                <?php foreach ($item['prerequisites'] as $prereq): ?>
                                                <div class="prerequisite-item required">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span><?php echo htmlspecialchars($prereq); ?></span>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="content-block">
                                            <h4><i class="fas fa-code text-primary me-2"></i>Exemplo Prático</h4>
                                            <div class="code-example-container">
                                                <div class="code-example-header">
                                                    <span class="code-example-title">
                                                        <?php
                                                        $exampleTitle = match($item['category']) {
                                                            'HTML' => 'exemplo.html',
                                                            'CSS' => 'estilos.css',
                                                            'JavaScript' => 'script.js',
                                                            'PHP' => 'codigo.php',
                                                            default => 'exemplo.txt'
                                                        };
                                                        echo $exampleTitle;
                                                        ?>
                                                    </span>
                                                    <div class="code-example-actions">
                                                        <button class="btn-code-action" onclick="copyCode(this)" title="Copiar código">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="code-example-body">
                                                    <pre><code class="language-<?php echo strtolower($item['category']); ?>"><?php
                                                    // Gerar exemplo baseado na categoria e ID do tutorial
                                                    $codeExample = '';
                                                    
                                                    if ($item['category'] === 'HTML') {
                                                        if (stripos($item['title'], 'Estrutura') !== false || $item['id'] == 1) {
                                                            $codeExample = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Página com estrutura HTML5 semântica">
    <title>Estrutura HTML5 Moderna</title>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="#home">Início</a></li>
                <li><a href="#sobre">Sobre</a></li>
                <li><a href="#contato">Contato</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <article>
            <h1>Bem-vindo ao HTML5</h1>
            <p>Esta é uma estrutura semântica e acessível.</p>
        </article>
    </main>
    
    <footer>
        <p>&copy; 2024 - Todos os direitos reservados</p>
    </footer>
</body>
</html>';
                                                        } elseif (stripos($item['title'], 'Formul') !== false || $item['id'] == 4) {
                                                            $codeExample = '<form action="/processar" method="POST" aria-label="Formulário de contato">
    <fieldset>
        <legend>Informações Pessoais</legend>
        
        <div class="form-group">
            <label for="nome">Nome completo:</label>
            <input type="text" id="nome" name="nome" 
                   required 
                   aria-required="true"
                   placeholder="Digite seu nome">
        </div>
        
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" 
                   required 
                   aria-required="true"
                   aria-describedby="email-help">
            <small id="email-help">Nunca compartilharemos seu e-mail</small>
        </div>
        
        <div class="form-group">
            <label for="mensagem">Mensagem:</label>
            <textarea id="mensagem" name="mensagem" 
                      rows="4" 
                      aria-label="Digite sua mensagem"></textarea>
        </div>
    </fieldset>
    
    <button type="submit" class="btn-submit">Enviar</button>
</form>';
                                                        } else {
                                                            $codeExample = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>' . htmlspecialchars($item['title']) . '</title>
</head>
<body>
    <h1>Exemplo de ' . htmlspecialchars($item['category']) . '</h1>
    <p>Conteúdo do tutorial sobre ' . htmlspecialchars($item['title']) . '</p>
</body>
</html>';
                                                        }
                                                    } elseif ($item['category'] === 'CSS') {
                                                        if (stripos($item['title'], 'Grid') !== false || $item['id'] == 2) {
                                                            $codeExample = '/* Layout com CSS Grid */
.container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-template-rows: auto 1fr auto;
    grid-template-areas: 
        "header header header"
        "sidebar main main"
        "footer footer footer";
    gap: 20px;
    min-height: 100vh;
}

.header {
    grid-area: header;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem;
    color: white;
}

.sidebar {
    grid-area: sidebar;
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
}

.main {
    grid-area: main;
    padding: 2rem;
}

.footer {
    grid-area: footer;
    background: #2c3e50;
    color: white;
    padding: 1.5rem;
    text-align: center;
}

/* Responsivo */
@media (max-width: 768px) {
    .container {
        grid-template-columns: 1fr;
        grid-template-areas: 
            "header"
            "main"
            "sidebar"
            "footer";
    }
}';
                                                        } elseif (stripos($item['title'], 'Flexbox') !== false || $item['id'] == 5) {
                                                            $codeExample = '/* Layout Flexível com Flexbox */
.flex-container {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    padding: 2rem;
}

.flex-item {
    flex: 1;
    padding: 1.5rem;
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.flex-item:hover {
    transform: translateY(-5px);
}

/* Navegação com Flexbox */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    background: #2c3e50;
}

.nav-links {
    display: flex;
    gap: 2rem;
    list-style: none;
}

/* Cards responsivos */
.card-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.card {
    flex: 1 1 300px;
    max-width: 350px;
}';
                                                        } elseif (stripos($item['title'], 'Animation') !== false || $item['id'] == 7) {
                                                            $codeExample = '/* Animações CSS Avançadas */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

.animated-card {
    animation: fadeInUp 0.6s ease-out;
    transition: all 0.3s ease;
}

.animated-card:hover {
    animation: pulse 1s infinite;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

/* Loading Spinner */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

/* Transition suave */
.smooth-transition {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}';
                                                        } else {
                                                            $codeExample = '/* Estilos para ' . htmlspecialchars($item['title']) . ' */
.exemplo {
    padding: 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    color: white;
}

.exemplo h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.exemplo p {
    line-height: 1.6;
    opacity: 0.9;
}';
                                                        }
                                                    } elseif ($item['category'] === 'JavaScript') {
                                                        if (stripos($item['title'], 'ES6') !== false || stripos($item['title'], 'Moderno') !== false || $item['id'] == 3) {
                                                            $codeExample = '// JavaScript ES6+ Moderno

// Arrow Functions
const saudacao = (nome) => `Olá, ${nome}!`;

// Destructuring
const usuario = {
    nome: "Maria",
    email: "maria@example.com",
    idade: 25
};

const { nome, email } = usuario;

// Spread Operator
const numeros = [1, 2, 3];
const maisNumeros = [...numeros, 4, 5, 6];

// Template Literals
const mensagem = `
    Bem-vindo, ${nome}!
    Seu email é: ${email}
`;

// Async/Await
async function buscarDados() {
    try {
        const response = await fetch(\'/api/dados\');
        const dados = await response.json();
        console.log(dados);
    } catch (erro) {
        console.error(\'Erro:\', erro);
    }
}

// Promises
const promessa = new Promise((resolve, reject) => {
    setTimeout(() => {
        resolve(\'Dados carregados!\');
    }, 1000);
});

promessa.then(resultado => console.log(resultado));

// Map, Filter, Reduce
const valores = [1, 2, 3, 4, 5];
const dobrados = valores.map(n => n * 2);
const pares = valores.filter(n => n % 2 === 0);
const soma = valores.reduce((acc, n) => acc + n, 0);';
                                                        } elseif (stripos($item['title'], 'DOM') !== false || $item['id'] == 8) {
                                                            $codeExample = '// Manipulação do DOM

// Seleção de elementos
const titulo = document.querySelector(\'h1\');
const botoes = document.querySelectorAll(\'.btn\');
const formulario = document.getElementById(\'meuForm\');

// Modificar conteúdo
titulo.textContent = \'Novo Título\';
titulo.innerHTML = \'<strong>Título em Negrito</strong>\';

// Adicionar classes
titulo.classList.add(\'destaque\');
titulo.classList.toggle(\'ativo\');
titulo.classList.remove(\'antigo\');

// Criar elementos
const novoDiv = document.createElement(\'div\');
novoDiv.className = \'card\';
novoDiv.innerHTML = `
    <h3>Novo Card</h3>
    <p>Criado dinamicamente</p>
`;

document.body.appendChild(novoDiv);

// Eventos
botoes.forEach(btn => {
    btn.addEventListener(\'click\', (e) => {
        console.log(\'Botão clicado!\', e.target);
    });
});

// Event Delegation
document.addEventListener(\'click\', (e) => {
    if (e.target.matches(\'.deletar\')) {
        e.target.closest(\'.item\').remove();
    }
});

// Formulários
formulario.addEventListener(\'submit\', (e) => {
    e.preventDefault();
    const dados = new FormData(formulario);
    console.log(Object.fromEntries(dados));
});';
                                                        } else {
                                                            $codeExample = '// Exemplo de ' . htmlspecialchars($item['title']) . '

// Função principal
function executar() {
    console.log("Exemplo de JavaScript");
    
    // Seu código aqui
    const dados = {
        titulo: "' . addslashes($item['title']) . '",
        status: "ativo"
    };
    
    return dados;
}

// Executar
executar();';
                                                        }
                                                    } elseif ($item['category'] === 'PHP') {
                                                        if (stripos($item['title'], 'Fundamental') !== false || $item['id'] == 6) {
                                                            $codeExample = '<?php
// PHP Fundamentals

// Variáveis e tipos
$nome = "João";
$idade = 25;
$ativo = true;
$preco = 99.90;

// Arrays
$frutas = ["maçã", "banana", "laranja"];
$usuario = [
    "nome" => "Maria",
    "email" => "maria@example.com",
    "idade" => 30
];

// Estruturas de controle
if ($idade >= 18) {
    echo "Maior de idade";
} else {
    echo "Menor de idade";
}

// Loops
foreach ($frutas as $fruta) {
    echo $fruta . "<br>";
}

// Funções
function saudacao($nome) {
    return "Olá, " . $nome . "!";
}

echo saudacao($nome);

// Classes e Objetos
class Usuario {
    private $nome;
    private $email;
    
    public function __construct($nome, $email) {
        $this->nome = $nome;
        $this->email = $email;
    }
    
    public function getNome() {
        return $this->nome;
    }
}

$user = new Usuario("João", "joao@example.com");

// Processamento de formulário
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $dados = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    // Processar dados
}
?>';
                                                        } else {
                                                            $codeExample = '<?php
// Exemplo de ' . htmlspecialchars($item['title']) . '

// Configuração
require_once \'config.php\';

// Função principal
function processar() {
    $resultado = [
        "status" => "sucesso",
        "mensagem" => "Operação concluída"
    ];
    
    return $resultado;
}

// Executar
$dados = processar();
echo json_encode($dados);
?>';
                                                        }
                                                    } else {
                                                        $codeExample = '// Exemplo de código para ' . htmlspecialchars($item['title']) . '

console.log("Tutorial em desenvolvimento");';
                                                    }
                                                    
                                                    echo htmlspecialchars($codeExample);
                                                    ?></code></pre>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="content-block">
                                            <div class="alert alert-success">
                                                <i class="fas fa-play-circle me-2"></i>
                                                <strong>Comece agora!</strong> Este tutorial está pronto para você começar a aprender. 
                                                Pratique o exemplo acima e explore os conceitos apresentados.
                                            </div>
                                        </div>
                                    </div>
                                </section>

                            <?php else: ?>
                                <section class="content-section">
                                    <h3 class="section-title">🎯 Descrição do Exercício</h3>
                                    <div class="exercise-description-box">
                                        <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                                    </div>
                                    
                                    <div class="exercise-instructions mt-4">
                                        <h4 class="instruction-title">
                                            <i class="fas fa-list-ol text-primary me-2"></i>
                                            Como realizar este exercício
                                        </h4>
                                        <div class="instruction-steps">
                                            <div class="instruction-step">
                                                <div class="step-number">1</div>
                                                <div class="step-content">
                                                    <h5>Leia o enunciado com atenção</h5>
                                                    <p>Entenda completamente o que é solicitado antes de começar a programar</p>
                                                </div>
                                            </div>
                                            <div class="instruction-step">
                                                <div class="step-number">2</div>
                                                <div class="step-content">
                                                    <h5>Planeje sua solução</h5>
                                                    <p>Pense na estrutura e lógica antes de escrever o código</p>
                                                </div>
                                            </div>
                                            <div class="instruction-step">
                                                <div class="step-number">3</div>
                                                <div class="step-content">
                                                    <h5>Implemente e teste</h5>
                                                    <p>Desenvolva a solução e teste em diferentes cenários</p>
                                                </div>
                                            </div>
                                            <div class="instruction-step">
                                                <div class="step-number">4</div>
                                                <div class="step-content">
                                                    <h5>Refine seu código</h5>
                                                    <p>Melhore a qualidade e siga as boas práticas</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-success mt-4">
                                        <i class="fas fa-play-circle me-2"></i>
                                        <strong>Pronto para começar?</strong> Clique no botão "Iniciar Exercício" na barra lateral para começar a praticar!
                                    </div>
                                </section>
                            <?php endif; ?>
                        </div>

                        <!-- Aba de Objetivos (Exercícios) -->
                        <?php if ($type === 'exercise'): ?>
                        <div class="tab-pane fade" id="objectives" role="tabpanel">
                            <div class="objectives-grid">
                                <?php foreach ($item['objectives'] as $objective): ?>
                                <div class="objective-item">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span><?php echo $objective; ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="technologies-section mt-4">
                                <h4 class="section-title">🛠 Tecnologias Utilizadas</h4>
                                <div class="technologies-grid">
                                    <?php foreach ($item['technologies'] as $tech): ?>
                                    <div class="tech-badge">
                                        <i class="fas fa-cog me-1"></i>
                                        <?php echo $tech; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Aba de Recursos -->
                        <div class="tab-pane fade" id="resources" role="tabpanel">
                            <div class="resources-grid">
                                <?php
                                // Definir recursos baseados na categoria
                                $category = $item['category'];
                                $resources = [];
                                
                                if ($category === 'HTML') {
                                    $resources = [
                                        [
                                            'icon' => 'fab fa-html5 text-danger',
                                            'title' => 'MDN Web Docs - HTML',
                                            'description' => 'Documentação oficial completa de HTML',
                                            'url' => 'https://developer.mozilla.org/pt-BR/docs/Web/HTML',
                                            'type' => 'external'
                                        ],
                                        [
                                            'icon' => 'fas fa-book text-primary',
                                            'title' => 'Guia HTML5',
                                            'description' => 'Guia completo de HTML5 semântico',
                                            'url' => 'https://www.w3schools.com/html/',
                                            'type' => 'external'
                                        ],
                                        [
                                            'icon' => 'fas fa-video text-danger',
                                            'title' => 'Vídeos HTML',
                                            'description' => 'Tutoriais em vídeo sobre HTML',
                                            'url' => 'https://www.youtube.com/results?search_query=html5+tutorial+portugues',
                                            'type' => 'external'
                                        ],
                                        [
                                            'icon' => 'fas fa-code text-success',
                                            'title' => 'Exemplos Práticos',
                                            'description' => 'Galeria de exemplos de código HTML',
                                            'url' => 'https://www.w3schools.com/html/html_examples.asp',
                                            'type' => 'external'
                                        ],
                                        [
                                            'icon' => 'fas fa-tasks text-warning',
                                            'title' => 'Exercícios HTML',
                                            'description' => 'Pratique com exercícios interativos',
                                            'url' => 'exercises_index.php?category=HTML',
                                            'type' => 'internal'
                                        ],
                                        [
                                            'icon' => 'fas fa-comments text-info',
                                            'title' => 'Fórum HTML',
                                            'description' => 'Discussões e dúvidas sobre HTML',
                                            'url' => 'forum_index.php?category=HTML',
                                            'type' => 'internal'
                                        ]
                                    ];
                                } elseif ($category === 'CSS') {
                                    $resources = [
                                        [
                                            'icon' => 'fab fa-css3-alt text-primary',
                                            'title' => 'MDN Web Docs - CSS',
                                            'description' => 'Documentação oficial completa de CSS',
                                            'url' => 'https://developer.mozilla.org/pt-BR/docs/Web/CSS',
                                            'type' => 'external'
                                        ],
                                        [
                                            'icon' => 'fas fa-palette text-info',
                                            'title' => 'CSS Tricks',
                                            'description' => 'Dicas e truques avançados de CSS',
                                            'url' => 'https://css-tricks.com/',
                                            'type' => 'external'
                                        ],
                                        [
                                            'icon' => 'fas fa-video text-danger',
                                            'title' => 'Vídeos CSS',
                                            'description' => 'Tutoriais em vídeo sobre CSS',
                                            'url' => 'https://www.youtube.com/results?search_query=css3+tutorial+portugues',
                                            'type' => 'external'
                                        ],
                                        [
                                            'icon' => 'fas fa-th text-success',
                                            'title' => 'CSS Grid Garden',
                                            'description' => 'Aprenda Grid Layout jogando',
                                            'url' => 'https://cssgridgarden.com/#pt-br',
                                            'type' => 'external'
                                        ],
                                        [
                                            'icon' => 'fas fa-frog text-warning',
                                            'title' => 'Flexbox Froggy',
                                            'description' => 'Aprenda Flexbox jogando',
                                            'url' => 'https://flexboxfroggy.com/#pt-br',
                                            'type' => 'external'
                                        ],
                                        [
                                            'icon' => 'fas fa-tasks text-primary',
                                            'title' => 'Exercícios CSS',
                                            'description' => 'Pratique com exercícios interativos',
                                            'url' => 'exercises_index.php?category=CSS',
                                            'type' => 'internal'
                                        ]
                                    ];
                                } elseif ($category === 'JavaScript') {
                                    $resources = [
                                        [
                                            'icon' => 'fab fa-js-square text-warning',
                                            'title' => 'MDN Web Docs - JavaScript',
                                            'description' => 'Documentação oficial completa de JS',
                                            'url' => 'https://developer.mozilla.org/pt-BR/docs/Web/JavaScript',
                                            'type' => 'external'
                                        ],
                                        [
                                            'icon' => 'fas fa-book-open text-success',
                                            'title' => 'JavaScript.info',
                                            'description' => 'Tutorial moderno de JavaScript',
                                            'url' => 'https://javascript.info/',
                                            'type' => 'external'
                                        ],
                                        [
                                            'icon' => 'fas fa-video text-danger',
                                            'title' => 'Vídeos JavaScript',
                                            'description' => 'Tutoriais em vídeo sobre JS',
                                            'url' => 'https://www.youtube.com/results?search_query=javascript+tutorial+portugues',
                                            'type' => 'external'
                                        ],
                                        [
                                            'icon' => 'fas fa-gamepad text-info',
                                            'title' => 'Exercícios Interativos',
                                            'description' => 'Pratique JavaScript jogando',
                                            'url' => 'https://www.freecodecamp.org/learn/javascript-algorithms-and-data-structures/',
                                            'type' => 'external'
                                        ],
                                        [
                                            'icon' => 'fas fa-tasks text-primary',
                                            'title' => 'Exercícios JavaScript',
                                            'description' => 'Pratique com exercícios',
                                            'url' => 'exercises_index.php?category=JavaScript',
                                            'type' => 'internal'
                                        ],
                                        [
                                            'icon' => 'fab fa-github text-dark',
                                            'title' => 'Projetos no GitHub',
                                            'description' => 'Exemplos de código no GitHub',
                                            'url' => 'https://github.com/topics/javascript',
                                            'type' => 'external'
                                        ]
                                    ];
                                } elseif ($category === 'PHP') {
                                    $resources = [
                                        [
                                            'icon' => 'fab fa-php text-info',
                                            'title' => 'PHP Manual',
                                            'description' => 'Documentação oficial do PHP',
                                            'url' => 'https://www.php.net/manual/pt_BR/',
                                            'type' => 'external'
                                        ],
                                        [
                                            'icon' => 'fas fa-book text-primary',
                                            'title' => 'PHP: The Right Way',
                                            'description' => 'Boas práticas de PHP moderno',
                                            'url' => 'https://phptherightway.com/pages/The-Basics.html',
                                            'type' => 'external'
                                        ],
                                        [
                                            'icon' => 'fas fa-video text-danger',
                                            'title' => 'Vídeos PHP',
                                            'description' => 'Tutoriais em vídeo sobre PHP',
                                            'url' => 'https://www.youtube.com/results?search_query=php+tutorial+portugues',
                                            'type' => 'external'
                                        ],
                                        [
                                            'icon' => 'fas fa-code text-success',
                                            'title' => 'Exemplos de Código',
                                            'description' => 'Galeria de exemplos PHP',
                                            'url' => 'https://www.w3schools.com/php/php_examples.asp',
                                            'type' => 'external'
                                        ],
                                        [
                                            'icon' => 'fas fa-tasks text-warning',
                                            'title' => 'Exercícios PHP',
                                            'description' => 'Pratique com exercícios',
                                            'url' => 'exercises_index.php?category=PHP',
                                            'type' => 'internal'
                                        ],
                                        [
                                            'icon' => 'fab fa-laravel text-danger',
                                            'title' => 'Framework Laravel',
                                            'description' => 'Aprenda Laravel (avançado)',
                                            'url' => 'https://laravel.com/docs',
                                            'type' => 'external'
                                        ]
                                    ];
                                } else {
                                    // Recursos genéricos
                                    $resources = [
                                        [
                                            'icon' => 'fas fa-book text-primary',
                                            'title' => 'Documentação',
                                            'description' => 'Referência oficial completa',
                                            'url' => 'https://developer.mozilla.org/pt-BR/',
                                            'type' => 'external'
                                        ],
                                        [
                                            'icon' => 'fas fa-video text-danger',
                                            'title' => 'Vídeo Aulas',
                                            'description' => 'Tutoriais em vídeo',
                                            'url' => 'https://www.youtube.com/results?search_query=web+development+tutorial',
                                            'type' => 'external'
                                        ],
                                        [
                                            'icon' => 'fas fa-tasks text-success',
                                            'title' => 'Exercícios',
                                            'description' => 'Pratique seus conhecimentos',
                                            'url' => 'exercises_index.php',
                                            'type' => 'internal'
                                        ],
                                        [
                                            'icon' => 'fas fa-comments text-info',
                                            'title' => 'Comunidade',
                                            'description' => 'Fórum de discussões',
                                            'url' => 'forum_index.php',
                                            'type' => 'internal'
                                        ]
                                    ];
                                }
                                
                                foreach ($resources as $resource):
                                ?>
                                <div class="resource-card">
                                    <i class="<?php echo $resource['icon']; ?>"></i>
                                    <h5><?php echo htmlspecialchars($resource['title']); ?></h5>
                                    <p><?php echo htmlspecialchars($resource['description']); ?></p>
                                    <a href="<?php echo $resource['url']; ?>" 
                                       class="btn btn-outline-primary btn-sm" 
                                       <?php echo $resource['type'] === 'external' ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                        <?php echo $resource['type'] === 'external' ? '<i class="fas fa-external-link-alt me-1"></i>Acessar' : '<i class="fas fa-arrow-right me-1"></i>Ver'; ?>
                                    </a>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="mt-4 p-3 bg-light rounded">
                                <h5 class="mb-3">
                                    <i class="fas fa-lightbulb text-warning me-2"></i>
                                    Dica de Estudo
                                </h5>
                                <p class="mb-2">
                                    <strong>Para aprender efetivamente:</strong>
                                </p>
                                <ul class="mb-0">
                                    <li>📚 Leia a documentação oficial primeiro</li>
                                    <li>💻 Pratique com exemplos de código</li>
                                    <li>🎯 Faça os exercícios práticos</li>
                                    <li>🎥 Assista vídeos para reforçar conceitos</li>
                                    <li>👥 Participe das discussões no fórum</li>
                                    <li>🚀 Crie seus próprios projetos</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Nova Aba de Comunidade -->
                        <div class="tab-pane fade" id="community" role="tabpanel">
                            <div class="community-section">
                                <div class="discussion-forum mb-4">
                                    <h4 class="section-title">💬 Discussões</h4>
                                    <div class="discussion-list">
                                        <div class="discussion-item">
                                            <div class="discussion-header">
                                                <img src="https://ui-avatars.com/api/?name=João+Silva&background=random" alt="João Silva" class="user-avatar">
                                                <div class="user-info">
                                                    <h6>João Silva</h6>
                                                    <span class="discussion-date">há 2 horas</span>
                                                </div>
                                            </div>
                                            <div class="discussion-content">
                                                <p>Alguém pode me ajudar com a parte de validação do formulário? Não estou conseguindo fazer funcionar.</p>
                                            </div>
                                            <div class="discussion-actions">
                                                <button class="btn btn-sm btn-outline-secondary">
                                                    <i class="far fa-thumbs-up"></i> 5
                                                </button>
                                                <button class="btn btn-sm btn-outline-secondary">
                                                    <i class="far fa-comment"></i> Responder
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="discussion-item">
                                            <div class="discussion-header">
                                                <img src="https://ui-avatars.com/api/?name=Maria+Santos&background=random" alt="Maria Santos" class="user-avatar">
                                                <div class="user-info">
                                                    <h6>Maria Santos</h6>
                                                    <span class="discussion-date">há 1 dia</span>
                                                </div>
                                            </div>
                                            <div class="discussion-content">
                                                <p>Excelente exercício! Aprendi muito sobre estrutura semântica. Alguém tem dicas para melhorar a acessibilidade?</p>
                                            </div>
                                            <div class="discussion-actions">
                                                <button class="btn btn-sm btn-outline-secondary">
                                                    <i class="far fa-thumbs-up"></i> 12
                                                </button>
                                                <button class="btn btn-sm btn-outline-secondary">
                                                    <i class="far fa-comment"></i> Responder
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="add-discussion mt-3">
                                        <textarea class="form-control" placeholder="Participe da discussão..."></textarea>
                                        <div class="mt-2 text-end">
                                            <button class="btn btn-primary">Enviar</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="user-solutions">
                                    <h4 class="section-title">💡 Soluções da Comunidade</h4>
                                    <div class="solutions-grid">
                                        <div class="solution-card">
                                            <div class="solution-header">
                                                <img src="https://ui-avatars.com/api/?name=Carlos+Almeida&background=random" alt="Carlos Almeida" class="user-avatar">
                                                <div class="user-info">
                                                    <h6>Carlos Almeida</h6>
                                                    <span class="solution-date">Solução enviada há 3 dias</span>
                                                </div>
                                            </div>
                                            <div class="solution-preview">
                                                <pre><code>&lt;!-- Solução limpa e semântica --&gt;</code></pre>
                                            </div>
                                            <div class="solution-actions">
                                                <span class="rating">
                                                    <i class="fas fa-star text-warning"></i> 4.8
                                                </span>
                                                <button class="btn btn-sm btn-outline-primary">Ver Solução</button>
                                            </div>
                                        </div>
                                        
                                        <div class="solution-card">
                                            <div class="solution-header">
                                                <img src="https://ui-avatars.com/api/?name=Ana+Costa&background=random" alt="Ana Costa" class="user-avatar">
                                                <div class="user-info">
                                                    <h6>Ana Costa</h6>
                                                    <span class="solution-date">Solução enviada há 1 semana</span>
                                                </div>
                                            </div>
                                            <div class="solution-preview">
                                                <pre><code>&lt;!-- Abordagem criativa --&gt;</code></pre>
                                            </div>
                                            <div class="solution-actions">
                                                <span class="rating">
                                                    <i class="fas fa-star text-warning"></i> 4.5
                                                </span>
                                                <button class="btn btn-sm btn-outline-primary">Ver Solução</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Card de Ações -->
            <div class="action-card mb-4">
                <div class="action-buttons">
                    <a href="<?php echo $type === 'tutorial' ? 'tutorials_index.php' : 'exercises_index.php'; ?>" 
                       class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-arrow-left me-2"></i>Voltar para Lista
                    </a>
                    
                    <?php if (!$preview): ?>
                        <?php if ($type === 'exercise'): ?>
                            <button class="btn btn-success btn-block mt-2" id="startExercise">
                                <i class="fas fa-play me-2"></i>Iniciar Exercício
                            </button>
                            <button class="btn btn-outline-success btn-block mt-2" id="saveProgress">
                                <i class="fas fa-save me-2"></i>Salvar Progresso
                            </button>
                        <?php else: ?>
                            <div class="btn-group w-100 mt-2" role="group">
                                <button class="btn btn-primary" id="markComplete">
                                    <i class="fas fa-check me-2"></i>Marcar como Concluído
                                </button>
                            </div>
                            <div class="btn-group w-100 mt-2" role="group">
                                <button class="btn btn-outline-primary" id="favoriteBtn">
                                    <i class="far fa-heart me-2"></i>Favoritar
                                </button>
                                <button class="btn btn-outline-primary" id="shareBtn">
                                    <i class="fas fa-share-alt me-2"></i>Compartilhar
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card de Informações -->
            <div class="info-card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações Detalhadas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <span class="info-label">Categoria:</span>
                        <span class="info-value"><?php echo sanitize($item['category']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Dificuldade:</span>
                        <span class="info-value"><?php echo sanitize($item['difficulty']); ?></span>
                    </div>
                    
                    <?php if ($type === 'tutorial'): ?>
                    <div class="info-item">
                        <span class="info-label">Duração:</span>
                        <span class="info-value"><?php echo sanitize($item['duration']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status:</span>
                        <span class="info-value status-<?php echo strtolower($item['status']); ?>">
                            <?php echo sanitize($item['status']); ?>
                        </span>
                    </div>
                    <?php else: ?>
                    <div class="info-item">
                        <span class="info-label">Tempo Estimado:</span>
                        <span class="info-value"><?php echo $item['estimated_time']; ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="info-item">
                        <span class="info-label">Avaliação:</span>
                        <span class="info-value">
                            <?php
                            $rating = isset($item['rating']) ? $item['rating'] : 4.5;
                            $fullStars = floor($rating);
                            $hasHalfStar = $rating - $fullStars >= 0.5;
                            
                            for ($i = 0; $i < $fullStars; $i++) {
                                echo '<i class="fas fa-star text-warning"></i>';
                            }
                            if ($hasHalfStar) {
                                echo '<i class="fas fa-star-half-alt text-warning"></i>';
                            }
                            $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                            for ($i = 0; $i < $emptyStars; $i++) {
                                echo '<i class="far fa-star text-warning"></i>';
                            }
                            ?>
                            (<?php echo $rating; ?>)
                        </span>
                    </div>
                    
                    <?php if (isset($item['author'])): ?>
                    <div class="info-item">
                        <span class="info-label">Autor:</span>
                        <span class="info-value"><?php echo $item['author']; ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="info-item">
                        <span class="info-label">Atualizado:</span>
                        <span class="info-value"><?php echo isset($item['last_updated']) ? date('d/m/Y', strtotime($item['last_updated'])) : 'Recentemente'; ?></span>
                    </div>
                </div>
            </div>

            <!-- Card de Progresso -->
            <div class="progress-card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Seu Progresso
                    </h6>
                </div>
                <div class="card-body">
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: <?php echo $user_progress; ?>%"></div>
                    </div>
                    <div class="progress-stats">
                        <span><?php echo $user_progress; ?>% completo</span>
                        <span><?php echo $time_remaining; ?></span>
                    </div>
                    <?php if ($user_progress < 100): ?>
                    <button class="btn btn-outline-primary btn-sm w-100 mt-2" id="continueBtn">
                        <i class="fas fa-play me-1"></i> Continuar
                    </button>
                    <?php else: ?>
                    <button class="btn btn-success btn-sm w-100 mt-2">
                        <i class="fas fa-check me-1"></i> Concluído
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card de Conteúdo Relacionado -->
            <div class="related-card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-layer-group me-2"></i>
                        Conteúdo Relacionado
                    </h6>
                </div>
                <div class="card-body">
                    <div class="related-list">
                        <?php
                        // Buscar conteúdo relacionado
                        if ($type === 'tutorial') {
                            $allTutorials = getTutorials();
                            $relatedItems = array_filter($allTutorials, function($t) use ($item) {
                                return $t['id'] !== $item['id'] && 
                                       ($t['category'] === $item['category'] || $t['level'] === $item['level']);
                            });
                            $relatedItems = array_slice($relatedItems, 0, 4);
                            
                            foreach ($relatedItems as $related):
                                $iconClass = match($related['category']) {
                                    'HTML' => 'fab fa-html5 text-danger',
                                    'CSS' => 'fab fa-css3-alt text-primary',
                                    'JavaScript' => 'fab fa-js-square text-warning',
                                    'PHP' => 'fab fa-php text-info',
                                    default => 'fas fa-book text-secondary'
                                };
                        ?>
                            <a href="show.php?type=tutorial&id=<?php echo $related['id']; ?>" class="related-item">
                                <div class="related-icon">
                                    <i class="<?php echo $iconClass; ?>"></i>
                                </div>
                                <div class="related-content">
                                    <h6><?php echo htmlspecialchars($related['title']); ?></h6>
                                    <span class="text-muted">Tutorial • <?php echo $related['duration']; ?></span>
                                </div>
                            </a>
                        <?php
                            endforeach;
                        } else {
                            // Exercícios relacionados
                            $allExercises = $dbConnector->getExercises('', '', '', 1, 50);
                            $relatedItems = array_filter($allExercises, function($e) use ($item) {
                                return $e['id'] !== $item['id'] && 
                                       ($e['category'] === $item['category'] || $e['difficulty'] === $item['difficulty']);
                            });
                            $relatedItems = array_slice($relatedItems, 0, 4);
                            
                            foreach ($relatedItems as $related):
                                $iconClass = match($related['category']) {
                                    'HTML' => 'fab fa-html5 text-danger',
                                    'CSS' => 'fab fa-css3-alt text-primary',
                                    'JavaScript' => 'fab fa-js-square text-warning',
                                    'PHP' => 'fab fa-php text-info',
                                    default => 'fas fa-dumbbell text-secondary'
                                };
                        ?>
                            <a href="show.php?type=exercise&id=<?php echo $related['id']; ?>" class="related-item">
                                <div class="related-icon">
                                    <i class="<?php echo $iconClass; ?>"></i>
                                </div>
                                <div class="related-content">
                                    <h6><?php echo htmlspecialchars($related['title']); ?></h6>
                                    <span class="text-muted">Exercício • <?php echo $related['difficulty']; ?></span>
                                </div>
                            </a>
                        <?php
                            endforeach;
                        }
                        ?>
                        
                        <?php if (empty($relatedItems)): ?>
                            <div class="text-muted text-center py-3">
                                <i class="fas fa-info-circle me-1"></i>
                                Nenhum conteúdo relacionado disponível no momento.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Card de Estatísticas -->
            <div class="stats-card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Estatísticas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo isset($item['downloads']) ? $item['downloads'] : '1.2k'; ?></div>
                            <div class="stat-label">Downloads</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo isset($item['rating_count']) ? $item['rating_count'] : '245'; ?></div>
                            <div class="stat-label">Avaliações</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo isset($item['views']) ? $item['views'] : '3.4k'; ?></div>
                            <div class="stat-label">Visualizações</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">87%</div>
                            <div class="stat-label">Taxa de Conclusão</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --primary-color: #4361ee;
    --secondary-color: #3a0ca3;
    --success-color: #4cc9f0;
    --warning-color: #f72585;
    --text-heading: #2b2d42;
    --text-body: #4a4c5e;
    --bg-light: #f8f9fa;
    --border-radius: 12px;
    --shadow: 0 10px 30px rgba(0,0,0,0.08);
    --transition: all 0.3s ease;
}

/* Estilos para a página de detalhes */
.content-header-card {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    padding: 2.5rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    position: relative;
    overflow: hidden;
}

.content-header-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    transform: translate(30%, -30%);
}

.content-type-badge {
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.875rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

.content-type-badge.tutorial {
    background: rgba(255,255,255,0.2);
    color: white;
}

.content-type-badge.exercise {
    background: rgba(40, 167, 69, 0.2);
    color: #28a745;
}

.difficulty-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.difficulty-iniciante { background: #d4edda; color: #155724; }
.difficulty-intermediário { background: #fff3cd; color: #856404; }
.difficulty-avançado { background: #f8d7da; color: #721c24; }

.rating-badge, .downloads-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    background: rgba(255,255,255,0.2);
    color: white;
}

.tags-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.tag {
    padding: 0.3rem 0.8rem;
    background: rgba(255,255,255,0.2);
    border-radius: 15px;
    font-size: 0.8rem;
    color: white;
}

.metadata-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 1.5rem;
}

.metadata-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.metadata-item i {
    width: 16px;
    text-align: center;
}

.header-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.2);
    color: white;
    border: none;
    transition: var(--transition);
}

.btn-icon:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-2px);
}

.main-content-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.content-navigation .nav-tabs {
    padding: 0 1.5rem;
    border-bottom: 1px solid #e9ecef;
    background: var(--bg-light);
}

.content-navigation .nav-link {
    border: none;
    padding: 1rem 1.5rem;
    color: #6c757d;
    font-weight: 500;
    transition: var(--transition);
}

.content-navigation .nav-link:hover {
    color: var(--primary-color);
}

.content-navigation .nav-link.active {
    color: var(--primary-color);
    border-bottom: 3px solid var(--primary-color);
    background: transparent;
}

.content-section {
    padding: 0 1.5rem 2rem;
}

.tutorial-content,
.exercise-description-box {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
}

.content-block {
    margin-bottom: 2rem;
}

.content-block h4 {
    color: var(--text-heading);
    margin-bottom: 1rem;
    font-size: 1.1rem;
    font-weight: 600;
}

.topics-list {
    list-style: none;
    padding: 0;
}

.topics-list li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.topics-list li:last-child {
    border-bottom: none;
}

.code-example-container {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    overflow: hidden;
    background: #1e1e1e;
    margin: 1rem 0;
}

.code-example-header {
    background: #2d2d2d;
    padding: 0.75rem 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #3e3e3e;
}

.code-example-title {
    color: #cccccc;
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    font-weight: 500;
}

.code-example-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-code-action {
    background: transparent;
    border: 1px solid #4a4a4a;
    color: #cccccc;
    padding: 0.25rem 0.75rem;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.85rem;
}

.btn-code-action:hover {
    background: #3a3a3a;
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.code-example-body {
    padding: 1.5rem;
    overflow-x: auto;
}

.code-example-body pre {
    margin: 0;
    font-family: 'Courier New', Consolas, monospace;
    font-size: 0.9rem;
    line-height: 1.6;
}

.code-example-body code {
    color: #d4d4d4;
    white-space: pre;
    display: block;
}

/* Syntax highlighting colors */
.language-html .keyword { color: #569cd6; }
.language-css .keyword { color: #c586c0; }
.language-javascript .keyword { color: #569cd6; }
.language-php .keyword { color: #c586c0; }

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: var(--text-heading);
    display: flex;
    align-items: center;
}

.learning-objectives {
    display: grid;
    gap: 1rem;
}

.objective-card {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid var(--primary-color);
    transition: var(--transition);
}

.objective-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.objective-card i {
    font-size: 1.5rem;
    margin-top: 0.25rem;
}

.objective-card h5 {
    margin-bottom: 0.5rem;
    color: var(--text-heading);
}

.objective-card p {
    margin: 0;
    color: #6c757d;
}

.prerequisites-grid {
    display: grid;
    gap: 0.75rem;
}

.prerequisite-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: 8px;
    background: #f8f9fa;
}

.prerequisite-item.required i { color: #28a745; }
.prerequisite-item.recommended i { color: #ffc107; }
.prerequisite-item.optional i { color: #6c757d; }

.content-steps {
    display: grid;
    gap: 1rem;
}

.content-step {
    display: flex;
    gap: 1rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 10px;
    transition: var(--transition);
}

.content-step:hover {
    background: #e9ecef;
}

.content-step.active {
    background: var(--primary-color);
    color: white;
}

.content-step.active h5,
.content-step.active p,
.content-step.active .step-duration {
    color: white;
}

.content-step.completed {
    background: #d4edda;
}

.step-indicator {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    flex-shrink: 0;
}

.content-step.active .step-indicator {
    background: white;
    color: var(--primary-color);
}

.content-step.completed .step-indicator {
    background: #28a745;
}

.step-content {
    flex-grow: 1;
}

.step-content h5 {
    margin-bottom: 0.5rem;
    color: var(--text-heading);
}

.content-step.active h5 {
    color: white;
}

.step-content p {
    margin: 0;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.step-duration {
    font-size: 0.8rem;
    color: #6c757d;
    font-weight: 500;
}

.exercise-instructions {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1.5rem;
}

.instruction-steps {
    display: grid;
    gap: 1.5rem;
}

.instruction-step {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.step-number {
    width: 32px;
    height: 32px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.step-content h5 {
    margin-bottom: 0.5rem;
    color: var(--text-heading);
}

.step-content p {
    margin: 0;
    color: #6c757d;
}

.code-editor-container {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.code-editor {
    background: #1e1e1e;
    color: #d4d4d4;
}

.editor-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1rem;
    background: #2d2d2d;
    border-bottom: 1px solid #3e3e3e;
}

.editor-header span {
    color: #cccccc;
    font-weight: 500;
}

.editor-actions {
    display: flex;
    gap: 0.5rem;
}

.editor-content {
    padding: 1rem;
}

.editor-content pre {
    margin: 0;
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
}

.objectives-grid {
    display: grid;
    gap: 0.75rem;
}

.objective-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.technologies-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.tech-badge {
    padding: 0.5rem 1rem;
    background: var(--primary-color);
    color: white;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 1rem;
    padding: 1rem;
}

.resource-card {
    text-align: center;
    padding: 1.25rem;
    background: #f8f9fa;
    border-radius: 10px;
    border: 2px solid transparent;
    transition: var(--transition);
    min-height: 220px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.resource-card:hover {
    border-color: var(--primary-color);
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.resource-card i {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.resource-card h5 {
    margin-bottom: 0.5rem;
    color: var(--text-heading);
}

.resource-card p {
    color: #6c757d;
    margin-bottom: 1rem;
}

/* Estilos da Comunidade */
.community-section {
    padding: 0 1rem;
}

.add-discussion-form,
.add-solution-form {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 10px;
    border: 2px dashed var(--primary-color);
}

.discussion-item {
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 1rem;
}

.discussion-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.user-info h6 {
    margin: 0;
    color: var(--text-heading);
}

.discussion-date {
    font-size: 0.8rem;
    color: #6c757d;
}

.discussion-content p {
    margin: 0;
    color: var(--text-body);
}

.discussion-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.solutions-grid {
    display: grid;
    gap: 1rem;
}

.solution-card {
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.solution-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.solution-preview {
    background: #1e1e1e;
    border-radius: 5px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.solution-preview pre {
    margin: 0;
    color: #d4d4d4;
    font-family: 'Courier New', monospace;
    font-size: 0.8rem;
}

.solution-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* Sidebar Styles */
.action-card {
    background: white;
    padding: 1.5rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

.action-buttons .btn-block {
    width: 100%;
}

.info-card, .progress-card, .related-card, .stats-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #6c757d;
    flex: 1;
}

.info-value {
    font-weight: 500;
    color: var(--text-heading);
}

.status-publicado { color: #28a745; }
.status-rascunho { color: #ffc107; }

.progress-stats {
    display: flex;
    justify-content: space-between;
    font-size: 0.875rem;
    color: #6c757d;
}

.related-list {
    display: grid;
    gap: 0.75rem;
}

.related-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 8px;
    text-decoration: none;
    color: inherit;
    transition: var(--transition);
    border: 1px solid transparent;
}

.related-item:hover {
    background: #f8f9fa;
    border-color: var(--primary-color);
    text-decoration: none;
    color: inherit;
    transform: translateX(5px);
}

.related-icon {
    width: 40px;
    height: 40px;
    background: #f8f9fa;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.related-content h6 {
    margin: 0;
    font-size: 0.9rem;
    color: var(--text-heading);
}

.related-content span {
    font-size: 0.8rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.8rem;
    color: #6c757d;
}

/* Responsividade */
@media (max-width: 768px) {
    .content-header-card {
        padding: 1.5rem;
    }
    
    .metadata-grid {
        grid-template-columns: 1fr;
    }
    
    .content-navigation .nav-tabs {
        padding: 0 1rem;
        overflow-x: auto;
        flex-wrap: nowrap;
    }
    
    .content-section {
        padding: 0 1rem 1.5rem;
    }
    
    .resources-grid {
        grid-template-columns: 1fr;
    }
    
    .header-actions {
        position: absolute;
        top: 1rem;
        right: 1rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
}

/* Animações */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.content-header-card,
.main-content-card,
.action-card,
.info-card,
.progress-card,
.related-card,
.stats-card {
    animation: fadeIn 0.6s ease-out;
}

/* Scroll personalizado */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--secondary-color);
}
</style>

<script>
// Variáveis globais
const contentType = '<?php echo $type; ?>';
const contentId = <?php echo $id; ?>;
const isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;

document.addEventListener('DOMContentLoaded', function() {
    // Carrega discussões e soluções ao abrir a aba de comunidade
    const communityTab = document.querySelector('[data-bs-target="#community"]');
    if (communityTab) {
        communityTab.addEventListener('shown.bs.tab', function() {
            loadDiscussions();
            loadSolutions();
        });
    }
    
    // Sistema de abas
    const tabTriggers = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            // Atualiza estado ativo
            tabTriggers.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Animação de progresso
    const progressBar = document.querySelector('.progress-bar');
    if (progressBar) {
        setTimeout(() => {
            progressBar.style.transition = 'width 2s ease-in-out';
        }, 500);
    }

    // Tooltips para ícones
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Interatividade dos botões
    const favoriteBtn = document.getElementById('favoriteBtn');
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (icon.classList.contains('far')) {
                icon.classList.replace('far', 'fas');
                this.classList.add('btn-primary');
                this.classList.remove('btn-outline-primary');
                showToast('Adicionado aos favoritos!', 'success');
            } else {
                icon.classList.replace('fas', 'far');
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline-primary');
                showToast('Removido dos favoritos!', 'info');
            }
        });
    }
    
    const shareBtn = document.getElementById('shareBtn');
    if (shareBtn) {
        shareBtn.addEventListener('click', function() {
            if (navigator.share) {
                navigator.share({
                    title: document.title,
                    url: window.location.href
                });
            } else {
                // Fallback para copiar para a área de transferência
                navigator.clipboard.writeText(window.location.href);
                showToast('Link copiado para a área de transferência!', 'success');
            }
        });
    }
    
    const startExerciseBtn = document.getElementById('startExercise');
    if (startExerciseBtn) {
        startExerciseBtn.addEventListener('click', function() {
            const exerciseId = <?php echo $id; ?>;
            window.location.href = 'exercise_detail.php?id=' + exerciseId;
        });
    }
    
    const saveProgressBtn = document.getElementById('saveProgress');
    if (saveProgressBtn) {
        saveProgressBtn.addEventListener('click', function() {
            showToast('Progresso salvo com sucesso!', 'success');
        });
    }
    
    const markCompleteBtn = document.getElementById('markComplete');
    if (markCompleteBtn) {
        markCompleteBtn.addEventListener('click', function() {
            const tutorialId = <?php echo $id; ?>;
            
            // Enviar requisição para marcar como concluído
            fetch('api/mark_complete.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    type: '<?php echo $type; ?>',
                    id: tutorialId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Conteúdo marcado como concluído!', 'success');
                    document.querySelector('.progress-bar').style.width = '100%';
                    document.querySelector('.progress-stats span:first-child').textContent = '100% completo';
                    document.querySelector('.progress-stats span:last-child').textContent = 'Concluído';
                } else {
                    showToast('Erro ao salvar progresso. Tente novamente.', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showToast('Progresso marcado localmente!', 'info');
                document.querySelector('.progress-bar').style.width = '100%';
                document.querySelector('.progress-stats span:first-child').textContent = '100% completo';
                document.querySelector('.progress-stats span:last-child').textContent = 'Concluído';
            });
        });
    }
    
    const continueBtn = document.getElementById('continueBtn');
    if (continueBtn) {
        continueBtn.addEventListener('click', function() {
            // Simular progresso
            const progressBar = document.querySelector('.progress-bar');
            const currentWidth = parseInt(progressBar.style.width) || 0;
            const newWidth = Math.min(currentWidth + 25, 100);
            
            progressBar.style.width = newWidth + '%';
            document.querySelector('.progress-stats span:first-child').textContent = newWidth + '% completo';
            
            if (newWidth === 100) {
                document.querySelector('.progress-stats span:last-child').textContent = 'Concluído';
                this.innerHTML = '<i class="fas fa-check me-1"></i> Concluído';
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-success');
            }
            
            showToast('Progresso salvo!', 'success');
        });
    }
    
    // Função para mostrar notificações
    function showToast(message, type = 'info') {
        // Criar elemento de toast
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(toast);
        
        // Remover após 3 segundos
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 3000);
    }
    
    // Efeito de digitação no editor de código
    const codeElement = document.querySelector('.editor-content code');
    if (codeElement) {
        const originalCode = codeElement.textContent;
        codeElement.textContent = '';
        
        let i = 0;
        const typeWriter = () => {
            if (i < originalCode.length) {
                codeElement.textContent += originalCode.charAt(i);
                i++;
                setTimeout(typeWriter, 10);
            }
        };
        
        // Iniciar efeito quando a aba for ativada
        const codeTab = document.querySelector('[data-bs-target="#content"]');
        if (codeTab) {
            codeTab.addEventListener('click', function() {
                setTimeout(typeWriter, 500);
            }, { once: true });
        }
    }
    
    // Simular interações da comunidade
    const likeButtons = document.querySelectorAll('.discussion-actions .btn');
    likeButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.querySelector('.fa-thumbs-up')) {
                const countElement = this.querySelector('span') || this;
                let count = parseInt(countElement.textContent) || 0;
                count++;
                if (this.querySelector('span')) {
                    this.querySelector('span').textContent = count;
                } else {
                    this.textContent = `👍 ${count}`;
                }
                
                // Efeito visual
                this.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 200);
            }
        });
    });
});

// Função para copiar código
function copyCode(button) {
    const codeBlock = button.closest('.code-example-container').querySelector('code');
    const codeText = codeBlock.textContent;
    
    // Copiar para área de transferência
    navigator.clipboard.writeText(codeText).then(() => {
        // Feedback visual
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.style.color = '#28a745';
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.style.color = '';
        }, 2000);
        
        // Mostrar toast
        showToast('Código copiado para a área de transferência!', 'success');
    }).catch(err => {
        console.error('Erro ao copiar:', err);
        showToast('Erro ao copiar código', 'error');
    });
}

// ========================================
// SISTEMA DE COMUNIDADE
// ========================================

// Alternar formulário de discussão
function toggleAddDiscussion() {
    const form = document.getElementById('addDiscussionForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
    if (form.style.display === 'block') {
        document.getElementById('discussionMessage').focus();
    }
}

// Alternar formulário de solução
function toggleAddSolution() {
    const form = document.getElementById('addSolutionForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
    if (form.style.display === 'block') {
        document.getElementById('solutionTitle').focus();
    }
}

// Carregar discussões
function loadDiscussions() {
    fetch(`api/get_discussions.php?content_type=${contentType}&content_id=${contentId}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('discussionsList');
            
            if (!data.success || data.discussions.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-comments fa-3x mb-3"></i>
                        <p>Nenhuma discussão ainda. Seja o primeiro a participar!</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = data.discussions.map(d => `
                <div class="discussion-item">
                    <div class="discussion-header">
                        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(d.username)}&background=random" 
                             class="user-avatar" alt="${d.username}">
                        <div class="flex-grow-1">
                            <h6>${d.username}</h6>
                            <small class="discussion-date">${timeAgo(d.created_at)}</small>
                        </div>
                    </div>
                    <div class="discussion-content">
                        <p>${escapeHtml(d.message)}</p>
                    </div>
                    <div class="discussion-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="likeDiscussion(${d.id}, this)">
                            <i class="fas fa-thumbs-up"></i> <span>${d.likes}</span>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-reply"></i> Responder (${d.replies})
                        </button>
                    </div>
                </div>
            `).join('');
        })
        .catch(err => {
            console.error('Erro ao carregar discussões:', err);
            document.getElementById('discussionsList').innerHTML = `
                <div class="alert alert-danger">Erro ao carregar discussões</div>
            `;
        });
}

// Carregar soluções
function loadSolutions() {
    fetch(`api/get_solutions.php?content_type=${contentType}&content_id=${contentId}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('solutionsList');
            
            if (!data.success || data.solutions.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-code fa-3x mb-3"></i>
                        <p>Nenhuma solução compartilhada ainda.</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = data.solutions.map(s => `
                <div class="solution-card">
                    <div class="solution-header">
                        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(s.username)}&background=random" 
                             class="user-avatar" alt="${s.username}">
                        <div class="flex-grow-1">
                            <h6>${escapeHtml(s.title)}</h6>
                            <small class="text-muted">por ${s.username} • ${timeAgo(s.created_at)}</small>
                        </div>
                    </div>
                    <div class="solution-preview">
                        <pre><code class="language-${s.language}">${escapeHtml(s.code)}</code></pre>
                    </div>
                    <div class="solution-actions">
                        <button class="btn btn-sm btn-outline-success">
                            <i class="fas fa-thumbs-up"></i> ${s.likes}
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="copySolutionCode(this)">
                            <i class="fas fa-copy"></i> Copiar
                        </button>
                    </div>
                </div>
            `).join('');
        })
        .catch(err => {
            console.error('Erro ao carregar soluções:', err);
            document.getElementById('solutionsList').innerHTML = `
                <div class="alert alert-danger">Erro ao carregar soluções</div>
            `;
        });
}

// Submeter discussão
function submitDiscussion() {
    if (!isLoggedIn) {
        showToast('Você precisa estar logado para participar', 'warning');
        return;
    }
    
    const message = document.getElementById('discussionMessage').value.trim();
    
    if (message.length < 10) {
        showToast('A mensagem deve ter pelo menos 10 caracteres', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('content_type', contentType);
    formData.append('content_id', contentId);
    formData.append('message', message);
    
    fetch('api/add_discussion.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Discussão publicada!', 'success');
            document.getElementById('discussionMessage').value = '';
            toggleAddDiscussion();
            loadDiscussions(); // Recarrega lista
        } else {
            showToast(data.message || 'Erro ao publicar', 'error');
        }
    })
    .catch(err => {
        console.error('Erro:', err);
        showToast('Erro ao publicar discussão', 'error');
    });
}

// Submeter solução
function submitSolution() {
    if (!isLoggedIn) {
        showToast('Você precisa estar logado para compartilhar soluções', 'warning');
        return;
    }
    
    const title = document.getElementById('solutionTitle').value.trim();
    const code = document.getElementById('solutionCode').value.trim();
    const language = document.getElementById('solutionLanguage').value;
    
    if (!title || code.length < 20) {
        showToast('Preencha todos os campos corretamente', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('content_type', contentType);
    formData.append('content_id', contentId);
    formData.append('title', title);
    formData.append('code', code);
    formData.append('language', language);
    
    fetch('api/add_solution.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Solução compartilhada!', 'success');
            document.getElementById('solutionTitle').value = '';
            document.getElementById('solutionCode').value = '';
            toggleAddSolution();
            loadSolutions(); // Recarrega lista
        } else {
            showToast(data.message || 'Erro ao compartilhar', 'error');
        }
    })
    .catch(err => {
        console.error('Erro:', err);
        showToast('Erro ao compartilhar solução', 'error');
    });
}

// Curtir discussão
function likeDiscussion(discussionId, button) {
    if (!isLoggedIn) {
        showToast('Faça login para curtir', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('discussion_id', discussionId);
    
    fetch('api/like_discussion.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const span = button.querySelector('span');
            span.textContent = data.total_likes;
            
            if (data.action === 'added') {
                button.classList.add('btn-primary');
                button.classList.remove('btn-outline-primary');
            } else {
                button.classList.remove('btn-primary');
                button.classList.add('btn-outline-primary');
            }
        }
    })
    .catch(err => console.error('Erro:', err));
}

// Copiar código de solução
function copySolutionCode(button) {
    const codeElement = button.closest('.solution-card').querySelector('code');
    const code = codeElement.textContent;
    
    navigator.clipboard.writeText(code).then(() => {
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> Copiado';
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
        }, 2000);
    });
}

// Funções auxiliares
function timeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
    if (seconds < 60) return 'agora mesmo';
    if (seconds < 3600) return Math.floor(seconds / 60) + ' min atrás';
    if (seconds < 86400) return Math.floor(seconds / 3600) + 'h atrás';
    if (seconds < 604800) return Math.floor(seconds / 86400) + 'd atrás';
    return date.toLocaleDateString('pt-BR');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?php include 'footer.php'; ?>