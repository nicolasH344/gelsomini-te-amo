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
                'title' => 'Introdução ao HTML5',
                'description' => 'Aprenda os fundamentos do HTML5 e suas principais tags',
                'category' => 'HTML',
                'duration' => '15 min',
                'level' => 'Iniciante',
                'difficulty' => 'Iniciante',
                'views' => 1250,
                'status' => 'Publicado',
                'created_at' => '2024-01-15'
            ],
            [
                'id' => 2,
                'title' => 'CSS Grid Layout',
                'description' => 'Domine o sistema de grid do CSS para layouts modernos',
                'category' => 'CSS',
                'duration' => '25 min',
                'level' => 'Intermediário',
                'difficulty' => 'Intermediário',
                'views' => 890,
                'status' => 'Publicado',
                'created_at' => '2024-01-20'
            ],
            [
                'id' => 3,
                'title' => 'JavaScript ES6+',
                'description' => 'Conheça as funcionalidades modernas do JavaScript',
                'category' => 'JavaScript',
                'duration' => '30 min',
                'level' => 'Intermediário',
                'difficulty' => 'Intermediário',
                'views' => 2100,
                'status' => 'Publicado',
                'created_at' => '2024-01-25'
            ],
            [
                'id' => 4,
                'title' => 'Formulários Acessíveis',
                'description' => 'Crie formulários que funcionam para todos os usuários',
                'category' => 'HTML',
                'duration' => '20 min',
                'level' => 'Intermediário',
                'difficulty' => 'Intermediário',
                'views' => 650,
                'status' => 'Rascunho',
                'created_at' => '2024-02-01'
            ],
            [
                'id' => 5,
                'title' => 'Flexbox na Prática',
                'description' => 'Aprenda a usar Flexbox para layouts flexíveis',
                'category' => 'CSS',
                'duration' => '18 min',
                'level' => 'Iniciante',
                'difficulty' => 'Iniciante',
                'views' => 1800,
                'status' => 'Publicado',
                'created_at' => '2024-02-05'
            ],
            [
                'id' => 6,
                'title' => 'PHP Básico',
                'description' => 'Primeiros passos com PHP para desenvolvimento web',
                'category' => 'PHP',
                'duration' => '35 min',
                'level' => 'Iniciante',
                'difficulty' => 'Iniciante',
                'views' => 980,
                'status' => 'Publicado',
                'created_at' => '2024-02-10'
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
        'created_at' => date('Y-m-d')
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