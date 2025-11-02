<?php
namespace App\Controllers;

use App\Models\Exercise;
use App\Models\UserProgress;

class ExerciseController extends BaseController {
    private $exerciseModel;
    private $progressModel;
    
    public function __construct() {
        $this->exerciseModel = new Exercise();
        $this->progressModel = new UserProgress();
    }
    
    public function index() {
        // Parâmetros de filtro
        $category = $this->sanitize($_GET['category'] ?? '');
        $difficulty = $this->sanitize($_GET['difficulty'] ?? '');
        $search = $this->sanitize($_GET['search'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 9;
        
        // Buscar dados
        $exercises = $this->exerciseModel->getExercisesWithCategory($category, $difficulty, $search, $page, $perPage);
        $totalResults = $this->exerciseModel->countExercises($category, $difficulty, $search);
        $totalPages = $totalResults > 0 ? ceil($totalResults / $perPage) : 1;
        
        // Buscar exercícios completados pelo usuário
        $completedIds = [];
        $userStats = null;
        if ($this->isLoggedIn()) {
            $userId = $this->getCurrentUser()['id'];
            $completedIds = $this->progressModel->getCompletedExerciseIds($userId);
            $userStats = $this->progressModel->getUserStats($userId);
        }
        
        return [
            'exercises' => $exercises,
            'totalResults' => $totalResults,
            'totalPages' => $totalPages,
            'category' => $category,
            'difficulty' => $difficulty,
            'search' => $search,
            'page' => $page,
            'completedIds' => $completedIds,
            'userStats' => $userStats
        ];
    }
    
    public function show($id) {
        $exercise = $this->exerciseModel->getExerciseWithCategory($id);
        if (!$exercise) {
            $this->redirect('exercises_index.php');
        }
        
        // Buscar progresso do usuário
        $userProgress = null;
        $isCompleted = false;
        if ($this->isLoggedIn()) {
            $userId = $this->getCurrentUser()['id'];
            $userProgress = $this->progressModel->getExerciseProgress($userId, $id);
            $isCompleted = $userProgress && $userProgress['completed'];
        }
        
        // Processar submissão de código
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_code']) && $this->isLoggedIn()) {
            $userCode = $_POST['user_code'] ?? '';
            $userId = $this->getCurrentUser()['id'];
            
            // Simular avaliação do código (aqui você pode implementar lógica mais complexa)
            $score = $this->evaluateCode($userCode, $exercise);
            
            if ($this->progressModel->markExerciseCompleted($userId, $id, $userCode, $score)) {
                $feedback = "Exercício concluído! Pontuação: {$score}%";
                $this->setSuccess($feedback);
                $this->redirect("exercise_detail_oop.php?id=$id");
            }
        }
        
        return [
            'exercise' => $exercise,
            'userProgress' => $userProgress,
            'isCompleted' => $isCompleted
        ];
    }
    
    private function evaluateCode($userCode, $exercise) {
        // Simulação simples de avaliação
        $solutionCode = $exercise['solution_code'] ?? '';
        
        if (empty($userCode)) return 0;
        
        // Verificações básicas
        $score = 50; // Pontuação base por tentar
        
        // Bonus por ter conteúdo
        if (strlen($userCode) > 20) $score += 20;
        
        // Bonus por tags HTML básicas
        if (strpos($userCode, '<html>') !== false) $score += 10;
        if (strpos($userCode, '<head>') !== false) $score += 5;
        if (strpos($userCode, '<body>') !== false) $score += 10;
        if (strpos($userCode, '<title>') !== false) $score += 5;
        
        return min(100, $score);
    }
    
    public function progress() {
        if (!$this->isLoggedIn()) {
            $this->redirect('login.php');
        }
        
        $userId = $this->getCurrentUser()['id'];
        $userStats = $this->progressModel->getUserStats($userId);
        $categoryProgress = $this->progressModel->getCategoryProgress($userId);
        
        return [
            'userStats' => $userStats,
            'categoryProgress' => $categoryProgress
        ];
    }
}
?>