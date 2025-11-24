<?php
require_once 'config.php';

echo "<h1>🔄 Populando Dados de Exemplo</h1>";

$conn = getDBConnection();
if (!$conn) {
    die("❌ Erro de conexão");
}

// Criar categorias
echo "<h2>📂 Criando Categorias</h2>";
$categories = [
    ['HTML', 'Linguagem de marcação para estruturar páginas web'],
    ['CSS', 'Folhas de estilo para design e layout'],
    ['JavaScript', 'Linguagem de programação para interatividade'],
    ['PHP', 'Linguagem de programação server-side']
];

foreach ($categories as $cat) {
    $stmt = $conn->prepare("INSERT IGNORE INTO categories (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $cat[0], $cat[1]);
    if ($stmt->execute()) {
        echo "✅ Categoria {$cat[0]} criada<br>";
    }
}

// Buscar IDs das categorias
$cat_ids = [];
$result = $conn->query("SELECT id, name FROM categories");
while ($row = $result->fetch_assoc()) {
    $cat_ids[$row['name']] = $row['id'];
}

// Criar exercícios
echo "<h2>💪 Criando Exercícios</h2>";
$exercises = [
    ['Primeiro HTML', 'Crie sua primeira página HTML com título e parágrafo', 'HTML', 'beginner'],
    ['Estilizando com CSS', 'Adicione cores e fontes ao seu HTML', 'CSS', 'beginner'],
    ['Botão Interativo', 'Crie um botão que muda de cor ao clicar', 'JavaScript', 'intermediate'],
    ['Formulário de Contato', 'Desenvolva um formulário completo', 'HTML', 'intermediate'],
    ['Layout Responsivo', 'Crie um layout que se adapta a diferentes telas', 'CSS', 'advanced'],
    ['Validação de Formulário', 'Implemente validação client-side', 'JavaScript', 'advanced'],
    ['Sistema de Login', 'Desenvolva autenticação com PHP', 'PHP', 'advanced'],
    ['Galeria de Imagens', 'Crie uma galeria responsiva', 'CSS', 'intermediate']
];

foreach ($exercises as $ex) {
    $cat_id = $cat_ids[$ex[2]] ?? 1;
    $stmt = $conn->prepare("INSERT IGNORE INTO exercises (title, description, category_id, difficulty, content) VALUES (?, ?, ?, ?, ?)");
    $content = "Conteúdo do exercício: {$ex[0]}";
    $stmt->bind_param("ssiss", $ex[0], $ex[1], $cat_id, $ex[3], $content);
    if ($stmt->execute()) {
        echo "✅ Exercício '{$ex[0]}' criado<br>";
    }
}

// Criar tutoriais
echo "<h2>📚 Criando Tutoriais</h2>";
$tutorials = [
    ['Introdução ao HTML', 'Aprenda os fundamentos do HTML', 'HTML', 'beginner'],
    ['CSS Básico', 'Primeiros passos com CSS', 'CSS', 'beginner'],
    ['JavaScript para Iniciantes', 'Conceitos básicos de JavaScript', 'JavaScript', 'beginner'],
    ['HTML Semântico', 'Usando tags HTML de forma semântica', 'HTML', 'intermediate'],
    ['Flexbox e Grid', 'Layouts modernos com CSS', 'CSS', 'intermediate'],
    ['DOM e Eventos', 'Manipulando o DOM com JavaScript', 'JavaScript', 'intermediate'],
    ['PHP Orientado a Objetos', 'Programação OOP em PHP', 'PHP', 'advanced'],
    ['APIs REST com PHP', 'Criando APIs RESTful', 'PHP', 'advanced']
];

foreach ($tutorials as $tut) {
    $cat_id = $cat_ids[$tut[2]] ?? 1;
    $stmt = $conn->prepare("INSERT IGNORE INTO tutorials (title, description, category_id, difficulty, content) VALUES (?, ?, ?, ?, ?)");
    $content = "Conteúdo completo do tutorial: {$tut[0]}";
    $stmt->bind_param("ssiss", $tut[0], $tut[1], $cat_id, $tut[3], $content);
    if ($stmt->execute()) {
        echo "✅ Tutorial '{$tut[0]}' criado<br>";
    }
}

// Criar badges
echo "<h2>🏆 Criando Badges</h2>";
$badges = [
    ['Primeiro Passo', 'Complete seu primeiro exercício'],
    ['Estudioso', 'Complete 5 exercícios'],
    ['Dedicado', 'Complete 10 exercícios'],
    ['Mestre', 'Complete 20 exercícios'],
    ['Leitor', 'Leia seu primeiro tutorial'],
    ['Explorador', 'Leia 5 tutoriais'],
    ['Conhecedor', 'Leia 10 tutoriais'],
    ['Especialista', 'Leia 20 tutoriais']
];

foreach ($badges as $badge) {
    $stmt = $conn->prepare("INSERT IGNORE INTO badges (name, description, condition_type, condition_value) VALUES (?, ?, 'exercise_count', 1)");
    $stmt->bind_param("ss", $badge[0], $badge[1]);
    if ($stmt->execute()) {
        echo "✅ Badge '{$badge[0]}' criado<br>";
    }
}

echo "<h2>✅ Dados Populados com Sucesso!</h2>";
echo "<p><a href='exercises_index.php'>Ver Exercícios</a> | <a href='tutorials_index.php'>Ver Tutoriais</a> | <a href='conquistas.php'>Ver Conquistas</a></p>";

$conn->close();
?>