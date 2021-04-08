<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\Area;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AreasController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'create', 'update', 'delete', 'parents'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'parents'],
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
        $query = Area::find()
            ->select(new Expression('area.*, CONCAT(COALESCE(parentData.name, \'\'), area.name) as sortername'))
            ->joinWith('parentData as parentData');
        $keyword = Yii::$app->request->get('keyword');
        $type = Yii::$app->request->get('type');

        if (!empty($keyword)) {
            $query->andWhere(
                'name like :keyword',
                [':keyword' => "%{$keyword}%"]
            );
        }

        if (!empty($type)) {
            if ($type === 'district') {
                $query->andWhere('area.parent is null or area.parent = \'\'');
            } else {
                $query->andWhere('area.parent is not null and area.parent != \'\'');
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['sortername' => SORT_ASC],
                'attributes' => [
                    'sortername'
                ],
            ],
        ]);
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'keyword' => $keyword,
            'type' => $type,
        ]);
    }


    public function actionCreate()
    {
        $model = new Area();

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

        $parents = ArrayHelper::map(Area::find($model->parent)->all(), 'id', 'name');
        return $this->render('create', [
            'model' => $model,
            'parents' => $parents,
        ]);
    }

    public function actionEdit($id)
    {
        $model = Area::findOne($id);
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

        $parents = ArrayHelper::map(Area::find($model->parent)->all(), 'id', 'name');
        return $this->render('edit', [
            'model' => $model,
            'parents' => $parents,
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
                $model = Area::findOne($id[$i]);
                if($model) {
                    $model->delete();
                }
            }
        }
        else {
            $model = Area::findOne($id);
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

    public function actionParents($q = null, $id = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        
        if (!is_null($q)) {
            $query = new Query();
            $query->select('id, name as text')
                ->from('area')
                ->where(['like', 'name', $q])
                ->andWhere('parent is null or parent = \'\'')
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Area::findOne($id)->name];
        }

        return $out;
    }
}