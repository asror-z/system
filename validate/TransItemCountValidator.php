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
use zetsoft\models\ware\WareTrans;
use zetsoft\models\ware\WareTransItem;
use zetsoft\system\actives\ZValidator;
use zetsoft\system\Az;

class TransItemCountValidator extends ZValidator
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
     * @param WareTransItem $model
     * @param string $attribute
     * @return bool
     */
    public function validateAttribute($model, $attribute)
    {
        global $boot;

        $wareTrans = WareTrans::findOne($model->ware_trans_id);

        if ($wareTrans === null) {
            return false;
        }

        $ware = Ware::findOne($wareTrans->warehouse_from);

        if ($ware !== null) {
            $shopCatalogWare = ShopCatalogWare::find()
                ->where([
                    'ware_id' => $ware->id,
                    'shop_catalog_id' => $model->shop_catalog_id
                ])
                ->one();

//            vd($model->shop_catalog_id);
//            vd($ware->id);
//
//            vdd($shopCatalogWare);

            $message = Az::l("Количество в складе $shopCatalogWare->amount");
            if ($shopCatalogWare->amount < $model->amount) {
                $this->addError($model, $attribute, $message);
                return false;
            } else
                return true;
        }
    }
}
