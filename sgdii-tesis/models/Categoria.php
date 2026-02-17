<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "categoria".
 *
 * @property int $id
 * @property string $nombre
 * @property string $descripcion
 * @property int $activo
 * @property string $created_at
 *
 * @property Subcategoria[] $subcategorias
 * @property Tesis[] $tesis
 */
class Categoria extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'categoria';
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
            [['nombre'], 'string', 'max' => 255],
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
     * Gets query for [[Subcategorias]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubcategorias()
    {
        return $this->hasMany(Subcategoria::class, ['categoria_id' => 'id']);
    }

    /**
     * Gets query for [[Tesis]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTesis()
    {
        return $this->hasMany(Tesis::class, ['categoria_id' => 'id']);
    }
}
