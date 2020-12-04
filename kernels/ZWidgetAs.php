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


use yii\bootstrap4\Widget;
use yii\caching\TagDependency;
use zetsoft\dbitem\data\ALLData;
use zetsoft\dbitem\data\Config;
use zetsoft\models\shop\ShopCategory;
use zetsoft\service\cores\Cache;
use zetsoft\service\forms\Active;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\assets\ZColor;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZHTML;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\widgets\former\ZFormWidget;
use zetsoft\widgets\former\ZGrapesJsWidgetBosya;
use zetsoft\widgets\inputes\ZHInputWidget;
use zetsoft\widgets\inputes\ZKSelect2Widget;
use zetsoft\widgets\navigat\ZButtonWidget;
use function False\true;


class ZWidgetAs extends Widget implements ZColor
{

    use ZCoreTrait;

    public $name;
    public $names;
    public $dbType = dbTypeString;
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
    public $grapesWrap = true;

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
        'pjax' => 1,
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
        'click' => true,
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

        'change paste keyup' => self::event['main'],
        'change paste keyup select' => self::event['main'],
        'change paste keyup cut select' => self::event['main'],
        'input propertychange' => self::event['main'],
        'change' => self::event['main'],
        'select' => self::event['main'],


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

        'icon' => null,

        'eventMain' => "$('#{id}')
                {eventItem}
           ;",

        'eventItem' => ".on('{key}', {event})",
        'eventFunc' => 'function(event){ {event} }',

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

