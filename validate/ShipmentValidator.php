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
 */

namespace zetsoft\system\validate;

use zetsoft\models\place\PlaceAdress;
use zetsoft\models\shop\ShopCourier;
use zetsoft\system\actives\ZValidator;
use zetsoft\system\Az;

class ShipmentValidator extends ZValidator
{

    public function validateAttribute($model, $attribute)
    {

        $shop_courier_id = $model->shop_courier_id;
        $shop_courier = ShopCourier::findOne($shop_courier_id);

        $place_region_id = Az::$app->market->courier->placeAdress($shop_courier);

        $place_address = PlaceAdress::findOne([
            'place_region_id' => $place_region_id
        ]);


        if (!$place_address) {
            $title = Az::l('В регионе данного курьера отсутствуют заказы');

            $this->addError($model, $attribute, $title);
        }
    }

}
