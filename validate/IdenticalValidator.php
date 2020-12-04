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

class IdenticalValidator extends ZValidator
{

    public $first;
    public $second;
    public $message;


    public function init()
    {
        parent::init();

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
        $first = $this->first;
        $second = $this->second;
        $message = $this->message;
        /**
         *
         * https://www.amcharts.com/docs/v4/reference/mappolygon/#tooltipX_property
         */
        if ($model->$first !== $model->$second) {
            return true;
        }

        $this->addError($model, $attribute, $message);
        return false;
    }
}
