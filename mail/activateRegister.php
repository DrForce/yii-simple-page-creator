<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 02.10.2015
 * Time: 17:48
 * @var $user \app\models\User
 */
use yii\helpers\Html;

echo 'Здравствуйте, '.Html::encode($user->username).'. ';
echo Html::a('Для активации аккаунта перейдите по этой ссылке.',
    Yii::$app->urlManager->createAbsoluteUrl(
        [
            '/site/activate-account',
            'code' => $user->verification_code
        ]
    ));