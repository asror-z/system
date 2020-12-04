<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    04.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\actives;


use zetsoft\dbitem\data\Event;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\kernels\ZActiveTrait;
use zetsoft\system\kernels\ZTrait;
use yii\base\Model;

class ZModel extends Model
{
    use ZCoreTrait;
    use ZActiveTrait;
    

    public function init()
    {
        parent::init();
        $this->trait();

        $this->kernel();


    }
}
