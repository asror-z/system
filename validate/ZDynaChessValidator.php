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
use zetsoft\system\helpers\ZArrayHelper;

class ZDynaChessValidator extends ZValidator
{

    public function init()
    {
        parent::init();
        $this->message = Az::l('Name must be');
    }


    /**
     *
     * Function  validateAttribute
     * @param \yii\base\Model $model
     * @param string $attribute
     * @return  bool|void
     */
    public function validateAttribute($model, $attribute)
    {
    
        if(preg_match('/^[a-zA-Z0-9]{0,}$/', $model->$attribute)) {
            $this->addError($model, $attribute, $this->message);
            return false;
        }
        return true;
    
    }

}
