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

namespace zetsoft\widgets\extend;

use kartik\form\ActiveForm;
use kartik\helpers\Html;
use yii\base\InvalidConfigException;
use zetsoft\system\column\ZKDataColumn;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\widgets\dialogs\ZKPopoverXWidget;
use zetsoft\widgets\inputes\ZHInputWidget;
use zetsoft\widgets\inputes\ZKSelect2Widget;
use zetsoft\widgets\inputes\ZKSwitchInputWidget;
use zetsoft\widgets\navigat\ZButtonWidget;

class ZKEditColumn extends ZKDataColumn
{

    public $attribute;
    public $widget;

    /**
     * {@inheritdoc}
     * @throws \yii\base\InvalidConfigException if [[attribute]] is not set.
     */
    public function init()
    {
        parent::init();
        $this->js();
        if (empty($this->attribute)) {
            throw new InvalidConfigException('The "attribute" property must be set.');
        }
    }

    public function js()
    {
        $this->_view->registerJsFile('/system/column/@/assets/editable.js');
    }

    protected function renderForm($model, $key, $index, $widgetOptions)
    {
        $core = [
            'model' => $model,
            'attribute' => $this->attribute,
            'id' => 'edfield-'.$model->id.'-'.$this->attribute
        ];

        echo Html::beginTag('div', ['class' => 'editable-field-'.$model->id.'-'.$this->attribute]);
        
        //ZKPopoverXWidget::begin($widgetOptions);

        $form = ActiveForm::begin([
            'fieldConfig' => ['showLabels' => false],
            'action' => ['/'.$this->moduleId.'/'.$this->controlId.'/edit'],
            'options' => [
                'id' => 'form-'.$model->id.'-'.$this->attribute,
                'class' => 'editable-form',
            ]
        ]);

        $data = $model->columns[$this->attribute]->data;

        if (is_array($data)) {
            $this->options['data'] = $data;
        }

        echo Html::hiddenInput('editableIndex', $index);
        echo Html::hiddenInput('editableKey', $key);
        echo Html::hiddenInput('editableAttribute', $this->attribute);

        echo $form->field($model, $this->attribute)->widget($this->widget, ZArrayHelper::merge($core, $this->options));

        echo Html::submitButton('Submit', ['class' => 'btn btn-primary btn-sm']);
//        echo ZButtonWidget::widget([
//            'types' => ZButtonWidget::Type_Reset,
//            'label' => 'Reset',
//            'icon' => '',
//            'btnSize' => ZButtonWidget::Size_Small,
//        ]);

        echo '<div class="editable-loading lds-ellipsis">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>';

        ActiveForm::end();
        ZKPopoverXWidget::end();
        echo Html::endTag('div');

    }

    public function renderDataCellContent($model, $key, $index)
    {
        $cellData = parent::renderDataCellContent($model, $key, $index);

        $header = "<i class='fas fa-edit'></i> Редактировать: {$model->getAttributeLabel($this->attribute)}";

        if (!is_string($cellData))
            return false;

        $label = "<span class='editable-label'>{$cellData}</span>";

        $widgetOptions = [
            'config' => [
                'header' => $header,
                'toggleLabel' => $label,
                'toggleTag' => 'a',
            ]
        ];

        $this->_view->registerJs("refreshGridField('{$this->grid->id}', '{$model->id}', '{$this->attribute}')");

        ob_start();

        $this->renderForm($model, $key, $index, $widgetOptions);

        return ob_get_clean();
    }
}
