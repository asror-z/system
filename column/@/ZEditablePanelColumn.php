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


use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\kernels\ZTrait;
use zetsoft\widgets\former\ZEditableIframeWidget;

class ZEditablePanelColumn extends ZKDataColumn
{

    public $editableOptions = [
        'editableValueOptions' => [
            'class' => 'text-success'
        ]
    ];

    public $readonly = false;
    public $defaultSort;
    public $widgetClass;
    public $widgetOptions;

    public function renderDataCellContent($model, $key, $index)
    {

        $id = $this->model->className . '-' . $this->attribute . '-' . $key;

        
        return ZEditableIframeWidget::widget([
            'model' => $model,
            'attribute' => $this->attribute,
            'widgetClass' => $this->widgetClass,
            'readonly' => $this->readonly,
            'id' => $id,
            'config' => [
                'type' => ZEditableIframeWidget::type['panel'],
                'class' => 'editable-dyna-' . $this->attribute,
                'options' => $this->widgetOptions,
                'key' => $key,
                'index' => $index,
            ]
        ]);

    }

    use ZCoreTrait;
}

