<?php
/**
 * ARQUIVO: show.php
 * DESCRIÇÃO: Exibe detalhes completos de um tutorial ou exercício
 * AUTOR: Sistema WebLearn
 * DATA: 2024
 */

// Inclui arquivo de configuração com funções auxiliares (sanitize, redirect, etc)
require_once 'config.php';

// Inclui funções de tutorial se necessário
if (file_exists('tutorial_functions.php')) {
    require_once 'tutorial_functions.php';
}

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
    // EXERCÍCIOS: Busca do banco de dados
    $conn = getDBConnection();
    $item = null;
    
    if ($conn) {
        $stmt = $conn->prepare("SELECT e.*, c.name as category_name FROM exercises e LEFT JOIN categories c ON e.category_id = c.id WHERE e.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        
        if ($item) {
            $item['category'] = $item['category_name'] ?? 'Geral';
        }
    }
    
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
    // Obtém conexão com banco de dados
    $conn = getDBConnection();
    
    // Se tem conexão E é tutorial (exercícios tem sistema próprio)
    if ($conn && $type === 'tutorial') {
        try {
            // Verifica se a tabela existe antes de consultar
            if ($conn instanceof PDO) {
                // Conexão PDO
                $stmt = $conn->prepare("SELECT progress FROM tutorial_progress WHERE user_id = ? AND tutorial_id = ?");
                $stmt->execute([getCurrentUser()['id'], $id]);
                $progress_data = $stmt->fetch();
            } else {
                // Conexão MySQLi
                $stmt = $conn->prepare("SELECT progress FROM tutorial_progress WHERE user_id = ? AND tutorial_id = ?");
                if ($stmt) {
                    $stmt->bind_param("ii", getCurrentUser()['id'], $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $progress_data = $result->fetch_assoc();
                } else {
                    $progress_data = false;
                }
            }
            
            if ($progress_data) {
                // Converte progresso para inteiro
                $user_progress = (int)$progress_data['progress'];
                
                // Define status baseado no progresso
                $time_remaining = $user_progress >= 100 ? 'Concluído' : 'Em andamento';
            }
            
        } catch (Exception $e) {
            // TRATAMENTO DE ERRO: Silencia erro se tabela não existir
            // Não exibe erro para não quebrar a página
            // Mantém valores padrão: $user_progress = 0, $time_remaining = 'Não iniciado'
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
                <!-- Background decorativo -->
                <div class="header-background"></div>
                
                <div class="header-content">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="flex-grow-1">
                            <!-- Badges no topo -->
                            <div class="badges-row mb-3">
                                <span class="badge-modern type-badge <?php echo $type === 'tutorial' ? 'tutorial' : 'exercise'; ?>">
                                    <i class="fas fa-<?php echo $type === 'tutorial' ? 'graduation-cap' : 'code'; ?>"></i>
                                    <?php echo $type === 'tutorial' ? 'Tutorial' : 'Exercício'; ?>
                                </span>
                                <span class="badge-modern difficulty-badge-modern difficulty-<?php echo strtolower($item['difficulty']); ?>">
                                    <i class="fas fa-signal"></i>
                                    <?php echo sanitize($item['difficulty']); ?>
                                </span>
                            </div>
                            
                            <!-- Título principal -->
                            <h1 class="header-title mb-3"><?php echo sanitize($item['title']); ?></h1>
                            
                            <!-- Descrição -->
                            <p class="header-description mb-4"><?php echo sanitize($item['description']); ?></p>
                            
                            <!-- Estatísticas em destaque -->
                            <div class="header-stats">
                                <?php if (isset($item['rating'])): ?>
                                <div class="stat-badge">
                                    <div class="stat-icon rating">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="stat-info">
                                        <span class="stat-value"><?php echo $item['rating']; ?></span>
                                        <span class="stat-label">(<?php echo $item['rating_count']; ?>)</span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (isset($item['downloads'])): ?>
                                <div class="stat-badge">
                                    <div class="stat-icon downloads">
                                        <i class="fas fa-download"></i>
                                    </div>
                                    <div class="stat-info">
                                        <span class="stat-value"><?php echo number_format($item['downloads']); ?></span>
                                        <span class="stat-label">downloads</span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Ações do header -->
                        <div class="header-actions-modern">
                            <button class="btn-action-modern favorite" title="Adicionar aos favoritos">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="btn-action-modern share" title="Compartilhar">
                                <i class="fas fa-share-alt"></i>
                            </button>
                            <button class="btn-action-modern bookmark" title="Salvar para depois">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Metadados em grid -->
                    <div class="metadata-grid-modern">
                        <div class="metadata-card">
                            <div class="metadata-icon category">
                                <i class="fas fa-folder"></i>
                            </div>
                            <div class="metadata-text">
                                <span class="metadata-label">Categoria</span>
                                <span class="metadata-value"><?php echo sanitize($item['category']); ?></span>
                            </div>
                        </div>
                        
                        <?php if ($type === 'tutorial'): ?>
                        <div class="metadata-card">
                            <div class="metadata-icon duration">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="metadata-text">
                                <span class="metadata-label">Duração</span>
                                <span class="metadata-value"><?php echo sanitize($item['duration']); ?></span>
                            </div>
                        </div>
                        
                        <div class="metadata-card">
                            <div class="metadata-icon views">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="metadata-text">
                                <span class="metadata-label">Visualizações</span>
                                <span class="metadata-value"><?php echo number_format($item['views']); ?></span>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="metadata-card">
                            <div class="metadata-icon time">
                                <i class="fas fa-stopwatch"></i>
                            </div>
                            <div class="metadata-text">
                                <span class="metadata-label">Tempo estimado</span>
                                <span class="metadata-value"><?php echo $item['estimated_time']; ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="metadata-card">
                            <div class="metadata-icon author">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="metadata-text">
                                <span class="metadata-label">Autor</span>
                                <span class="metadata-value"><?php echo isset($item['author']) ? $item['author'] : 'Equipe WebLearn'; ?></span>
                            </div>
                        </div>
                        
                        <div class="metadata-card">
                            <div class="metadata-icon date">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="metadata-text">
                                <span class="metadata-label">Atualização</span>
                                <span class="metadata-value"><?php echo isset($item['last_updated']) ? date('d/m/Y', strtotime($item['last_updated'])) : date('d/m/Y'); ?></span>
                            </div>
                        </div>
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
                                <!-- Cabeçalho da Comunidade -->
                                <div class="community-header mb-4">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                        <div>
                                            <h3 class="mb-1"><i class="fas fa-users text-primary me-2"></i>Comunidade</h3>
                                            <p class="text-muted mb-0 small">Compartilhe conhecimento e tire dúvidas</p>
                                        </div>
                                        <div class="community-stats d-flex gap-2">
                                            <span class="badge bg-primary"><i class="fas fa-comments me-1"></i><span id="totalDiscussions">0</span> discussões</span>
                                            <span class="badge bg-success"><i class="fas fa-code me-1"></i><span id="totalSolutions">0</span> soluções</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="discussion-forum mb-5">
                                    <div class="section-header-community mb-3">
                                        <h4 class="mb-0"><i class="fas fa-comments me-2 text-primary"></i>Discussões</h4>
                                        <?php if (isLoggedIn()): ?>
                                        <button class="btn btn-primary btn-sm" onclick="toggleAddDiscussion()">
                                            <i class="fas fa-plus me-2"></i>Nova Discussão
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if (isLoggedIn()): ?>
                                    <div id="addDiscussionForm" style="display: none;" class="add-discussion-form mb-3">
                                        <div class="form-card">
                                            <label for="discussionMessage" class="form-label fw-bold">
                                                <i class="fas fa-edit me-2"></i>Sua mensagem
                                            </label>
                                            <textarea id="discussionMessage" class="form-control" rows="4" 
                                                placeholder="Compartilhe suas dúvidas, experiências ou insights sobre este conteúdo..."></textarea>
                                            <div class="form-help mt-2">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Seja claro e respeitoso. Mínimo de 10 caracteres.
                                            </div>
                                            <div class="d-flex gap-2 mt-3">
                                                <button class="btn btn-primary" onclick="submitDiscussion()">
                                                    <i class="fas fa-paper-plane me-2"></i>Publicar
                                                </button>
                                                <button class="btn btn-outline-secondary" onclick="toggleAddDiscussion()">
                                                    <i class="fas fa-times me-2"></i>Cancelar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <a href="login.php" class="alert-link">Faça login</a> para participar das discussões
                                    </div>
                                    <?php endif; ?>
                                    
                                    <!-- Container de Discussões (preenchido via AJAX) -->
                                    <div id="discussionsList">
                                        <div class="loading-state">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Carregando...</span>
                                            </div>
                                            <p class="mt-3 text-muted">Carregando discussões...</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="user-solutions">
                                    <div class="section-header-community mb-3">
                                        <h4 class="mb-0"><i class="fas fa-code me-2 text-success"></i>Soluções da Comunidade</h4>
                                        <?php if (isLoggedIn()): ?>
                                        <button class="btn btn-success btn-sm" onclick="toggleAddSolution()">
                                            <i class="fas fa-plus me-2"></i>Compartilhar Solução
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if (isLoggedIn()): ?>
                                    <div id="addSolutionForm" style="display: none;" class="add-solution-form mb-3">
                                        <div class="form-card">
                                            <div class="mb-3">
                                                <label for="solutionTitle" class="form-label fw-bold">
                                                    <i class="fas fa-heading me-2"></i>Título da Solução
                                                </label>
                                                <input type="text" id="solutionTitle" class="form-control" 
                                                    placeholder="Ex: Solução usando Grid Layout">
                                            </div>
                                            <div class="mb-3">
                                                <label for="solutionLanguage" class="form-label fw-bold">
                                                    <i class="fas fa-code me-2"></i>Linguagem
                                                </label>
                                                <select id="solutionLanguage" class="form-select">
                                                    <option value="html">HTML</option>
                                                    <option value="css">CSS</option>
                                                    <option value="javascript">JavaScript</option>
                                                    <option value="php">PHP</option>
                                                    <option value="python">Python</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="solutionCode" class="form-label fw-bold">
                                                    <i class="fas fa-file-code me-2"></i>Código
                                                </label>
                                                <textarea id="solutionCode" class="form-control code-textarea" rows="8" 
                                                    placeholder="Cole seu código aqui..."></textarea>
                                                <div class="form-help mt-2">
                                                    <i class="fas fa-lightbulb me-1"></i>
                                                    Adicione comentários para facilitar o entendimento.
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-success" onclick="submitSolution()">
                                                    <i class="fas fa-share-alt me-2"></i>Compartilhar
                                                </button>
                                                <button class="btn btn-outline-secondary" onclick="toggleAddSolution()">
                                                    <i class="fas fa-times me-2"></i>Cancelar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <a href="login.php" class="alert-link">Faça login</a> para compartilhar soluções
                                    </div>
                                    <?php endif; ?>
                                    
                                    <!-- Container de Soluções (preenchido via AJAX) -->
                                    <div id="solutionsList" class="solutions-grid">
                                        <div class="loading-state">
                                            <div class="spinner-border text-success" role="status">
                                                <span class="visually-hidden">Carregando...</span>
                                            </div>
                                            <p class="mt-3 text-muted">Carregando soluções...</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dicas de Uso -->
                                <div class="community-tips mt-5">
                                    <h5 class="mb-3"><i class="fas fa-lightbulb text-warning me-2"></i>Dicas para melhor participação</h5>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="tip-card">
                                                <i class="fas fa-question-circle fa-2x text-primary mb-2"></i>
                                                <h6>Seja específico</h6>
                                                <p class="small mb-0">Descreva claramente sua dúvida ou contribuição</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="tip-card">
                                                <i class="fas fa-heart fa-2x text-danger mb-2"></i>
                                                <h6>Seja respeitoso</h6>
                                                <p class="small mb-0">Trate todos com cordialidade e profissionalismo</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="tip-card">
                                                <i class="fas fa-share-nodes fa-2x text-success mb-2"></i>
                                                <h6>Compartilhe conhecimento</h6>
                                                <p class="small mb-0">Ajude outros e aprenda com a comunidade</p>
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
                <div class="card-header bg-gradient-primary">
                    <h6 class="mb-0 text-white">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações Detalhadas
                    </h6>
                </div>
                <div class="card-body p-0">
                    <!-- Categoria -->
                    <div class="info-item-modern">
                        <div class="info-icon">
                            <?php
                            $categoryIcon = match($item['category']) {
                                'HTML' => 'fab fa-html5',
                                'CSS' => 'fab fa-css3-alt',
                                'JavaScript' => 'fab fa-js-square',
                                'PHP' => 'fab fa-php',
                                default => 'fas fa-code'
                            };
                            $categoryColor = match($item['category']) {
                                'HTML' => 'danger',
                                'CSS' => 'primary',
                                'JavaScript' => 'warning',
                                'PHP' => 'info',
                                default => 'secondary'
                            };
                            ?>
                            <i class="<?php echo $categoryIcon; ?> text-<?php echo $categoryColor; ?>"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Categoria</span>
                            <span class="info-value fw-bold text-<?php echo $categoryColor; ?>">
                                <?php echo sanitize($item['category']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Dificuldade -->
                    <div class="info-item-modern">
                        <div class="info-icon">
                            <?php
                            $difficultyIcon = 'fas fa-signal';
                            $difficultyColor = match(strtolower($item['difficulty'])) {
                                'iniciante', 'beginner', 'fácil' => 'success',
                                'intermediário', 'intermediate', 'médio' => 'warning',
                                'avançado', 'advanced', 'difícil' => 'danger',
                                default => 'info'
                            };
                            ?>
                            <i class="<?php echo $difficultyIcon; ?> text-<?php echo $difficultyColor; ?>"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Dificuldade</span>
                            <span class="info-value">
                                <span class="badge bg-<?php echo $difficultyColor; ?>">
                                    <?php echo ucfirst(sanitize($item['difficulty'])); ?>
                                </span>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Duração / Tempo Estimado -->
                    <div class="info-item-modern">
                        <div class="info-icon">
                            <i class="fas fa-clock text-primary"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">
                                <?php echo $type === 'tutorial' ? 'Duração' : 'Tempo Estimado'; ?>
                            </span>
                            <span class="info-value fw-bold">
                                <?php 
                                echo $type === 'tutorial' 
                                    ? sanitize($item['duration']) 
                                    : $item['estimated_time']; 
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if ($type === 'tutorial' && isset($item['status'])): ?>
                    <!-- Status -->
                    <div class="info-item-modern">
                        <div class="info-icon">
                            <i class="fas fa-circle text-<?php echo strtolower($item['status']) === 'publicado' ? 'success' : 'warning'; ?>"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Status</span>
                            <span class="info-value">
                                <span class="badge bg-<?php echo strtolower($item['status']) === 'publicado' ? 'success' : 'warning'; ?>">
                                    <i class="fas fa-check-circle me-1"></i>
                                    <?php echo sanitize($item['status']); ?>
                                </span>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Avaliação -->
                    <div class="info-item-modern">
                        <div class="info-icon">
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Avaliação</span>
                            <span class="info-value">
                                <div class="rating-stars">
                                    <?php
                                    $rating = isset($item['rating']) ? $item['rating'] : 4.5;
                                    $fullStars = floor($rating);
                                    $hasHalfStar = $rating - $fullStars >= 0.5;
                                    
                                    for ($i = 0; $i < $fullStars; $i++) {
                                        echo '<i class="fas fa-star"></i>';
                                    }
                                    if ($hasHalfStar) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    }
                                    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                    for ($i = 0; $i < $emptyStars; $i++) {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                    ?>
                                    <strong class="ms-2"><?php echo number_format($rating, 1); ?></strong>
                                </div>
                            </span>
                        </div>
                    </div>
                    
                    <?php if (isset($item['author'])): ?>
                    <!-- Autor -->
                    <div class="info-item-modern">
                        <div class="info-icon">
                            <i class="fas fa-user-circle text-secondary"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Autor</span>
                            <span class="info-value">
                                <strong><?php echo $item['author']; ?></strong>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Data de Atualização -->
                    <div class="info-item-modern border-0">
                        <div class="info-icon">
                            <i class="fas fa-calendar-alt text-muted"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Última Atualização</span>
                            <span class="info-value text-muted">
                                <?php 
                                echo isset($item['last_updated']) 
                                    ? date('d/m/Y', strtotime($item['last_updated'])) 
                                    : date('d/m/Y'); 
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card de Progresso -->
            <div class="progress-card mb-4">
                <div class="card-header bg-gradient-progress text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-trophy me-2"></i>
                            Seu Progresso
                        </h6>
                        <?php if ($user_progress >= 100): ?>
                            <span class="badge bg-white bg-opacity-25">
                                <i class="fas fa-medal"></i> Completo
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Círculo de Progresso -->
                    <div class="progress-circle-container mb-4">
                        <svg class="progress-ring" width="120" height="120">
                            <circle class="progress-ring__circle-bg" 
                                    stroke="#e9ecef" 
                                    stroke-width="8" 
                                    fill="transparent" 
                                    r="52" 
                                    cx="60" 
                                    cy="60"/>
                            <circle class="progress-ring__circle" 
                                    stroke="url(#gradient)" 
                                    stroke-width="8" 
                                    fill="transparent" 
                                    r="52" 
                                    cx="60" 
                                    cy="60"
                                    style="stroke-dasharray: <?php echo 2 * 3.14159 * 52; ?>; stroke-dashoffset: <?php echo 2 * 3.14159 * 52 * (1 - $user_progress / 100); ?>;"/>
                            <defs>
                                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#06ffa5;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#06d6a0;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="progress-percentage">
                            <span class="percentage-value"><?php echo $user_progress; ?>%</span>
                            <span class="percentage-label">Completo</span>
                        </div>
                    </div>
                    
                    <!-- Status e Tempo -->
                    <div class="progress-info">
                        <div class="info-row">
                            <div class="info-icon">
                                <i class="fas fa-clock text-primary"></i>
                            </div>
                            <div class="info-text">
                                <small class="text-muted">Status</small>
                                <div class="fw-semibold"><?php echo $time_remaining; ?></div>
                            </div>
                        </div>
                        
                        <?php if ($user_progress > 0 && $user_progress < 100): ?>
                        <div class="info-row">
                            <div class="info-icon">
                                <i class="fas fa-fire text-danger"></i>
                            </div>
                            <div class="info-text">
                                <small class="text-muted">Sequência</small>
                                <div class="fw-semibold"><?php echo rand(1, 7); ?> dias</div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Botão de Ação -->
                    <?php if ($user_progress < 100): ?>
                    <button class="btn btn-gradient-success w-100 mt-3" id="continueBtn">
                        <i class="fas fa-play me-2"></i> Continuar Aprendendo
                    </button>
                    <?php else: ?>
                    <button class="btn btn-success w-100 mt-3" disabled>
                        <i class="fas fa-check-circle me-2"></i> Concluído com Sucesso
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card de Conteúdo Relacionado -->
            <div class="related-card">
                <div class="card-header bg-gradient-related text-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-compass me-2"></i>
                        Continue Explorando
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="related-list">
                        <?php
                        // Buscar conteúdo relacionado
                        $relatedItems = [];
                        
                        if ($type === 'tutorial') {
                            // Buscar tutoriais relacionados da mesma categoria
                            $allTutorials = getTutorials();
                            $relatedItems = array_filter($allTutorials, function($t) use ($item) {
                                return $t['id'] !== $item['id'] && $t['category'] === $item['category'];
                            });
                            
                            // Se não encontrar da mesma categoria, busca do mesmo nível
                            if (empty($relatedItems)) {
                                $relatedItems = array_filter($allTutorials, function($t) use ($item) {
                                    return $t['id'] !== $item['id'] && $t['level'] === $item['level'];
                                });
                            }
                            
                            // Limita a 4 itens
                            $relatedItems = array_slice(array_values($relatedItems), 0, 4);
                            
                        } else {
                            // Buscar exercícios relacionados
                            if (file_exists('data/exercises.php')) {
                                require_once 'data/exercises.php';
                                $allExercises = getExercisesData();
                                
                                // Filtra exercícios da mesma categoria
                                $relatedItems = array_filter($allExercises, function($e) use ($item) {
                                    return $e['id'] !== $item['id'] && $e['category'] === $item['category'];
                                });
                                
                                // Se não encontrar da mesma categoria, busca da mesma dificuldade
                                if (empty($relatedItems)) {
                                    $relatedItems = array_filter($allExercises, function($e) use ($item) {
                                        return $e['id'] !== $item['id'] && $e['difficulty'] === $item['difficulty'];
                                    });
                                }
                                
                                // Limita a 4 itens
                                $relatedItems = array_slice(array_values($relatedItems), 0, 4);
                            }
                        }
                        
                        // Exibir itens relacionados
                        if (empty($relatedItems)): ?>
                            <div class="empty-related text-center py-5">
                                <i class="fas fa-compass fa-3x text-muted mb-3 opacity-25"></i>
                                <p class="text-muted mb-0">Nenhum conteúdo relacionado disponível</p>
                            </div>
                        <?php else:
                            foreach ($relatedItems as $index => $related):
                                $iconClass = match($related['category']) {
                                    'HTML' => 'fab fa-html5',
                                    'CSS' => 'fab fa-css3-alt',
                                    'JavaScript' => 'fab fa-js-square',
                                    'PHP' => 'fab fa-php',
                                    default => 'fas fa-book'
                                };
                                
                                $colorClass = match($related['category']) {
                                    'HTML' => 'html',
                                    'CSS' => 'css',
                                    'JavaScript' => 'js',
                                    'PHP' => 'php',
                                    default => 'default'
                                };
                                
                                $relatedType = $type;
                                $relatedLabel = $type === 'tutorial' ? 'Tutorial' : 'Exercício';
                                $relatedInfo = $type === 'tutorial' 
                                    ? ($related['duration'] ?? '30 min')
                                    : ($related['difficulty'] ?? 'Intermediário');
                            ?>
                                <a href="show.php?type=<?php echo $relatedType; ?>&id=<?php echo $related['id']; ?>" 
                                   class="related-item-modern" 
                                   style="animation-delay: <?php echo $index * 0.1; ?>s">
                                    <div class="related-icon-modern <?php echo $colorClass; ?>">
                                        <i class="<?php echo $iconClass; ?>"></i>
                                    </div>
                                    <div class="related-content-modern">
                                        <h6 class="related-title"><?php echo htmlspecialchars($related['title']); ?></h6>
                                        <div class="related-meta">
                                            <span class="badge-type"><?php echo $relatedLabel; ?></span>
                                            <span class="badge-info">
                                                <i class="fas fa-clock"></i>
                                                <?php echo $relatedInfo; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="related-arrow">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </a>
                            <?php
                            endforeach;
                        endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Card de Estatísticas -->
            <div class="stats-card mt-4">
                <div class="card-header bg-gradient-stats text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-chart-line me-2"></i>
                            Estatísticas
                        </h6>
                        <span class="badge bg-white bg-opacity-25">Últimos 30 dias</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="stats-grid-modern">
                        <div class="stat-item-modern">
                            <div class="stat-icon-container downloads">
                                <i class="fas fa-download"></i>
                            </div>
                            <div class="stat-details">
                                <div class="stat-value-modern"><?php echo isset($item['downloads']) ? number_format($item['downloads']) : '1,200'; ?></div>
                                <div class="stat-label-modern">Downloads</div>
                                <div class="stat-trend positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+12% este mês</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="stat-item-modern">
                            <div class="stat-icon-container ratings">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stat-details">
                                <div class="stat-value-modern"><?php echo isset($item['rating_count']) ? number_format($item['rating_count']) : '245'; ?></div>
                                <div class="stat-label-modern">Avaliações</div>
                                <div class="stat-trend positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+8% este mês</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="stat-item-modern">
                            <div class="stat-icon-container views">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="stat-details">
                                <div class="stat-value-modern"><?php echo isset($item['views']) ? number_format($item['views']) : '3,400'; ?></div>
                                <div class="stat-label-modern">Visualizações</div>
                                <div class="stat-trend positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+15% este mês</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="stat-item-modern">
                            <div class="stat-icon-container completion">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-details">
                                <div class="stat-value-modern">87%</div>
                                <div class="stat-label-modern">Taxa de Conclusão</div>
                                <div class="progress stat-progress mt-2">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: 87%"
                                         aria-valuenow="87" 
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer com estatísticas adicionais -->
                    <div class="stats-footer">
                        <div class="footer-stat-item">
                            <i class="fas fa-fire text-danger"></i>
                            <span><strong>Popular</strong> - Top 10 desta semana</span>
                        </div>
                        <div class="footer-stat-item">
                            <i class="fas fa-users text-primary"></i>
                            <span><strong><?php echo rand(50, 200); ?></strong> usuários ativos agora</span>
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

/* ========================================
   ESTILOS PARA A PÁGINA DE DETALHES
   ======================================== */

/* Card principal do cabeçalho - container do topo da página */
.content-header-card {
    background: white; /* Fundo branco base (coberto pelo gradiente) */
    border-radius: 20px; /* Cantos arredondados suaves */
    box-shadow: 0 10px 40px rgba(0,0,0,0.08); /* Sombra sutil para profundidade */
    position: relative; /* Permite posicionamento absoluto dos filhos */
    overflow: hidden; /* Esconde elementos que ultrapassam os limites */
}

/* Fundo gradiente decorativo do header */
.header-background {
    position: absolute; /* Posicionado em relação ao .content-header-card */
    top: 0; /* Alinhado ao topo */
    left: 0; /* Alinhado à esquerda */
    right: 0; /* Alinhado à direita */
    height: 250px; /* Altura fixa do gradiente */
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); /* Gradiente diagonal das cores do site */
    opacity: 1; /* Totalmente opaco */
    z-index: 0; /* Fica atrás do conteúdo */
}

/* Círculo decorativo superior direito (efeito visual) */
.header-background::before {
    content: ''; /* Cria elemento vazio */
    position: absolute; /* Posicionado em relação ao .header-background */
    top: -50%; /* Posicionado acima do topo (parcialmente oculto) */
    right: -10%; /* Posicionado à direita (parcialmente oculto) */
    width: 400px; /* Largura do círculo */
    height: 400px; /* Altura do círculo */
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%); /* Gradiente radial branco transparente */
    border-radius: 50%; /* Torna o elemento circular */
}

/* Círculo decorativo inferior esquerdo (efeito visual) */
.header-background::after {
    content: ''; /* Cria elemento vazio */
    position: absolute; /* Posicionado em relação ao .header-background */
    bottom: -30%; /* Posicionado abaixo do fundo (parcialmente oculto) */
    left: -5%; /* Posicionado à esquerda (parcialmente oculto) */
    width: 300px; /* Largura do círculo */
    height: 300px; /* Altura do círculo */
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%); /* Gradiente radial branco mais sutil */
    border-radius: 50%; /* Torna o elemento circular */
}

/* Container do conteúdo visível do header */
.header-content {
    position: relative; /* Permite controlar z-index */
    z-index: 1; /* Fica acima do fundo gradiente */
    padding: 2.5rem; /* Espaçamento interno generoso */
}

/* ========================================
   BADGES MODERNOS
   ======================================== */

/* Container flexível para badges (Tutorial/Exercício, Dificuldade) */
.badges-row {
    display: flex; /* Layout flexível horizontal */
    align-items: center; /* Alinha verticalmente ao centro */
    gap: 0.75rem; /* Espaçamento entre badges */
    flex-wrap: wrap; /* Permite quebra de linha em telas pequenas */
}

/* Estilo base para todos os badges modernos */
.badge-modern {
    display: inline-flex; /* Flexbox inline para ícone + texto */
    align-items: center; /* Alinha verticalmente ao centro */
    gap: 0.5rem; /* Espaço entre ícone e texto */
    padding: 0.5rem 1rem; /* Espaçamento interno (vertical horizontal) */
    border-radius: 25px; /* Cantos bem arredondados (formato pílula) */
    font-size: 0.85rem; /* Tamanho da fonte pequeno */
    font-weight: 600; /* Fonte semi-negrito */
    backdrop-filter: blur(10px); /* Efeito de desfoque do fundo (glassmorphism) */
    transition: all 0.3s ease; /* Transição suave para hover */
}

/* Efeito hover nos badges - levanta e adiciona sombra */
.badge-modern:hover {
    transform: translateY(-2px); /* Move 2px para cima */
    box-shadow: 0 5px 15px rgba(0,0,0,0.2); /* Adiciona sombra forte */
}

/* Badge de tipo Tutorial - branco semi-transparente */
.type-badge.tutorial {
    background: rgba(255,255,255,0.25); /* Fundo branco 25% opaco */
    color: white; /* Texto branco */
    border: 2px solid rgba(255,255,255,0.3); /* Borda branca 30% opaca */
}

/* Badge de tipo Exercício - verde semi-transparente */
.type-badge.exercise {
    background: rgba(40, 167, 69, 0.25); /* Fundo verde 25% opaco */
    color: white; /* Texto branco */
    border: 2px solid rgba(40, 167, 69, 0.3); /* Borda verde 30% opaca */
}

/* Badge de dificuldade - branco semi-transparente */
.difficulty-badge-modern {
    background: rgba(255,255,255,0.2); /* Fundo branco 20% opaco */
    color: white; /* Texto branco */
    border: 2px solid rgba(255,255,255,0.25); /* Borda branca 25% opaca */
}

/* Ícone dentro do badge de dificuldade - tamanho menor */
.difficulty-badge-modern i {
    font-size: 0.75rem; /* Ícone pequeno */
}

/* ========================================
   TÍTULO E DESCRIÇÃO
   ======================================== */

/* Título principal do tutorial/exercício */
.header-title {
    font-size: 2.5rem; /* Título grande e impactante */
    font-weight: 800; /* Fonte extra-negrito */
    color: white; /* Texto branco */
    line-height: 1.2; /* Espaçamento entre linhas compacto */
    margin: 0; /* Remove margem padrão */
    text-shadow: 0 2px 20px rgba(0,0,0,0.3); /* Sombra forte para contraste */
}

/* Descrição/subtítulo do conteúdo */
.header-description {
    font-size: 1.1rem; /* Fonte ligeiramente maior que o normal */
    color: white; /* Texto branco */
    line-height: 1.6; /* Espaçamento entre linhas confortável */
    margin: 0; /* Remove margem padrão */
    text-shadow: 0 1px 10px rgba(0,0,0,0.2); /* Sombra média para legibilidade */
    font-weight: 500; /* Fonte média */
}

/* ========================================
   ESTATÍSTICAS EM DESTAQUE
   ======================================== */

/* Container flexível para badges de estatísticas (rating, downloads) */
.header-stats {
    display: flex; /* Layout flexível horizontal */
    gap: 1.5rem; /* Espaçamento entre badges */
    flex-wrap: wrap; /* Permite quebra de linha em telas pequenas */
}

/* Badge individual de estatística */
.stat-badge {
    display: flex; /* Layout flexível horizontal */
    align-items: center; /* Alinha verticalmente ao centro */
    gap: 0.75rem; /* Espaço entre ícone e informação */
    padding: 0.75rem 1.25rem; /* Espaçamento interno generoso */
    background: rgba(255,255,255,0.2); /* Fundo branco 20% opaco */
    backdrop-filter: blur(10px); /* Efeito glassmorphism */
    border-radius: 15px; /* Cantos arredondados */
    border: 2px solid rgba(255,255,255,0.3); /* Borda branca 30% opaca */
    transition: all 0.3s ease; /* Transição suave para hover */
}

/* Efeito hover nos badges de estatística */
.stat-badge:hover {
    background: rgba(255,255,255,0.3); /* Aumenta opacidade do fundo */
    transform: translateY(-3px); /* Move 3px para cima */
    box-shadow: 0 8px 20px rgba(0,0,0,0.15); /* Adiciona sombra elevada */
}

/* Container do ícone da estatística */
.stat-icon {
    width: 40px; /* Largura fixa */
    height: 40px; /* Altura fixa (quadrado) */
    border-radius: 12px; /* Cantos arredondados */
    display: flex; /* Flexbox para centralizar ícone */
    align-items: center; /* Centraliza verticalmente */
    justify-content: center; /* Centraliza horizontalmente */
    font-size: 1.1rem; /* Tamanho do ícone */
}

/* Ícone de rating (estrela) - gradiente amarelo/laranja */
.stat-icon.rating {
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); /* Gradiente amarelo para laranja */
    color: white; /* Ícone branco */
}

/* Ícone de downloads - gradiente azul claro/escuro */
.stat-icon.downloads {
    background: linear-gradient(135deg, #4cc9f0 0%, #4361ee 100%); /* Gradiente azul claro para escuro */
    color: white; /* Ícone branco */
}

/* Container das informações da estatística (valor + label) */
.stat-info {
    display: flex; /* Layout flexível */
    flex-direction: column; /* Empilha verticalmente */
}

/* Valor numérico da estatística (ex: 4.8, 380) */
.stat-value {
    font-size: 1.25rem; /* Fonte grande para destaque */
    font-weight: 800; /* Fonte extra-negrito */
    color: white; /* Texto branco */
    line-height: 1; /* Sem espaçamento extra */
    text-shadow: 0 1px 5px rgba(0,0,0,0.2); /* Sombra para contraste */
}

/* Label da estatística (ex: "(144)", "downloads") */
.stat-label {
    font-size: 0.75rem; /* Fonte pequena */
    color: white; /* Texto branco */
    font-weight: 600; /* Fonte semi-negrito */
    text-shadow: 0 1px 3px rgba(0,0,0,0.15); /* Sombra sutil */
}

/* ========================================
   AÇÕES DO HEADER
   ======================================== */

/* Container para botões de ação (favoritar, compartilhar, salvar) */
.header-actions-modern {
    display: flex; /* Layout flexível */
    gap: 0.5rem; /* Espaçamento entre botões */
    flex-direction: column; /* Empilha verticalmente */
}

/* Botão de ação circular */
.btn-action-modern {
    width: 48px; /* Largura fixa */
    height: 48px; /* Altura fixa (circular) */
    border-radius: 50%; /* Totalmente circular */
    display: flex; /* Flexbox para centralizar ícone */
    align-items: center; /* Centraliza verticalmente */
    justify-content: center; /* Centraliza horizontalmente */
    background: rgba(255,255,255,0.2); /* Fundo branco 20% opaco */
    backdrop-filter: blur(10px); /* Efeito glassmorphism */
    border: 2px solid rgba(255,255,255,0.3); /* Borda branca 30% opaca */
    color: white; /* Ícone branco */
    font-size: 1.1rem; /* Tamanho do ícone */
    cursor: pointer; /* Cursor de clique */
    transition: all 0.3s ease; /* Transição suave para hover */
}

/* Efeito hover nos botões de ação */
.btn-action-modern:hover {
    background: rgba(255,255,255,0.35); /* Aumenta opacidade */
    transform: scale(1.1) rotate(5deg); /* Aumenta e rotaciona levemente */
    box-shadow: 0 5px 15px rgba(0,0,0,0.2); /* Adiciona sombra */
}

/* Hover específico do botão favoritar - fica vermelho */
.btn-action-modern.favorite:hover {
    color: #ff6b6b; /* Ícone vermelho */
}

/* Hover específico do botão compartilhar - fica azul */
.btn-action-modern.share:hover {
    color: #4cc9f0; /* Ícone azul claro */
}

/* Hover específico do botão salvar - fica amarelo */
.btn-action-modern.bookmark:hover {
    color: #ffd93d; /* Ícone amarelo */
}

/* ========================================
   GRID DE METADADOS MODERNO
   ======================================== */

/* Grid responsivo para cards de metadados (Categoria, Duração, etc) */
.metadata-grid-modern {
    display: grid; /* Layout em grid */
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* Colunas responsivas mínimo 200px */
    gap: 1rem; /* Espaçamento entre cards */
    margin-top: 2rem; /* Margem superior */
    padding-top: 2rem; /* Padding superior */
    border-top: 2px solid rgba(255,255,255,0.2); /* Linha divisória superior */
}

/* Card individual de metadado */
.metadata-card {
    display: flex; /* Layout flexível horizontal */
    align-items: center; /* Alinha verticalmente ao centro */
    gap: 1rem; /* Espaço entre ícone e texto */
    padding: 1rem; /* Espaçamento interno */
    background: rgba(255,255,255,0.15); /* Fundo branco 15% opaco */
    backdrop-filter: blur(10px); /* Efeito glassmorphism */
    border-radius: 12px; /* Cantos arredondados */
    border: 1px solid rgba(255,255,255,0.2); /* Borda sutil */
    transition: all 0.3s ease; /* Transição suave para hover */
}

/* Efeito hover nos cards de metadado */
.metadata-card:hover {
    background: rgba(255,255,255,0.25); /* Aumenta opacidade do fundo */
    transform: translateY(-2px); /* Move 2px para cima */
    box-shadow: 0 5px 15px rgba(0,0,0,0.1); /* Adiciona sombra */
}

/* Container do ícone do metadado */
.metadata-icon {
    width: 42px; /* Largura fixa */
    height: 42px; /* Altura fixa (quadrado) */
    border-radius: 10px; /* Cantos arredondados */
    display: flex; /* Flexbox para centralizar ícone */
    align-items: center; /* Centraliza verticalmente */
    justify-content: center; /* Centraliza horizontalmente */
    flex-shrink: 0; /* Não encolhe em espaços pequenos */
    font-size: 1.1rem; /* Tamanho do ícone */
    transition: all 0.3s ease; /* Transição suave para animação */
}

/* Animação do ícone no hover do card */
.metadata-card:hover .metadata-icon {
    transform: scale(1.1) rotate(-5deg); /* Aumenta e rotaciona */
}

/* Ícone de Categoria - gradiente branco semi-transparente */
.metadata-icon.category {
    background: linear-gradient(135deg, rgba(255,255,255,0.25) 0%, rgba(255,255,255,0.15) 100%); /* Gradiente branco */
    color: white; /* Ícone branco */
    border: 2px solid rgba(255,255,255,0.3); /* Borda branca */
}

/* Ícone de Duração/Tempo - gradiente azul semi-transparente */
.metadata-icon.duration,
.metadata-icon.time {
    background: linear-gradient(135deg, rgba(76,201,240,0.3) 0%, rgba(67,97,238,0.3) 100%); /* Gradiente azul */
    color: white; /* Ícone branco */
    border: 2px solid rgba(255,255,255,0.25); /* Borda branca */
}

/* Ícone de Visualizações - gradiente verde semi-transparente */
.metadata-icon.views {
    background: linear-gradient(135deg, rgba(6,255,165,0.25) 0%, rgba(6,214,160,0.25) 100%); /* Gradiente verde */
    color: white; /* Ícone branco */
    border: 2px solid rgba(255,255,255,0.25); /* Borda branca */
}

/* Ícone de Autor - gradiente rosa/roxo semi-transparente */
.metadata-icon.author {
    background: linear-gradient(135deg, rgba(247,37,133,0.25) 0%, rgba(181,23,158,0.25) 100%); /* Gradiente rosa */
    color: white; /* Ícone branco */
    border: 2px solid rgba(255,255,255,0.25); /* Borda branca */
}

/* Ícone de Data - gradiente amarelo/laranja semi-transparente */
.metadata-icon.date {
    background: linear-gradient(135deg, rgba(255,193,7,0.25) 0%, rgba(255,152,0,0.25) 100%); /* Gradiente amarelo */
    color: white; /* Ícone branco */
    border: 2px solid rgba(255,255,255,0.25); /* Borda branca */
}

/* Container do texto do metadado (label + valor) */
.metadata-text {
    display: flex; /* Layout flexível */
    flex-direction: column; /* Empilha verticalmente */
    gap: 0.15rem; /* Pequeno espaço entre label e valor */
}

/* Label do metadado (ex: "CATEGORIA", "DURAÇÃO") */
.metadata-label {
    font-size: 0.75rem; /* Fonte pequena */
    color: #ffffff; /* Branco sólido */
    text-transform: uppercase; /* Texto em maiúsculas */
    letter-spacing: 1px; /* Espaçamento entre letras */
    font-weight: 800; /* Fonte extra-negrito */
    text-shadow: 0 2px 8px rgba(0,0,0,0.4); /* Sombra forte para contraste */
    opacity: 1; /* Totalmente opaco */
}

/* Valor do metadado (ex: "CSS", "25 min") */
.metadata-value {
    font-size: 1rem; /* Fonte normal */
    color: #ffffff; /* Branco sólido */
    font-weight: 800; /* Fonte extra-negrito */
    text-shadow: 0 2px 10px rgba(0,0,0,0.4); /* Sombra forte para contraste */
}

/* Animação de entrada */
@keyframes headerFadeIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.content-header-card {
    animation: headerFadeIn 0.6s ease-out;
}

.metadata-card {
    animation: slideInMetadata 0.5s ease;
}

@keyframes slideInMetadata {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.metadata-card:nth-child(1) { animation-delay: 0.1s; }
.metadata-card:nth-child(2) { animation-delay: 0.2s; }
.metadata-card:nth-child(3) { animation-delay: 0.3s; }
.metadata-card:nth-child(4) { animation-delay: 0.4s; }
.metadata-card:nth-child(5) { animation-delay: 0.5s; }

/* Estilos antigos - mantidos para compatibilidade */
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
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 0.75rem;
    padding: 1rem;
}

.resource-card {
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    border: 2px solid transparent;
    transition: var(--transition);
    min-height: 180px;
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
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.resource-card h5 {
    margin-bottom: 0.25rem;
    color: var(--text-heading);
    font-size: 0.9rem;
}

.resource-card p {
    color: #6c757d;
    margin-bottom: 0.75rem;
    font-size: 0.8rem;
}

/* Estilos da Comunidade */
.community-section {
    padding: 1.5rem;
}

.community-header {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    padding: 1.5rem;
    border-radius: 12px;
    border-left: 4px solid var(--primary-color);
}

.community-stats .badge {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
    font-weight: 600;
}

.section-header-community {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 2px solid #e9ecef;
    margin-bottom: 1.5rem;
}

.section-header-community h4 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-heading);
}

.add-discussion-form,
.add-solution-form {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-card {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    border: 2px solid var(--primary-color);
    box-shadow: 0 5px 15px rgba(67, 97, 238, 0.1);
}

.form-card .form-label {
    color: var(--text-heading);
    margin-bottom: 0.5rem;
}

.form-card .form-control,
.form-card .form-select {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-card .form-control:focus,
.form-card .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
}

.code-textarea {
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    background: #f8f9fa;
}

.form-help {
    font-size: 0.85rem;
    color: #6c757d;
    font-style: italic;
}

.loading-state {
    text-align: center;
    padding: 3rem 1rem;
}

.loading-state .spinner-border {
    width: 3rem;
    height: 3rem;
}

.discussion-item {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.discussion-item:hover {
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}

.discussion-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f1f3f4;
}

.user-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.2rem;
}

.user-info {
    flex-grow: 1;
}

.user-info h6 {
    margin: 0;
    color: var(--text-heading);
    font-weight: 600;
}

.discussion-date {
    font-size: 0.85rem;
    color: #6c757d;
}

.discussion-content {
    margin-bottom: 1rem;
}

.discussion-content p {
    margin: 0;
    color: var(--text-body);
    line-height: 1.6;
}

.discussion-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.discussion-actions .btn {
    font-size: 0.85rem;
    padding: 0.4rem 0.8rem;
}

.discussion-replies {
    margin-top: 1rem;
    padding-left: 2.5rem;
    border-left: 3px solid var(--primary-color);
}

.reply-item {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 0.75rem;
}

.reply-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.reply-header .user-avatar {
    width: 32px;
    height: 32px;
    font-size: 0.9rem;
}

.reply-header strong {
    font-size: 0.9rem;
    color: var(--text-heading);
}

.reply-content {
    color: var(--text-body);
    font-size: 0.9rem;
    line-height: 1.5;
}

.reply-form {
    background: white;
    padding: 1rem;
    border-radius: 8px;
    border: 2px dashed #dee2e6;
    margin-top: 1rem;
}

.solutions-grid {
    display: grid;
    gap: 1.5rem;
}

.solution-card {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.solution-card:hover {
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}

.solution-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f1f3f4;
}

.solution-preview {
    background: #1e1e1e;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    max-height: 200px;
    overflow-y: auto;
}

.solution-preview pre {
    margin: 0;
    color: #d4d4d4;
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
    line-height: 1.5;
}

.solution-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.rating i {
    color: #ffc107;
}

.modal-code-container {
    background: #1e1e1e;
    border-radius: 8px;
    padding: 1.5rem;
    max-height: 400px;
    overflow-y: auto;
}

.modal-code-container pre {
    margin: 0;
    color: #d4d4d4;
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    white-space: pre-wrap;
    line-height: 1.6;
}

/* Dicas da Comunidade */
.community-tips {
    background: linear-gradient(135deg, #fff3cd, #fff8e1);
    padding: 1.5rem;
    border-radius: 12px;
    border-left: 4px solid #ffc107;
}

.community-tips h5 {
    font-weight: 600;
    color: var(--text-heading);
}

.tip-card {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    text-align: center;
    height: 100%;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.tip-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.tip-card h6 {
    font-weight: 600;
    color: var(--text-heading);
    margin: 0.75rem 0 0.5rem;
}

.tip-card p {
    color: #6c757d;
}

/* Estado vazio */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h5 {
    margin-bottom: 0.5rem;
}

.empty-state p {
    margin-bottom: 1.5rem;
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

/* Estilo moderno para card de informações */
.bg-gradient-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
}

.info-item-modern {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f1f3f4;
    transition: all 0.3s ease;
}

.info-item-modern:last-child {
    border-bottom: none;
}

.info-item-modern:hover {
    background: #f8f9fa;
    padding-left: 1.75rem;
}

.info-icon {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 10px;
    flex-shrink: 0;
    font-size: 1.25rem;
    transition: all 0.3s ease;
}

.info-item-modern:hover .info-icon {
    transform: scale(1.1);
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.info-content {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-label {
    font-size: 0.8rem;
    font-weight: 700;
    color: #495057;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

.info-value {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-heading);
    display: flex;
    align-items: center;
}

.rating-stars {
    display: flex;
    align-items: center;
    gap: 0.15rem;
}

.rating-stars i {
    color: #ffc107;
    font-size: 0.9rem;
}

.info-value .badge {
    font-size: 0.85rem;
    padding: 0.35rem 0.75rem;
    font-weight: 600;
}

/* Old styles - mantidos para compatibilidade */
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

.status-publicado { color: #28a745; }
.status-rascunho { color: #ffc107; }

.progress-stats {
    display: flex;
    justify-content: space-between;
    font-size: 0.875rem;
    color: #6c757d;
}

/* ========================================
   CARD DE PROGRESSO MODERNO
   ======================================== */
.bg-gradient-progress {
    background: linear-gradient(135deg, #06ffa5 0%, #06d6a0 100%);
}

.progress-circle-container {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
}

.progress-ring {
    transform: rotate(-90deg);
    display: block;
    margin: 0 auto;
}

.progress-ring__circle {
    transition: stroke-dashoffset 1.5s ease-in-out;
    stroke-linecap: round;
}

.progress-ring__circle-bg {
    opacity: 0.2;
}

.progress-percentage {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.percentage-value {
    display: block;
    font-size: 2rem;
    font-weight: 800;
    color: #06d6a0;
    line-height: 1;
}

.percentage-label {
    display: block;
    font-size: 0.75rem;
    color: #718096;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 0.25rem;
}

.progress-info {
    display: grid;
    gap: 0.75rem;
}

.info-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.info-row:hover {
    background: #e9ecef;
    transform: translateX(3px);
}

.info-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 10px;
    flex-shrink: 0;
    font-size: 1.1rem;
}

.info-text {
    flex: 1;
}

.info-text small {
    display: block;
    font-size: 0.75rem;
    margin-bottom: 0.15rem;
}

.info-text .fw-semibold {
    color: #2d3748;
    font-size: 0.95rem;
}

.btn-gradient-success {
    background: linear-gradient(135deg, #06ffa5 0%, #06d6a0 100%);
    border: none;
    color: white;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(6, 214, 160, 0.3);
}

.btn-gradient-success:hover {
    background: linear-gradient(135deg, #06d6a0 0%, #06b389 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(6, 214, 160, 0.4);
    color: white;
}

/* ========================================
   CONTEÚDO RELACIONADO MODERNO
   ======================================== */
.bg-gradient-related {
    background: linear-gradient(135deg, #4cc9f0 0%, #4361ee 100%);
}

.related-item-modern {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border: 2px solid #f1f3f4;
    border-radius: 12px;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    margin-bottom: 0.75rem;
    position: relative;
    overflow: hidden;
    animation: slideInRelated 0.5s ease;
}

.related-item-modern::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: transparent;
    transition: all 0.3s ease;
}

.related-item-modern:hover {
    border-color: transparent;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    transform: translateX(5px);
    text-decoration: none;
    color: inherit;
}

.related-item-modern:hover::before {
    width: 100%;
    opacity: 0.05;
}

.related-item-modern:hover .related-arrow {
    transform: translateX(5px);
    opacity: 1;
}

.related-icon-modern {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.related-icon-modern.html {
    background: linear-gradient(135deg, rgba(227, 76, 38, 0.1), rgba(241, 101, 41, 0.1));
    color: #e34c26;
}

.related-icon-modern.css {
    background: linear-gradient(135deg, rgba(38, 77, 228, 0.1), rgba(33, 150, 243, 0.1));
    color: #264de4;
}

.related-icon-modern.js {
    background: linear-gradient(135deg, rgba(240, 219, 79, 0.2), rgba(247, 223, 30, 0.2));
    color: #f0db4f;
}

.related-icon-modern.php {
    background: linear-gradient(135deg, rgba(119, 123, 180, 0.1), rgba(79, 93, 149, 0.1));
    color: #777bb3;
}

.related-icon-modern.default {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    color: #667eea;
}

.related-item-modern:hover .related-icon-modern {
    transform: scale(1.1) rotate(5deg);
}

.related-item-modern:hover .related-icon-modern.html::before {
    background: linear-gradient(135deg, #e34c26, #f16529);
}

.related-item-modern:hover .related-icon-modern.css::before {
    background: linear-gradient(135deg, #264de4, #2196f3);
}

.related-item-modern:hover .related-icon-modern.js::before {
    background: linear-gradient(135deg, #f0db4f, #f7df1e);
}

.related-item-modern:hover .related-icon-modern.php::before {
    background: linear-gradient(135deg, #777bb3, #4f5d95);
}

.related-content-modern {
    flex: 1;
    min-width: 0;
}

.related-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0 0 0.35rem 0;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
}

.related-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.badge-type {
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.2rem 0.6rem;
    background: #e9ecef;
    color: #495057;
    border-radius: 12px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.badge-info {
    font-size: 0.75rem;
    color: #718096;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.badge-info i {
    font-size: 0.7rem;
}

.related-arrow {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #cbd5e0;
    font-size: 0.875rem;
    flex-shrink: 0;
    transition: all 0.3s ease;
    opacity: 0.5;
}

.empty-related {
    padding: 3rem 1rem;
}

@keyframes slideInRelated {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
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

/* Estatísticas Modernas */
.bg-gradient-stats {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stats-grid-modern {
    display: grid;
    gap: 0;
}

.stat-item-modern {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.5rem;
    border-bottom: 1px solid #f1f3f4;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-item-modern:last-child {
    border-bottom: none;
}

.stat-item-modern::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: transparent;
    transition: all 0.3s ease;
}

.stat-item-modern:hover {
    background: #f8f9fa;
    transform: translateX(5px);
}

.stat-item-modern:nth-child(1):hover::before {
    background: linear-gradient(to bottom, #667eea, #764ba2);
}

.stat-item-modern:nth-child(2):hover::before {
    background: linear-gradient(to bottom, #f72585, #b5179e);
}

.stat-item-modern:nth-child(3):hover::before {
    background: linear-gradient(to bottom, #4cc9f0, #4361ee);
}

.stat-item-modern:nth-child(4):hover::before {
    background: linear-gradient(to bottom, #06ffa5, #06d6a0);
}

.stat-icon-container {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
    position: relative;
    transition: all 0.3s ease;
}

.stat-icon-container::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 15px;
    padding: 2px;
    background: linear-gradient(135deg, currentColor, transparent);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    mask-composite: exclude;
    opacity: 0.3;
}

.stat-icon-container.downloads {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    color: #667eea;
}

.stat-icon-container.ratings {
    background: linear-gradient(135deg, rgba(247, 37, 133, 0.1), rgba(181, 23, 158, 0.1));
    color: #f72585;
}

.stat-icon-container.views {
    background: linear-gradient(135deg, rgba(76, 201, 240, 0.1), rgba(67, 97, 238, 0.1));
    color: #4cc9f0;
}

.stat-icon-container.completion {
    background: linear-gradient(135deg, rgba(6, 255, 165, 0.1), rgba(6, 214, 160, 0.1));
    color: #06ffa5;
}

.stat-item-modern:hover .stat-icon-container {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.stat-details {
    flex: 1;
}

.stat-value-modern {
    font-size: 1.75rem;
    font-weight: 800;
    color: #2d3748;
    line-height: 1.2;
    margin-bottom: 0.25rem;
}

.stat-label-modern {
    font-size: 0.875rem;
    color: #718096;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.stat-trend {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.8rem;
    font-weight: 600;
    padding: 0.25rem 0.6rem;
    border-radius: 20px;
}

.stat-trend.positive {
    background: rgba(6, 214, 160, 0.1);
    color: #06d6a0;
}

.stat-trend.negative {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.stat-trend i {
    font-size: 0.7rem;
}

.stat-progress {
    height: 6px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.stat-progress .progress-bar {
    transition: width 1.5s ease;
    border-radius: 10px;
}

.stats-footer {
    background: linear-gradient(to bottom, transparent, #f8f9fa);
    padding: 1.25rem 1.5rem;
    border-top: 1px solid #e9ecef;
}

.footer-stat-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0;
    font-size: 0.875rem;
    color: #4a5568;
}

.footer-stat-item:first-child {
    border-bottom: 1px solid #e9ecef;
}

.footer-stat-item i {
    font-size: 1.1rem;
}

.footer-stat-item strong {
    color: #2d3748;
}

/* Animação de entrada */
@keyframes statFadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-item-modern {
    animation: statFadeIn 0.5s ease;
}

.stat-item-modern:nth-child(1) { animation-delay: 0.1s; }
.stat-item-modern:nth-child(2) { animation-delay: 0.2s; }
.stat-item-modern:nth-child(3) { animation-delay: 0.3s; }
.stat-item-modern:nth-child(4) { animation-delay: 0.4s; }

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

// Debug: Log das variáveis globais
console.log('🔧 Comunidade Debug:', {
    contentType: contentType,
    contentId: contentId,
    isLoggedIn: isLoggedIn
});

document.addEventListener('DOMContentLoaded', function() {
    // Carrega discussões e soluções ao abrir a aba de comunidade
    const communityTab = document.querySelector('[data-bs-target="#community"]');
    if (communityTab) {
        console.log('✅ Aba de comunidade encontrada');
        communityTab.addEventListener('shown.bs.tab', function() {
            console.log('📂 Aba de comunidade aberta - carregando dados...');
            loadDiscussions();
            loadSolutions();
        });
    } else {
        console.error('❌ Aba de comunidade NÃO encontrada');
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
                    showToast('Tutorial marcado como concluído!', 'success');
                    document.querySelector('.progress-bar').style.width = '100%';
                    document.querySelector('.progress-stats span:first-child').textContent = '100% completo';
                    document.querySelector('.progress-stats span:last-child').textContent = 'Concluído';
                    
                    // Redirecionar para página de progresso após 2 segundos
                    setTimeout(() => {
                        window.location.href = 'progress.php';
                    }, 2000);
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
    console.log('🔄 Carregando discussões...');
    
    const container = document.getElementById('discussionsList');
    if (!container) {
        console.error('❌ Container discussionsList não encontrado');
        return;
    }
    
    fetch(`api/get_discussions.php?content_type=${contentType}&content_id=${contentId}`)
        .then(response => {
            console.log('📥 Resposta recebida:', response.status);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('📊 Dados das discussões:', data);
            
            // Atualizar contador
            const totalElement = document.getElementById('totalDiscussions');
            if (totalElement) {
                totalElement.textContent = data.discussions ? data.discussions.length : 0;
            }
            
            if (!data.success || !data.discussions || data.discussions.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-comments"></i>
                        <h5>Nenhuma discussão ainda</h5>
                        <p>Seja o primeiro a iniciar uma conversa sobre este conteúdo!</p>
                        ${isLoggedIn ? '<button class="btn btn-primary" onclick="toggleAddDiscussion()"><i class="fas fa-plus me-2"></i>Iniciar Discussão</button>' : '<a href="login.php" class="btn btn-primary">Fazer login para participar</a>'}
                    </div>
                `;
                return;
            }
            
            console.log(`✅ ${data.discussions.length} discussões encontradas`);
            
            container.innerHTML = data.discussions.map(d => `
                <div class="discussion-item">
                    <div class="discussion-header">
                        <div class="user-avatar">${d.user_name ? d.user_name.charAt(0).toUpperCase() : 'U'}</div>
                        <div class="user-info">
                            <h6>${escapeHtml(d.user_name || 'Usuário')}</h6>
                            <span class="discussion-date">${timeAgo(d.created_at)}</span>
                        </div>
                    </div>
                    <div class="discussion-content">
                        <p>${escapeHtml(d.message)}</p>
                    </div>
                    <div class="discussion-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="likeDiscussion(${d.id}, this)">
                            <i class="fas fa-thumbs-up me-1"></i><span>${d.likes || 0}</span>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="toggleReplyForm(${d.id})">
                            <i class="fas fa-reply me-1"></i>Responder <span class="badge bg-secondary ms-1">${d.replies_count || 0}</span>
                        </button>
                    </div>
                    <div id="replies${d.id}" class="discussion-replies"></div>
                    <div id="replyForm${d.id}" class="reply-form" style="display: none;">
                        <textarea id="replyText${d.id}" class="form-control mb-2" rows="2" placeholder="Escreva sua resposta..."></textarea>
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-sm btn-secondary" onclick="toggleReplyForm(${d.id})">Cancelar</button>
                            <button class="btn btn-sm btn-primary" onclick="submitReply(${d.id})">Enviar</button>
                        </div>
                    </div>
                </div>
            `).join('');
        })
        .catch(err => {
            console.error('❌ Erro ao carregar discussões:', err);
            container.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Não foi possível carregar as discussões.</strong>
                    <p class="mb-2 small">A funcionalidade de comunidade requer configuração da API.</p>
                    <button class="btn btn-sm btn-outline-warning" onclick="loadDiscussions()">
                        <i class="fas fa-sync me-2"></i>Tentar novamente
                    </button>
                </div>
            `;
        });
}

// Carregar soluções
function loadSolutions() {
    console.log('🔄 Carregando soluções...');
    
    fetch(`api/get_solutions.php?content_type=${contentType}&content_id=${contentId}`)
        .then(response => {
            console.log('📥 Resposta recebida:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('📊 Dados das soluções:', data);
            const container = document.getElementById('solutionsList');
            
            if (!container) {
                console.error('❌ Container solutionsList não encontrado!');
                return;
            }
            
            if (!data.success || data.solutions.length === 0) {
                container.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb me-2"></i>
                        Nenhuma solução compartilhada ainda. Seja o primeiro!
                    </div>
                `;
                return;
            }
            
            console.log(`✅ ${data.solutions.length} soluções encontradas`);
            
            container.innerHTML = data.solutions.map(s => {
                const escapedCode = s.code.replace(/`/g, '\\`').replace(/\$/g, '\\$').replace(/\\/g, '\\\\');
                const escapedTitle = escapeHtml(s.title).replace(/'/g, "\\'");
                return `
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
                            <pre><code class="language-${s.language}">${escapeHtml(s.code.substring(0, 150))}${s.code.length > 150 ? '...' : ''}</code></pre>
                        </div>
                        <div class="solution-actions">
                            <button class="btn btn-sm btn-primary" onclick='viewFullSolution(${s.id}, "${escapedTitle}", "${s.language}", \`${escapedCode}\`)'>
                                <i class="fas fa-eye"></i> Ver Completo
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick='copySolutionCode(this, \`${escapedCode}\`)'>
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                            <span class="badge bg-success ms-2">
                                <i class="fas fa-thumbs-up"></i> ${s.likes}
                            </span>
                        </div>
                    </div>
                `;
            }).join('');
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
function copySolutionCode(button, code) {
    navigator.clipboard.writeText(code).then(() => {
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> Copiado';
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
        }, 2000);
        
        showToast('Código copiado!', 'success');
    }).catch(err => {
        console.error('Erro ao copiar:', err);
        showToast('Erro ao copiar código', 'error');
    });
}

// Ver solução completa em modal
function viewFullSolution(id, title, language, code) {
    const modalHtml = `
        <div class="modal fade" id="solutionModal${id}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${escapeHtml(title)}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <span class="badge bg-secondary">${language}</span>
                        </div>
                        <div class="modal-code-container">
                            <pre><code class="language-${language}">${escapeHtml(code)}</code></pre>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" onclick="copySolutionCode(this, \`${code.replace(/`/g, '\\`')}\`)">
                            <i class="fas fa-copy"></i> Copiar Código
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove modal anterior se existir
    const oldModal = document.getElementById(`solutionModal${id}`);
    if (oldModal) oldModal.remove();
    
    // Adiciona novo modal
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Abre o modal
    const modal = new bootstrap.Modal(document.getElementById(`solutionModal${id}`));
    modal.show();
}

// Alternar formulário de resposta
function toggleReplyForm(discussionId) {
    const form = document.getElementById(`replyForm${discussionId}`);
    const repliesContainer = document.getElementById(`replies${discussionId}`);
    
    if (form.style.display === 'none') {
        form.style.display = 'block';
        document.getElementById(`replyText${discussionId}`).focus();
        
        // Carregar respostas existentes
        loadReplies(discussionId);
    } else {
        form.style.display = 'none';
        repliesContainer.innerHTML = '';
    }
}

// Submeter resposta
function submitReply(discussionId) {
    if (!isLoggedIn) {
        showToast('Faça login para responder', 'warning');
        return;
    }
    
    const replyText = document.getElementById(`replyText${discussionId}`).value.trim();
    
    if (replyText.length < 5) {
        showToast('A resposta deve ter pelo menos 5 caracteres', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('discussion_id', discussionId);
    formData.append('message', replyText);
    
    fetch('api/add_reply.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Resposta publicada!', 'success');
            document.getElementById(`replyText${discussionId}`).value = '';
            loadReplies(discussionId);
            loadDiscussions(); // Atualiza contador
        } else {
            showToast(data.message || 'Erro ao responder', 'error');
        }
    })
    .catch(err => {
        console.error('Erro:', err);
        showToast('Erro ao publicar resposta', 'error');
    });
}

// Carregar respostas de uma discussão
function loadReplies(discussionId) {
    fetch(`api/get_replies.php?discussion_id=${discussionId}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById(`replies${discussionId}`);
            
            if (!data.success || data.replies.length === 0) {
                container.innerHTML = '<p class="text-muted small mt-2">Nenhuma resposta ainda</p>';
                return;
            }
            
            container.innerHTML = data.replies.map(r => `
                <div class="reply-item">
                    <div class="reply-header">
                        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(r.username)}&background=random" 
                             class="user-avatar" alt="${escapeHtml(r.username)}">
                        <strong>${escapeHtml(r.username)}</strong>
                        <small class="text-muted ms-2">${timeAgo(r.created_at)}</small>
                    </div>
                    <div class="reply-content">
                        ${escapeHtml(r.message)}
                    </div>
                </div>
            `).join('');
        })
        .catch(err => console.error('Erro ao carregar respostas:', err));
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