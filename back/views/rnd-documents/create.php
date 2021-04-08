<?php

/* @var $this yii\web\View */
/* @var $model app\model\Category */

use app\models\Document;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Upload Dokumen';
$this->params['header'] = 'Upload Dokumen';
$this->params['breadcrumbs'][] = 'Dokumen';
$this->params['breadcrumbs'][] = 'Litbang';
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