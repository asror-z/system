<?php

use yii\validators\Validator;

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 * Author: Jahongir Qudratov
 */

namespace zetsoft\system\validate;

use zetsoft\system\actives\ZValidator;
use zetsoft\system\Az;

class TransLinkValidator extends ZValidator
{

    public function init()
    {
        parent::init();
        $this->message = Az::l('На счету недостаточно денег');
    }

    public function validateAttribute($model, $attribute)
    {
        if ($model->cpas_trans_form || $model->cpas_land_id)
        {
            if (!$model->$attribute){

                $this->addError($model, $attribute, $this->message);
                return false;
            }
        }
        else{
            return true;
        }



    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        $form = $model->cpas_trans_form;
        $attr = $model->$attribute;
        $return = true;
        if ($model->cpas_trans_form || $model->cpas_land_id)
        {
            $return = $model->$attribute? true : false;
        }
        $errorMessage = Az::l('На счету недостаточно денег');

        return <<<JS
        var land = $('[name="CpasStreamItem[cpas_land_id]"]').val(); 
        var form = $('[name="CpasStreamItem[cpas_trans_form]"]').val(); 
        console.log(land);
        console.log(form);
        if (land === null || form === null || this.value === null)
            messages.push('$errorMessage');
        else
            return true;    
            
                        
JS;

    }

}
