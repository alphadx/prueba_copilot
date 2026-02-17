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
                ],
            ],
        ];
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
