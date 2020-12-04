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
use zetsoft\widgets\former\ZEditableIframeWidget2;

class ZEditableSweetColumn2 extends ZKDataColumn
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

        $id = $model->className . '-' . $this->attribute . '-' . $key;
        return ZEditableIframeWidget2::widget([
            'model' => $model,
            'attribute' => $this->attribute,
            'widgetClass' => $this->widgetClass,
            'widgetOptions' => $this->widgetOptions,
            'readonly' => $this->readonly,
            'id' => $id,
            'config' => [
                'successChange' => $this->successChange,
                'editableUrl' => $this->editableUrl,
                'formId' => 'edit-form',
                'class' => 'editable-dyna-' . $this->attribute,
                'modalId' => 'ravshan',
                'width' => '500px',
                'height' => '300px',
                'key' => $key,
                'index' => $index,

            ]
        ]);

    }

    use ZCoreTrait;
}
