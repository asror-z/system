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


use DebugBar\DataFormatter\VarDumper\DebugBarHtmlDumper;
use PHPUnit\Util\Log\JSON;
use zetsoft\dbitem\data\Form;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZHTML;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZTrait;
use zetsoft\widgets\former\ZEditableIframeWidget;
use zetsoft\widgets\former\ZEditableIframeWidgetAs;
use zetsoft\widgets\inputes\ZKSelect2Widget;
use zetsoft\widgets\inputes\ZSelect2Widget;
use zetsoft\widgets\navigat\ZButtonWidget;

class ZEditableSweetColumnRav extends ZKDataColumn
{

    public $editableOptions = [
        'editableValueOptions' => [
            'class' => 'text-success'
        ]
    ];

    public $defaultSort;
    public $sort;

    public $layout = [

        'editable' => <<<HTML
<div class="rv-editable-link {class}" aria-label="{aria-label}">
   {value} 
</div>
HTML,

        'sort' => <<<HTML
<a class="{class} editable-dyna-{attribute}" href="{href}" data-sort="{sort}">{label}</a>
HTML,

    ];


    public $readonly = false;
    public $widgetClass;
    public $widgetOptions;
    /**
     * @var Form
     */
    public $column;

    public function init()
    {
        parent::init();

        $this->column = $this->model->columns[$this->attribute];

    }

    #region MAIN DATA OF TABLE

    private function isRelated()
    {

        $b1 = !ZStringHelper::endsWith($this->attribute, '_id');
        $b2 = !ZStringHelper::endsWith($this->attribute, '_ids');
        $b3 = empty($this->column->fkTable);

        if ($b1 && $b2 && $b3)
            return false;

        return true;

    }

    public function cleanValue($value)
    {
        $return = $value;
        if ($this->modelColumn->dbType === 'jsonb') {

            if (!is_array($value) && !empty($value)) {
                $return = \yii\helpers\Json::decode($value, true);
            }

        }

        return $return;
    }

    public function renderDataCellContent($model, $keys, $index)
    {

        $key = ZArrayHelper::getValue($model, 'id');
        $column = $this->column;

        $id = $this->model->className . '-' . $this->attribute . '-' . $key;

        Az::$app->forms->wiData->clean();
        Az::$app->forms->wiData->model = $this->model;
        Az::$app->forms->wiData->modelAttrs = $model;
        Az::$app->forms->wiData->attribute = $this->attribute;

        $value = Az::$app->forms->wiData->value();

        $options = [
            'title' => $column->title,
            'url' => ZUrl::to([
                '/core/edit/editable',
                'modelClassName' => $this->model->className,
                'key' => $key,
                'index' => $index,
                'attribute' => $this->attribute
            ]),
            'width' => $column->modalWidth,
            'height' => $column->modalHeight,
        ];

        $readonly = 'enable-edit';
        if ($this->readonly)
            $readonly = 'readonly';

        return strtr($this->layout['editable'], [

            '{value}' => ZHTML::tag('span', $value, [
                'id' => 'editable-' . $id,
                'aria-label' => $column->tooltip,
                'class' => "point-raw dyna-editable $readonly",
                'options' => json_encode($options),
                "data-{$this->attribute}" => ZVarDumper::beauty($value),
            ]),

            '{class}' => "editable-dyna-$this->attribute hint--top",
            '{aria-label}' => $column->tooltip
        ]);

    }

    #endregion

    #region FILTER OF TABLE

