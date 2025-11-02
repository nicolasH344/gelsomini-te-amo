<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$exerciseId = (int)($_GET['id'] ?? 0);
if (!$exerciseId) {
    redirect('exercises_index.php');
}

$title = 'Exercício Colaborativo';
$user = getCurrentUser();

include 'header.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-users-cog me-2"></i>Exercício Colaborativo #<?php echo $exerciseId; ?></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>HTML</h6>
                            <textarea id="htmlEditor" class="form-control" rows="15" placeholder="Digite o HTML aqui..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <h6>CSS</h6>
                            <textarea id="cssEditor" class="form-control" rows="15" placeholder="Digite o CSS aqui..."></textarea>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h6>JavaScript</h6>
                        <textarea id="jsEditor" class="form-control" rows="8" placeholder="Digite o JavaScript aqui..."></textarea>
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <button class="btn btn-success" onclick="runCode()">
                            <i class="fas fa-play"></i> Executar
                        </button>
                        <button class="btn btn-primary" onclick="saveProgress()">
                            <i class="fas fa-save"></i> Salvar
                        </button>
                        <button class="btn btn-info" onclick="shareCode()">
                            <i class="fas fa-share"></i> Compartilhar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h6><i class="fas fa-eye me-2"></i>Preview</h6>
                </div>
                <div class="card-body p-0">
                    <iframe id="preview" style="width: 100%; height: 300px; border: none;"></iframe>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header">
                    <h6><i class="fas fa-users me-2"></i>Colaboradores Online</h6>
                </div>
                <div class="card-body" id="collaborators">
                    <div class="d-flex align-items-center mb-2">
                        <div class="status-indicator online me-2"></div>
                        <span><?php echo sanitize($user['first_name']); ?> (Você)</span>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-comments me-2"></i>Chat do Exercício</h6>
                </div>
                <div class="card-body">
                    <div id="exerciseChat" class="exercise-chat mb-3"></div>
                    <div class="input-group">
                        <input type="text" id="chatInput" class="form-control" placeholder="Comentário...">
                        <button class="btn btn-primary btn-sm" onclick="sendExerciseMessage()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.exercise-chat {
    height: 200px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.5rem;
    background-color: #f8f9fa;
}

.chat-message {
    margin-bottom: 0.5rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    background-color: white;
    font-size: 0.875rem;
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

.status-indicator.online {
    background-color: #28a745;
}

.code-editor {
    font-family: 'Courier New', monospace;
    font-size: 14px;
}
</style>

<script>
let exerciseId = <?php echo $exerciseId; ?>;
let currentUser = {
    id: <?php echo $user['id']; ?>,
    name: '<?php echo sanitize($user['first_name']); ?>'
};

function runCode() {
    const html = document.getElementById('htmlEditor').value;
    const css = document.getElementById('cssEditor').value;
    const js = document.getElementById('jsEditor').value;
    
    const code = `
        <!DOCTYPE html>
        <html>
        <head>
            <style>${css}</style>
        </head>
        <body>
            ${html}
            <script>${js}<\/script>
        </body>
        </html>
    `;
    
    const preview = document.getElementById('preview');
    preview.src = 'data:text/html;charset=utf-8,' + encodeURIComponent(code);
}

function saveProgress() {
    const data = {
        exercise_id: exerciseId,
        html: document.getElementById('htmlEditor').value,
        css: document.getElementById('cssEditor').value,
        js: document.getElementById('jsEditor').value
    };
    
    fetch('api/collaborative_save.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showAlert('Progresso salvo!', 'success');
        } else {
            showAlert('Erro ao salvar', 'danger');
        }
    });
}

function shareCode() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        showAlert('Link copiado para a área de transferência!', 'info');
    });
}

function sendExerciseMessage() {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    
    if (message) {
        fetch('api/exercise_chat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                exercise_id: exerciseId,
                message: message
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                input.value = '';
                loadExerciseChat();
            }
        });
    }
}

function loadExerciseChat() {
    fetch(`api/exercise_chat.php?exercise_id=${exerciseId}`)
        .then(response => response.json())
        .then(messages => {
            const chatContainer = document.getElementById('exerciseChat');
            chatContainer.innerHTML = '';
            
            messages.forEach(message => {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'chat-message';
                messageDiv.innerHTML = `
                    <strong>${message.username}:</strong> ${message.content}
                    <small class="text-muted d-block">${formatTime(message.created_at)}</small>
                `;
                chatContainer.appendChild(messageDiv);
            });
            
            chatContainer.scrollTop = chatContainer.scrollHeight;
        });
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => alertDiv.remove(), 3000);
}

function formatTime(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
}

// Event listeners
document.getElementById('chatInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendExerciseMessage();
    }
});

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    loadExerciseChat();
    setInterval(loadExerciseChat, 5000);
});
</script>

<?php include 'footer.php'; ?>