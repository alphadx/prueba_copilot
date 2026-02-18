<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\SttForm;
use app\models\Origen;
use app\models\Modalidad;
use app\models\Profesor;
use app\models\Alumno;
use app\models\CarreraMalla;
use app\models\Empresa;

/**
 * Controller for Solicitud de Tema de Tesis (STT)
 */
class SttController extends Controller
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['get', 'post'],
                    'update' => ['get', 'post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Solicitud de Tema de Tesis
     * @return mixed
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $query = \app\models\SolicitudTemaTesis::find();
        
        // Filter based on user role
        if ($user->rol === 'alumno') {
            // Show only student's own STTs
            $alumno = \app\models\Alumno::findOne(['user_id' => $user->id]);
            if ($alumno) {
                $query->joinWith('sttAlumnos')
                    ->where(['stt_alumno.alumno_id' => $alumno->id]);
            } else {
                $query->where('1=0'); // No results
            }
        } elseif ($user->rol === 'profesor') {
            // Show STTs where professor is involved
            $profesor = \app\models\Profesor::findOne(['user_id' => $user->id]);
            if ($profesor) {
                $query->where(['or',
                    ['profesor_curso_id' => $profesor->id],
                    ['profesor_guia_propuesto_id' => $profesor->id],
                    ['profesor_revisor1_propuesto_id' => $profesor->id],
                    ['profesor_revisor2_propuesto_id' => $profesor->id],
                ]);
            }
        }
        // Admin and comision can see all
        
        $solicitudes = $query->orderBy(['fecha_creacion' => SORT_DESC])->all();
        
        return $this->render('index', [
            'solicitudes' => $solicitudes,
        ]);
    }

    /**
     * Creates a new Solicitud de Tema de Tesis
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SttForm();
        
        // Pre-fill profesor_curso_id if user is a professor
        $user = Yii::$app->user->identity;
        if ($user->rol === 'profesor') {
            $profesor = Profesor::findOne(['user_id' => $user->id]);
            if ($profesor) {
                $model->profesor_curso_id = $profesor->id;
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            $stt = $model->save();
            if ($stt) {
                // Send notifications to committee about new STT
                Yii::$app->notificationService->notifyCommitteeAboutNewSTT($stt);
                
                Yii::$app->session->setFlash('success', 
                    "Solicitud de Tema de Tesis creada exitosamente. Correlativo: {$stt->correlativo}"
                );
                return $this->redirect(['view', 'id' => $stt->id]);
            } else {
                Yii::$app->session->setFlash('error', 
                    'No se pudo crear la solicitud. Por favor, revise los errores.'
                );
            }
        }

        return $this->render('create', [
            'model' => $model,
            'origenes' => Origen::find()->where(['activo' => 1])->all(),
            'modalidades' => Modalidad::find()->where(['activo' => 1])->all(),
            'profesores' => Profesor::find()->where(['activo' => 1])->all(),
            'alumnos' => Alumno::find()->where(['activo' => 1])->all(),
            'carreras' => CarreraMalla::find()->where(['activo' => 1])->all(),
            'empresas' => Empresa::find()->where(['activo' => 1])->all(),
        ]);
    }

    /**
     * Displays a single Solicitud de Tema de Tesis
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $stt = $this->findModel($id);
        
        return $this->render('view', [
            'model' => $stt,
        ]);
    }

    /**
     * Updates an existing Solicitud de Tema de Tesis
     * Only allowed for STTs that haven't been resolved yet
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $stt = $this->findModel($id);
        
        // Check if STT can be updated (not yet resolved)
        if (!in_array($stt->estado, ['Enviada', 'En revisión'])) {
            Yii::$app->session->setFlash('error', 
                'No se puede modificar una solicitud que ya ha sido resuelta.'
            );
            return $this->redirect(['view', 'id' => $id]);
        }
        
        // Check permissions
        $user = Yii::$app->user->identity;
        if ($user->rol === 'alumno') {
            // Students can only update their own STTs
            $alumno = Alumno::findOne(['user_id' => $user->id]);
            $isOwner = false;
            foreach ($stt->sttAlumnos as $sttAlumno) {
                if ($sttAlumno->alumno_id == $alumno->id) {
                    $isOwner = true;
                    break;
                }
            }
            if (!$isOwner) {
                throw new \yii\web\ForbiddenHttpException('No tiene permisos para modificar esta solicitud.');
            }
        } elseif ($user->rol === 'profesor') {
            // Professors can update if they are the course professor
            $profesor = Profesor::findOne(['user_id' => $user->id]);
            if ($stt->profesor_curso_id != $profesor->id && $user->rol !== 'admin') {
                throw new \yii\web\ForbiddenHttpException('No tiene permisos para modificar esta solicitud.');
            }
        }
        
        // Populate form model with existing data
        $model = new SttForm();
        $model->origen_id = $stt->origen_id;
        $model->profesor_curso_id = $stt->profesor_curso_id;
        $model->nota = $stt->nota;
        $model->modalidad_id = $stt->modalidad_id;
        $model->titulo = $stt->titulo;
        $model->profesor_guia_propuesto_id = $stt->profesor_guia_propuesto_id;
        $model->profesor_revisor1_propuesto_id = $stt->profesor_revisor1_propuesto_id;
        $model->profesor_revisor2_propuesto_id = $stt->profesor_revisor2_propuesto_id;
        $model->empresa_id = $stt->empresa_id;
        
        // Get students info
        $sttAlumnos = $stt->sttAlumnos;
        if (isset($sttAlumnos[0])) {
            $model->alumno_1_id = $sttAlumnos[0]->alumno_id;
            $model->carrera_1_id = $sttAlumnos[0]->carrera_malla_id;
        }
        if (isset($sttAlumnos[1])) {
            $model->alumno_2_id = $sttAlumnos[1]->alumno_id;
            $model->carrera_2_id = $sttAlumnos[1]->carrera_malla_id;
        }
        
        // Get company info if exists
        if ($stt->empresa) {
            $model->empresa_rut = $stt->empresa->rut;
            $model->empresa_nombre = $stt->empresa->nombre;
            $model->empresa_supervisor_rut = $stt->empresa->supervisor_rut;
            $model->empresa_supervisor_nombre = $stt->empresa->supervisor_nombre;
            $model->empresa_supervisor_correo = $stt->empresa->supervisor_correo;
            $model->empresa_supervisor_telefono = $stt->empresa->supervisor_telefono;
            $model->empresa_supervisor_cargo = $stt->empresa->supervisor_cargo;
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // Update STT
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Update basic fields
                $stt->origen_id = $model->origen_id;
                $stt->profesor_curso_id = $model->profesor_curso_id;
                $stt->nota = $model->nota;
                $stt->titulo = $model->titulo;
                $stt->profesor_guia_propuesto_id = $model->profesor_guia_propuesto_id;
                $stt->profesor_revisor1_propuesto_id = $model->profesor_revisor1_propuesto_id;
                $stt->profesor_revisor2_propuesto_id = $model->profesor_revisor2_propuesto_id;
                
                if (!$stt->save()) {
                    throw new \Exception('Error al actualizar la solicitud.');
                }
                
                $transaction->commit();
                
                Yii::$app->session->setFlash('success', 'Solicitud actualizada exitosamente.');
                return $this->redirect(['view', 'id' => $stt->id]);
                
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());
            }
        }

        return $this->render('update', [
            'model' => $model,
            'stt' => $stt,
            'origenes' => Origen::find()->where(['activo' => 1])->all(),
            'modalidades' => Modalidad::find()->where(['activo' => 1])->all(),
            'profesores' => Profesor::find()->where(['activo' => 1])->all(),
            'alumnos' => Alumno::find()->where(['activo' => 1])->all(),
            'carreras' => CarreraMalla::find()->where(['activo' => 1])->all(),
            'empresas' => Empresa::find()->where(['activo' => 1])->all(),
        ]);
    }

    /**
     * Finds the SolicitudTemaTesis model based on its primary key value.
     * @param integer $id
     * @return \app\models\SolicitudTemaTesis the loaded model
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = \app\models\SolicitudTemaTesis::findOne($id);
        
        if ($model !== null) {
            return $model;
        }

        throw new \yii\web\NotFoundHttpException('La solicitud solicitada no existe.');
    }
}
