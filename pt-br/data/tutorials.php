<?php
// Arquivo de dados dos tutoriais
$tutorialsFile = __DIR__ . '/tutorials.json';

// Função para obter tutoriais
function getTutorials() {
    global $tutorialsFile;
    
    if (!file_exists($tutorialsFile)) {
        // Criar arquivo com dados iniciais
        $defaultData = [
            [
                'id' => 1,
                'title' => '🏗️ Estrutura HTML: Do Zero ao Profissional',
                'description' => 'Domine a base da web! Aprenda HTML5 com exemplos práticos, semântica moderna e acessibilidade. Inclui diagramas visuais e exercícios interativos.',
                'category' => 'HTML',
                'duration' => '25 min',
                'level' => 'Iniciante',
                'difficulty' => 'Iniciante',
                'views' => 3250,
                'status' => 'Publicado',
                'created_at' => '2024-01-15',
                'topics' => ['Tags básicas', 'Semântica', 'Formulários', 'Acessibilidade'],
                'visual_elements' => ['Diagramas DOM', 'Fluxogramas', 'Exemplos visuais'],
                'prerequisites' => [],
                'learning_outcomes' => ['Criar páginas HTML válidas', 'Usar tags semânticas', 'Implementar formulários']
            ],
            [
                'id' => 2,
                'title' => '🎨 CSS Grid: Layouts Modernos e Responsivos',
                'description' => 'Revolucione seus layouts! Aprenda CSS Grid com exemplos visuais, diagramas interativos e projetos práticos. De básico ao avançado.',
                'category' => 'CSS',
                'duration' => '35 min',
                'level' => 'Intermediário',
                'difficulty' => 'Intermediário',
                'views' => 2890,
                'status' => 'Publicado',
                'created_at' => '2024-01-20',
                'topics' => ['Grid Container', 'Grid Items', 'Areas', 'Responsividade'],
                'visual_elements' => ['Diagramas de grid', 'Animações CSS', 'Comparações visuais'],
                'prerequisites' => ['HTML básico', 'CSS básico'],
                'learning_outcomes' => ['Criar layouts complexos', 'Dominar grid areas', 'Layouts responsivos']
            ],
            [
                'id' => 3,
                'title' => '⚡ JavaScript Moderno: ES6+ na Prática',
                'description' => 'Eleve seu JavaScript! Explore arrow functions, destructuring, async/await e mais. Com diagramas de fluxo e exemplos interativos.',
                'category' => 'JavaScript',
                'duration' => '45 min',
                'level' => 'Intermediário',
                'difficulty' => 'Intermediário',
                'views' => 4100,
                'status' => 'Publicado',
                'created_at' => '2024-01-25',
                'topics' => ['Arrow Functions', 'Destructuring', 'Promises', 'Async/Await'],
                'visual_elements' => ['Fluxogramas de execução', 'Diagramas de promises', 'Comparações de sintaxe'],
                'prerequisites' => ['JavaScript básico', 'DOM básico'],
                'learning_outcomes' => ['Usar sintaxe moderna', 'Programação assíncrona', 'Código mais limpo']
            ],
            [
                'id' => 4,
                'title' => '♿ Formulários Web Acessíveis e Inclusivos',
                'description' => 'Crie formulários para todos! Aprenda validação, acessibilidade e UX com exemplos visuais e testes práticos.',
                'category' => 'HTML',
                'duration' => '30 min',
                'level' => 'Intermediário',
                'difficulty' => 'Intermediário',
                'views' => 1650,
                'status' => 'Publicado',
                'created_at' => '2024-02-01',
                'topics' => ['Validação HTML5', 'ARIA labels', 'UX patterns', 'Testes de acessibilidade'],
                'visual_elements' => ['Wireframes', 'Fluxos de usuário', 'Testes visuais'],
                'prerequisites' => ['HTML básico'],
                'learning_outcomes' => ['Formulários acessíveis', 'Validação eficaz', 'Melhor UX']
            ],
            [
                'id' => 5,
                'title' => '🔧 Flexbox: Layout Flexível e Intuitivo',
                'description' => 'Domine o Flexbox! Tutorial visual completo com diagramas interativos, casos de uso reais e exercícios práticos.',
                'category' => 'CSS',
                'duration' => '28 min',
                'level' => 'Iniciante',
                'difficulty' => 'Iniciante',
                'views' => 3800,
                'status' => 'Publicado',
                'created_at' => '2024-02-05',
                'topics' => ['Flex container', 'Flex items', 'Alinhamento', 'Casos práticos'],
                'visual_elements' => ['Diagramas flexbox', 'Animações de propriedades', 'Exemplos visuais'],
                'prerequisites' => ['HTML básico', 'CSS básico'],
                'learning_outcomes' => ['Layouts flexíveis', 'Alinhamento perfeito', 'Componentes responsivos']
            ],
            [
                'id' => 6,
                'title' => '🐘 PHP Fundamentals: Backend Descomplicado',
                'description' => 'Inicie no backend! PHP do básico ao intermediário com diagramas de arquitetura, exemplos práticos e projetos reais.',
                'category' => 'PHP',
                'duration' => '40 min',
                'level' => 'Iniciante',
                'difficulty' => 'Iniciante',
                'views' => 2980,
                'status' => 'Publicado',
                'created_at' => '2024-02-10',
                'topics' => ['Sintaxe básica', 'Variáveis', 'Funções', 'Formulários'],
                'visual_elements' => ['Diagramas de fluxo', 'Arquitetura web', 'Exemplos de código'],
                'prerequisites' => ['HTML básico'],
                'learning_outcomes' => ['Lógica de programação', 'Processamento de formulários', 'Conceitos de backend']
            ],
            [
                'id' => 7,
                'title' => '🎯 CSS Animations: Movimento e Vida na Web',
                'description' => 'Anime sua web! Aprenda CSS animations e transitions com exemplos visuais, timeline interativa e efeitos impressionantes.',
                'category' => 'CSS',
                'duration' => '32 min',
                'level' => 'Intermediário',
                'difficulty' => 'Intermediário',
                'views' => 2150,
                'status' => 'Publicado',
                'created_at' => '2024-02-15',
                'topics' => ['Keyframes', 'Transitions', 'Transform', 'Performance'],
                'visual_elements' => ['Timeline de animações', 'Exemplos interativos', 'Comparações de performance'],
                'prerequisites' => ['CSS intermediário'],
                'learning_outcomes' => ['Animações fluidas', 'Efeitos profissionais', 'Otimização de performance']
            ],
            [
                'id' => 8,
                'title' => '🌐 DOM Manipulation: JavaScript Interativo',
                'description' => 'Domine o DOM! Manipulação avançada com diagramas da árvore DOM, eventos interativos e projetos práticos.',
                'category' => 'JavaScript',
                'duration' => '38 min',
                'level' => 'Intermediário',
                'difficulty' => 'Intermediário',
                'views' => 3450,
                'status' => 'Publicado',
                'created_at' => '2024-02-20',
                'topics' => ['Seletores', 'Eventos', 'Manipulação', 'Performance'],
                'visual_elements' => ['Árvore DOM', 'Fluxo de eventos', 'Exemplos interativos'],
                'prerequisites' => ['JavaScript básico'],
                'learning_outcomes' => ['Manipulação eficiente', 'Eventos avançados', 'Interatividade rica']
            ]
        ];
        saveTutorials($defaultData);
        return $defaultData;
    }
    
    $data = file_get_contents($tutorialsFile);
    return json_decode($data, true) ?: [];
}

