<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\assets;


use zetsoft\system\kernels\ZTrait;
use yii\web\AssetBundle;
use zetsoft\system\kernels\ZView;

class ZAssetBundle extends AssetBundle
{
    public $cssOptions = [
        'position' => ZView::POS_HEAD
    ];

    
    public $jsOptions = [
        'position' => ZView::POS_HEAD
    ];

}
