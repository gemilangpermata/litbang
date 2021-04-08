<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property string $id
 * @property string $email
 * @property string $password
 * @property string $new_password
 * @property string $confirm_password
 * @property string $name
 * @property int $type
 * @property string|null $password_reset_token
 * @property string|null $authentication_key
 * @property string $created_at
 * @property string $updated_at
 * @property string $created_by
 * @property string|null $updated_by
 */
class User extends ActiveRecord implements IdentityInterface
{
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_FORGET_PASSWORD = 'forget-password';
    const SCENARIO_RESET_PASSWORD = 'reset-password';

    public $confirm_password;
    public $new_password;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'email', 'password', 'name'], 'required'],
            [['type'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['id', 'email', 'created_by', 'updated_by'], 'string', 'max' => 50],
            [['password', 'new_password', 'password_reset_token', 'authentication_key'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 150],
            [['email'], 'unique'],
            [['email'], 'email'],
            [['id'], 'unique'],
            [
                ['confirm_password'],
                'required',
                'when' => function($model) {
                    return !empty($model->password) && $model->isNewRecord;
                },      
                'whenClient' => "function (attribute, value) {
                    return $('.textInputPassword').length > 0 && $('.textInputPassword').val().length > 0;
                }",
            ],
            [
                ['confirm_password'],
                'required',
                'when' => function($model) {
                    return !empty($model->new_password) && !$model->isNewRecord;
                },      
                'whenClient' => "function (attribute, value) {
                    return $('.textInputNewPassword').length > 0 && $('.textInputNewPassword').val().length > 0;
                }",
            ],
            [
                ['confirm_password'],
                'compare',
                'compareAttribute' => 'password',
                'operator' => '==',
                'when' => function ($model) {
                    return $model->isNewRecord && !empty($model->password);
                },
                'whenClient' => "function (attribute, value) {
                    return $('.textInputPassword').length > 0 && $('.textInputPassword').val().length > 0;
                }",
                'message' => 'Password konfirmasi tidak cocok.'
            ],   
            [
                ['confirm_password'],
                'compare',
                'compareAttribute' => 'new_password',
                'operator' => '==',
                'when' => function ($model) {
                    return !$model->isNewRecord && !empty($model->new_password);
                },
                'whenClient' => "function (attribute, value) {
                    return $('.textInputNewPassword').length > 0 && $('.textInputNewPassword').val().length > 0;
                }",
                'message' => 'Password konfirmasi tidak cocok.'
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

            $this->type = strval($this->type);

            return true;
        }
        return false;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if($insert) {
                $this->setPassword($this->password);
            } else {
                if(!empty($this->new_password)) {
                    $this->setPassword($this->new_password);
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'password' => 'Password',
            'name' => 'Nama',
            'type' => 'Role',
            'password_reset_token' => 'Token Reset Password',
            'authentication_key' => 'Kunci Otentikasi',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($username)
    {
        return static::findOne(['email' => $username]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['PASSWORD_RESET_EXPIRE'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authentication_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->authentication_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function getTypeName() {
        return Yii::$app->params['USER_TYPES'][strval($this->type)];
    }
}
