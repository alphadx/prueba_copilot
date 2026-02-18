<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\models\Notificacion;

/**
 * NotificationController handles notification operations
 */
class NotificationController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all notifications for current user
     * @return mixed
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $userId = Yii::$app->user->id;
        
        // Build query
        $query = Notificacion::find()
            ->where(['usuario_destinatario_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC]);
        
        // Apply filter
        $filtro = $request->get('filtro', 'todas');
        if ($filtro === 'no_leidas') {
            $query->andWhere(['estado' => Notificacion::ESTADO_NO_LEIDA]);
        } elseif ($filtro === 'leidas') {
            $query->andWhere(['estado' => Notificacion::ESTADO_LEIDA]);
        }
        
        $notificaciones = $query->all();
        
        return $this->render('index', [
            'notificaciones' => $notificaciones,
            'filtro' => $filtro,
        ]);
    }

    /**
     * Mark notification as read
     * @param integer $id
     * @return mixed
     */
    public function actionMarkAsRead($id)
    {
        $notificacion = $this->findModel($id);
        
        if ($notificacion->marcarComoLeida()) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'unreadCount' => Notificacion::getUnreadCount(Yii::$app->user->id),
                ];
            }
            Yii::$app->session->setFlash('success', 'Notificación marcada como leída.');
        } else {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false];
            }
            Yii::$app->session->setFlash('error', 'No se pudo marcar la notificación como leída.');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Mark all notifications as read for current user
     * @return mixed
     */
    public function actionMarkAllAsRead()
    {
        $count = Yii::$app->notificationService->markAllAsRead(Yii::$app->user->id);
        
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'count' => $count,
                'unreadCount' => 0,
            ];
        }
        
        Yii::$app->session->setFlash('success', "Se marcaron {$count} notificaciones como leídas.");
        return $this->redirect(['index']);
    }

    /**
     * Get unread count (for AJAX requests)
     * @return mixed
     */
    public function actionGetUnreadCount()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'count' => Notificacion::getUnreadCount(Yii::$app->user->id),
        ];
    }

    /**
     * Finds the Notificacion model based on its primary key value.
     * Ensures that the notification belongs to the current user.
     * @param integer $id
     * @return Notificacion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Notificacion::findOne([
            'id' => $id,
            'usuario_destinatario_id' => Yii::$app->user->id,
        ]);
        
        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('La notificación no existe o no tiene permiso para acceder a ella.');
    }
}
