<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$title = 'Chat da Comunidade';
$user = getCurrentUser();

include 'header.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-users me-2"></i>Usuários Online</h5>
                </div>
                <div class="card-body" id="onlineUsers">
                    <div class="d-flex align-items-center mb-2">
                        <div class="status-indicator online me-2"></div>
                        <span><?php echo sanitize($user['first_name']); ?> (Você)</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-comments me-2"></i>Chat Geral</h5>
                    <button class="btn btn-sm btn-outline-secondary" onclick="clearChat()">
                        <i class="fas fa-trash"></i> Limpar
                    </button>
                </div>
                <div class="card-body">
                    <div id="chatMessages" class="chat-messages mb-3"></div>
                    <div class="input-group">
                        <input type="text" id="messageInput" class="form-control" placeholder="Digite sua mensagem..." maxlength="500">
                        <button class="btn btn-primary" onclick="sendMessage()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.chat-messages {
    height: 400px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    background-color: #f8f9fa;
}

.message {
    margin-bottom: 1rem;
    padding: 0.5rem;
    border-radius: 0.375rem;
    background-color: white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.message.own {
    background-color: #e3f2fd;
    margin-left: 2rem;
}

.message-header {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.message-content {
    font-size: 0.95rem;
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

.status-indicator.offline {
    background-color: #6c757d;
}
</style>

<script>
let chatSocket;
let currentUser = {
    id: <?php echo $user['id']; ?>,
    name: '<?php echo sanitize($user['first_name'] . ' ' . $user['last_name']); ?>'
};

// Simular WebSocket com polling
function initChat() {
    loadMessages();
    setInterval(loadMessages, 2000); // Atualizar a cada 2 segundos
    updateOnlineUsers();
    setInterval(updateOnlineUsers, 10000); // Atualizar usuários a cada 10 segundos
}

function loadMessages() {
    fetch('api/chat_messages.php')
        .then(response => response.json())
        .then(messages => {
            const chatContainer = document.getElementById('chatMessages');
            chatContainer.innerHTML = '';
            
            messages.forEach(message => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${message.user_id == currentUser.id ? 'own' : ''}`;
                messageDiv.innerHTML = `
                    <div class="message-header">
                        <strong>${message.username}</strong>
                        <small class="text-muted">${formatTime(message.created_at)}</small>
                    </div>
                    <div class="message-content">${message.content}</div>
                `;
                chatContainer.appendChild(messageDiv);
            });
            
            chatContainer.scrollTop = chatContainer.scrollHeight;
        })
        .catch(error => console.error('Erro ao carregar mensagens:', error));
}

function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (message) {
        fetch('api/chat_messages.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                message: message,
                user_id: currentUser.id
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                input.value = '';
                loadMessages();
            }
        })
        .catch(error => console.error('Erro ao enviar mensagem:', error));
    }
}

function updateOnlineUsers() {
    fetch('api/online_users.php')
        .then(response => response.json())
        .then(users => {
            const container = document.getElementById('onlineUsers');
            container.innerHTML = '';
            
            users.forEach(user => {
                const userDiv = document.createElement('div');
                userDiv.className = 'd-flex align-items-center mb-2';
                userDiv.innerHTML = `
                    <div class="status-indicator online me-2"></div>
                    <span>${user.name} ${user.id == currentUser.id ? '(Você)' : ''}</span>
                `;
                container.appendChild(userDiv);
            });
        })
        .catch(error => console.error('Erro ao carregar usuários online:', error));
}

function clearChat() {
    if (confirm('Tem certeza que deseja limpar o chat?')) {
        document.getElementById('chatMessages').innerHTML = '';
    }
}

function formatTime(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
}

// Event listeners
document.getElementById('messageInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
});

// Inicializar chat
document.addEventListener('DOMContentLoaded', initChat);
</script>

<?php include 'footer.php'; ?>