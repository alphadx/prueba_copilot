<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "resolucion_stt".
 *
 * @property int $id
 * @property int $stt_id
 * @property string $tipo
 * @property string $motivo
 * @property int $usuario_id
 * @property string $fecha_resolucion
 * @property string $created_at
 *
 * @property SolicitudTemaTesis $stt
 * @property User $usuario
 */
class ResolucionStt extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resolucion_stt';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stt_id', 'tipo', 'motivo', 'usuario_id'], 'required'],
            [['stt_id', 'usuario_id'], 'integer'],
            [['motivo'], 'string'],
            [['fecha_resolucion', 'created_at'], 'safe'],
            [['tipo'], 'string', 'max' => 50],
            [['stt_id'], 'exist', 'skipOnError' => true, 'targetClass' => SolicitudTemaTesis::class, 'targetAttribute' => ['stt_id' => 'id']],
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
            'tipo' => 'Tipo',
            'motivo' => 'Motivo',
            'usuario_id' => 'Usuario',
            'fecha_resolucion' => 'Fecha de Resolución',
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
     * Gets query for [[Usuario]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(User::class, ['id' => 'usuario_id']);
    }
}
