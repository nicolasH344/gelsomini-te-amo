<?php
require_once __DIR__ . "/data/exercises.php";

function getExercises($category = "", $difficulty = "", $search = "", $page = 1, $perPage = 9) {
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
?>
