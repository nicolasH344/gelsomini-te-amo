// Dados dos exercícios
const basicExercises = [
    {
        id: 1,
        title: 'Estrutura Básica HTML',
        description: 'Aprenda a criar a estrutura básica de uma página HTML',
        difficulty: 'Iniciante',
        category: 'HTML',
        completed: false
    },
    {
        id: 2,
        title: 'Estilização com CSS',
        description: 'Pratique estilização básica com CSS',
        difficulty: 'Iniciante',
        category: 'CSS',
        completed: false
    },
    {
        id: 3,
        title: 'Interatividade com JavaScript',
        description: 'Adicione interatividade às suas páginas',
        difficulty: 'Intermediário',
        category: 'JavaScript',
        completed: false
    },
    {
        id: 4,
        title: 'Formulários HTML',
        description: 'Crie formulários funcionais e acessíveis',
        difficulty: 'Iniciante',
        category: 'HTML',
        completed: false
    },
    {
        id: 5,
        title: 'Layout Responsivo',
        description: 'Desenvolva layouts que se adaptam a diferentes telas',
        difficulty: 'Intermediário',
        category: 'CSS',
        completed: false
    },
    {
        id: 6,
        title: 'Manipulação do DOM',
        description: 'Aprenda a manipular elementos da página dinamicamente',
        difficulty: 'Intermediário',
        category: 'JavaScript',
        completed: false
    }
];

const advancedExercises = [
    {
        id: 1,
        title: 'Algoritmos de Ordenação Avançados',
        description: 'Implemente algoritmos como QuickSort, MergeSort e HeapSort em Python',
        difficulty: 'Avançado',
        category: 'Python',
        completed: false,
        estimated_time: '2-3 horas',
        topics: ['Algoritmos', 'Estruturas de Dados', 'Complexidade']
    },
    {
        id: 2,
        title: 'Sistema de Gerenciamento de Memória',
        description: 'Desenvolva um sistema de alocação de memória personalizado em C++',
        difficulty: 'Avançado',
        category: 'C++',
        completed: false,
        estimated_time: '4-5 horas',
        topics: ['Ponteiros', 'Gerenciamento de Memória', 'Performance']
    },
    {
        id: 3,
        title: 'API RESTful com Spring Boot',
        description: 'Crie uma API completa com autenticação JWT e documentação Swagger',
        difficulty: 'Avançado',
        category: 'Java',
        completed: false,
        estimated_time: '3-4 horas',
        topics: ['Spring Boot', 'REST API', 'JWT', 'Swagger']
    },
    {
        id: 4,
        title: 'Machine Learning com TensorFlow',
        description: 'Implemente uma rede neural para classificação de imagens',
        difficulty: 'Avançado',
        category: 'Python',
        completed: false,
        estimated_time: '5-6 horas',
        topics: ['Machine Learning', 'TensorFlow', 'Redes Neurais']
    },
    {
        id: 5,
        title: 'Aplicação React com Redux',
        description: 'Desenvolva uma SPA complexa com gerenciamento de estado avançado',
        difficulty: 'Avançado',
        category: 'JavaScript',
        completed: false,
        estimated_time: '4-5 horas',
        topics: ['React', 'Redux', 'SPA', 'Estado Global']
    },
    {
        id: 6,
        title: 'Microserviços com Docker',
        description: 'Arquitetura de microserviços usando containers Docker e Kubernetes',
        difficulty: 'Avançado',
        category: 'DevOps',
        completed: false,
        estimated_time: '6-8 horas',
        topics: ['Docker', 'Kubernetes', 'Microserviços', 'Orquestração']
    },
    {
        id: 7,
        title: 'Compilador de Linguagem Simples',
        description: 'Construa um compilador básico para uma linguagem de programação simples',
        difficulty: 'Avançado',
        category: 'C++',
        completed: false,
        estimated_time: '8-10 horas',
        topics: ['Compiladores', 'Análise Léxica', 'Parsing', 'AST']
    },
    {
        id: 8,
        title: 'Sistema Distribuído com Go',
        description: 'Implemente um sistema distribuído usando Go e gRPC',
        difficulty: 'Avançado',
        category: 'Go',
        completed: false,
        estimated_time: '5-6 horas',
        topics: ['Go', 'gRPC', 'Sistemas Distribuídos', 'Concorrência']
    },
    {
        id: 9,
        title: 'Blockchain e Smart Contracts',
        description: 'Desenvolva uma blockchain simples e smart contracts em Solidity',
        difficulty: 'Avançado',
        category: 'Solidity',
        completed: false,
        estimated_time: '6-7 horas',
        topics: ['Blockchain', 'Smart Contracts', 'Ethereum', 'Web3']
    },
    {
        id: 10,
        title: 'Análise de Big Data com Spark',
        description: 'Processe grandes volumes de dados usando Apache Spark e Scala',
        difficulty: 'Avançado',
        category: 'Scala',
        completed: false,
        estimated_time: '4-5 horas',
        topics: ['Big Data', 'Apache Spark', 'Scala', 'Processamento Distribuído']
    },
    {
        id: 11,
        title: 'Game Engine em C++',
        description: 'Desenvolva um motor de jogos 2D básico com OpenGL',
        difficulty: 'Avançado',
        category: 'C++',
        completed: false,
        estimated_time: '10-12 horas',
        topics: ['Game Development', 'OpenGL', 'Graphics Programming', 'Engine Architecture']
    },
    {
        id: 12,
        title: 'Sistema de Recomendação',
        description: 'Implemente um sistema de recomendação usando algoritmos de filtragem colaborativa',
        difficulty: 'Avançado',
        category: 'Python',
        completed: false,
        estimated_time: '4-5 horas',
        topics: ['Machine Learning', 'Sistemas de Recomendação', 'Filtragem Colaborativa']
    }
];

