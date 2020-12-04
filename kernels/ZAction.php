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

namespace zetsoft\system\kernels;


use yii\base\Action;

use zetsoft\models\page\PageAction;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\module\Models;



class ZAction extends Action
{

    use ZCoreTrait;

    public function init()
    {
        parent::init();
        $this->trait();
    }

}
