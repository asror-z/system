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


use DebugBar\DataFormatter\VarDumper\DebugBarHtmlDumper;
use zetsoft\dbcore\ALL\UserCore;
use zetsoft\former\auth\AuthLoginForm;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCatalogWare;
use zetsoft\models\ware\Ware;
use zetsoft\models\ware\WareExit;
use zetsoft\models\ware\WareExitItem;
use zetsoft\system\actives\ZValidator;
use zetsoft\system\Az;

class ExitItemCountValidator extends ZValidator
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
     * @param WareExitItem $model
     * @param string $attribute
     * @return bool
     */
    public function validateAttribute($model, $attribute)
    {

        if (empty($model->ware_exit_id))
            return false;

        $wareExit = WareExit::findOne($model->ware_exit_id);

        if (!$wareExit && empty($wareExit->ware_id))
            return null;

        $ware = Ware::findOne($wareExit->ware_id);

        if ($ware === null)
            return false;

        $shopCatalogWare = ShopCatalogWare::find()
            ->where([
                'ware_id' => $ware->id,
                'shop_catalog_id' => $model->shop_catalog_id,
                'best_before' => $model->best_before
            ])
            ->one();
        
        /** @var ShopCatalogWare $shopCatalogWare */

        if ((int)$shopCatalogWare->amount < (int)$model->amount) {
            $message = Az::l("Количество в складе $shopCatalogWare->amount");
            $this->addError($model, $attribute, $message);
            return false;
        }

        return true;

    }
}
