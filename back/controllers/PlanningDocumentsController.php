<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\Document;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class PlanningDocumentsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'create', 'update', 'delete', 'set-status'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'set-status'],
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
        $year = Yii::$app->request->get('year');
        $query = Document::find()
            ->where([
                'category' => Yii::$app->params['DOC_CATEGORY_PLANNING'],
                'status' => intval($status)
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

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
                'attributes' => [
                    'no',
                    'name',
                    'year',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'status' => $status,
            'year' => $year,
            'keyword' => $keyword,
        ]);
    }

    public function actionCreate()
    {
        $model = new Document();
        $model->category = Yii::$app->params['DOC_CATEGORY_PLANNING'];
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

        return $this->render('create', [
            'model' => $model,
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

        return $this->render('edit', [
            'model' => $model,
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

        return $this->render('view', [
            'model' => $model,
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
}