<?php

/* @var $this yii\web\View */
/* @var $model app\model\Category */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Edit Kategori';
$this->params['header'] = 'Edit Kategori';
$this->params['breadcrumbs'][] = 'Kategori';
$this->params['breadcrumbs'][] = 'Edit';
?>

<div class="row justify-content-md-center">
    <div class="col col-md-6">
        <div class="card card-default">
            <?php $form = ActiveForm::begin([
                'id' => 'user-form',
            ]); ?>
            <div class="card-body">
                <?= $form
                    ->field($model, 'name')
                    ->textInput(['placeholder' => 'Nama', 'maxlength' => true])
                ?>
            </div>

            <div class="card-footer text-right">
                <?= Html::submitButton('Simpan Perubahan', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>