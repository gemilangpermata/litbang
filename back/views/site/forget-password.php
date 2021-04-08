<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ResetPasswordRequest */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Lupa Password';
?>

<div class="card-body login-card-body">
    <p class="login-box-msg">Silahkan masukan email anda. Kami akan mengirim link untuk mereset password anda.</p>

    <?php $form = ActiveForm::begin([
        'id' => 'forget-password-form',
        'fieldConfig' => [
            'options' => [
                'class' => 'mb-3',
            ],
            'template' => '<div class="input-group">{label}{input}</div>{error}',
        ],
    ]); ?>

        <?= $form
            ->field($model, 'email', [
                'inputTemplate' => '
                    {input}
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                ',
            ])
            ->label(false)
            ->textInput(['autofocus' => true, 'placeholder' => 'Email'])
        ?>

        <div class="text-right">
            <?= Html::submitButton('Reset Password', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>