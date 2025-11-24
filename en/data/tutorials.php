<?php
// Arquivo de dados dos tutoriais (English version)
$tutorialsFile = __DIR__ . '/tutorials.json';

// Função para obter tutoriais
function getTutorials() {
    global $tutorialsFile;
    
    if (!file_exists($tutorialsFile)) {
        // Criar arquivo com dados iniciais
        $defaultData = [
            [
                'id' => 1,
                'title' => '🏗️ HTML Structure: From Zero to Professional',
                'description' => 'Master the foundation of the web! Learn HTML5 with practical examples, modern semantics and accessibility.',
                'category' => 'HTML',
                'duration' => '25 min',
                'level' => 'Beginner',
                'difficulty' => 'Beginner',
                'views' => 3250,
                'status' => 'Published',
                'created_at' => '2024-01-15',
                'topics' => ['Basic tags', 'Semantics', 'Forms', 'Accessibility'],
                'visual_elements' => ['DOM diagrams', 'Flowcharts', 'Visual examples'],
                'prerequisites' => [],
                'learning_outcomes' => ['Create valid HTML pages', 'Use semantic tags', 'Implement forms']
            ],
            [
                'id' => 2,
                'title' => '🎨 CSS Grid: Modern and Responsive Layouts',
                'description' => 'Revolutionize your layouts! Learn CSS Grid with visual examples and interactive diagrams.',
                'category' => 'CSS',
                'duration' => '35 min',
                'level' => 'Intermediate',
                'difficulty' => 'Intermediate',
                'views' => 2890,
                'status' => 'Published',
                'created_at' => '2024-01-20',
                'topics' => ['Grid Container', 'Grid Items', 'Areas', 'Responsiveness'],
                'visual_elements' => ['Grid diagrams', 'CSS animations', 'Visual comparisons'],
                'prerequisites' => ['Basic HTML', 'Basic CSS'],
                'learning_outcomes' => ['Create complex layouts', 'Master grid areas', 'Responsive layouts']
            ],
            [
                'id' => 3,
                'title' => '⚡ Modern JavaScript: ES6+ in Practice',
                'description' => 'Elevate your JavaScript! Explore arrow functions, destructuring, async/await and more.',
                'category' => 'JavaScript',
                'duration' => '45 min',
                'level' => 'Intermediate',
                'difficulty' => 'Intermediate',
                'views' => 4100,
                'status' => 'Published',
                'created_at' => '2024-01-25',
                'topics' => ['Arrow Functions', 'Destructuring', 'Promises', 'Async/Await'],
                'visual_elements' => ['Execution flowcharts', 'Promise diagrams', 'Syntax comparisons'],
                'prerequisites' => ['Basic JavaScript', 'Basic DOM'],
                'learning_outcomes' => ['Use modern syntax', 'Asynchronous programming', 'Cleaner code']
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
            $tutorial['status'] = $tutorial['status'] === 'Published' ? 'Draft' : 'Published';
            break;
        }
    }
    
    saveTutorials($tutorials);
}
?>
