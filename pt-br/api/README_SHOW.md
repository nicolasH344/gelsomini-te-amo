# API de Comunidade - WebLearn

API completa para funcionalidades de comunidade (discussões e soluções compartilhadas).

## 📁 Estrutura da API

### Discussões

#### GET `get_discussions.php`
Obtém todas as discussões de um tutorial ou exercício

**Parâmetros:**
- `content_type` (string): 'tutorial' ou 'exercise'
- `content_id` (int): ID do conteúdo

**Resposta:**
```json
{
  "success": true,
  "discussions": [
    {
      "id": 1,
      "user_name": "João Silva",
      "message": "Ótimo tutorial!",
      "likes": 5,
      "replies_count": 3,
      "created_at": "2024-11-24 10:30:00"
    }
  ],
  "total": 1
}
```

#### POST `add_discussion.php`
Adiciona nova discussão (requer autenticação)

**Parâmetros:**
- `content_type` (string)
- `content_id` (int)
- `message` (string, min: 10 caracteres)

**Resposta:**
```json
{
  "success": true,
  "message": "Discussão publicada com sucesso!",
  "discussion": { ... }
}
```

#### POST `like_discussion.php`
Curtir/descurtir discussão (requer autenticação)

**Parâmetros:**
- `discussion_id` (int)

**Resposta:**
```json
{
  "success": true,
  "action": "added",
  "total_likes": 6
}
```

### Respostas

#### GET `get_replies.php`
Obtém respostas de uma discussão

**Parâmetros:**
- `discussion_id` (int)

**Resposta:**
```json
{
  "success": true,
  "replies": [
    {
      "id": 1,
      "user_name": "Maria",
      "message": "Concordo!",
      "created_at": "2024-11-24 11:00:00"
    }
  ],
  "total": 1
}
```

#### POST `add_reply.php`
Adiciona resposta a discussão (requer autenticação)

**Parâmetros:**
- `discussion_id` (int)
- `message` (string, min: 5 caracteres)

### Soluções

#### GET `get_solutions.php`
Obtém soluções compartilhadas

**Parâmetros:**
- `content_type` (string)
- `content_id` (int)

**Resposta:**
```json
{
  "success": true,
  "solutions": [
    {
      "id": 1,
      "user_name": "Pedro",
      "title": "Solução com Grid",
      "code": "...",
      "language": "css",
      "likes": 10,
      "created_at": "2024-11-24 09:00:00"
    }
  ],
  "total": 1
}
```

#### POST `add_solution.php`
Compartilha solução (requer autenticação)

**Parâmetros:**
- `content_type` (string)
- `content_id` (int)
- `title` (string)
- `code` (string, min: 20 caracteres)
- `language` (string): html, css, javascript, php, python

## 🗄️ Estrutura do Banco de Dados

### Tabela: `discussions`
```sql
CREATE TABLE discussions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content_type VARCHAR(50) NOT NULL,
    content_id INT NOT NULL,
    user_id INT NOT NULL,
    user_name VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_content (content_type, content_id)
);
```

### Tabela: `discussion_likes`
```sql
CREATE TABLE discussion_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    discussion_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (discussion_id, user_id),
    FOREIGN KEY (discussion_id) REFERENCES discussions(id) ON DELETE CASCADE
);
```

### Tabela: `discussion_replies`
```sql
CREATE TABLE discussion_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    discussion_id INT NOT NULL,
    user_id INT NOT NULL,
    user_name VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (discussion_id) REFERENCES discussions(id) ON DELETE CASCADE
);
```

### Tabela: `community_solutions`
```sql
CREATE TABLE community_solutions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content_type VARCHAR(50) NOT NULL,
    content_id INT NOT NULL,
    user_id INT NOT NULL,
    user_name VARCHAR(100) NOT NULL,
    title VARCHAR(200) NOT NULL,
    code TEXT NOT NULL,
    language VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_content (content_type, content_id)
);
```

### Tabela: `solution_likes`
```sql
CREATE TABLE solution_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    solution_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (solution_id, user_id)
);
```

## ✅ Recursos

- ✅ Auto-criação de tabelas no primeiro uso
- ✅ Suporte a MySQLi
- ✅ Validação de entrada
- ✅ Autenticação de usuário
- ✅ Tratamento de erros
- ✅ JSON responses padronizadas
- ✅ Sistema de likes
- ✅ Sistema de respostas aninhadas
- ✅ Contadores em tempo real

## 🚀 Uso

```javascript
// Carregar discussões
fetch(`api/get_discussions.php?content_type=tutorial&content_id=1`)
  .then(res => res.json())
  .then(data => console.log(data.discussions));

// Adicionar discussão
const formData = new FormData();
formData.append('content_type', 'tutorial');
formData.append('content_id', 1);
formData.append('message', 'Excelente conteúdo!');

fetch('api/add_discussion.php', {
  method: 'POST',
  body: formData
})
  .then(res => res.json())
  .then(data => console.log(data));
```

## 🔒 Segurança

- Todas as entradas são sanitizadas
- Queries usam prepared statements
- Autenticação obrigatória para POST
- FOREIGN KEY constraints para integridade
- Validação de comprimento mínimo de mensagens

## 📝 Notas

- As tabelas são criadas automaticamente na primeira execução
- O código suporta tanto PDO quanto MySQLi
- Todas as respostas são em JSON
- Timestamps em formato MySQL padrão
