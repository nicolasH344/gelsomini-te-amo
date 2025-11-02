<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$title = 'Sistema de Mentoria';
$user = getCurrentUser();

include 'header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-user-graduate me-2"></i>Encontrar Mentor</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="skillFilter" class="form-label">Área de Interesse</label>
                            <select class="form-select" id="skillFilter">
                                <option value="">Todas as áreas</option>
                                <option value="html">HTML</option>
                                <option value="css">CSS</option>
                                <option value="javascript">JavaScript</option>
                                <option value="php">PHP</option>
                                <option value="react">React</option>
                                <option value="nodejs">Node.js</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="levelFilter" class="form-label">Nível</label>
                            <select class="form-select" id="levelFilter">
                                <option value="">Todos os níveis</option>
                                <option value="junior">Júnior</option>
                                <option value="pleno">Pleno</option>
                                <option value="senior">Sênior</option>
                            </select>
                        </div>
                    </div>
                    
                    <div id="mentorsList">
                        <!-- Mentores serão carregados aqui -->
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-chalkboard-teacher me-2"></i>Tornar-se Mentor</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Compartilhe seu conhecimento e ajude outros desenvolvedores em sua jornada.</p>
                    
                    <form id="mentorForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expertise" class="form-label">Áreas de Expertise</label>
                                    <select class="form-select" id="expertise" multiple>
                                        <option value="html">HTML</option>
                                        <option value="css">CSS</option>
                                        <option value="javascript">JavaScript</option>
                                        <option value="php">PHP</option>
                                        <option value="react">React</option>
                                        <option value="nodejs">Node.js</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="experience" class="form-label">Anos de Experiência</label>
                                    <select class="form-select" id="experience">
                                        <option value="1-2">1-2 anos</option>
                                        <option value="3-5">3-5 anos</option>
                                        <option value="5+">5+ anos</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bio" class="form-label">Biografia</label>
                            <textarea class="form-control" id="bio" rows="4" placeholder="Conte sobre sua experiência e como pode ajudar outros desenvolvedores..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="availability" class="form-label">Disponibilidade</label>
                            <select class="form-select" id="availability">
                                <option value="weekdays">Dias de semana</option>
                                <option value="weekends">Fins de semana</option>
                                <option value="flexible">Flexível</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-user-plus me-2"></i>Tornar-se Mentor
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-calendar-alt me-2"></i>Minhas Sessões</h6>
                </div>
                <div class="card-body" id="mySessions">
                    <p class="text-muted">Nenhuma sessão agendada.</p>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="fas fa-star me-2"></i>Top Mentores</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="https://via.placeholder.com/40" alt="Mentor" class="rounded-circle me-3">
                        <div>
                            <h6 class="mb-0">Ana Silva</h6>
                            <small class="text-muted">React • 5+ anos</small>
                            <div>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <small class="text-muted">(4.9)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <img src="https://via.placeholder.com/40" alt="Mentor" class="rounded-circle me-3">
                        <div>
                            <h6 class="mb-0">Carlos Santos</h6>
                            <small class="text-muted">JavaScript • 3+ anos</small>
                            <div>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star-half-alt text-warning"></i>
                                <small class="text-muted">(4.7)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="fas fa-info-circle me-2"></i>Como Funciona</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex mb-2">
                        <div class="badge bg-primary rounded-circle me-3" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">1</div>
                        <small>Encontre um mentor na sua área</small>
                    </div>
                    <div class="d-flex mb-2">
                        <div class="badge bg-primary rounded-circle me-3" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">2</div>
                        <small>Agende uma sessão</small>
                    </div>
                    <div class="d-flex mb-2">
                        <div class="badge bg-primary rounded-circle me-3" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">3</div>
                        <small>Participe da videochamada</small>
                    </div>
                    <div class="d-flex">
                        <div class="badge bg-primary rounded-circle me-3" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">4</div>
                        <small>Avalie a experiência</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const mentors = [
    {
        id: 1,
        name: 'João Oliveira',
        avatar: 'https://via.placeholder.com/60',
        skills: ['JavaScript', 'React', 'Node.js'],
        experience: '5+ anos',
        rating: 4.8,
        reviews: 23,
        bio: 'Desenvolvedor Full Stack com experiência em startups e grandes empresas.',
        availability: 'Flexível',
        price: 'Gratuito'
    },
    {
        id: 2,
        name: 'Maria Costa',
        avatar: 'https://via.placeholder.com/60',
        skills: ['HTML', 'CSS', 'JavaScript'],
        experience: '3-5 anos',
        rating: 4.9,
        reviews: 31,
        bio: 'Front-end Developer apaixonada por UI/UX e acessibilidade.',
        availability: 'Fins de semana',
        price: 'Gratuito'
    },
    {
        id: 3,
        name: 'Pedro Silva',
        avatar: 'https://via.placeholder.com/60',
        skills: ['PHP', 'MySQL', 'Laravel'],
        experience: '5+ anos',
        rating: 4.7,
        reviews: 18,
        bio: 'Backend Developer especializado em PHP e arquitetura de sistemas.',
        availability: 'Dias de semana',
        price: 'Gratuito'
    }
];

