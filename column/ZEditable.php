<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\column;


use kartik\base\Config;
use kartik\editable\Editable;
use kartik\popover\PopoverX;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use zetsoft\widgets\notifier\ZPopoverWidget;
use zetsoft\widgets\notifier\ZPopoverXWidget;

class ZEditable extends Editable
{
    /**
     * Editable LINK display format (default)
     */
    const FORMAT_LINK = 'link';
    /**
     * Editable BUTTON display format
     */
    const FORMAT_BUTTON = 'button';
    /**
     * @var string editable prebuilt inline template number 1 for content before
     */
    const INLINE_BEFORE_1 = <<< HTML
<div class="kv-editable-form-inline">
    <div class="form-group">
        {loading}
    </div>
HTML;
    /**
     * Editable prebuilt inline template number 1 for content after
     */
    const INLINE_AFTER_1 = <<< HTML
    <div class="form-group">
        {buttons}{close}
    </div>
</div>
HTML;
    /**
     * Editable prebuilt inline template number 2 for content before
     */
    const INLINE_BEFORE_2 = <<< HTML
<div class="card-header panel-heading">
    {close}
    {header}
</div>
<div class="card-body panel-body">
HTML;
    /**
     * Editable prebuilt inline template number 2 for content after
     */
    const INLINE_AFTER_2 = <<< HTML
</div>
<div class="card-footer panel-footer">
    {loading}{buttons}
</div>
HTML;

    /**
     * Hidden input
     */
    const INPUT_HIDDEN = 'hiddenInput';
    /**
     * Text input
     */
    const INPUT_TEXT = 'textInput';
    /**
     * Text area
     */
    const INPUT_TEXTAREA = 'textArea';
    /**
     * Password input
     */
    const INPUT_PASSWORD = 'passwordInput';
    /**
     * Dropdown list allowing single select
     */
    const INPUT_DROPDOWN_LIST = 'dropDownList';
    /**
     * List box allowing multiple select
     */
    const INPUT_LIST_BOX = 'listBox';
    /**
     * Checkbox input
     */
    const INPUT_CHECKBOX = 'checkbox';
    /**
     * Radio input
     */
    const INPUT_RADIO = 'radio';
    /**
     * Checkbox inputs as a list allowing multiple selection
     */
    const INPUT_CHECKBOX_LIST = 'checkboxList';
    /**
     * Radio inputs as a list
     */
    const INPUT_RADIO_LIST = 'radioList';
    /**
     * Bootstrap styled checkbox button group
     */
    const INPUT_CHECKBOX_BUTTON_GROUP = 'checkboxButtonGroup';
    /**
     * Bootstrap styled radio button group
     */
    const INPUT_RADIO_BUTTON_GROUP = 'radioButtonGroup';
    /**
     * Krajee styled multiselect input that allows formatted checkbox list and radio list
     */
    const INPUT_MULTISELECT = 'multiselect';
    /**
     * File input
     */
    const INPUT_FILE = 'fileInput';
    /**
     * Other HTML5 input (e.g. color, range, email etc.)
     */
    const INPUT_HTML5 = 'input';
    /**
     * Input widget
     */
    const INPUT_WIDGET = 'widget';
    /**
     * Krajee dependent dropdown input widget [[\kartik\depdrop\DepDrop]]
     */
    const INPUT_DEPDROP = '\kartik\depdrop\DepDrop';
    /**
     * Krajee select2 input widget [[\kartik\select2\Select2]]
     */
    const INPUT_SELECT2 = '\kartik\select2\Select2';
    /**
     * Krajee typeahead input widget [[\kartik\typeahead\Typeahead]]
     */
    const INPUT_TYPEAHEAD = '\kartik\typeahead\Typeahead';
    /**
     * Krajee switch input widget [[\kartik\switchinput\SwitchInput]]
     */
    const INPUT_SWITCH = '\kartik\switchinput\SwitchInput';
    /**
     * Krajee touch spin input widget [[\kartik\touchspin\TouchSpin]]
     */
    const INPUT_SPIN = '\kartik\touchspin\TouchSpin';
    /**
     * Krajee star rating input widget [[\kartik\rating\StarRating]]
     */
    const INPUT_RATING = '\kartik\rating\StarRating';
    /**
     * Krajee range input widget [[\kartik\range\RangeInput]]
     */
    const INPUT_RANGE = '\kartik\range\RangeInput';
    /**
     * Krajee color input widget [[\kartik\color\ColorInput]]
     */
    const INPUT_COLOR = '\kartik\color\ColorInput';
    /**
     * Krajee file input widget [[\kartik\file\FileInput]]
     */
    const INPUT_FILEINPUT = '\kartik\file\FileInput';
    /**
     * Krajee date picker input widget [[\kartik\date\DatePicker]]
     */
    const INPUT_DATE = '\kartik\date\DatePicker';
    /**
     * Krajee Time picker input widget [[\kartik\time\TimePicker]]
     */
    const INPUT_TIME = '\kartik\time\TimePicker';
    /**
     * Krajee date time Picker input widget [[\kartik\datetime\DateTimePicker]]
     */
    const INPUT_DATETIME = '\kartik\datetime\DateTimePicker';
    /**
     * Krajee date range picker input widget [[\kartik\daterange\DateRangePicker]]
     */
    const INPUT_DATE_RANGE = '\kartik\daterange\DateRangePicker';
    /**
     * Krajee sortable input widget [[\kartik\sortinput\SortableInput]]
     */
    const INPUT_SORTABLE = '\kartik\sortinput\SortableInput';
    /**
     * Krajee slider input widget [[\kartik\slider\Slider]]
     */
    const INPUT_SLIDER = '\kartik\slider\Slider';
    /**
     * Krajee mask money input widget [[\kartik\money\MaskMoney]]
     */
    const INPUT_MONEY = '\kartik\money\MaskMoney';
    /**
     * Krajee checkbox extended input widget [[\kartik\checkbox\CheckboxX]]
     */
    const INPUT_CHECKBOX_X = '\kartik\checkbox\CheckboxX';
    /**
     * Loading indicator markup for the editable
     */
    const LOAD_INDICATOR = '<div class="kv-editable-loading" style="display:none">&nbsp;</div>';
    /**
     * CSS setting for the editable parent
     */
    const CSS_PARENT = "kv-editable-parent form-group";

