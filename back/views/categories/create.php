<?php

/* @var $this yii\web\View */
/* @var $model app\model\Category */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Tambah Kategori';
$this->params['header'] = 'Tambah Kategori';
$this->params['breadcrumbs'][] = 'Kategori';
$this->params['breadcrumbs'][] = 'Tambah Baru';
?>

<div class="row justify-content-md-center">
    <div class="col col-md-6">
        <div class="card card-default">
            <?php $form = ActiveForm::begin([
                'id' => 'category-form',
            ]); ?>
            <div class="card-body">
                <?= $form
                    ->field($model, 'name')
                    ->textInput(['placeholder' => 'Nama', 'maxlength' => true])
                ?>
            </div>

            <div class="card-footer text-right">
                <?= Html::submitButton('Simpan', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>