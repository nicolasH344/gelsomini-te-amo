<?php
require_once __DIR__ . "/data/exercises.php";

// Função principal para buscar exercícios com filtros avançados
function getExercises($category = "", $difficulty = "", $search = "", $page = 1, $perPage = 9, $sortBy = 'id', $sortOrder = 'ASC') {
    $exercises = getExercisesData();
    
    // Filtro por categoria
    if ($category) {
        $exercises = array_filter($exercises, fn($e) => strcasecmp($e["category"] ?? "", $category) === 0);
    }
    
    // Filtro por dificuldade
    if ($difficulty) {
        $exercises = array_filter($exercises, fn($e) => strcasecmp($e["difficulty"] ?? "", $difficulty) === 0);
    }
    
    // Busca textual melhorada
    if ($search) {
        $search = strtolower(trim($search));
        $exercises = array_filter($exercises, function($e) use ($search) {
            $searchFields = [
                strtolower($e["title"] ?? ""),
                strtolower($e["description"] ?? ""),
                strtolower($e["instructions"] ?? ""),
                strtolower($e["category"] ?? "")
            ];
            
            foreach ($searchFields as $field) {
                if (strpos($field, $search) !== false) return true;
            }
            return false;
        });
    }
    
    // Ordenação
    usort($exercises, function($a, $b) use ($sortBy, $sortOrder) {
        $valueA = $a[$sortBy] ?? '';
        $valueB = $b[$sortBy] ?? '';
        
        if (is_numeric($valueA) && is_numeric($valueB)) {
            $result = $valueA <=> $valueB;
        } else {
            $result = strcasecmp($valueA, $valueB);
        }
        
        return $sortOrder === 'DESC' ? -$result : $result;
    });
    
    // Paginação
    $exercises = array_values($exercises);
    $offset = ($page - 1) * $perPage;
    return array_slice($exercises, $offset, $perPage);
}

function getExercise($id) {
    $exercises = getExercisesData();
    foreach ($exercises as $exercise) {
        if ($exercise["id"] == $id) return $exercise;
    }
    return null;
}

function countExercises($category = "", $difficulty = "", $search = "") {
    $exercises = getExercisesData();
    
    if ($category) {
        $exercises = array_filter($exercises, fn($e) => strcasecmp($e["category"] ?? "", $category) === 0);
    }
    
    if ($difficulty) {
        $exercises = array_filter($exercises, fn($e) => strcasecmp($e["difficulty"] ?? "", $difficulty) === 0);
    }
    
    if ($search) {
        $search = strtolower($search);
        $exercises = array_filter($exercises, function($e) use ($search) {
            $title = strtolower($e["title"] ?? "");
            $description = strtolower($e["description"] ?? "");
            return strpos($title, $search) !== false || strpos($description, $search) !== false;
        });
    }
    
    return count($exercises);
}

function getExerciseCategories() {
    $exercises = getExercisesData();
    $categories = [];
    foreach ($exercises as $exercise) {
        $cat = $exercise["category"] ?? "Outros";
        if (!isset($categories[$cat])) $categories[$cat] = 0;
        $categories[$cat]++;
    }
    return $categories;
}

function getRelatedExercises($currentId, $category, $limit = 3) {
    $exercises = getExercisesData();
    $related = array_filter($exercises, fn($e) => $e["id"] != $currentId && strcasecmp($e["category"] ?? "", $category) === 0);
    $related = array_values($related);
    return array_slice($related, 0, $limit);
}

// Função para obter exercícios por nível de dificuldade
function getExercisesByDifficulty($difficulty) {
    $exercises = getExercisesData();
    return array_filter($exercises, fn($e) => strcasecmp($e["difficulty"] ?? "", $difficulty) === 0);
}

// Função para obter estatísticas dos exercícios
function getExerciseStats() {
    $exercises = getExercisesData();
    $stats = [
        'total' => count($exercises),
        'by_category' => [],
        'by_difficulty' => [],
        'avg_points' => 0
    ];
    
    $totalPoints = 0;
    foreach ($exercises as $exercise) {
        $cat = $exercise['category'] ?? 'Outros';
        $diff = $exercise['difficulty'] ?? 'Iniciante';
        $points = $exercise['points'] ?? 10;
        
        $stats['by_category'][$cat] = ($stats['by_category'][$cat] ?? 0) + 1;
        $stats['by_difficulty'][$diff] = ($stats['by_difficulty'][$diff] ?? 0) + 1;
        $totalPoints += $points;
    }
    
    $stats['avg_points'] = $stats['total'] > 0 ? round($totalPoints / $stats['total'], 1) : 0;
    return $stats;
}

// Função para validar dados do exercício
function validateExerciseData($data) {
    $errors = [];
    
    if (empty($data['title'])) {
        $errors[] = 'Título é obrigatório';
    }
    
    if (empty($data['description'])) {
        $errors[] = 'Descrição é obrigatória';
    }
    
    if (empty($data['category'])) {
        $errors[] = 'Categoria é obrigatória';
    }
    
    $validCategories = ['HTML', 'CSS', 'JavaScript', 'PHP'];
    if (!in_array($data['category'], $validCategories)) {
        $errors[] = 'Categoria inválida';
    }
    
    $validDifficulties = ['Iniciante', 'Intermediário', 'Avançado'];
    if (!in_array($data['difficulty'], $validDifficulties)) {
        $errors[] = 'Dificuldade inválida';
    }
    
    return $errors;
}

// Função para buscar exercícios similares
function findSimilarExercises($exerciseId, $limit = 5) {
    $exercises = getExercisesData();
    $currentExercise = getExercise($exerciseId);
    
    if (!$currentExercise) return [];
    
    $similar = [];
    foreach ($exercises as $exercise) {
        if ($exercise['id'] == $exerciseId) continue;
        
        $score = 0;
        
        // Mesma categoria = +3 pontos
        if (strcasecmp($exercise['category'], $currentExercise['category']) === 0) {
            $score += 3;
        }
        
        // Mesma dificuldade = +2 pontos
        if (strcasecmp($exercise['difficulty'], $currentExercise['difficulty']) === 0) {
            $score += 2;
        }
        
        // Palavras em comum no título = +1 ponto por palavra
        $currentWords = explode(' ', strtolower($currentExercise['title']));
        $exerciseWords = explode(' ', strtolower($exercise['title']));
        $commonWords = array_intersect($currentWords, $exerciseWords);
        $score += count($commonWords);
        
        if ($score > 0) {
            $exercise['similarity_score'] = $score;
            $similar[] = $exercise;
        }
    }
    
    // Ordenar por score de similaridade
    usort($similar, fn($a, $b) => $b['similarity_score'] <=> $a['similarity_score']);
    
    return array_slice($similar, 0, $limit);
}

// Função para obter próximo exercício recomendado
function getNextRecommendedExercise($currentId, $userLevel = 'Iniciante') {
    $exercises = getExercisesData();
    $current = getExercise($currentId);
    
    if (!$current) return null;
    
    // Buscar exercícios da mesma categoria, próximo nível
    $nextLevel = [
        'Iniciante' => 'Intermediário',
        'Intermediário' => 'Avançado',
        'Avançado' => 'Avançado'
    ];
    
    $recommended = array_filter($exercises, function($e) use ($current, $nextLevel, $userLevel) {
        return $e['id'] != $current['id'] && 
               $e['category'] === $current['category'] && 
               $e['difficulty'] === $nextLevel[$userLevel];
    });
    
    return !empty($recommended) ? array_values($recommended)[0] : null;
}
?>
