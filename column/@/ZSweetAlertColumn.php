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
use zetsoft\widgets\former\ZFormWidget;
use zetsoft\widgets\inputes\ZHInputWidget;
use zetsoft\widgets\navigat\ZButtonWidget;
use zetsoft\widgets\notifier\ZSweetAlert2ColumnWidget;

class ZSweetAlertColumn extends ZKDataColumn
{

    public $readonly = false;
    public $readonlyWidget = false;
    public $refreshGrid;


    public $editableClass = self::editableClass['editable'];
    public $footer = '{buttons}';
    public $contentOptions = [];


    public const editableClass = [
        'editable' => Editable::class,
        'zeditable' => ZEditable::class,
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
        $model->configs->nameOn = [
            $this->attribute
        ];
        //$id = $this->modelClassName  . '-form-' . $key . rand(1,1000);
        $model->columns();
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


        $val = $this->getDataCellValue($model, $key, $index);
        if (!isset($this->_editableOptions['button']) && $val !== null && $val !== '') {
            $this->_editableOptions['button'] = parent::renderDataCellContent($model, $key, $index);
        }


        if ($this->refreshGrid) {
            $id = $this->grid->options['id'];
            $this->_view->registerJs("kvRefreshEC('{$id}','{$this->_css}');");
        }


        $this->_editableOptions['model'] = $model;
        $modal_config = ['config' => $this->_editableOptions];
        return ZSweetAlert2ColumnWidget::widget($modal_config);
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

        return function ($model, $key, $index) use ($configs, $column, $attribute) {
            
            /* @var Models $model */
            Az::$app->forms->wiData->clean();
            Az::$app->forms->wiData->model = $model;
            Az::$app->forms->wiData->attribute = $attribute;
            $data = Az::$app->forms->wiData->data();

            $modelBase = bname($model::className());


            if (!empty($data))
                $column->options['data'] = $data;
            /*vd($configs);
            vd($column);
            vd($attribute);*/
            $widgetOptions = ZArrayHelper::merge($column->options, [
                'model' => $model,
                'attribute' => $attribute,
            ]);

            //$line =str_replace("\r\n", "", $column->widget::widget($widgetOptions));

            //vdd($line);
            //vdd($column->widget::widget($widgetOptions));
            return [
                'title' => 'Редактировать ' . $column->title,
                'html' => $column->widget::widget($widgetOptions),
                'timer' => 12000,
                'icon' => '',
                'input' => '',
                'footer' => '',
                'width' => 600,
                
            ];
        };
    }

    use ZCoreTrait;
}
