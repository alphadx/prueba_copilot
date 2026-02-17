<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "alumno".
 *
 * @property int $id
 * @property string $rut
 * @property string $nombre
 * @property string $correo
 * @property string $telefono
 * @property int $carrera_malla_id
 * @property string $tipo_ingreso
 * @property int $anio_ingreso
 * @property int $user_id
 * @property int $activo
 * @property string $created_at
 * @property string $updated_at
 *
 * @property CarreraMalla $carreraMalla
 * @property User $user
 * @property SttAlumno[] $sttAlumnos
 */
class Alumno extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'alumno';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rut', 'nombre', 'carrera_malla_id'], 'required'],
            [['carrera_malla_id', 'anio_ingreso', 'user_id', 'activo'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['rut'], 'string', 'max' => 12],
            [['nombre', 'correo'], 'string', 'max' => 255],
            [['telefono'], 'string', 'max' => 20],
            [['tipo_ingreso'], 'string', 'max' => 50],
            [['rut'], 'unique'],
            [['carrera_malla_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarreraMalla::class, 'targetAttribute' => ['carrera_malla_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rut' => 'RUT',
            'nombre' => 'Nombre',
            'correo' => 'Correo',
            'telefono' => 'Teléfono',
            'carrera_malla_id' => 'Carrera/Malla',
            'tipo_ingreso' => 'Tipo de Ingreso',
            'anio_ingreso' => 'Año de Ingreso',
            'user_id' => 'Usuario',
            'activo' => 'Activo',
            'created_at' => 'Fecha de Creación',
            'updated_at' => 'Fecha de Actualización',
        ];
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

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for [[SttAlumnos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSttAlumnos()
    {
        return $this->hasMany(SttAlumno::class, ['alumno_id' => 'id']);
    }

    /**
     * Gets query for [[SolicitudesTemaTesis]] through SttAlumno.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSolicitudesTemaTesis()
    {
        return $this->hasMany(SolicitudTemaTesis::class, ['id' => 'stt_id'])
            ->viaTable('stt_alumno', ['alumno_id' => 'id']);
    }
}
