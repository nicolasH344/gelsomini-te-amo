<?php
// Arquivo de dados dos exercícios
$exercisesFile = __DIR__ . '/exercises.json';

// Função para obter exercícios
function getExercisesData() {
    global $exercisesFile;
    
    if (!file_exists($exercisesFile)) {
        // Criar arquivo com dados iniciais
        $defaultData = [
            [
                'id' => 1,
                'title' => 'Minha Primeira Página HTML',
                'description' => 'Crie uma página HTML básica com título e parágrafo',
                'category' => 'HTML',
                'difficulty' => 'Iniciante',
                'difficulty_level' => 'beginner',
                'points' => 10,
                'estimated_time' => '15 min',
                'instructions' => 'Crie uma página HTML5 válida que contenha:\n- Uma estrutura básica com DOCTYPE, html, head e body\n- Um título na tag <title>\n- Um cabeçalho <h1> com o texto "Minha Primeira Página"\n- Um parágrafo <p> com uma breve descrição sobre você',
                'initial_code' => '<!DOCTYPE html>\n<html lang="pt-BR">\n<head>\n    <meta charset="UTF-8">\n    <title>Minha Página</title>\n</head>\n<body>\n    <!-- Adicione seu código aqui -->\n</body>\n</html>',
                'hints' => ['Use a tag <h1> para o título principal', 'Lembre-se de fechar todas as tags corretamente', 'Use meta charset="UTF-8" para acentuação'],
                'exercise_type' => 'HTML',
                'created_at' => '2024-01-15'
            ],
            [
                'id' => 2,
                'title' => 'Lista de Compras',
                'description' => 'Crie uma lista não ordenada com itens de compras',
                'category' => 'HTML',
                'difficulty' => 'Iniciante',
                'difficulty_level' => 'beginner',
                'points' => 10,
                'estimated_time' => '10 min',
                'instructions' => 'Crie uma página HTML com:\n- Um título <h2> "Minha Lista de Compras"\n- Uma lista não ordenada <ul> com pelo menos 5 itens de compras\n- Cada item deve estar dentro de uma tag <li>',
                'initial_code' => '<!DOCTYPE html>\n<html lang="pt-BR">\n<head>\n    <meta charset="UTF-8">\n    <title>Lista de Compras</title>\n</head>\n<body>\n    <h2>Minha Lista de Compras</h2>\n    <!-- Crie a lista aqui -->\n</body>\n</html>',
                'hints' => ['Use <ul> para lista não ordenada', 'Cada item vai dentro de <li>', 'Você pode aninhar listas se quiser'],
                'exercise_type' => 'HTML',
                'created_at' => '2024-01-16'
            ],
            [
                'id' => 3,
                'title' => 'Formulário de Contato',
                'description' => 'Crie um formulário básico com campos de nome, email e mensagem',
                'category' => 'HTML',
                'difficulty' => 'Iniciante',
                'difficulty_level' => 'beginner',
                'points' => 15,
                'estimated_time' => '20 min',
                'instructions' => 'Crie um formulário HTML com:\n- Tag <form> com action e method\n- Campo de texto para nome (type="text")\n- Campo de email (type="email")\n- Área de texto para mensagem (<textarea>)\n- Botão de envio (type="submit")',
                'initial_code' => '<!DOCTYPE html>\n<html lang="pt-BR">\n<head>\n    <meta charset="UTF-8">\n    <title>Contato</title>\n</head>\n<body>\n    <h2>Entre em Contato</h2>\n    <form>\n        <!-- Adicione os campos aqui -->\n    </form>\n</body>\n</html>',
                'hints' => ['Use <label> para cada campo', 'Não esqueça o atributo name em cada input', 'Use placeholder para dar dicas ao usuário'],
                'exercise_type' => 'HTML',
                'created_at' => '2024-01-17'
            ],
            [
                'id' => 4,
                'title' => 'Estilizando Texto',
                'description' => 'Aplique estilos básicos a um parágrafo',
                'category' => 'CSS',
                'difficulty' => 'Iniciante',
                'difficulty_level' => 'beginner',
                'points' => 10,
                'estimated_time' => '15 min',
                'instructions' => 'Crie estilos CSS para:\n- Mudar a cor do texto para azul (#2563eb)\n- Definir o tamanho da fonte para 18px\n- Aplicar negrito (font-weight: bold)\n- Adicionar espaçamento entre linhas (line-height: 1.6)',
                'initial_code' => '<!DOCTYPE html>\n<html lang="pt-BR">\n<head>\n    <meta charset="UTF-8">\n    <title>Estilizando Texto</title>\n    <style>\n        p {\n            /* Adicione seus estilos aqui */\n        }\n    </style>\n</head>\n<body>\n    <p>Este é um parágrafo que será estilizado.</p>\n</body>\n</html>',
                'hints' => ['Use color para cor do texto', 'Use font-size para tamanho', 'Valores em px são pixels'],
                'exercise_type' => 'CSS',
                'created_at' => '2024-01-18'
            ],
            [
                'id' => 5,
                'title' => 'Layout com Flexbox',
                'description' => 'Crie um layout flexível com três caixas',
                'category' => 'CSS',
                'difficulty' => 'Intermediário',
                'difficulty_level' => 'intermediate',
                'points' => 20,
                'estimated_time' => '25 min',
                'instructions' => 'Use Flexbox para criar um layout com:\n- Um container com display: flex\n- Três divs filhas com largura igual\n- Espaçamento entre as caixas (gap: 20px)\n- Cada caixa com cor de fundo diferente e padding',
                'initial_code' => '<!DOCTYPE html>\n<html lang="pt-BR">\n<head>\n    <meta charset="UTF-8">\n    <title>Flexbox Layout</title>\n    <style>\n        .container {\n            /* Adicione flexbox aqui */\n        }\n        .box {\n            /* Estilize as caixas */\n        }\n    </style>\n</head>\n<body>\n    <div class="container">\n        <div class="box">Caixa 1</div>\n        <div class="box">Caixa 2</div>\n        <div class="box">Caixa 3</div>\n    </div>\n</body>\n</html>',
                'hints' => ['Use display: flex no container', 'Use flex: 1 nas caixas para largura igual', 'gap cria espaçamento automático'],
                'exercise_type' => 'CSS',
                'created_at' => '2024-01-19'
            ],
            [
                'id' => 6,
                'title' => 'Olá Mundo JavaScript',
                'description' => 'Exiba uma mensagem no console',
                'category' => 'JavaScript',
                'difficulty' => 'Iniciante',
                'difficulty_level' => 'beginner',
                'points' => 10,
                'estimated_time' => '10 min',
                'instructions' => 'Escreva código JavaScript para:\n- Exibir "Olá, Mundo!" no console\n- Criar uma variável com seu nome\n- Exibir uma mensagem personalizada com seu nome',
                'initial_code' => '// Seu código JavaScript aqui\n',
                'hints' => ['Use console.log() para exibir no console', 'Use const ou let para criar variáveis', 'Use template strings com `${variavel}`'],
                'exercise_type' => 'JavaScript',
                'created_at' => '2024-01-20'
            ],
            [
                'id' => 7,
                'title' => 'Calculadora Simples',
                'description' => 'Crie uma função que soma dois números',
                'category' => 'JavaScript',
                'difficulty' => 'Iniciante',
                'difficulty_level' => 'beginner',
                'points' => 15,
                'estimated_time' => '15 min',
                'instructions' => 'Crie uma função JavaScript que:\n- Receba dois números como parâmetros\n- Retorne a soma desses números\n- Teste a função com console.log()',
                'initial_code' => 'function somar(a, b) {\n    // Seu código aqui\n}\n\n// Teste sua função\nconsole.log(somar(5, 3)); // Deve exibir 8',
                'hints' => ['Use return para retornar o resultado', 'Use o operador + para somar', 'Teste com diferentes valores'],
                'exercise_type' => 'JavaScript',
                'created_at' => '2024-01-21'
            ],
            [
                'id' => 8,
                'title' => 'Manipulação de Array',
                'description' => 'Trabalhe com arrays e loops',
                'category' => 'JavaScript',
                'difficulty' => 'Intermediário',
                'difficulty_level' => 'intermediate',
                'points' => 20,
                'estimated_time' => '20 min',
                'instructions' => 'Crie código JavaScript para:\n- Criar um array com 5 números\n- Usar um loop for para percorrer o array\n- Exibir cada número multiplicado por 2',
                'initial_code' => 'const numeros = [1, 2, 3, 4, 5];\n\n// Use um loop for aqui\n',
                'hints' => ['Use array.length para saber o tamanho', 'Use for (let i = 0; i < array.length; i++)', 'Acesse elementos com array[i]'],
                'exercise_type' => 'JavaScript',
                'created_at' => '2024-01-22'
            ],
            [
                'id' => 9,
                'title' => 'Olá Mundo PHP',
                'description' => 'Exiba uma mensagem usando PHP',
                'category' => 'PHP',
                'difficulty' => 'Iniciante',
                'difficulty_level' => 'beginner',
                'points' => 10,
                'estimated_time' => '10 min',
                'instructions' => 'Escreva código PHP para:\n- Usar echo para exibir "Olá, Mundo!"\n- Criar uma variável com seu nome\n- Exibir uma mensagem personalizada',
                'initial_code' => '<?php\n// Seu código PHP aqui\n\n?>',
                'hints' => ['Use echo ou print para exibir', 'Variáveis começam com $', 'Use . para concatenar strings'],
                'exercise_type' => 'PHP',
                'created_at' => '2024-01-23'
            ]
        ];
        saveExercisesData($defaultData);
        return $defaultData;
    }
    
    $data = file_get_contents($exercisesFile);
    return json_decode($data, true) ?: [];
}

