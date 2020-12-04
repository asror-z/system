<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\web;

/**
 * This asset bundle provides the [jQuery](http://jquery.com/) JavaScript library.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class JqueryAsset extends AssetBundle
{
    public $sourcePath;
    
    public $js = [
        // 'https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js', 'position' => \yii\web\View::POS_BEGIN
    ];


    
}
