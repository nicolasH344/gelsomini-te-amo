# 🗄️ Banco de Dados WebLearn

Este diretório contém todos os scripts SQL para criar o banco de dados completo do WebLearn.

## 📋 Estrutura dos Arquivos

### Scripts de Criação (Execute em ordem):

1. **`01_create_database.sql`** - Cria o banco de dados
2. **`02_users_table.sql`** - Sistema de usuários
3. **`03_categories_exercises.sql`** - Categorias e exercícios
4. **`04_progress_system.sql`** - Sistema de progresso e conquistas
5. **`05_forum_system.sql`** - Sistema de fórum
6. **`06_tutorials_system.sql`** - Sistema de tutoriais
7. **`07_security_system.sql`** - Sistema de segurança
8. **`08_chat_system.sql`** - Sistema de chat
9. **`09_notifications_system.sql`** - Sistema de notificações
10. **`10_insert_sample_data.sql`** - Dados de exemplo

## 🚀 Como Usar

### Opção 1: Executar Todos os Scripts
```bash
# No MySQL/phpMyAdmin, execute os arquivos em ordem
# Ou use o comando:
mysql -u root -p < 01_create_database.sql
mysql -u root -p < 02_users_table.sql
# ... continue com todos os arquivos
```

### Opção 2: Script Único
```bash
# Concatenar todos os arquivos em um só:
cat *.sql > weblearn_complete.sql
mysql -u root -p < weblearn_complete.sql
```

## 📊 Tabelas Criadas

### 👥 Sistema de Usuários
- `users` - Dados dos usuários
- `user_sessions` - Sessões ativas
- `activity_logs` - Logs de atividade

### 📚 Sistema de Aprendizado
- `categories` - Categorias de conteúdo
- `exercises` - Exercícios disponíveis
- `tutorials` - Tutoriais do sistema
- `user_progress` - Progresso individual
- `tutorial_progress` - Progresso em tutoriais

### 🏆 Sistema de Gamificação
- `badges` - Conquistas disponíveis
- `user_badges` - Conquistas dos usuários

### 💬 Sistema Social
- `forum_categories` - Categorias do fórum
- `forum_posts` - Posts do fórum
- `forum_comments` - Comentários
- `forum_votes` - Sistema de votação
- `chat_rooms` - Salas de chat
- `chat_messages` - Mensagens do chat
- `online_users` - Usuários online

### 🔐 Sistema de Segurança
- `password_resets` - Tokens de recuperação
- `verification_codes` - Códigos de verificação

### 🔔 Sistema de Notificações
- `notifications` - Notificações dos usuários
- `notification_settings` - Configurações de notificação
- `email_logs` - Log de emails enviados

## 🔧 Configurações Importantes

- **Charset:** utf8mb4_unicode_ci
- **Engine:** InnoDB (padrão)
- **Chaves estrangeiras:** Habilitadas
- **Índices:** Otimizados para performance

## 📈 Recursos Avançados

- **Full-text search** em exercícios e tutoriais
- **JSON fields** para metadados flexíveis
- **Soft deletes** com status enum
- **Timestamps automáticos**
- **Índices compostos** para queries otimizadas

## 🛠️ Manutenção

### Backup
```bash
mysqldump -u root -p weblearn_db > backup_$(date +%Y%m%d).sql
```

### Limpeza de Dados Antigos
```sql
-- Limpar sessões antigas (>30 dias)
DELETE FROM user_sessions WHERE last_activity < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Limpar logs antigos (>90 dias)
DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

## 📝 Notas

- Todos os scripts são **idempotentes** (podem ser executados múltiplas vezes)
- Use `IF NOT EXISTS` para evitar erros
- Dados de exemplo incluem usuário admin padrão
- Senhas são hasheadas com bcrypt
- Sistema preparado para **multilíngue** (pt-br, en, es)