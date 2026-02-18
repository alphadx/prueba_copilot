<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Form model for creating Solicitud de Tema de Tesis
 * 
 * This form handles dynamic validation based on modality:
 * - TT: Guides and reviewers are optional
 * - Papers: Guide is required
 * - Pasantía: Company information is required
 */
class SttForm extends Model
{
    // Basic fields
    public $origen_id;
    public $profesor_curso_id;
    public $nota;
    public $modalidad_id;
    public $titulo;
    
    // Student fields (support up to 2 students)
    public $alumno_1_id;
    public $carrera_1_id;
    public $alumno_2_id;
    public $carrera_2_id;
    
    // Professor fields
    public $profesor_guia_propuesto_id;
    public $profesor_revisor1_propuesto_id;
    public $profesor_revisor2_propuesto_id;
    
    // Company fields (for Pasantía modality)
    public $empresa_id;
    public $empresa_rut;
    public $empresa_nombre;
    public $empresa_supervisor_rut;
    public $empresa_supervisor_nombre;
    public $empresa_supervisor_correo;
    public $empresa_supervisor_telefono;
    public $empresa_supervisor_cargo;
    
    // For validation
    private $_modalidad;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // Required fields
            [['origen_id', 'profesor_curso_id', 'nota', 'modalidad_id', 'titulo', 'alumno_1_id', 'carrera_1_id'], 'required'],
            
            // Integer fields
            [['origen_id', 'profesor_curso_id', 'modalidad_id', 'alumno_1_id', 'carrera_1_id', 'alumno_2_id', 'carrera_2_id', 'profesor_guia_propuesto_id', 'profesor_revisor1_propuesto_id', 'profesor_revisor2_propuesto_id', 'empresa_id'], 'integer'],
            
            // Nota validation with automatic transformation
            [['nota'], 'number', 'min' => 1.0, 'max' => 70.0],
            [['nota'], 'filter', 'filter' => function($value) {
                // Transform grade from 10-70 range to 1.0-7.0 range
                if ($value >= 10 && $value <= 70) {
                    return $value / 10;
                }
                return $value;
            }],
            [['nota'], 'number', 'min' => 1.0, 'max' => 7.0, 'message' => 'La nota debe estar entre 1.0 y 7.0'],
            
            // String fields
            [['titulo'], 'string', 'max' => 500],
            [['empresa_rut', 'empresa_supervisor_rut'], 'string', 'max' => 12],
            [['empresa_nombre', 'empresa_supervisor_nombre', 'empresa_supervisor_correo', 'empresa_supervisor_cargo'], 'string', 'max' => 255],
            [['empresa_supervisor_telefono'], 'string', 'max' => 20],
            
