# 🚀 Guia do Editor de Código Interativo

## 📋 Visão Geral

O editor de código foi completamente reformulado com funcionalidades avançadas, incluindo execução de código JavaScript em tempo real e um sistema de desafios aleatórios para praticar programação.

## ✨ Funcionalidades Principais

### 🎯 Sistema de Desafios Aleatórios

**Como usar:**
1. Clique no botão **"Desafio Aleatório"** (botão amarelo com ícone de dados)
2. Um modal será exibido com:
   - Título do desafio
   - Nível de dificuldade (Fácil, Médio, Difícil)
   - Descrição do problema
   - Lista dos testes que serão executados
3. O template do código será carregado automaticamente no editor
4. Implemente sua solução
5. Clique em **"Executar Testes"** para validar

**Desafios Disponíveis:**
- ✅ **Fácil**: Soma de Números, Par ou Ímpar, Ordenar Array
- ⚠️ **Médio**: Reverter String, Maior Número, Contar Vogais, Fatorial, Palíndromo, Remover Duplicatas
- 🔥 **Difícil**: Fibonacci

### 🎨 Temas do Editor

5 temas profissionais disponíveis:
- **Default**: Tema claro estilo VS Code
- **Dark**: Tema escuro (#1e1e1e)
- **Monokai**: Cores quentes (#272822)
- **Dracula**: Estilo vampiro (#282a36)
- **GitHub**: Tema branco limpo

**Como trocar:** Use o seletor "Tema" no topo do editor.

### 🔤 Tamanhos de Fonte

Opções: 12px, 14px, 16px, 18px, 20px

**Como trocar:** Use o seletor de tamanho ao lado do tema.

### ⚡ Execução de Código

#### Executar Código (`Ctrl+Enter`)
- Executa o código JavaScript digitado
- Captura e exibe `console.log()`
- Mostra valores retornados
- Mede tempo de execução
- Exibe erros detalhados com stack trace

#### Executar Testes
- Valida sua solução contra casos de teste predefinidos
- Mostra quais testes passaram/falharam
- Exibe valores esperados vs recebidos
- Calcula taxa de sucesso
- Animação de celebração quando todos os testes passam! 🎉

### 📝 Recursos do Editor

#### Histórico Undo/Redo
- **Undo** (`Ctrl+Z`): Desfazer até 50 ações
- **Redo** (`Ctrl+Y`): Refazer ações desfeitas

#### Ferramentas de Edição
- **Copiar Código**: Copia todo o conteúdo para área de transferência
- **Formatar Código** (`Shift+Alt+F`): Formata automaticamente
- **Tab**: Insere 2 espaços (boa prática JavaScript)

#### Visualização
- **Números de Linha**: Sidebar sincronizada com scroll
- **Fullscreen** (`F11`): Modo tela cheia
- **Status Bar**: Mostra posição do cursor, seleção, linhas totais

#### Persistência
- **Auto-Save**: Salva automaticamente no localStorage
- **Salvar** (`Ctrl+S`): Salva manualmente
- **Download**: Baixa código como arquivo `.js`
- **Reset**: Restaura código inicial

### ⌨️ Atalhos de Teclado

| Atalho | Ação |
|--------|------|
| `Ctrl+Enter` | Executar código |
| `Ctrl+S` | Salvar |
| `Ctrl+Z` | Desfazer |
| `Ctrl+Y` | Refazer |
| `Shift+Alt+F` | Formatar código |
| `F11` | Modo tela cheia |
| `Tab` | Inserir 2 espaços |

### 📊 Barra de Status

Informações em tempo real:
- **Esquerda**:
  - Posição do cursor (Linha, Coluna)
  - Texto selecionado (quantidade de caracteres)
  - Linguagem (JavaScript)

- **Direita**:
  - Total de linhas
  - Espaçamento (2 espaços)
  - Encoding (UTF-8)
  - Status auto-save (Salvando/Salvo)

## 🎓 Fluxo de Uso Recomendado

### Para Iniciantes

1. **Escolha um Desafio Fácil**
   - Clique em "Desafio Aleatório" até encontrar nível "Fácil"
   
2. **Leia a Descrição**
   - Entenda o que é pedido
   - Veja os casos de teste

3. **Implemente a Solução**
   - Use o template fornecido
   - Complete a função

4. **Teste Sua Solução**
   - Clique em "Executar Código" para ver se funciona
   - Use `console.log()` para debugar
   - Clique em "Executar Testes" para validar

5. **Ajuste e Melhore**
   - Veja quais testes falharam
   - Corrija o código
   - Teste novamente

### Para Avançados

1. **Desafios Difíceis**
   - Fibonacci, algoritmos complexos
   
2. **Otimize Performance**
   - Veja tempo de execução nos testes
   - Melhore eficiência do código

3. **Explore Recursos**
   - Use undo/redo para experimentar
   - Teste diferentes abordagens
   - Compare tempos de execução

## 🐛 Tratamento de Erros

### Erros de Sintaxe
- Exibidos em vermelho na aba "Saída"
- Mensagem detalhada do erro
- Stack trace completo

### Testes Falhando
- Mostra valor esperado vs recebido
- Indica qual teste falhou
- Tempo de execução de cada teste

### Debug com Console
```javascript
function soma(a, b) {
  console.log('Valores recebidos:', a, b); // Debug
  const resultado = a + b;
  console.log('Resultado:', resultado); // Debug
  return resultado;
}
```

## 💡 Dicas e Truques

### 1. Use Console.log para Debug
```javascript
console.log('Variável X:', x);
console.log('Tipo:', typeof x);
```

### 2. Teste Casos Extremos
- Arrays vazios
- Números negativos
- Strings vazias
- Valores undefined/null

### 3. Leia os Testes
Os testes mostram exatamente o que é esperado. Use-os como guia!

### 4. Experimente Diferentes Abordagens
Use undo/redo livremente para testar ideias diferentes.

### 5. Salve Seu Progresso
O editor salva automaticamente, mas você pode clicar em "Salvar" para garantir.

## 🎯 Exemplos de Uso

### Exemplo 1: Soma de Números
```javascript
function soma(a, b) {
  return a + b;
}

// Testes automaticamente verificam:
// soma(2, 3) === 5
// soma(10, 20) === 30
// soma(-5, 5) === 0
```

### Exemplo 2: Par ou Ímpar
```javascript
function parOuImpar(numero) {
  return numero % 2 === 0 ? 'par' : 'ímpar';
}

// Use console.log para debug:
console.log(parOuImpar(4)); // 'par'
console.log(parOuImpar(7)); // 'ímpar'
```

### Exemplo 3: Fibonacci
```javascript
function fibonacci(n) {
  console.log('Calculando fibonacci de:', n);
  
  if (n <= 1) return n;
  
  let a = 0, b = 1;
  for (let i = 2; i <= n; i++) {
    [a, b] = [b, a + b];
    console.log(`Passo ${i}: ${b}`);
  }
  
  return b;
}
```

## 📱 Responsividade

O editor funciona em:
- 💻 Desktop (melhor experiência)
- 📱 Tablet (funcional)
- 📞 Mobile (limitado)

Recomendado: Use em desktop para melhor experiência de código.

## 🔒 Segurança

- Código executado no navegador (client-side)
- Isolado em escopo local
- Sem acesso ao servidor
- Sem acesso a arquivos do sistema

## 🚧 Limitações Conhecidas

1. Apenas JavaScript é suportado (por enquanto)
2. Execução limitada ao navegador
3. Sem suporte a imports externos
4. Performance dependente do navegador

## 🎉 Conquistas

Ao completar todos os testes:
- Animação de celebração 🎉
- Mensagem de parabéns
- Taxa de sucesso 100%

## 📞 Suporte

Para reportar bugs ou sugerir melhorias, entre em contato através do sistema de feedback da plataforma.

---

**Versão:** 1.0  
**Última Atualização:** 17 de Novembro de 2025  
**Desenvolvido com:** ❤️ e muito JavaScript
