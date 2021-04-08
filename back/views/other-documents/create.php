<?php

/* @var $this yii\web\View */
/* @var $model app\model\Category */

use app\models\Document;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

$this->title = 'Upload Dokumen';
$this->params['header'] = 'Upload Dokumen';
$this->params['breadcrumbs'][] = 'Dokumen';
$this->params['breadcrumbs'][] = 'Lainnya';
$this->params['breadcrumbs'][] = 'Tambah Baru';
?>

<div class="row justify-content-md-center">
    <div class="col col-md-6">
        <div class="card card-default">
            <?php $form = ActiveForm::begin([
                'id' => 'document-form',
                'options' => [
                    'enctype' => 'multipart/form-data',
                ]
            ]); ?>
            <div class="card-body">
                <?= $form
                    ->field($model, 'no')
                    ->textInput(['placeholder' => 'Nomor Dokumen', 'maxlength' => true])
                ?>

                <?= $form
                    ->field($model, 'name')
                    ->textInput(['placeholder' => 'Nama Dokumen', 'maxlength' => true])
                ?>

                <?= $form->field($model, 'area')->widget(Select2::class, [
                    'bsVersion' => '4.6.0',
                    'theme' => Select2::THEME_KRAJEE_BS4,
                    'data' => $areas,
                    'options' => ['placeholder' => 'Pilih daerah'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Memuat data...'; }"),
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
                ])
                ?>

                <?= $form->field($model, 'category')->widget(Select2::class, [
                    'bsVersion' => '4.6.0',
                    'theme' => Select2::THEME_KRAJEE_BS4,
                    'data' => $categories,
                    'options' => ['placeholder' => 'Pilih kategori'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Memuat data...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['categories']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(category) { return category.text; }'),
                        'templateSelection' => new JsExpression('function (category) { return category.text; }'),
                    ],
                ])
                ?>

                <?= $form
                    ->field($model, 'year')
                    ->widget(Select2::class, [
                        'name' => 'year',
                        'bsVersion' => '4.6.0',
                        'theme' => Select2::THEME_KRAJEE_BS4,
                        'data' => Document::getYears(),
                        'options' => ['placeholder' => 'Pilih tahun'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ])
                ?>

                <?= $form
                    ->field($model, 'pic')
                    ->textInput(['placeholder' => 'Penanggungjawab', 'maxlength' => true])
                ?>
                
                <?= $form->field($model, 'file')->fileInput() ?>
            </div>

            <div class="card-footer text-right">
                <?= Html::submitButton('Simpan', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>