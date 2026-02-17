<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stt_alumno".
 *
 * @property int $id
 * @property int $stt_id
 * @property int $alumno_id
 * @property int $carrera_malla_id
 * @property string $created_at
 *
 * @property SolicitudTemaTesis $stt
 * @property Alumno $alumno
 * @property CarreraMalla $carreraMalla
 */
class SttAlumno extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stt_alumno';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stt_id', 'alumno_id', 'carrera_malla_id'], 'required'],
            [['stt_id', 'alumno_id', 'carrera_malla_id'], 'integer'],
            [['created_at'], 'safe'],
            [['stt_id', 'alumno_id'], 'unique', 'targetAttribute' => ['stt_id', 'alumno_id']],
            [['stt_id'], 'exist', 'skipOnError' => true, 'targetClass' => SolicitudTemaTesis::class, 'targetAttribute' => ['stt_id' => 'id']],
            [['alumno_id'], 'exist', 'skipOnError' => true, 'targetClass' => Alumno::class, 'targetAttribute' => ['alumno_id' => 'id']],
            [['carrera_malla_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarreraMalla::class, 'targetAttribute' => ['carrera_malla_id' => 'id']],
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
            'alumno_id' => 'Alumno',
            'carrera_malla_id' => 'Carrera/Malla',
            'created_at' => 'Fecha de Creación',
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
     * Gets query for [[Alumno]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAlumno()
    {
        return $this->hasOne(Alumno::class, ['id' => 'alumno_id']);
    }

    /**
     * Gets query for [[CarreraMalla]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarreraMalla()
    {
        return $this->hasOne(CarreraMalla::class, ['id' => 'carrera_malla_id']);
    }
}
