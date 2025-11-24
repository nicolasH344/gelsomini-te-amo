# Estrutura do Projeto e Arquitetura

## Organização de Diretórios

### Estrutura do Nível Raiz
```
gelsomini-te-amo/
├── pt-br/                 # Versão em português (principal)
├── en/                    # Versão em inglês
├── es/                    # Versão em espanhol
├── src/                   # Arquitetura orientada a objetos
├── database/              # Esquemas e configuração do banco de dados
├── Dump20250908/          # Arquivos de dump SQL
├── anotações/             # Anotações e documentação do projeto
├── index.php              # Ponto de entrada para auto-detecção de idioma
├── start.php              # Página de inicialização do sistema
├── style.css              # Folha de estilo global
└── script.js              # Funcionalidade JavaScript global
```

### Diretórios Específicos por Idioma
Cada pasta de idioma (pt-br/, en/, es/) contém estrutura idêntica:
```
{idioma}/
├── api/                   # Endpoints de API RESTful
│   ├── chat_messages.php
│   ├── collaborative_save.php
│   ├── exercise_chat.php
│   ├── forgot_password.php
│   ├── online_users.php
│   ├── password_reset.php
│   └── setup_password_reset.php
├── data/                  # Arquivos de dados estáticos
│   ├── tutorials.json
│   └── tutorials.php
├── sql/                   # Scripts de banco de dados (apenas pt-br)
└── [arquivos da aplicação] # Páginas principais da aplicação
```

### Arquitetura Moderna (src/)
```
src/
├── Config/
│   ├── Database.php       # Conexão singleton do banco de dados
│   └── Environment.php    # Configuração de ambiente
├── Controllers/
│   ├── BaseController.php # Fundação abstrata do controller
│   ├── ExerciseController.php # Lógica de gerenciamento de exercícios
│   └── ForumController.php    # Funcionalidade do fórum
├── Models/
│   ├── BaseModel.php      # Modelo abstrato com métodos comuns
│   ├── Badge.php          # Modelo do sistema de badges
│   ├── Exercise.php       # Modelo de dados de exercícios
│   ├── Forum.php          # Modelo de posts/comentários do fórum
│   ├── User.php           # Modelo de autenticação de usuário
│   └── UserProgress.php   # Modelo de acompanhamento de progresso
├── autoload.php           # Autoloader compatível com PSR-4
└── SecurityHelper.php     # Utilitários de segurança e validação
```

## Componentes Principais e Relacionamentos

### Sistema de Autenticação
- **Pontos de Entrada**: login.php, register.php, logout.php
- **Segurança**: forgot_password.php, reset_password.php
- **Suporte API**: api/password_reset.php, api/setup_password_reset.php
- **Models**: User.php manipula lógica de autenticação
- **Sessões**: Login persistente com funcionalidade "lembrar-me"

### Gerenciamento de Exercícios
- **Listagem**: exercises_index.php (procedural), exercises_index_oop.php (OOP)
- **Detalhes**: exercise_detail.php com editor de código integrado
- **Conclusão**: complete_exercise.php para acompanhamento de progresso
- **Colaboração**: collaborative_exercise.php para sessões multi-usuário
- **Controller**: ExerciseController.php gerencia lógica de exercícios
- **Model**: Exercise.php, UserProgress.php para gerenciamento de dados

### Sistema de Fórum
- **Visualização Principal**: forum_index.php (procedural), forum_index_oop.php (OOP)
- **Detalhes do Post**: forum_post.php com comentários aninhados
- **Funções**: forum_functions.php para operações comuns
- **Controller**: ForumController.php manipula lógica do fórum
- **Model**: Forum.php gerencia posts e comentários

### Recursos em Tempo Real
- **Sistema de Chat**: chat.php com backend api/chat_messages.php
- **Usuários Online**: api/online_users.php para rastreamento de presença
- **Edição Colaborativa**: Compartilhamento e edição de código em tempo real

### Interface Administrativa
- **Painel Admin**: admin.php para gerenciamento do sistema
- **Gerenciamento de Tutoriais**: admin_tutorial_form.php, gerenciartuto.php
- **Configuração do Banco**: setup_database.php, install_cursinho_db.php

## Padrões Arquiteturais

### Abordagem de Arquitetura Dupla
O projeto implementa padrões tanto procedurais quanto orientados a objetos:

**Arquivos Procedurais** (Legado/Original):
- Processamento PHP direto com HTML incorporado
- Conexões imediatas de banco de dados
- Organização de código baseada em funções
- Arquivos: exercises_index.php, forum_index.php, etc.

**Arquivos Orientados a Objetos** (Moderno):
- Implementação do padrão MVC
- Injeção de dependência através do autoloader
- Arquitetura baseada em classes com herança
- Arquivos: exercises_index_oop.php, forum_index_oop.php, etc.

### Camada de Banco de Dados
- **Padrão Singleton**: Database.php garante instância única de conexão
- **Implementação PDO**: Prepared statements para segurança
- **Abstração de Model**: BaseModel.php fornece operações CRUD comuns
- **Suporte a Migração**: Arquivos SQL em database/ e Dump20250908/

### Arquitetura de Segurança
- **Sanitização de Entrada**: SecurityHelper.php centraliza validação
- **Proteção CSRF**: Segurança de formulário baseada em token
- **Segurança de Senha**: Hash bcrypt com salt
- **Gerenciamento de Sessão**: Manipulação segura de sessão com regeneração

### Internacionalização
- **Detecção de Idioma**: index.php auto-detecta idioma do navegador
- **Localização Baseada em Pasta**: Cópias completas da aplicação por idioma
- **Estrutura Consistente**: Organização idêntica de arquivos entre idiomas
- **Sistema de Fallback**: Padrão para português se idioma não suportado

### Design de API
- **Endpoints RESTful**: Estrutura de API limpa em diretórios api/
- **Respostas JSON**: Formato de dados padronizado
- **Autenticação**: Acesso à API baseado em sessão
- **Suporte em Tempo Real**: Atualizações baseadas em polling para chat e colaboração

## Relacionamentos de Componentes

### Fluxo de Dados
1. **Requisição do Usuário** → Detecção de Idioma (index.php) → Pasta de Idioma Apropriada
2. **Autenticação** → Model User.php → Gerenciamento de Sessão
3. **Acesso a Exercícios** → ExerciseController → Model Exercise.php → Banco de Dados
4. **Acompanhamento de Progresso** → UserProgress.php → Atualizações do Banco de Dados
5. **Interação do Fórum** → ForumController → Model Forum.php → Atualizações em Tempo Real

### Pontos de Integração
- **Integração GitHub**: github_integration.php conecta com API do GitHub
- **Sistema de Email**: email_config.php, setup_email.php para notificações
- **Sistema de Badges**: badges.php integra com UserProgress para conquistas
- **Mentoria**: mentorship.php conecta usuários para aprendizado entre pares
- **Integração de Chat**: Incorporado em páginas de exercícios e fórum para comunicação contextual