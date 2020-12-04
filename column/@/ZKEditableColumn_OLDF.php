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


use Closure;
use kartik\base\Config;
use kartik\editable\Editable;
use kartik\grid\EditableColumn;
use kartik\grid\EditableColumnAsset;
use kartik\grid\GridView;
use kartik\popover\PopoverX;
use rmrevin\yii\fontawesome\FA;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use zetsoft\dbitem\data\FormDb;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZHTML;
use zetsoft\system\helpers\Zicon;
use zetsoft\system\kernels\ZTrait;
use zetsoft\system\module\Models;
use zetsoft\widgets\inputes\ZHInputWidget;

class ZKEditableColumn_OLDF extends ZKDataColumn
{

    public $refreshGrid = false;
    public $readonly = false;

    public $header;
    public $footer = '{buttons}';
    public $contentOptions = [];


    public $editableOptions = [];
    protected $_editableOptions = [];

    protected $_css;

    /**
     *
     * Function  run
     * @param FormDb $column
     * @return  \Closure
     */

    public function init()
    {
        parent::init();
        $this->trait();
        $this->_css = 'kv-edcol-' . hash('crc32', uniqid(rand(1, 100), true));
        if ($this->refreshGrid) {
            EditableColumnAsset::register($this->_view);
        }
    }


    public function renderDataCellContent($model, $key, $index)
    {
        $readonly = $this->readonly;
        if ($readonly instanceof Closure) {
            $readonly = call_user_func($readonly, $model, $key, $index, $this);
        }
        if ($readonly === true) {
            return parent::renderDataCellContent($model, $key, $index);
        }
        $this->_editableOptions = $this->editableOptions;
        if (!empty($this->editableOptions) && $this->editableOptions instanceof Closure) {
            $this->_editableOptions = call_user_func($this->editableOptions, $model, $key, $index, $this);
        }
        if (!is_array($this->_editableOptions)) {
            $this->_editableOptions = [];
        }
        if (empty($this->_editableOptions['class'])) {
            Config::checkDependency('editable\Editable', 'yii2-editable', 'for GridView EditableColumn');
        } elseif (!class_exists($this->_editableOptions['class'])) {
            throw new InvalidConfigException(
                "The widget class '" . $this->_editableOptions['class'] . "' set in `editableOptions` does not exist."
            );
        }
        $options = ArrayHelper::getValue($this->_editableOptions, 'containerOptions', []);
        ZHTML::addCssClass($options, $this->_css);
        $this->_editableOptions['containerOptions'] = $options;
        if ($this->grid->pjax && empty($this->_editableOptions['pjaxContainerId'])) {
            $this->_editableOptions['pjaxContainerId'] = $this->grid->getPjaxContainerId();
        }
        if (!isset($key)) {
            throw new InvalidConfigException('Invalid or no primary key found for the grid data.');
        }
        $strKey = !is_string($key) && !is_numeric($key) ? (is_array($key) ? Json::encode($key) : (string)$key) : $key;
        if ($this->attribute !== null) {
            $this->_editableOptions['model'] = $model;
            $this->_editableOptions['attribute'] = "[{$index}]{$this->attribute}";
        } elseif (empty($this->_editableOptions['name']) && empty($this->_editableOptions['model']) ||
            !empty($this->_editableOptions['model']) && empty($this->_editableOptions['attribute'])
        ) {
            throw new InvalidConfigException(
                "You must setup the 'attribute' for your EditableColumn OR set one of 'name' OR 'model' & 'attribute'" .
                " in 'editableOptions' (Exception at index: '{$index}', key: '{$strKey}')."
            );
        }
        $val = $this->getDataCellValue($model, $key, $index);
        if (!isset($this->_editableOptions['displayValue']) && $val !== null && $val !== '') {
            $this->_editableOptions['displayValue'] = parent::renderDataCellContent($model, $key, $index);
        }
        $params = ZHTML::hiddenInput('editableIndex', $index) . ZHTML::hiddenInput('editableKey', $strKey) .
            ZHTML::hiddenInput('editableAttribute', $this->attribute);
        if (empty($this->_editableOptions['beforeInput'])) {
            $this->_editableOptions['beforeInput'] = $params;
        } else {
            $output = $this->_editableOptions['beforeInput'];
            $this->_editableOptions['beforeInput'] = function ($form, $widget) use ($output, $params) {
                return $params . ($output instanceof Closure ? call_user_func($output, $form, $widget) : $output);
            };
        }
        if ($this->refreshGrid) {
            $id = $this->grid->options['id'];
            $this->_view->registerJs("kvRefreshEC('{$id}','{$this->_css}');");
        }
        $editableClass = ArrayHelper::remove($this->_editableOptions, 'class', Editable::className());
        if (!isset($this->_editableOptions['inlineSettings']['options'])) {
            $this->_editableOptions['inlineSettings']['options']['class'] = 'skip-export';
        } else {
            ZHTML::addCssClass($this->_editableOptions['inlineSettings']['options'], 'skip-export');
        }
        return $editableClass::widget($this->_editableOptions);
    }


