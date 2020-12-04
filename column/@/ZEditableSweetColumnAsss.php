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
use zetsoft\widgets\inputes\ZKSelect2WidgetRav;
use zetsoft\widgets\inputes\ZSelect2Widget;
use zetsoft\widgets\navigat\ZButtonWidget;

class ZEditableSweetColumnAsss extends ZKDataColumn
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

    public function renderDataCellContent($model, $keys, $index)
    {
        // return $model[$this->attribute];

        $key = ZArrayHelper::getValue($model, 'id');

        $id = $this->model->className . '-' . $this->attribute . '-' . $key;

        //  $value = '';

        Az::$app->forms->wiData->clean();
        Az::$app->forms->wiData->model = $this->model;
        Az::$app->forms->wiData->attributes = $model;
        Az::$app->forms->wiData->attribute = $this->attribute;

        $value = Az::$app->forms->wiData->value();

        $column = $this->model->columns[$this->attribute];

        $options = [
            'title' => $column->title,
            'widgetClass' => $this->widgetClass,
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

        $btn = ZHTML::tag('span', $value, [
            'id' => 'editable-' . $id,
            'aria-label' => $column->tooltip,
            'class' => "point-raw dyna-editable $readonly",
            'widget-options' => \yii\helpers\Json::htmlEncode($this->widgetOptions),
            'options' => \yii\helpers\Json::htmlEncode($options),
            "data-{$this->attribute}" => ZVarDumper::beauty($value),
        ]);

        return strtr($this->layout['editable'], [
            '{value}' => $btn,
            '{class}' => "editable-dyna-$this->attribute hint--top",
            '{aria-label}' => $column->tooltip
        ]);

        /*
                return ZEditableIframeWidgetAs::widget([
                    'model' => $model,
                    'attribute' => $this->attribute,
                    'widgetClass' => $this->widgetClass,
                    'widgetOptions' => $this->widgetOptions,
                    //  'readonly' => $this->readonly,
                    //   'id' => $id,
                    'config' => [
                        'formId' => 'edit-form',
                        'class' => 'editable-dyna-' . $this->attribute,
                        'modalId' => 'ravshan',
                        'width' => '500px',
                        'height' => '300px',
                        'key' => $key,
                        'index' => $index,
                    ]
                ]);*/

    }

    public function getDefaultSort($sorts)
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

    public function getSort($cleanSort)
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

        $column = $this->model->columns[$this->attribute];

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

    protected function renderFilterCellContent()
    {

        $filterTypes = [
            dbTypeInteger,
            dbTypeString,
            dbTypeBigInteger,
            dbTypeTime,
            dbTypeDate,
        ];

        $dates = [
            dbTypeTime,
            dbTypeDate,
        ];

        $column = $this->model->columns[$this->attribute];

        Az::$app->forms->wiData->clean();
        Az::$app->forms->wiData->model = $this->model;
        Az::$app->forms->wiData->attribute = $this->attribute;

        $data = Az::$app->forms->wiData->data();

        if (ZArrayHelper::isIn($column->dbType, $filterTypes)) {

            $column->filterWidget = ZKSelect2WidgetRav::class;

            $this->filterWidgetOptions = ZArrayHelper::merge([
                'config' => [
                    'multiple' => true,
                    'showToggleAll' => false,
                    
                ],
            ], $this->filterWidgetOptions);

            if (ZArrayHelper::isIn($column->dbType, $dates))
                $this->filterWidgetOptions = ZArrayHelper::merge([
                    'config' => [
                        'multiple' => true,
                        'showToggleAll' => false,
                        'maxSelect' => 2,
                    ],
                ], $this->filterWidgetOptions);

            $table_name = ZInflector::underscore($this->modelClassName);

            if (empty($data)) {

                /*Az::$app->forms->wiData->fkTable = $table_name;
                Az::$app->forms->wiData->isFilter = true;*/
                $data = Az::$app->forms->wiData->relatedFilter($table_name, $this->attribute);

                $data = $this->processData($data);
            }

        }

        $widgetClass = $column->filterWidget;

        $options = ZArrayHelper::merge($this->filterWidgetOptions, [
            'attribute' => $this->attribute,
            'data' => $data
        ]);

        return $widgetClass::widget($options);

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

}
