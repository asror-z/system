<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\debug;

use yii\web\AssetBundle;

/**
 * Debugger asset bundle
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DebugAsset extends AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@root/vendors/kernel/yiisofts/vendor/yiisoft/yii2-debug/src/assets';

    /**
     * {@inheritdoc}
     */
    public $css = [
        'css/main.css',
        'css/toolbar.css',
    ];
    /**
     * {@inheritdoc}
     */
    public $js = [
        'js/polyfill.min.js',
        'js/bs4-native.min.js'
    ];
}
