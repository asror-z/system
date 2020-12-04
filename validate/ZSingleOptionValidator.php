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

namespace zetsoft\system\validate;


use zetsoft\dbcore\ALL\UserCore;
use zetsoft\former\auth\AuthLoginForm;
use zetsoft\system\actives\ZValidator;
use zetsoft\system\Az;

class ZSingleOptionValidator extends ZValidator
{


    public function init()
    {
        parent::init();
        $this->message = Az::l('Вы не можете выбрать опцию дважды');
    }


    /**
     *
     * Function  validateAttribute
     * @param AuthLoginForm $model
     * @param string $attribute
     * @return bool
     */
    public function validateAttribute($model, $attribute)
    {
        global $boot;
        /**
         *
         * https://www.amcharts.com/docs/v4/reference/mappolygon/#tooltipX_property
         */
        $data = $this->httpPost($model->className[$attribute]);
        if (!is_array($data)) return true;

        

    }


}
