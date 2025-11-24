<?php
// Archivo de datos de tutoriales (versión española)
$tutorialsFile = __DIR__ . '/tutorials.json';

// Función para obtener tutoriales
function getTutorials() {
    global $tutorialsFile;
    
    if (!file_exists($tutorialsFile)) {
        // Crear archivo con datos iniciales
        $defaultData = [
            [
                'id' => 1,
                'title' => '🏗️ Estructura HTML: De Cero a Profesional',
                'description' => '¡Domina la base de la web! Aprende HTML5 con ejemplos prácticos, semántica moderna y accesibilidad.',
                'category' => 'HTML',
                'duration' => '25 min',
                'level' => 'Principiante',
                'difficulty' => 'Principiante',
                'views' => 3250,
                'status' => 'Publicado',
                'created_at' => '2024-01-15',
                'topics' => ['Etiquetas básicas', 'Semántica', 'Formularios', 'Accesibilidad'],
                'visual_elements' => ['Diagramas DOM', 'Diagramas de flujo', 'Ejemplos visuales'],
                'prerequisites' => [],
                'learning_outcomes' => ['Crear páginas HTML válidas', 'Usar etiquetas semánticas', 'Implementar formularios']
            ],
            [
                'id' => 2,
                'title' => '🎨 CSS Grid: Diseños Modernos y Responsivos',
                'description' => '¡Revoluciona tus diseños! Aprende CSS Grid con ejemplos visuales y diagramas interactivos.',
                'category' => 'CSS',
                'duration' => '35 min',
                'level' => 'Intermedio',
                'difficulty' => 'Intermedio',
                'views' => 2890,
                'status' => 'Publicado',
                'created_at' => '2024-01-20',
                'topics' => ['Grid Container', 'Grid Items', 'Áreas', 'Responsividad'],
                'visual_elements' => ['Diagramas de grid', 'Animaciones CSS', 'Comparaciones visuales'],
                'prerequisites' => ['HTML básico', 'CSS básico'],
                'learning_outcomes' => ['Crear diseños complejos', 'Dominar áreas de grid', 'Diseños responsivos']
            ],
            [
                'id' => 3,
                'title' => '⚡ JavaScript Moderno: ES6+ en la Práctica',
                'description' => '¡Eleva tu JavaScript! Explora arrow functions, destructuring, async/await y más.',
                'category' => 'JavaScript',
                'duration' => '45 min',
                'level' => 'Intermedio',
                'difficulty' => 'Intermedio',
                'views' => 4100,
                'status' => 'Publicado',
                'created_at' => '2024-01-25',
                'topics' => ['Arrow Functions', 'Destructuring', 'Promises', 'Async/Await'],
                'visual_elements' => ['Diagramas de flujo', 'Diagramas de promesas', 'Comparaciones de sintaxis'],
                'prerequisites' => ['JavaScript básico', 'DOM básico'],
                'learning_outcomes' => ['Usar sintaxis moderna', 'Programación asíncrona', 'Código más limpio']
            ]
        ];
        saveTutorials($defaultData);
        return $defaultData;
    }
    
    $data = file_get_contents($tutorialsFile);
    return json_decode($data, true) ?: [];
}

// Función para guardar tutoriales
function saveTutorials($tutorials) {
    global $tutorialsFile;
    file_put_contents($tutorialsFile, json_encode($tutorials, JSON_PRETTY_PRINT));
}

// Función para agregar tutorial
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

// Función para actualizar tutorial
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

// Función para eliminar tutorial
function deleteTutorial($id) {
    $tutorials = getTutorials();
    $tutorials = array_filter($tutorials, fn($t) => $t['id'] != $id);
    saveTutorials(array_values($tutorials));
}

// Función para alternar estado
function toggleTutorialStatus($id) {
    $tutorials = getTutorials();
    
    foreach ($tutorials as &$tutorial) {
        if ($tutorial['id'] == $id) {
            $tutorial['status'] = $tutorial['status'] === 'Publicado' ? 'Borrador' : 'Publicado';
            break;
        }
    }
    
    saveTutorials($tutorials);
}
?>
