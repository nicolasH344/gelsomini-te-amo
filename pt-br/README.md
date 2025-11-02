# WebLearn - Jornada do Desenvolvedor

## 📋 Visão Geral
Plataforma interativa de aprendizagem para desenvolvimento web com exercícios práticos, fórum da comunidade e sistema de progresso completo.

## 🌐 Idiomas Suportados
- **Português (pt-br/)** - Idioma principal
- **English (en/)** - Versão em inglês
- **Español (es/)** - Versão em espanhol

## 🚀 Funcionalidades Implementadas

### 🔐 Sistema de Autenticação
- **Login/Logout** com validação segura
- **Registro de usuários** com validação completa
- **Recuperação de senha** via código de verificação
- **Sessões persistentes** com "Lembrar de mim"
- **Contas de teste**: admin/admin123, usuario/123456

### 📚 Sistema de Exercícios
- **Catálogo completo** com exercícios de HTML, CSS, JavaScript e PHP
- **Filtros avançados** por categoria, dificuldade e busca
- **Editor de código integrado** com syntax highlighting
- **Sistema de avaliação** automática com pontuação
- **Progresso do usuário** com tracking de conclusões
- **Soluções disponíveis** para cada exercício
- **Dicas e instruções** detalhadas

### 💬 Fórum da Comunidade
- **Posts e discussões** organizados por categorias
- **Sistema de comentários** em tempo real
- **Filtros e busca** de conteúdo
- **Estatísticas** de posts e membros ativos
- **Interface responsiva** para mobile e desktop

### 📊 Sistema de Progresso
- **Tracking completo** de exercícios realizados
- **Estatísticas detalhadas** por categoria
- **Percentual de conclusão** geral e por área
- **Histórico de tentativas** e pontuações
- **Dashboard visual** com gráficos de progresso

### 🎨 Interface e UX
- **4 temas visuais**: Roxo, Azul, Verde e Escuro
- **Modo acessibilidade** para pessoas com daltonismo
- **Design responsivo** para todos os dispositivos
- **Animações suaves** e transições
- **Navegação intuitiva** com breadcrumbs

### 🏗️ Arquitetura Técnica
- **Padrão MVC** com orientação a objetos
- **Autoloader PSR-4** para classes
- **Singleton Database** com PDO
- **Controllers especializados** (Forum, Exercise, User)
- **Models com herança** (BaseModel)
- **API RESTful** para recuperação de senha

## 📁 Estrutura do Projeto

```
gelsomini-te-amo/
├── pt-br/                 # Versão em português
├── en/                    # Versão em inglês  
├── es/                    # Versão em espanhol
├── src/                   # Código orientado a objetos
│   ├── Config/           # Configurações (Database)
│   ├── Controllers/      # Controladores MVC
│   ├── Models/          # Modelos de dados
│   └── autoload.php     # Carregamento automático
├── Dump20250908/        # Scripts SQL do banco
└── style.css           # Estilos globais
```

## 🗄️ Banco de Dados
- **users** - Usuários do sistema
- **exercises** - Catálogo de exercícios
- **exercise_categories** - Categorias dos exercícios
- **user_progress** - Progresso dos usuários
- **forum_posts** - Posts do fórum
- **forum_comments** - Comentários do fórum
- **forum_categories** - Categorias do fórum
- **password_reset_codes** - Códigos de recuperação

## 🔧 Configuração e Instalação

### Pré-requisitos
- XAMPP/WAMP com PHP 7.4+
- MySQL 5.7+
- Navegador moderno

### Instalação
1. Clone o projeto no htdocs do XAMPP
2. Configure o banco no `config.php`
3. Execute `setup_database.php` em qualquer idioma
4. Acesse `http://localhost/gelsomini-te-amo/`

### URLs de Acesso
- **Auto-detect**: `http://localhost/gelsomini-te-amo/`
- **Português**: `http://localhost/gelsomini-te-amo/pt-br/`
- **English**: `http://localhost/gelsomini-te-amo/en/`
- **Español**: `http://localhost/gelsomini-te-amo/es/`

## 🎯 Páginas Principais

### Versão Procedural (Original)
- `forum_index.php` - Fórum da comunidade
- `exercises_index.php` - Lista de exercícios
- `exercise_detail.php` - Detalhes do exercício
- `forum_post.php` - Post individual do fórum
- `login.php` / `register.php` - Autenticação
- `forgot_password.php` - Recuperação de senha

### Versão OOP (Moderna)
- `forum_index_oop.php` - Fórum com MVC
- `exercises_index_oop.php` - Exercícios com MVC
- `exercise_detail_oop.php` - Detalhes com MVC

## 🔒 Segurança Implementada
- **Sanitização** de dados de entrada
- **Prepared statements** para SQL
- **Password hashing** com bcrypt
- **Validação CSRF** em formulários
- **Sessões seguras** com regeneração de ID

## 📱 Recursos de Acessibilidade
- **ARIA labels** em todos os elementos
- **Navegação por teclado** completa
- **Alto contraste** no modo acessibilidade
- **Textos alternativos** em imagens
- **Estrutura semântica** HTML5

## 🌟 Destaques Técnicos
- **Multilíngue completo** com detecção automática
- **Sistema de temas** dinâmico
- **Progresso em tempo real** dos exercícios
- **API de recuperação** de senha funcional
- **Arquitetura escalável** com OOP
- **Interface moderna** com Bootstrap 5

## 🚀 Próximas Melhorias
- Sistema de badges e conquistas
- Chat em tempo real
- Exercícios colaborativos
- Integração com GitHub
- App mobile nativo
- Sistema de mentoria

## 👥 Contas de Teste
- **Administrador**: admin / admin123
- **Usuário comum**: usuario / 123456

## 📞 Suporte
Para dúvidas ou problemas, utilize o fórum da própria plataforma ou entre em contato através do sistema de mensagens.

---
**Desenvolvido com ❤️ para a comunidade de desenvolvedores**