    public static function run($column, $attribute)
    {
        return function ($model, $key, $index) use ($column, $attribute) {
            /* @var Models $model */
            Az::$app->forms->wiData->clean();
            Az::$app->forms->wiData->model = $model;
            Az::$app->forms->wiData->attribute = $attribute;
            $data = Az::$app->forms->wiData->data();

            if (!empty($data))
                $column->options['data'] = $data;


            return [

                'header' => $column->title,
                'size' => 'mg',
                'formOptions' => [
                    'action' => [
                        'edit',
                        'modelClassName' => bname($model::className()),
                    ],
                ],


                /*
                 *  PopoverX::ALIGN_RIGHT или 'right'
                    PopoverX::ALIGN_LEFT или 'left'
                    PopoverX::ALIGN_TOP или 'top'
                    PopoverX::ALIGN_BOTTOM или 'bottom'
                    PopoverX::ALIGN_TOP_LEFT или 'top top-left'
                    PopoverX::ALIGN_BOTTOM_LEFT или 'bottom bottom-left'
                    PopoverX::ALIGN_TOP_RIGHT или 'top top-right'
                    PopoverX::ALIGN_BOTTOM_RIGHT или 'bottom bottom-right'
                    PopoverX::ALIGN_LEFT_TOP или 'left left-top'
                    PopoverX::ALIGN_LEFT_BOTTOM или 'left left-bottom'
                    PopoverX::ALIGN_RIGHT_TOP или 'right right-top'
                    PopoverX::ALIGN_RIGHT_BOTTOM или 'right right-bottom'*/

                'placement' => PopoverX::ALIGN_AUTO_HORIZONTAL,
                'inputType' => Editable::INPUT_WIDGET,
                'widgetClass' => $column->widget,
                'options' => $column->options,
                'inputFieldConfig' => [],

                'beforeInput' => function () {
                },
                'showButtons' => false,

                'afterInput' => function () {
                },
                'submitOnEnter' => true,
                'i18n' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@kveditable/messages',
                    'forceTranslation' => true
                ],
                'showButtonLabels' => true,
                'buttonsTemplate' => '{reset}{submit}',
                'submitButton' => [
                    'icon' => '<i class="fas fa-check"></i>',
                    'label' => 'Apply',
                    'class' => 'btn btn-sm btn-success editable-dynagrid'
                ],

                'resetButton' => [
                    'icon' => '<i class="fas fa-ban"></i>',
                    'label' => 'Reset',
                    'class' => 'btn btn-sm btn-danger editable-dynagrid'
                ],
                'pjaxContainerId' => $model->id . '_pjaxContainerId',
                'asPopover' => true,
                'ajaxSettings' => [],
                'showAjaxErrors' => true,
                'encodeOutput' => true,
                'pluginEvents' => [
                    'editableChange' => "function(event, val) { console.log('Changed Value ' + val); }",
                    'editableBeforeSubmit' => "function(event, jqXHR) { console.log('Before submit triggered'); }",
                    'editableSubmit' => "function(event, val, form, jqXHR) { console.log('Submitted Value ' + val); }",
                    'editableReset' => "function(event) { console.log('Reset editable form'); }",
                    "editableSuccess" => "function(event, val, form, data) { console.log('Successful submission of value ' + val); }",
                    'editableError' => "function(event, val, form, data) { console.log('Error while submission of value ' + val); }",
                    'editableAjaxError' => "function(event, jqXHR, status, message) { console.log(jqXHR); }",
                ],
                'valueIfNull' => '<em>' . Az::l('Unknown') . '</em>',

            ];
        };
    }

    use ZCoreTrait;


}
