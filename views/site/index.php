<?php

/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;

if ($keyword) {
    $this->title = 'Hasil pencarian untuk "'. $keyword .'"';
    $this->params['header'] = '<small>' . $this->title . '</small>';
}

if ($category === 'others') {
    $this->params['breadcrumbs'][] = 'Data Lainnya';
}
?>

    <?php
    if ($category !== 'others') {
    ?>
    <div class="row">
        <?php
        foreach($data as $code => $row) {
        ?>
        <div class="col-md-3 col-sm-6 col-12">
            <a
                href="<?= Url::to([$code === 'others' ? 'index' : 'documents', 'category' => $code, 'keyword' => $keyword]) ?>"
                class="info-box text-dark"
            >
                <span class="info-box-icon bg-info"><i class="fa fa-folder-open"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text"><?= $row['name'] ?></span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asDecimal($row['total']) . ' dokumen' ?></span>
                </div>
            </a>
        </div>
        <?php
        }
        ?>
    </div>
    <?php
    } else {
        $template = <<< EOL
            <div class="categories">
                <div class="row">
                    {items}
                </div>
                {pager}
            </div>
        EOL;
    ?>
    <?= ListView::widget([
        'dataProvider' => $data,
        'itemOptions' => [
            'tag' => false,
        ],
        'layout' => $template,
        'itemView' => function ($model) use ($keyword) {
            return '
            <div class="col-md-3 col-sm-6 col-12">
                <a
                    href="'. Url::to(['documents', 'category' => $model['id'], 'keyword' => $keyword]) .'"
                    class="info-box text-dark"
                >
                    <span class="info-box-icon bg-info"><i class="fa fa-folder-open"></i></span>
        
                    <div class="info-box-content">
                        <span class="info-box-text">'. $model['name'] .'</span>
                        <span class="info-box-number">'. Yii::$app->formatter->asDecimal($model['total']) .' dokumen</span>
                    </div>
                </a>
            </div>
            ';
        },
        'pager' => [
            'maxButtonCount' => 3,
            'linkContainerOptions' => [
                'class' => 'page-item',
            ],
            'linkOptions' => [
                'class' => 'page-link',
            ],
            'disabledListItemSubTagOptions' => ['tag' => 'span', 'class' => 'page-link'],
        ],
    ]) ?>
    <?php
    }
    ?>
</div>