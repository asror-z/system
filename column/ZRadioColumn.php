<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014-2019
 * @version   3.3.4
 */

namespace zetsoft\system\column;

use Closure;
use kartik\grid\RadioColumn as YiiRadioColumn;
use zetsoft\system\Az;
use kartik\grid\ColumnTrait;
use kartik\grid\GridView;
use kartik\grid\RadioColumnAsset;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use zetsoft\widgets\incores\ZDynaCheckboxWidget;
use zetsoft\widgets\incores\ZMRadioWidget;

/**
 * The CheckboxColumn displays a column of checkboxes in a grid view and extends the [[YiiCheckboxColumn]] with
 * various enhancements.
 *
 * To add a CheckboxColumn to the gridview, add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         'class' => CheckboxColumn::className(),
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class ZRadioColumn extends YiiRadioColumn
{
    use ColumnTrait;

    public $name = 'kvradio';
    public $value = 'kvradio';
    public $showClear = true;
    public $clearOptions = ['class' => 'close'];
    public $rowHighlight = true;
    public $rowSelectedClass;
    public $attribute;
    public $modelClassName;
    public $type;
    public $radioOptions;
    public $cssClass = 'kv-row-radio';
    protected $_clientVars = '';

    public function init()
    {
        $this->initColumnSettings([
            'hiddenFromExport' => true,
            'mergeHeader' => true,
            'hAlign' => GridView::ALIGN_CENTER,
            'vAlign' => GridView::ALIGN_MIDDLE,
            'width' => '50px'
        ]);
        if (empty($this->name)) {
            throw new InvalidConfigException('The "name" property must be set.');
        }

        if (!isset($this->rowSelectedClass)) {
            $this->rowSelectedClass = $this->grid->getCssClass(GridView::BS_TABLE_SUCCESS);
        }
        $css = $this->rowHighlight ? $this->rowSelectedClass : '';
        $this->_view = $this->grid->getView();
        RadioColumnAsset::register($this->_view);
        $grid = $this->grid->options['id'];
        $this->_clientVars = "'{$grid}', '{$this->name}', '{$css}'";
        $this->_clientScript = "kvSelectRadio({$this->_clientVars});";
        $this->_view->registerJs($this->_clientScript);
        $this->parseFormat();
        $this->parseVisibility();
        parent::init();
        $this->setPageRows();
    }


    public function renderHeaderCellContent()
    {
        if ($this->header !== null) {
            return parent::renderHeaderCellContent();
        }
        /*if ($this->type === 'checkbox')
        {
            return ZDynaCheckboxWidget::widget([
                'id' => '',
                'name' => 'selection_all',
                'config' => [
                    'class' => 'select-on-check-all',
                    'color' => ZDynaCheckboxWidget::color['red'],
                    'radio' => true
                ]
            ]);
        }*/
        if ($this->header !== null || !$this->showClear) {
            return parent::renderHeaderCellContent();
        } else {
            $label = ArrayHelper::remove($this->clearOptions, 'label', '&times;');
            Html::addCssClass($this->clearOptions, 'kv-clear-radio');
            if (empty($this->clearOptions['title'])) {
                $this->clearOptions['title'] = Azl . 'Очистить выделение';

            }
            $this->_view->registerJs("kvClearRadio({$this->_clientVars});");
            return Html::button($label, $this->clearOptions);
        }
    }

    public function renderDataCell($model, $key, $index)
    {
        $this->initPjax($this->_clientScript);
        $options = $this->fetchContentOptions($model, $key, $index);
        Html::addCssClass($options, 'kv-row-radio-select');
        return Html::tag('td', $this->renderDataCellContent($model, $key, $index), $options);
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $this->radioOptions = ['class' => 'kv-row-radio radio-' . $this->modelClassName];
        if ($this->radioOptions instanceof Closure) {
            $options = call_user_func($this->radioOptions, $model, $key, $index, $this);
        } else {
            $options = $this->radioOptions;
            if (!isset($options['value'])) {
                $options['value'] = is_array($key) ? Json::encode($key) : $key;
            }
        }

        return ZDynaCheckboxWidget::widget([
            'name' => $this->name,
            'value' => $options['value'],
            'config' => [
                'class' => $options['class'],
                'color' => ZDynaCheckboxWidget::color['green'],
                'radio' => true,
            ]
        ]);
    }


}
