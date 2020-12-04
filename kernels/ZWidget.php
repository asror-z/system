<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\kernels;


use Elasticsearch\Endpoints\Ml\ValidateDetector;
use yii\bootstrap4\Widget;
use zetsoft\dbitem\data\ALLData;
use zetsoft\service\forms\Active;
use zetsoft\service\forms\ZPjax;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\assets\ZColor;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZHTML;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\module\Forms;
use zetsoft\system\module\Models;
use zetsoft\widgets\former\ZFormWidget;
use zetsoft\widgets\former\ZGrapesJsWidgetBosya;
use zetsoft\widgets\former\ZMultiWidget;
use zetsoft\widgets\inputes\ZFileInputWidget;
use zetsoft\widgets\inputes\ZInputMaskWidget;
use zetsoft\widgets\inputes\ZKSwitchInputWidget;
use zetsoft\widgets\navigat\ZButtonWidget;
use function False\true;


class ZWidget extends Widget implements ZColor
{

    use ZCoreTrait;

    public $name;
    public $names;
    public $dbType = dbTypeString;

    /* @var Active $form*/
    public $form;
    public $theme;
    public $formName = null;

    public $value;
    public $data = [];

    /**
     *
     *  Grapes
     */
    public $opts = true;
    public $grapesWrap = false;

    /**
     *
     * @var
     * Form Build
     */

    public $source;
    public $options;

    /**
     *
     * @var
     * Reset Button
     */

    public $btnReset;
    public $btnResetID;
    public $bsVersion;

    /**
     *
     * Public Vars
     */

    public $all;
    public $htm;
    public $id;
    public $selector;
    public $dataWidget;
    public $idApp;

    public $js;
    public $css;
    public $hidden;

    /**
     * @var array
     * rightBtn
     * Right toolbar
     */
    public $rightBtn = [];
    public $_rightBtn = [];
    /**
     * @var array
     * For set which buttons are showed on right toolbar.
     * Toolbar keys:
     * update,
     * add-clone-delete,
     * filter-sort-id,
     * export,
     * toggleData,
     */
    public $rightName = [];
    /**
     * @var array
     * For set which buttons aren't showed on right toolbar.
     * Toolbar keys:
     * update,
     * add-clone-delete,
     * filter-sort-id,
     * export,
     * toggleData,
     */
    public $rightNameEx = [];


    /**
     * @var array
     * leftBtn
     * Left toolbar
     */
    public $leftBtn = [];
    public $_leftBtn = [];
    /**
     * @var array
     * For set which buttons are showed on left toolbar.
     * Toolbar keys:
     * search, barcode
     */
    public $leftName = [];
    /**
     * @var array
     * For set which buttons aren't showed on left toolbar.
     * Toolbar keys:
     * search, barcode
     */
    public $leftNameEx = [];


    public $replace = [];


    public static $grapes = [

    ];


    public $config = [];
    public $_config = [];
    public $__config = [

        'changeFormAjax' => false,

        'enableEvent' => true,
        'enableEventOn' => true,

        'label' => false,
        'name' => null,
        'depend' => null,
        'class' => '',
        'cooler' => false,
        'begin' => false,
        'panel' => false,
        'isFilter' => false,

        'App' => false,
        'namespace' => null,
        'service' => null,
        'method' => null,
        'args' => null,

        'grapes' => false,
        'grapeIcon' => null,
        'grapeName' => null,
        'grapeTitle' => null,

        'reload' => false,
        'badge' => null,
        'hidden' => false,
        'badgeType' => null,

        'ttText' => null,

        'ttSide' => self::ttSide['top'],
        'ttSize' => self::ttSize['medium'],
        'ttColor' => self::ttColor['default'],
        'ttAnimate' => self::ttAnimate['bounce'],
        'placeholder' => null,

        'readonly' => false,
        'disabled' => false,
        'confirm' => true,
        'export' => [],

        'formClass' => null,
        'formAttr' => null,
        'formModel' => null,


        /**
         *
         * Icons
         */
        'hasIcon' => false,
        'iconCode' => null,
        'tooltip' => null,

        'hasAnimate' => true,
        'hasBadge' => false,
        'hasPlace' => true,

        'ariaHidden' => true,

        'icon' => null,
        'animate' => null,

        'multiple' => false,
        'visible' => true,
        'pjax' => true,
        'pjaxAdd' => false,
        'btnSize' => ZColor::btnSize['default'],

        'models' => null,
        'attribute' => null,
    ];


