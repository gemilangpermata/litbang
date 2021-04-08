<?php

/* @var $this yii\web\View */
/* @var $model app\model\User */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Profil';
$this->params['header'] = 'Profil';
$this->params['breadcrumbs'][] = 'Profil';
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
                    ->label('Password Baru')
                    ->passwordInput(['maxlength' => true, 'class' => 'form-control textInputNewPassword'])
                    ->hint('Masukan password baru untuk merubah password yang lama.')
                ?>
            
                <?= $form
                    ->field($model, 'confirm_password')
                    ->label('Konfirmasi Password')
                    ->passwordInput(['maxlength' => true])
                ?>

                <?= $form
                    ->field($model, 'name')
                    ->textInput(['placeholder' => 'Nama', 'maxlength' => true])
                ?>

                <div class="form-group">
                    <label>Role</label>
                    <div><?= Yii::$app->params['USER_TYPES'][$model->type] ?></div>
                </div>
            </div>

            <div class="card-footer text-right">
                <?= Html::submitButton('Simpan Perubahan', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>