    protected function renderFilterCellContent()
    {

        if (!$this->model->configs->customFilter)
            return parent::renderFilterCellContent();
            
        $filterTypes = [
            //dbTypeInteger,
            //dbTypeString,
            //dbTypeBigInteger,
            //dbTypeTime,
            //dbTypeDate,
        ];

        $column = $this->column;

        $data = $this->getData();

        if (ZArrayHelper::isIn($column->dbType, $filterTypes)) {

            $column->filterWidget = ZKSelect2Widget::class;

            $this->filterWidgetOptions = ZArrayHelper::merge([
                'config' => [
                    'multiple' => true,
                    'showToggleAll' => false,
                ],
            ], $this->filterWidgetOptions);

            $table_name = ZInflector::underscore($this->modelClassName);

            if (empty($data)) {
                         
                Az::$app->forms->wiData->clean();
                Az::$app->forms->wiData->fkTable = $table_name;
                Az::$app->forms->wiData->isFilter = true;

                $data = Az::$app->forms->wiData->related();
                $data = $this->processData($data);

            }

        }

        $widgetClass = $column->filterWidget;

        $column->value = null;

        $options = ZArrayHelper::merge($this->filterWidgetOptions, [
            'attribute' => $this->attribute,
            'data' => $data,
            'config' => [
                'class' => 'filter-dyna-widgets'
            ],
            
        ]);

        return $widgetClass::widget($options);

    }

    private function getData()
    {

        Az::$app->forms->wiData->clean();
        Az::$app->forms->wiData->model = $this->model;
        Az::$app->forms->wiData->attribute = $this->attribute;

        return Az::$app->forms->wiData->data();

    }

    private function processData($data)
    {

        $return = [];

        foreach ($data as $key => $item) {
            if (!empty($item) && !empty($key))
                $return[$key] = $item;
        }

        return $return;
    }

    #endregion

    #region HEADER OF TABLE (SORT)

    private function getDefaultSort($sorts)
    {

        $return = '';
        foreach ($sorts as $key => $sort) {

            if ($sort === SORT_DESC)
                $return .= '-' . $key . ',';
            else
                $return .= $key . ',';

        }

        if (ZStringHelper::endsWith($return, ','))
            $return = str_replace(',', '', $return);

        return $return;

    }

    private function getCleanSort($sorts)
    {

        $cleanSort = [];
        foreach ($sorts as $sort) {
            $attribute = str_replace('-', '', $sort);
            $cleanSort[$attribute] = $sort;
        }

        return $cleanSort;
    }

    private function getSort($cleanSort)
    {

        $return = [];
        foreach ($cleanSort as $key => $value) {

            if ($key === $this->attribute) {

                $return[$key] = '-' . $key;
                if (ZStringHelper::startsWith($value, '-'))
                    $return[$key] = $key;

            } else
                $return[$key] = $value;

        }

        if (!ZArrayHelper::keyExists($this->attribute, $cleanSort))
            $return = ZArrayHelper::merge($cleanSort, [
                $this->attribute => $this->attribute
            ]);

        $return = ZArrayHelper::merge([
            $this->attribute => $return[$this->attribute]
        ], $return);

        return implode(',', $return);

    }

    private function getClass($cleanSort)
    {

        $class = '';
        foreach ($cleanSort as $key => $sort) {

            if ($key !== $this->attribute)
                continue;

            if (ZStringHelper::startsWith($sort, '-'))
                $class = 'desc';
            else
                $class = 'asc';

        }

        return $class;

    }

    protected function renderHeaderCellContent()
    {

        if (!$this->model->configs->customSort)
            return parent::renderHeaderCellContent();

        $column = $this->column;

        if (!empty($this->httpGet('sort')))
            $sorts = $this->httpGet('sort');
        else
            $sorts = $this->getDefaultSort($this->defaultSort);

        $sortsArray = [];
        if (!empty($sorts))
            $sortsArray = explode(',', $sorts);

        $cleanSort = $this->getCleanSort($sortsArray);

        $getSort = $this->getSort($cleanSort);
        $class = $this->getClass($cleanSort);

        return strtr($this->layout['sort'], [
            '{class}' => $class,
            '{attribute}' => $this->attribute,
            '{href}' => $this->urlArrayStr . '.aspx?sort=' . $getSort,
            '{label}' => $column->title,                           
            '{sort}' => $getSort,
        ]);
    }

    #endregion

}
