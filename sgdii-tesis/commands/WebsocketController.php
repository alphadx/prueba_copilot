<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * WebSocket server controller
 * Run: php yii websocket/start
 */
class WebsocketController extends Controller
{
    /**
     * Start the WebSocket server
     * @return int Exit code
     */
    public function actionStart()
    {
        $this->stdout("Starting WebSocket server...\n");
        
        try {
            $webSocketService = new \app\components\WebSocketService([
                'host' => '0.0.0.0',
                'port' => 8080,
            ]);
            
            $webSocketService->start();
            
            return ExitCode::OK;
        } catch (\Exception $e) {
            $this->stderr("Error starting WebSocket server: " . $e->getMessage() . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
    
    /**
     * Test WebSocket broadcast
     * @return int Exit code
     */
    public function actionTest()
    {
        $this->stdout("Sending test broadcast...\n");
        
        // This would be called from your application code
        // For now, just demonstrate the usage
        $this->stdout("To broadcast updates, call:\n");
        $this->stdout("  Yii::\$app->webSocketService->broadcastThesisUpdate(\$tesisId, 'state_changed', \$data);\n");
        $this->stdout("  Yii::\$app->webSocketService->broadcastNotification(\$userId, \$notification);\n");
        
        return ExitCode::OK;
    }
}