// Função para obter cor do badge baseada na categoria
function getCategoryBadgeClass(category) {
    const colorMap = {
        'HTML': 'bg-danger',
        'CSS': 'bg-primary',
        'JavaScript': 'bg-warning',
        'PHP': 'bg-info',
        'Python': 'bg-success',
        'Java': 'bg-danger',
        'C++': 'bg-primary',
        'Go': 'bg-info',
        'Scala': 'bg-secondary',
        'Solidity': 'bg-dark',
        'DevOps': 'bg-light text-dark'
    };
    return colorMap[category] || 'bg-secondary';
}

// Função para obter cor do badge baseada na dificuldade
function getDifficultyBadgeClass(difficulty) {
    const colorMap = {
        'Iniciante': 'bg-success',
        'Intermediário': 'bg-warning',
        'Avançado': 'bg-danger'
    };
    return colorMap[difficulty] || 'bg-secondary';
}

// Função para renderizar exercícios básicos
function renderBasicExercises(exercises = basicExercises) {
    const container = document.getElementById('basicExercisesList');
    if (!container) return;

    container.innerHTML = exercises.map(exercise => `
        <div class="col-md-6 col-lg-4 mb-4 exercise-card" 
             data-category="${exercise.category}" 
             data-difficulty="${exercise.difficulty}">
            <div class="card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="badge ${getCategoryBadgeClass(exercise.category)}">
                        ${exercise.category}
                    </span>
                    <span class="badge ${getDifficultyBadgeClass(exercise.difficulty)}">
                        ${exercise.difficulty}
                    </span>
                </div>
                
                <div class="card-body">
                    <h3 class="card-title h5">${exercise.title}</h3>
                    <p class="card-text">${exercise.description}</p>
                    
                    ${exercise.completed ? `
                        <div class="alert alert-success py-2" role="alert">
                            <i class="fas fa-check-circle me-1"></i>
                            <small>Concluído</small>
                        </div>
                    ` : ''}
                </div>
                
                <div class="card-footer bg-transparent">
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm flex-fill" onclick="startExercise('basic', ${exercise.id})">
                            <i class="fas fa-play"></i> 
                            ${exercise.completed ? 'Revisar' : 'Começar'}
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="previewExercise('basic', ${exercise.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// Função para renderizar exercícios avançados
function renderAdvancedExercises(exercises = advancedExercises) {
    const container = document.getElementById('advancedExercisesList');
    if (!container) return;

    container.innerHTML = exercises.map(exercise => `
        <div class="col-md-6 col-lg-4 mb-4 exercise-card" 
             data-category="${exercise.category}" 
             data-difficulty="${exercise.difficulty}">
            <div class="card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="badge ${getCategoryBadgeClass(exercise.category)}">
                        ${exercise.category}
                    </span>
                    <span class="badge ${getDifficultyBadgeClass(exercise.difficulty)}">
                        ${exercise.difficulty}
                    </span>
                </div>
                
                <div class="card-body">
                    <h3 class="card-title h5">${exercise.title}</h3>
                    <p class="card-text">${exercise.description}</p>
                    
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Tempo estimado: ${exercise.estimated_time}
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        ${exercise.topics.map(topic => `
                            <span class="badge bg-light text-dark me-1 mb-1">${topic}</span>
                        `).join('')}
                    </div>
                    
                    ${exercise.completed ? `
                        <div class="alert alert-success py-2" role="alert">
                            <i class="fas fa-check-circle me-1"></i>
                            <small>Concluído</small>
                        </div>
                    ` : ''}
                </div>
                
                <div class="card-footer bg-transparent">
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm flex-fill" onclick="startExercise('advanced', ${exercise.id})">
                            <i class="fas fa-play"></i> 
                            ${exercise.completed ? 'Revisar' : 'Começar'}
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="previewExercise('advanced', ${exercise.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="showExerciseDetails('advanced', ${exercise.id})">
                            <i class="fas fa-info-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// Função para filtrar exercícios básicos
function filterBasicExercises() {
    const category = document.getElementById('basicCategory')?.value || '';
    const difficulty = document.getElementById('basicDifficulty')?.value || '';
    const search = document.getElementById('basicSearch')?.value.toLowerCase() || '';
    
    const filtered = basicExercises.filter(exercise => {
        const matchesCategory = !category || exercise.category === category;
        const matchesDifficulty = !difficulty || exercise.difficulty === difficulty;
        const matchesSearch = !search || 
            exercise.title.toLowerCase().includes(search) || 
            exercise.description.toLowerCase().includes(search);
        
        return matchesCategory && matchesDifficulty && matchesSearch;
    });
    
    renderBasicExercises(filtered);
}

// Função para filtrar exercícios avançados
function filterAdvancedExercises() {
    const category = document.getElementById('advancedCategory')?.value || '';
    const difficulty = document.getElementById('advancedDifficulty')?.value || '';
    const search = document.getElementById('advancedSearch')?.value.toLowerCase() || '';
    
    const filtered = advancedExercises.filter(exercise => {
        const matchesCategory = !category || exercise.category === category;
        const matchesDifficulty = !difficulty || exercise.difficulty === difficulty;
        const matchesSearch = !search || 
            exercise.title.toLowerCase().includes(search) || 
            exercise.description.toLowerCase().includes(search);
        
        return matchesCategory && matchesDifficulty && matchesSearch;
    });
    
    renderAdvancedExercises(filtered);
}

// Função para iniciar exercício
function startExercise(type, id) {
    alert(`Iniciando exercício ${type} #${id}. Em um projeto real, isso redirecionaria para a página do exercício.`);
}

// Função para visualizar exercício
function previewExercise(type, id) {
    alert(`Visualizando exercício ${type} #${id}. Em um projeto real, isso abriria uma prévia do exercício.`);
}

// Função para mostrar detalhes do exercício
function showExerciseDetails(type, exerciseId) {
    const exercises = type === 'advanced' ? advancedExercises : basicExercises;
    const exercise = exercises.find(ex => ex.id === exerciseId);
    
    if (exercise) {
        const content = `
            <div class="row">
                <div class="col-md-8">
                    <h4>${exercise.title}</h4>
                    <p class="lead">${exercise.description}</p>
                    
                    ${exercise.topics ? `
                        <h6>Tópicos Abordados:</h6>
                        <div class="mb-3">
                            ${exercise.topics.map(topic => `<span class="badge bg-primary me-1">${topic}</span>`).join('')}
                        </div>
                    ` : ''}
                    
                    <h6>Objetivos de Aprendizado:</h6>
                    <ul>
                        <li>Aplicar conceitos ${type === 'advanced' ? 'avançados' : 'básicos'} de ${exercise.category}</li>
                        <li>Resolver problemas ${type === 'advanced' ? 'complexos' : 'fundamentais'} de programação</li>
                        <li>Implementar soluções eficientes e escaláveis</li>
                        <li>Seguir boas práticas de desenvolvimento</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Informações</h6>
                            <p><strong>Linguagem:</strong> ${exercise.category}</p>
                            <p><strong>Dificuldade:</strong> ${exercise.difficulty}</p>
                            ${exercise.estimated_time ? `<p><strong>Tempo estimado:</strong> ${exercise.estimated_time}</p>` : ''}
                            <p><strong>Status:</strong> ${exercise.completed ? 'Concluído' : 'Não iniciado'}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('exerciseDetailsContent').innerHTML = content;
        document.getElementById('startExerciseBtn').onclick = () => {
            startExercise(type, exerciseId);
            bootstrap.Modal.getInstance(document.getElementById('exerciseDetailsModal')).hide();
        };
        
        new bootstrap.Modal(document.getElementById('exerciseDetailsModal')).show();
    }
}

// Gerenciamento de temas
function setTheme(theme) {
    document.body.className = document.body.className.replace(/theme-\w+/g, '');
    if (theme !== 'purple') {
        document.body.classList.add('theme-' + theme);
    }
    
    // Atualizar indicador visual
    document.querySelectorAll('.theme-option').forEach(option => {
        option.classList.remove('active');
    });
    document.querySelector('.theme-option.theme-' + theme).classList.add('active');
    
    // Salvar preferência
    localStorage.setItem('selectedTheme', theme);
}

// Toggle do seletor de tema
function toggleThemeSelector() {
    const selector = document.getElementById('themeSelector');
    const overlay = document.getElementById('settingsOverlay');
    
    if (selector.classList.contains('collapsed')) {
        selector.classList.remove('collapsed');
        overlay.classList.add('show');
    } else {
        selector.classList.add('collapsed');
        overlay.classList.remove('show');
    }
}

function closeThemeSelector() {
    document.getElementById('themeSelector').classList.add('collapsed');
    document.getElementById('settingsOverlay').classList.remove('show');
}

// Toggle do modo acessibilidade
function toggleAccessibility() {
    document.body.classList.toggle('accessibility-mode');
    localStorage.setItem('accessibilityMode', document.body.classList.contains('accessibility-mode'));
}

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    // Carregar preferências salvas
    const savedTheme = localStorage.getItem('selectedTheme') || 'purple';
    const accessibilityMode = localStorage.getItem('accessibilityMode') === 'true';
    
    setTheme(savedTheme);
    
    if (accessibilityMode) {
        document.body.classList.add('accessibility-mode');
        const checkbox = document.getElementById('accessibilityMode');
        if (checkbox) checkbox.checked = true;
    }
    
    // Renderizar exercícios
    renderBasicExercises();
    renderAdvancedExercises();
    
    // Adicionar event listeners para filtros
    const basicCategoryFilter = document.getElementById('basicCategory');
    const basicDifficultyFilter = document.getElementById('basicDifficulty');
    const basicSearchFilter = document.getElementById('basicSearch');
    
    if (basicCategoryFilter) basicCategoryFilter.addEventListener('change', filterBasicExercises);
    if (basicDifficultyFilter) basicDifficultyFilter.addEventListener('change', filterBasicExercises);
    if (basicSearchFilter) basicSearchFilter.addEventListener('input', filterBasicExercises);
    
    const advancedCategoryFilter = document.getElementById('advancedCategory');
    const advancedDifficultyFilter = document.getElementById('advancedDifficulty');
    const advancedSearchFilter = document.getElementById('advancedSearch');
    
    if (advancedCategoryFilter) advancedCategoryFilter.addEventListener('change', filterAdvancedExercises);
    if (advancedDifficultyFilter) advancedDifficultyFilter.addEventListener('change', filterAdvancedExercises);
    if (advancedSearchFilter) advancedSearchFilter.addEventListener('input', filterAdvancedExercises);
    
    // Smooth scrolling para links de navegação
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});