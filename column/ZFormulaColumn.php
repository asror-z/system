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
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZTrait;
use zetsoft\system\module\Models;

class ZFormulaColumn extends FormulaColumn
{

    public $vAlign = 'middle';
    public $hAlign = 'center';

    public $columnKey;
    public $hidden = false;
    public $noWrap = false;

    public $mergeHeader = false;

    public $hiddenFromExport = false;
    public $exportMenuStyle = [];
    public $xlFormat = null;

    public $pageSummaryFunc = GridView::F_SUM;
    public $pageSummaryOptions = [
        'prepend' => null,
        'append' => null,
        'colspan' => null,
        'data-colspan-dir' => 'ltr',
    ];
    public $pageSummaryFormat = 'text';
    public $hidePageSummary = false;

    use ZCoreTrait;

    public function init()
    {
        parent::init();
        $this->trait();
    }


    public static function run($model, $formula) {

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
