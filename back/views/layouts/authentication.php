<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\AppAsset;

AppAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) . ($this->title ? ' | ' : '') . Yii::$app->params['APP_NAME'] ?></title>
        <?php $this->head() ?>
    </head>
    <body class="hold-transition login-page">
    <?php $this->beginBody() ?>
        <div class="login-box">
            <div class="login-logo">
                <a href="javascript:void(0)"><?= Yii::$app->params['APP_NAME'] ?></a>
            </div>
            <div class="card">
                <?= $content ?>
            </div>
        </div>
    <?php $this->endBody() ?>

    <?php
    $notify = \Yii::$app->session->getFlash('NOTIFY', []);
    $shouldNotify = !empty($notify);
    $notifyCommand = $shouldNotify ? '
        Toast.fire({
            icon: "'. $notify['type'] .'",
            title: "'. $notify['message'] .'",
        })
    ' : '';
    ?>
    <script type="text/javascript">
    var Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });

    <?= $notifyCommand ?>    
    </script>
    </body>
</html>
<?php $this->endPage() ?>