    public function run()
    {


        /**
         *
         * Core
         */

        if (!$this->_config['visible'])
            return null;


        $this->asset();
        $this->codes();

        /*    if ($this->cacheGet($key))
                $this->htm = $this->cacheGet($key);
            else
                $this->codes();

            $this->cacheSet($key, $this->htm, Cache::type['cache']);*/

        //   $this->after();

        $this->register();

//        $this->cacheSet($this::className() . serialize($this->_config), $this->htm, Cache::type['redis']);

        return $this->htm;
    }


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
        $this->icon();
        $this->eventReady();
        $this->event();
        $this->data();
        $this->value();
    }

    #endregion

    #region Widgets


    public function asset()
    {
    }


    public function codes()
    {

    }


    private function merge()
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

        $this->_layout = ZArrayHelper::merge($this->__layout, $this->_layout, $this->layout);

        $this->_event = ZArrayHelper::merge($this->__event, $this->_event, $this->event);
        $this->_active = ZArrayHelper::merge($this->__active, $this->_active, $this->active);
        $this->_label = ZArrayHelper::merge($this->__label, $this->_label, $this->label);
        $this->_branch = ZArrayHelper::merge($this->__branch, $this->_branch, $this->branch);
        $this->_branchLabel = ZArrayHelper::merge($this->__branchLabel, $this->_branchLabel, $this->branchLabel);


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

    #region Change


    #endregion

    #region Events

    public function event()
    {


        if ($this->_config['changeSubmit']) {

            $changeSubmitId = $this->_config['changeSubmitId'];

            $eventOld = ZArrayHelper::getValue($this->_event, 'change paste keyup cut select');

            $this->_event['change paste keyup cut select'] = $eventOld . "
                 console.log('changeSubmit: ' + event.target.id);
                 $($changeSubmitId).submit();
                ";
        }


        if ($this->_config['changeSave']) {
            $eventOld = ZArrayHelper::getValue($this->_event, 'change paste keyup cut select');
            // $eventOld = ZArrayHelper::getValue($this->_event, 'change');

            /**
             *
             * Get Attributes
             *
             */
            $id = ZArrayHelper::getValue($this->model, 'id');

            $allApp = $this->modelMain->allApp();
            //   vd($allApp );
            if (!empty($allApp->parentAttr))
                $attribute = "$allApp->parentAttr-$this->attribute";
            else
                $attribute = $this->attribute;

            /**
             *
             * Parent Class
             */

            $parentClass = bname($allApp->parentClass);

            if (!empty($allApp->parentClass))
                $modelClass = $parentClass;
            else
                $modelClass = $this->modelClassName;


            /**
             *
             * ID Attribute
             */
            $valueKey = strtolower("$modelClass-$attribute");
            //    vd($valueKey);

            $changeSave = <<<JS
            
               //start|DavlatovRavshan|2020.10.12
               function isFunctionDefined(functionName) {
                   if (eval("typeof(" + functionName + ") == typeof(Function)")) {
                       return true;
                   }
               }
                
               var value = $('#$valueKey').val();  
               
               console.log('changeSave ID: $valueKey');
               console.log('changeSave Value: ' + value);
            //   var url = '/api/core/save/auto.aspx';
               var url = '/api/core/save/auto.aspx';
               
               if (xhr !== undefined) {
                    console.log('Abort XHR: ' + xhr);
                    xhr.abort()
               }
               
               var xhr = $.ajax({
                    
                    url: url,  
                    type: 'POST',  
                    data: { 
                        id: '$id', 
                        value: value, 
                        attr: '$this->attribute', 
                        parentAttr: '{$allApp->parentAttr}', 
                        parentClass: '{$parentClass}', 
                        parentId: '{$allApp->parentId}', 
                        modelClassName: '$this->modelClassName', 
                    },  
                    
                    success: function (data, status, xhr) {
                        
                        console.log('Change success! Value: ' + value);
                        console.log('Data: ',  data);
                        
                        {sendAjax}
                    },
                    
                    error: function (jqXhr, textStatus, errorMessage) {
                        console.error(jqXhr);
                        console.error('message: ---' + errorMessage);
                        console.error('text-status: ---' + textStatus);
                    }
                });
            
JS;

//start|DavlatovRavshan|2020.10.11

            $sendAjax = '';
            if (!empty($this->form)) {

//vd($this->_config['changeReloadPjax']);

                switch ($this->modelConfigs->changeReloadPjax) {
                    case ALLData::changeReloadType['ajax']:
                        $sendAjax = <<<JS
         if (event.type === 'change' && isFunctionDefined('sendAjax')) {
         
            console.log('sendAjax | ' + event.target.id);
            sendAjax()
         }
JS;

                        break;
                    case ALLData::changeReloadType['pjax']:
                        $sendAjax = <<<JS
         if (event.type === 'change' && isFunctionDefined('sendPjax')) {
            console.log('sendPjax | ' + event.target.id);
            sendPjax()
         }
JS;

                        break;


                }


            }


            $this->_event['change paste keyup cut select'] = $eventOld . strtr($changeSave, [
                    //                  $this->_event['change'] = $eventOld . strtr($changeSave, [
                    '{sendAjax}' => $sendAjax
                ]);

            $this->_event['switchChange.bootstrapSwitch'] = $eventOld . strtr($changeSave, [
                    //                  $this->_event['change'] = $eventOld . strtr($changeSave, [
                    '{sendAjax}' => $sendAjax
                ]);
        }

        if ($this->_config['changeReload']) {

            $changeReloadId = $this->modelConfigs->changeReloadId;

            vd($this->model->className(), $changeReloadId);

            
            $eventOld = ZArrayHelper::getValue($this->_event, 'change');


            if (!empty($changeReloadId))
            {

             //   vd($changeReloadId);
                
                $this->_event['change'] = $eventOld . "

                    console.log('changeReload from: ' + event.target.id);

                    $.pjax.reload({
                         container: '#{$changeReloadId}',
                         async: true,
                         timeout: false
                    });
                 ";
            }

        }

        if ($this->_config['enableEvent']) {

            $eventItem = $this->eventsOn($this->_event);


            // vd( $eventItem);
            $this->js .= strtr($this->_layout['eventMain'], [
                '{id}' => $this->id,
                '{eventItem}' => $eventItem,
            ]);

            /*
                        if ($this::className() === ZFormWidget::class)
                            vd($this->js);*/
        }

    }


    public function eventReady()
    {
        foreach ($this->_active as $key => $value) {
            if (!$value)
                unset($this->_event[$key]);
        }

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
                $check = ZArrayHelper::keyCheck($value, $this->_active) && $this->_active[$value];
            else
                $check = ZArrayHelper::keyCheck($key, $this->_active) && $this->_active[$key];

            if ($check)
                $return[$key] = $this->eventCode($value);
        }

        return $return;
    }

    public function eventsOn(array $list)
    {
        $eventItem = null;
        //  vd($list );
        foreach ($list as $key => $event) {

            $event = $this->eventFunc($event);

            $eventItem .= strtr($this->_layout['eventItem'], [
                    '{key}' => $key,
                    '{event}' => $event,
                ]) . PHP_EOL;
        }

        return $eventItem;
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
    public function eventCode($event, $prepare = true)
    {

        $event = ZArrayHelper::getValue($this->_event, $event);

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


    #region Privates

    public function icon()
    {

        if ($this->_config['hasAnimate'])
            if (empty($this->_config['animate']))
                $this->_config['animate'] = Az::$app->utility->mains->animation();

        if ($this->_config['hasIcon'])
            if (empty($this->_config['icon']))
                $this->_config['icon'] = Az::$app->utility->mains->icon();

        if ($this->_config['hasIcon']) {
            $this->_config['iconCode'] = strtr($this->_layout['icon'], [
                '{icon}' => $this->_config['icon'],
                '{tooltip}' => $this->_config['tooltip']
            ]);
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


        /**
         *
         * modelColumns
         */

        if (!empty($this->modelConfigs)) {
            if ($this->modelConfigs->dynamic)
                $this->_config['ajax'] = false;
        }

        if (empty($this->_config['theme']))
            $this->_config['theme'] = '';


        /**
         *
         * Model Classes
         */

        if ($this->modelCheck()) {

            if (empty($this->name)) {

                // if (!is_object($this->model))
                //    vdd($this);
                $this->name = ZHTML::getInputName($this->model, $this->attribute);
            }

            if (empty($this->_config['placeholder']))
                $this->_config['placeholder'] = ZArrayHelper::getValue($this->model->attributeLabels(), $this->attributeAll);

            if (!$this->_config['hasPlaceholder'])
                $this->_config['placeholder'] = null;
        }
        //vdd($this->js);
    }

    private function isWidget($class)
    {
        if ($this::className() === $class)
            return true;

        return false;

        //  vd($this->_config);
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

        $namespace = $this->_config['namespace'];
        $service = $this->_config['service'];
        $method = $this->_config['method'];
        $args = $this->_config['args'];

        $arguments = [];
        if (!empty($args)) {
            $arguments = explode('|', $args);
        }

        if ($this->checkData()) {
            if ($this->_config['App'])
                $this->data = Az::$app->App->$namespace->$service->$method(...$arguments);
            else
                $this->data = Az::$app->$namespace->$service->$method(...$arguments);
        }


        return true;

    }


    public function value()
    {
        $dateTypes = [
            dbTypeDate,
            dbTypeDateTime
        ];

        if (!$this->modelCheck())
            return false;

        $column = $this->model->columns[$this->attribute];

        if (!ZArrayHelper::isIn($this->attributeAll, $this->model->columnsList()))
            return false;

        $value = ZHTML::getAttributeValue($this->model, $this->attributeAll);

        $b1 = ZArrayHelper::isIn($column->dbType, $dateTypes);
        $b2 = !empty($value);

        if ($b1 && $b2) {
            $value = date('d-m-Y', strtotime($value));
        }

        if ($value !== null) {
            $this->value = $value;
            return true;
        }

        if ($this->value !== null) {
            $this->setAttr($this->value);
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


    private function setAttr($value)
    {
        if ($this->model instanceof ZActiveRecord)
            $this->model->setAttribute($this->attributeAll, $value);
        else
            $this->model->setAttributes([$this->attributeAll => $value]);

    }

    #endregion

    #region Services

    public function getState($name)
    {

        $value = null;
        if (ZArrayHelper::keyCheck($name, $this->_config))
            $value = $this->_config[$name];

        if ($this->modelColumn !== null) {
            if ($this->modelColumn->$name !== null)
                $value = $this->modelColumn->$name;
        }

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