            // Foreign key validations
            [['origen_id'], 'exist', 'skipOnError' => true, 'targetClass' => Origen::class, 'targetAttribute' => ['origen_id' => 'id']],
            [['modalidad_id'], 'exist', 'skipOnError' => true, 'targetClass' => Modalidad::class, 'targetAttribute' => ['modalidad_id' => 'id']],
            [['profesor_curso_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profesor::class, 'targetAttribute' => ['profesor_curso_id' => 'id']],
            [['profesor_guia_propuesto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profesor::class, 'targetAttribute' => ['profesor_guia_propuesto_id' => 'id']],
            [['profesor_revisor1_propuesto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profesor::class, 'targetAttribute' => ['profesor_revisor1_propuesto_id' => 'id']],
            [['profesor_revisor2_propuesto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profesor::class, 'targetAttribute' => ['profesor_revisor2_propuesto_id' => 'id']],
            [['alumno_1_id'], 'exist', 'skipOnError' => true, 'targetClass' => Alumno::class, 'targetAttribute' => ['alumno_1_id' => 'id']],
            [['alumno_2_id'], 'exist', 'skipOnError' => true, 'targetClass' => Alumno::class, 'targetAttribute' => ['alumno_2_id' => 'id']],
            [['carrera_1_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarreraMalla::class, 'targetAttribute' => ['carrera_1_id' => 'id']],
            [['carrera_2_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarreraMalla::class, 'targetAttribute' => ['carrera_2_id' => 'id']],
            [['empresa_id'], 'exist', 'skipOnError' => true, 'targetClass' => Empresa::class, 'targetAttribute' => ['empresa_id' => 'id']],
            
            // Custom validations
            [['alumno_1_id'], 'validateStudentNotInActiveTesis'],
            [['alumno_2_id'], 'validateStudentNotInActiveTesis', 'skipOnEmpty' => true],
            [['alumno_2_id'], 'validateSecondStudent', 'skipOnEmpty' => true],
            
            // Conditional validations based on modality
            [['profesor_guia_propuesto_id'], 'requiredForPapersAndPasantia'],
            [['empresa_rut', 'empresa_nombre', 'empresa_supervisor_rut', 'empresa_supervisor_nombre', 'empresa_supervisor_correo', 'empresa_supervisor_telefono', 'empresa_supervisor_cargo'], 'requiredForPasantia'],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'origen_id' => 'Origen',
            'profesor_curso_id' => 'Profesor de Curso',
            'nota' => 'Nota',
            'modalidad_id' => 'Modalidad',
            'titulo' => 'Título de la Tesis',
            'alumno_1_id' => 'Alumno 1',
            'carrera_1_id' => 'Carrera Alumno 1',
            'alumno_2_id' => 'Alumno 2 (Opcional)',
            'carrera_2_id' => 'Carrera Alumno 2',
            'profesor_guia_propuesto_id' => 'Profesor Guía Propuesto',
            'profesor_revisor1_propuesto_id' => 'Profesor Revisor 1 Propuesto',
            'profesor_revisor2_propuesto_id' => 'Profesor Revisor 2 Propuesto',
            'empresa_id' => 'Empresa (si existe)',
            'empresa_rut' => 'RUT Empresa',
            'empresa_nombre' => 'Nombre Empresa',
            'empresa_supervisor_rut' => 'RUT Supervisor',
            'empresa_supervisor_nombre' => 'Nombre Supervisor',
            'empresa_supervisor_correo' => 'Correo Supervisor',
            'empresa_supervisor_telefono' => 'Teléfono Supervisor',
            'empresa_supervisor_cargo' => 'Cargo Supervisor',
        ];
    }
    
    /**
     * Validates that student does not have an active thesis
     */
    public function validateStudentNotInActiveTesis($attribute)
    {
        $alumnoId = $this->$attribute;
        
        if (empty($alumnoId)) {
            return;
        }
        
        // Check if student has an active STT (not rejected or converted)
        $activeStt = SttAlumno::find()
            ->joinWith('stt')
            ->where(['stt_alumno.alumno_id' => $alumnoId])
            ->andWhere(['not in', 'solicitud_tema_tesis.estado', ['Rechazada', 'Convertida a TT']])
            ->exists();
        
        if ($activeStt) {
            $this->addError($attribute, 'El alumno ya tiene una solicitud de tema de tesis vigente.');
        }
    }
    
    /**
     * Validates that second student is different from first student
     */
    public function validateSecondStudent($attribute)
    {
        if (!empty($this->alumno_2_id) && $this->alumno_2_id == $this->alumno_1_id) {
            $this->addError($attribute, 'El segundo alumno debe ser diferente del primero.');
        }
        
        if (!empty($this->alumno_2_id) && empty($this->carrera_2_id)) {
            $this->addError('carrera_2_id', 'Debe seleccionar la carrera del segundo alumno.');
        }
    }
    
    /**
     * Validates that profesor_guia is required for Papers and Pasantía modalities
     */
    public function requiredForPapersAndPasantia($attribute)
    {
        $modalidad = $this->getModalidad();
        
        if ($modalidad && in_array($modalidad->nombre, ['Papers', 'Pasantía'])) {
            if (empty($this->$attribute)) {
                $this->addError($attribute, 'El profesor guía es obligatorio para la modalidad ' . $modalidad->nombre . '.');
            }
        }
    }
    
    /**
     * Validates that company fields are required for Pasantía modality
     */
    public function requiredForPasantia($attribute)
    {
        $modalidad = $this->getModalidad();
        
        if ($modalidad && $modalidad->nombre === 'Pasantía') {
            // Only require company fields if no existing company is selected
            if (empty($this->empresa_id) && empty($this->$attribute)) {
                $this->addError($attribute, 'Este campo es obligatorio para la modalidad Pasantía.');
            }
        }
    }
    
    /**
     * Get the Modalidad model
     */
    private function getModalidad()
    {
        if ($this->_modalidad === null && !empty($this->modalidad_id)) {
            $this->_modalidad = Modalidad::findOne($this->modalidad_id);
        }
        return $this->_modalidad;
    }
    
    /**
     * Saves the STT to database
     * @return SolicitudTemaTesis|null the saved model or null if saving failed
     */
    public function save()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Handle company creation for Pasantía if needed
            $empresaId = $this->empresa_id;
            if ($this->getModalidad()->nombre === 'Pasantía' && empty($empresaId)) {
                $empresa = new Empresa();
                $empresa->rut = $this->empresa_rut;
                $empresa->nombre = $this->empresa_nombre;
                $empresa->supervisor_rut = $this->empresa_supervisor_rut;
                $empresa->supervisor_nombre = $this->empresa_supervisor_nombre;
                $empresa->supervisor_correo = $this->empresa_supervisor_correo;
                $empresa->supervisor_telefono = $this->empresa_supervisor_telefono;
                $empresa->supervisor_cargo = $this->empresa_supervisor_cargo;
                
                if (!$empresa->save()) {
                    throw new \Exception('Error al guardar la empresa.');
                }
                $empresaId = $empresa->id;
            }
            
            // Create STT
            $stt = new SolicitudTemaTesis();
            $stt->origen_id = $this->origen_id;
            $stt->profesor_curso_id = $this->profesor_curso_id;
            $stt->nota = $this->nota;
            $stt->modalidad_id = $this->modalidad_id;
            $stt->titulo = $this->titulo;
            $stt->profesor_guia_propuesto_id = $this->profesor_guia_propuesto_id;
            $stt->profesor_revisor1_propuesto_id = $this->profesor_revisor1_propuesto_id;
            $stt->profesor_revisor2_propuesto_id = $this->profesor_revisor2_propuesto_id;
            $stt->empresa_id = $empresaId;
            $stt->estado = 'Enviada';
            
            // Generate correlativo
            $stt->correlativo = $this->generateCorrelativo();
            
            if (!$stt->save()) {
                throw new \Exception('Error al guardar la solicitud.');
            }
            
            // Create SttAlumno records
            $sttAlumno1 = new SttAlumno();
            $sttAlumno1->stt_id = $stt->id;
            $sttAlumno1->alumno_id = $this->alumno_1_id;
            $sttAlumno1->carrera_malla_id = $this->carrera_1_id;
            
            if (!$sttAlumno1->save()) {
                throw new \Exception('Error al asociar el primer alumno.');
            }
            
            // Second student if provided
            if (!empty($this->alumno_2_id)) {
                $sttAlumno2 = new SttAlumno();
                $sttAlumno2->stt_id = $stt->id;
                $sttAlumno2->alumno_id = $this->alumno_2_id;
                $sttAlumno2->carrera_malla_id = $this->carrera_2_id;
                
                if (!$sttAlumno2->save()) {
                    throw new \Exception('Error al asociar el segundo alumno.');
                }
            }
            
            $transaction->commit();
            return $stt;
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            return null;
        }
    }
    
    /**
     * Generates a unique correlativo number for the STT
     * Format: STT-YYYY-NNNN (e.g., STT-2026-0001)
     */
    private function generateCorrelativo()
    {
        $year = date('Y');
        $prefix = "STT-{$year}-";
        
        // Get the last correlativo for this year
        $lastStt = SolicitudTemaTesis::find()
            ->where(['like', 'correlativo', $prefix])
            ->orderBy(['correlativo' => SORT_DESC])
            ->one();
        
        if ($lastStt && preg_match('/STT-\d{4}-(\d{4})/', $lastStt->correlativo, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }
        
        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
