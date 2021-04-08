<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "area".
 *
 * @property string $id
 * @property string $name
 * @property string|null $parent
 * 
 * @property Area $parentData
 */
class Area extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'area';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id', 'parent'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 200],
            [['id'], 'unique'],
        ];
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if(empty($this->id)) {
                $this->id = strval(round(microtime(true) * 1000));
            }

            return true;
        }
        return false;
    }

    public function afterDelete()
     {
        parent::afterDelete();

        $children = self::find()
            ->where(['parent' => $this->id])
            ->all();

        foreach ($children as $row) {
            $row->delete();
        }
     }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nama',
            'parent' => 'Induk',
        ];
    }

    public function getParentData()
    {
        return $this->hasOne(self::class, ['id' => 'parent']);
    }
}
