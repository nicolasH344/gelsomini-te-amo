<?php
require_once 'notifications.php';

if (!isLoggedIn()) return;

$user_id = getCurrentUser()['id'];
$unread_count = getUnreadCount($user_id);
?>

<div class="notification-widget">
    <button class="btn btn-outline-light position-relative" id="notificationBtn" data-bs-toggle="dropdown">
        <i class="fas fa-bell"></i>
        <?php if ($unread_count > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?php echo $unread_count > 9 ? '9+' : $unread_count; ?>
            </span>
        <?php endif; ?>
    </button>
    
    <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
        <div class="dropdown-header d-flex justify-content-between">
            <span>Notificações</span>
            <small class="text-muted" id="unreadCount"><?php echo $unread_count; ?> não lidas</small>
        </div>
        
        <div id="notificationList">
            <div class="text-center p-3">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
            </div>
        </div>
        
        <div class="dropdown-divider"></div>
        <div class="dropdown-item text-center">
            <small class="text-muted">Sistema de notificações interno</small>
        </div>
    </div>
</div>

<style>
.notification-widget .dropdown-menu {
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.notification-item {
    padding: 12px 16px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background-color 0.2s;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #e3f2fd;
    border-left: 3px solid #2196f3;
}

.notification-item .notification-title {
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 4px;
}

.notification-item .notification-message {
    font-size: 0.8rem;
    color: #666;
    margin-bottom: 4px;
}

.notification-item .notification-time {
    font-size: 0.75rem;
    color: #999;
}

.notification-type-success { border-left-color: #4caf50 !important; }
.notification-type-warning { border-left-color: #ff9800 !important; }
.notification-type-error { border-left-color: #f44336 !important; }
</style>

<script>
// Garantir que apenas nosso sistema de notificações funcione
if ('Notification' in window) {
    Notification.requestPermission = function() {
        return Promise.resolve('denied');
    };
}

document.addEventListener('DOMContentLoaded', function() {
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationList = document.getElementById('notificationList');
    const unreadCount = document.getElementById('unreadCount');
    
    // Carregar notificações quando abrir dropdown
    notificationBtn.addEventListener('click', loadNotifications);
    
    function loadNotifications() {
        fetch('notifications.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayNotifications(data.notifications);
            }
        })
        .catch(error => {
            notificationList.innerHTML = '<div class="text-center p-3 text-muted">Erro ao carregar</div>';
        });
    }
    
    function displayNotifications(notifications) {
        if (notifications.length === 0) {
            notificationList.innerHTML = '<div class="text-center p-3 text-muted">Nenhuma notificação</div>';
            return;
        }
        
        const html = notifications.map(notification => {
            const isUnread = notification.is_read == 0;
            const timeAgo = formatTimeAgo(notification.created_at);
            
            return `
                <div class="notification-item ${isUnread ? 'unread' : ''} notification-type-${notification.type}" 
                     data-id="${notification.id}" onclick="markAsRead(${notification.id})">
                    <div class="notification-title">${notification.title}</div>
                    <div class="notification-message">${notification.message}</div>
                    <div class="notification-time">${timeAgo}</div>
                </div>
            `;
        }).join('');
        
        notificationList.innerHTML = html;
    }
    
    window.markAsRead = function(id) {
        fetch('notifications.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=mark_read&id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = document.querySelector(`[data-id="${id}"]`);
                if (item) {
                    item.classList.remove('unread');
                    updateUnreadCount();
                }
            }
        });
    }
    
    function updateUnreadCount() {
        const unreadItems = document.querySelectorAll('.notification-item.unread').length;
        unreadCount.textContent = `${unreadItems} não lidas`;
        
        const badge = notificationBtn.querySelector('.badge');
        if (unreadItems === 0) {
            if (badge) badge.remove();
        } else {
            if (!badge) {
                const newBadge = document.createElement('span');
                newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                notificationBtn.appendChild(newBadge);
            }
            notificationBtn.querySelector('.badge').textContent = unreadItems > 9 ? '9+' : unreadItems;
        }
    }
    
    function formatTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        const minutes = Math.floor(diff / 60000);
        
        if (minutes < 1) return 'Agora';
        if (minutes < 60) return `${minutes}m atrás`;
        
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `${hours}h atrás`;
        
        const days = Math.floor(hours / 24);
        return `${days}d atrás`;
    }
});
</script>