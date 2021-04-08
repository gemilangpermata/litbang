<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\Login */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Masuk';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card-body login-card-body">
    <p class="login-box-msg">Masukan email dan password anda.</p>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
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

        <div class="row">
            <div class="col-8">
                <div class="icheck-primary">
                    <input type="checkbox" name="remember" id="remember" value="1">
                    <label for="remember">
                        Ingat Saya
                    </label>
                </div>
            </div>
            <div class="col-4">
                <?= Html::submitButton('Masuk', ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
            </div>
        </div>

        <p class="mb-0 mt-2">
            <a href="<?= Url::to(['forget-password']) ?>">Saya lupa password</a>
        </p>
    <?php ActiveForm::end(); ?>
</div>
