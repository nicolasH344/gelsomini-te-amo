# Stack Tecnológico e Dependências

## Linguagens de Programação e Versões

### Tecnologias Backend
- **PHP 7.4+** - Linguagem de script server-side
- **MySQL 5.7+** - Gerenciamento de banco de dados relacional
- **SQL** - Consultas de banco de dados e gerenciamento de esquema

### Tecnologias Frontend
- **HTML5** - Marcação semântica e estrutura
- **CSS3** - Estilização e design responsivo
- **JavaScript (ES6+)** - Interatividade client-side
- **Bootstrap 5.3.0** - Framework CSS para design responsivo
- **Font Awesome 6.4.0** - Biblioteca de ícones

### Ambiente de Desenvolvimento
- **XAMPP/WAMP** - Stack de servidor de desenvolvimento local
- **Apache** - Servidor web
- **phpMyAdmin** - Interface de administração de banco de dados

## Sistemas de Build e Dependências

### Bibliotecas Externas (CDN)
```html
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Ícones Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
```

### Extensões PHP Necessárias
- **PDO** - Camada de abstração de banco de dados
- **PDO_MySQL** - Driver MySQL para PDO
- **Session** - Gerenciamento de sessão
- **JSON** - Manipulação de dados JSON
- **mbstring** - Funções de string multibyte
- **OpenSSL** - Funções de criptografia e segurança

### Esquema do Banco de Dados
- **cursinho_users** - Autenticação e perfis de usuário
- **cursinho_exercises** - Catálogo e conteúdo de exercícios
- **cursinho_exercise_categories** - Categorização de exercícios
- **cursinho_user_progress** - Acompanhamento de progresso
- **cursinho_forum_posts** - Discussões do fórum
- **cursinho_forum_comments** - Threads de comentários do fórum
- **cursinho_forum_categories** - Organização do fórum
- **cursinho_tutorials** - Gerenciamento de conteúdo de tutoriais
- **password_reset_codes** - Sistema de recuperação de senha
- **chat_messages** - Funcionalidade de chat em tempo real
- **collaborative_sessions** - Sessões de exercícios multi-usuário
- **user_badges** - Sistema de gamificação
- **mentorship_requests** - Conexões de aprendizado entre pares
- **github_integrations** - Integração de controle de versão

## Comandos de Desenvolvimento e Configuração

### Configuração de Desenvolvimento Local
```bash
# 1. Iniciar serviços XAMPP
# Iniciar Apache e MySQL do Painel de Controle XAMPP

# 2. Inicialização do banco de dados
# Acesso: http://localhost/gelsomini-te-amo/pt-br/setup_database.php

# 3. Configuração alternativa do banco de dados
# Acesso: http://localhost/gelsomini-te-amo/pt-br/install_cursinho_db.php
```

### URLs do Projeto
```
# Ponto de entrada para auto-detecção
http://localhost/gelsomini-te-amo/

# Acesso específico por idioma
http://localhost/gelsomini-te-amo/pt-br/    # Português
http://localhost/gelsomini-te-amo/en/       # Inglês
http://localhost/gelsomini-te-amo/es/       # Espanhol

# Inicialização do sistema
http://localhost/gelsomini-te-amo/start.php

# Teste de configuração
http://localhost/gelsomini-te-amo/test.php
```

### Configuração do Banco de Dados
```php
// estrutura do config.php
$host = 'localhost';
$dbname = 'cursinho_db';
$username = 'root';
$password = '';
$charset = 'utf8mb4';
```

### Requisitos de Estrutura de Arquivos
```
# Permissões de diretório necessárias
/gelsomini-te-amo/          # Acesso Leitura/Escrita
├── pt-br/api/              # Endpoints API - Acesso de Escrita
├── en/api/                 # Endpoints API - Acesso de Escrita
├── es/api/                 # Endpoints API - Acesso de Escrita
├── database/               # Arquivos SQL - Acesso de Leitura
├── Dump20250908/           # Arquivos de backup - Acesso de Leitura
└── src/                    # Classes OOP - Acesso de Leitura
```

## Padrões Arquiteturais e Padrões

### Padrões PHP
- **PSR-4** - Implementação de padrão de autoloading
- **Padrão MVC** - Arquitetura Model-View-Controller
- **Padrão Singleton** - Gerenciamento de conexão de banco de dados
- **Padrão Factory** - Criação de objetos em controllers

### Padrões de Segurança
- **PDO Prepared Statements** - Prevenção de injeção SQL
- **Hash de Senha bcrypt** - Armazenamento seguro de senhas
- **Proteção de Token CSRF** - Prevenção de falsificação de requisição cross-site
- **Sanitização de Entrada** - Prevenção de ataques XSS
- **Segurança de Sessão** - Gerenciamento seguro de sessão

### Organização de Código
- **Autoloader** - `src/autoload.php` para carregamento de classes
- **Classes Base** - Controllers e models abstratos
- **Estrutura de Namespace** - Hierarquia de classes organizada
- **Gerenciamento de Configuração** - Configurações centralizadas

### Design de API
- **Endpoints RESTful** - Estrutura de API limpa
- **Respostas JSON** - Formato de dados padronizado
- **Tratamento de Erros** - Respostas de erro consistentes
- **Autenticação** - Acesso à API baseado em sessão

## Performance e Otimização

### Otimização Frontend
- **Recursos CDN** - Entrega de biblioteca externa
- **Assets Minificados** - Arquivos CSS/JS comprimidos
- **Imagens Responsivas** - Entrega de mídia otimizada
- **Lazy Loading** - Carregamento diferido de conteúdo

### Otimização Backend
- **Indexação de Banco de Dados** - Performance de consulta otimizada
- **Connection Pooling** - Conexões de banco de dados eficientes
- **Estratégia de Cache** - Cache de dados baseado em sessão
- **Otimização de Consultas** - Operações SQL eficientes

### Ferramentas de Desenvolvimento
- **DevTools do Navegador** - Depuração frontend
- **phpMyAdmin** - Gerenciamento de banco de dados
- **Logs XAMPP** - Rastreamento de erros do servidor
- **Inspetor de Rede** - Monitoramento de requisições API