    public $label = [];
    public $_label = [];
    public $__label = [
        'label' => Azl . 'Текст Лейбла',
        'name' => Azl . 'Добавить name',
        'class' => Azl . 'Добавить class',

        'hidden' => Azl . 'Скрытый',

        'badge' => Azl . 'Значок',
        'badgeType' => Azl . 'Тип badge',

        'ttText' => Azl . 'Текст tooltip',
        'ttSide' => Azl . 'Сторона tooltip',
        'ttColor' => Azl . 'Цвет tooltip',
        'ttAnimate' => Azl . 'Анимация tooltip',
        'ttSize' => Azl . 'Размер tooltip',
        'placeholder' => Azl . 'Текст в placeholder',
        'readonly' => Azl . 'Только для чтения',
        'disabled' => Azl . 'Отключён',
        'confirm' => Azl . 'Окно подтверждения',

        'hasIcon' => Azl . 'Добавить иконку',
        'hasAnimate' => Azl . 'Добавить анимацию',
        'hasBadge' => Azl . 'Добавить значок',
        'hasPlace' => Azl . 'Добавить placeholder',

        'ariaHidden' => Azl . 'ariaHidden',

        'icon' => Azl . 'Текст иконки',
        'animate' => Azl . 'Анимация',

        'multiple' => Azl . 'Множественный',
        'visible' => Azl . 'Видимый',
        'pjax' => Azl . 'Разрешить Pjax',
    ];


    public $branch = [];
    public $_branch = [];
    public $__branch = [
        'general' => [
            'placeholder',
            'label',
            'name',
            'class',
        ],
        'system' => [
            'grapes',
            'cooler',
        ],
        'tooltip' => [
            'ttText',
            'ttSide',
            'ttColor',
            'ttSize',
            'ttAnimate',
        ],

        'dataBase' => [
            'models',
            'attribute',
        ],

        'data' => [
            'App',
            'namespace',
            'service',
            'method',
            'args'
        ]
    ];


    public const categories = [
        'inputes' => [
            'title' => Azl . 'Инпуты',
            'placeholder',
            'label',
            'name',
            'class',
        ],
        'former' => [
            'title' => Azl . 'Формы',
            'grapes',
            'cooler',
        ],
        'columns' => [
            'title' => Azl . 'Колонки',
            'ttText',
            'ttSide',
            'ttColor',
            'ttSize',
            'ttAnimate',
        ],
        'blocks' => [
            'title' => Azl . 'Блоки',
            'models',
            'attribute',
        ]
    ];


    public $branchLabel = [];
    public $_branchLabel = [];
    public $__branchLabel = [
        'general' => Azl . 'Основное',
        'tooltip' => Azl . 'Примечание',
        //'system' => Azl . 'Системные',
        'data' => Azl . 'Данные',
        'widget' => Azl . 'Виджет',
        'dataBase' => Azl . 'Опции для БД',
    ];


    public $active = [];
    public $_active = [];
    public $__active = [

        /**
         * Mouse
         */
        'click' => false,
        'dblclick' => false,
        'mousedown' => false,
        'mouseup' => false,
        'mouseover' => false,
        'mousemove' => false,
        'mouseout' => false,
        'dragstart' => false,
        'drag' => false,
        'dragenter' => false,
        'dragleave' => false,
        'dragover' => false,
        'drop' => false,
        'dragend' => false,


        /**
         *
         * Keyboard
         */
        'keydown' => false,
        'keypress' => false,

        /**
         *
         * Change
         */
        'change paste keyup' => false,
        'change paste keyup select' => false,
        'change paste keyup cut select' => false,
        'input propertychange' => false,

        'keyup' => false,
        'change' => false,
        'select' => false,


        /**
         * Form
         *
         */

        'submit' => false,
        'reset' => false,
        'focus' => false,
        'blur' => false,

        /**
         *
         * User int
         */
        'focusin' => false,
        'focusout' => false
    ];


    /**
     *
     * Events
     *
     */

