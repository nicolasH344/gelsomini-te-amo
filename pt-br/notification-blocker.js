// Bloquear todas as notificações do navegador
(function() {
    'use strict';
    
    // Bloquear Notification API
    if ('Notification' in window) {
        Object.defineProperty(window, 'Notification', {
            value: function() {
                console.log('Notificação do navegador bloqueada pelo sistema interno');
                return null;
            },
            writable: false,
            configurable: false
        });
    }
    
    // Bloquear Service Worker notifications
    if ('serviceWorker' in navigator) {
        const originalRegister = navigator.serviceWorker.register;
        navigator.serviceWorker.register = function() {
            console.log('Service Worker bloqueado pelo sistema interno');
            return Promise.reject('Service Workers desabilitados');
        };
    }
    
    // Bloquear Push API
    if ('PushManager' in window) {
        Object.defineProperty(window, 'PushManager', {
            value: undefined,
            writable: false,
            configurable: false
        });
    }
    
    // Interceptar tentativas de requestPermission
    if (navigator.permissions) {
        const originalQuery = navigator.permissions.query;
        navigator.permissions.query = function(permissionDesc) {
            if (permissionDesc.name === 'notifications' || permissionDesc.name === 'push') {
                return Promise.resolve({ state: 'denied' });
            }
            return originalQuery.call(this, permissionDesc);
        };
    }
    
    // Bloquear Web Push
    if ('webkitNotifications' in window) {
        delete window.webkitNotifications;
    }
    
    console.log('Sistema de notificações interno ativo - notificações externas bloqueadas');
})();