<?php

/* @var $this yii\web\View */
/* @var $status string */
/* @var $keyword string */
/* @var $year string */

use app\models\Document;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Perda/Perbup';
$this->params['header'] = 'Perda/Perbup';
$this->params['breadcrumbs'][] = 'Dokumen';
$this->params['breadcrumbs'][] = 'Perda/Perbup';

$this->registerJsFile(
    '@web/js/regulation-documents/index.js?v=' . date('YmdHis'),
    [
        'depends' => [
            \yii\web\JqueryAsset::class,
        ]
    ]
);

$user = Yii::$app->user->identity;
$isAdministrator = $user->type === intval(Yii::$app->params['USER_TYPE_ADMINISTRATOR']);
$isOperator = $user->type === intval(Yii::$app->params['USER_TYPE_OPERATOR']);
$isValidator = $user->type === intval(Yii::$app->params['USER_TYPE_VALIDATOR']);
$isPublisher = $user->type === intval(Yii::$app->params['USER_TYPE_PUBLISHER']);
$hasSearch = !empty($year) || !empty($keyword);
?>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title dropdown">
            <a class="dropdown-toggle text-dark" data-toggle="dropdown" href="javascript:void(0)" aria-expanded="false">
                <?= Yii::$app->params['DOC_STATUSES'][$status] ?> <span class="caret"></span>
            </a>
            <div class="dropdown-menu">
                <?php
                foreach(Yii::$app->params['DOC_STATUSES'] as $code => $name) {
                    echo '<a class="dropdown-item '. (intval($code) === intval($status) ? 'active' : '') .'" tabindex="-1" href="'. Url::current(['status' => $code]) .'">'. $name .'</a>';
                }
                ?>
            </div>
        </h3>
        <div class="card-tools">
            <div class="input-group input-group-sm">
                <button type="button" class="btn btn-sm btn-<?= $hasSearch ? 'primary' : 'default' ?>" data-toggle="modal" data-target="#filter-modal">
                    <i class="fas fa-search"></i> Pencarian
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="p-3">
            <?= Html::beginForm(Url::to(['delete']), 'post', [
                'id' => 'delete-document-form',
                'class' => 'dropdown d-inline-block'
            ]) ?>
                <?= Html::hiddenInput('id', '', [
                    'class' => 'hidden-id'
                ]); ?>
                <button type="button" id="delete-button" class="btn btn-default">
                    <i class="far fa-trash-alt"></i>
                </button>
            <?= Html::endForm() ?>

            <?php
            if (
                $isAdministrator
                || (($status === Yii::$app->params['DOC_STATUS_DRAFT'] || $status === Yii::$app->params['DOC_STATUS_APPROVED']) && $isValidator)
                || (($status === Yii::$app->params['DOC_STATUS_APPROVED'] || $status === Yii::$app->params['DOC_STATUS_PUBLISHED']) && $isPublisher)
            ) {
            ?>
            <?= Html::beginForm(Url::to(['set-status', 'status' => '']), 'post', [
                'id' => 'set-status-document-form',
                'class' => 'dropdown d-inline-block'
            ]) ?>
                <?= Html::hiddenInput('id', '', [
                    'class' => 'hidden-id'
                ]); ?>
                <button class="btn btn-secondary dropdown-toggle" type="button" id="set-status-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Ubah Status
                </button>
                <?php
                $statusOptions = [];
                if ($status === Yii::$app->params['DOC_STATUS_DRAFT'] && ($isValidator || $isAdministrator)) {
                    $statusOptions = [
                        Yii::$app->params['DOC_STATUS_APPROVED'] => Yii::$app->params['DOC_STATUSES'][Yii::$app->params['DOC_STATUS_APPROVED']]
                    ];
                } else if ($status === Yii::$app->params['DOC_STATUS_APPROVED']) {
                    if ($isValidator || $isAdministrator) {
                        $statusOptions[Yii::$app->params['DOC_STATUS_DRAFT']] = Yii::$app->params['DOC_STATUSES'][Yii::$app->params['DOC_STATUS_DRAFT']];
                    }
                    if ($isPublisher || $isAdministrator) {
                        $statusOptions[Yii::$app->params['DOC_STATUS_PUBLISHED']] = Yii::$app->params['DOC_STATUSES'][Yii::$app->params['DOC_STATUS_PUBLISHED']];
                    }
                } else if ($status === Yii::$app->params['DOC_STATUS_PUBLISHED'] && ($isPublisher || $isAdministrator)) {
                    $statusOptions = [
                        Yii::$app->params['DOC_STATUS_APPROVED'] => 'Tidak Dipublikasikan',
                    ];
                }
                ?>
                <div class="dropdown-menu" aria-labelledby="set-status-button">
                    <?php
                    foreach ($statusOptions as $code => $name) {
                        echo '
                        <a
                            class="dropdown-item set-status-button"
                            data-command="'. $name .'"
                            data-is-to-approve="' . (intval($code) === intval(Yii::$app->params['DOC_STATUS_APPROVED'] && $status === Yii::$app->params['DOC_STATUS_DRAFT']) ? '1' : '0') . '"
                            data-code="'. $code .'"
                            href="javascript:void(0)">
                            '. $name .'
                        </a>';
                    }
                    ?>
                </div>
            <?= Html::endForm() ?>
            <?php
            }
            ?>

            <a href="<?= Url::to(['create']) ?>" class="btn btn-primary">
                <i class="fa fa-folder-plus"></i> Tambah Baru
            </a>
        </div>

        <?php
        $columns = [
            [
                'header' => '
                    <div class="icheck-primary">
                        <input type="checkbox" id="check-all" />
                        <label for="check-all"></label>
                    </div>
                ',
                'headerOptions' => [
                    'style' => 'width: 30px; text-align: center;'
                ],
                'format' => 'raw',
                'value' => function ($model) use ($user) {
                    return $model->id === $user->id ? '' : '
                        <div class="icheck-primary">
                            <input type="checkbox" class="grid-select" name=id[] value="'. $model->id .'" id="check-'. $model->id .'" />
                            <label for="check-'. $model->id .'"></label>
                        </div>
                    ';
                },
            ],
            'no',
            'name',
            'year',
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return Yii::$app->formatter->asDatetime($model->created_at, 'php:d F Y');
                }
            ],
            [
                'attribute' => 'updated_at',
                'value' => function ($model) {
                    return Yii::$app->formatter->asDatetime($model->updated_at, 'php:d F Y');
                }
            ],
            [
                'headerOptions' => [
                    'style' => '
                        width: 130px;
                        text-align: center;
                        white-space: nowrap;
                    '
                ],
                'format' => 'raw',
                'value' => function ($model) use ($user, $isAdministrator) {
                    $delete = $model->created_by === $user->id || $isAdministrator ? Html::a(
                        '<i class="fa fa-trash-alt"></i>',
                        'javascript:void(0)',
                        [
                            'data-id' => $model->id,
                            'class' => 'btn btn-sm btn-default delete-button',
                        ]
                    ) . ' ' : '';
                    $edit = $model->created_by === $user->id ? Html::a(
                        '<i class="fa fa-pen-alt"></i>',
                        $model->id === $user->id ? ['site/profile'] : ['edit', 'id' => $model->id],
                        [
                            'class' => 'btn btn-sm btn-default',
                        ]
                    ) . ' ' : '';
                    $view = empty($edit) ? Html::a(
                        '<i class="fa fa-eye"></i>',
                        $model->id === $user->id ? ['site/profile'] : ['view', 'id' => $model->id],
                        [
                            'class' => 'btn btn-sm btn-default',
                        ]
                    ) : '';

                    return  $delete . $edit . $view;
                }
            ]
        ];
        ?>
        <div id="document-gridview">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'bordered' => false,
            'hover' => true,
            'layout' => '
                <div style="width: 100%; overflow: auto;">
                    {items}
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="px-3">
                            {pager}
                        </div>
                    </div>
                    <div class="col-md-4 text-right d-md-block">
                        <div class="px-3 pb-3">
                            {summary}
                        </div>
                    </div>
                </div>
            ',
            'pager' => [
                'hideOnSinglePage' => false,
            ],
            'columns' => $columns,
        ]);
        ?>
        </div>
    </div>
</div>

<div class="modal fade" id="filter-modal" tabindex="-1" role="dialog" aria-labelledby="filter-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filter-modal-label">Pencarian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?= Html::beginForm(
                Url::current([
                    'year' => null,
                    'keyword' => null,
                ]),
                'get',
                [
                    'id' => 'filter-form'
                ]
            ) ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label">Kata Kunci</label>
                    <?= Html::textInput('keyword', $keyword, [
                        'id' => 'keyword-text',
                        'class' => 'form-control',
                        'placeholder' => 'Masukan kata kunci',
                    ]) ?>
                </div>
                <div class="form-group">
                    <label class="control-label">Tahun</label>
                    <?= Select2::widget([
                        'name' => 'year',
                        'bsVersion' => '4.6.0',
                        'theme' => Select2::THEME_KRAJEE_BS4,
                        'value' => $year,
                        'data' => Document::getYears(),
                        'options' => ['placeholder' => 'Pilih tahun'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="clear-filter-button" class="btn btn-secondary">Reset</button>
                <button type="submit" class="btn btn-primary">Terapkan Pencarian</button>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>