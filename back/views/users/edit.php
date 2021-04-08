<?php

/* @var $this yii\web\View */
/* @var $model app\model\User */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Edit Pengguna';
$this->params['header'] = 'Edit Pengguna';
$this->params['breadcrumbs'][] = 'Pengguna';
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
                    ->field($model, 'email')
                    ->textInput(['placeholder' => 'Email', 'maxlength' => true])
                ?>
                
                <?= $form
                    ->field($model, 'new_password')
                    ->passwordInput(['maxlength' => true, 'class' => 'form-control textInputNewPassword'])
                    ->hint('Masukan password baru apabila ingin mengubah yang lama.')
                ?>
            
                <?= $form
                    ->field($model, 'confirm_password')
                    ->label('Konfirmasi Password')
                    ->passwordInput(['maxlength' => true])
                ?>

                <?= $form
                    ->field($model, 'name')
                    ->textInput(['placeholder' => 'Name', 'maxlength' => true])
                ?>

                <?= $form
                    ->field($model, 'type')
                    ->dropDownList(Yii::$app->params['USER_TYPES'], [
                        'prompt' => 'Pilih role',
                        'class' => 'custom-select',
                    ])
                ?>
            </div>

            <div class="card-footer text-right">
                <?= Html::submitButton('Simpan Perubahan', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>