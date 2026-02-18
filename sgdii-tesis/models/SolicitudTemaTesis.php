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
 * @property string $motivo_resolucion
 * @property string $observaciones
 * @property string $fecha_resolucion
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
            [['motivo_resolucion', 'observaciones'], 'string'],
            [['fecha_creacion', 'fecha_resolucion', 'created_at', 'updated_at'], 'safe'],
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
            'motivo_resolucion' => 'Motivo de Resolución',
            'observaciones' => 'Observaciones',
            'fecha_resolucion' => 'Fecha de Resolución',
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

    /**
     * Constants for STT states
     */
    const ESTADO_SOLICITADA = 'Solicitada';
    const ESTADO_EN_REVISION = 'En revisión';
    const ESTADO_ACEPTADA = 'Aceptada';
    const ESTADO_ACEPTADA_CON_OBSERVACIONES = 'Aceptada con observaciones';
    const ESTADO_RECHAZADA = 'Rechazada';
    const ESTADO_CONVERTIDA_A_TT = 'Convertida a TT';

    /**
     * Get all possible states
     * @return array
     */
    public static function getEstados()
    {
        return [
            self::ESTADO_SOLICITADA => 'Solicitada',
            self::ESTADO_EN_REVISION => 'En revisión',
            self::ESTADO_ACEPTADA => 'Aceptada',
            self::ESTADO_ACEPTADA_CON_OBSERVACIONES => 'Aceptada con observaciones',
            self::ESTADO_RECHAZADA => 'Rechazada',
            self::ESTADO_CONVERTIDA_A_TT => 'Convertida a TT',
        ];
    }

    /**
     * Get pending states (estados that can be resolved)
     * @return array
     */
    public static function getEstadosPendientes()
    {
        return [
            self::ESTADO_SOLICITADA,
            self::ESTADO_EN_REVISION,
        ];
    }

    /**
     * Mark STT as under review
     * @param int $userId User who performed the action
     * @return bool
     */
    public function marcarEnRevision($userId)
    {
        $oldState = $this->estado;
        $this->estado = self::ESTADO_EN_REVISION;
        
        if ($this->save(false)) {
            $this->registrarHistorial($oldState, $this->estado, $userId, 'STT marcada en revisión');
            return true;
        }
        return false;
    }

    /**
     * Resolve STT as accepted
     * @param int $userId User who performed the action
     * @return bool
     */
    public function aceptar($userId, $motivo = null)
    {
        $oldState = $this->estado;
        $this->estado = self::ESTADO_ACEPTADA;
        $this->fecha_resolucion = date('Y-m-d H:i:s');
        if ($motivo) {
            $this->motivo_resolucion = $motivo;
        }
        
        if ($this->save(false)) {
            $this->registrarHistorial($oldState, $this->estado, $userId, $motivo ?: 'STT aceptada');
            return true;
        }
        return false;
    }

    /**
     * Resolve STT as accepted with observations
     * @param int $userId User who performed the action
     * @param string $observaciones Observations
     * @return bool
     */
    public function aceptarConObservaciones($userId, $observaciones)
    {
        $oldState = $this->estado;
        $this->estado = self::ESTADO_ACEPTADA_CON_OBSERVACIONES;
        $this->fecha_resolucion = date('Y-m-d H:i:s');
        $this->observaciones = $observaciones;
        
        if ($this->save(false)) {
            $this->registrarHistorial($oldState, $this->estado, $userId, $observaciones);
            return true;
        }
        return false;
    }

    /**
     * Resolve STT as rejected
     * @param int $userId User who performed the action
     * @param string $motivo Reason for rejection
     * @return bool
     */
    public function rechazar($userId, $motivo)
    {
        $oldState = $this->estado;
        $this->estado = self::ESTADO_RECHAZADA;
        $this->fecha_resolucion = date('Y-m-d H:i:s');
        $this->motivo_resolucion = $motivo;
        
        if ($this->save(false)) {
            $this->registrarHistorial($oldState, $this->estado, $userId, $motivo);
            return true;
        }
        return false;
    }

    /**
     * Register state change in history
     * @param string $oldState
     * @param string $newState
     * @param int $userId
     * @param string $motivo
     */
    private function registrarHistorial($oldState, $newState, $userId, $motivo = null)
    {
        $historial = new HistorialEstado();
        $historial->stt_id = $this->id;
        $historial->estado_anterior = $oldState;
        $historial->estado_nuevo = $newState;
        $historial->motivo = $motivo;
        $historial->usuario_id = $userId;
        $historial->fecha = date('Y-m-d H:i:s');
        $historial->save(false);
    }

    /**
     * Check if STT can be resolved
     * @return bool
     */
    public function puedeSerResuelta()
    {
        return in_array($this->estado, self::getEstadosPendientes());
    }
}
