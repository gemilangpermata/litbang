<?php

namespace app\controllers;

use app\models\Area;
use app\models\Category;
use app\models\Document;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\Login;
use app\models\ResetPassword;
use app\models\ResetPasswordRequest;
use app\models\User;
use yii\base\InvalidArgumentException;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'documents', 'document', 'areas'],
                'rules' => [
                    [
                        'actions' => ['index', 'documents', 'document', 'areas'],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $categories = Yii::$app->params['DOC_CATEGORIES'];
        $category = Yii::$app->request->get('category');
        $keyword = Yii::$app->request->get('keyword');

        $data = [];
        if (empty($category)) {
            foreach ($categories as $code => $name) {
                $query = Document::find()
                    ->joinWith(['areaData as areaData'])
                    ->where([
                        'category' => $code,
                        'status' => intval(Yii::$app->params['DOC_STATUS_PUBLISHED']),
                    ]);
                
                if ($keyword) {
                    $query->andWhere(
                        'document.no like :keyword 
                        or document.name like :keyword
                        or document.year like :keyword
                        or areaData.name like :keyword',
                        [':keyword' => "%{$keyword}%"],
                    );
                }

                $total = $query->count();
                $data[$code] = [
                    'name' => $name,
                    'total' => $total,
                ];
            }

            $query = Document::find()
                ->joinWith(['areaData as areaData'])
                ->where(['status' => intval(Yii::$app->params['DOC_STATUS_PUBLISHED'])])
                ->andWhere('
                    category != :category_perda_perbup
                    and category != :category_rnd
                    and category != :category_planning
                ', [
                    ':category_perda_perbup' => Yii::$app->params['DOC_CATEGORY_PERDA_PERBUP'],
                    ':category_rnd' => Yii::$app->params['DOC_CATEGORY_LITBANG'],
                    ':category_planning' => Yii::$app->params['DOC_CATEGORY_PLANNING'],
                ]);
            
            if ($keyword) {
                $query->andWhere(
                    '(document.no like :keyword 
                    or document.name like :keyword
                    or document.year like :keyword
                    or areaData.name like :keyword)',
                    [':keyword' => "%{$keyword}%"],
                );
            }

            $total = $query->count();
            $data['others'] = [
                'name' => 'Data Lainnya',
                'total' => $total,
            ];
        }

        if ($category === 'others') {
            $count = Yii::$app->db->createCommand('select count(*) FROM category')->queryScalar();
            $data = new SqlDataProvider([
                'totalCount' => $count,
                'sql' => '
                    select
                        *,
                        coalesce((
                            select
                                count(*)
                            from document
                            left join area as areaData on document.area = areaData.id
                            where
                                category = category.id
                                and status = :status
                                and (document.no like :keyword
                                or document.name like :keyword
                                or document.year like :keyword 
                                or areaData.name like :keyword)
                        ), 0) as total
                    from category
                    order by name asc
                ',
                'params' => [
                    ':status' => intval(Yii::$app->params['DOC_STATUS_PUBLISHED']),
                    ':keyword' => "%{$keyword}%",
                ],
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);
        }

        return $this->render('index', [
            'data' => $data,
            'category' => $category,
            'keyword' => $keyword,
        ]);
    }

    public function actionDocuments()
    {
        $category = Yii::$app->request->get('category');
        $categories = Yii::$app->params['DOC_CATEGORIES'];
        $categoryModel = Category::findOne(['id' => $category]);
        $keyword = Yii::$app->request->get('keyword');
        $area = Yii::$app->request->get('area');
        $year = Yii::$app->request->get('year');
        $sort = Yii::$app->request->get('sort', 'name');
        $sorts = [
            'no' => 'Number (A - Z)',
            '-no' => 'Number (Z - A)',
            'name' => 'Name (A - Z)',
            '-name' => 'Name (Z - A)',
            'areaData.name' => 'Area (A - Z)',
            '-areaData.name' => 'Area (Z - A)',
            '-areaData.name' => 'Area (Z - A)',
            'year' => 'Year (Oldest - Newest)',
            '-year' => 'Year (Newest - Oldest)',
            'updated_at' => 'Updated At (Oldest - Newest)',
            '-updated_at' => 'Updated At (Newest - Oldest)',
        ];

        if (empty($category) || (!isset($categories[$category]) && !$categoryModel)) {
            return $this->redirect(['index']);
        }

        $query = Document::find()
            ->joinWith(['areaData as areaData'])
            ->where([
                'status' => intval(Yii::$app->params['DOC_STATUS_PUBLISHED']),
                'category' => $category,
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
        return $this->render('documents', [
            'category' => $category,
            'dataProvider' => $dataProvider,
            'fromOthers' => !empty($categoryModel),
            'categoryName' => !empty($categoryModel) ? $categoryModel->name : $categories[$category],
            'year' => $year,
            'keyword' => $keyword,
            'area' => $area,
            'areas' => $areas,
            'sort' => $sort,
            'sorts' => $sorts,
        ]);
    }

    public function actionDocument($id)
    {
        $document = Document::findOne(['id' => $id]);
        if (!$document) {
            throw new NotFoundHttpException();
        }

        if ($document->status !== intval(Yii::$app->params['DOC_STATUS_PUBLISHED'])) {
            throw new ForbiddenHttpException();
        }

        return $this->redirect('http://docs.google.com/viewer?url=' . $document->fileUrl);
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
}
