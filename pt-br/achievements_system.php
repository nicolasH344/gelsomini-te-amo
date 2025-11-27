<?php
// Sistema de Conquistas e Moedas
class AchievementsSystem {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
        $this->initializeTables();
    }
    
    private function initializeTables() {
        // Tabela de conquistas
        $this->db->conn->query("
            CREATE TABLE IF NOT EXISTS achievements (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                icon VARCHAR(50),
                color VARCHAR(20),
                coins_reward INT DEFAULT 0,
                requirement_type ENUM('exercises_completed', 'tutorials_completed', 'total_activities', 'streak_days', 'first_exercise', 'first_tutorial') NOT NULL,
                requirement_value INT DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Tabela de conquistas do usuário
        $this->db->conn->query("
            CREATE TABLE IF NOT EXISTS user_achievements (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                achievement_id INT NOT NULL,
                earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                coins_earned INT DEFAULT 0,
                UNIQUE KEY unique_user_achievement (user_id, achievement_id),
                FOREIGN KEY (achievement_id) REFERENCES achievements(id) ON DELETE CASCADE
            )
        ");
        
        // Tabela de moedas do usuário
        $this->db->conn->query("
            CREATE TABLE IF NOT EXISTS user_coins (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL UNIQUE,
                total_coins INT DEFAULT 0,
                earned_coins INT DEFAULT 0,
                spent_coins INT DEFAULT 0,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        
        $this->seedAchievements();
    }
    
    private function seedAchievements() {
        $achievements = [
            [
                'name' => 'Primeiro Passo',
                'description' => 'Completou seu primeiro exercício',
                'icon' => 'fas fa-medal',
                'color' => 'text-warning',
                'coins_reward' => 10,
                'requirement_type' => 'first_exercise',
                'requirement_value' => 1
            ],
            [
                'name' => 'Leitor Iniciante',
                'description' => 'Leu seu primeiro tutorial',
                'icon' => 'fas fa-book-open',
                'color' => 'text-info',
                'coins_reward' => 5,
                'requirement_type' => 'first_tutorial',
                'requirement_value' => 1
            ],
            [
                'name' => 'Praticante',
                'description' => 'Completou 5 exercícios',
                'icon' => 'fas fa-star',
                'color' => 'text-primary',
                'coins_reward' => 25,
                'requirement_type' => 'exercises_completed',
                'requirement_value' => 5
            ],
            [
                'name' => 'Estudioso',
                'description' => 'Leu 3 tutoriais',
                'icon' => 'fas fa-graduation-cap',
                'color' => 'text-success',
                'coins_reward' => 15,
                'requirement_type' => 'tutorials_completed',
                'requirement_value' => 3
            ],
            [
                'name' => 'Dedicado',
                'description' => 'Completou 10 atividades no total',
                'icon' => 'fas fa-fire',
                'color' => 'text-danger',
                'coins_reward' => 50,
                'requirement_type' => 'total_activities',
                'requirement_value' => 10
            ],
            [
                'name' => 'Experiente',
                'description' => 'Completou 20 exercícios',
                'icon' => 'fas fa-trophy',
                'color' => 'text-warning',
                'coins_reward' => 100,
                'requirement_type' => 'exercises_completed',
                'requirement_value' => 20
            ],
            [
                'name' => 'Mestre',
                'description' => 'Completou 50 atividades no total',
                'icon' => 'fas fa-crown',
                'color' => 'text-success',
                'coins_reward' => 200,
                'requirement_type' => 'total_activities',
                'requirement_value' => 50
            ]
        ];
        
        foreach ($achievements as $achievement) {
            $stmt = $this->db->conn->prepare("
                INSERT IGNORE INTO achievements (name, description, icon, color, coins_reward, requirement_type, requirement_value)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("ssssisi", 
                $achievement['name'],
                $achievement['description'],
                $achievement['icon'],
                $achievement['color'],
                $achievement['coins_reward'],
                $achievement['requirement_type'],
                $achievement['requirement_value']
            );
            $stmt->execute();
            $stmt->close();
        }
    }
    
    public function checkAndAwardAchievements($user_id) {
        $newAchievements = [];
        
        // Obter estatísticas do usuário
        $stats = $this->getUserStats($user_id);
        
        // Buscar conquistas não obtidas
        $stmt = $this->db->conn->prepare("
            SELECT a.* FROM achievements a
            LEFT JOIN user_achievements ua ON a.id = ua.achievement_id AND ua.user_id = ?
            WHERE ua.id IS NULL
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($achievement = $result->fetch_assoc()) {
            if ($this->checkRequirement($achievement, $stats)) {
                $this->awardAchievement($user_id, $achievement);
                $newAchievements[] = $achievement;
            }
        }
        
        $stmt->close();
        return $newAchievements;
    }
    
    private function getUserStats($user_id) {
        $stats = [
            'exercises_completed' => 0,
            'tutorials_completed' => 0,
            'total_activities' => 0
        ];
        
        // Exercícios completados
        $stmt = $this->db->conn->prepare("SELECT COUNT(*) as count FROM user_progress WHERE user_id = ? AND status = 'completed'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['exercises_completed'] = $row['count'];
        }
        $stmt->close();
        
        // Tutoriais completados
        $stmt = $this->db->conn->prepare("SELECT COUNT(*) as count FROM tutorial_progress WHERE user_id = ? AND status = 'completed'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['tutorials_completed'] = $row['count'];
        }
        $stmt->close();
        
        $stats['total_activities'] = $stats['exercises_completed'] + $stats['tutorials_completed'];
        
        return $stats;
    }
    
    private function checkRequirement($achievement, $stats) {
        switch ($achievement['requirement_type']) {
            case 'first_exercise':
                return $stats['exercises_completed'] >= 1;
            case 'first_tutorial':
                return $stats['tutorials_completed'] >= 1;
            case 'exercises_completed':
                return $stats['exercises_completed'] >= $achievement['requirement_value'];
            case 'tutorials_completed':
                return $stats['tutorials_completed'] >= $achievement['requirement_value'];
            case 'total_activities':
                return $stats['total_activities'] >= $achievement['requirement_value'];
            default:
                return false;
        }
    }
    
    private function awardAchievement($user_id, $achievement) {
        // Registrar conquista
        $stmt = $this->db->conn->prepare("
            INSERT INTO user_achievements (user_id, achievement_id, coins_earned)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iii", $user_id, $achievement['id'], $achievement['coins_reward']);
        $stmt->execute();
        $stmt->close();
        
        // Adicionar moedas
        $this->addCoins($user_id, $achievement['coins_reward']);
    }
    
    public function addCoins($user_id, $amount) {
        $stmt = $this->db->conn->prepare("
            INSERT INTO user_coins (user_id, total_coins, earned_coins)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE
            total_coins = total_coins + ?,
            earned_coins = earned_coins + ?
        ");
        $stmt->bind_param("iiiii", $user_id, $amount, $amount, $amount, $amount);
        $stmt->execute();
        $stmt->close();
    }
    
    public function getUserCoins($user_id) {
        $stmt = $this->db->conn->prepare("SELECT total_coins FROM user_coins WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $coins = $row['total_coins'];
        } else {
            $coins = 0;
        }
        
        $stmt->close();
        return $coins;
    }
    
    public function getUserAchievements($user_id) {
        $stmt = $this->db->conn->prepare("
            SELECT a.*, ua.earned_at, ua.coins_earned
            FROM achievements a
            JOIN user_achievements ua ON a.id = ua.achievement_id
            WHERE ua.user_id = ?
            ORDER BY ua.earned_at DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $achievements = [];
        while ($row = $result->fetch_assoc()) {
            $achievements[] = $row;
        }
        
        $stmt->close();
        return $achievements;
    }
    
    public function getAllAchievements() {
        $result = $this->db->conn->query("SELECT * FROM achievements ORDER BY requirement_value ASC");
        $achievements = [];
        
        while ($row = $result->fetch_assoc()) {
            $achievements[] = $row;
        }
        
        return $achievements;
    }
    
    public function completeExercise($user_id, $exercise_id, $score = 100) {
        // Registrar progresso do exercício
        $stmt = $this->db->conn->prepare("
            INSERT INTO user_progress (user_id, exercise_id, status, score, completed_at)
            VALUES (?, ?, 'completed', ?, NOW())
            ON DUPLICATE KEY UPDATE
            status = 'completed',
            score = GREATEST(score, ?),
            completed_at = NOW()
        ");
        $stmt->bind_param("iiii", $user_id, $exercise_id, $score, $score);
        $stmt->execute();
        $stmt->close();
        
        // Verificar e conceder conquistas
        return $this->checkAndAwardAchievements($user_id);
    }
    
    public function completeTutorial($user_id, $tutorial_id) {
        // Registrar progresso do tutorial
        $stmt = $this->db->conn->prepare("
            INSERT INTO tutorial_progress (user_id, tutorial_id, status, completed_at)
            VALUES (?, ?, 'completed', NOW())
            ON DUPLICATE KEY UPDATE
            status = 'completed',
            completed_at = NOW()
        ");
        $stmt->bind_param("ii", $user_id, $tutorial_id);
        $stmt->execute();
        $stmt->close();
        
        // Verificar e conceder conquistas
        return $this->checkAndAwardAchievements($user_id);
    }
}
?>