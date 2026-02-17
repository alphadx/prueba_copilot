<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "origen".
 *
 * @property int $id
 * @property string $nombre
 * @property string $descripcion
 * @property int $activo
 * @property string $created_at
 *
 * @property SolicitudTemaTesis[] $solicitudesTemaTesis
 */
class Origen extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'origen';
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
        return $this->hasMany(SolicitudTemaTesis::class, ['origen_id' => 'id']);
    }
}
