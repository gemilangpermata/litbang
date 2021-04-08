<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\Login;
use app\models\ResetPassword;
use app\models\ResetPasswordRequest;
use app\models\User;
use yii\base\InvalidArgumentException;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'index', 'profile'],
                'rules' => [
                    [
                        'actions' => ['logout', 'index', 'profile'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        $this->layout = 'authentication';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new Login();
        $model->scenario = User::SCENARIO_LOGIN;
        if ($model->load(Yii::$app->request->post())) {
            $model->remember_me = boolval(Yii::$app->request->post('remember'));

            if ($model->login()) {
                return $this->goBack();
            }
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }
    
    public function actionForgetPassword()
    {
		if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'authentication';
        
        $model = new ResetPasswordRequest();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $success = $model->sendEmail();
            $model->email = $success ? '' : $model->email;
            Yii::$app->session->setFlash('NOTIFY', [
                'type' => $success ? 'success' : 'error',
                'message' => $success
                    ? 'Kami telah mengirim link untuk mereset password ke email anda. Silahkan periksa folder inbox atau spam pada email anda.'
                    : 'Maaf, sistem gagal link untuk mereset password.',
            ]);
        }
        
        return $this->render('forget-password', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'authentication';
        
        $invalidToken = false;
        $model = null;
        try {
            $model = new ResetPassword($token);
        } catch (InvalidArgumentException $e) {
            $invalidToken = true;
        }
        
        if (!$invalidToken && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $success = $model->resetPassword();
            Yii::$app->session->setFlash('NOTIFY', [
                'type' => $success ? 'success' : 'error',
                'message' => $success
                    ? 'Password anda berhasil diubah, anda bisa dapat log in kembali.'
                    : 'Gagal mengubah password anda, silahkan hubungi administrator.',
            ]);

            return $this->redirect(['login']);
        }
        
        return $this->render('reset-password', [
            'model' => $model,
            'invalidToken' => $invalidToken,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionProfile()
    {
        $model = User::findOne(Yii::$app->user->id);
        if($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('NOTIFY', [
                'type' => 'success',
                'message' => 'Perubahan berhasil disimpan.'
            ]);
            return $this->refresh();
        }

        return $this->render('profile', [
            'model' => $model,
        ]);
    }
}
