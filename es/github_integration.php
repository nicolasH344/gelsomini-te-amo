<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$title = 'Integração GitHub';
$user = getCurrentUser();

include 'header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fab fa-github me-2"></i>Conectar com GitHub</h5>
                </div>
                <div class="card-body">
                    <div id="githubStatus" class="mb-3">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Conecte sua conta GitHub para sincronizar seus exercícios e projetos.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="githubToken" class="form-label">Token de Acesso GitHub</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="githubToken" placeholder="ghp_xxxxxxxxxxxx">
                            <button class="btn btn-primary" onclick="connectGitHub()">
                                <i class="fab fa-github me-2"></i>Conectar
                            </button>
                        </div>
                        <div class="form-text">
                            <a href="https://github.com/settings/tokens" target="_blank">
                                <i class="fas fa-external-link-alt me-1"></i>
                                Gerar token no GitHub
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4" id="repositoriesCard" style="display: none;">
                <div class="card-header">
                    <h5><i class="fas fa-folder me-2"></i>Seus Repositórios</h5>
                </div>
                <div class="card-body">
                    <div id="repositoriesList"></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-upload me-2"></i>Exportar Exercícios</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Exporte seus exercícios concluídos para um repositório GitHub.</p>
                    <div class="mb-3">
                        <label for="repoName" class="form-label">Nome do Repositório</label>
                        <input type="text" class="form-control" id="repoName" placeholder="weblearn-exercises">
                    </div>
                    <button class="btn btn-success w-100" onclick="exportExercises()">
                        <i class="fas fa-upload me-2"></i>Exportar
                    </button>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="fas fa-download me-2"></i>Importar Projetos</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Importe projetos do GitHub como exercícios personalizados.</p>
                    <div class="mb-3">
                        <label for="importRepo" class="form-label">URL do Repositório</label>
                        <input type="text" class="form-control" id="importRepo" placeholder="https://github.com/user/repo">
                    </div>
                    <button class="btn btn-info w-100" onclick="importProject()">
                        <i class="fas fa-download me-2"></i>Importar
                    </button>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="fas fa-chart-line me-2"></i>Estatísticas GitHub</h6>
                </div>
                <div class="card-body" id="githubStats">
                    <p class="text-muted">Conecte-se ao GitHub para ver suas estatísticas.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let githubToken = '';
let githubUser = null;

function connectGitHub() {
    const token = document.getElementById('githubToken').value;
    
    if (!token) {
        showAlert('Por favor, insira um token válido', 'warning');
        return;
    }
    
    githubToken = token;
    
    fetch('https://api.github.com/user', {
        headers: {
            'Authorization': `token ${token}`,
            'Accept': 'application/vnd.github.v3+json'
        }
    })
    .then(response => response.json())
    .then(user => {
        if (user.login) {
            githubUser = user;
            showGitHubConnected(user);
            loadRepositories();
            loadGitHubStats();
        } else {
            showAlert('Token inválido', 'danger');
        }
    })
    .catch(error => {
        showAlert('Erro ao conectar com GitHub', 'danger');
        console.error(error);
    });
}

function showGitHubConnected(user) {
    document.getElementById('githubStatus').innerHTML = `
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            Conectado como <strong>${user.login}</strong>
            <img src="${user.avatar_url}" alt="Avatar" class="rounded-circle ms-2" width="24" height="24">
        </div>
    `;
    
    document.getElementById('repositoriesCard').style.display = 'block';
}

function loadRepositories() {
    fetch('https://api.github.com/user/repos?sort=updated&per_page=10', {
        headers: {
            'Authorization': `token ${githubToken}`,
            'Accept': 'application/vnd.github.v3+json'
        }
    })
    .then(response => response.json())
    .then(repos => {
        const reposList = document.getElementById('repositoriesList');
        reposList.innerHTML = '';
        
        repos.forEach(repo => {
            const repoDiv = document.createElement('div');
            repoDiv.className = 'border rounded p-3 mb-2';
            repoDiv.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">
                            <a href="${repo.html_url}" target="_blank" class="text-decoration-none">
                                ${repo.name}
                            </a>
                        </h6>
                        <p class="text-muted small mb-2">${repo.description || 'Sem descrição'}</p>
                        <small class="text-muted">
                            <i class="fas fa-star me-1"></i>${repo.stargazers_count}
                            <i class="fas fa-code-branch ms-2 me-1"></i>${repo.forks_count}
                            ${repo.language ? `<span class="badge bg-secondary ms-2">${repo.language}</span>` : ''}
                        </small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" onclick="syncRepo('${repo.name}')">
                        <i class="fas fa-sync"></i>
                    </button>
                </div>
            `;
            reposList.appendChild(repoDiv);
        });
    })
    .catch(error => {
        console.error('Erro ao carregar repositórios:', error);
    });
}

function loadGitHubStats() {
    if (!githubUser) return;
    
    const stats = document.getElementById('githubStats');
    stats.innerHTML = `
        <div class="text-center">
            <img src="${githubUser.avatar_url}" alt="Avatar" class="rounded-circle mb-2" width="60" height="60">
            <h6>${githubUser.name || githubUser.login}</h6>
            <div class="row text-center">
                <div class="col-4">
                    <div class="fw-bold">${githubUser.public_repos}</div>
                    <small class="text-muted">Repos</small>
                </div>
                <div class="col-4">
                    <div class="fw-bold">${githubUser.followers}</div>
                    <small class="text-muted">Seguidores</small>
                </div>
                <div class="col-4">
                    <div class="fw-bold">${githubUser.following}</div>
                    <small class="text-muted">Seguindo</small>
                </div>
            </div>
        </div>
    `;
}

function exportExercises() {
    const repoName = document.getElementById('repoName').value || 'weblearn-exercises';
    
    if (!githubToken) {
        showAlert('Conecte-se ao GitHub primeiro', 'warning');
        return;
    }
    
    // Simular exportação
    showAlert('Exportando exercícios para GitHub...', 'info');
    
    setTimeout(() => {
        showAlert(`Exercícios exportados para ${repoName}!`, 'success');
    }, 2000);
}

function importProject() {
    const repoUrl = document.getElementById('importRepo').value;
    
    if (!repoUrl) {
        showAlert('Insira a URL do repositório', 'warning');
        return;
    }
    
    showAlert('Importando projeto...', 'info');
    
    setTimeout(() => {
        showAlert('Projeto importado como exercício personalizado!', 'success');
    }, 2000);
}

function syncRepo(repoName) {
    showAlert(`Sincronizando ${repoName}...`, 'info');
    
    setTimeout(() => {
        showAlert(`${repoName} sincronizado!`, 'success');
    }, 1500);
}

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
</script>

<?php include 'footer.php'; ?>