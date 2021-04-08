<?php

/* @var $this yii\web\View */
/* @var $model app\model\Category */

use app\models\Document;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Edit Dokumen';
$this->params['header'] = 'Edit Dokumen';
$this->params['breadcrumbs'][] = 'Dokumen';
$this->params['breadcrumbs'][] = 'Perencanaan';
$this->params['breadcrumbs'][] = 'Edit';

$user = Yii::$app->user->identity;
$isAdministrator = $user->type === intval(Yii::$app->params['USER_TYPE_ADMINISTRATOR']);
$isOperator = $user->type === intval(Yii::$app->params['USER_TYPE_OPERATOR']);
$isValidator = $user->type === intval(Yii::$app->params['USER_TYPE_VALIDATOR']);
$isPublisher = $user->type === intval(Yii::$app->params['USER_TYPE_PUBLISHER']);
$status = strval($model->status);
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

                <a
                    href="http://docs.google.com/viewer?url=<?= $model->fileUrl ?>"
                    target="_blank"
                    class="alert alert-info d-flex align-items-start text-white d-block"
                >
                    <div>
                        <i class="fa fa-file-alt"></i>&nbsp;
                    </div>
                    <div style="flex-grow: 1;">
                        <?= $model->filename ?>
                    </div>
                </a>

                <div class="form-group">
                    <label class="control-label">Diupload</label>
                    <div>
                        <?= 'by <strong>' . $model->creator->name . '</strong> pada <strong>' . Yii::$app->formatter->asDatetime($model->created_at, 'php:d F Y H:i:s') . '</strong>' ?>
                    </div>
                </div>

                <?php
                if ($model->updated_at && $model->updated_at !== $model->created_at) {
                ?>
                <div class="form-group">
                    <label class="control-label">Diperbaharui</label>
                    <div>
                        <?= 'by <strong>' . $model->modifier->name . '</strong> pada <strong>' . Yii::$app->formatter->asDatetime($model->updated_at, 'php:d F Y H:i:s') . '</strong>' ?>
                    </div>
                </div>
                <?php
                }
                ?>
            </div>

            <div class="card-footer text-right">
                <div class="form-inline justify-content-end">
                    <?php
                    $statusOptions = [$model->status => 'No'];
                    if ($status === Yii::$app->params['DOC_STATUS_DRAFT'] && ($isValidator || $isAdministrator) && $model->created_by !== $user->id) {
                        $statusOptions[Yii::$app->params['DOC_STATUS_APPROVED']] = Yii::$app->params['DOC_STATUSES'][Yii::$app->params['DOC_STATUS_APPROVED']];
                    } else if ($status === Yii::$app->params['DOC_STATUS_APPROVED']) {
                        if (($isValidator || $isAdministrator) && $model->created_by !== $user->id) {
                            $statusOptions[Yii::$app->params['DOC_STATUS_DRAFT']] = Yii::$app->params['DOC_STATUSES'][Yii::$app->params['DOC_STATUS_DRAFT']];
                        }
                        if ($isPublisher || $isAdministrator) {
                            $statusOptions[Yii::$app->params['DOC_STATUS_PUBLISHED']] = Yii::$app->params['DOC_STATUSES'][Yii::$app->params['DOC_STATUS_PUBLISHED']];
                        }
                    } else if ($status === Yii::$app->params['DOC_STATUS_PUBLISHED'] && ($isPublisher || $isAdministrator)) {
                        $statusOptions[Yii::$app->params['DOC_STATUS_APPROVED']] = 'Unpublished';
                    } else {
                        $statusOptions = [];
                    }
                    ?>
                    <?php
                    if(!empty($statusOptions)) {
                    ?>
                    <label>Ubah Status: &nbsp;</label>
                    <?= $form->field($model, 'status', [
                        'template' => '{input}',
                        'options' => ['class' => ''],
                    ])->dropDownList($statusOptions, [
                        'class' => 'custom-select',
                    ]) ?>
                    <?php
                    }
                    ?>
                    <?= Html::submitButton('Simpan Perubahan', ['class' => 'btn ml-2 btn-primary']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>