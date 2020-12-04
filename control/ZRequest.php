<?php
/**
 *
 * Author:  Asror Zakirov
 * Created: 29.06.2017 19:06
 * https://www.linkedin.com/in/asror-zakirov-167961a9
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\system\control;


use yii\web\Request;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZInflector;
use yii\console\Controller;


class ZRequest extends Request
{
    use ZCoreTrait;

    public function validateCsrfToken($clientSuppliedToken = null)
    {
        if (Az::$app->controller->module->id === "gridview")
            return true;

        if (!Az::$app->controller->enableCsrfValidation)
            return true;

        return parent::validateCsrfToken($clientSuppliedToken);
    }


}
