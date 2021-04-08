<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);

$user = Yii::$app->user->identity;
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
    <body class="hold-transition sidebar-mini">
    <?php $this->beginBody() ?>

        <div class="wrapper">
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                            <img src=<?= Yii::getAlias('@web/img/user.jpeg') ?> class="user-image img-circle elevation-2 mr-0 mr-md-2" alt="User Image">
                            <span class="d-none d-md-inline"><?= $user->name ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <!-- User image -->
                            <li class="user-header bg-primary">
                                <img src=<?= Yii::getAlias('@web/img/user.jpeg') ?> class="img-circle elevation-2" alt="User Image">

                                <p>
                                    <?= $user->name . ' - ' . Yii::$app->params['USER_TYPES'][strval($user->type)] ?>
                                    <small>Terdaftar pada <?= Yii::$app->formatter->asDatetime($user->created_at, "php:F, Y") ?></small>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <a href="<?= Url::to(['site/profile']) ?>" class="btn btn-default btn-flat">Profil</a>
                                <a href="<?= Url::to(['site/logout']) ?>" data-method="post" class="btn btn-default btn-flat float-right">Keluar</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <a href="javascript:void(0)" class="brand-link">
                    <span class="brand-text font-weight-light">Litbang</span>
                </a>
                <div class="sidebar">
                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                            <li class="nav-item">
                                <a href="<?= Url::to(['users/index']) ?>" class="nav-link <?= Yii::$app->controller->id === 'users' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>
                                        Pengguna
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= Url::to(['areas/index']) ?>" class="nav-link <?= Yii::$app->controller->id === 'areas' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-map-marker"></i>
                                    <p>Daerah</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= Url::to(['categories/index']) ?>" class="nav-link <?= Yii::$app->controller->id === 'categories' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-boxes"></i>
                                    <p>Kategori</p>
                                </a>
                            </li>
                            <li
                                class="nav-item <?=
                                    Yii::$app->controller->id === 'regulation-documents'
                                    || Yii::$app->controller->id === 'rnd-documents'
                                    || Yii::$app->controller->id === 'planning-documents'
                                    || Yii::$app->controller->id === 'other-documents'
                                    ? 'menu-is-opening menu-open'
                                    : ''
                                ?>"
                            >
                                <a href="#" class="nav-link <?=
                                    Yii::$app->controller->id === 'regulation-documents'
                                    || Yii::$app->controller->id === 'rnd-documents'
                                    || Yii::$app->controller->id === 'planning-documents'
                                    || Yii::$app->controller->id === 'other-documents'
                                    ? 'active'
                                    : ''
                                ?>">
                                    <i class="nav-icon fas fa-folder-open"></i>
                                    <p>
                                        Dokumen
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="<?= Url::to(['regulation-documents/index']) ?>" class="nav-link <?= Yii::$app->controller->id === 'regulation-documents' ? 'active' : '' ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Perda & Perbup</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= Url::to(['rnd-documents/index']) ?>" class="nav-link <?= Yii::$app->controller->id === 'rnd-documents' ? 'active' : '' ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Litbang</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= Url::to(['planning-documents/index']) ?>" class="nav-link <?= Yii::$app->controller->id === 'planning-documents' ? 'active' : '' ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Perencanaan</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= Url::to(['other-documents/index']) ?>" class="nav-link <?= Yii::$app->controller->id === 'other-documents' ? 'active' : '' ?>">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Lainnya</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1><?= isset($this->params['header']) ? $this->params['header'] : '' ?></h1>
                            </div>
                            <div class="col-sm-6">
                                <?= Breadcrumbs::widget([
                                    'homeLink' => [
                                        'label' => '<i class="fas fa-home"></i>',
                                        'url' => Yii::$app->homeUrl,
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
                    <div class="<?= Yii::$app->controller->id . '/' . Yii::$app->controller->action->id === Yii::$app->components['errorHandler']['errorAction'] ? 'error-page' : 'container-fluid' ?>">
                        <?= $content ?>
                    </div>
                </section>
            </div>

            <footer class="main-footer">
                Copyright &copy; <?= date('Y') ?> <a href="javascript:void(0)"><?= Yii::$app->params['COMPANY'] ?></a>.
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
