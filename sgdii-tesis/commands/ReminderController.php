<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Reminder command for sending notifications about pending STTs
 */
class ReminderController extends Controller
{
    /**
     * Send reminders for pending STTs older than specified days
     * @param int $days Number of days threshold (default: 7)
     * @return int Exit code
     */
    public function actionPendingStts($days = 7)
    {
        $this->stdout("Checking for pending STTs older than {$days} days...\n");
        
        $notificationService = Yii::$app->notificationService;
        $count = $notificationService->sendRemindersForOldPendingSTTs($days);
        
        $this->stdout("Sent {$count} reminder(s).\n");
        
        return ExitCode::OK;
    }
    
    /**
     * Send daily reminders (can be scheduled via cron)
     * Checks for STTs pending for more than 7 days
     * @return int Exit code
     */
    public function actionDaily()
    {
        $this->stdout("Running daily reminder check...\n");
        return $this->actionPendingStts(7);
    }
    
    /**
     * Send weekly reminders (can be scheduled via cron)
     * Checks for STTs pending for more than 14 days
     * @return int Exit code
     */
    public function actionWeekly()
    {
        $this->stdout("Running weekly reminder check...\n");
        return $this->actionPendingStts(14);
    }
}
