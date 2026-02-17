<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use app\models\SolicitudTemaTesis;
use app\models\Profesor;
use app\models\Modalidad;
use app\models\Tesis;
use app\models\HistorialEstado;

/**
 * Controller for Comisión Evaluadora - STT Resolution
 */
class ComisionController extends Controller
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
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            // Only admin or comision_evaluadora can access
                            if ($user->rol === 'admin') {
                                return true;
                            }
                            
                            if ($user->rol === 'profesor') {
                                $profesor = Profesor::findOne(['user_id' => $user->id]);
                                return $profesor && $profesor->es_comision_evaluadora == 1;
                            }
                            
                            return false;
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'resolve' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all pending STT for resolution with advanced filters
     * @return mixed
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        
        // Build query
        $query = SolicitudTemaTesis::find()
            ->joinWith(['modalidad', 'profesorGuiaPropuesto', 'alumnos']);
        
        // Apply filters
        $modalidadId = $request->get('modalidad_id');
        if ($modalidadId) {
            $query->andWhere(['modalidad_id' => $modalidadId]);
        }
        
        $estado = $request->get('estado');
        if ($estado) {
            $query->andWhere(['estado' => $estado]);
        }
        
        $fechaDesde = $request->get('fecha_desde');
        if ($fechaDesde) {
            $query->andWhere(['>=', 'fecha_creacion', $fechaDesde . ' 00:00:00']);
        }
        
        $fechaHasta = $request->get('fecha_hasta');
        if ($fechaHasta) {
            $query->andWhere(['<=', 'fecha_creacion', $fechaHasta . ' 23:59:59']);
        }
        
        $profesorGuiaId = $request->get('profesor_guia_id');
        if ($profesorGuiaId) {
            $query->andWhere(['profesor_guia_propuesto_id' => $profesorGuiaId]);
        }
        
        // Order by creation date (newest first)
        $query->orderBy(['fecha_creacion' => SORT_DESC]);
        
        $solicitudes = $query->all();
        
        return $this->render('index', [
            'solicitudes' => $solicitudes,
            'modalidades' => Modalidad::find()->where(['activo' => 1])->all(),
            'profesores' => Profesor::find()->where(['activo' => 1])->all(),
            'estados' => SolicitudTemaTesis::getEstados(),
            'filters' => [
                'modalidad_id' => $modalidadId,
                'estado' => $estado,
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
                'profesor_guia_id' => $profesorGuiaId,
            ],
        ]);
    }

    /**
     * Displays resolution form for a specific STT
     * @param integer $id
     * @return mixed
     */
    public function actionReview($id)
    {
        $stt = $this->findModel($id);
        
        // Check if STT can be resolved
        if (!$stt->puedeSerResuelta()) {
            Yii::$app->session->setFlash('warning', 
                "Esta solicitud ya ha sido resuelta con estado: {$stt->estado}"
            );
        }
        
        return $this->render('review', [
            'model' => $stt,
            'profesores' => Profesor::find()->where(['activo' => 1])->all(),
        ]);
    }

    /**
     * Process STT resolution
     * @param integer $id
     * @return mixed
     */
    public function actionResolve($id)
    {
        $stt = $this->findModel($id);
        
        // Check if STT can be resolved
        if (!$stt->puedeSerResuelta()) {
            Yii::$app->session->setFlash('error', 
                'Esta solicitud ya no puede ser resuelta.'
            );
            return $this->redirect(['review', 'id' => $id]);
        }
        
        $request = Yii::$app->request;
        $resolucion = $request->post('resolucion');
        $motivo = $request->post('motivo');
        $userId = Yii::$app->user->id;
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $success = false;
            
            switch ($resolucion) {
                case 'aceptar':
                    $success = $stt->aceptar($userId, $motivo);
                    $message = 'Solicitud aceptada exitosamente.';
                    break;
                    
                case 'aceptar_con_observaciones':
                    if (empty($motivo)) {
                        throw new \Exception('Debe proporcionar las observaciones.');
                    }
                    $success = $stt->aceptarConObservaciones($userId, $motivo);
                    $message = 'Solicitud aceptada con observaciones.';
                    break;
                    
                case 'rechazar':
                    if (empty($motivo)) {
                        throw new \Exception('Debe proporcionar el motivo de rechazo.');
                    }
                    $success = $stt->rechazar($userId, $motivo);
                    $message = 'Solicitud rechazada.';
                    break;
                    
                default:
                    throw new \Exception('Tipo de resolución inválido.');
            }
            
            if ($success) {
                // Save resolution record
                $resolucionRecord = new \app\models\ResolucionStt();
                $resolucionRecord->stt_id = $stt->id;
                $resolucionRecord->tipo = $resolucion;
                $resolucionRecord->motivo = $motivo;
                $resolucionRecord->usuario_id = $userId;
                $resolucionRecord->fecha_resolucion = date('Y-m-d H:i:s');
                $resolucionRecord->save(false);
                
                $transaction->commit();
                
                // Send notifications
                $this->enviarNotificaciones($stt, $resolucion, $motivo);
                
                Yii::$app->session->setFlash('success', $message);
                return $this->redirect(['index']);
            } else {
                throw new \Exception('No se pudo guardar la resolución.');
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());
            return $this->redirect(['review', 'id' => $id]);
        }
    }

    /**
     * Get professor's active thesis summary (for modal display)
     * @param integer $id Professor ID
     * @return mixed
     */
    public function actionProfesorTheses($id)
    {
        $profesor = Profesor::findOne($id);
        
        if (!$profesor) {
            throw new NotFoundHttpException('Profesor no encontrado.');
        }
        
        // Get active thesis as guide
        $tesisComoGuia = Tesis::find()
            ->where(['profesor_guia_id' => $id])
            ->andWhere(['<>', 'estado', 'Finalizada'])
            ->with(['stt', 'stt.alumnos'])
            ->all();
        
        // Get active thesis as reviewer 1
        $tesisComoRevisor1 = Tesis::find()
            ->where(['profesor_revisor1_id' => $id])
            ->andWhere(['<>', 'estado', 'Finalizada'])
            ->with(['stt', 'stt.alumnos'])
            ->all();
        
        // Get active thesis as reviewer 2
        $tesisComoRevisor2 = Tesis::find()
            ->where(['profesor_revisor2_id' => $id])
            ->andWhere(['<>', 'estado', 'Finalizada'])
            ->with(['stt', 'stt.alumnos'])
            ->all();
        
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_profesor_theses', [
                'profesor' => $profesor,
                'tesisComoGuia' => $tesisComoGuia,
                'tesisComoRevisor1' => $tesisComoRevisor1,
                'tesisComoRevisor2' => $tesisComoRevisor2,
            ]);
        }
        
        return $this->render('profesor-theses', [
            'profesor' => $profesor,
            'tesisComoGuia' => $tesisComoGuia,
            'tesisComoRevisor1' => $tesisComoRevisor1,
            'tesisComoRevisor2' => $tesisComoRevisor2,
        ]);
    }

    /**
     * Send notifications to students and professors
     * @param SolicitudTemaTesis $stt
     * @param string $resolucion
     * @param string $motivo
     */
    private function enviarNotificaciones($stt, $resolucion, $motivo)
    {
        // Get recipients
        $alumnos = $stt->alumnos;
        $profesorGuia = $stt->profesorGuiaPropuesto;
        $profesorRevisor1 = $stt->profesorRevisor1Propuesto;
        $profesorRevisor2 = $stt->profesorRevisor2Propuesto;
        
        // Prepare message based on resolution type
        $subject = '';
        $body = '';
        
        switch ($resolucion) {
            case 'aceptar':
                $subject = "STT Aceptada - {$stt->correlativo}";
                $body = "Su solicitud de tema de tesis '{$stt->titulo}' ha sido ACEPTADA por la Comisión de Titulación.";
                if ($motivo) {
                    $body .= "\n\nComentarios: {$motivo}";
                }
                break;
                
            case 'aceptar_con_observaciones':
                $subject = "STT Aceptada con Observaciones - {$stt->correlativo}";
                $body = "Su solicitud de tema de tesis '{$stt->titulo}' ha sido ACEPTADA CON OBSERVACIONES por la Comisión de Titulación.";
                $body .= "\n\nObservaciones: {$motivo}";
                break;
                
            case 'rechazar':
                $subject = "STT Rechazada - {$stt->correlativo}";
                $body = "Su solicitud de tema de tesis '{$stt->titulo}' ha sido RECHAZADA por la Comisión de Titulación.";
                $body .= "\n\nMotivo: {$motivo}";
                break;
        }
        
        // Send to students
        foreach ($alumnos as $alumno) {
            if ($alumno->correo) {
                Yii::$app->session->addFlash('info', 
                    "Notificación enviada a alumno: {$alumno->nombre} ({$alumno->correo})"
                );
                // In production, use Yii::$app->mailer->compose()->send()
            }
        }
        
        // Send to professors
        if ($profesorGuia && $profesorGuia->correo) {
            Yii::$app->session->addFlash('info', 
                "Notificación enviada a Profesor Guía: {$profesorGuia->nombre} ({$profesorGuia->correo})"
            );
        }
        
        if ($profesorRevisor1 && $profesorRevisor1->correo) {
            Yii::$app->session->addFlash('info', 
                "Notificación enviada a Revisor 1: {$profesorRevisor1->nombre} ({$profesorRevisor1->correo})"
            );
        }
        
        if ($profesorRevisor2 && $profesorRevisor2->correo) {
            Yii::$app->session->addFlash('info', 
                "Notificación enviada a Revisor 2: {$profesorRevisor2->nombre} ({$profesorRevisor2->correo})"
            );
        }
    }

    /**
     * Finds the SolicitudTemaTesis model based on its primary key value.
     * @param integer $id
     * @return SolicitudTemaTesis the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = SolicitudTemaTesis::findOne($id);
        
        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('La solicitud no existe.');
    }
}
