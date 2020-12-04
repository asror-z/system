<?php
/**
 *
 * Author:  Asror Zakirov
 * Created: 29.06.2017 19:06
 * https://www.linkedin.com/in/asror-zakirov-167961a9
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\system\kernels;

use yii\web\View;
use zetsoft\dbitem\core\WebItem;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;


/**
 *
 * @property ZModule $module
 */
class ZView extends View
{

    use ZCoreTrait;
    


    public function init()
    {
        parent::init();
        $this->trait();
    }
     public function beginPage()
     {

         parent::beginPage();
     }

}
