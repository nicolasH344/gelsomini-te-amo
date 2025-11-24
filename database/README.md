# Banco de Dados - Plataforma de Aprendizado

Este diretório contém todos os arquivos relacionados ao banco de dados da plataforma.

## Arquivos

### 📄 schema.sql
Esquema completo do banco de dados com todas as tabelas, índices e relacionamentos.

**Principais tabelas:**
- `users` - Usuários da plataforma
- `exercises` - Exercícios de programação
- `user_progress` - Progresso dos usuários
- `forum_posts` / `forum_comments` - Sistema de fórum
- `chat_messages` - Chat em tempo real
- `badges` / `user_badges` - Sistema de gamificação
- `collaborative_sessions` - Sessões colaborativas
- `mentorship_requests` - Sistema de mentoria

### 📄 seed_data.sql
Dados iniciais para popular o banco com:
- Categorias de exercícios
- Exercícios de exemplo
- Categorias do fórum
- Badges do sistema
- Usuário administrador padrão
- Posts e tutoriais de exemplo

### 📄 install.php
Script PHP para instalação automática do banco de dados.

## Instalação

### Opção 1: Script Automático
1. Acesse: `http://localhost/gelsomini-te-amo/database/install.php`
2. O script criará o banco e inserirá os dados iniciais

### Opção 2: Manual via phpMyAdmin
1. Abra o phpMyAdmin
2. Execute o arquivo `schema.sql`
3. Execute o arquivo `seed_data.sql`

### Opção 3: Linha de Comando MySQL
```bash
mysql -u root -p < schema.sql
mysql -u root -p < seed_data.sql
```

## Configuração

Após a instalação, configure a conexão no arquivo `config.php`:

```php
$host = 'localhost';
$dbname = 'cursinho_db';
$username = 'root';
$password = '';
```

## Usuário Padrão

Após a instalação, você pode fazer login com:
- **Username:** admin
- **Email:** admin@cursinho.local  
- **Senha:** password

## Estrutura das Tabelas

### Usuários e Autenticação
- `users` - Dados dos usuários
- `password_reset_codes` - Códigos de recuperação de senha
- `online_users` - Rastreamento de usuários online

### Sistema de Exercícios
- `exercise_categories` - Categorias (HTML, CSS, JS, PHP)
- `exercises` - Exercícios com código inicial e solução
- `user_progress` - Progresso e submissões dos usuários

### Fórum e Comunidade
- `forum_categories` - Categorias do fórum
- `forum_posts` - Posts principais
- `forum_comments` - Comentários e respostas

### Recursos Colaborativos
- `chat_messages` - Sistema de chat
- `collaborative_sessions` - Sessões de código colaborativo
- `session_participants` - Participantes das sessões

### Gamificação e Mentoria
- `badges` - Definição dos badges
- `user_badges` - Badges conquistados pelos usuários
- `mentorship_requests` - Solicitações de mentoria

### Conteúdo e Integrações
- `tutorials` - Tutoriais da plataforma
- `github_integrations` - Integração com GitHub

## Índices e Performance

O banco foi otimizado com índices em:
- Campos de busca frequente (username, email, slug)
- Chaves estrangeiras
- Campos de filtro (status, difficulty, technology)
- Campos de ordenação (created_at, sort_order)

## Backup e Manutenção

Para fazer backup:
```bash
mysqldump -u root -p cursinho_db > backup_$(date +%Y%m%d).sql
```

Para limpeza de dados antigos (chat, sessões expiradas):
```sql
DELETE FROM chat_messages WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
DELETE FROM collaborative_sessions WHERE expires_at < NOW();
```