<?php
require_once 'config.php';

function getExercises($category = '', $difficulty = '', $search = '', $page = 1, $perPage = 9) {
    $conn = getDBConnection();
    if (!$conn) {
        return [
            ['id' => 1, 'title' => 'Estrutura Básica HTML', 'description' => 'Aprenda a criar a estrutura básica de uma página HTML', 'difficulty_level' => 'beginner', 'category_name' => 'HTML'],
            ['id' => 2, 'title' => 'Estilização com CSS', 'description' => 'Pratique estilização básica com CSS', 'difficulty_level' => 'beginner', 'category_name' => 'CSS'],
            ['id' => 3, 'title' => 'Interatividade com JavaScript', 'description' => 'Adicione interatividade às suas páginas', 'difficulty_level' => 'intermediate', 'category_name' => 'JavaScript']
        ];
    }
    
    $offset = ($page - 1) * $perPage;
    $sql = "SELECT e.*, ec.name as category_name
            FROM exercises e 
            LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
            WHERE 1=1";
    $params = [];
    
    if ($category) {
        $sql .= " AND ec.name = ?";
        $params[] = $category;
    }
    
    if ($difficulty) {
        $difficulty_map = ['Iniciante' => 'beginner', 'Intermediário' => 'intermediate', 'Avançado' => 'advanced'];
        if (isset($difficulty_map[$difficulty])) {
            $sql .= " AND e.difficulty_level = ?";
            $params[] = $difficulty_map[$difficulty];
        }
    }
    
    if ($search) {
        $sql .= " AND (e.title LIKE ? OR e.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY e.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $perPage;
    $params[] = $offset;
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getExercise($id) {
    $conn = getDBConnection();
    
    // Dados mockados por ID (enquanto não há banco)
    $mock_exercises = [
        1 => [
            'id' => 1,
            'title' => 'Minha Primeira Página HTML',
            'description' => 'Crie uma página HTML básica com título e parágrafo',
            'category_name' => 'HTML',
            'difficulty_level' => 'beginner',
            'instructions' => 'Crie uma página HTML5 válida que contenha:\n- Uma estrutura básica com DOCTYPE, html, head e body\n- Um título na tag <title>\n- Um cabeçalho <h1> com o texto "Minha Primeira Página"\n- Um parágrafo <p> com uma breve descrição sobre você',
            'initial_code' => '<!DOCTYPE html>\n<html lang="pt-BR">\n<head>\n    <meta charset="UTF-8">\n    <title>Minha Página</title>\n</head>\n<body>\n    <!-- Adicione seu código aqui -->\n</body>\n</html>',
            'hints' => 'Use a tag <h1> para o título principal\nLembre-se de fechar todas as tags corretamente',
            'exercise_type' => 'HTML'
        ],
        2 => [
            'id' => 2,
            'title' => 'Lista de Compras',
            'description' => 'Crie uma lista não ordenada com itens de compras',
            'category_name' => 'HTML',
            'difficulty_level' => 'beginner',
            'instructions' => 'Crie uma página HTML com:\n- Um título <h2> "Minha Lista de Compras"\n- Uma lista não ordenada <ul> com pelo menos 5 itens de compras\n- Cada item deve estar dentro de uma tag <li>',
            'initial_code' => '<!DOCTYPE html>\n<html lang="pt-BR">\n<head>\n    <meta charset="UTF-8">\n    <title>Lista de Compras</title>\n</head>\n<body>\n    <h2>Minha Lista de Compras</h2>\n    <!-- Crie a lista aqui -->\n</body>\n</html>',
            'hints' => 'Use <ul> para lista não ordenada\nCada item vai dentro de <li>',
            'exercise_type' => 'HTML'
        ],
        3 => [
            'id' => 3,
            'title' => 'Formulário de Contato',
            'description' => 'Crie um formulário básico com campos de nome, email e mensagem',
            'category_name' => 'HTML',
            'difficulty_level' => 'beginner',
            'instructions' => 'Crie um formulário HTML com:\n- Tag <form> com action e method\n- Campo de texto para nome (type="text")\n- Campo de email (type="email")\n- Área de texto para mensagem (<textarea>)\n- Botão de envio (type="submit")',
            'initial_code' => '<!DOCTYPE html>\n<html lang="pt-BR">\n<head>\n    <meta charset="UTF-8">\n    <title>Contato</title>\n</head>\n<body>\n    <h2>Entre em Contato</h2>\n    <form>\n        <!-- Adicione os campos aqui -->\n    </form>\n</body>\n</html>',
            'hints' => 'Use <label> para cada campo\nNão esqueça o atributo name em cada input',
            'exercise_type' => 'HTML'
        ],
        4 => [
            'id' => 4,
            'title' => 'Estilizando Texto',
            'description' => 'Aplique estilos básicos a um parágrafo',
            'category_name' => 'CSS',
            'difficulty_level' => 'beginner',
            'instructions' => 'Crie estilos CSS para:\n- Mudar a cor do texto para azul (#2563eb)\n- Definir o tamanho da fonte para 18px\n- Aplicar negrito (font-weight: bold)\n- Adicionar espaçamento entre linhas (line-height: 1.6)',
            'initial_code' => '<!DOCTYPE html>\n<html lang="pt-BR">\n<head>\n    <meta charset="UTF-8">\n    <title>Estilizando Texto</title>\n    <style>\n        p {\n            /* Adicione seus estilos aqui */\n        }\n    </style>\n</head>\n<body>\n    <p>Este é um parágrafo que será estilizado.</p>\n</body>\n</html>',
            'hints' => 'Use color para cor do texto\nUse font-size para tamanho',
            'exercise_type' => 'CSS'
        ],
        5 => [
            'id' => 5,
            'title' => 'Layout com Flexbox',
            'description' => 'Crie um layout flexível com três caixas',
            'category_name' => 'CSS',
            'difficulty_level' => 'beginner',
            'instructions' => 'Use Flexbox para criar um layout com:\n- Um container com display: flex\n- Três divs filhas com largura igual\n- Espaçamento entre as caixas (gap: 20px)\n- Cada caixa com cor de fundo diferente e padding',
            'initial_code' => '<!DOCTYPE html>\n<html lang="pt-BR">\n<head>\n    <meta charset="UTF-8">\n    <title>Flexbox Layout</title>\n    <style>\n        .container {\n            /* Adicione flexbox aqui */\n        }\n        .box {\n            /* Estilize as caixas */\n        }\n    </style>\n</head>\n<body>\n    <div class="container">\n        <div class="box">Caixa 1</div>\n        <div class="box">Caixa 2</div>\n        <div class="box">Caixa 3</div>\n    </div>\n</body>\n</html>',
            'hints' => 'Use display: flex no container\nUse flex: 1 nas caixas para largura igual',
            'exercise_type' => 'CSS'
        ],
        6 => [
            'id' => 6,
            'title' => 'Olá Mundo JavaScript',
            'description' => 'Exiba uma mensagem no console',
            'category_name' => 'JavaScript',
            'difficulty_level' => 'beginner',
            'instructions' => 'Escreva código JavaScript para:\n- Exibir "Olá, Mundo!" no console\n- Criar uma variável com seu nome\n- Exibir uma mensagem personalizada com seu nome',
            'initial_code' => '// Seu código JavaScript aqui\n',
            'hints' => 'Use console.log() para exibir no console\nUse const ou let para criar variáveis',
            'exercise_type' => 'JavaScript'
        ],
        7 => [
            'id' => 7,
            'title' => 'Calculadora Simples',
            'description' => 'Crie uma função que soma dois números',
            'category_name' => 'JavaScript',
            'difficulty_level' => 'beginner',
            'instructions' => 'Crie uma função JavaScript que:\n- Receba dois números como parâmetros\n- Retorne a soma desses números\n- Teste a função com console.log()',
            'initial_code' => 'function somar(a, b) {\n    // Seu código aqui\n}\n\n// Teste sua função\nconsole.log(somar(5, 3)); // Deve exibir 8',
            'hints' => 'Use return para retornar o resultado\nUse o operador + para somar',
            'exercise_type' => 'JavaScript'
        ],
        8 => [
            'id' => 8,
            'title' => 'Manipulação de Array',
            'description' => 'Trabalhe com arrays e loops',
            'category_name' => 'JavaScript',
            'difficulty_level' => 'beginner',
            'instructions' => 'Crie código JavaScript para:\n- Criar um array com 5 números\n- Usar um loop for para percorrer o array\n- Exibir cada número multiplicado por 2',
            'initial_code' => 'const numeros = [1, 2, 3, 4, 5];\n\n// Use um loop for aqui\n',
            'hints' => 'Use array.length para saber o tamanho\nUse for (let i = 0; i < array.length; i++)',
            'exercise_type' => 'JavaScript'
        ],
        9 => [
            'id' => 9,
            'title' => 'Olá Mundo PHP',
            'description' => 'Exiba uma mensagem usando PHP',
            'category_name' => 'PHP',
            'difficulty_level' => 'beginner',
            'instructions' => 'Escreva código PHP para:\n- Usar echo para exibir "Olá, Mundo!"\n- Criar uma variável com seu nome\n- Exibir uma mensagem personalizada',
            'initial_code' => '<?php\n// Seu código PHP aqui\n\n?>',
            'hints' => 'Use echo ou print para exibir\nVariáveis começam com $',
            'exercise_type' => 'PHP'
        ]
    ];
    
    // Se tiver conexão com banco, tenta buscar de lá
    if ($conn) {
        $stmt = $conn->prepare("SELECT e.*, ec.name as category_name
                               FROM exercises e 
                               LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
                               WHERE e.id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        // Se encontrou no banco, usa do banco
        if ($result) {
            return $result;
        }
    }
    
    // Senão, retorna os dados mockados
    return $mock_exercises[$id] ?? null;
}

function countExercises($category = '', $difficulty = '', $search = '') {
    $conn = getDBConnection();
    if (!$conn) return 6;
    
    $sql = "SELECT COUNT(*) as total FROM exercises e 
            LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
            WHERE 1=1";
    $params = [];
    
    if ($category) {
        $sql .= " AND ec.name = ?";
        $params[] = $category;
    }
    
    if ($difficulty) {
        $difficulty_map = ['Iniciante' => 'beginner', 'Intermediário' => 'intermediate', 'Avançado' => 'advanced'];
        if (isset($difficulty_map[$difficulty])) {
            $sql .= " AND e.difficulty_level = ?";
            $params[] = $difficulty_map[$difficulty];
        }
    }
    
    if ($search) {
        $sql .= " AND (e.title LIKE ? OR e.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}
?>