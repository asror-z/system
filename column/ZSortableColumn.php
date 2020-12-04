<?php

namespace zetsoft\system\column;

use kotchuprik\sortable\assets\SortableAsset;
use kotchuprik\sortable\assets\WidgetCdnAsset;
use kotchuprik\sortable\assets\WidgetLocalAsset;
use yii\helpers\Html;
use yii\web\View;
use zetsoft\system\helpers\ZArrayHelper;

class ZSortableColumn extends \kotchuprik\sortable\grid\Column
{

    protected function renderDataCellContent($model, $key, $index)
    {
        $offset = 0;

        if (is_object($this->grid->dataProvider->pagination)) {
            $offset = $this->grid->dataProvider->pagination->pageSize * $this->grid->dataProvider->pagination->page;
        }

        $dataId = $index;
        
        if (is_array($model))
            $dataId = ZArrayHelper::getValue($model, 'id');

        return Html::tag('div', '&#9776;', [
            'class' => 'sortable-widget-handler',
            'data-id' => $dataId,
            'data-offset' => $offset
        ]);
    }
}
