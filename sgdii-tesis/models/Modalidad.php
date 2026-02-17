<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "modalidad".
 *
 * @property int $id
 * @property string $nombre
 * @property string $descripcion
 * @property int $activo
 * @property string $created_at
 *
 * @property SolicitudTemaTesis[] $solicitudesTemaTesis
 */
class Modalidad extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'modalidad';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['descripcion'], 'string'],
            [['activo'], 'integer'],
            [['created_at'], 'safe'],
            [['nombre'], 'string', 'max' => 100],
            [['nombre'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripción',
            'activo' => 'Activo',
            'created_at' => 'Fecha de Creación',
        ];
    }

    /**
     * Gets query for [[SolicitudesTemaTesis]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSolicitudesTemaTesis()
    {
        return $this->hasMany(SolicitudTemaTesis::class, ['modalidad_id' => 'id']);
    }
}
