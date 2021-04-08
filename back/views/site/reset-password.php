<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ResetPassword */
/* @var $invalidToken boolean */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Reset Password';
?>

<div class="card-body login-card-body">
    <?php
    if ($invalidToken) {
    ?>
    <p class="login-box-msg p-0 m-0">Link reset password tidak valid atau telah kadaluarsa.</p>
    <?php
    } else {
    ?>

    <p class="login-box-msg">Silahkan masukan password baru anda.</p>

    <?php $form = ActiveForm::begin([
        'id' => 'reset-password-form',
        'fieldConfig' => [
            'options' => [
                'class' => 'mb-3',
            ],
            'template' => '<div class="input-group">{label}{input}</div>{error}',
        ],
    ]); ?>

        <?= $form
            ->field($model, 'password', [
                'inputTemplate' => '
                    {input}
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                ',
            ])
            ->label(false)
            ->passwordInput(['placeholder' => 'Password'])
        ?>

        <?= $form
            ->field($model, 'confirm_password', [
                'inputTemplate' => '
                    {input}
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                ',
            ])
            ->label(false)
            ->passwordInput(['placeholder' => 'Password Konfirmasi'])
        ?>

        <div class="text-right">
            <?= Html::submitButton('Reset Password', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

    <?php
    }
    ?>
</div>