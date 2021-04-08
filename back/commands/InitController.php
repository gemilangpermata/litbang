<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use app\models\User;
use yii\console\Controller;
use yii\console\ExitCode;

class InitController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex()
    {
        $user = new User();
        $user->email = Yii::$app->params['EMAIL_USERNAME'];
        $user->password = Yii::$app->params['EMAIL_PASSWORD'];
        $user->confirm_password = Yii::$app->params['EMAIL_PASSWORD'];
        $user->name = 'Administrator';
        $user->type = Yii::$app->params['USER_TYPE_ADMINISTRATOR'];

        if ($user->save()) {
            return ExitCode::OK;
        }

        return ExitCode::DATAERR;
    }
}
