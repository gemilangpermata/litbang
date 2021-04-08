<?php

/* @var $this yii\web\View */
/* @var $type string */
/* @var $keyword string */

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Pengguna';
$this->params['header'] = 'Pengguna';
$this->params['breadcrumbs'][] = 'Pengguna';

$this->registerJsFile(
    '@web/js/users/index.js?v=' . date('YmdHis'),
    [
        'depends' => [
            \yii\web\JqueryAsset::class,
        ]
    ]
);

$user = Yii::$app->user->identity;
$isAdministrator = $user->type === intval(Yii::$app->params['USER_TYPE_ADMINISTRATOR']);
?>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title dropdown">
            <a class="dropdown-toggle text-dark" data-toggle="dropdown" href="javascript:void(0)" aria-expanded="false">
                <?= Yii::$app->params['USER_TYPES'][$type] ?> <span class="caret"></span>
            </a>
            <div class="dropdown-menu">
                <?php
                foreach(Yii::$app->params['USER_TYPES'] as $code => $name) {
                    echo '<a class="dropdown-item '. (intval($code) === intval($type) ? 'active' : '') .'" tabindex="-1" href="'. Url::current(['type' => $code]) .'">'. $name .'</a>';
                }
                ?>
            </div>
        </h3>
        <div class="card-tools">
            <?= Html::beginForm(Url::current(['keyword' => null]), 'get') ?>
            <div class="input-group input-group-sm">
                <input type="text" name="keyword" class="form-control" value="<?= $keyword ?>" placeholder="Cari Pengguna">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
    <div class="card-body p-0">
        <?php
        if ($isAdministrator) {
        ?>
        <div class="p-3">
            <button type="button" id="delete-button" class="btn btn-default">
                <i class="far fa-trash-alt"></i>
            </button>
            <a href="<?= Url::to(['create']) ?>" class="btn btn-primary">
                <i class="fa fa-user-plus"></i> Tambah Baru
            </a>
        </div>
        <?php
        }
        ?>

        <?php
        $columns = [
            'name',
            'email',
        ];
        if ($isAdministrator) {
            array_unshift($columns, [
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
            ]);

            array_push($columns, [
                'headerOptions' => [
                    'style' => '
                        width: 130px;
                        text-align: center;
                        white-space: nowrap;
                    '
                ],
                'format' => 'raw',
                'value' => function ($model) use ($user) {
                    $delete = $model->id === $user->id ? '' : Html::a(
                        '<i class="fa fa-trash-alt"></i>',
                        'javascript:void(0)',
                        [
                            'data-id' => $model->id,
                            'class' => 'btn btn-sm btn-default delete-button',
                        ]
                    );
                    $edit = Html::a(
                        '<i class="fa fa-pen-alt"></i>',
                        $model->id === $user->id ? ['site/profile'] : ['edit', 'id' => $model->id],
                        [
                            'class' => 'btn btn-sm btn-default',
                        ]
                    );

                    return  $delete . ' ' . $edit;
                }
            ]);
        }
        ?>

        <?= Html::beginForm(Url::to(['delete']), 'post', [
            'id' => 'delete-user-form',
        ]) ?>
        <?= Html::hiddenInput('id', '', [
            'id' => 'hidden-id'
        ]); ?>
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
        <?= Html::endForm() ?>
    </div>
</div>