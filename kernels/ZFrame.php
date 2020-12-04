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


use yii\base\BaseObject;
use zetsoft\system\control\ZCoreTrait;

class ZFrame extends BaseObject
{

    public $layout;



    use ZCoreTrait;

    public function layout() {
        
    }

    public function init()
    {
        parent::init();
        $this->trait();
        $this->layout();
    }


}
