# 📚 DOCUMENTAÇÃO COMPLETA - show.php

## 🎯 PROPÓSITO DO ARQUIVO
Este arquivo exibe a página de detalhes de tutoriais e exercícios no sistema WebLearn.

---

## 🏗️ ESTRUTURA GERAL

### 1️⃣ CABEÇALHO PHP (Linhas 1-82)
**Função:** Processar dados antes de exibir HTML

```php
<?php
// Inclui arquivos necessários
require_once 'config.php';           // Funções auxiliares (sanitize, redirect, isLoggedIn)
require_once 'database_connector.php'; // Conexão com banco MySQL
```

### 2️⃣ CAPTURA DE PARÂMETROS DA URL
```php
$type = sanitize($_GET['type'] ?? '');  // 'tutorial' ou 'exercise'
$id = (int)($_GET['id'] ?? 0);          // ID numérico do item
$preview = isset($_GET['preview']);     // true/false para modo prévia
```

**Exemplo de URL:** `show.php?type=tutorial&id=2&preview=1`

### 3️⃣ CARREGAMENTO DE DADOS

#### Para Tutoriais:
```php
if ($type === 'tutorial') {
    require_once 'data/tutorials.php';  // Carrega funções de tutorial
    $items = getTutorials();             // Busca todos do JSON
    $item = array_filter($items, fn($t) => $t['id'] === $id); // Filtra por ID
}
```

#### Para Exercícios:
```php
elseif ($type === 'exercise') {
    $exercises = $dbConnector->getExercises('', '', '', 1, 100); // MySQL
    $item = array_filter($exercises, fn($e) => $e['id'] === $id);
}
```

### 4️⃣ ENRIQUECIMENTO DE DADOS
Adiciona campos que podem estar faltando:

```php
$item['author'] = $item['author'] ?? 'Equipe WebLearn';
$item['rating'] = 4.8;
$item['rating_count'] = rand(50, 200);
```

**Operador ??** = Se valor à esquerda for `null`, usa valor à direita

---

## 🎨 SEÇÃO HTML

### 🔹 CABEÇALHO DO CONTEÚDO (Linha 100+)
```html
<div class="content-header-card mb-4">
    <!-- Badge de tipo (Tutorial/Exercício) -->
    <span class="content-type-badge">Tutorial</span>
    
    <!-- Badge de dificuldade (Iniciante/Intermediário/Avançado) -->
    <span class="difficulty-badge">Iniciante</span>
    
    <!-- Título e descrição -->
    <h1><?php echo sanitize($item['title']); ?></h1>
    <p><?php echo sanitize($item['description']); ?></p>
</div>
```

### 🔹 SISTEMA DE ABAS
```html
<ul class="nav nav-tabs">
    <li class="nav-item">
        <button data-bs-toggle="tab" data-bs-target="#content">
            Conteúdo
        </button>
    </li>
    <li class="nav-item">
        <button data-bs-toggle="tab" data-bs-target="#resources">
            Recursos
        </button>
    </li>
</ul>
```

---

## 💻 GERAÇÃO DINÂMICA DE CÓDIGO

### 📝 Sistema de Exemplos por Categoria

```php
if ($item['category'] === 'HTML') {
    if (stripos($item['title'], 'Estrutura') !== false) {
        $codeExample = '<!DOCTYPE html>...'; // Exemplo específico
    }
}
elseif ($item['category'] === 'CSS') {
    if (stripos($item['title'], 'Grid') !== false) {
        $codeExample = '.container { display: grid; }';
    }
}
```

**stripos()** = Busca string ignorando maiúsculas/minúsculas
**!== false** = Verifica se encontrou a palavra

---

## 🎯 ABA DE RECURSOS

### 📚 Recursos Dinâmicos por Categoria

```php
$category = $item['category'];
$resources = [];

if ($category === 'HTML') {
    $resources = [
        [
            'icon' => 'fab fa-html5 text-danger',
            'title' => 'MDN Web Docs - HTML',
            'description' => 'Documentação oficial',
            'url' => 'https://developer.mozilla.org/pt-BR/docs/Web/HTML',
            'type' => 'external'  // Abre em nova aba
        ],
        // ... mais recursos
    ];
}
```

### 🔗 Tipos de Links

1. **External** (`target="_blank"`) - Abre em nova aba
2. **Internal** - Navega dentro do site

```php
<?php echo $resource['type'] === 'external' ? 
    'target="_blank" rel="noopener noreferrer"' : ''; ?>
```

---

## 🎨 CSS - VARIÁVEIS E ESTILOS

### 🌈 Variáveis CSS Customizadas
```css
:root {
    --primary-color: #4361ee;      /* Cor principal */
    --secondary-color: #3a0ca3;    /* Cor secundária */
    --border-radius: 12px;         /* Bordas arredondadas */
    --shadow: 0 10px 30px rgba(0,0,0,0.08); /* Sombra */
    --transition: all 0.3s ease;   /* Animação padrão */
}
```

### 🎴 Classes Importantes

#### Card de Recurso
```css
.resource-card {
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    /* Auto-ajusta colunas, mínimo 180px, máximo 1 fração */
    
    min-height: 220px;
    /* Garante altura uniforme */
    
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    /* Distribui conteúdo verticalmente */
}
```

#### Hover Effects
```css
.resource-card:hover {
    border-color: var(--primary-color);
    transform: translateY(-5px);    /* Levanta 5px */
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
```

---

## 🔧 JAVASCRIPT - FUNCIONALIDADES