    /**
     * @var string the identifier for the PJAX widget container if the editable widget is to be rendered inside a PJAX
     * container. This will ensure the PopoverX plugin is initialized correctly after a PJAX request is completed.
     * If this is not set, no re-initialization will be done for pjax.
     */
    public $pjaxContainerId;

    /**
     * @var string the display format for the editable. Accepts one of the following values.
     * - `'link' or [[Editable::FORMAT_LINK]]: will convert the text to a clickable editable link.
     * - `'button' or [[Editable::FORMAT_BUTTON]]: will display a separate button beside the text.
     * Defaults to [[Editable::FORMAT_LINK]] if you do not set it as [[Editable::FORMAT_BUTTON]].
     */
    public $format = self::FORMAT_LINK;

    /**
     * @var boolean whether to show the editable input as a popover. Defaults to `true`. If set to `false`, it will be
     * rendered inline.
     */
    public $asPopover = true;

    /**
     * @var array the settings for the inline editable when [[asPopover]] is `false`. The following properties are
     * recognized:
     * - `options`: _array_, the HTML attributes for the `div` panel container that will enclose the inline content. By
     * default the options will be set to `['class' => 'card panel panel-default']`.
     * - `closeButton`: _string_, the markup for rendering the close button to close the inline panel. Note the markup must
     * have the CSS class `kv-editable-close` to trigger the closure of the inline panel. The `closeButton`
     * defaults to `<button class="kv-editable-close close">&times;</button>`.
     * - `templateBefore`: _string_, the template for inline content rendered before the input. Defaults to
     * [[INLINE_BEFORE_1]].
     * - `templateAfter`: _string_, he template for inline content rendered after the input. Defaults to
     * [[INLINE_AFTER_1]]. The following tokens in the templates above will be automatically replaced:
     *    - '{header}': the header generated via `preHeader` and `header` properties.
     *    - '{inputs}': the main form input content (combining `beforeInput`, the input/widget generated based on
     *      `inputType`, and `afterInput`)
     *    - '{buttons}': the form action buttons (submit and reset).
     *    - '{loading}': the loading indicator.
     *    - '{close}': the close button to close the inline content as set in `inlineSettings['closeButton']`.
     */
    public $inlineSettings = [];

