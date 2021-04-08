<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);

$user = Yii::$app->user->identity;
$keyword = Yii::$app->request->get('keyword');
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
    <body class="layout-top-nav">
    <?php $this->beginBody() ?>

        <div class="wrapper">
            <nav class="main-header navbar navbar-expand-md navbar-white navbar-light">
                <div class="container">
                    <a href="<?= Url::home() ?>" class="navbar-brand"><?= Yii::$app->params['APP_NAME'] ?></a>

                    <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                        <form class="form-inline ml-0 ml-md-3">
                            <div class="input-group input-group-sm">
                                <input name="keyword" value="<?= $keyword ?>" class="form-control form-control-navbar" type="search" placeholder="Pencarian" aria-label="Pencarian">
                                <div class="input-group-append">
                                    <button class="btn btn-navbar" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </nav>

            <div class="content-wrapper">
                <section class="content-header">
                    <div class="container">
                        <div class="row mb-2">
                            <div class="col-sm-<?= isset($this->params['breadcrumbs']) && !empty($this->params['breadcrumbs']) ? '6' : '12' ?>">
                                <h1><?= isset($this->params['header']) ? $this->params['header'] : '' ?></h1>
                            </div>
                            <div class="col-sm-6">
                                <?= Breadcrumbs::widget([
                                    'homeLink' => [
                                        'label' => '<i class="fas fa-home"></i>',
                                        'url' => Url::to(['index', 'keyword' => $keyword]),
                                        'encode' => false,
                                    ],
                                    'options' => [
                                        'class' => 'breadcrumb float-sm-right',
                                    ],
                                    'itemTemplate' => '<li class="breadcrumb-item">{link}</li>',
                                    'activeItemTemplate' => '<li class="breadcrumb-item active">{link}</li>',
                                    'encodeLabels' => false,
                                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="content">
                    <div class="<?= Yii::$app->controller->id . '/' . Yii::$app->controller->action->id === Yii::$app->components['errorHandler']['errorAction'] ? 'error-page' : 'container' ?>">
                        <?= $content ?>
                    </div>
                </section>
            </div>

            <footer class="main-footer">
                <div class="container">
                    Copyright &copy; <?= date('Y') ?> <a href="javascript:void(0)"><?= Yii::$app->params['COMPANY'] ?></a>.
                </div>
            </footer>
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
