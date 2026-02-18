<?php

namespace app\components;

use Yii;
use yii\base\Component;
use app\models\Notificacion;
use app\models\SolicitudTemaTesis;
use app\models\User;

/**
 * NotificationService handles creation and sending of notifications
 */
class NotificationService extends Component
{
    /**
     * Create and send notification
     * @param int $userId User ID to notify
     * @param string $tipo Notification type
     * @param string $contenido Notification content
     * @param int|null $sttId Related STT ID (optional)
     * @param bool $sendEmail Whether to send email notification
     * @return Notificacion|null
     */
    public function create($userId, $tipo, $contenido, $sttId = null, $sendEmail = true)
    {
        $notificacion = new Notificacion();
        $notificacion->tipo = $tipo;
        $notificacion->contenido = $contenido;
        $notificacion->estado = Notificacion::ESTADO_NO_LEIDA;
        $notificacion->usuario_destinatario_id = $userId;
        $notificacion->stt_id = $sttId;

        if ($notificacion->save()) {
            // Send email if requested
            if ($sendEmail) {
                $this->sendEmail($notificacion);
            }
            return $notificacion;
        }

        // Log error if save failed
        Yii::error('Failed to create notification: ' . json_encode($notificacion->errors), __METHOD__);
        return null;
    }

    /**
     * Notify students about STT resolution
     * @param SolicitudTemaTesis $stt
     * @param string $resolucion Resolution type (aceptar, rechazar, aceptar_con_observaciones)
     * @param string|null $motivo Resolution reason
     */
    public function notifyStudentsAboutResolution($stt, $resolucion, $motivo = null)
    {
        $alumnos = $stt->alumnos;
        
        $tipo = '';
        $contenido = '';
        
        switch ($resolucion) {
            case 'aceptar':
                $tipo = Notificacion::TIPO_STT_ACEPTADA;
                $contenido = "Su solicitud de tema de tesis '{$stt->titulo}' (Correlativo: {$stt->correlativo}) ha sido ACEPTADA por la Comisión de Titulación.";
                if ($motivo) {
                    $contenido .= "\n\nComentarios: {$motivo}";
                }
                break;
                
            case 'aceptar_con_observaciones':
                $tipo = Notificacion::TIPO_STT_ACEPTADA_CON_OBSERVACIONES;
                $contenido = "Su solicitud de tema de tesis '{$stt->titulo}' (Correlativo: {$stt->correlativo}) ha sido ACEPTADA CON OBSERVACIONES por la Comisión de Titulación.";
                $contenido .= "\n\nObservaciones: {$motivo}";
                break;
                
            case 'rechazar':
                $tipo = Notificacion::TIPO_STT_RECHAZADA;
                $contenido = "Su solicitud de tema de tesis '{$stt->titulo}' (Correlativo: {$stt->correlativo}) ha sido RECHAZADA por la Comisión de Titulación.";
                $contenido .= "\n\nMotivo: {$motivo}";
                break;
        }
        
        foreach ($alumnos as $alumno) {
            if ($alumno->user_id) {
                $this->create($alumno->user_id, $tipo, $contenido, $stt->id);
            }
        }
    }

    /**
     * Notify professors about STT resolution
     * @param SolicitudTemaTesis $stt
     * @param string $resolucion Resolution type
     */
    public function notifyProfessorsAboutResolution($stt, $resolucion)
    {
        $profesores = [];
        
        if ($stt->profesorGuiaPropuesto) {
            $profesores[] = $stt->profesorGuiaPropuesto;
        }
        
        if ($stt->profesorRevisor1Propuesto) {
            $profesores[] = $stt->profesorRevisor1Propuesto;
        }
        
        if ($stt->profesorRevisor2Propuesto) {
            $profesores[] = $stt->profesorRevisor2Propuesto;
        }
        
        $estadoTexto = '';
        switch ($resolucion) {
            case 'aceptar':
                $estadoTexto = 'ACEPTADA';
                break;
            case 'aceptar_con_observaciones':
                $estadoTexto = 'ACEPTADA CON OBSERVACIONES';
                break;
            case 'rechazar':
                $estadoTexto = 'RECHAZADA';
                break;
        }
        
        $tipo = "STT Resuelta - {$estadoTexto}";
        $contenido = "La solicitud de tema de tesis '{$stt->titulo}' (Correlativo: {$stt->correlativo}) en la que usted participa ha sido {$estadoTexto} por la Comisión de Titulación.";
        
        foreach ($profesores as $profesor) {
            if ($profesor->user_id) {
                $this->create($profesor->user_id, $tipo, $contenido, $stt->id);
            }
        }
    }

    /**
     * Notify committee when new STT is created
     * @param SolicitudTemaTesis $stt
     */
    public function notifyCommitteeAboutNewSTT($stt)
    {
        // Get all users with comision_evaluadora role
        $comisionUsers = User::find()
            ->where(['rol' => 'comision_evaluadora', 'activo' => 1])
            ->all();
        
        // Also get admins
        $adminUsers = User::find()
            ->where(['rol' => 'admin', 'activo' => 1])
            ->all();
        
        $users = array_merge($comisionUsers, $adminUsers);
        
        $tipo = Notificacion::TIPO_STT_CREADA;
        $contenido = "Se ha registrado una nueva Solicitud de Tema de Tesis para revisión.\n\n";
        $contenido .= "Correlativo: {$stt->correlativo}\n";
        $contenido .= "Título: {$stt->titulo}\n";
        $contenido .= "Modalidad: " . ($stt->modalidad ? $stt->modalidad->nombre : 'N/A');
        
        foreach ($users as $user) {
            $this->create($user->id, $tipo, $contenido, $stt->id);
        }
    }

