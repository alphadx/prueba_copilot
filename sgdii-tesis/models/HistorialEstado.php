<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "historial_estado".
 *
 * @property int $id
 * @property int $stt_id
 * @property int $tesis_id
 * @property string $estado_anterior
 * @property string $estado_nuevo
 * @property int $etapa
 * @property string $motivo
 * @property int $usuario_id
 * @property string $fecha
 * @property string $created_at
 *
 * @property SolicitudTemaTesis $stt
 * @property Tesis $tesis
 * @property User $usuario
 */
class HistorialEstado extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'historial_estado';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estado_nuevo', 'usuario_id'], 'required'],
            [['stt_id', 'tesis_id', 'etapa', 'usuario_id'], 'integer'],
            [['motivo'], 'string'],
            [['fecha', 'created_at'], 'safe'],
            [['estado_anterior', 'estado_nuevo'], 'string', 'max' => 50],
            [['stt_id'], 'exist', 'skipOnError' => true, 'targetClass' => SolicitudTemaTesis::class, 'targetAttribute' => ['stt_id' => 'id']],
            [['tesis_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tesis::class, 'targetAttribute' => ['tesis_id' => 'id']],
            [['usuario_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['usuario_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stt_id' => 'Solicitud de Tema de Tesis',
            'tesis_id' => 'Tesis',
            'estado_anterior' => 'Estado Anterior',
            'estado_nuevo' => 'Estado Nuevo',
            'etapa' => 'Etapa',
            'motivo' => 'Motivo',
            'usuario_id' => 'Usuario',
            'fecha' => 'Fecha',
            'created_at' => 'Creado',
        ];
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
     * Gets query for [[Tesis]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTesis()
    {
        return $this->hasOne(Tesis::class, ['id' => 'tesis_id']);
    }

    /**
     * Gets query for [[Usuario]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(User::class, ['id' => 'usuario_id']);
    }
}
