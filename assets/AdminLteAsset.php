<?php
namespace app\assets;

use yii\web\AssetBundle;

class AdminLteAsset extends AssetBundle {

  public $sourcePath = '@vendor/almasaeed2010/adminlte';

  public $css = [
    'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback',
    'dist/css/adminlte.min.css',
    'plugins/fontawesome-free/css/all.min.css',
    'plugins/icheck-bootstrap/icheck-bootstrap.min.css',
    'plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css',
  ];
  public $js = [
    'dist/js/adminlte.min.js',
    'plugins/bootstrap/js/bootstrap.bundle.min.js',
    'plugins/sweetalert2/sweetalert2.min.js',
  ];
}