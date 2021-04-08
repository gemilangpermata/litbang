<?php

namespace app\models;

use Yii;
use yii\base\Model;

class Login extends Model
{
    public $email;
    public $password;
    public $confirm_password;
    public $remember_me = false;
    private $user = false;

    public function rules()
    {
        return [
            [['email'], 'required', 'on' => [User::SCENARIO_LOGIN, User::SCENARIO_FORGET_PASSWORD]],
            [['email', 'confirm_password', 'password'], 'safe'],
            ['password', 'required', 'on' => [User::SCENARIO_LOGIN, User::SCENARIO_RESET_PASSWORD]],
            ['confirm_password', 'required', 'on' => [User::SCENARIO_RESET_PASSWORD]],
            [['confirm_password'], 'compare', 'compareAttribute' => 'password', 'operator' => '==', 'on' => User::SCENARIO_RESET_PASSWORD],
            ['remember_me', 'boolean'],
            ['password', 'validatePassword', 'on' => User::SCENARIO_LOGIN],
		];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Email',
            'password' => 'Password',
            'remember_me' => 'Ingat Saya',
            'confirm_password' => 'Password Konfirmasi',
        ];
    }
    

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Email atau password salah.');
            }
        }
    }

    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            return Yii::$app->user->login($user, $this->remember_me ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Find user by email
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->user === false) {
            $this->user = User::findByEmail($this->email);
        }

        return $this->user;
    }
}
