<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 02.10.2015
 * Time: 17:15
 * @var $user \app\models\User
 */
use yii\helpers\Html;

echo 'Здравствуйте, '.Html::encode($user->username).'. ';
echo Html::a('Для смены пароля перейдите по этой ссылке.',
    Yii::$app->urlManager->createAbsoluteUrl(
        [
            '/site/reset-password',
            'code' => $user->verification_code
        ]
    ));