    /**
     * @var array the HTML attributes for the editable button to be displayed when the format has been set to 'button':
     * - `label`: _string_, the editable button label. This is not HTML encoded. Defaults to [[defaultEditableBtnIcon]].
     */
    public $editableButtonOptions = [];

    /**
     * @var array the HTML attributes for the editable value displayed
     */
    public $editableValueOptions = [];

    /**
     * @var array the HTML attributes for the editable container
     */
    public $containerOptions = [];

    /**
     * @var array the HTML attributes for the input container applicable only when not using with ActiveForm
     */
    public $inputContainerOptions = [];

    /**
     * @var string the popover contextual type. Must be one of the [[PopoverX::TYPE]] constants Defaults to
     * [[PopoverX::TYPE_DEFAULT]] or `default`. This will be applied only if [[asPopover]] is `true`.
     */
    public $type = PopoverX::TYPE_DEFAULT;

    /**
     * @var string the size of the popover window. One of the `PopoverX::SIZE` constants. This will be applied only
     * if [[asPopover]] is `true`.
     */
    public $size;

    /**
     * @var string the popover placement. Must be one of the `PopoverX::ALIGN` constants Defaults to
     * [[PopoverX::ALIGN_RIGHT]] or `right`. This will be applied only if [[asPopover]] is `true`.
     */
    public $placement = PopoverX::ALIGN_RIGHT;

    /**
     * @var string the header content placed before the header text in the popover dialog or inline panel. This
     * defaults to: '{icon} Edit' - where {icon} is the [[defaultPreHeaderIcon]] markup.
     */
    public $preHeader;

    /**
     * @var string the header content in the popover dialog or inline panel. If not set, this will be auto generated
     * based on the attribute label or set to null.
     */
    public $header;

    /**
     * @var string the footer content in the popover dialog or inline panel. The following special tags/variables will
     * be parsed and replaced in the footer:
     * - `{loading}`: _string_, will be replaced with the loading indicator.
     * - `{buttons}`: _string_, will be replaced with the submit and reset button. If this is set to null or an empty
     * string, it will not be displayed.
     */
    public $footer = '{loading}{buttons}';

    /**
     * @var string the value to be displayed. If not set, this will default to the attribute value. If the attribute
     * value is null, then this will display the value as set in [[valueIfNull]].
     */
    public $displayValue;

    /**
     * @var array the configuration to auto-calculate display value, based on the value of the editable input. This
     * should be a single dimensional array whose keys must match the input value, and the array values must be the
     * description to be displayed. For example, to display user friendly bool values, you could configure this as
     * `[0 => 'Inactive', 1 => 'Active']`. If this is set, it will override any value set in `displayValue`.
     */
    public $displayValueConfig = [];

    /**
     * @var string the value to display if the displayValue is null. Defaults to '<em>(not set)</em>'.
     */
    public $valueIfNull;

    /**
     * @var array the HTML attributes for the container enclosing the main content in the popover dialog.
     */
    public $contentOptions = [];

    /**
     * @var array the class for the ActiveForm widget to be used. The class must extend from `\yii\widgets\ActiveForm`.
     * This defaults to `\kartik\form\ActiveForm`.
     */
    public $formClass = '\kartik\form\ActiveForm';

    /**
     * @var array the options for the ActiveForm widget class selected in `formClass`.
     */
    public $formOptions = [];

    /**
     * @var array the input type to render for the editing the input in the editable form. This must be one of the
     * `Editable::TYPE` constants.
     */
    public $inputType = self::INPUT_TEXT;

    /**
     * @var string any custom widget class to use. Will only be used if the `inputType` is set to
     * [[INPUT_WIDGET]]
     */
    public $widgetClass;

    /**
     * @var boolean additional ajax settings to pass to the plugin.
     * @see http://api.jquery.com/jquery
     */
    public $ajaxSettings = [];

    /**
     * @var boolean whether to display any ajax processing errors. Defaults to `true`.
     */
    public $showAjaxErrors = true;

    /**
     * @var boolean whether to auto submit/save the form on pressing ENTER key.
     */
    public $submitOnEnter = true;

    /**
     * @var boolean whether to select all text in the input when editable is opened.
     */
    public $selectAllOnEdit = true;

    /**
     * @var boolean whether to HTML encode the output via javascript after editable update. Defaults to `true`. Note that
     * this is only applied, if you do not return an output value via your AJAX response action. If you return an
     * output value via AJAX it will not be HTML encoded.
     */
    public $encodeOutput = true;

