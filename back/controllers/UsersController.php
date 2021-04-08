<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class UsersController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $type = Yii::$app->request->get('type', intval(Yii::$app->params['USER_TYPE_ADMINISTRATOR']));
        $query = User::find()->where(['type' => $type]);
        $keyword = Yii::$app->request->get('keyword');
        if (!empty($keyword)) {
            $query->andWhere(
                'name like :keyword or email like :keyword',
                [':keyword' => "%{$keyword}%"]
            );
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['name' => SORT_ASC],
                'attributes' => ['name', 'email']
            ]
        ]);
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'type' => $type,
            'keyword' => $keyword,
        ]);
    }

    public function actionCreate()
    {
        $model = new User();
        $model->scenario = User::SCENARIO_DEFAULT;

        if ($model->load(Yii::$app->request->post())) {
            $success = $model->save();
        
            Yii::$app->session->setFlash('NOTIFY', [
                'type' => $success ? 'success' : 'error',
                'message' => $success
                    ? 'Data berhasil disimpan.'
                    : 'Ups, terjadi kesalahan. Silahkan hubungi administrator.',
            ]);

            if ($success) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionEdit($id)
    {
        $model = User::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException();
        }

        $model->scenario = User::SCENARIO_DEFAULT;

        if ($model->load(Yii::$app->request->post())) {
            $success = $model->save();
        
            Yii::$app->session->setFlash('NOTIFY', [
                'type' => $success ? 'success' : 'error',
                'message' => $success
                    ? 'Data berhasil disimpan.'
                    : 'Ups, terjadi kesalahan. Silahkan hubungi administrator.',
            ]);

            if ($success) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    public function actionDelete()
    {
        $id = Yii::$app->request->get('id');
        if(!$id) {
            $id = Yii::$app->request->post('id');
        }
        
        if(is_array($id)) {
            for ($i = 0; $i < count($id); $i++) {
                $model = User::findOne($id[$i]);
                if($model) {
                    $model->delete();
                }
            }
        }
        else {
            $model = User::findOne($id);
            if($model) {
                $model->delete();
            }
        }
        
        $multiple = is_array($id) && count($id) > 1;
        Yii::$app->session->setFlash('NOTIFY', [
            'type' => 'success',
            'message' => ($multiple ? count($id) : 1) . ' data berhasil dihapus.',
        ]);
        return $this->redirect(Yii::$app->request->referrer);
    }
}