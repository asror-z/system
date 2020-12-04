<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    29.05.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\kernels;

use yii\base\Module;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZInflector;

class ZModule extends Module
{

    use ZCoreTrait;

    public function init()
    {
        parent::init();
        $this->trait();
    }


}

