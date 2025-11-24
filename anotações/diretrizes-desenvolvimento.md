# Diretrizes e Padrões de Desenvolvimento

## Padrões de Qualidade de Código

### Padrões de Codificação PHP
- **Gerenciamento de Sessão**: Sempre verificar status da sessão antes de iniciar: `if (session_status() == PHP_SESSION_NONE) { session_start(); }`
- **Inclusão de Arquivos**: Use `require_once` para dependências críticas, `include` para componentes opcionais
- **Sanitização de Variáveis**: Sanitize todas as entradas do usuário usando função personalizada `sanitize()` ou `htmlspecialchars()`
- **Funções de Array**: Prefira sintaxe PHP moderna com arrow functions: `array_filter($array, fn($item) => $condition)`
- **Null Coalescing**: Use operador `??` para valores padrão: `$_GET['param'] ?? 'default'`
- **Conversão de Tipo**: Conversão explícita de tipo para segurança: `max(1, (int)$_GET['page'])`

### Padrões HTML/CSS
- **Estrutura Semântica**: Use hierarquia adequada de cabeçalhos (h1, h2, h3) e elementos semânticos HTML5
- **Integração Bootstrap**: Uso consistente de classes Bootstrap 5 com melhorias CSS personalizadas
- **Acessibilidade**: Inclua labels ARIA e rotulagem adequada de formulários: `aria-hidden="true"`, `aria-label`, `aria-current`
- **Uso de Ícones**: Ícones Font Awesome com significado semântico e atributos de acessibilidade
- **Design Responsivo**: Abordagem mobile-first com sistema de grid Bootstrap

### Padrões JavaScript
- **Sanitização de Entrada**: Sempre escape HTML em conteúdo dinâmico usando função `escapeHtml()`
- **Manipulação de Eventos**: Use `addEventListener` para vinculação de eventos com tratamento adequado de erros
- **Manipulação DOM**: Verifique existência do elemento antes da manipulação: `if (container) return;`
- **Nomenclatura de Funções**: Nomes descritivos de funções com propósito claro: `renderBasicExercises()`, `filterAdvancedExercises()`
- **Validação de Dados**: Valide e sanitize todas as entradas do usuário antes do processamento

## Padrões Arquiteturais

### Implementação MVC
- **Controllers**: Estenda a classe `BaseController` com funcionalidade comum
- **Models**: Herde de `BaseModel` para operações compartilhadas de banco de dados
- **Separação**: Separação clara entre lógica de negócio (controllers) e acesso a dados (models)
- **Injeção de Dependência**: Injeção por construtor para dependências de modelo

### Padrões de Banco de Dados
- **Padrão Singleton**: Conexão de banco de dados usando padrão singleton em `Database.php`
- **Uso PDO**: Prepared statements para todas as consultas de banco de dados
- **Tratamento de Erros**: Tratamento adequado de exceções com logging: `error_log("Database connection failed: " . $e->getMessage())`
- **Configuração de Ambiente**: Use `Environment::get()` para valores de configuração com padrões

### Padrões de Segurança
- **Validação de Entrada**: Sanitize todas as entradas do usuário usando funções dedicadas
- **Prevenção de Injeção SQL**: Use prepared statements exclusivamente
- **Prevenção XSS**: Escape saída com `htmlspecialchars()` ou `escapeHtml()` personalizado
- **Segurança de Sessão**: Gerenciamento adequado de sessão com verificações de status

## Padrões de Organização de Arquivos

### Estrutura de Diretórios
- **Pastas de Idioma**: Estrutura idêntica entre diretórios `pt-br/`, `en/`, `es/`
- **Endpoints API**: Diretório `api/` separado para endpoints RESTful
- **Arquivos de Dados**: Dados estáticos no diretório `data/` com formatos JSON e PHP
- **Arquitetura Moderna**: Código orientado a objetos no diretório `src/` com autoloading PSR-4

### Convenções de Nomenclatura de Arquivos
- **Arquivos Procedurais**: Nomes descritivos como `tutorials_index.php`, `forum_index.php`
- **Arquivos OOP**: Sufixo com `_oop.php` para versões orientadas a objetos
- **Arquivos API**: Nomes claros de endpoint no diretório `api/`
- **Configuração**: Arquivos de configuração centralizados como `config.php`, `database.php`

