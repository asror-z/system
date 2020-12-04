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
use zetsoft\models\shop\ShopOrder;
use zetsoft\system\actives\ZValidator;
use zetsoft\system\Az;

class ZCallCenterValidator extends ZValidator
{


    public function init()
    {
        parent::init();
        $this->message = Az::l('Вы должны ввести дату доставки этого заказа');
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
        
        if ($model->status_callcenter !== ShopOrder::status_callcenter['approved'])
            return true;

        if (!empty($model->$attribute))
            return true;

            $this->addError($this->message);
            return false;
    }

}
