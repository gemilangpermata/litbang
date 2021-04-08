<?php

/* @var $this yii\web\View */

use app\models\Document;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

$this->title = $categoryName;
if ($fromOthers) {
    $this->params['breadcrumbs'][] = ['url' => Url::to(['index', 'category' => 'others', 'keyword' => $keyword]), 'label' => 'Data Lainnya'];
}
$this->params['breadcrumbs'][] = $categoryName;

if ($keyword) {
    $this->title = 'Hasil pencarian untuk "'. $keyword .'"';
    $this->params['header'] = '<small>' . $this->title . '</small>';
}

$hasSearch = !empty($year) || !empty($keyword) || !empty($area);

$this->registerJsFile(
    '@web/js/documents.js?v=' . date('YmdHis'),
    [
        'depends' => [
            \yii\web\JqueryAsset::class,
        ]
    ]
);
?>

<input type="hidden" name="document-url" id="document-url" value="<?= Url::to(['document', 'id' => '']) ?>" />
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title dropdown">
            <?= $categoryName ?>
        </h3>
        <div class="card-tools">
            <div class="input-group input-group-sm">
                <button type="button" class="btn btn-sm btn-<?= $hasSearch ? 'primary' : 'default' ?>" data-toggle="modal" data-target="#filter-modal">
                    <i class="fas fa-search"></i> Pencarian Lebih Lanjut
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <?php
        $columns = [
            'no',
            'name',
            'year',
            [
                'header' => 'Area',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->areaData ? $model->areaData->name : Yii::$app->params['COMPANY_AREA'];
                },
            ],
        ];
        ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'bordered' => false,
            'hover' => true,
            'layout' => '
                <div id="data-grid" style="width: 100%; overflow: auto;">
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
            'rowOptions' => [
                'data-widget' => 'expandable-table',
                'aria-expanded' => 'false',
            ],
            'columns' => $columns,
        ]);
        ?>
    </div>
</div>

<div class="modal fade" id="filter-modal" tabindex="-1" role="dialog" aria-labelledby="filter-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filter-modal-label">Pencarian Lebih Lanjut</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?= Html::beginForm(
                Url::current([
                    'year' => null,
                    'keyword' => null,
                    'area' => null,
                    'sort' => null,
                ]),
                'get',
                [
                    'id' => 'filter-form'
                ]
            ) ?>
            <input type="hidden" name="reset-url" id="reset-url" value="<?= Url::current([
                'year' => null,
                'keyword' => null,
                'area' => null,
                'sort' => null,
                'category' => $category,
            ]) ?>" />
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
                    <label class="control-label">Area</label>
                    <?= Select2::widget([
                        'name' => 'area',
                        'bsVersion' => '4.6.0',
                        'theme' => Select2::THEME_KRAJEE_BS4,
                        'data' => $areas,
                        'value' => $area,
                        'options' => ['placeholder' => 'Pilih daerah'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                            ],
                            'ajax' => [
                                'url' => Url::to(['areas']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(area) { return area.text; }'),
                            'templateSelection' => new JsExpression('function (area) { return area.text; }'),
                        ],
                    ]) ?>
                </div>
                <div class="form-group">
                    <label class="control-label">Year</label>
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
                <div class="form-group">
                    <label class="control-label">Sort By</label>
                    <?= Html::dropDownList('sort', $sort, $sorts, [
                        'class' => 'custom-select',
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