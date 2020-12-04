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
use zetsoft\widgets\former\ZEditableModalWidget;

class ZEditableModalColumn extends ZKDataColumn
{

    public $editableOptions = [
        'editableValueOptions' => [
            'class' => 'text-success'
        ]
    ];

    public $readonly = false;
    public $widgetClass;
    public $widgetOptions;

    public function renderDataCellContent($model, $key, $index)
    {

        $id = $this->model->className . '-' . $this->attribute . '-' . $key;

        return ZEditableModalWidget::widget([
            'readonly' => $this->readonly,
            'model' => $model,
            'attribute' => $this->attribute,
            'id' => $id,
            'config' => [
                'class' => 'editable-dyna-' . $this->attribute,
                'widgetClass' => $this->widgetClass,
                'options' => $this->widgetOptions,
            ]
        ]);

    }

    use ZCoreTrait;
}