// Função para salvar exercícios
function saveExercisesData($exercises) {
    global $exercisesFile;
    file_put_contents($exercisesFile, json_encode($exercises, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Função para adicionar exercício
function addExerciseData($data) {
    $exercises = getExercisesData();
    $newId = empty($exercises) ? 1 : max(array_column($exercises, 'id')) + 1;
    
    $exercise = [
        'id' => $newId,
        'title' => $data['title'],
        'description' => $data['description'],
        'category' => $data['category'],
        'difficulty' => $data['difficulty'],
        'difficulty_level' => strtolower($data['difficulty']),
        'points' => $data['points'] ?? 10,
        'estimated_time' => $data['estimated_time'] ?? '15 min',
        'instructions' => $data['instructions'] ?? '',
        'initial_code' => $data['initial_code'] ?? '',
        'hints' => $data['hints'] ?? [],
        'exercise_type' => $data['exercise_type'] ?? $data['category'],
        'created_at' => date('Y-m-d')
    ];
    
    $exercises[] = $exercise;
    saveExercisesData($exercises);
    return $newId;
}

// Função para atualizar exercício
function updateExerciseData($id, $data) {
    $exercises = getExercisesData();
    
    foreach ($exercises as &$exercise) {
        if ($exercise['id'] == $id) {
            $exercise['title'] = $data['title'];
            $exercise['description'] = $data['description'];
            $exercise['category'] = $data['category'];
            $exercise['difficulty'] = $data['difficulty'];
            $exercise['difficulty_level'] = strtolower($data['difficulty']);
            $exercise['points'] = $data['points'] ?? $exercise['points'];
            $exercise['estimated_time'] = $data['estimated_time'] ?? $exercise['estimated_time'];
            $exercise['instructions'] = $data['instructions'] ?? $exercise['instructions'];
            $exercise['initial_code'] = $data['initial_code'] ?? $exercise['initial_code'];
            $exercise['hints'] = $data['hints'] ?? $exercise['hints'];
            $exercise['exercise_type'] = $data['exercise_type'] ?? $exercise['exercise_type'];
            break;
        }
    }
    
    saveExercisesData($exercises);
}

// Função para deletar exercício
function deleteExerciseData($id) {
    $exercises = getExercisesData();
    $exercises = array_filter($exercises, fn($e) => $e['id'] != $id);
    saveExercisesData(array_values($exercises));
}
?>
