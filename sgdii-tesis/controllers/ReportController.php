<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use app\models\SolicitudTemaTesis;
use app\models\Tesis;
use app\models\Profesor;
use app\models\Alumno;
use app\models\Modalidad;
use app\models\Categoria;
use app\models\HistorialEstado;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use kartik\mpdf\Pdf;

/**
 * Controller for Reports and Statistics
 */
class ReportController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Reports dashboard - shows available reports based on user role
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        
        return $this->render('index', [
            'user' => $user,
        ]);
    }

    /**
     * Professor report - theses under supervision
     */
    public function actionProfesor($profesor_id = null)
    {
        $user = Yii::$app->user->identity;
        
        // If no profesor_id provided, try to get current user's profesor record
        if (!$profesor_id) {
            if ($user->rol === 'admin') {
                // Admin can view all professors
                $profesor_id = Yii::$app->request->get('profesor_id');
            } else {
                $profesor = Profesor::findOne(['user_id' => $user->id]);
                if (!$profesor) {
                    throw new ForbiddenHttpException('No tiene permisos para acceder a este reporte.');
                }
                $profesor_id = $profesor->id;
            }
        } else {
            // Verify access
            if ($user->rol !== 'admin') {
                $profesor = Profesor::findOne(['user_id' => $user->id, 'id' => $profesor_id]);
                if (!$profesor) {
                    throw new ForbiddenHttpException('No tiene permisos para acceder a este reporte.');
                }
            }
        }
        
        if (!$profesor_id) {
            return $this->render('profesor', [
                'profesor' => null,
                'tesisComoGuia' => [],
                'tesisComoRevisor' => [],
                'estadisticas' => [],
                'profesores' => Profesor::find()->where(['activo' => 1])->all(),
            ]);
        }
        
        $profesor = Profesor::findOne($profesor_id);
        
        // Get theses where professor is guide
        $tesisComoGuia = Tesis::find()
            ->where(['profesor_guia_id' => $profesor_id])
            ->with(['stt', 'stt.alumnos', 'stt.modalidad', 'categoria'])
            ->all();
        
        // Get theses where professor is reviewer
        $tesisComoRevisor = Tesis::find()
            ->where(['or',
                ['profesor_revisor1_id' => $profesor_id],
                ['profesor_revisor2_id' => $profesor_id]
            ])
            ->with(['stt', 'stt.alumnos', 'stt.modalidad', 'categoria'])
            ->all();
        
        // Calculate statistics
        $estadisticas = [
            'total_como_guia' => count($tesisComoGuia),
            'total_como_revisor' => count($tesisComoRevisor),
            'total_general' => count($tesisComoGuia) + count($tesisComoRevisor),
            'tesis_en_curso' => count(array_filter($tesisComoGuia, function($t) { return $t->estado !== 'Finalizada'; })) +
                               count(array_filter($tesisComoRevisor, function($t) { return $t->estado !== 'Finalizada'; })),
            'tesis_finalizadas' => count(array_filter($tesisComoGuia, function($t) { return $t->estado === 'Finalizada'; })) +
                                  count(array_filter($tesisComoRevisor, function($t) { return $t->estado === 'Finalizada'; })),
        ];
        
        return $this->render('profesor', [
            'profesor' => $profesor,
            'tesisComoGuia' => $tesisComoGuia,
            'tesisComoRevisor' => $tesisComoRevisor,
            'estadisticas' => $estadisticas,
            'profesores' => Profesor::find()->where(['activo' => 1])->all(),
        ]);
    }

    /**
     * Committee report - all theses with advanced filters
     */
    public function actionComision()
    {
        $user = Yii::$app->user->identity;
        
        // Check if user has committee access
        $canAccess = false;
        if ($user->rol === 'admin') {
            $canAccess = true;
        } elseif ($user->rol === 'profesor' || $user->rol === 'comision_evaluadora') {
            $profesor = Profesor::findOne(['user_id' => $user->id]);
            $canAccess = $profesor && $profesor->es_comision_evaluadora == 1;
        }
        
        if (!$canAccess) {
            throw new ForbiddenHttpException('No tiene permisos para acceder a este reporte.');
        }
        
        $request = Yii::$app->request;
        
        // Build query for STT
        $query = SolicitudTemaTesis::find()
            ->joinWith(['modalidad', 'profesorGuiaPropuesto', 'alumnos']);
        
        // Apply filters
        $modalidadId = $request->get('modalidad_id');
        if ($modalidadId) {
            $query->andWhere(['modalidad_id' => $modalidadId]);
        }
        
        $estado = $request->get('estado');
        if ($estado) {
            $query->andWhere(['estado' => $estado]);
        }
        
        $fechaDesde = $request->get('fecha_desde');
        if ($fechaDesde) {
            $query->andWhere(['>=', 'fecha_creacion', $fechaDesde . ' 00:00:00']);
        }
        
        $fechaHasta = $request->get('fecha_hasta');
        if ($fechaHasta) {
            $query->andWhere(['<=', 'fecha_creacion', $fechaHasta . ' 23:59:59']);
        }
        
        $profesorGuiaId = $request->get('profesor_guia_id');
        if ($profesorGuiaId) {
            $query->andWhere(['profesor_guia_propuesto_id' => $profesorGuiaId]);
        }
        
        $query->orderBy(['fecha_creacion' => SORT_DESC]);
        
        $solicitudes = $query->all();
        
        // Calculate statistics
        $estadisticas = $this->calcularEstadisticasGenerales();
        
        return $this->render('comision', [
            'solicitudes' => $solicitudes,
            'modalidades' => Modalidad::find()->where(['activo' => 1])->all(),
            'profesores' => Profesor::find()->where(['activo' => 1])->all(),
            'estados' => SolicitudTemaTesis::getEstados(),
            'estadisticas' => $estadisticas,
            'filters' => [
                'modalidad_id' => $modalidadId,
                'estado' => $estado,
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
                'profesor_guia_id' => $profesorGuiaId,
            ],
        ]);
    }

    /**
     * Student report - personal thesis progress
     */
    public function actionEstudiante($alumno_id = null)
    {
        $user = Yii::$app->user->identity;
        
        // If no alumno_id provided, try to get current user's alumno record
        if (!$alumno_id) {
            if ($user->rol === 'admin') {
                // Admin can view all students
                $alumno_id = Yii::$app->request->get('alumno_id');
            } else {
                $alumno = Alumno::findOne(['user_id' => $user->id]);
                if (!$alumno) {
                    throw new ForbiddenHttpException('No tiene permisos para acceder a este reporte.');
                }
                $alumno_id = $alumno->id;
            }
        } else {
            // Verify access
            if ($user->rol !== 'admin') {
                $alumno = Alumno::findOne(['user_id' => $user->id, 'id' => $alumno_id]);
                if (!$alumno) {
                    throw new ForbiddenHttpException('No tiene permisos para acceder a este reporte.');
                }
            }
        }
        
        if (!$alumno_id) {
            return $this->render('estudiante', [
                'alumno' => null,
                'solicitudes' => [],
                'tesis' => null,
                'alumnos' => Alumno::find()->all(),
            ]);
        }
        
        $alumno = Alumno::findOne($alumno_id);
        
        // Get all STT for this student
        $solicitudes = SolicitudTemaTesis::find()
            ->joinWith('sttAlumnos')
            ->where(['stt_alumno.alumno_id' => $alumno_id])
            ->with(['modalidad', 'profesorGuiaPropuesto', 'tesis', 'historialEstados'])
            ->orderBy(['fecha_creacion' => SORT_DESC])
            ->all();
        
        // Get current active thesis
        $tesis = null;
        foreach ($solicitudes as $stt) {
            if ($stt->tesis && $stt->tesis->estado !== 'Finalizada') {
                $tesis = $stt->tesis;
                break;
            }
        }
        
        return $this->render('estudiante', [
            'alumno' => $alumno,
            'solicitudes' => $solicitudes,
            'tesis' => $tesis,
            'alumnos' => Alumno::find()->all(),
        ]);
    }

    /**
     * Statistics dashboard with charts
     */
    public function actionEstadisticas()
    {
        $estadisticas = $this->calcularEstadisticasGenerales();
        
        // Get data for charts
        $chartsData = [
            'modalidades' => $this->getModalidadesDistribution(),
            'categorias' => $this->getCategoriasDistribution(),
            'evolucionMensual' => $this->getEvolucionMensual(),
            'modalidadEstado' => $this->getModalidadPorEstado(),
            'tiemposResolucion' => $this->getTiemposResolucion(),
        ];
        
        return $this->render('estadisticas', [
            'estadisticas' => $estadisticas,
            'chartsData' => $chartsData,
        ]);
    }

    /**
     * Export professor report to Excel
     */
    public function actionExportProfesorExcel($profesor_id)
    {
        $profesor = Profesor::findOne($profesor_id);
        if (!$profesor) {
            throw new \yii\web\NotFoundHttpException('Profesor no encontrado.');
        }
        
        // Get data
        $tesisComoGuia = Tesis::find()
            ->where(['profesor_guia_id' => $profesor_id])
            ->with(['stt', 'stt.alumnos', 'stt.modalidad', 'categoria'])
            ->all();
        
        $tesisComoRevisor = Tesis::find()
            ->where(['or',
                ['profesor_revisor1_id' => $profesor_id],
                ['profesor_revisor2_id' => $profesor_id]
            ])
            ->with(['stt', 'stt.alumnos', 'stt.modalidad', 'categoria'])
            ->all();
        
        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Headers
        $sheet->setCellValue('A1', 'Reporte de Tesis - Profesor: ' . $profesor->nombre);
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        $sheet->setCellValue('A3', 'Tesis como Guía');
        $sheet->mergeCells('A3:G3');
        $sheet->getStyle('A3')->getFont()->setBold(true);
        
        $row = 4;
        $sheet->setCellValue('A' . $row, 'Correlativo');
        $sheet->setCellValue('B' . $row, 'Título');
        $sheet->setCellValue('C' . $row, 'Modalidad');
        $sheet->setCellValue('D' . $row, 'Categoría');
        $sheet->setCellValue('E' . $row, 'Alumnos');
        $sheet->setCellValue('F' . $row, 'Estado');
        $sheet->setCellValue('G' . $row, 'Etapa');
        $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
        
        $row++;
        foreach ($tesisComoGuia as $tesis) {
            $alumnos = implode(', ', array_map(function($a) { return $a->nombre; }, $tesis->stt->alumnos));
            $sheet->setCellValue('A' . $row, $tesis->stt->correlativo);
            $sheet->setCellValue('B' . $row, $tesis->stt->titulo);
            $sheet->setCellValue('C' . $row, $tesis->stt->modalidad->nombre);
            $sheet->setCellValue('D' . $row, $tesis->categoria ? $tesis->categoria->nombre : 'N/A');
            $sheet->setCellValue('E' . $row, $alumnos);
            $sheet->setCellValue('F' . $row, $tesis->estado);
            $sheet->setCellValue('G' . $row, $tesis->getEtapaLabel());
            $row++;
        }
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Tesis como Revisor');
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Correlativo');
        $sheet->setCellValue('B' . $row, 'Título');
        $sheet->setCellValue('C' . $row, 'Modalidad');
        $sheet->setCellValue('D' . $row, 'Categoría');
        $sheet->setCellValue('E' . $row, 'Alumnos');
        $sheet->setCellValue('F' . $row, 'Estado');
        $sheet->setCellValue('G' . $row, 'Etapa');
        $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
        
        $row++;
        foreach ($tesisComoRevisor as $tesis) {
            $alumnos = implode(', ', array_map(function($a) { return $a->nombre; }, $tesis->stt->alumnos));
            $sheet->setCellValue('A' . $row, $tesis->stt->correlativo);
            $sheet->setCellValue('B' . $row, $tesis->stt->titulo);
            $sheet->setCellValue('C' . $row, $tesis->stt->modalidad->nombre);
            $sheet->setCellValue('D' . $row, $tesis->categoria ? $tesis->categoria->nombre : 'N/A');
            $sheet->setCellValue('E' . $row, $alumnos);
            $sheet->setCellValue('F' . $row, $tesis->estado);
            $sheet->setCellValue('G' . $row, $tesis->getEtapaLabel());
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Create file
        $writer = new Xlsx($spreadsheet);
        $filename = 'reporte_profesor_' . $profesor->id . '_' . date('Y-m-d') . '.xlsx';
        $tempFile = Yii::getAlias('@runtime') . '/' . $filename;
        $writer->save($tempFile);
        
        // Send to browser
        return Yii::$app->response->sendFile($tempFile, $filename, [
            'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'inline' => false,
        ]);
    }

    /**
     * Export professor report to PDF
     */
    public function actionExportProfesorPdf($profesor_id)
    {
        $profesor = Profesor::findOne($profesor_id);
        if (!$profesor) {
            throw new \yii\web\NotFoundHttpException('Profesor no encontrado.');
        }
        
        // Get data
        $tesisComoGuia = Tesis::find()
            ->where(['profesor_guia_id' => $profesor_id])
            ->with(['stt', 'stt.alumnos', 'stt.modalidad', 'categoria'])
            ->all();
        
        $tesisComoRevisor = Tesis::find()
            ->where(['or',
                ['profesor_revisor1_id' => $profesor_id],
                ['profesor_revisor2_id' => $profesor_id]
            ])
            ->with(['stt', 'stt.alumnos', 'stt.modalidad', 'categoria'])
            ->all();
        
        // Render view for PDF
        $content = $this->renderPartial('_profesor_pdf', [
            'profesor' => $profesor,
            'tesisComoGuia' => $tesisComoGuia,
            'tesisComoRevisor' => $tesisComoRevisor,
        ]);
        
        // Generate PDF
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => 'body { font-family: Arial; font-size: 10pt; }',
            'options' => ['title' => 'Reporte de Profesor'],
            'methods' => [
                'SetTitle' => 'Reporte de Profesor - ' . $profesor->nombre,
                'SetAuthor' => 'SGDII - Módulo Tesis',
            ],
            'filename' => 'reporte_profesor_' . $profesor->id . '_' . date('Y-m-d') . '.pdf',
        ]);
        
        return $pdf->render();
    }

    /**
     * Export committee report to Excel
     */
    public function actionExportComisionExcel()
    {
        $request = Yii::$app->request;
        
        // Build query
        $query = SolicitudTemaTesis::find()
            ->joinWith(['modalidad', 'profesorGuiaPropuesto', 'alumnos']);
        
        // Apply same filters as in actionComision
        $modalidadId = $request->get('modalidad_id');
        if ($modalidadId) {
            $query->andWhere(['modalidad_id' => $modalidadId]);
        }
        
        $estado = $request->get('estado');
        if ($estado) {
            $query->andWhere(['estado' => $estado]);
        }
        
        $fechaDesde = $request->get('fecha_desde');
        if ($fechaDesde) {
            $query->andWhere(['>=', 'fecha_creacion', $fechaDesde . ' 00:00:00']);
        }
        
        $fechaHasta = $request->get('fecha_hasta');
        if ($fechaHasta) {
            $query->andWhere(['<=', 'fecha_creacion', $fechaHasta . ' 23:59:59']);
        }
        
        $profesorGuiaId = $request->get('profesor_guia_id');
        if ($profesorGuiaId) {
            $query->andWhere(['profesor_guia_propuesto_id' => $profesorGuiaId]);
        }
        
        $query->orderBy(['fecha_creacion' => SORT_DESC]);
        
        $solicitudes = $query->all();
        
        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Headers
        $sheet->setCellValue('A1', 'Reporte de Solicitudes de Tema de Tesis - Comisión Evaluadora');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        $row = 3;
        $sheet->setCellValue('A' . $row, 'Correlativo');
        $sheet->setCellValue('B' . $row, 'Título');
        $sheet->setCellValue('C' . $row, 'Modalidad');
        $sheet->setCellValue('D' . $row, 'Estado');
        $sheet->setCellValue('E' . $row, 'Alumnos');
        $sheet->setCellValue('F' . $row, 'Profesor Guía');
        $sheet->setCellValue('G' . $row, 'Fecha Creación');
        $sheet->setCellValue('H' . $row, 'Fecha Resolución');
        $sheet->setCellValue('I' . $row, 'Motivo Resolución');
        $sheet->getStyle('A' . $row . ':I' . $row)->getFont()->setBold(true);
        
        $row++;
        foreach ($solicitudes as $stt) {
            $alumnos = implode(', ', array_map(function($a) { return $a->nombre; }, $stt->alumnos));
            $sheet->setCellValue('A' . $row, $stt->correlativo);
            $sheet->setCellValue('B' . $row, $stt->titulo);
            $sheet->setCellValue('C' . $row, $stt->modalidad->nombre);
            $sheet->setCellValue('D' . $row, $stt->estado);
            $sheet->setCellValue('E' . $row, $alumnos);
            $sheet->setCellValue('F' . $row, $stt->profesorGuiaPropuesto ? $stt->profesorGuiaPropuesto->nombre : 'N/A');
            $sheet->setCellValue('G' . $row, $stt->fecha_creacion);
            $sheet->setCellValue('H' . $row, $stt->fecha_resolucion ?? 'N/A');
            $sheet->setCellValue('I' . $row, $stt->motivo_resolucion ?? 'N/A');
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Create file
        $writer = new Xlsx($spreadsheet);
        $filename = 'reporte_comision_' . date('Y-m-d') . '.xlsx';
        $tempFile = Yii::getAlias('@runtime') . '/' . $filename;
        $writer->save($tempFile);
        
        // Send to browser
        return Yii::$app->response->sendFile($tempFile, $filename, [
            'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'inline' => false,
        ]);
    }

    /**
     * Export committee report to PDF
     */
    public function actionExportComisionPdf()
    {
        $request = Yii::$app->request;
        
        // Build query (same as Excel export)
        $query = SolicitudTemaTesis::find()
            ->joinWith(['modalidad', 'profesorGuiaPropuesto', 'alumnos']);
        
        $modalidadId = $request->get('modalidad_id');
        if ($modalidadId) {
            $query->andWhere(['modalidad_id' => $modalidadId]);
        }
        
        $estado = $request->get('estado');
        if ($estado) {
            $query->andWhere(['estado' => $estado]);
        }
        
        $fechaDesde = $request->get('fecha_desde');
        if ($fechaDesde) {
            $query->andWhere(['>=', 'fecha_creacion', $fechaDesde . ' 00:00:00']);
        }
        
        $fechaHasta = $request->get('fecha_hasta');
        if ($fechaHasta) {
            $query->andWhere(['<=', 'fecha_creacion', $fechaHasta . ' 23:59:59']);
        }
        
        $profesorGuiaId = $request->get('profesor_guia_id');
        if ($profesorGuiaId) {
            $query->andWhere(['profesor_guia_propuesto_id' => $profesorGuiaId]);
        }
        
        $query->orderBy(['fecha_creacion' => SORT_DESC]);
        
        $solicitudes = $query->all();
        
        // Render view for PDF
        $content = $this->renderPartial('_comision_pdf', [
            'solicitudes' => $solicitudes,
        ]);
        
        // Generate PDF
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => 'body { font-family: Arial; font-size: 8pt; } table { font-size: 8pt; }',
            'options' => ['title' => 'Reporte Comisión'],
            'methods' => [
                'SetTitle' => 'Reporte de Comisión Evaluadora',
                'SetAuthor' => 'SGDII - Módulo Tesis',
            ],
            'filename' => 'reporte_comision_' . date('Y-m-d') . '.pdf',
        ]);
        
        return $pdf->render();
    }

    /**
     * Calculate general statistics
     */
    private function calcularEstadisticasGenerales()
    {
        $totalSTT = SolicitudTemaTesis::find()->count();
        $totalTesis = Tesis::find()->count();
        
        $estadisticas = [
            'total_stt' => $totalSTT,
            'total_tesis' => $totalTesis,
            'stt_por_estado' => [],
            'tesis_por_modalidad' => [],
            'promedio_tiempo_resolucion' => 0,
            'tasa_aceptacion' => 0,
            'tasa_rechazo' => 0,
            'promedio_revisores' => 0,
        ];
        
        // STT by status
        $estados = SolicitudTemaTesis::getEstados();
        foreach ($estados as $estado => $label) {
            $count = SolicitudTemaTesis::find()->where(['estado' => $estado])->count();
            $estadisticas['stt_por_estado'][$estado] = $count;
        }
        
        // Tesis by modality
        $modalidades = Modalidad::find()->where(['activo' => 1])->all();
        foreach ($modalidades as $modalidad) {
            $count = SolicitudTemaTesis::find()
                ->where(['modalidad_id' => $modalidad->id])
                ->count();
            $estadisticas['tesis_por_modalidad'][$modalidad->nombre] = $count;
        }
        
        // Average resolution time (in days)
        $resueltas = SolicitudTemaTesis::find()
            ->where(['not', ['fecha_resolucion' => null]])
            ->all();
        
        if (count($resueltas) > 0) {
            $totalDias = 0;
            foreach ($resueltas as $stt) {
                $fechaCreacion = new \DateTime($stt->fecha_creacion);
                $fechaResolucion = new \DateTime($stt->fecha_resolucion);
                $diff = $fechaCreacion->diff($fechaResolucion);
                $totalDias += $diff->days;
            }
            $estadisticas['promedio_tiempo_resolucion'] = round($totalDias / count($resueltas), 1);
        }
        
        // Acceptance and rejection rates
        $aceptadas = SolicitudTemaTesis::find()
            ->where(['or',
                ['estado' => SolicitudTemaTesis::ESTADO_ACEPTADA],
                ['estado' => SolicitudTemaTesis::ESTADO_ACEPTADA_CON_OBSERVACIONES]
            ])
            ->count();
        
        $rechazadas = SolicitudTemaTesis::find()
            ->where(['estado' => SolicitudTemaTesis::ESTADO_RECHAZADA])
            ->count();
        
        if ($totalSTT > 0) {
            $estadisticas['tasa_aceptacion'] = round(($aceptadas / $totalSTT) * 100, 1);
            $estadisticas['tasa_rechazo'] = round(($rechazadas / $totalSTT) * 100, 1);
        }
        
        // Average reviewers per thesis
        $tesisConRevisores = Tesis::find()->all();
        if (count($tesisConRevisores) > 0) {
            $totalRevisores = 0;
            foreach ($tesisConRevisores as $tesis) {
                if ($tesis->profesor_revisor1_id) $totalRevisores++;
                if ($tesis->profesor_revisor2_id) $totalRevisores++;
            }
            $estadisticas['promedio_revisores'] = round($totalRevisores / count($tesisConRevisores), 1);
        }
        
        return $estadisticas;
    }

    /**
     * Get modality distribution for charts
     */
    private function getModalidadesDistribution()
    {
        $modalidades = Modalidad::find()->where(['activo' => 1])->all();
        $data = [
            'labels' => [],
            'values' => [],
        ];
        
        foreach ($modalidades as $modalidad) {
            $count = SolicitudTemaTesis::find()
                ->where(['modalidad_id' => $modalidad->id])
                ->count();
            $data['labels'][] = $modalidad->nombre;
            $data['values'][] = $count;
        }
        
        return $data;
    }

    /**
     * Get category distribution for charts
     * Enhanced to show all active categories and handle edge cases
     */
    private function getCategoriasDistribution()
    {
        try {
            $categorias = Categoria::find()->where(['activo' => 1])->all();
            $data = [
                'labels' => [],
                'values' => [],
            ];
            
            if (empty($categorias)) {
                Yii::warning('No active categories found in database', __METHOD__);
                return $data;
            }
            
            foreach ($categorias as $categoria) {
                $count = Tesis::find()
                    ->where(['categoria_id' => $categoria->id])
                    ->count();
                
                // Include all categories, even with 0 thesis
                $data['labels'][] = $categoria->nombre;
                $data['values'][] = (int)$count;
            }
            
            return $data;
        } catch (\Exception $e) {
            Yii::error('Error loading category distribution: ' . $e->getMessage(), __METHOD__);
            // Return empty data structure for consistency
            return [
                'labels' => [],
                'values' => [],
            ];
        }
    }

    /**
     * Get monthly evolution of STT
     */
    private function getEvolucionMensual()
    {
        $data = [
            'labels' => [],
            'values' => [],
        ];
        
        // Get last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = new \DateTime();
            $date->modify("-$i months");
            $year = $date->format('Y');
            $month = $date->format('m');
            
            $count = SolicitudTemaTesis::find()
                ->where(['like', 'fecha_creacion', "$year-$month", false])
                ->count();
            
            $data['labels'][] = $date->format('M Y');
            $data['values'][] = $count;
        }
        
        return $data;
    }

    /**
     * Get modality by state distribution
     */
    private function getModalidadPorEstado()
    {
        $modalidades = Modalidad::find()->where(['activo' => 1])->all();
        $estados = ['Solicitada', 'En revisión', 'Aceptada', 'Aceptada con observaciones', 'Rechazada'];
        
        $data = [
            'labels' => array_map(function($m) { return $m->nombre; }, $modalidades),
            'datasets' => [],
        ];
        
        foreach ($estados as $estado) {
            $values = [];
            foreach ($modalidades as $modalidad) {
                $count = SolicitudTemaTesis::find()
                    ->where(['modalidad_id' => $modalidad->id, 'estado' => $estado])
                    ->count();
                $values[] = $count;
            }
            $data['datasets'][] = [
                'label' => $estado,
                'values' => $values,
            ];
        }
        
        return $data;
    }

    /**
     * Get resolution times data
     */
    private function getTiemposResolucion()
    {
        // Only get professors who have resolved at least one STT
        $profesores = Profesor::find()
            ->where(['activo' => 1])
            ->andWhere(['or',
                ['exists', 
                    SolicitudTemaTesis::find()
                        ->where(['profesor_guia_propuesto_id' => new \yii\db\Expression('profesor.id')])
                        ->andWhere(['not', ['fecha_resolucion' => null]])
                ],
                ['exists',
                    SolicitudTemaTesis::find()
                        ->where(['or',
                            ['profesor_revisor1_propuesto_id' => new \yii\db\Expression('profesor.id')],
                            ['profesor_revisor2_propuesto_id' => new \yii\db\Expression('profesor.id')]
                        ])
                        ->andWhere(['not', ['fecha_resolucion' => null]])
                ]
            ])
            ->limit(10)
            ->all();
        
        $data = [
            'labels' => [],
            'guia' => [],
            'revisor' => [],
        ];
        
        foreach ($profesores as $profesor) {
            $data['labels'][] = substr($profesor->nombre, 0, 20);
            
            // Calculate average resolution time as guide
            $sttComoGuia = SolicitudTemaTesis::find()
                ->where(['profesor_guia_propuesto_id' => $profesor->id])
                ->andWhere(['not', ['fecha_resolucion' => null]])
                ->all();
            
            $tiempoGuia = 0;
            if (count($sttComoGuia) > 0) {
                $totalDias = 0;
                foreach ($sttComoGuia as $stt) {
                    $fechaCreacion = new \DateTime($stt->fecha_creacion);
                    $fechaResolucion = new \DateTime($stt->fecha_resolucion);
                    $diff = $fechaCreacion->diff($fechaResolucion);
                    $totalDias += $diff->days;
                }
                $tiempoGuia = $totalDias / count($sttComoGuia);
            }
            $data['guia'][] = round($tiempoGuia, 1);
            
            // Calculate average resolution time as reviewer
            $sttComoRevisor = SolicitudTemaTesis::find()
                ->where(['or',
                    ['profesor_revisor1_propuesto_id' => $profesor->id],
                    ['profesor_revisor2_propuesto_id' => $profesor->id]
                ])
                ->andWhere(['not', ['fecha_resolucion' => null]])
                ->all();
            
            $tiempoRevisor = 0;
            if (count($sttComoRevisor) > 0) {
                $totalDias = 0;
                foreach ($sttComoRevisor as $stt) {
                    $fechaCreacion = new \DateTime($stt->fecha_creacion);
                    $fechaResolucion = new \DateTime($stt->fecha_resolucion);
                    $diff = $fechaCreacion->diff($fechaResolucion);
                    $totalDias += $diff->days;
                }
                $tiempoRevisor = $totalDias / count($sttComoRevisor);
            }
            $data['revisor'][] = round($tiempoRevisor, 1);
        }
        
        return $data;
    }
}