### 📋 Copiar Código
```javascript
function copyCode(button) {
    // Encontra o bloco de código mais próximo
    const codeBlock = button.closest('.code-example-container')
                            .querySelector('code');
    
    // Copia para área de transferência
    navigator.clipboard.writeText(codeBlock.textContent)
        .then(() => {
            // Feedback visual: muda ícone temporariamente
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.style.color = '#28a745';
            
            setTimeout(() => {
                button.innerHTML = originalHTML;
            }, 2000);
        });
}
```

### 🎯 Sistema de Progresso
```javascript
const continueBtn = document.getElementById('continueBtn');
continueBtn.addEventListener('click', function() {
    // Pega largura atual
    const currentWidth = parseInt(progressBar.style.width) || 0;
    
    // Adiciona 25%, máximo 100
    const newWidth = Math.min(currentWidth + 25, 100);
    
    // Atualiza visualmente
    progressBar.style.width = newWidth + '%';
    
    // Atualiza texto
    document.querySelector('.progress-stats span:first-child')
            .textContent = newWidth + '% completo';
});
```

---

## 🔐 SEGURANÇA

### 1️⃣ SQL Injection Prevention
```php
// ❌ NUNCA faça assim:
$query = "SELECT * FROM users WHERE id = " . $_GET['id'];

// ✅ USE prepared statements:
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
```

### 2️⃣ XSS Prevention
```php
// ❌ NUNCA imprima direto:
echo $item['title'];

// ✅ USE sanitize ou htmlspecialchars:
echo sanitize($item['title']);
echo htmlspecialchars($item['description']);
```

### 3️⃣ Type Casting
```php
$id = (int)$_GET['id'];  // Garante que é número
```

---

## 📊 FLUXO DE DADOS

```
1. URL: show.php?type=tutorial&id=2
           ↓
2. PHP captura parâmetros e sanitiza
           ↓
3. Busca dados (JSON ou MySQL)
           ↓
4. Enriquece com dados extras
           ↓
5. Gera exemplo de código específico
           ↓
6. Define recursos por categoria
           ↓
7. Renderiza HTML com dados
           ↓
8. JavaScript adiciona interatividade
           ↓
9. CSS aplica estilos e animações
```

---

## 🎓 RECURSOS INTEGRADOS

### HTML
- MDN Web Docs
- W3Schools
- Vídeos YouTube
- Exercícios práticos

### CSS
- MDN Docs
- CSS Tricks
- Grid Garden (jogo)
- Flexbox Froggy (jogo)

### JavaScript
- MDN Docs
- JavaScript.info
- FreeCodeCamp
- GitHub Projects

### PHP
- PHP Manual
- PHP The Right Way
- Laravel Docs

---

## 🐛 TRATAMENTO DE ERROS

### Try-Catch para Banco de Dados
```php
try {
    $stmt = $conn->prepare("SELECT ...");
    $stmt->execute([...]);
} catch (PDOException $e) {
    // Silencia erro se tabela não existir
    // Não quebra a página
}
```

### Validações
```php
// Se não tem tipo ou ID
if (!$type || !$id) {
    redirect('index.php');
}

// Se não encontrou item
if (!$item) {
    redirect($type === 'tutorial' ? 
             'tutorials_index.php' : 
             'exercises_index.php');
}
```

---

## 📱 RESPONSIVIDADE

### Media Queries
```css
@media (max-width: 768px) {
    .resources-grid {
        grid-template-columns: 1fr; /* 1 coluna em mobile */
    }
    
    .content-header-card {
        padding: 1.5rem; /* Menos padding */
    }
}
```

---

## ⚡ OTIMIZAÇÕES

1. **Lazy Loading**: Abas carregam conteúdo só quando ativadas
2. **Debounce**: Busca espera usuário parar de digitar
3. **Cache**: Dados ficam em variáveis PHP
4. **Minificação**: CSS/JS podem ser comprimidos
5. **CDN**: Bootstrap e Font Awesome via CDN

---

## 🔄 CICLO DE VIDA DA PÁGINA

```
1. Usuário clica em tutorial
2. PHP carrega dados
3. HTML é gerado dinamicamente
4. Browser baixa CSS
5. Browser baixa JavaScript
6. JavaScript adiciona eventos
7. Usuário interage (cliques, hovers)
8. AJAX pode atualizar sem recarregar
```

---

## 📝 CONVENÇÕES DE CÓDIGO

### Nomenclatura
- **Classes CSS**: kebab-case (`.resource-card`)
- **Variáveis PHP**: snake_case (`$user_progress`)
- **Funções JS**: camelCase (`copyCode()`)
- **Constantes CSS**: SCREAMING_SNAKE (`:root`)

### Comentários
```php
// Comentário de linha única

/* 
 * Comentário de múltiplas linhas
 * Usado para explicações longas
 */

/** 
 * DocBlock - Documentação formal
 * @param int $id
 * @return array
 */
```

---

## 🎯 PRÓXIMOS PASSOS

1. ✅ Adicionar sistema de favoritos
2. ✅ Implementar compartilhamento social
3. ✅ Sistema de avaliações
4. ⏳ Comentários e discussões
5. ⏳ Certificados de conclusão

---

## 📞 SUPORTE

- **Documentação**: Ver anotações/
- **Fórum**: forum_index.php
- **Admin**: admin.php

---

**Última atualização:** 17/11/2025
**Versão:** 2.0
**Autor:** Sistema WebLearn
