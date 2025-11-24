<?php
// Arquivo de dados dos exercícios
$exercisesFile = __DIR__ . '/exercises.json';

// Função para obter exercícios
function getExercisesData() {
    global $exercisesFile;
    
    if (!file_exists($exercisesFile)) {
        // Criar arquivo com dados iniciais - Exercícios completos por categoria
        $defaultData = [
            // ==================== EXERCÍCIOS DE HTML ====================
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
            ],
            
            // ==================== MAIS EXERCÍCIOS HTML ====================
            [
                'id' => 10,
                'title' => 'Tabela de Dados',
                'description' => 'Crie uma tabela HTML com dados de produtos',
                'category' => 'HTML',
                'difficulty' => 'Iniciante',
                'difficulty_level' => 'beginner',
                'points' => 15,
                'estimated_time' => '20 min',
                'instructions' => 'Crie uma tabela HTML com:\n- Cabeçalhos usando <thead> e <th>\n- Pelo menos 3 linhas de dados com <tbody> e <td>\n- Colunas: Produto, Preço, Quantidade\n- Use border="1" para visualizar a tabela',
                'initial_code' => '<!DOCTYPE html>\n<html lang="pt-BR">\n<head>\n    <meta charset="UTF-8">\n    <title>Tabela de Produtos</title>\n</head>\n<body>\n    <h2>Lista de Produtos</h2>\n    <!-- Crie a tabela aqui -->\n</body>\n</html>',
                'hints' => ['Use <table> para criar tabela', '<thead> para cabeçalho e <tbody> para corpo', 'Cada linha é um <tr> e cada célula é <td>'],
                'exercise_type' => 'HTML',
                'created_at' => '2024-01-24'
            ],
            [
                'id' => 11,
                'title' => 'Links e Navegação',
                'description' => 'Crie uma página com links internos e externos',
                'category' => 'HTML',
                'difficulty' => 'Iniciante',
                'difficulty_level' => 'beginner',
                'points' => 12,
                'estimated_time' => '15 min',
                'instructions' => 'Crie uma página com:\n- Um link externo para um site (target="_blank")\n- Um link para email usando mailto:\n- Um link âncora para uma seção da própria página (#secao)',
                'initial_code' => '<!DOCTYPE html>\n<html lang="pt-BR">\n<head>\n    <meta charset="UTF-8">\n    <title>Links</title>\n</head>\n<body>\n    <h1>Meus Links</h1>\n    <!-- Adicione os links aqui -->\n</body>\n</html>',
                'hints' => ['Use <a href="..."> para links', 'target="_blank" abre em nova aba', 'mailto:email@exemplo.com para email'],
                'exercise_type' => 'HTML',
                'created_at' => '2024-01-25'
            ],
            [
                'id' => 12,
                'title' => 'Formulário de Cadastro Completo',
                'description' => 'Crie um formulário de cadastro com diversos tipos de campos',
                'category' => 'HTML',
                'difficulty' => 'Intermediário',
                'difficulty_level' => 'intermediate',
                'points' => 20,
                'estimated_time' => '30 min',
                'instructions' => 'Crie um formulário com:\n- Campos: nome, email, senha, data de nascimento\n- Radio buttons para gênero\n- Checkbox para aceitar termos\n- Select para escolher país\n- Botões de enviar e limpar',
                'initial_code' => '<!DOCTYPE html>\n<html lang="pt-BR">\n<head>\n    <meta charset="UTF-8">\n    <title>Cadastro</title>\n</head>\n<body>\n    <h1>Formulário de Cadastro</h1>\n    <form action="/processar" method="post">\n        <!-- Adicione os campos aqui -->\n    </form>\n</body>\n</html>',
                'hints' => ['Use type="email" para validação automática', 'Radio buttons devem ter o mesmo name', 'required torna campo obrigatório'],
                'exercise_type' => 'HTML',
                'created_at' => '2024-01-26'
            ],
            [
                'id' => 13,
                'title' => 'Página com Imagens e Multimídia',
                'description' => 'Adicione imagens, áudio e vídeo em uma página HTML',
                'category' => 'HTML',
                'difficulty' => 'Intermediário',
                'difficulty_level' => 'intermediate',
                'points' => 18,
                'estimated_time' => '25 min',
                'instructions' => 'Crie uma página com:\n- Uma imagem com <img> e alt text\n- Um vídeo usando <video> com controles\n- Um áudio usando <audio>\n- Use figure e figcaption para legenda da imagem',
                'initial_code' => '<!DOCTYPE html>\n<html lang="pt-BR">\n<head>\n    <meta charset="UTF-8">\n    <title>Multimídia</title>\n</head>\n<body>\n    <h1>Galeria Multimídia</h1>\n    <!-- Adicione imagens, vídeo e áudio aqui -->\n</body>\n</html>',
                'hints' => ['Use src para especificar o arquivo', 'controls adiciona botões de controle', 'alt é importante para acessibilidade'],
                'exercise_type' => 'HTML',
                'created_at' => '2024-01-27'
            ],
            [
                'id' => 14,
                'title' => 'Estrutura Semântica HTML5',
                'description' => 'Use tags semânticas para estruturar uma página',
                'category' => 'HTML',
                'difficulty' => 'Intermediário',
                'difficulty_level' => 'intermediate',
                'points' => 20,
                'estimated_time' => '30 min',
                'instructions' => 'Crie uma página usando tags semânticas:\n- <header> com logo e navegação\n- <nav> para menu\n- <main> para conteúdo principal\n- <article> para artigos\n- <aside> para sidebar\n- <footer> para rodapé',
                'initial_code' => '<!DOCTYPE html>\n<html lang="pt-BR">\n<head>\n    <meta charset="UTF-8">\n    <title>Página Semântica</title>\n</head>\n<body>\n    <!-- Use tags semânticas aqui -->\n</body>\n</html>',
                'hints' => ['Tags semânticas melhoram SEO', '<section> agrupa conteúdo relacionado', '<article> é para conteúdo independente'],
                'exercise_type' => 'HTML',
                'created_at' => '2024-01-28'
            ],
            
            // ==================== MAIS EXERCÍCIOS CSS ====================
            [
                'id' => 15,
                'title' => 'Box Model - Margin e Padding',
                'description' => 'Pratique o uso de margin e padding',
                'category' => 'CSS',
                'difficulty' => 'Iniciante',
                'difficulty_level' => 'beginner',
                'points' => 12,
                'estimated_time' => '15 min',
                'instructions' => 'Crie estilos para:\n- Adicionar padding de 20px em uma div\n- Adicionar margin de 10px entre elementos\n- Definir border de 2px sólida\n- Usar box-sizing: border-box',
                'initial_code' => '<!DOCTYPE html>\n<html>\n<head>\n    <style>\n        .box {\n            /* Adicione estilos aqui */\n        }\n    </style>\n</head>\n<body>\n    <div class="box">Caixa 1</div>\n    <div class="box">Caixa 2</div>\n</body>\n</html>',
                'hints' => ['padding é espaço interno', 'margin é espaço externo', 'border-box inclui border no tamanho total'],
                'exercise_type' => 'CSS',
                'created_at' => '2024-01-29'
            ],
            [
                'id' => 16,
                'title' => 'Cores e Gradientes',
                'description' => 'Trabalhe com cores e gradientes CSS',
                'category' => 'CSS',
                'difficulty' => 'Iniciante',
                'difficulty_level' => 'beginner',
                'points' => 15,
                'estimated_time' => '20 min',
                'instructions' => 'Crie estilos usando:\n- Cores em hexadecimal, RGB e HSL\n- Um gradiente linear de azul para roxo\n- Um gradiente radial\n- background-image com gradiente',
                'initial_code' => '<!DOCTYPE html>\n<html>\n<head>\n    <style>\n        .gradient-box {\n            /* Adicione gradientes aqui */\n        }\n    </style>\n</head>\n<body>\n    <div class="gradient-box">Gradiente</div>\n</body>\n</html>',
                'hints' => ['linear-gradient(direção, cor1, cor2)', 'radial-gradient(cor1, cor2)', 'Use 135deg para diagonal'],
                'exercise_type' => 'CSS',
                'created_at' => '2024-01-30'
            ],
            [
                'id' => 17,
                'title' => 'CSS Grid Layout',
                'description' => 'Crie um layout usando CSS Grid',
                'category' => 'CSS',
                'difficulty' => 'Intermediário',
                'difficulty_level' => 'intermediate',
                'points' => 25,
                'estimated_time' => '35 min',
                'instructions' => 'Crie um layout grid com:\n- display: grid no container\n- 3 colunas de tamanhos iguais\n- gap de 20px entre itens\n- grid-template-areas para layout nomeado',
                'initial_code' => '<!DOCTYPE html>\n<html>\n<head>\n    <style>\n        .grid-container {\n            /* Adicione grid aqui */\n        }\n    </style>\n</head>\n<body>\n    <div class="grid-container">\n        <div class="item">1</div>\n        <div class="item">2</div>\n        <div class="item">3</div>\n    </div>\n</body>\n</html>',
                'hints' => ['grid-template-columns: repeat(3, 1fr)', 'gap: 20px para espaçamento', 'fr é uma unidade flexível'],
                'exercise_type' => 'CSS',
                'created_at' => '2024-01-31'
            ],
            [
                'id' => 18,
                'title' => 'Animações CSS',
                'description' => 'Crie animações usando @keyframes',
                'category' => 'CSS',
                'difficulty' => 'Intermediário',
                'difficulty_level' => 'intermediate',
                'points' => 22,
                'estimated_time' => '30 min',
                'instructions' => 'Crie animações com:\n- @keyframes para definir animação\n- animation para aplicar (nome, duração, iteração)\n- Uma animação de fade in\n- Uma animação de rotação',
                'initial_code' => '<!DOCTYPE html>\n<html>\n<head>\n    <style>\n        @keyframes fadeIn {\n            /* Defina animação aqui */\n        }\n        .animated {\n            /* Aplique animação */\n        }\n    </style>\n</head>\n<body>\n    <div class="animated">Texto Animado</div>\n</body>\n</html>',
                'hints' => ['0% e 100% definem início e fim', 'animation: nome 2s infinite', 'transform: rotate(360deg) para rotação'],
                'exercise_type' => 'CSS',
                'created_at' => '2024-02-01'
            ],
            [
                'id' => 19,
                'title' => 'Responsividade com Media Queries',
                'description' => 'Torne uma página responsiva',
                'category' => 'CSS',
                'difficulty' => 'Intermediário',
                'difficulty_level' => 'intermediate',
                'points' => 25,
                'estimated_time' => '35 min',
                'instructions' => 'Use media queries para:\n- Layout de 3 colunas em desktop\n- Layout de 2 colunas em tablet (max-width: 768px)\n- Layout de 1 coluna em mobile (max-width: 480px)\n- Ajustar tamanhos de fonte',
                'initial_code' => '<!DOCTYPE html>\n<html>\n<head>\n    <style>\n        .container {\n            display: grid;\n            grid-template-columns: repeat(3, 1fr);\n        }\n        /* Adicione media queries aqui */\n    </style>\n</head>\n<body>\n    <div class="container">\n        <div>Item 1</div>\n        <div>Item 2</div>\n        <div>Item 3</div>\n    </div>\n</body>\n</html>',
                'hints' => ['@media (max-width: 768px) { }', 'Mobile first: comece pelo menor', 'Teste redimensionando o navegador'],
                'exercise_type' => 'CSS',
                'created_at' => '2024-02-02'
            ],
            [
                'id' => 20,
                'title' => 'Variáveis CSS (Custom Properties)',
                'description' => 'Use variáveis CSS para temas',
                'category' => 'CSS',
                'difficulty' => 'Avançado',
                'difficulty_level' => 'advanced',
                'points' => 28,
                'estimated_time' => '40 min',
                'instructions' => 'Crie um sistema de temas usando:\n- Variáveis CSS com --nome-variavel\n- Cores primária, secundária e de fundo\n- Use var(--variavel) para aplicar\n- Crie tema claro e escuro',
                'initial_code' => '<!DOCTYPE html>\n<html>\n<head>\n    <style>\n        :root {\n            /* Defina variáveis aqui */\n        }\n        body {\n            background: var(--bg-color);\n        }\n    </style>\n</head>\n<body>\n    <h1>Tema com Variáveis CSS</h1>\n</body>\n</html>',
                'hints' => [':root { --primary: #3498db; }', 'var(--primary) para usar', 'Pode mudar todas as cores de uma vez'],
                'exercise_type' => 'CSS',
                'created_at' => '2024-02-03'
            ],
            
            // ==================== MAIS EXERCÍCIOS JAVASCRIPT ====================
            [
                'id' => 21,
                'title' => 'Funções e Arrow Functions',
                'description' => 'Pratique diferentes formas de criar funções',
                'category' => 'JavaScript',
                'difficulty' => 'Iniciante',
                'difficulty_level' => 'beginner',
                'points' => 15,
                'estimated_time' => '20 min',
                'instructions' => 'Crie funções de diferentes formas:\n- Função tradicional para multiplicar dois números\n- Arrow function para calcular quadrado\n- Função que retorna outra função',
                'initial_code' => '// Função tradicional\nfunction multiplicar(a, b) {\n    // Complete aqui\n}\n\n// Arrow function\nconst quadrado = (n) => {\n    // Complete aqui\n};\n\nconsole.log(multiplicar(5, 3));\nconsole.log(quadrado(4));',
                'hints' => ['Arrow function: const nome = (param) => { }', 'return é obrigatório para retornar valor', 'Funções podem retornar funções'],
                'exercise_type' => 'JavaScript',
                'created_at' => '2024-02-04'
            ],
            [
                'id' => 22,
                'title' => 'Manipulação de Objetos',
                'description' => 'Trabalhe com objetos JavaScript',
                'category' => 'JavaScript',
                'difficulty' => 'Iniciante',
                'difficulty_level' => 'beginner',
                'points' => 18,
                'estimated_time' => '25 min',
                'instructions' => 'Crie e manipule objetos:\n- Objeto pessoa com nome, idade e cidade\n- Adicione propriedade usando notação de ponto\n- Acesse propriedades com colchetes\n- Use Object.keys() e Object.values()',
                'initial_code' => 'const pessoa = {\n    nome: "João",\n    idade: 25\n};\n\n// Adicione propriedade cidade\n\n// Exiba todas as chaves e valores\n',
                'hints' => ['pessoa.cidade = "São Paulo"', 'pessoa["nome"] também funciona', 'Object.keys() retorna array de chaves'],
                'exercise_type' => 'JavaScript',
                'created_at' => '2024-02-05'
            ],
            [
                'id' => 23,
                'title' => 'Métodos de Array - Map, Filter, Reduce',
                'description' => 'Use métodos funcionais de arrays',
                'category' => 'JavaScript',
                'difficulty' => 'Intermediário',
                'difficulty_level' => 'intermediate',
                'points' => 25,
                'estimated_time' => '35 min',
                'instructions' => 'Use métodos de array:\n- map() para dobrar cada número\n- filter() para filtrar pares\n- reduce() para somar todos\n- Encadeie métodos (chaining)',
                'initial_code' => 'const numeros = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];\n\n// Use map para dobrar\nconst dobrados = numeros.map(/* complete */);\n\n// Use filter para pares\nconst pares = numeros.filter(/* complete */);\n\n// Use reduce para somar\nconst soma = numeros.reduce(/* complete */);\n\nconsole.log(dobrados, pares, soma);',
                'hints' => ['map: array.map(n => n * 2)', 'filter: array.filter(n => n % 2 === 0)', 'reduce: array.reduce((acc, n) => acc + n, 0)'],
                'exercise_type' => 'JavaScript',
                'created_at' => '2024-02-06'
            ],
            [
                'id' => 24,
                'title' => 'Promises e Async/Await',
                'description' => 'Trabalhe com código assíncrono',
                'category' => 'JavaScript',
                'difficulty' => 'Intermediário',
                'difficulty_level' => 'intermediate',
                'points' => 28,
                'estimated_time' => '40 min',
                'instructions' => 'Crie código assíncrono:\n- Promise que resolve após 2 segundos\n- Use .then() e .catch()\n- Função async/await\n- Trate erros com try/catch',
                'initial_code' => '// Promise\nconst minhaPromise = new Promise((resolve, reject) => {\n    setTimeout(() => {\n        // Complete aqui\n    }, 2000);\n});\n\n// Async/await\nasync function buscarDados() {\n    try {\n        // Complete aqui\n    } catch (error) {\n        console.error(error);\n    }\n}',
                'hints' => ['resolve(valor) para sucesso', 'reject(erro) para falha', 'await aguarda Promise resolver'],
                'exercise_type' => 'JavaScript',
                'created_at' => '2024-02-07'
            ],
            [
                'id' => 25,
                'title' => 'DOM Manipulation',
                'description' => 'Manipule elementos do DOM',
                'category' => 'JavaScript',
                'difficulty' => 'Intermediário',
                'difficulty_level' => 'intermediate',
                'points' => 22,
                'estimated_time' => '30 min',
                'instructions' => 'Manipule o DOM:\n- Selecione elemento com querySelector\n- Altere conteúdo com innerHTML/textContent\n- Adicione evento de clique\n- Crie novo elemento e adicione à página',
                'initial_code' => '// Selecione o botão\nconst botao = document.querySelector("#meuBotao");\n\n// Adicione evento\nbotao.addEventListener("click", () => {\n    // Complete aqui\n});\n\n// Crie novo elemento\nconst novoElemento = document.createElement("div");\n// Complete aqui',
                'hints' => ['querySelector("#id") ou querySelector(".class")', 'element.textContent = "texto"', 'appendChild para adicionar'],
                'exercise_type' => 'JavaScript',
                'created_at' => '2024-02-08'
            ],
            [
                'id' => 26,
                'title' => 'Classes e POO',
                'description' => 'Use programação orientada a objetos',
                'category' => 'JavaScript',
                'difficulty' => 'Avançado',
                'difficulty_level' => 'advanced',
                'points' => 30,
                'estimated_time' => '45 min',
                'instructions' => 'Crie classes JavaScript:\n- Classe Pessoa com construtor\n- Métodos para a classe\n- Classe Estudante que herda de Pessoa\n- Use super() no construtor filho',
                'initial_code' => 'class Pessoa {\n    constructor(nome, idade) {\n        // Complete aqui\n    }\n    \n    apresentar() {\n        // Complete aqui\n    }\n}\n\nclass Estudante extends Pessoa {\n    constructor(nome, idade, curso) {\n        // Complete aqui\n    }\n}',
                'hints' => ['this.propriedade dentro da classe', 'super(args) chama construtor pai', 'extends para herança'],
                'exercise_type' => 'JavaScript',
                'created_at' => '2024-02-09'
            ],
            
            // ==================== MAIS EXERCÍCIOS PHP ====================
            [
                'id' => 27,
                'title' => 'Variáveis e Tipos de Dados',
                'description' => 'Trabalhe com diferentes tipos de dados em PHP',
                'category' => 'PHP',
                'difficulty' => 'Iniciante',
                'difficulty_level' => 'beginner',
                'points' => 12,
                'estimated_time' => '15 min',
                'instructions' => 'Crie variáveis PHP:\n- String, integer, float, boolean\n- Use var_dump() para ver o tipo\n- Concatene strings com .\n- Use interpolação com aspas duplas',
                'initial_code' => '<?php\n$nome = "João";\n$idade = 25;\n// Crie mais variáveis\n\n// Use var_dump\n\n// Concatene e exiba\necho "Nome: " . $nome;\n?>',
                'hints' => ['$ antes do nome da variável', '. para concatenar', '"Texto $variavel" para interpolação'],
                'exercise_type' => 'PHP',
                'created_at' => '2024-02-10'
            ],
            [
                'id' => 28,
                'title' => 'Arrays em PHP',
                'description' => 'Manipule arrays indexados e associativos',
                'category' => 'PHP',
                'difficulty' => 'Iniciante',
                'difficulty_level' => 'beginner',
                'points' => 15,
                'estimated_time' => '20 min',
                'instructions' => 'Trabalhe com arrays:\n- Array indexado com números\n- Array associativo com chaves\n- Use foreach para percorrer\n- Funções count(), array_push(), array_pop()',
                'initial_code' => '<?php\n$frutas = ["maçã", "banana", "laranja"];\n$pessoa = [\n    "nome" => "João",\n    "idade" => 25\n];\n\n// Percorra com foreach\n\n// Use funções de array\n?>',
                'hints' => ['foreach ($array as $item)', 'foreach ($array as $key => $value)', 'count($array) retorna tamanho'],
                'exercise_type' => 'PHP',
                'created_at' => '2024-02-11'
            ],
            [
                'id' => 29,
                'title' => 'Funções em PHP',
                'description' => 'Crie e use funções personalizadas',
                'category' => 'PHP',
                'difficulty' => 'Intermediário',
                'difficulty_level' => 'intermediate',
                'points' => 18,
                'estimated_time' => '25 min',
                'instructions' => 'Crie funções PHP:\n- Função que recebe parâmetros\n- Função com valor padrão\n- Função que retorna valor\n- Função com tipo de retorno declarado',
                'initial_code' => '<?php\nfunction somar($a, $b) {\n    // Complete aqui\n}\n\nfunction saudacao($nome = "visitante") {\n    // Complete aqui\n}\n\n// Teste as funções\necho somar(5, 3);\necho saudacao("João");\n?>',
                'hints' => ['return para retornar valor', 'function nome($param = "padrão")', ': tipo após parâmetros para tipar retorno'],
                'exercise_type' => 'PHP',
                'created_at' => '2024-02-12'
            ],
            [
                'id' => 30,
                'title' => 'Formulários e Superglobais',
                'description' => 'Processe dados de formulários',
                'category' => 'PHP',
                'difficulty' => 'Intermediário',
                'difficulty_level' => 'intermediate',
                'points' => 22,
                'estimated_time' => '30 min',
                'instructions' => 'Trabalhe com formulários:\n- Acesse dados com $_POST\n- Valide campos vazios\n- Use filter_var() para validar email\n- Sanitize dados com htmlspecialchars()',
                'initial_code' => '<?php\nif ($_SERVER["REQUEST_METHOD"] == "POST") {\n    $nome = $_POST["nome"] ?? "";\n    \n    // Valide e sanitize\n    if (empty($nome)) {\n        echo "Nome é obrigatório";\n    }\n    \n    // Valide email\n}\n?>\n<form method="post">\n    <input type="text" name="nome">\n    <button type="submit">Enviar</button>\n</form>',
                'hints' => ['$_POST["nome_campo"]', 'filter_var($email, FILTER_VALIDATE_EMAIL)', 'htmlspecialchars() previne XSS'],
                'exercise_type' => 'PHP',
                'created_at' => '2024-02-13'
            ],
            [
                'id' => 31,
                'title' => 'Classes e Objetos em PHP',
                'description' => 'Use POO em PHP',
                'category' => 'PHP',
                'difficulty' => 'Avançado',
                'difficulty_level' => 'advanced',
                'points' => 28,
                'estimated_time' => '40 min',
                'instructions' => 'Crie classes PHP:\n- Classe com propriedades e métodos\n- Construtor __construct()\n- Visibilidade: public, private, protected\n- Getters e setters',
                'initial_code' => '<?php\nclass Produto {\n    private $nome;\n    private $preco;\n    \n    public function __construct($nome, $preco) {\n        // Complete aqui\n    }\n    \n    public function getNome() {\n        // Complete aqui\n    }\n}\n\n$produto = new Produto("Livro", 29.90);\necho $produto->getNome();\n?>',
                'hints' => ['$this->propriedade dentro da classe', 'public/private/protected para visibilidade', '__construct é o construtor'],
                'exercise_type' => 'PHP',
                'created_at' => '2024-02-14'
            ],
            [
                'id' => 32,
                'title' => 'Conexão com Banco de Dados',
                'description' => 'Conecte e consulte MySQL com PHP',
                'category' => 'PHP',
                'difficulty' => 'Avançado',
                'difficulty_level' => 'advanced',
                'points' => 35,
                'estimated_time' => '50 min',
                'instructions' => 'Trabalhe com banco de dados:\n- Conecte com MySQLi ou PDO\n- Execute SELECT query\n- Use prepared statements\n- Trate erros com try/catch',
                'initial_code' => '<?php\n// Conexão\n$pdo = new PDO("mysql:host=localhost;dbname=teste", "user", "pass");\n\n// Prepared statement\n$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");\n$stmt->execute([1]);\n\n// Busque resultados\n$resultado = $stmt->fetch(PDO::FETCH_ASSOC);\nprint_r($resultado);\n?>',
                'hints' => ['PDO é mais seguro que MySQLi', 'Sempre use prepared statements', 'try/catch para erros de conexão'],
                'exercise_type' => 'PHP',
                'created_at' => '2024-02-15'
            ],
            
            // ==================== EXERCÍCIOS DESAFIO ====================
            [
                'id' => 33,
                'title' => 'Calculadora Interativa',
                'description' => 'Crie uma calculadora completa com todas as operações',
                'category' => 'JavaScript',
                'difficulty' => 'Intermediário',
                'difficulty_level' => 'intermediate',
                'points' => 30,
                'estimated_time' => '45 min',
                'instructions' => 'Crie uma calculadora que:\n- Realize as 4 operações básicas (+, -, *, /)\n- Trate divisão por zero\n- Tenha função de limpar\n- Exiba resultado formatado',
                'initial_code' => 'class Calculadora {\n  constructor() {\n    // Implemente aqui\n  }\n  \n  somar(a, b) {\n    // Complete\n  }\n  \n  subtrair(a, b) {\n    // Complete\n  }\n}',
                'hints' => ['Valide entrada de números', 'Use toFixed() para formatar decimais', 'Retorne mensagem de erro para divisão por zero'],
                'exercise_type' => 'JavaScript',
                'created_at' => '2024-02-16'
            ],
            [
                'id' => 34,
                'title' => 'To-Do List Dinâmica',
                'description' => 'Construa uma lista de tarefas interativa',
                'category' => 'JavaScript',
                'difficulty' => 'Avançado',
                'difficulty_level' => 'advanced',
                'points' => 40,
                'estimated_time' => '60 min',
                'instructions' => 'Crie um To-Do List que:\n- Adicione tarefas\n- Marque como concluído\n- Remova tarefas\n- Salve no localStorage\n- Filtre por status',
                'initial_code' => 'const todoList = {\n  tasks: [],\n  \n  addTask(text) {\n    // Implemente\n  },\n  \n  removeTask(id) {\n    // Implemente\n  },\n  \n  toggleComplete(id) {\n    // Implemente\n  }\n};',
                'hints' => ['Use array para armazenar tarefas', 'localStorage.setItem() para persistir dados', 'filter() para filtrar por status'],
                'exercise_type' => 'JavaScript',
                'created_at' => '2024-02-17'
            ],
            [
                'id' => 35,
                'title' => 'Sistema de Login',
                'description' => 'Implemente autenticação de usuários',
                'category' => 'PHP',
                'difficulty' => 'Avançado',
                'difficulty_level' => 'advanced',
                'points' => 50,
                'estimated_time' => '90 min',
                'instructions' => 'Crie sistema de login com:\n- Registro de usuário\n- Login com senha hash\n- Validação de sessão\n- Logout seguro\n- Proteção contra SQL injection',
                'initial_code' => '<?php\nsession_start();\n\nfunction registrar($email, $senha) {\n  // Hash da senha\n  $hash = password_hash($senha, PASSWORD_DEFAULT);\n  \n  // Salvar no banco\n  // Implemente aqui\n}\n\nfunction login($email, $senha) {\n  // Verificar credenciais\n  // Implemente aqui\n}\n?>',
                'hints' => ['Use password_hash() e password_verify()', 'Prepared statements sempre', 'Valide formato de email', 'Session para manter login'],
                'exercise_type' => 'PHP',
                'created_at' => '2024-02-18'
            ],
            [
                'id' => 36,
                'title' => 'Landing Page Responsiva',
                'description' => 'Construa uma landing page moderna e responsiva',
                'category' => 'HTML',
                'difficulty' => 'Avançado',
                'difficulty_level' => 'advanced',
                'points' => 45,
                'estimated_time' => '75 min',
                'instructions' => 'Crie uma landing page com:\n- Header com navegação\n- Seção hero com CTA\n- Cards de features\n- Formulário de contato\n- Footer com redes sociais\n- Totalmente responsiva',
                'initial_code' => '<!DOCTYPE html>\n<html lang="pt-BR">\n<head>\n  <meta charset="UTF-8">\n  <meta name="viewport" content="width=device-width, initial-scale=1.0">\n  <title>Minha Landing Page</title>\n</head>\n<body>\n  <!-- Implemente aqui -->\n</body>\n</html>',
                'hints' => ['Use flexbox ou grid para layout', 'Media queries para responsividade', 'Semântica HTML5', 'Mobile-first approach'],
                'exercise_type' => 'HTML',
                'created_at' => '2024-02-19'
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
