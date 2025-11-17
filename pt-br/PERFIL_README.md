# 🎯 Sistema de Perfil - Atualização Completa

## ✨ O que foi corrigido

### 1. Upload de Avatar ✅
- **ANTES**: Upload não salvava no banco de dados
- **AGORA**: 
  - Avatar é salvo no banco de dados na coluna `users.avatar`
  - Arquivo enviado para `uploads/avatars/`
  - Avatar antigo é automaticamente removido ao fazer upload de novo
  - Suporta JPG, PNG, GIF, WEBP (máximo 2MB)
  - Preview em tempo real antes do upload

### 2. Estatísticas Reais ✅
- **ANTES**: Dados fictícios/simulados
- **AGORA**: Todas as estatísticas vêm do banco de dados:
  - **Exercícios Completados**: Conta `user_progress` com status 'completed'
  - **Tutoriais Visualizados**: Conta tutoriais únicos em `tutorial_progress`
  - **Posts no Fórum**: Soma de `forum_posts` + `forum_comments`
  - **Badges Conquistados**: Conta registros em `user_badges`
  - **Nível**: Calculado com base em XP (cada 500 pontos = 1 nível)
  - **XP**: Soma dos pontos (`score`) de todos os exercícios completados

### 3. Sistema de Badges Automático ✅
- **Badges são concedidos automaticamente** quando você atinge os critérios:
  - 🌱 **Iniciante**: Complete 1 exercício
  - ❓ **Curioso**: Visualize 5 tutoriais
  - 🏆 **Persistente**: Complete 10 exercícios
  - 🤝 **Colaborador**: Faça 5 posts no fórum
  - ⭐ **Dedicado**: Complete 25 exercícios
  - 👑 **Mestre**: Complete 50 exercícios
  - 🔥 **Lenda**: Complete 100 exercícios

### 4. Informações de Perfil Salvas ✅
- Nome, sobrenome, email, biografia e website são salvos no banco
- Preferências (tema, idioma, notificações) são persistentes
- Validação de email duplicado

## 🚀 Como usar

### Primeira vez (Instalação)

1. **Execute o script de configuração**:
   ```
   http://localhost/gelsomini-te-amo/pt-br/setup_profile_tables.php
   ```
   Isso irá:
   - Adicionar colunas necessárias na tabela `users`
   - Criar tabelas `tutorial_progress`, `badges`, `user_badges`
   - Inserir badges padrão
   - Criar diretório de uploads

2. **Faça login** na plataforma

3. **Acesse seu perfil**:
   ```
   http://localhost/gelsomini-te-amo/pt-br/profile.php
   ```

### Fazendo upload de avatar

1. No perfil, clique no ícone de câmera sobre a foto
2. Escolha uma imagem (JPG, PNG, GIF ou WEBP, máx 2MB)
3. Visualize o preview
4. Clique em "Salvar"
5. A imagem será enviada e salva automaticamente

### Editando informações

#### Aba "Informações"
- Nome e Sobrenome
- Email (verificação de duplicata)
- Biografia (texto livre)
- Website (URL completo)

#### Aba "Preferências"
- Tema (Claro/Escuro/Automático)
- Idioma (Português/English/Español)
- Notificações por email
- Newsletter

#### Aba "Segurança"
- Alterar senha (em desenvolvimento)
- Ver sessões ativas

#### Aba "Atividade"
- Histórico de ações recentes

## 📊 Estrutura do Banco de Dados

### Novas Colunas em `users`
```sql
avatar VARCHAR(255) NULL          -- Caminho do arquivo de avatar
bio TEXT NULL                     -- Biografia do usuário
website VARCHAR(255) NULL         -- Website pessoal
theme VARCHAR(50) DEFAULT 'light' -- Tema preferido
language VARCHAR(10) DEFAULT 'pt' -- Idioma
notifications BOOLEAN DEFAULT 1   -- Receber notificações
newsletter BOOLEAN DEFAULT 0      -- Receber newsletter
```