    /**
     * Send email notification
     * @param Notificacion $notificacion
     * @return bool
     */
    protected function sendEmail($notificacion)
    {
        try {
            $user = $notificacion->usuarioDestinatario;
            
            if (!$user || !$user->correo) {
                return false;
            }

            // Get email configuration from params
            $from = Yii::$app->params['senderEmail'] ?? 'noreply@example.com';
            $fromName = Yii::$app->params['senderName'] ?? 'SGDII Módulo Tesis';

            $result = Yii::$app->mailer->compose('notification', [
                    'notificacion' => $notificacion,
                    'user' => $user,
                ])
                ->setFrom([$from => $fromName])
                ->setTo($user->correo)
                ->setSubject($notificacion->tipo)
                ->send();

            return $result;
        } catch (\Exception $e) {
            Yii::error('Failed to send email: ' . $e->getMessage(), __METHOD__);
            return false;
        }
    }

    /**
     * Mark all notifications as read for a user
     * @param int $userId
     * @return int Number of notifications marked as read
     */
    public function markAllAsRead($userId)
    {
        return Notificacion::updateAll(
            ['estado' => Notificacion::ESTADO_LEIDA],
            [
                'usuario_destinatario_id' => $userId,
                'estado' => Notificacion::ESTADO_NO_LEIDA
            ]
        );
    }
    
    /**
     * Notify when a professor responds to a review request
     * @param \app\models\Tesis $tesis
     * @param \app\models\Profesor $profesor
     * @param string $respuesta Response text or decision
     * @param string $tipo Type of response (acepta, rechaza, comentario)
     */
    public function notifyAboutProfessorResponse($tesis, $profesor, $respuesta, $tipo = 'comentario')
    {
        $accionTexto = [
            'acepta' => 'ha aceptado',
            'rechaza' => 'ha rechazado',
            'comentario' => 'ha respondido a'
        ];
        
        $accion = $accionTexto[$tipo] ?? 'ha respondido a';
        
        $contenido = "El profesor {$profesor->nombre} {$accion} la revisión de la tesis '{$tesis->stt->titulo}' (Correlativo: {$tesis->stt->correlativo}).\n\n";
        $contenido .= "Respuesta: {$respuesta}";
        
        // Notify students
        foreach ($tesis->stt->alumnos as $alumno) {
            if ($alumno->user_id) {
                $this->create($alumno->user_id, Notificacion::TIPO_PROFESOR_RESPONDE, $contenido, $tesis->stt_id);
            }
        }
        
        // Notify guide professor if reviewer responded
        if ($tesis->profesorGuia && $tesis->profesorGuia->id !== $profesor->id && $tesis->profesorGuia->user_id) {
            $this->create($tesis->profesorGuia->user_id, Notificacion::TIPO_PROFESOR_RESPONDE, $contenido, $tesis->stt_id);
        }
        
        // Notify other reviewers
        foreach ([$tesis->profesorRevisor1, $tesis->profesorRevisor2] as $revisor) {
            if ($revisor && $revisor->id !== $profesor->id && $revisor->user_id) {
                $this->create($revisor->user_id, Notificacion::TIPO_PROFESOR_RESPONDE, $contenido, $tesis->stt_id);
            }
        }
    }
    
    /**
     * Send reminder notification for pending STTs
     * @param \app\models\SolicitudTemaTesis $stt
     * @param array $userIds User IDs to notify
     */
    public function sendReminderForPendingSTT($stt, $userIds)
    {
        $contenido = "Recordatorio: La Solicitud de Tema de Tesis '{$stt->titulo}' (Correlativo: {$stt->correlativo}) ";
        $contenido .= "está pendiente de revisión desde el " . Yii::$app->formatter->asDate($stt->fecha_creacion) . ".\n\n";
        $contenido .= "Por favor, revise y resuelva esta solicitud a la brevedad.";
        
        foreach ($userIds as $userId) {
            $this->create($userId, Notificacion::TIPO_RECORDATORIO, $contenido, $stt->id, true);
        }
    }
    
    /**
     * Send bulk reminders for all pending STTs older than X days
     * @param int $dias Number of days threshold
     * @return int Number of reminders sent
     */
    public function sendRemindersForOldPendingSTTs($dias = 7)
    {
        $fechaLimite = date('Y-m-d H:i:s', strtotime("-{$dias} days"));
        
        $pendingSTTs = SolicitudTemaTesis::find()
            ->where(['in', 'estado', [
                SolicitudTemaTesis::ESTADO_SOLICITADA,
                SolicitudTemaTesis::ESTADO_EN_REVISION
            ]])
            ->andWhere(['<', 'fecha_creacion', $fechaLimite])
            ->all();
        
        $count = 0;
        
        // Get committee members
        $comisionUsers = User::find()
            ->where(['in', 'rol', ['comision_evaluadora', 'admin']])
            ->andWhere(['activo' => 1])
            ->all();
        
        $userIds = array_map(fn($u) => $u->id, $comisionUsers);
        
        foreach ($pendingSTTs as $stt) {
            $this->sendReminderForPendingSTT($stt, $userIds);
            $count++;
        }
        
        return $count;
    }
    
    /**
     * Alias for create method to maintain backward compatibility
     * @param string $tipo Notification type
     * @param string $contenido Notification content
     * @param int $userId User ID to notify
     * @param int|null $sttId Related STT ID (optional)
     * @param bool $sendEmail Whether to send email notification
     * @return Notificacion|null
     */
    public function crearNotificacion($tipo, $contenido, $userId, $sttId = null, $sendEmail = true)
    {
        return $this->create($userId, $tipo, $contenido, $sttId, $sendEmail);
    }
}
