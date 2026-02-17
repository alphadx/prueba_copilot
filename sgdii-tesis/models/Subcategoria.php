<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "subcategoria".
 *
 * @property int $id
 * @property int $categoria_id
 * @property string $nombre
 * @property string $descripcion
 * @property int $activo
 * @property string $created_at
 *
 * @property Categoria $categoria
 * @property Tesis[] $tesis
 */
class Subcategoria extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subcategoria';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['categoria_id', 'nombre'], 'required'],
            [['categoria_id', 'activo'], 'integer'],
            [['descripcion'], 'string'],
            [['created_at'], 'safe'],
            [['nombre'], 'string', 'max' => 255],
            [['categoria_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categoria::class, 'targetAttribute' => ['categoria_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'categoria_id' => 'Categoría',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripción',
            'activo' => 'Activo',
            'created_at' => 'Fecha de Creación',
        ];
    }

    /**
     * Gets query for [[Categoria]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategoria()
    {
        return $this->hasOne(Categoria::class, ['id' => 'categoria_id']);
    }

    /**
     * Gets query for [[Tesis]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTesis()
    {
        return $this->hasMany(Tesis::class, ['subcategoria_id' => 'id']);
    }
}
