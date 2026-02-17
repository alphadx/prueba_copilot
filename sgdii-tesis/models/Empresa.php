<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "empresa".
 *
 * @property int $id
 * @property string $rut
 * @property string $nombre
 * @property string $supervisor_rut
 * @property string $supervisor_nombre
 * @property string $supervisor_correo
 * @property string $supervisor_telefono
 * @property string $supervisor_cargo
 * @property int $activo
 * @property string $created_at
 * @property string $updated_at
 *
 * @property SolicitudTemaTesis[] $solicitudesTemaTesis
 */
class Empresa extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'empresa';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rut', 'nombre'], 'required'],
            [['activo'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['rut', 'supervisor_rut'], 'string', 'max' => 12],
            [['nombre', 'supervisor_nombre', 'supervisor_correo', 'supervisor_cargo'], 'string', 'max' => 255],
            [['supervisor_telefono'], 'string', 'max' => 20],
            [['rut'], 'unique'],
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
            'supervisor_rut' => 'RUT Supervisor',
            'supervisor_nombre' => 'Nombre Supervisor',
            'supervisor_correo' => 'Correo Supervisor',
            'supervisor_telefono' => 'Teléfono Supervisor',
            'supervisor_cargo' => 'Cargo Supervisor',
            'activo' => 'Activo',
            'created_at' => 'Fecha de Creación',
            'updated_at' => 'Fecha de Actualización',
        ];
    }

    /**
     * Gets query for [[SolicitudesTemaTesis]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSolicitudesTemaTesis()
    {
        return $this->hasMany(SolicitudTemaTesis::class, ['empresa_id' => 'id']);
    }
}