function loadMentors() {
    const mentorsList = document.getElementById('mentorsList');
    mentorsList.innerHTML = '';
    
    mentors.forEach(mentor => {
        const mentorCard = document.createElement('div');
        mentorCard.className = 'card mb-3';
        mentorCard.innerHTML = `
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <img src="${mentor.avatar}" alt="${mentor.name}" class="rounded-circle" width="60" height="60">
                    </div>
                    <div class="col-md-7">
                        <h6 class="mb-1">${mentor.name}</h6>
                        <div class="mb-2">
                            ${mentor.skills.map(skill => `<span class="badge bg-secondary me-1">${skill}</span>`).join('')}
                        </div>
                        <div class="mb-2">
                            ${Array(5).fill().map((_, i) => 
                                `<i class="fas fa-star ${i < Math.floor(mentor.rating) ? 'text-warning' : 'text-muted'}"></i>`
                            ).join('')}
                            <small class="text-muted ms-1">(${mentor.rating}) • ${mentor.reviews} avaliações</small>
                        </div>
                        <p class="text-muted small mb-0">${mentor.bio}</p>
                    </div>
                    <div class="col-md-3 text-end">
                        <div class="mb-2">
                            <small class="text-muted">${mentor.experience}</small><br>
                            <small class="text-success fw-bold">${mentor.price}</small>
                        </div>
                        <button class="btn btn-primary btn-sm" onclick="requestMentorship(${mentor.id})">
                            <i class="fas fa-calendar-plus me-1"></i>Agendar
                        </button>
                    </div>
                </div>
            </div>
        `;
        mentorsList.appendChild(mentorCard);
    });
}

function requestMentorship(mentorId) {
    const mentor = mentors.find(m => m.id === mentorId);
    
    if (confirm(`Deseja agendar uma sessão com ${mentor.name}?`)) {
        showAlert(`Solicitação enviada para ${mentor.name}!`, 'success');
        
        // Simular agendamento
        setTimeout(() => {
            const sessionsDiv = document.getElementById('mySessions');
            sessionsDiv.innerHTML = `
                <div class="border rounded p-3 mb-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">Sessão com ${mentor.name}</h6>
                            <small class="text-muted">Amanhã às 14:00</small>
                        </div>
                        <span class="badge bg-warning">Pendente</span>
                    </div>
                </div>
            `;
        }, 1000);
    }
}

document.getElementById('mentorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const expertise = Array.from(document.getElementById('expertise').selectedOptions).map(option => option.value);
    const experience = document.getElementById('experience').value;
    const bio = document.getElementById('bio').value;
    const availability = document.getElementById('availability').value;
    
    if (expertise.length === 0 || !bio.trim()) {
        showAlert('Preencha todos os campos obrigatórios', 'warning');
        return;
    }
    
    showAlert('Solicitação para se tornar mentor enviada!', 'success');
    
    // Reset form
    this.reset();
});

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => alertDiv.remove(), 4000);
}

// Inicializar
document.addEventListener('DOMContentLoaded', loadMentors);
</script>

<?php include 'footer.php'; ?>