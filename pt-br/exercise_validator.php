<?php
class ExerciseValidator {
    
    public static function validateExercise($exerciseId, $userCode) {
        $exercise = getExercise($exerciseId);
        if (!$exercise) {
            return ['success' => false, 'message' => 'Exercício não encontrado'];
        }
        
        $category = $exercise['category'];
        $validationMethod = 'validate' . $category;
        
        if (method_exists(self::class, $validationMethod)) {
            return self::$validationMethod($exercise, $userCode);
        }
        
        return ['success' => true, 'message' => 'Validação não implementada para esta categoria'];
    }
    
    private static function validateHTML($exercise, $userCode) {
        $tests = [];
        $passed = 0;
        $total = 0;
        
        // Testes básicos para HTML
        switch ($exercise['id']) {
            case 1: // Minha Primeira Página HTML
                $tests = [
                    'DOCTYPE presente' => self::checkPattern($userCode, '/<!DOCTYPE\s+html>/i'),
                    'Tag html presente' => self::checkPattern($userCode, '/<html[^>]*>/i'),
                    'Tag head presente' => self::checkPattern($userCode, '/<head[^>]*>/i'),
                    'Tag body presente' => self::checkPattern($userCode, '/<body[^>]*>/i'),
                    'Tag title presente' => self::checkPattern($userCode, '/<title[^>]*>.*<\/title>/i'),
                    'Tag h1 presente' => self::checkPattern($userCode, '/<h1[^>]*>.*<\/h1>/i'),
                    'Tag p presente' => self::checkPattern($userCode, '/<p[^>]*>.*<\/p>/i')
                ];
                break;
                
            case 2: // Lista de Compras
                $tests = [
                    'Tag h2 presente' => self::checkPattern($userCode, '/<h2[^>]*>.*Lista.*<\/h2>/i'),
                    'Tag ul presente' => self::checkPattern($userCode, '/<ul[^>]*>/i'),
                    'Pelo menos 3 itens li' => self::countMatches($userCode, '/<li[^>]*>.*<\/li>/i') >= 3
                ];
                break;
                
            case 10: // Tabela de Dados
                $tests = [
                    'Tag table presente' => self::checkPattern($userCode, '/<table[^>]*>/i'),
                    'Tag thead presente' => self::checkPattern($userCode, '/<thead[^>]*>/i'),
                    'Tag tbody presente' => self::checkPattern($userCode, '/<tbody[^>]*>/i'),
                    'Tags th presentes' => self::countMatches($userCode, '/<th[^>]*>.*<\/th>/i') >= 3,
                    'Tags td presentes' => self::countMatches($userCode, '/<td[^>]*>.*<\/td>/i') >= 6
                ];
                break;
        }
        
        foreach ($tests as $testName => $result) {
            $total++;
            if ($result) $passed++;
        }
        
        $percentage = $total > 0 ? ($passed / $total) * 100 : 0;
        
        return [
            'success' => $percentage >= 70,
            'percentage' => round($percentage, 1),
            'tests' => $tests,
            'passed' => $passed,
            'total' => $total,
            'message' => $percentage >= 70 ? 'Exercício concluído com sucesso!' : 'Alguns testes falharam. Revise seu código.'
        ];
    }
    
    private static function validateCSS($exercise, $userCode) {
        $tests = [];
        $passed = 0;
        $total = 0;
        
        switch ($exercise['id']) {
            case 4: // Estilizando Texto
                $tests = [
                    'Cor definida' => self::checkPattern($userCode, '/color\s*:\s*[^;]+;/i'),
                    'Tamanho da fonte' => self::checkPattern($userCode, '/font-size\s*:\s*[^;]+;/i'),
                    'Peso da fonte' => self::checkPattern($userCode, '/font-weight\s*:\s*bold/i'),
                    'Altura da linha' => self::checkPattern($userCode, '/line-height\s*:\s*[^;]+;/i')
                ];
                break;
                
            case 5: // Layout com Flexbox
                $tests = [
                    'Display flex' => self::checkPattern($userCode, '/display\s*:\s*flex/i'),
                    'Gap definido' => self::checkPattern($userCode, '/gap\s*:\s*[^;]+;/i'),
                    'Flex nas caixas' => self::checkPattern($userCode, '/flex\s*:\s*1/i')
                ];
                break;
        }
        
        foreach ($tests as $testName => $result) {
            $total++;
            if ($result) $passed++;
        }
        
        $percentage = $total > 0 ? ($passed / $total) * 100 : 0;
        
        return [
            'success' => $percentage >= 70,
            'percentage' => round($percentage, 1),
            'tests' => $tests,
            'passed' => $passed,
            'total' => $total,
            'message' => $percentage >= 70 ? 'Exercício concluído com sucesso!' : 'Alguns testes falharam. Revise seu código.'
        ];
    }
    