    public const event = [
        'none' => null,
        'main' => "console.log('{className} | {event}');",
        'multi' => "function(e, row, currentIndex) {
    console.log('{className} | {event}');
}",
        'multiItem' => "function(e, item){       
    console.log('multiItem', item);
}",
    ];

    /**
     * @var array
     * Ready to use system
     */
    public array $eventReady = [];

    public $event = [];
    public $_event = [];
    public $__event = [

        /**
         * Mouse
         */
        'click' => self::event['main'],
        'dblclick' => self::event['main'],
        'mousedown' => self::event['main'],
        'mouseup' => self::event['main'],
        'mouseover' => self::event['main'],
        'mousemove' => self::event['main'],
        'mouseout' => self::event['main'],
        'dragstart' => self::event['main'],
        'drag' => self::event['main'],
        'dragenter' => self::event['main'],
        'dragleave' => self::event['main'],
        'dragover' => self::event['main'],
        'drop' => self::event['main'],
        'dragend' => self::event['main'],

        /**
         *
         * Keyboard
         */

        'keydown' => self::event['main'],
        'keypress' => self::event['main'],
        'keyup' => self::event['main'],


        /**
         *
         * Change
         */

        eventChangePasteKeyup => self::event['main'],
        eventChangePasteKeyupSelect => self::event['main'],
        eventChangePasteKeyupSelectCut => self::event['main'],
        eventInputPropertychange => self::event['main'],
        eventChange => self::event['main'],
        eventSelect => self::event['main'],


        /**
         * Form
         *
         */
        'submit' => self::event['main'],
        'reset' => self::event['main'],
        'focus' => self::event['main'],
        'blur' => self::event['main'],

        /**
         *
         * User interface
         */
        'focusin' => self::event['main'],
        'focusout' => self::event['main'],
    ];


    public $layout = [];
    public $_layout = [];
    public $__layout = [


        'regExp' => <<<JS
    
    window.index = null;
    
    var paragraph = '{name}';
    var regex = /.*\[.*\]\[(\d*)\]\[.*\]/;
    var found = paragraph.match(regex);

    if (found !== null) 
    {
        if (found.length > 0)
        {
            console.log('RegExp: ' + found[1]);
            window.index = found[1];
        }
    
    }       
  
JS,

        'valueApp' => <<<JS
    
    window.value = $('#{valueKey}').val();

  
JS,
        'valueMask' => <<<JS
    
    window.value = $('#{valueKey}').val();

  
JS,
        'valueFileinput' => <<<JS
        
    var el =  $("[name='{name}']");
            
    var filesEl = el.val();
    var key = 'value{attribute}';
    console.log(key);
    window.value = window.localStorage.getItem(key);
         console.log(window.key);
    console.log('Get Value');
    console.log(value);
  
JS,

        'valueSwitchInput' => <<<JS
            // bootstrap-switch-id-user-verified_email
            
            var myclass = 'bootstrap-switch-id-{valueKey}';  
                       
            console.log('Switch Input: ' + myclass);
            
            var ids  = $("."+myclass); 
            console.log('has class switch: ' + ids.hasClass('bootstrap-switch-on'));
            
            if (ids.hasClass('bootstrap-switch-on'))
            {
                window.value = 1;
            }
            else
            {
                window.value = 0;
            }
            
            console.log('changeSave Before: ' + window.value);

JS,


        'changeSave' => <<<JS
               
               {value}
               {regExp}
               
                   
               console.log('changeSave ID: {valueKey}');
               console.log('changeSave Value: ' + window.value);
               var url = '/api/core/save/auto.aspx';
               
               if (xhr !== undefined) {
                    console.log('Abort XHR: ' + xhr);
                    xhr.abort()
               }
               
               var xhr = $.ajax({
                    
                    url: url,  
                    type: 'POST',  
                    data: { 
                        id: '{id}', 
                        value: window.value, 
                        attr: '{attr}', 
                        index: window.index, 
                        tranz: '{tranz}', 
                        parentAttr: '{parentAttr}', 
                        parentClass: '{parentClass}', 
                        parentId: '{parentId}', 
                        modelClassName: '{modelClassName}', 
                    },  
                    
                    success: function (data, status, xhr) {
                        
                        console.log('Change success! Value: ' + value);
                        
                        if ({sendAjax} !== undefined)
                            {sendAjax}();
                    },
                    
                    error: function (jqXhr, textStatus, errorMessage) {
                        console.error(jqXhr);
                        console.error('message: ---' + errorMessage);
                        console.error('text-status: ---' + textStatus);
                    }
                });
            
JS,


        'icon' => null,

        'eventMain' => "$('#{id}')
                {eventItem}
           ;",

        'eventOnItem' => ".on('{key}', {event})",
        'eventFunc' => 'function(event, second){ {event} }',

        'grapes' => <<<HTML
      <!--BEGIN-->
      
      <div class="widgetsWrap" basename="{basename}" parent-id="{id}" {options}>
        {content}
      </div>
      
      <!--END-->
HTML,

        'form' => "'form' => \$form,",

        'comment' => "<!--

echo {widget}::widget([
    {form}
    'model' => new {model}(),
    'selector' => '{id}',
    'attribute' => '{attribute}',
    'config' => {config}  
]); 

-->",


        'wrapGrapes' => <<<HTML
     <div class="zwidget parent-div {id}" data-id="{id}" data-css="" ajaxable="true">{content}</div>
HTML,


    ];

    #endregion

    #region Builder


    private function getComments()
    {

        $service = Az::$app->smart->widget;
        $widget = self::className();

        $model = $this->modelClass ?? $service->modelFix($widget);
        $attribute = $this->attribute;
        if (empty($attribute))
            $attribute = $service->attributeFix($widget, $model);

        $configs = Az::$app->App->eyuf->grape->getOptions($this->_config, $widget);
        $configs = $this->configFix($configs);
        $id = $this->selector;

        $form = '';
        if ($this->form)
            $form = strtr($this->__layout['form'], [
            ]);

        return strtr($this->__layout['comment'], [
            '{widget}' => $widget,
            '{model}' => $model,
            '{form}' => $form,
            '{id}' => $id,
            '{attribute}' => $attribute,
            '{config}' => ZVarDumper::export($configs),
        ]);

    }

    private function configFix($configs, $bool = false)
    {

        $excludes = [
            'toolbar',
            'models',
            'attribute',
            'datas',
        ];

        if ($bool)
            $excludes = ['toolbar'];

        $items = [];
        foreach ($configs as $key => $value) {
            if (ZArrayHelper::isIn($key, $excludes) || is_callable($value))
                continue;

            $items[$key] = $value;
        }

        return $items;
    }


    private function after()
    {

        $excludeWidgets = [
            ZGrapesJsWidgetBosya::class,
        ];

        $b1 = !$this->paramGet('grape');
        $b2 = ZArrayHelper::isIn(self::className(), $excludeWidgets);

        if (!$this->_config['grapes']) {
            return false;
        }

        $service = Az::$app->App->eyuf->grape;

        $class = self::className();
        $configs = $service->getOptions($this->_config, $class);
        $json = json_encode($configs);

        $grapesOptions = '';
        if ($this->opts)
            $grapesOptions = "widget='$class' config='$json'";

        if (empty($this->selector))
            $this->selector = $this->id;

        $this->htm = strtr($this->__layout['grapes'], [
            '{content}' => $this->htm,
            '{basename}' => bname($class),
            '{id}' => $this->selector,
            '{options}' => $grapesOptions
        ]);

        $this->htm .= $this->getComments();

        if ($this->grapesWrap)
            $this->htm = strtr($this->__layout['wrapGrapes'], [
                '{id}' => $this->selector,
                '{content}' => $this->htm,
            ]);

        return null;
    }



    #endregion


    #region Main

    public $pjaxId;

    public function run()
    {

        if (!$this->_config['visible'])
            return null;


        $this->pjaxAddBegin();

        $this->eventChange();

        $this->asset();
        $this->codes();

        //   $this->after();

        $this->eventReady();
        $this->eventActive();
        $this->eventItem();

        $this->register();

        $this->pjaxAddEnd();

        /*  vd($this->_config['changeSave']);
          vd($this->_config['changeReload']);*/
        return $this->htm;
    }


    public function asset()
    {
    }


    public function codes()
    {

    }


    private function register()
    {

        global $boot;


        if (!$boot->isCLI()) {
            $this->getView()->registerCss($this->css);
            $this->getView()->registerJs($this->js);

            return true;
        }

        Az::debug($this->js);
        Az::debug(EOL . EOL);
        Az::debug($this->css);
        return false;
    }

    #endregion

    #region Init


    public function init()
    {
        parent::init();
        $this->trait();
        /**
         *
         * Init
         */


        $this->merge();

        $this->vars();

        $this->ajax();
        $this->icon();
        $this->data();
        $this->depend();

        $this->value();

        $this->valueProc();

        //vd($this->value);

    }


    public function merge()
    {


        global $boot;

        $this->_config['hasIcon'] = $boot->env('hasIcon');
        $this->_config['hasAnimate'] = $boot->env('hasAnimate');
        $this->_config['hasPlace'] = $boot->env('hasPlace');

        $this->_config = ZArrayHelper::merge($this->__config, $this->_config, $this->config);

        $this->_rightBtn = ZArrayHelper::merge($this->_rightBtn, $this->rightBtn);

        $__rightBtn = [];
        if (!empty($this->rightName))
            foreach ($this->_rightBtn as $key => $button) {
                if (ZArrayHelper::isIn($key, $this->rightName))
                    $__rightBtn[$key] = $button;
            }

        if (!empty($this->rightNameEx))
            foreach ($this->_rightBtn as $key => $button) {
                if (!ZArrayHelper::isIn($key, $this->rightNameEx))
                    $__rightBtn[$key] = $button;
            }
        if (!empty($this->rightName) || !empty($this->rightNameEx))
            $this->_rightBtn = $__rightBtn;

        $this->_leftBtn = ZArrayHelper::merge($this->_leftBtn, $this->leftBtn);

        $__leftBtn = [];
        if (!empty($this->leftName))
            foreach ($this->_leftBtn as $key => $button) {
                if (ZArrayHelper::isIn($key, $this->leftName))
                    $__leftBtn[$key] = $button;
            }

        if (!empty($this->leftNameEx))
            foreach ($this->_leftBtn as $key => $button) {

                if (!ZArrayHelper::isIn($key, $this->leftNameEx))
                    $__leftBtn[$key] = $button;
            }
        if (!empty($this->leftName) || !empty($this->leftNameEx))
            $this->_leftBtn = $__leftBtn;

        $this->__layout = ZArrayHelper::merge($this->__layout, $this->__layout, $this->layout);

        $this->_event = ZArrayHelper::merge($this->__event, $this->_event, $this->event);
        $this->_active = ZArrayHelper::merge($this->__active, $this->_active, $this->active);
        $this->_label = ZArrayHelper::merge($this->__label, $this->_label, $this->label);
        $this->_branch = ZArrayHelper::merge($this->__branch, $this->_branch, $this->branch);
        $this->_branchLabel = ZArrayHelper::merge($this->__branchLabel, $this->_branchLabel, $this->branchLabel);

    }


    private function ajax()
    {
        /**
         *
         * modelColumns
         */

        if (!empty($this->modelConfigs)) {
            if ($this->modelConfigs->dynamic)
                $this->_config['ajax'] = false;
        }


    }

    private function vars()
    {

        /**
         *
         * Execute Folder
         */

        if (empty($this->id)) {
            switch (true) {

                case $this->modelCheck():
                    $this->id = ZHTML::getInputId($this->model, $this->attribute);
                    break;

                case $this->model !== null:
                    $this->id = strtolower($this->modelClassName);
                    break;

                default:
                    $this->id = $this->getId();
            }

            if ($this->_config['grapes'])
                $this->id = $this->myId();

        }

        $this->idApp = $this->id . '-app';
        $this->paramSet('model', $this->model);
        $this->bsVersion = $this->paramGet('bsVersion');

        if ($this->_config['hidden'])
            $this->hidden = 'hidden';
        else
            $this->hidden = 'text';

        $this->dataWidget = self::className();
        $this->_config['models'] = $this->modelClass;
        $this->_config['attribute'] = $this->attribute;


        /**
         *
         * Process State
         */

        foreach (ALLData::varString as $state)
            $this->_config[$state] = $this->getState($state);

        foreach (ALLData::varBool as $state)
            $this->_config[$state] = $this->getState($state);


        foreach (ALLData::varInt as $state)
            $this->_config[$state] = $this->getState($state);

        foreach (ALLData::varCalls as $state)
            $this->_config[$state] = $this->getState($state, true);


        if (empty($this->_config['theme']))
            $this->_config['theme'] = '';


        /**
         *
         * Model Classes
         */

        if ($this->modelCheck()) {

            if (empty($this->name))
                $this->name = ZHTML::getInputName($this->model, $this->attribute);

            if (empty($this->_config['placeholder']))
                $this->_config['placeholder'] = ZArrayHelper::getValue($this->model->attributeLabels(), $this->attributeAll);

            if (!$this->_config['hasPlaceholder'])
                $this->_config['placeholder'] = null;
        }
        //vdd($this->js);
    }


    public function icon()
    {

        if ($this->_config['hasAnimate'])
            if (empty($this->_config['animate']))
                $this->_config['animate'] = Az::$app->utility->mains->animation();

        if ($this->_config['hasIcon'])
            if (empty($this->_config['icon']))
                $this->_config['icon'] = Az::$app->utility->mains->icon();

        if ($this->_config['hasIcon']) {
            $this->_config['iconCode'] = strtr($this->__layout['icon'], [
                '{icon}' => $this->_config['icon'],
                '{tooltip}' => $this->_config['tooltip']
            ]);
        }

    }


    private function data()
    {


        /**
         *
         * data Filling
         */

        if (!empty($this->data) || !$this->modelCheck())
            return false;

        Az::$app->forms->wiData->clean();
        Az::$app->forms->wiData->model = $this->model;
        Az::$app->forms->wiData->attribute = $this->attribute;
        Az::$app->forms->wiData->data();

        $this->data = Az::$app->forms->wiData->data;

///vd($this->data);

        return true;

    }

    public function depend()
    {

        $namespace = $this->_config['namespace'];
        $service = $this->_config['service'];
        $method = $this->_config['method'];
        $args = $this->_config['args'];
        $depend = $this->_config['depends'];

        if (empty($depend))
            return null;

        $dependValue = $this->model->$depend;

        $argument = null;
        if (!empty($args)) {
            if (ZStringHelper::find($args, '|'))
                $argument = explode('|', $args);
            else
                $argument = $args;
        } else
            if (!empty($dependValue))
                $argument = $dependValue;

        //   vd($argument);
        if ($this->checkData()) {
            if ($this->_config['App'])
                if (is_array($argument))
                    $this->data = Az::$app->App->$namespace->$service->$method(...$argument);
                else
                    $this->data = Az::$app->App->$namespace->$service->$method($argument);

            else
                if (is_array($argument))
                    $this->data = Az::$app->$namespace->$service->$method(...$argument);
                else
                    $this->data = Az::$app->$namespace->$service->$method($argument);
        }
    }


    public function value()
    {
        $dateTypes = [

        ];

        if (!$this->modelCheck())
            return false;


        $column = $this->modelColumn;

        if (!ZArrayHelper::isIn($this->attributeAll, $this->model->columnsList()))
            return false;

        $value = ZHTML::getAttributeValue($this->model, $this->attributeAll);

        if ($value !== null) {
            $this->value = $value;
            return true;
        }


        $value = $this->model->columns[$this->attributeAll]->value;

        if (is_callable($this->model->columns[$this->attributeAll]->value)) {
            $val = $this->model->columns[$this->attributeAll]->value;
            $value = $val($this->model, $this->model->id);
        }

        if ($value !== null) {
            $this->value = $value;
            $this->setAttr($value);
            return true;
        }

        if (ZArrayHelper::keyExists($this->modelClassName, $this->urlParam)) {
            $urlData = $this->urlParam[$this->modelClassName];
            if (ZArrayHelper::keyExists($this->attributeAll, $urlData)) {
                $value = $urlData[$this->attributeAll];
                $this->value = $value;
                $this->setAttr($value);
                return true;
            }
        }

        return true;
    }

    public function valueProc()
    {

        if ($this->modelColumn !== null)
            switch ($this->modelColumn->dbType) {
                case  dbTypeDate:
                case  dbTypeDateTime:
                    if (!empty($this->value))
                        $this->value = date('d-m-Y', strtotime($this->value));
                    break;

                case  dbTypeBoolean:

                    switch (true) {
                        case is_string($this->value):
                        case is_int($this->value):
                            if ($this->value == '1')
                                $this->value = true;
                            else
                                $this->value = false;

                            break;

                    }


                    break;
                default:

            }

    }


    private function setAttr($value)
    {
        if ($this->model instanceof ZActiveRecord)
            $this->model->setAttribute($this->attributeAll, $value);
        else
            $this->model->setAttributes([$this->attributeAll => $value]);
    }

    #endregion


    #region Change

    public function pjaxAddBegin()
    {

        if ($this->_config['pjaxAdd']) {
            $pjaxId = bname($this->className);
            if (!empty($this->modelClassName))
                $pjaxId .= $this->modelClassName;


            $this->pjaxBegin(function (ZPjax $pjax) use ($pjaxId) {
                $pjax->id = $pjaxId;
                return $pjax;
            });
        }

    }

    public function pjaxAddEnd()
    {
        if ($this->_config['pjaxAdd'])
            $this->pjaxEnd();
    }


    #endregion

    #region Events

    public function eventChange()
    {


        if ($this->_config['changeSubmit'])
            $this->eventReg(eventChange, <<<JS
     $.submitForm();
JS
            );

        if ($this->_config['changeSave']) {

            $id = ZArrayHelper::getValue($this->modelMain, 'id');
            $allApp = $this->modelMain->allApp();
            $parentClass = bname($allApp->parentClass);

            /**
             *
             * ID Attribute
             */
            switch (static::class) {
                case ZKSwitchInputWidget::class:
                    $value = $this->__layout['valueSwitchInput'];
                    break;

                case ZFileInputWidget::class:
                    $value = $this->__layout['valueFileinput'];
                    break;

                case ZFormWidget::class:
                case ZMultiWidget::class:
                    $value = null;
                    break;

                default:
                    $value = $this->__layout['valueApp'];

            }


            if ($value !== null) {
                $value = strtr($value, [
                    '{valueKey}' => $this->id,
                    '{name}' => $this->name,
                    '{attribute}' => $this->attribute,
                ]);

                $regExp = strtr($this->__layout['regExp'], [
                    '{name}' => $this->name,
                ]);

                $changeSave = strtr($this->__layout['changeSave'], [
                    '{parentClass}' => $parentClass,
                    '{id}' => $id,
                    '{valueKey}' => $this->id,
                    '{value}' => $value,
                    '{regExp}' => $regExp,
                    '{attr}' => $this->attribute,
                    '{tranz}' => $this->paramGet(paramTransact),
                    '{parentAttr}' => $allApp->parentAttr,
                    '{parentId}' => $allApp->parentId,
                    '{modelClassName}' => $this->modelClassName,
                ]);


                switch (static::class) {
                    case ZFileInputWidget::class:
                        $eventType = 'filebatchuploadsuccess';
                        break;

                    case ZKSwitchInputWidget::class:
                        $eventType = 'switchChange.bootstrapSwitch';
                        break;

                    default:
                        $eventType = 'change';

                }

                $this->_active[$eventType] = true;

      
                if ($this->_config['changeReload']) {

                    if ($this->_config['changeReloadPjax']) {
                        $this->eventReg($eventType, strtr($changeSave, [
                                '{sendAjax}' => '$.sendPjax'
                            ])
                        );

                    } else
                        $this->eventReg($eventType, strtr($changeSave, [
                                '{sendAjax}' => '$.sendAjax'
                            ])
                        );
                } else {

                    $this->eventReg($eventType, strtr($changeSave, [
                            '{sendAjax}' => '$.Empty'
                        ])
                    );
                }
            }


        }

    }

    public function eventItem()
    {
        if ($this->_config['enableEvent']) {
            $eventItem = $this->eventsOn($this->_event);

            //      vd($eventItem );
            $this->js .= strtr($this->__layout['eventMain'], [
                '{id}' => $this->id,
                '{eventItem}' => $eventItem,
            ]);

        }
    }


    public $formALL;

    public function formApp()
    {

        $this->formALL = Az::$app->forms->former->checkFix($this->_config);
        if (!$this->formALL)
            return $this->model;

        /** @var Forms $model */
        $modelz = Az::$app->forms->modelz;
        $model = $modelz->formObject($this->_config, $this->model);
        if (!$model)
            return null;

        if ($this->model->isModel())
            $this->paramSet('modelId', $this->model->id);

        $model->formName = $this->modelClassName . "[$this->attribute]";

        if (!empty($this->name))
            $model->formName = $this->name;

        if (!empty($this->model))
            $model->setAttributes($this->value);

        $this->_config['topBtn'] = false;
        $this->_config['botBtn'] = false;

        $this->attribute = $this->attributeAll;

        return $model;
    }

    public function formParent($model)
    {
        if (!empty($this->_config['model'])) {

            /** @var Models $parentModel */
            $parentModel = $this->_config['model'];
            $parentAttr = ZArrayHelper::getValue($this->_config, 'attribute');;

            $values = $parentModel->$parentAttr;

            $parentColumn = $parentModel->columns[$parentAttr];

            /** @var Models $model */
            $model->configs->changeSave = $parentColumn->changeSave;
            $model->configs->changeReload = $parentColumn->changeReload;
            $model->configs->changeReloadPjax = $parentColumn->changeReloadPjax;

            $model->setAttributes($values);
            $model->parentAttr = $parentAttr;
            $model->parentClass = $parentModel::className();
            $model->parentClassName = bname($model->parentClass);
            $model->parentModel = $parentModel;
            $model->parentId = $parentModel->id;

            $model->columns();
        }

        return $model;
    }


    public function eventReady()
    {
        foreach ($this->_event as $key => $value)
            $this->_event[$key] = strtr($value, [
                '{className}' => $this->className,
                '{event}' => $key
            ]);

    }

    public function eventsAll(array $list, $isValue = true)
    {
        $return = [];
        foreach ($list as $key => $value) {
            if ($isValue)
                $elem = $value;
            else
                $elem = $key;


            $return[$key] = $this->eventCode($elem);
        }

        return $return;
    }

    public function eventActive()
    {
        foreach ($this->_active as $key => $value) {
            if (!$value)
                unset($this->_event[$key]);
        }
    }

    public function eventsOn(array $list, $isValue = false)
    {
        $eventItem = null;

        foreach ($list as $key => $value) {

            if ($isValue)
                $elem = $value;
            else
                $elem = $key;

            $check = ZArrayHelper::keyCheck($elem, $this->_active) && $this->_active[$elem];

            if (!$check)
                continue;

            $value = $this->eventFunc($value);

            $eventItem .= strtr($this->__layout['eventOnItem'], [
                    '{key}' => $key,
                    '{event}' => $value,
                ]) . PHP_EOL;
        }

        return $eventItem;
    }


    public function eventReg($name, $code)
    {
        $eventOld = ZArrayHelper::getValue($this->_event, $name);
        $this->_event[$name] = $eventOld . $code;
    }


    /**
     *
     * Function  eventCode
     *
     * Method for processing event code from inside of widget
     *
     * '{dblclick}' => $this->eventCode('dblclick'),
     *
     * @param $value
     * @return  bool|int|string|null
     *
     */
    public function eventCode($eventName, $prepare = true)
    {


        $check = ZArrayHelper::keyCheck($eventName, $this->_active) && $this->_active[$eventName];

        if (!$check)
            return $this->eventFunc("''");

        $event = ZArrayHelper::getValue($this->_event, $eventName);

        if ($event === null)
            $event = "''";


        $event = ZVarDumper::jscode($event);

        if (!$prepare)
            return $event;

        return $this->eventFunc($event);

    }

    public function eventFunc($event)
    {

        $b1 = ZStringHelper::find($event, 'function(event');
        $b2 = ZStringHelper::find($event, 'function (event');
        $b3 = ZStringHelper::find($event, 'function(data)');
        $b4 = ZStringHelper::find($event, 'function (data)');
        $b5 = ZStringHelper::find($event, 'function(panel');
        $b6 = ZStringHelper::find($event, 'function (panel');
        $b7 = ZStringHelper::find($event, 'function()');
        $b8 = ZStringHelper::find($event, 'function ()');
        $b9 = ZStringHelper::find($event, 'function(result');
        $b10 = ZStringHelper::find($event, 'function (result');
        $b11 = ZStringHelper::find($event, 'function(response)');
        $b12 = ZStringHelper::find($event, 'function(e,');

        if (!$b1 && !$b2 && !$b3 && !$b4 && !$b5 && !$b6 && !$b7 && !$b8 && !$b9 && !$b10 && !$b11 && !$b12)
            $event = strtr($this->__layout['eventFunc'], [
                '{event}' => $event
            ]);

        return $event;
    }


    #endregion


    #region Services

    public function getState($name, $call = false)
    {

        $value = null;

        if (ZArrayHelper::keyExists($name, $this->_config))
            $value = $this->_config[$name];

        if ($this->modelColumn !== null) {
            if ($this->modelColumn->$name !== null)
                $value = $this->modelColumn->$name;
        }

        if ($call)
            if ($this->callableCheck($value))
                if ($this->callableCan())
                    $value = $this->callableExec($value, $this->model);

        //   vd($name, $value);
        return $value;

    }


    public function checkData()
    {

        $b1 = !empty($this->_config['namespace']);
        $b2 = !empty($this->_config['service']);
        $b3 = !empty($this->_config['method']);

        return $b1 && $b2 && $b3;

    }


    protected function reset()
    {

        /**
         *
         *
         * Reset Button
         */

        $this->btnResetID = "{$this->id}_Reset";
        $this->btnReset = ZButtonWidget::widget([
            'config' => [

                'name' => 'zReset Data',
                'color' => ZButtonWidget::btnStyle['btn-success'],
                'btnSize' => ZButtonWidget::btnSize['btn-sm'],
                'class' => 'zCoreReset',
                'id' => $this->btn,
                'url' => ZUrl::current(),
            ],

        ]);


    }

#endregion

}
