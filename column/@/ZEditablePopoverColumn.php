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
use kartik\widgets\InputWidget;
use rmrevin\yii\fontawesome\FA;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use zetsoft\dbitem\data\Form;
use zetsoft\dbitem\data\FormDb;
use zetsoft\service\forms\Dynas;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZHTML;
use zetsoft\system\helpers\Zicon;
use zetsoft\system\kernels\ZTrait;
use zetsoft\system\module\Models;
use zetsoft\widgets\inputes\ZHInputWidget;
use zetsoft\widgets\inputes\ZKSelect2Widget;
use zetsoft\widgets\navigat\ZButtonWidget;
use zetsoft\widgets\notifier\ZPopoverXWidget;

class ZEditablePopoverColumn extends ZKDataColumn
{

    public $readonly = false;
    public $readonlyWidget = false;
    public $refreshGrid;


    public $editableClass = self::editableClass['editable'];
    public $footer = '{buttons}';


    public const editableClass = [
        'editable' => ZPopoverXWidget::class,
        'zeditable' => ZPopoverXWidget::class,
    ];

    public $editableOptions = [
        'editableValueOptions' => [
            'class' => 'text-success'
        ]
    ];
    protected $_editableOptions = [

    ];

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
            $this->_editableOptions['button'] = parent::renderDataCellContent($model, $key, $index);
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
        $editableClass = ArrayHelper::remove($this->editableOptions, 'class', $this->editableClass);
        if (!isset($this->_editableOptions['inlineSettings']['options'])) {
            $this->_editableOptions['inlineSettings']['options']['class'] = 'skip-export';
        } else {
            ZHTML::addCssClass($this->_editableOptions['inlineSettings']['options'], 'skip-export');
        }

        
        return ZPopoverXWidget::widget([
            'config' => $this->_editableOptions
        ]);
        
    }


    /**
     *
     * Function  run
     * @param Form $column
     * @param $attribute
     * @param string $size
     * @return  Closure
     */
    public static function run($configs, $column, $attribute)
    {
        //  vdd($column->options);

        return function ($model, $key, $index) use ($configs, $column, $attribute) {
            /* @var Models $model */
            Az::$app->forms->wiData->clean();
            Az::$app->forms->wiData->model = $model;
            Az::$app->forms->wiData->attribute = $attribute;
            $data = Az::$app->forms->wiData->data();

            $modelBase = bname($model::className());

            $widgetOptions = ZArrayHelper::merge($column->options, [
                'name' => 'name'
            ]);

            $option = ZArrayHelper::merge($column->options, [
                "attribute" => $attribute,
                "model" => $model,

            ]);



            if (!empty($data))
                $column->options['data'] = $data;
            $content = $column->widget::widget($option);

            return [
                'content' => $content,
                'keyboard' => true,
                'width' =>'800px',
                'maxWidth' => '1100px',
                'titleHead' => 'qwe'
            ];
        };
    }

    use ZCoreTrait;
}