    /**
     * @var boolean whether to close the editable form when it loses focus.
     */
    public $closeOnBlur = false;

    /**
     * @var integer editable submission validation delay (in micro-seconds).
     */
    public $validationDelay = 500;

    /**
     * @var integer editable reset delay (in micro-seconds).
     */
    public $resetDelay = 200;

    /**
     * @var integer|string editable fade animation delay (in micro-seconds). If entered as a string, it can be one of
     * `'slow'` or `'fast'`.
     * @see http://api.jquery.com/fadein/
     */
    public $animationDelay = 300;

    /**
     * @var array the options for the input. If the inputType is one of the HTML inputs, this will capture the HTML
     * attributes. If the `inputType` is set to [[INPUT_WIDGET]] or set to an input widget from the `\kartik\`
     * namespace, then this will capture the widget options.
     */
    public $options = [];

    /**
     * @var array the ActiveField configuration, if you are using with `model`.
     */
    public $inputFieldConfig = [];

    /**
     * @var string|Closure the content to be placed before the rendered input. If not set as a string, this can be
     * passed as a callback function of the following signature:
     *
     * ```
     * function ($form, $widget) {
     *    // echo $form->field($widget->model, 'attrib');
     * }
     * ```
     *
     * where:
     *
     * - `$form`: _ActiveForm_, is the active form instance for the editable form
     * - `$widget`: _Editable_, is the current editable widget instance
     */
    public $beforeInput;

    /**
     * @var string|Closure the content to be placed after the rendered input. If not set as a string, this can be
     * passed as a callback function of the following signature:
     * `
     * function ($form, $widget) {
     *    // echo $form->field($widget->model, 'attrib');
     * }
     * `
     *
     * where:
     *
     * - `$form`: _ActiveForm_, is the active form instance for the editable form
     * - `$widget`: _Editable_, is the current editable widget instance
     */
    public $afterInput;

    /**
     * @var boolean whether you wish to automatically display the form submit and reset buttons. Defaults to `true`.
     */
    public $showButtons = true;

    /**
     * @var boolean whether you want to show the button labels. Defaults to `false`.
     */
    public $showButtonLabels = false;

    /**
     * @var string the template for rendering the buttons
     */
    public $buttonsTemplate = "{reset}{submit}";

    /**
     * @var string the default icon for editable button set as the label in [[editableButtonOptions]]. Defaults to
     *   `<i class="glyphicon glyphicon-pencil"></i> for [[bsVersion]] = '3.x' and
     *   `<i class="fas fa-pencil-alt"></i> for [[bsVersion]] = '4.x'
     */
    public $defaultEditableBtnIcon;

    /**
     * @var string the default icon for editable button used in the [[preHeader]]. Defaults to:
     *   `<i class="glyphicon glyphicon-ok"></i> ` for [[bsVersion]] = '3.x' and
     *   `<i class="fas fa-check"></i> ` for [[bsVersion]] = '4.x'
     */
    public $defaultSubmitBtnIcon;

    /**
     * @var string the default icon for editable button used in the [[preHeader]]. Defaults to:
     *   `<i class="fas fa-ban"></i> ` for [[bsVersion]] = '3.x' and
     *   `<i class="fas fa-ban"></i> ` for [[bsVersion]] = '4.x'
     */
    public $defaultResetBtnIcon;

    /**
     * @var string the default icon for editable button used in the [[preHeader]]. Defaults to:
     *   `<i class="glyphicon glyphicon-edit"></i> ` for [[bsVersion]] = '3.x' and
     *   `<i class="fas fa-edit"></i> Edit` for [[bsVersion]] = '4.x'
     */
    public $defaultPreHeaderIcon;

    /**
     * @var array the HTML attributes for the form submit button. The following special properties are additionally
     * recognized:
     * - `icon`: _string_, the icon for the button. Defaults to [[defaultSubmitBtnIcon]].
     * - `label`: _string_, the label of the button. This is HTML encoded. Defaults to `Apply` and is translated via yii
     *   i18n message files.
     */
    public $submitButton = ['class' => 'btn btn-sm btn-primary'];

    /**
     * @var array the HTML attributes for the form reset button. The following special properties are additionally
     * recognized:
     * - `icon`: _string_, the icon for the button. Defaults to [[defaultResetBtnIcon]]
     * - `label`: _string_, the label of the button. This is HTML encoded. Defaults to `Reset` and is translated via yii
     * i18n message files.
     */
    public $resetButton = [];

