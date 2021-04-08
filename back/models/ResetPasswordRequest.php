<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

class ResetPasswordRequest extends Model {
    public $email;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            [['email'], 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\app\models\User',
                'targetAttribute' => ['email' => 'email'],
                'message' => 'Email tidak ditemukan.'
            ],
        ];
    }
    
    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'email' => $this->email,
        ]);
        if (!$user) {
            return false;
        }
        
        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }
        
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'reset-password'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['EMAIL_USERNAME'] => Yii::$app->params['EMAIL_DISPLAY_NAME']])
            ->setTo($this->email)
            ->setSubject('Reset Password Sistem Informasi Direktori Litbang')
            ->send();
    }
}
