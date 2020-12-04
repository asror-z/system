<?php


namespace zetsoft\system\actives;

use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JqueryAsset;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZJsonHelper;
use zetsoft\system\kernels\ZTrait;
use zetsoft\system\kernels\ZView;


class ZActiveForm extends \kartik\form\ActiveForm
{
    public function registerAssets()
    {
        if (!CLI)
            parent::registerAssets();

            

    }


}
