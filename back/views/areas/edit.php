<?php

/* @var $this yii\web\View */
/* @var $model app\model\Area */

use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

$this->title = 'Edit Daerah';
$this->params['header'] = 'Edit Daerah';
$this->params['breadcrumbs'][] = 'Daerah';
$this->params['breadcrumbs'][] = 'Edit';
?>

<div class="row justify-content-md-center">
    <div class="col col-md-6">
        <div class="card card-default">
            <?php $form = ActiveForm::begin([
                'id' => 'area-form',
            ]); ?>
            <div class="card-body">
                <?= $form
                    ->field($model, 'name')
                    ->textInput(['placeholder' => 'Nama', 'maxlength' => true])
                ?>

                <?= $form->field($model, 'parent')->widget(Select2::class, [
                    'bsVersion' => '4.6.0',
                    'theme' => Select2::THEME_KRAJEE_BS4,
                    'data' => $parents,
                    'options' => ['placeholder' => 'Pilih Kecamatan'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Memuat data...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['parents']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(area) { return area.text; }'),
                        'templateSelection' => new JsExpression('function (area) { return area.text; }'),
                    ],
                ])->hint('Pilih apabila daerah merupakan kelurahan.')
                ?>
            </div>

            <div class="card-footer text-right">
                <?= Html::submitButton('Simpan Perubahan', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>