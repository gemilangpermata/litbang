<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\HttpException;

$code = $exception->getCode();
if ($exception instanceof HttpException) {
    $code = $exception->statusCode;
}

$this->title = $code === 404 ? 'Halaman Tidak Ditemukan' : 'Halaman Error';
$header = $code === 404 ? 'Halaman tidak ditemukan.' : 'Terjadi kesalahan';
?>

<h2 class="headline text-warning"> <?= $code ?></h2>

<div class="error-content">
    <h3><i class="fas fa-exclamation-triangle text-warning"></i> Ups! <?= $header ?></h3>

    <?php
    if ($code === 404) {
    ?>
    <p>
        Kami tidak dapat menemukan halaman yang anda cari.
        Namun, kamu bisa <a href="<?= Url::home() ?>">kembali ke beranda</a> atau cari halaman yang lainnya dengan form pencarian.
    </p>

    <?= Html::beginForm(Url::to(['index', 'keyword' => null]), 'get', [
        'class' => 'search-form'
    ]) ?>
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Pencarian">

            <div class="input-group-append">
                <button type="submit" name="submit" class="btn btn-warning">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    <?= Html::endForm() ?>
    <?php
    } else {
    ?>
    <p>
        Kami tidak dapat membuka halaman yang anda minta.
        Namun, kamu masih bisa <a href="<?= Url::home() ?>">kembali ke beranda</a>.
    </p>
    <?php
    }
    ?>
</div>