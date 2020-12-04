<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014-2019
 * @version   3.3.4
 */

namespace zetsoft\system\column;

use Closure;
use kartik\grid\CheckboxColumnAsset;
use kartik\grid\ColumnTrait;
use kartik\grid\GridView;
use yii\grid\CheckboxColumn as YiiCheckboxColumn;
use yii\helpers\Html;
use yii\helpers\Json;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\widgets\incores\ZDynaCheckboxWidget;

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
class ZCheckColumn2 extends YiiCheckboxColumn
{
    use ColumnTrait;

    public $rowHighlight = true;
    public $rowSelectedClass;
    public $attribute;
    public $type;
    public $cssClass = 'kv-row-checkbox';

    

    public function init()
    {
        $this->initColumnSettings([
            'hiddenFromExport' => true,
            'mergeHeader' => true,
            'hAlign' => GridView::ALIGN_CENTER,
            'vAlign' => GridView::ALIGN_MIDDLE,
            'width' => '50px'
        ]);
        if (!isset($this->rowSelectedClass)) {
            $this->rowSelectedClass = $this->grid->getCssClass(GridView::BS_TABLE_DANGER);
        }
        $id = $this->grid->options['id'];
        $view = $this->grid->getView();
        CheckboxColumnAsset::register($view);
        if ($this->type === 'checkbox')
        {
            if ($this->rowHighlight) {
                Html::addCssClass($this->headerOptions, 'kv-all-select');
                $this->_clientScript = "kvSelectRow('{$id}', '{$this->rowSelectedClass}');";
                $view->registerJs($this->_clientScript);
            }
        }

        $this->parseFormat();
        $this->parseVisibility();
        parent::init();
        $this->setPageRows();
        $opts = Json::encode(
            [
                'name' => $this->name,
                'multiple' => $this->multiple,
                'checkAll' => $this->grid->showHeader ? $this->getHeaderCheckBoxName() : null,
            ]
        );
        $this->_clientScript .= "\nkvSelectColumn('{$id}', {$opts});";
    }


    public function renderHeaderCellContent()
    {
        if ($this->header !== null || !$this->multiple) {
            return parent::renderHeaderCellContent();
        }
        if ($this->type === 'checkbox')
        {
            return ZDynaCheckboxWidget::widget([
                'value' => 1,
                'name' => $this->getHeaderCheckBoxName(),
                'config' => [
                    'class' => 'select-on-check-all',
                    'color' => ZDynaCheckboxWidget::color['red'],
                    'type' => $this->type,
                ]
            ]);
        }
    }

    protected function getHeaderCheckBoxName()
    {
        $name = $this->name;
        if (substr_compare($name, '[]', -2, 2) === 0) {
            $name = substr($name, 0, -2);
        }
        if (substr_compare($name, ']', -1, 1) === 0) {
            $name = substr($name, 0, -1) . '_all]';
        } else {
            $name .= '_all';
        }

        return $name;
    }


    public function renderDataCellContent($model, $key, $index)
    {
        if ($this->content !== null) {
            return parent::renderDataCellContent($model, $key, $index);
        }

        if ($this->checkboxOptions instanceof Closure) {

            $options = call_user_func($this->checkboxOptions, $model, $key, $index, $this);
        } else {
            $options = $this->checkboxOptions;
        }

        if (!isset($options['value'])) {
            $options['value'] = is_array($key) ? Json::encode($key) : $key;
        }
        return ZDynaCheckboxWidget::widget([
            'name' => $this->name,
            'value' => $options['value'],
            'config' => [
                'class' => $options['class'],
                'color' => ZDynaCheckboxWidget::color['red'],
                'type' => $this->type,
            ]
        ]);

    }


    /**
     * @inheritdoc
     */
    public function renderDataCell($model, $key, $index)
    {
        $options = $this->fetchContentOptions($model, $key, $index);
        if ($this->rowHighlight) {
            Html::addCssClass($options, 'kv-row-select');
        }
        $this->initPjax($this->_clientScript);
        if ($this->attribute !== null) {
            $this->name = Html::getInputName($model, "[{$index}]{$this->attribute}");
            if (!$this->checkboxOptions instanceof Closure) {
                $this->checkboxOptions['value'] = Html::getAttributeValue($model, $this->attribute);
            }
        }
                                     
        return Html::tag('td', $this->renderDataCellContent($model, $key, $index), $options);
    }
}
