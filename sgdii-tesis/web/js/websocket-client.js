/**
 * WebSocket Client for real-time updates in SGDII Tesis
 * Handles connection, reconnection, and message handling
 */

class SGDIIWebSocket {
    constructor(url, options = {}) {
        this.url = url;
        this.ws = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = options.maxReconnectAttempts || 5;
        this.reconnectDelay = options.reconnectDelay || 3000;
        this.heartbeatInterval = options.heartbeatInterval || 30000;
        this.heartbeatTimer = null;
        this.handlers = {};
        this.connected = false;
        
        this.connect();
    }
    
    /**
     * Establish WebSocket connection
     */
    connect() {
        try {
            this.ws = new WebSocket(this.url);
            
            this.ws.onopen = () => this.onOpen();
            this.ws.onmessage = (event) => this.onMessage(event);
            this.ws.onerror = (error) => this.onError(error);
            this.ws.onclose = () => this.onClose();
            
        } catch (error) {
            console.error('WebSocket connection error:', error);
            this.reconnect();
        }
    }
    
    /**
     * Handle connection open
     */
    onOpen() {
        console.log('✓ WebSocket connected');
        this.connected = true;
        this.reconnectAttempts = 0;
        
        // Start heartbeat
        this.startHeartbeat();
        
        // Subscribe to default channel
        this.send({
            type: 'subscribe',
            channel: 'thesis_updates'
        });
        
        // Trigger connected event
        this.trigger('connected');
    }
    
    /**
     * Handle incoming message
     */
    onMessage(event) {
        try {
            const message = JSON.parse(event.data);
            console.log('WebSocket message received:', message);
            
            // Handle different message types
            switch (message.type) {
                case 'thesis_update':
                    this.trigger('thesis_update', message);
                    this.handleThesisUpdate(message);
                    break;
                    
                case 'notification':
                    this.trigger('notification', message);
                    this.handleNotification(message);
                    break;
                    
                case 'pong':
                    // Heartbeat response
                    break;
                    
                default:
                    console.log('Unknown message type:', message.type);
            }
        } catch (error) {
            console.error('Error parsing WebSocket message:', error);
        }
    }
    
    /**
     * Handle connection error
     */
    onError(error) {
        console.error('WebSocket error:', error);
        this.connected = false;
    }
    
    /**
     * Handle connection close
     */
    onClose() {
        console.log('WebSocket disconnected');
        this.connected = false;
        this.stopHeartbeat();
        
        // Trigger disconnected event
        this.trigger('disconnected');
        
        // Attempt to reconnect
        this.reconnect();
    }
    
    /**
     * Attempt to reconnect
     */
    reconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            console.error('Max reconnection attempts reached');
            return;
        }
        
        this.reconnectAttempts++;
        console.log(`Reconnecting... (attempt ${this.reconnectAttempts}/${this.maxReconnectAttempts})`);
        
        setTimeout(() => {
            this.connect();
        }, this.reconnectDelay * this.reconnectAttempts);
    }
    
    /**
     * Send message to server
     */
    send(data) {
        if (this.ws && this.ws.readyState === WebSocket.OPEN) {
            this.ws.send(JSON.stringify(data));
        } else {
            console.warn('WebSocket not connected, message not sent:', data);
        }
    }
    
    /**
     * Start heartbeat to keep connection alive
     */
    startHeartbeat() {
        this.heartbeatTimer = setInterval(() => {
            if (this.connected) {
                this.send({ type: 'ping' });
            }
        }, this.heartbeatInterval);
    }
    
    /**
     * Stop heartbeat
     */
    stopHeartbeat() {
        if (this.heartbeatTimer) {
            clearInterval(this.heartbeatTimer);
            this.heartbeatTimer = null;
        }
    }
    
    /**
     * Register event handler
     */
    on(event, handler) {
        if (!this.handlers[event]) {
            this.handlers[event] = [];
        }
        this.handlers[event].push(handler);
    }
    
    /**
     * Trigger event handlers
     */
    trigger(event, data) {
        if (this.handlers[event]) {
            this.handlers[event].forEach(handler => {
                try {
                    handler(data);
                } catch (error) {
                    console.error('Error in event handler:', error);
                }
            });
        }
    }
    
    /**
     * Handle thesis update
     */
    handleThesisUpdate(message) {
        // Show notification
        this.showNotification('Actualización de Tesis', 
            `Tesis ${message.thesis_id}: ${message.action}`);
        
        // Update UI if on thesis page
        if (window.location.pathname.includes('/tesis/view')) {
            const urlParams = new URLSearchParams(window.location.search);
            const currentThesisId = urlParams.get('id');
            
            if (currentThesisId == message.thesis_id) {
                // Reload page to show updates
                window.location.reload();
            }
        }
    }
    
    /**
     * Handle notification
     */
    handleNotification(message) {
        // Update notification counter
        this.updateNotificationCounter();
        
        // Show browser notification
        this.showNotification('Nueva Notificación', message.notification.contenido);
        
        // Show in-app toast
        this.showToast(message.notification.contenido);
    }
    
    /**
     * Update notification counter in navbar
     */
    updateNotificationCounter() {
        // Fetch updated count from server
        fetch('/notification/get-unread-count')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.notification-badge');
                if (badge && data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline';
                } else if (badge) {
                    badge.style.display = 'none';
                }
            })
            .catch(error => console.error('Error updating notification count:', error));
    }
    
    /**
     * Show browser notification
     */
    showNotification(title, body) {
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(title, {
                body: body,
                icon: '/assets/icon-192.png',
                badge: '/assets/badge.png'
            });
        }
    }
    
    /**
     * Show toast notification
     */
    showToast(message) {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-primary border-0';
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        // Add to container
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(container);
        }
        
        container.appendChild(toast);
        
        // Show toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Remove after hiding
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
    
    /**
     * Close connection
     */
    close() {
        this.stopHeartbeat();
        if (this.ws) {
            this.ws.close();
        }
    }
}

// Initialize WebSocket connection when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Only connect if user is logged in
    if (document.querySelector('.navbar .nav-item:has(.btn-outline-light)')) {
        // Initialize WebSocket (adjust URL based on your configuration)
        const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        const host = window.location.hostname;
        const wsUrl = `${protocol}//${host}:8080`;
        
        window.sgdiiWebSocket = new SGDIIWebSocket(wsUrl, {
            maxReconnectAttempts: 5,
            reconnectDelay: 3000,
            heartbeatInterval: 30000
        });
        
        // Request notification permission if not granted
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
        
        console.log('WebSocket client initialized');
    }
});
