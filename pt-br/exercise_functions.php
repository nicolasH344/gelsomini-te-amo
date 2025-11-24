<?php
require_once 'config.php';
require_once 'data/exercises.php';

function getExercises($category = '', $difficulty = '', $search = '', $page = 1, $perPage = 9) {
    // Buscar todos os exercícios do arquivo de dados
    $allExercises = getExercisesData();
    
    // Aplicar filtros
    $exercises = $allExercises;
    
    if ($category) {
        $exercises = array_filter($exercises, fn($e) => $e['category'] === $category);
    }
    
    if ($difficulty) {
        $exercises = array_filter($exercises, fn($e) => $e['difficulty'] === $difficulty);
    }
    
    if ($search) {
        $exercises = array_filter($exercises, function($e) use ($search) {
            return stripos($e['title'], $search) !== false || 
                   stripos($e['description'], $search) !== false;
        });
    }
    
    // Paginação
    $offset = ($page - 1) * $perPage;
    return array_slice($exercises, $offset, $perPage);
}

function getExercise($id) {
    $exercises = getExercisesData();
    
    foreach ($exercises as $exercise) {
        if ($exercise['id'] == $id) {
            // Adicionar category_name para compatibilidade
            $exercise['category_name'] = $exercise['category'];
            return $exercise;
        }
    }
    
    return null;
}

function countExercises($category = '', $difficulty = '', $search = '') {
    $allExercises = getExercisesData();
    $exercises = $allExercises;
    
    if ($category) {
        $exercises = array_filter($exercises, fn($e) => $e['category'] === $category);
    }
    
    if ($difficulty) {
        $exercises = array_filter($exercises, fn($e) => $e['difficulty'] === $difficulty);
    }
    
    if ($search) {
        $exercises = array_filter($exercises, function($e) use ($search) {
            return stripos($e['title'], $search) !== false || 
                   stripos($e['description'], $search) !== false;
        });
    }
    
    return count($exercises);
}
?>
