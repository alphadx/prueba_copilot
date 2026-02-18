<?php

namespace app\components;

use Yii;
use yii\base\Component;

/**
 * WebSocket Service for real-time updates
 * 
 * This is a lightweight implementation for development.
 * For production, consider using Ratchet, Swoole, or a dedicated WebSocket service.
 */
class WebSocketService extends Component
{
    public $host = '0.0.0.0';
    public $port = 8080;
    public $clients = [];
    public $socket;
    
    /**
     * Initialize WebSocket server
     */
    public function init()
    {
        parent::init();
    }
    
    /**
     * Start WebSocket server (to be run as a daemon)
     */
    public function start()
    {
        // Create socket
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($this->socket, $this->host, $this->port);
        socket_listen($this->socket);
        
        Yii::info("WebSocket server started on {$this->host}:{$this->port}", __METHOD__);
        
        while (true) {
            $read = array_merge([$this->socket], $this->clients);
            $write = null;
            $except = null;
            
            if (socket_select($read, $write, $except, 0, 10) === false) {
                break;
            }
            
            // New client connection
            if (in_array($this->socket, $read)) {
                $client = socket_accept($this->socket);
                $this->clients[] = $client;
                $this->onConnect($client);
                
                $key = array_search($this->socket, $read);
                unset($read[$key]);
            }
            
            // Handle client messages
            foreach ($read as $client) {
                $data = @socket_read($client, 1024);
                
                if ($data === false) {
                    $this->onDisconnect($client);
                    continue;
                }
                
                if (!empty($data)) {
                    $this->onMessage($client, $data);
                }
            }
        }
        
        socket_close($this->socket);
    }
    
    /**
     * Handle new client connection
     */
    protected function onConnect($client)
    {
        Yii::info('New WebSocket client connected', __METHOD__);
        
        // Perform WebSocket handshake
        $request = socket_read($client, 5000);
        $this->performHandshake($client, $request);
    }
    
    /**
     * Handle client disconnect
     */
    protected function onDisconnect($client)
    {
        $key = array_search($client, $this->clients);
        if ($key !== false) {
            unset($this->clients[$key]);
            socket_close($client);
            Yii::info('WebSocket client disconnected', __METHOD__);
        }
    }
    
    /**
     * Handle incoming message
     */
    protected function onMessage($client, $data)
    {
        $message = $this->decode($data);
        
        if ($message) {
            Yii::info('Received: ' . $message, __METHOD__);
            
            // Parse message and handle accordingly
            $payload = json_decode($message, true);
            
            if ($payload && isset($payload['type'])) {
                $this->handleMessage($client, $payload);
            }
        }
    }
    
    /**
     * Handle specific message types
     */
    protected function handleMessage($client, $payload)
    {
        switch ($payload['type']) {
            case 'subscribe':
                // Subscribe to specific channels (e.g., thesis updates)
                $this->subscribe($client, $payload['channel'] ?? 'default');
                break;
                
            case 'ping':
                // Respond to ping
                $this->send($client, json_encode(['type' => 'pong']));
                break;
                
            default:
                Yii::warning('Unknown message type: ' . $payload['type'], __METHOD__);
        }
    }
    
    /**
     * Subscribe client to a channel
     */
    protected function subscribe($client, $channel)
    {
        // Store channel subscription (simplified)
        // In production, use a proper data structure
        Yii::info("Client subscribed to channel: {$channel}", __METHOD__);
    }
    
    /**
     * Broadcast message to all clients
     */
    public function broadcast($message)
    {
        $encoded = $this->encode(json_encode($message));
        
        foreach ($this->clients as $client) {
            @socket_write($client, $encoded, strlen($encoded));
        }
    }
    
    /**
     * Send message to specific client
     */
    public function send($client, $message)
    {
        $encoded = $this->encode($message);
        @socket_write($client, $encoded, strlen($encoded));
    }
    
    /**
     * Perform WebSocket handshake
     */
    protected function performHandshake($client, $request)
    {
        $headers = [];
        $lines = explode("\n", $request);
        
        foreach ($lines as $line) {
            $line = rtrim($line);
            if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
                $headers[$matches[1]] = $matches[2];
            }
        }
        
        if (!isset($headers['Sec-WebSocket-Key'])) {
            return false;
        }
        
        $key = $headers['Sec-WebSocket-Key'];
        $acceptKey = base64_encode(sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
        
        $response = "HTTP/1.1 101 Switching Protocols\r\n";
        $response .= "Upgrade: websocket\r\n";
        $response .= "Connection: Upgrade\r\n";
        $response .= "Sec-WebSocket-Accept: {$acceptKey}\r\n\r\n";
        
        socket_write($client, $response, strlen($response));
        
        return true;
    }
    
    /**
     * Encode message for WebSocket
     */
    protected function encode($message)
    {
        $length = strlen($message);
        $encoded = chr(129); // Text frame
        
        if ($length <= 125) {
            $encoded .= chr($length);
        } elseif ($length <= 65535) {
            $encoded .= chr(126) . pack('n', $length);
        } else {
            $encoded .= chr(127) . pack('N', 0) . pack('N', $length);
        }
        
        return $encoded . $message;
    }
    
    /**
     * Decode WebSocket message
     */
    protected function decode($data)
    {
        $length = ord($data[1]) & 127;
        
        if ($length == 126) {
            $masks = substr($data, 4, 4);
            $payload = substr($data, 8);
        } elseif ($length == 127) {
            $masks = substr($data, 10, 4);
            $payload = substr($data, 14);
        } else {
            $masks = substr($data, 2, 4);
            $payload = substr($data, 6);
        }
        
        $text = '';
        for ($i = 0; $i < strlen($payload); $i++) {
            $text .= $payload[$i] ^ $masks[$i % 4];
        }
        
        return $text;
    }
    
    /**
     * Broadcast thesis workflow update
     */
    public function broadcastThesisUpdate($tesisId, $action, $data = [])
    {
        $message = [
            'type' => 'thesis_update',
            'thesis_id' => $tesisId,
            'action' => $action,
            'data' => $data,
            'timestamp' => time(),
        ];
        
        $this->broadcast($message);
    }
    
    /**
     * Broadcast notification
     */
    public function broadcastNotification($userId, $notification)
    {
        $message = [
            'type' => 'notification',
            'user_id' => $userId,
            'notification' => $notification,
            'timestamp' => time(),
        ];
        
        $this->broadcast($message);
    }
}
