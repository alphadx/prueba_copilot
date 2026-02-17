<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "profesor".
 *
 * @property int $id
 * @property string $rut
 * @property string $nombre
 * @property string $correo
 * @property string $telefono
 * @property string $especialidad
 * @property int $es_comision_evaluadora
 * @property int $user_id
 * @property int $activo
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 * @property SolicitudTemaTesis[] $sttCreadas
 * @property SolicitudTemaTesis[] $sttComoGuiaPropuesto
 * @property SolicitudTemaTesis[] $sttComoRevisor1Propuesto
 * @property SolicitudTemaTesis[] $sttComoRevisor2Propuesto
 * @property Tesis[] $tesisComoGuia
 * @property Tesis[] $tesisComoRevisor1
 * @property Tesis[] $tesisComoRevisor2
 */
class Profesor extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profesor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rut', 'nombre'], 'required'],
            [['es_comision_evaluadora', 'user_id', 'activo'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['rut'], 'string', 'max' => 12],
            [['nombre', 'correo', 'especialidad'], 'string', 'max' => 255],
            [['telefono'], 'string', 'max' => 20],
            [['rut'], 'unique'],
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
            'especialidad' => 'Especialidad',
            'es_comision_evaluadora' => 'Es Comisión Evaluadora',
            'user_id' => 'Usuario',
            'activo' => 'Activo',
            'created_at' => 'Fecha de Creación',
            'updated_at' => 'Fecha de Actualización',
        ];
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
     * Gets query for [[SttCreadas]] - STTs created by this professor (as curso professor).
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSttCreadas()
    {
        return $this->hasMany(SolicitudTemaTesis::class, ['profesor_curso_id' => 'id']);
    }

    /**
     * Gets query for [[SttComoGuiaPropuesto]] - STTs where this professor is proposed as guide.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSttComoGuiaPropuesto()
    {
        return $this->hasMany(SolicitudTemaTesis::class, ['profesor_guia_propuesto_id' => 'id']);
    }

    /**
     * Gets query for [[SttComoRevisor1Propuesto]] - STTs where this professor is proposed as reviewer 1.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSttComoRevisor1Propuesto()
    {
        return $this->hasMany(SolicitudTemaTesis::class, ['profesor_revisor1_propuesto_id' => 'id']);
    }

    /**
     * Gets query for [[SttComoRevisor2Propuesto]] - STTs where this professor is proposed as reviewer 2.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSttComoRevisor2Propuesto()
    {
        return $this->hasMany(SolicitudTemaTesis::class, ['profesor_revisor2_propuesto_id' => 'id']);
    }

    /**
     * Gets query for [[TesisComoGuia]] - Thesis where this professor is Guide.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTesisComoGuia()
    {
        return $this->hasMany(Tesis::class, ['profesor_guia_id' => 'id']);
    }

    /**
     * Gets query for [[TesisComoRevisor1]] - Thesis where this professor is Reviewer 1.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTesisComoRevisor1()
    {
        return $this->hasMany(Tesis::class, ['profesor_revisor1_id' => 'id']);
    }

    /**
     * Gets query for [[TesisComoRevisor2]] - Thesis where this professor is Reviewer 2.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTesisComoRevisor2()
    {
        return $this->hasMany(Tesis::class, ['profesor_revisor2_id' => 'id']);
    }
}