// Função para salvar tutoriais
function saveTutorials($tutorials) {
    global $tutorialsFile;
    file_put_contents($tutorialsFile, json_encode($tutorials, JSON_PRETTY_PRINT));
}

// Função para adicionar tutorial
function addTutorial($data) {
    $tutorials = getTutorials();
    $newId = empty($tutorials) ? 1 : max(array_column($tutorials, 'id')) + 1;
    
    $tutorial = [
        'id' => $newId,
        'title' => $data['title'],
        'description' => $data['description'],
        'category' => $data['category'],
        'duration' => $data['duration'] ?: '0 min',
        'level' => $data['difficulty'],
        'difficulty' => $data['difficulty'],
        'views' => 0,
        'status' => $data['status'],
        'created_at' => date('Y-m-d'),
        'topics' => $data['topics'] ?? [],
        'visual_elements' => $data['visual_elements'] ?? [],
        'prerequisites' => $data['prerequisites'] ?? [],
        'learning_outcomes' => $data['learning_outcomes'] ?? []
    ];
    
    $tutorials[] = $tutorial;
    saveTutorials($tutorials);
    return $newId;
}

// Função para atualizar tutorial
function updateTutorial($id, $data) {
    $tutorials = getTutorials();
    
    foreach ($tutorials as &$tutorial) {
        if ($tutorial['id'] == $id) {
            $tutorial['title'] = $data['title'];
            $tutorial['description'] = $data['description'];
            $tutorial['category'] = $data['category'];
            $tutorial['duration'] = $data['duration'] ?: $tutorial['duration'];
            $tutorial['level'] = $data['difficulty'];
            $tutorial['difficulty'] = $data['difficulty'];
            $tutorial['status'] = $data['status'];
            $tutorial['topics'] = $data['topics'] ?? $tutorial['topics'] ?? [];
            $tutorial['visual_elements'] = $data['visual_elements'] ?? $tutorial['visual_elements'] ?? [];
            $tutorial['prerequisites'] = $data['prerequisites'] ?? $tutorial['prerequisites'] ?? [];
            $tutorial['learning_outcomes'] = $data['learning_outcomes'] ?? $tutorial['learning_outcomes'] ?? [];
            break;
        }
    }
    
    saveTutorials($tutorials);
}

// Função para deletar tutorial
function deleteTutorial($id) {
    $tutorials = getTutorials();
    $tutorials = array_filter($tutorials, fn($t) => $t['id'] != $id);
    saveTutorials(array_values($tutorials));
}

// Função para alternar status
function toggleTutorialStatus($id) {
    $tutorials = getTutorials();
    
    foreach ($tutorials as &$tutorial) {
        if ($tutorial['id'] == $id) {
            $tutorial['status'] = $tutorial['status'] === 'Publicado' ? 'Rascunho' : 'Publicado';
            break;
        }
    }
    
    saveTutorials($tutorials);
}
?>