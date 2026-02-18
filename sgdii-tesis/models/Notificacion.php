<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "notificaciones".
 *
 * @property int $id
 * @property string $tipo
 * @property string $contenido
 * @property string $estado
 * @property int $usuario_destinatario_id
 * @property int $stt_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $usuarioDestinatario
 * @property SolicitudTemaTesis $stt
 */
class Notificacion extends ActiveRecord
{
    const ESTADO_NO_LEIDA = 'No leída';
    const ESTADO_LEIDA = 'Leída';

    const TIPO_STT_ACEPTADA = 'STT Aceptada';
    const TIPO_STT_RECHAZADA = 'STT Rechazada';
    const TIPO_STT_ACEPTADA_CON_OBSERVACIONES = 'STT Aceptada con Observaciones';
    const TIPO_STT_CREADA = 'STT Creada';
    const TIPO_TESIS_EN_REVISION = 'Tesis en Revisión';
    const TIPO_PROFESOR_RESPONDE = 'Profesor Responde';
    const TIPO_RECORDATORIO = 'Recordatorio';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notificaciones';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('CURRENT_TIMESTAMP'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo', 'contenido', 'usuario_destinatario_id'], 'required'],
            [['contenido'], 'string'],
            [['usuario_destinatario_id', 'stt_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['tipo'], 'string', 'max' => 100],
            [['estado'], 'string', 'max' => 20],
            [['estado'], 'in', 'range' => [self::ESTADO_NO_LEIDA, self::ESTADO_LEIDA]],
            [['usuario_destinatario_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['usuario_destinatario_id' => 'id']],
            [['stt_id'], 'exist', 'skipOnError' => true, 'targetClass' => SolicitudTemaTesis::class, 'targetAttribute' => ['stt_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo' => 'Tipo',
            'contenido' => 'Contenido',
            'estado' => 'Estado',
            'usuario_destinatario_id' => 'Usuario Destinatario',
            'stt_id' => 'STT',
            'created_at' => 'Fecha de Creación',
            'updated_at' => 'Fecha de Actualización',
        ];
    }

    /**
     * Gets query for [[UsuarioDestinatario]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioDestinatario()
    {
        return $this->hasOne(User::class, ['id' => 'usuario_destinatario_id']);
    }

    /**
     * Gets query for [[Stt]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStt()
    {
        return $this->hasOne(SolicitudTemaTesis::class, ['id' => 'stt_id']);
    }

    /**
     * Mark notification as read
     * @return bool
     */
    public function marcarComoLeida()
    {
        $this->estado = self::ESTADO_LEIDA;
        return $this->save(false);
    }

    /**
     * Check if notification is unread
     * @return bool
     */
    public function esNoLeida()
    {
        return $this->estado === self::ESTADO_NO_LEIDA;
    }

    /**
     * Get count of unread notifications for a user
     * @param int $userId
     * @return int
     */
    public static function getUnreadCount($userId)
    {
        return static::find()
            ->where([
                'usuario_destinatario_id' => $userId,
                'estado' => self::ESTADO_NO_LEIDA
            ])
            ->count();
    }

    /**
     * Get all estados
     * @return array
     */
    public static function getEstados()
    {
        return [
            self::ESTADO_NO_LEIDA => 'No leída',
            self::ESTADO_LEIDA => 'Leída',
        ];
    }
}