    /**
     * @var array additional data to be passed when editable is submitted via ajax request as `$key => $value` pairs.
     * This will generate hidden inputs in the editable form with input name as `$key` and input value as `$value`.
     */
    public $additionalData = [];

    /**
     * @var array the generated configuration for the [[PopoverX]] widget.
     */
    protected $_popoverOptions = [];

    /**
     * @var array the HTML attributes or options for the input/widget
     */
    protected $_inputOptions = [];

    /**
     * @var ActiveForm active form instance
     */
    protected $_form;

    /**
     * @var string the i18n message category
     */
    protected $_msgCat = 'kveditable';

    /**
     * @var array configuration of icons for BS3 and BS4
     */
    protected static $_icons = [
        'defaultEditableBtnIcon' => ['pencil', 'pencil-alt'],
        'defaultSubmitBtnIcon' => ['ok', 'check'],
        'defaultResetBtnIcon' => ['ban-circle', 'ban'],
        'defaultPreHeaderIcon' => ['edit', 'edit'],
    ];


    protected function initEditable()
    {
        if (empty($this->inputType)) {
            throw new InvalidConfigException("The 'type' of editable input must be set.");
        }
        if (!Config::isValidInput($this->inputType)) {
            throw new InvalidConfigException("Invalid input type '{$this->inputType}'.");
        }
        if ($this->inputType === self::INPUT_WIDGET && empty($this->widgetClass)) {
            throw new InvalidConfigException("The 'widgetClass' must be set when the 'inputType' is set to 'widget'.");
        }
        if (Config::isDropdownInput($this->inputType) && !isset($this->data)) {
            throw new InvalidConfigException("You must set the 'data' property for '{$this->inputType}'.");
        }
        if (!empty($this->formClass) && !class_exists($this->formClass)) {
            throw new InvalidConfigException("The form class '{$this->formClass}' does not exist.");
        }
        Config::validateInputWidget($this->inputType);
        $this->initI18N(__DIR__);
        $this->initIcons();
        $this->initOptions();
        $this->_popoverOptions['options']['id'] = $this->options['id'] . '-popover';
        $this->_popoverOptions['toggleButton']['id'] = $this->options['id'] . '-targ';
        if ($this->isBs4()) {
            $this->_popoverOptions['bsVersion'] = $this->bsVersion;
        }
        $this->registerAssets();
        echo Html::beginTag('div', $this->containerOptions);
        if ($this->format == self::FORMAT_BUTTON) {
            echo Html::tag('div', $this->displayValue, $this->editableValueOptions);
        }
        if ($this->asPopover === true) {
            $this->_popoverOptions['config']['button'] = $this->displayValue;
            $this->_popoverOptions['config']['titleHeader'] = $this->_popoverOptions['header'];

            ZPopoverXWidget::begin($this->_popoverOptions);
        } elseif ($this->format !== self::FORMAT_BUTTON) {
            echo $this->renderToggleButton();
        }
        echo Html::beginTag('div', $this->contentOptions);
        /**
         * @var ActiveForm $class
         */
        $class = $this->formClass;
        if (!class_exists($class)) {
            throw new InvalidConfigException("The form class '{$class}' set via 'Editable::formClass' does not exist.");
        }
        $this->_form = $class::begin($this->formOptions);
        if (!$this->_form instanceof ActiveForm) {
            throw new InvalidConfigException("The form class '{$class}' MUST extend from '\yii\widgets\ActiveForm'.");
        }
    }

    protected function initOptions()
    {
        parent::initOptions();
        $this->_popoverOptions['config']['button'] = $this->displayValue;

    }

    protected function runEditable()
    {
        if (!$this->asPopover) {
            echo Html::beginTag('div', $this->inlineSettings['options']);
        }
        echo $this->renderFormFields();
        if (!$this->asPopover) {
            echo "</div>\n"; // inline options
        }
        /**
         * @var ActiveForm $class
         */
        $class = $this->formClass;
        $class::end();
        echo "</div>\n"; // content options
        if ($this->asPopover === true) {
            ZPopoverXWidget::end();
        } elseif ($this->format == self::FORMAT_BUTTON) {
            echo $this->renderToggleButton();
        }
        echo "</div>\n"; // options
    }

    


}
