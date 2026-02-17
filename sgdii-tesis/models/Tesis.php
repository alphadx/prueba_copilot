<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tesis".
 *
 * @property int $id
 * @property int $stt_id
 * @property int $categoria_id
 * @property int $subcategoria_id
 * @property int $profesor_guia_id
 * @property int $profesor_revisor1_id
 * @property int $profesor_revisor2_id
 * @property int $total_etapas
 * @property int $etapa_actual
 * @property string $estado
 * @property string $resolucion_motivo
 * @property string $fecha_aceptacion
 * @property string $fecha_ultima_actualizacion
 * @property string $created_at
 * @property string $updated_at
 *
 * @property SolicitudTemaTesis $stt
 * @property Categoria $categoria
 * @property Subcategoria $subcategoria
 * @property Profesor $profesorGuia
 * @property Profesor $profesorRevisor1
 * @property Profesor $profesorRevisor2
 * @property HistorialEstado[] $historialEstados
 */
class Tesis extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tesis';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stt_id', 'profesor_guia_id'], 'required'],
            [['stt_id', 'categoria_id', 'subcategoria_id', 'profesor_guia_id', 'profesor_revisor1_id', 'profesor_revisor2_id', 'total_etapas', 'etapa_actual'], 'integer'],
            [['resolucion_motivo'], 'string'],
            [['fecha_aceptacion', 'fecha_ultima_actualizacion', 'created_at', 'updated_at'], 'safe'],
            [['estado'], 'string', 'max' => 50],
            [['stt_id'], 'unique'],
            [['total_etapas'], 'compare', 'compareValue' => 1, 'operator' => '>='],
            [['etapa_actual'], 'compare', 'compareValue' => 1, 'operator' => '>='],
            [['stt_id'], 'exist', 'skipOnError' => true, 'targetClass' => SolicitudTemaTesis::class, 'targetAttribute' => ['stt_id' => 'id']],
            [['categoria_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categoria::class, 'targetAttribute' => ['categoria_id' => 'id']],
            [['subcategoria_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subcategoria::class, 'targetAttribute' => ['subcategoria_id' => 'id']],
            [['profesor_guia_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profesor::class, 'targetAttribute' => ['profesor_guia_id' => 'id']],
            [['profesor_revisor1_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profesor::class, 'targetAttribute' => ['profesor_revisor1_id' => 'id']],
            [['profesor_revisor2_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profesor::class, 'targetAttribute' => ['profesor_revisor2_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stt_id' => 'Solicitud de Tema de Tesis',
            'categoria_id' => 'Categoría',
            'subcategoria_id' => 'Subcategoría',
            'profesor_guia_id' => 'Profesor Guía',
            'profesor_revisor1_id' => 'Profesor Revisor 1',
            'profesor_revisor2_id' => 'Profesor Revisor 2',
            'total_etapas' => 'Total de Etapas',
            'etapa_actual' => 'Etapa Actual',
            'estado' => 'Estado',
            'resolucion_motivo' => 'Motivo de Resolución',
            'fecha_aceptacion' => 'Fecha de Aceptación',
            'fecha_ultima_actualizacion' => 'Fecha de Última Actualización',
            'created_at' => 'Creado',
            'updated_at' => 'Actualizado',
        ];
    }

    /**
     * Gets query for [[Stt]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStt()
    {
        return $this->hasOne(SolicitudTemaTesis::class, ['id' => 'stt_id']);
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
     * Gets query for [[Subcategoria]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubcategoria()
    {
        return $this->hasOne(Subcategoria::class, ['id' => 'subcategoria_id']);
    }

    /**
     * Gets query for [[ProfesorGuia]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfesorGuia()
    {
        return $this->hasOne(Profesor::class, ['id' => 'profesor_guia_id']);
    }

    /**
     * Gets query for [[ProfesorRevisor1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfesorRevisor1()
    {
        return $this->hasOne(Profesor::class, ['id' => 'profesor_revisor1_id']);
    }

    /**
     * Gets query for [[ProfesorRevisor2]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfesorRevisor2()
    {
        return $this->hasOne(Profesor::class, ['id' => 'profesor_revisor2_id']);
    }

    /**
     * Gets query for [[HistorialEstados]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHistorialEstados()
    {
        return $this->hasMany(HistorialEstado::class, ['tesis_id' => 'id']);
    }

    /**
     * Calculate percentage based on current stage.
     *
     * @return int Percentage (0-100)
     */
    public function getPorcentaje()
    {
        if ($this->total_etapas <= 0) {
            return 0;
        }
        return (int) round(($this->etapa_actual / $this->total_etapas) * 100);
    }

    /**
     * Get human-readable stage label.
     *
     * @return string Stage label like "Etapa 2 de 3 (67%)"
     */
    public function getEtapaLabel()
    {
        $porcentaje = $this->getPorcentaje();
        return "Etapa {$this->etapa_actual} de {$this->total_etapas} ({$porcentaje}%)";
    }

    /**
     * Check if the thesis can advance to next stage.
     *
     * @return bool True if can advance (not at last stage and current review is accepted)
     */
    public function puedeAvanzarEtapa()
    {
        // Can only advance if not at the last stage
        return $this->etapa_actual < $this->total_etapas;
    }
}