    private static function validateJavaScript($exercise, $userCode) {
        $tests = [];
        $passed = 0;
        $total = 0;
        
        switch ($exercise['id']) {
            case 6: // Olá Mundo JavaScript
                $tests = [
                    'console.log presente' => self::checkPattern($userCode, '/console\.log\s*\(/i'),
                    'Variável declarada' => self::checkPattern($userCode, '/(let|const|var)\s+\w+/i'),
                    'String com nome' => self::checkPattern($userCode, '/["\'][^"\']*["\']/')
                ];
                break;
                
            case 7: // Calculadora Simples
                $tests = [
                    'Função somar definida' => self::checkPattern($userCode, '/function\s+somar\s*\(/i'),
                    'Return presente' => self::checkPattern($userCode, '/return\s+/i'),
                    'Operação de soma' => self::checkPattern($userCode, '/\+/')
                ];
                break;
                
            case 8: // Manipulação de Array
                $tests = [
                    'Loop for presente' => self::checkPattern($userCode, '/for\s*\(/i'),
                    'Array.length usado' => self::checkPattern($userCode, '/\.length/i'),
                    'Multiplicação por 2' => self::checkPattern($userCode, '/\*\s*2/')
                ];
                break;
        }
        
        foreach ($tests as $testName => $result) {
            $total++;
            if ($result) $passed++;
        }
        
        $percentage = $total > 0 ? ($passed / $total) * 100 : 0;
        
        return [
            'success' => $percentage >= 70,
            'percentage' => round($percentage, 1),
            'tests' => $tests,
            'passed' => $passed,
            'total' => $total,
            'message' => $percentage >= 70 ? 'Exercício concluído com sucesso!' : 'Alguns testes falharam. Revise seu código.'
        ];
    }
    
    private static function validatePHP($exercise, $userCode) {
        $tests = [];
        $passed = 0;
        $total = 0;
        
        switch ($exercise['id']) {
            case 9: // Olá Mundo PHP
                $tests = [
                    'Tag PHP presente' => self::checkPattern($userCode, '/<\?php/i'),
                    'Echo presente' => self::checkPattern($userCode, '/echo\s+/i'),
                    'Variável definida' => self::checkPattern($userCode, '/\$\w+\s*=/i')
                ];
                break;
                
            case 27: // Variáveis e Tipos
                $tests = [
                    'Variável string' => self::checkPattern($userCode, '/\$\w+\s*=\s*["\'][^"\']*["\']/'),
                    'Variável numérica' => self::checkPattern($userCode, '/\$\w+\s*=\s*\d+/'),
                    'var_dump usado' => self::checkPattern($userCode, '/var_dump\s*\(/i')
                ];
                break;
        }
        
        foreach ($tests as $testName => $result) {
            $total++;
            if ($result) $passed++;
        }
        
        $percentage = $total > 0 ? ($passed / $total) * 100 : 0;
        
        return [
            'success' => $percentage >= 70,
            'percentage' => round($percentage, 1),
            'tests' => $tests,
            'passed' => $passed,
            'total' => $total,
            'message' => $percentage >= 70 ? 'Exercício concluído com sucesso!' : 'Alguns testes falharam. Revise seu código.'
        ];
    }
    
    private static function checkPattern($code, $pattern) {
        return preg_match($pattern, $code) === 1;
    }
    
    private static function countMatches($code, $pattern) {
        return preg_match_all($pattern, $code);
    }
}

// API endpoint para validação
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {
    header('Content-Type: application/json');
    
    $exerciseId = (int)($_POST['exercise_id'] ?? 0);
    $userCode = $_POST['user_code'] ?? '';
    
    if ($exerciseId <= 0 || empty($userCode)) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        exit;
    }
    
    require_once 'exercise_functions.php';
    $result = ExerciseValidator::validateExercise($exerciseId, $userCode);
    echo json_encode($result);
    exit;
}
?>