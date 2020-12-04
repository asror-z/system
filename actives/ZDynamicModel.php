<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\actives;


use yii\base\DynamicModel;
use zetsoft\dbitem\data\Form;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\kernels\ZActiveTrait;
use zetsoft\system\kernels\ZTrait;
use zetsoft\widgets\inputes\ZHInputWidget;
use zetsoft\widgets\inputes\ZHTextareaWidget;
use zetsoft\widgets\inputes\ZKSelect2Widget;

class ZDynamicModel extends DynamicModel
{

    use ZCoreTrait;
    use ZActiveTrait;

    public function init()
    {
        parent::init();
        $this->trait();
    }

    public function __clone()
    {
        parent::__clone();
        $this->configs = clone $this->configs;
    }


}