### Padrões de Inclusão
- **Header/Footer**: Inclusão consistente de `header.php` e `footer.php`
- **Configuração**: Sempre inclua `config.php` no início da execução
- **Fontes de Dados**: Inclua arquivos de dados quando necessário: `require_once 'data/tutorials.php'`

## Padrões de Interface do Usuário

### Design de Formulários
- **Formulários Bootstrap**: Uso consistente de classes de formulário Bootstrap
- **Validação**: Validação client-side e server-side
- **Acessibilidade**: Rotulagem adequada e atributos ARIA
- **Tratamento de Erros**: Mensagens de erro amigáveis com feedback visual

### Padrões de Navegação
- **Breadcrumbs**: Hierarquia de navegação clara
- **Paginação**: Paginação consistente com suporte à acessibilidade
- **Filtragem**: Filtragem em tempo real com busca debounced
- **Suporte a Temas**: Múltiplas opções de tema com persistência localStorage

### Design Visual
- **Layout de Cards**: Design consistente baseado em cards para exibição de conteúdo
- **Sistema de Badges**: Badges codificados por cor para categorias e níveis de dificuldade
- **Ícones**: Ícones Font Awesome significativos com atributos de acessibilidade
- **Grid Responsivo**: Sistema de grid Bootstrap para todos os layouts

## Padrões de Gerenciamento de Dados

### Manipulação de Dados JSON
- **Armazenamento Baseado em Arquivo**: Arquivos JSON para configuração e dados estáticos
- **Dados Padrão**: Criação automática de dados padrão quando arquivos não existem
- **Pretty Printing**: Use `JSON_PRETTY_PRINT` para arquivos JSON legíveis
- **Tratamento de Erros**: Fallback para arrays vazios em erros de decode JSON

### Processamento de Arrays
- **Filtragem**: Use `array_filter()` com arrow functions para filtragem de dados
- **Mapeamento**: Transforme dados usando `array_map()` e sintaxe PHP moderna
- **Fatiamento**: Paginação usando `array_slice()` para performance
- **Contagem**: Use `count()` e `array_column()` para estatísticas

### Gerenciamento de Estado
- **Variáveis de Sessão**: Gerenciamento adequado de variáveis de sessão
- **Local Storage**: Preferências client-side usando localStorage
- **Parâmetros URL**: Manipulação limpa de parâmetros URL com padrões
- **Estado de Formulário**: Mantenha estado do formulário entre submissões

## Otimização de Performance

### Otimização Frontend
- **Recursos CDN**: Use CDN para bibliotecas externas (Bootstrap, Font Awesome)
- **Lazy Loading**: Implemente lazy loading para conteúdo dinâmico
- **Debouncing**: Debounce entradas de busca para reduzir requisições ao servidor
- **Caching**: Cache do navegador para recursos estáticos

### Otimização Backend
- **Consultas de Banco de Dados**: Consultas eficientes com indexação adequada
- **Operações de Arquivo**: Minimize operações de I/O de arquivo
- **Uso de Memória**: Operações eficientes de array e gerenciamento de memória
- **Log de Erros**: Log adequado de erros sem expor dados sensíveis

### Organização de Código
- **Autoloading**: Autoloading compatível com PSR-4 para classes
- **Gerenciamento de Dependências**: Dependências mínimas com separação clara
- **Reutilização de Código**: Funcionalidade compartilhada em classes base e funções helper
- **Design Modular**: Arquitetura modular para manutenibilidade

## Testes e Depuração

### Tratamento de Erros
- **Tratamento de Exceções**: Blocos try-catch adequados com mensagens significativas
- **Logging**: Use `error_log()` para depuração sem expor erros aos usuários
- **Fallbacks**: Degradação graciosa quando recursos falham
- **Validação**: Validação de entrada em múltiplos níveis

### Práticas de Desenvolvimento
- **Comentários de Código**: Comentários significativos para lógica complexa
- **Documentação de Funções**: Propósitos e parâmetros claros de funções
- **Nomenclatura de Variáveis**: Nomes descritivos de variáveis seguindo convenções
- **Formatação de Código**: Indentação e formatação consistentes

### Testes de Segurança
- **Sanitização de Entrada**: Teste todos os vetores de entrada para XSS e injeção
- **Autenticação**: Verifique lógica de autenticação e autorização
- **Gerenciamento de Sessão**: Teste manipulação e segurança de sessão
- **Validação de Dados**: Valide todas as transformações e armazenamento de dados