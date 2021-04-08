<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for table "document".
 *
 * @property string $id
 * @property string $no
 * @property string $name
 * @property string $category
 * @property string $area
 * @property string $pic
 * @property int $year
 * @property int $status
 * @property string $filename
 * @property string $created_at
 * @property string $updated_at
 * @property string $created_by
 * @property string|null $updated_by
 */
class Document extends ActiveRecord
{
    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'no', 'name', 'category', 'pic', 'filename', 'year'], 'required'],
            [['status', 'year'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['id', 'category', 'created_by', 'updated_by'], 'string', 'max' => 50],
            [['no'], 'string', 'max' => 30],
            [['name', 'pic'], 'string', 'max' => 200],
            [['filename'], 'string', 'max' => 250],
            [['no', 'category'], 'unique', 'targetAttribute' => ['no', 'category']],
            [['id'], 'unique'],
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'doc, docx, ppt, pptx, xls, xlsx, pdf'],
            [
                ['area'],
                'required',
                'when' => function($model) {
                    return $model->category !== Yii::$app->params['DOC_CATEGORY_PERDA_PERBUP'] && $model->category !== Yii::$app->params['DOC_CATEGORY_PLANNING'] && $model->category !== Yii::$app->params['DOC_CATEGORY_LITBANG'];
                },
            ],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'app\library\BlameableBehavior',
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if(empty($this->id)) {
                $this->id = strval(round(microtime(true) * 1000));
            }

            $this->status = strval($this->status);

            $this->file = UploadedFile::getInstance($this, 'file');
            if ($this->isNewRecord || !empty($this->file)) {
                $this->uploadFile();
            }

            return true;
        }
        return false;
    }

    public function afterDelete()
    {
        parent::afterDelete();

        if (is_file($this->filePath)) {
            unlink($this->filePath);
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if(!$insert){
            if (isset($changedAttributes['filename']) && is_file($this->getFilePath($changedAttributes['filename']))) {
                unlink($this->getFilePath(($changedAttributes['filename'])));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'no' => 'Nomor',
            'name' => 'Nama Dokumen',
            'area' => 'Daerah',
            'category' => 'Kategori',
            'pic' => 'Penanggungjawab',
            'filename' => 'Filename',
            'year' => 'Tahun',
            'created_at' => 'Diupload',
            'updated_at' => 'Diperbaharui',
            'created_by' => 'Diupload Oleh',
            'updated_by' => 'Diperbaharui Oleh',
        ];
    }

    public function getCategoryName() {
        $name = isset(Yii::$app->params['DOC_CATEGORIES'][$this->category]) ? Yii::$app->params['DOC_CATEGORIES'][$this->category] : null;

        if (!$name) {
            $category = Category::findOne(['id' => $this->category]);
            $name = $category ? $category->name : null;
        }

        return $name;
    }

    public function getStatusName() {
        return Yii::$app->params['DOC_STATUSES'][strval($this->status)];
    }

    public function getCategoryData()
    {
        return $this->hasOne(Category::class, ['id' => 'category']);
    }

    public function getAreaData()
    {
        return $this->hasOne(Area::class, ['id' => 'area']);
    }

    public function generateFilename() {
        $this->file = UploadedFile::getInstance($this, 'file');
        if(!empty($this->file)) {
            return $this->file->baseName . ' - ' . $this->id . '.' . $this->file->extension;
        }
        
        return null;
    }

    public function getFileUrl() {
        $ext = pathinfo($this->filename, PATHINFO_EXTENSION);
        $basename = pathinfo($this->filename, PATHINFO_FILENAME);
        return Url::base(true) . '/' . Yii::$app->params['DOCUMENTS_DIR'] . $basename . ' - ' . $this->id . '.' . $ext;
    }

    public function getFilePath($filename = null) {
        $ext = pathinfo($filename ?: $this->filename, PATHINFO_EXTENSION);
        $basename = pathinfo($filename ?: $this->filename, PATHINFO_FILENAME);
        return $this->getUploadPath() . $basename . ' - ' . $this->id . '.' . $ext;
    }
    
    public function getUploadPath()
    {
        return Yii::getAlias(Yii::$app->params['UPLOAD_DIR']);
    }

    public function uploadFile()
    {
        if ($this->hasErrors()) {
            return false;
        }

        $this->file = UploadedFile::getInstance($this, 'file');
        if (empty($this->file)) {
            $this->addError('file', 'Kamu harus melampirkan file.');
            return false;
        }
        
        $this->filename = $this->file->name;
        $path = $this->getUploadPath() . $this->generateFilename();
        if (!$this->file->saveAs($path)) {
            $this->addError('file', 'Terjadi kesalahan saat mengupload file, silahkan hubungi administrator.');
            return false;
        }

        $this->file = null;
        return true;
    }

    public function getCreator() {
        return User::findOne(['id' => $this->created_by]);
    }

    public function getModifier() {
        return User::findOne(['id' => $this->updated_by]);
    }

    public static function getYears() {
        $years = [];
        for ($i = 1900; $i <= intval(date('Y')) + 5; $i++) {
            $years[strval($i)] = strval($i);
        }

        return $years;
    }
}
