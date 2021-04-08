<?php
namespace app\models;

use yii\base\Model;
use app\models\User;
use yii\base\InvalidArgumentException;

class ResetPassword extends Model {
    public $password;
    public $confirm_password;
    
    /**
     * @var \app\models\User
     */
    private $_user;
    
    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidArgumentException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException('Password reset token cannot be empty.');
        }
        $this->_user = User::findByPasswordResetToken($token);
        if (!$this->_user) {
            throw new InvalidArgumentException('Invalid password reset token');
        }
        parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'confirm_password'], 'required'],
            ['password', 'string', 'min' => 6],
            [
                ['confirm_password'],
                'required',
                'when' => function($model) {
                    return !empty($model->password);
                },      
                'whenClient' => "function (attribute, value) {
                    return $('.textInputPassword').length > 0 && $('.textInputPassword').val().length > 0;
                }",
            ],
            [
                ['confirm_password'],
                'compare',
                'compareAttribute' => 'password',
                'operator' => '==',
                'when' => function ($model) {
                    return !empty($model->password);
                },
                'whenClient' => "function (attribute, value) {
                    return $('.textInputPassword').length > 0 && $('.textInputPassword').val().length > 0;
                }",
                'message' => 'Password konfirmasi tidak cocok.'
            ],
        ];
    }
    
    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        return $user->save(false);
    }
}
