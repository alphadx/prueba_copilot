<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "solicitud_tema_tesis".
 *
 * @property int $id
 * @property string $correlativo
 * @property int $origen_id
 * @property int $profesor_curso_id
 * @property float $nota
 * @property int $modalidad_id
 * @property int $profesor_guia_propuesto_id
 * @property int $profesor_revisor1_propuesto_id
 * @property int $profesor_revisor2_propuesto_id
 * @property int $empresa_id
 * @property string $titulo
 * @property string $documento_path
 * @property string $estado
 * @property string $fecha_creacion
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Origen $origen
 * @property Modalidad $modalidad
 * @property Profesor $profesorCurso
 * @property Profesor $profesorGuiaPropuesto
 * @property Profesor $profesorRevisor1Propuesto
 * @property Profesor $profesorRevisor2Propuesto
 * @property Empresa $empresa
 * @property SttAlumno[] $sttAlumnos
 * @property Alumno[] $alumnos
 * @property Tesis $tesis
 * @property ResolucionStt[] $resoluciones
 * @property HistorialEstado[] $historialEstados
 */
class SolicitudTemaTesis extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'solicitud_tema_tesis';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['origen_id', 'profesor_curso_id', 'nota', 'modalidad_id', 'titulo'], 'required'],
            [['origen_id', 'profesor_curso_id', 'modalidad_id', 'profesor_guia_propuesto_id', 'profesor_revisor1_propuesto_id', 'profesor_revisor2_propuesto_id', 'empresa_id'], 'integer'],
            [['nota'], 'number', 'min' => 1.0, 'max' => 7.0],
            [['fecha_creacion', 'created_at', 'updated_at'], 'safe'],
            [['correlativo'], 'string', 'max' => 20],
            [['correlativo'], 'unique'],
            [['titulo'], 'string', 'max' => 500],
            [['documento_path'], 'string', 'max' => 500],
            [['estado'], 'string', 'max' => 50],
            [['origen_id'], 'exist', 'skipOnError' => true, 'targetClass' => Origen::class, 'targetAttribute' => ['origen_id' => 'id']],
            [['modalidad_id'], 'exist', 'skipOnError' => true, 'targetClass' => Modalidad::class, 'targetAttribute' => ['modalidad_id' => 'id']],
            [['profesor_curso_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profesor::class, 'targetAttribute' => ['profesor_curso_id' => 'id']],
            [['profesor_guia_propuesto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profesor::class, 'targetAttribute' => ['profesor_guia_propuesto_id' => 'id']],
            [['profesor_revisor1_propuesto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profesor::class, 'targetAttribute' => ['profesor_revisor1_propuesto_id' => 'id']],
            [['profesor_revisor2_propuesto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profesor::class, 'targetAttribute' => ['profesor_revisor2_propuesto_id' => 'id']],
            [['empresa_id'], 'exist', 'skipOnError' => true, 'targetClass' => Empresa::class, 'targetAttribute' => ['empresa_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'correlativo' => 'Correlativo',
            'origen_id' => 'Origen',
            'profesor_curso_id' => 'Profesor de Curso',
            'nota' => 'Nota',
            'modalidad_id' => 'Modalidad',
            'profesor_guia_propuesto_id' => 'Profesor Guía Propuesto',
            'profesor_revisor1_propuesto_id' => 'Profesor Revisor 1 Propuesto',
            'profesor_revisor2_propuesto_id' => 'Profesor Revisor 2 Propuesto',
            'empresa_id' => 'Empresa',
            'titulo' => 'Título',
            'documento_path' => 'Documento',
            'estado' => 'Estado',
            'fecha_creacion' => 'Fecha de Creación',
            'created_at' => 'Creado',
            'updated_at' => 'Actualizado',
        ];
    }

    /**
     * Gets query for [[Origen]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrigen()
    {
        return $this->hasOne(Origen::class, ['id' => 'origen_id']);
    }

    /**
     * Gets query for [[Modalidad]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModalidad()
    {
        return $this->hasOne(Modalidad::class, ['id' => 'modalidad_id']);
    }

    /**
     * Gets query for [[ProfesorCurso]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfesorCurso()
    {
        return $this->hasOne(Profesor::class, ['id' => 'profesor_curso_id']);
    }

    /**
     * Gets query for [[ProfesorGuiaPropuesto]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfesorGuiaPropuesto()
    {
        return $this->hasOne(Profesor::class, ['id' => 'profesor_guia_propuesto_id']);
    }

    /**
     * Gets query for [[ProfesorRevisor1Propuesto]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfesorRevisor1Propuesto()
    {
        return $this->hasOne(Profesor::class, ['id' => 'profesor_revisor1_propuesto_id']);
    }

    /**
     * Gets query for [[ProfesorRevisor2Propuesto]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfesorRevisor2Propuesto()
    {
        return $this->hasOne(Profesor::class, ['id' => 'profesor_revisor2_propuesto_id']);
    }

    /**
     * Gets query for [[Empresa]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmpresa()
    {
        return $this->hasOne(Empresa::class, ['id' => 'empresa_id']);
    }

    /**
     * Gets query for [[SttAlumnos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSttAlumnos()
    {
        return $this->hasMany(SttAlumno::class, ['stt_id' => 'id']);
    }

    /**
     * Gets query for [[Alumnos]] through SttAlumno.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAlumnos()
    {
        return $this->hasMany(Alumno::class, ['id' => 'alumno_id'])
            ->viaTable('stt_alumno', ['stt_id' => 'id']);
    }

    /**
     * Gets query for [[Tesis]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTesis()
    {
        return $this->hasOne(Tesis::class, ['stt_id' => 'id']);
    }

    /**
     * Gets query for [[Resoluciones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResoluciones()
    {
        return $this->hasMany(ResolucionStt::class, ['stt_id' => 'id']);
    }

    /**
     * Gets query for [[HistorialEstados]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHistorialEstados()
    {
        return $this->hasMany(HistorialEstado::class, ['stt_id' => 'id']);
    }
}
