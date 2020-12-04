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

class ZEditableSweetColumn extends ZKDataColumn
{

    public $editableOptions = [
        'editableValueOptions' => [
            'class' => 'text-success'
        ]
    ];


    public $readonly = false;
    public $defaultSort;
    public $sort;
    public $objModel = null;
    public $widgetClass;
    public $widgetOptions;

    public function renderDataCellContent($model, $key, $index)
    {

        $id = $this->model->className . '-' . $this->attribute . '-' . $key;

        $readonly = $this->readonly;

        if (is_callable($readonly)) {
            $this->readonly = $readonly($model, $key, $index, $this);
        }

        $model->configs = $this->model->configs;
        $model->columns();

        return ZEditableIframeWidget::widget([
            'model' => $model,
            'attribute' => $this->attribute,
            'widgetClass' => $this->widgetClass,
            'widgetOptions' => $this->widgetOptions,
            'readonly' => $this->readonly,
            'id' => $id,
            'config' => [
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

}

