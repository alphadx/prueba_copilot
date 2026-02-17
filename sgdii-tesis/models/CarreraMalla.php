<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "carrera_malla".
 *
 * @property int $id
 * @property string $codigo
 * @property string $nombre
 * @property string $grado
 * @property int $activo
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Alumno[] $alumnos
 * @property SttAlumno[] $sttAlumnos
 */
class CarreraMalla extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carrera_malla';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo', 'nombre', 'grado'], 'required'],
            [['activo'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['codigo'], 'string', 'max' => 20],
            [['nombre'], 'string', 'max' => 255],
            [['grado'], 'string', 'max' => 50],
            [['codigo'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Código',
            'nombre' => 'Nombre',
            'grado' => 'Grado',
            'activo' => 'Activo',
            'created_at' => 'Fecha de Creación',
            'updated_at' => 'Fecha de Actualización',
        ];
    }

    /**
     * Gets query for [[Alumnos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAlumnos()
    {
        return $this->hasMany(Alumno::class, ['carrera_malla_id' => 'id']);
    }

    /**
     * Gets query for [[SttAlumnos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSttAlumnos()
    {
        return $this->hasMany(SttAlumno::class, ['carrera_malla_id' => 'id']);
    }
}
