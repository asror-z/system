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

namespace zetsoft\system\column;


use kartik\grid\DataColumn;
use kartik\grid\FormulaColumn;
use kartik\grid\GridView;
use yii\grid\SerialColumn;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZTrait;
use zetsoft\system\module\Models;

class ZSerialColumn extends \kartik\grid\SerialColumn
{

    public $exportMenuStyle = 'middle';
    public $exportHeaderMenuStyle = 'middle';
    public $hidden = true;

    use ZCoreTrait;

    public function init()
    {
        parent::init();
        $this->trait();
    }


    public function run($model, $formula) {

        return [
            'attribute' => 'formula',
            'label' => Az::l('Общее'),
            'options' => [

            ],
            'value' => static function () use ($formula, $model) {

                $summ = 0;
                if (!empty($formula)) {

                    foreach ($formula as $item) {
                        $summ += $model->{$item};
                    }

                    return $summ;
                }


                /** @var Models $model */
                $columns = $model->columns;

                foreach ($columns as $key => $item) {
                    $in = $item->dbType === dbTypeInteger;
                    $id = !ZStringHelper::endsWith($key, '_id');
                    $ids = !ZStringHelper::endsWith($key, '_ids');

                    if ($in && $id && $ids)
                        $summ += $model->{$key};

                }

                return $summ;
            }
        ];

    }


}
