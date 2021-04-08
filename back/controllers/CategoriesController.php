<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\Category;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class CategoriesController extends Controller
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
        $query = Category::find();
        $keyword = Yii::$app->request->get('keyword');
        if (!empty($keyword)) {
            $query->andWhere(
                'name like :keyword',
                [':keyword' => "%{$keyword}%"]
            );
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['name' => SORT_ASC],
                'attributes' => ['name']
            ]
        ]);
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'keyword' => $keyword,
        ]);
    }

    public function actionCreate()
    {
        $model = new Category();

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
        $model = Category::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException();
        }

        if ($model->load(Yii::$app->request->post())) {
            $success = $model->save();
        
            Yii::$app->session->setFlash('NOTIFY', [
                'type' => $success ? 'success' : 'error',
                'message' => $success
                    ? 'Perubahan berhasil disimpan.'
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
                $model = Category::findOne($id[$i]);
                if($model) {
                    $model->delete();
                }
            }
        }
        else {
            $model = Category::findOne($id);
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