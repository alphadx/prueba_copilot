<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Tesis;
use app\models\HistorialEstado;
use app\components\NotificationService;

/**
 * TesisController handles thesis workflow management
 */
class TesisController extends Controller
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'cambiar-estado' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Tesis models for current user
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $query = Tesis::find()->joinWith(['stt', 'stt.alumnos', 'profesorGuia', 'categoria']);
        
        // Filter based on role
        if ($user->rol === 'profesor' || $user->rol === 'comision_evaluadora') {
            // Show theses where user is guide or reviewer
            $profesor = \app\models\Profesor::findOne(['user_id' => $user->id]);
            if ($profesor) {
                $query->where(['or',
                    ['profesor_guia_id' => $profesor->id],
                    ['profesor_revisor1_id' => $profesor->id],
                    ['profesor_revisor2_id' => $profesor->id]
                ]);
            } else {
                // No profesor record, show empty
                $query->where('1=0');
            }
        } elseif ($user->rol === 'alumno') {
            // Show theses where user is one of the students
            $alumno = \app\models\Alumno::findOne(['user_id' => $user->id]);
            if ($alumno) {
                $query->joinWith('stt.sttAlumnos')->where(['stt_alumno.alumno_id' => $alumno->id]);
            } else {
                // No alumno record, show empty
                $query->where('1=0');
            }
        }
        // Admin sees all
        
        $tesis = $query->orderBy(['fecha_ultima_actualizacion' => SORT_DESC])->all();
        
        return $this->render('index', [
            'tesis' => $tesis,
        ]);
    }

    /**
     * Displays a single Tesis model
     */
    public function actionView($id)
    {
        $tesis = $this->findModel($id);
        $this->checkAccess($tesis);
        
        $historial = HistorialEstado::find()
            ->where(['tesis_id' => $id])
            ->orderBy(['fecha_cambio' => SORT_DESC])
            ->all();
        
        return $this->render('view', [
            'tesis' => $tesis,
            'historial' => $historial,
        ]);
    }

    /**
     * Change thesis workflow state
     */
    public function actionCambiarEstado($id)
    {
        $tesis = $this->findModel($id);
        $this->checkAccess($tesis);
        
        $nuevoEstado = Yii::$app->request->post('estado');
        $motivo = Yii::$app->request->post('motivo', '');
        
        if ($tesis->cambiarEstado($nuevoEstado, $motivo)) {
            // Send notifications
            $this->enviarNotificacionesCambioEstado($tesis, $nuevoEstado);
            
            Yii::$app->session->setFlash('success', 'El estado de la tesis ha sido actualizado correctamente.');
        } else {
            Yii::$app->session->setFlash('error', 'Error al cambiar el estado de la tesis.');
        }
        
        return $this->redirect(['view', 'id' => $tesis->id]);
    }

    /**
     * Advance to next stage
     */
    public function actionAvanzarEtapa($id)
    {
        $tesis = $this->findModel($id);
        $this->checkAccess($tesis);
        
        if ($tesis->puedeAvanzarEtapa()) {
            $tesis->etapa_actual += 1;
            $tesis->fecha_ultima_actualizacion = date('Y-m-d H:i:s');
            
            if ($tesis->save()) {
                // If reached final stage, mark as finalized
                if ($tesis->etapa_actual >= $tesis->total_etapas) {
                    $tesis->cambiarEstado(Tesis::ESTADO_FINALIZADA, 'Completadas todas las etapas');
                }
                
                $this->enviarNotificacionesAvanceEtapa($tesis);
                
                Yii::$app->session->setFlash('success', 'La tesis ha avanzado a la siguiente etapa.');
            } else {
                Yii::$app->session->setFlash('error', 'Error al avanzar la etapa.');
            }
        } else {
            Yii::$app->session->setFlash('error', 'La tesis ya está en la última etapa.');
        }
        
        return $this->redirect(['view', 'id' => $tesis->id]);
    }

    /**
     * Send notifications for state change
     */
    protected function enviarNotificacionesCambioEstado($tesis, $nuevoEstado)
    {
        $notificationService = Yii::$app->notificationService;
        
        // Notify students
        foreach ($tesis->stt->alumnos as $alumno) {
            if ($alumno->user_id) {
                $mensaje = "La tesis '{$tesis->stt->titulo}' ha cambiado a estado: {$nuevoEstado}";
                $notificationService->crearNotificacion(
                    'cambio_estado_tesis',
                    $mensaje,
                    $alumno->user_id,
                    $tesis->stt_id
                );
            }
        }
        
        // Notify guide professor
        if ($tesis->profesorGuia && $tesis->profesorGuia->user_id) {
            $mensaje = "La tesis '{$tesis->stt->titulo}' ha cambiado a estado: {$nuevoEstado}";
            $notificationService->crearNotificacion(
                'cambio_estado_tesis',
                $mensaje,
                $tesis->profesorGuia->user_id,
                $tesis->stt_id
            );
        }
        
        // Notify reviewers
        foreach ([$tesis->profesorRevisor1, $tesis->profesorRevisor2] as $revisor) {
            if ($revisor && $revisor->user_id) {
                $mensaje = "La tesis '{$tesis->stt->titulo}' ha cambiado a estado: {$nuevoEstado}";
                $notificationService->crearNotificacion(
                    'cambio_estado_tesis',
                    $mensaje,
                    $revisor->user_id,
                    $tesis->stt_id
                );
            }
        }
    }

    /**
     * Send notifications for stage advancement
     */
    protected function enviarNotificacionesAvanceEtapa($tesis)
    {
        $notificationService = Yii::$app->notificationService;
        
        // Notify students
        foreach ($tesis->stt->alumnos as $alumno) {
            if ($alumno->user_id) {
                $mensaje = "La tesis '{$tesis->stt->titulo}' ha avanzado a {$tesis->getEtapaLabel()}";
                $notificationService->crearNotificacion(
                    'avance_etapa_tesis',
                    $mensaje,
                    $alumno->user_id,
                    $tesis->stt_id
                );
            }
        }
    }

    /**
     * Finds the Tesis model based on its primary key value.
     */
    protected function findModel($id)
    {
        if (($model = Tesis::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('La tesis solicitada no existe.');
    }

    /**
     * Check if current user has access to this thesis
     */
    protected function checkAccess($tesis)
    {
        $user = Yii::$app->user->identity;
        
        // Admin has access to all
        if ($user->rol === 'admin') {
            return true;
        }
        
        // Professors can access theses they are involved in
        if ($user->rol === 'profesor' || $user->rol === 'comision_evaluadora') {
            $profesor = \app\models\Profesor::findOne(['user_id' => $user->id]);
            if ($profesor && (
                $tesis->profesor_guia_id === $profesor->id ||
                $tesis->profesor_revisor1_id === $profesor->id ||
                $tesis->profesor_revisor2_id === $profesor->id
            )) {
                return true;
            }
        }
        
        // Students can access their own theses
        if ($user->rol === 'alumno') {
            $alumno = \app\models\Alumno::findOne(['user_id' => $user->id]);
            if ($alumno) {
                foreach ($tesis->stt->alumnos as $tesisAlumno) {
                    if ($tesisAlumno->id === $alumno->id) {
                        return true;
                    }
                }
            }
        }
        
        throw new \yii\web\ForbiddenHttpException('No tiene permisos para acceder a esta tesis.');
    }
}
