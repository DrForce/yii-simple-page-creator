<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SiteAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/bootstrap.min.css',
        'css/fileinput.min.css',
        'css/jquery.tagsinput.css',
        'css/bootstrap-switch.css',
        'css/bootstrap-datetimepicker.min.css',
        'css/main.css'
    ];
    public $js = [
        'js/jquery-2.1.4.js',
        'js/fileinput.min.js',
        'js/jquery.tagsinput.js',
        'js/bootstrap-switch.min.js',
        'js/bootstrap-datetimepicker.min.js',
        'js/main.js'
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];
    public $depends = [
        'yii\web\YiiAsset'
    ];
}

