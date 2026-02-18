# WebSocket Setup for Real-Time Updates

## Overview
The SGDII Tesis system includes WebSocket support for real-time updates of thesis workflow changes and notifications.

## Components

### Server Side
1. **WebSocketService** (`sgdii-tesis/components/WebSocketService.php`)
   - PHP-based WebSocket server implementation
   - Handles client connections, messages, and broadcasts
   - Supports thesis updates and notification broadcasts

2. **WebsocketController** (`sgdii-tesis/commands/WebsocketController.php`)
   - Console command to start the WebSocket server
   - Run: `php yii websocket/start`

### Client Side
1. **websocket-client.js** (`sgdii-tesis/web/js/websocket-client.js`)
   - JavaScript WebSocket client
   - Auto-reconnection support
   - Heartbeat mechanism
   - Event handling for thesis updates and notifications

## Starting the WebSocket Server

### Development
```bash
# Start the WebSocket server
cd /path/to/prueba_copilot/sgdii-tesis
php yii websocket/start
```

The server will start on `0.0.0.0:8080` by default.

### Production
For production, you should:

1. **Use a Process Manager** (e.g., Supervisor)
   ```ini
   [program:sgdii-websocket]
   command=/usr/bin/php /path/to/sgdii-tesis/yii websocket/start
   directory=/path/to/sgdii-tesis
   autostart=true
   autorestart=true
   user=www-data
   redirect_stderr=true
   stdout_logfile=/var/log/sgdii-websocket.log
   ```

2. **Or use a more robust WebSocket solution:**
   - [Ratchet](http://socketo.me/) - PHP WebSocket library
   - [Swoole](https://www.swoole.co.uk/) - High-performance PHP framework
   - [Socket.IO](https://socket.io/) with Node.js
   - [Redis Pub/Sub](https://redis.io/topics/pubsub) with a WebSocket gateway

## Configuration

### Change WebSocket Port
Edit `sgdii-tesis/commands/WebsocketController.php`:
```php
$webSocketService = new \app\components\WebSocketService([
    'host' => '0.0.0.0',
    'port' => 8080, // Change this
]);
```

Also update `sgdii-tesis/web/js/websocket-client.js`:
```javascript
const wsUrl = `${protocol}//${host}:8080`; // Match the port
```

### Enable/Disable WebSocket
To disable WebSocket, remove or comment out this line in `main.php`:
```php
// $this->registerJsFile('/js/websocket-client.js', ['position' => \yii\web\View::POS_END]);
```

## Usage in Controllers

### Broadcasting Thesis Updates
```php
// In TesisController or any controller
if (Yii::$app->has('webSocketService')) {
    Yii::$app->webSocketService->broadcastThesisUpdate(
        $tesis->id,
        'state_changed',
        [
            'new_state' => $tesis->estado,
            'updated_by' => Yii::$app->user->identity->nombre
        ]
    );
}
```

### Broadcasting Notifications
```php
// In NotificationService or controllers
if (Yii::$app->has('webSocketService')) {
    Yii::$app->webSocketService->broadcastNotification(
        $userId,
        [
            'tipo' => 'notification_type',
            'contenido' => 'Notification message'
        ]
    );
}
```

## Client-Side Events

The WebSocket client triggers the following events:

- **connected**: When WebSocket connection is established
- **disconnected**: When WebSocket connection is closed
- **thesis_update**: When a thesis is updated
- **notification**: When a new notification is received

### Custom Event Handlers
```javascript
// Add custom handlers
if (window.sgdiiWebSocket) {
    window.sgdiiWebSocket.on('thesis_update', function(data) {
        console.log('Thesis updated:', data);
        // Custom handling
    });
    
    window.sgdiiWebSocket.on('notification', function(data) {
        console.log('New notification:', data);
        // Custom handling
    });
}
```

## Firewall Configuration

Ensure port 8080 (or your chosen port) is open:

```bash
# Ubuntu/Debian with UFW
sudo ufw allow 8080/tcp

# CentOS/RHEL with firewalld
sudo firewall-cmd --permanent --add-port=8080/tcp
sudo firewall-cmd --reload
```

## Docker Configuration

If using Docker, expose the WebSocket port in `docker-compose.yml`:

```yaml
services:
  sgdii:
    ports:
      - "8080:8080"  # WebSocket port
      - "80:80"      # HTTP port
```

## Troubleshooting

### WebSocket Connection Failed
1. Check if the WebSocket server is running
2. Verify firewall rules allow port 8080
3. Check browser console for error messages
4. Ensure the URL protocol matches (ws:// for http, wss:// for https)

### Connection Keeps Dropping
1. Check server logs for errors
2. Increase heartbeat interval
3. Verify network stability
4. Consider using a more robust WebSocket implementation

### SSL/TLS (wss://)
For secure WebSocket connections (wss://), you need to:
1. Use a reverse proxy (nginx, Apache) with SSL termination
2. Or implement SSL directly in the WebSocket server
3. Update the client URL to use `wss://` protocol

Example nginx configuration:
```nginx
location /ws {
    proxy_pass http://localhost:8080;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host $host;
}
```

## Future Enhancements

Consider these improvements for production:
- Implement authentication/authorization for WebSocket connections
- Add channel-based subscriptions (only receive relevant updates)
- Implement message queuing with Redis
- Add WebSocket clustering for horizontal scaling
- Implement reconnection with message replay
- Add compression for large messages