### Tabela `tutorial_progress`
```sql
CREATE TABLE tutorial_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tutorial_id INT NOT NULL,
    progress INT DEFAULT 0,           -- 0 a 100
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, tutorial_id)
);
```

### Tabela `badges`
```sql
CREATE TABLE badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50),                 -- Classe Font Awesome
    color VARCHAR(20),                -- Cor do Bootstrap
    criteria_type VARCHAR(50),        -- exercises/tutorials/forum
    criteria_value INT               -- Quantidade necessária
);
```

### Tabela `user_badges`
```sql
CREATE TABLE user_badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    badge_id INT NOT NULL,
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, badge_id)
);
```

## 🔒 Segurança

### Upload de Arquivos
- ✅ Validação de tipo MIME (apenas imagens)
- ✅ Limite de tamanho (2MB)
- ✅ Renomeação automática (evita sobrescrever)
- ✅ .htaccess bloqueia execução de PHP
- ✅ Remoção automática de arquivo antigo

### Banco de Dados
- ✅ Prepared Statements (previne SQL Injection)
- ✅ Validação de duplicatas (email/username)
- ✅ Sanitização de inputs
- ✅ Senha hash com `password_hash()`

## 🐛 Solução de Problemas

### Avatar não aparece após upload
1. Verifique permissões: `chmod 755 pt-br/uploads/avatars/`
2. Confirme que `.htaccess` existe em `pt-br/uploads/`
3. Verifique no banco: `SELECT avatar FROM users WHERE id = SEU_ID`

### Estatísticas zeradas
1. Execute `setup_profile_tables.php` para criar tabelas
2. Complete alguns exercícios em `exercises_index.php`
3. Atualize a página de perfil

### Badges não aparecem
1. Execute `setup_profile_tables.php` para inserir badges
2. Complete exercícios para ganhar automaticamente
3. Verifique: `SELECT * FROM badges` e `SELECT * FROM user_badges WHERE user_id = SEU_ID`

### Erro "Column not found"
1. Execute `setup_profile_tables.php` para adicionar colunas
2. Ou execute manualmente:
```sql
ALTER TABLE users 
ADD COLUMN avatar VARCHAR(255) NULL,
ADD COLUMN bio TEXT NULL,
ADD COLUMN website VARCHAR(255) NULL;
```

## 📝 Arquivos Modificados

1. **profile.php**: Sistema completo de perfil
   - Upload de avatar com salvamento no banco
   - Estatísticas reais do banco de dados
   - Badges automáticos
   - Preferências persistentes

2. **setup_profile_tables.php**: Script de instalação
   - Cria/atualiza estrutura do banco
   - Insere dados padrão
   - Verifica permissões

3. **uploads/.htaccess**: Segurança
   - Bloqueia execução de PHP
   - Permite apenas imagens

## 🎨 Recursos Visuais

- Avatar circular com borda e sombra
- Tooltip com descrição dos badges
- Preview de imagem antes do upload
- Badges com cores diferentes (desbloqueados vs bloqueados)
- Progresso visual para próximo nível
- Estatísticas com ícones coloridos

## 📈 Próximas Melhorias Sugeridas

- [ ] Sistema de streak (sequência de dias estudando)
- [ ] Gráfico de progresso por categoria
- [ ] Atividades recentes detalhadas
- [ ] Alteração de senha funcional
- [ ] Gerenciamento de sessões ativas
- [ ] Exportar dados (LGPD)
- [ ] Crop de imagem ao fazer upload
- [ ] Compressão automática de imagens grandes

## 💡 Dicas de Uso

1. **Complete exercícios** para ganhar XP e badges automaticamente
2. **Visualize tutoriais** para desbloquear badge "Curioso"
3. **Participe do fórum** para ganhar badge "Colaborador"
4. **Use foto de perfil** personalizada para se destacar na comunidade
5. **Preencha biografia** para outros usuários conhecerem você

---

**Desenvolvido com ❤️ para WebLearn - Jornada do Desenvolvedor**
