<?php

namespace app\controllers;

use app\models\Area;
use app\models\Category;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\Document;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class OtherDocumentsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'create', 'update', 'delete', 'set-status', 'areas', 'categories'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'set-status', 'areas', 'categories'],
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
        $status = Yii::$app->request->get('status', Yii::$app->params['DOC_STATUS_PUBLISHED']);
        $keyword = Yii::$app->request->get('keyword');
        $category = Yii::$app->request->get('category');
        $area = Yii::$app->request->get('area');
        $year = Yii::$app->request->get('year');
        $sort = Yii::$app->request->get('sort', '-created_at');
        $sorts = [
            'no' => 'Number (A - Z)',
            '-no' => 'Number (Z - A)',
            'name' => 'Name (A - Z)',
            '-name' => 'Name (Z - A)',
            'categoryData.name' => 'Category (A - Z)',
            '-categoryData.name' => 'Category (Z - A)',
            'areaData.name' => 'Area (A - Z)',
            '-areaData.name' => 'Area (Z - A)',
            '-areaData.name' => 'Area (Z - A)',
            'year' => 'Year (Oldest - Newest)',
            '-year' => 'Year (Newest - Oldest)',
            'created_at' => 'Created At (Oldest - Newest)',
            '-created_at' => 'Created At (Newest - Oldest)',
            'updated_at' => 'Updated At (Oldest - Newest)',
            '-updated_at' => 'Updated At (Newest - Oldest)',
        ];
        $query = Document::find()
            ->joinWith(['areaData as areaData', 'categoryData as categoryData'])
            ->where([
                'status' => intval($status)
            ])
            ->andWhere('
                category != :category_perda_perbup
                and category != :category_rnd
                and category != :category_planning
            ', [
                ':category_perda_perbup' => Yii::$app->params['DOC_CATEGORY_PERDA_PERBUP'],
                ':category_rnd' => Yii::$app->params['DOC_CATEGORY_LITBANG'],
                ':category_planning' => Yii::$app->params['DOC_CATEGORY_PLANNING'],
            ]);

        if (!empty($keyword)) {
            $query->andWhere(
                'document.name like :keyword 
                or document.no like :keyword
                or areaData.name like :keyword',
                [':keyword' => "%{$keyword}%"]
            );
        }

        if (!empty($year)) {
            $query->andWhere('YEAR(created_at) = :year or YEAR(updated_at) = :year or year = :year', [
                ':year' => intval($year),
            ]);
        }

        if (!empty($category)) {
            $query->andWhere(['category' => $category]);
        }

        if (!empty($area)) {
            $query->andWhere(['area' => $area]);
        }

        if (!empty($sort) && isset($sorts[$sort])) {
            $dir = substr($sort, 0, 1) === '-' ? 'DESC' : 'ASC';
            $column = str_replace('-', '', $sort);
            $query->orderBy($column . ' ' . $dir);
        } else {
            $query->orderBy('created_at DESC');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> false,
        ]);
        
        $areas = ArrayHelper::map(Area::find($area)->all(), 'id', 'name');
        $categories = ArrayHelper::map(Category::find($category)->all(), 'id', 'name');
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'status' => $status,
            'year' => $year,
            'keyword' => $keyword,
            'area' => $area,
            'areas' => $areas,
            'category' => $category,
            'categories' => $categories,
            'sort' => $sort,
            'sorts' => $sorts,
        ]);
    }

    public function actionCreate()
    {
        $model = new Document();
        $model->status = Yii::$app->params['DOC_STATUS_DRAFT'];
        $model->year = date('Y');

        if ($model->load(Yii::$app->request->post())) {
            $success = $model->save();
            
            Yii::$app->session->setFlash('NOTIFY', [
                'type' => $success ? 'success' : 'error',
                'message' => $success
                    ? 'Dokumen berhasil diupload.'
                    : 'Ups, terjadi kesalahan. Silahkan hubungi administrator.',
            ]);

            if ($success) {
                return $this->redirect(['index', 'status' => $model->status]);
            }
        }

        $areas = ArrayHelper::map(Area::find($model->area)->all(), 'id', 'name');
        $categories = ArrayHelper::map(Category::find($model->category)->all(), 'id', 'name');
        return $this->render('create', [
            'model' => $model,
            'areas' => $areas,
            'categories' => $categories,
        ]);
    }

    public function actionEdit($id)
    {
        $model = Document::findOne($id);
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
                return $this->redirect(['index', 'status' => $model->status]);
            }
        }

        $areas = ArrayHelper::map(Area::find($model->area)->all(), 'id', 'name');
        $categories = ArrayHelper::map(Category::find($model->category)->all(), 'id', 'name');
        return $this->render('edit', [
            'model' => $model,
            'areas' => $areas,
            'categories' => $categories,
        ]);
    }

    public function actionView($id)
    {
        $model = Document::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException();
        }

        if ($model->load(Yii::$app->request->post())) {
            $success = $model->save();
            
            Yii::$app->session->setFlash('NOTIFY', [
                'type' => $success ? 'success' : 'error',
                'message' => $success
                    ? 'Status berhasil diubah.'
                    : 'Ups, terjadi kesalahan. Silahkan hubungi administrator.',
            ]);

            if ($success) {
                return $this->redirect(['index', 'status' => $model->status]);
            }
        }

        $areas = ArrayHelper::map(Area::find($model->area)->all(), 'id', 'name');
        $categories = ArrayHelper::map(Category::find($model->category)->all(), 'id', 'name');
        return $this->render('view', [
            'model' => $model,
            'areas' => $areas,
            'categories' => $categories,
        ]);
    }

    public function actionDelete()
    {
        $id = Yii::$app->request->get('id');
        $user = Yii::$app->user->identity;
        $isAdministrator = $user->type === intval(Yii::$app->params['USER_TYPE_ADMINISTRATOR']);

        if(!$id) {
            $id = Yii::$app->request->post('id');
        }
        
        $id = explode(',', $id);
        $deleted = 0;
        for ($i = 0; $i < count($id); $i++) {
            $model = Document::findOne($id[$i]);
            if($model && ($model->created_by === $user->id || $isAdministrator)) {
                $model->delete();
                $deleted++;
            }
        }
        
        $multiple = $deleted > 1;
        Yii::$app->session->setFlash('NOTIFY', [
            'type' => 'success',
            'message' => $deleted . ' data berhasil dihapus.',
        ]);
        return $this->redirect(Yii::$app->request->referrer);
    }


    public function actionSetStatus($status)
    {
        $user = Yii::$app->user->identity;
        $isAdministrator = $user->type === intval(Yii::$app->params['USER_TYPE_ADMINISTRATOR']);
        $isOperator = $user->type === intval(Yii::$app->params['USER_TYPE_OPERATOR']);
        $isValidator = $user->type === intval(Yii::$app->params['USER_TYPE_VALIDATOR']);
        $isPublisher = $user->type === intval(Yii::$app->params['USER_TYPE_PUBLISHER']);

        if (
            !$isOperator
            || $isAdministrator
            || ($status === Yii::$app->params['DOC_STATUS_DRAFT'] && $isValidator)
            || ($status === Yii::$app->params['DOC_STATUS_APPROVED'] && $$isPublisher)
        ) {
            $id = Yii::$app->request->get('id');

            if(!$id) {
                $id = Yii::$app->request->post('id');
            }
            
            $id = explode(',', $id);
            $updated = 0;
            for ($i = 0; $i < count($id); $i++) {
                $model = Document::findOne($id[$i]);
                if ($model) {
                    $isRequireOtherApproval = (
                        $status === Yii::$app->params['DOC_STATUS_APPROVED']
                        && strval($model->status) === Yii::$app->params['DOC_STATUS_DRAFT']
                    ) || $status === Yii::$app->params['DOC_STATUS_DRAFT'];
                    if (!$isRequireOtherApproval || ($isRequireOtherApproval && $model->created_by !== $user->id)) {
                        $model->status = intval($status);
                        $updated += $model->save() ? 1 : 0;
                    }
                }
            }
            
            $multiple = $updated > 1;
            Yii::$app->session->setFlash('NOTIFY', [
                'type' => 'success',
                'message' => $updated . ' data berhasil diubah.',
            ]);
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionAreas($q = null, $id = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        
        if (!is_null($q)) {
            $query = new Query();
            $query->select('id, name as text')
                ->from('area')
                ->where(['like', 'name', $q])
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

    public function actionCategories($q = null, $id = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        
        if (!is_null($q)) {
            $query = new Query();
            $query->select('id, name as text')
                ->from('category')
                ->where(['like', 'name', $q])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Category::findOne($id)->name];
        }

        return $out;
    }
}