<?php

namespace zetsoft\system\kernels;

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */


/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\kernels;


use ArrayObject;
use Closure;
use DebugBar\DataFormatter\VarDumper\DebugBarHtmlDumper;
use kartik\form\ActiveForm;
use Opis\Closure\ReflectionClosure;
use RdKafka\Conf;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\Sort;
use yii\validators\Validator;
use zetsoft\dbdata\auth\RoleData;
use zetsoft\dbitem\data\ALLApp;
use zetsoft\dbitem\data\ALLData;
use zetsoft\dbitem\data\ALLTrait;
use zetsoft\dbitem\data\Config;
use zetsoft\dbitem\data\ConfigDB;
use zetsoft\dbitem\data\Event;
use zetsoft\dbitem\data\Form;
use zetsoft\dbitem\data\FormDb;
use zetsoft\former\lang\LanguageForm;
use zetsoft\former\stat\HistoryForm;
use zetsoft\former\stat\StatHistoryForm;
use zetsoft\models\dyna\DynaConfig;
use zetsoft\models\dyna\DynaMulti;
use zetsoft\models\shop\ShopOrderCopy;
use zetsoft\service\search\ActiveData;
use zetsoft\system\actives\ZActiveData;
use zetsoft\system\actives\ZActiveQuery;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\actives\ZArrayData;
use zetsoft\system\actives\ZArrayQuery;
use zetsoft\system\actives\ZDynamicModel;
use zetsoft\system\actives\ZModel;
use zetsoft\system\actives\ZValidator;
use zetsoft\system\Az;
use zetsoft\system\column\ZPagination;
use zetsoft\system\column\ZSort;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\module\Models;
use zetsoft\system\schema\PgSqlSchema;
use zetsoft\system\validate\ZRequiredValidator;
use zetsoft\widgets\former\ZFormWidget;
use zetsoft\widgets\former\ZMultiWidget;
use zetsoft\widgets\incores\ZMCheckboxWidget;
use zetsoft\widgets\inputes\ZFileInputWidget;
use zetsoft\widgets\inputes\ZHCheckboxButtonGroupWidget;
use zetsoft\widgets\inputes\ZHInputWidget;
use zetsoft\widgets\inputes\ZHRadioButtonGroupWidget;
use zetsoft\widgets\inputes\ZKDatepickerWidget;
use zetsoft\widgets\inputes\ZKDateTimePickerWidget;
use zetsoft\widgets\inputes\ZKSelect2Widget;
use zetsoft\widgets\inputes\ZKSwitchInputWidget;
use zetsoft\widgets\inputes\ZKTouchSpinWidget;
use zetsoft\widgets\values\ZDateFormatWidget;
use zetsoft\widgets\values\ZFormViewWidget;
use zetsoft\widgets\values\ZMultiViewWidget;
use function False\true;
use function foo\func;
use function Spatie\array_keys_exist;


trait ZActiveTrait
{

    #region Vars

    /* @var ConfigDB $configs */
    public $configs;

    /* @var ConfigDB $configsRead */
    public $configsRead;

    /* @var FormDb[] $columns */
    public $columns = [];

    /* @var array $blocks */
    public $cards;

    /* @var Event $events */
    public $events;

    public $formName;

    #endregion

    #region Static
    /**
     *
     * Function  getDb
     * @return  object|null
     * @throws \yii\base\InvalidConfigException
     */
    public static function getDb()
    {
        $dbName = static::$dbase;
        return Az::$app->get($dbName);
    }

    /**
     *
     * Function  tableName
     * @return  string current table name
     */
    public static function tableName()
    {
        $className = static::className();
        $className = bname($className);
        $tableName = ZInflector::underscore($className);

        return $tableName;
    }


    public function formName()
    {
        if (empty($this->formName))
            $return = bname(static::className());
        else
            $return = $this->formName;

        return $return;
    }


    public function attributeLabels()
    {

        $columns = $this->columns;
        $returns = [];

        /** @var Form $column */
        foreach ($columns as $key => $column) {
            $returns[$key] = $column->title;
        }

        return $returns;
    }

    #endregion

    #region Empty


    /**
     *
     * Function  config
     * @return \Closure null
     */
    public function config()
    {
        return null;
    }


    /**
     *
     * Function  events
     */
    public function events()
    {
        $this->events = $this->event();
    }


    /**
     *
     * Function column
     * @return \Closure[]
     */
    public function column()
    {
        return [];
    }


    /**
     * Function column
     * @return Event
     */
    public function event()
    {
        return null;
    }


    /**
     *
     * Function column
     * @return array
     */
    public function card()
    {
        return [];
    }


    #endregion


    #region Fillment


    /**
     *
     * Function  configs
     * @return  mixed
     */
    public function configs()
    {
        if ($this->isModel())
            $class = ConfigDB::class;
        else
            $class = Config::class;

        $configFunc = $this->config();

        if ($configFunc === null)
            return new $class();

        /** @var string $configFunc */
        $configs = $configFunc(new $class());

        $this->configs = $configs;
        $this->configsRead = clone $configs;

        $this->configsNull();
        $this->configsProc();

        // vd($configs->rules);

        return null;

    }


    public function configsNull()
    {

        if (!$this->paramGet(paramNorms) && !$this->paramGet(paramModel)) {
            $defaults = Az::$app->cores->defaults;

            $all = $defaults->all();

            foreach ($all as $item) {
                if ($this->configs->$item === $defaults->$item)
                    $this->configs->$item = null;

            }

        }
    }

    public function configsProc()
    {
        global $boot;

        if ($boot->env('enableAjaxValidation')) {
            $this->configs->changeSave = false;
            $this->configs->changeReload = false;
        }

    }

    /**
     *
     * Function  configs, generate of object columns
     * @return FormDb[]
     */
    public function columns()
    {
        /*     $trace = trace();
             $class = self::className();

             vd("$class - $trace");*/


        if ($this->isModel())
            $class = FormDb::class;
        else
            $class = Form::class;

        $columnsOne = [];

        foreach (extraColumn as $key => $attr) {
            if ($this->isAddColumn($attr))
                $columnsOne[$key] = $this->genColumn($key, $key);
        }

        /**
         *
         * Processing
         */
        $columns = $this->column();

        if (!empty($columns))
            foreach ($columns as $key => $function) {
                $column = $function(new $class());
                $columnsOne[$key] = $column;
            }

        if ($this->isAddColumn('Del'))
            foreach (softColumn as $key => $title) {
                $columnsOne[$key] = $this->genColumn($key, $title);
            }

        if ($this->isAddColumn('By'))
            foreach (excludedColumn as $key => $title) {
                $columnsOne[$key] = $this->genColumn($key, $title);
            }


        /**
         *
         *
         * Configs
         */

        $denyDB = $this->configs->denyDB;

        /**
         *
         * Names & NamesEx
         */
        $columnsMid = [];

        foreach ($columnsOne as $key => $columnMid) {

            $columnsMid[$key] = $columnMid;


            if ($columnMid->slug)
                $columnsMid[$key . '_slug'] = $this->genColumn('slug', Az::l('Slug {title}', [
                    'title' => $columnMid->title
                ]));

            if ($columnMid->lang)
                $columnsMid[$key . '_lang'] = $this->genColumn('lang', Az::l('Переводы {title}', [
                    'title' => $columnMid->title
                ]));

            if ($columnMid->history)
                $columnsMid[$key . '_history'] = $this->genColumn('history', Az::l('История {title}', [
                    'title' => $columnMid->title
                ]));

        }

        $columnsTwo = [];

        foreach ($columnsMid as $key => $column) {

            if (!$this->paramGet(paramFull))
                if (!$this->checkColumn($key))
                    continue;


            if (!empty($denyDB)) {
                $value = ZArrayHelper::getValue($denyDB, $key);
                if ($value !== null)
                    if (!$value)
                        continue;
            }


            /** @var FormDb $column */
            $column = $this->configALL($key, $column);
            $column = $this->columnProc($key, $column);
            $column->name = $key;
            $columnsTwo[$key] = $column;
        }

        /*
            if ($this::className() === ShopOrderCopy::class)
                vd($this->columns);
        */

        $columnsThree = [];
        if (!empty($this->configs->nameOn) && !$this->paramGet(paramFull)) {
            foreach ($this->configs->nameOn as $name)
                if (ZArrayHelper::keyExists($name, $columnsTwo))
                    $columnsThree[$name] = $columnsTwo[$name];
        } else
            $columnsThree = $columnsTwo;

        $this->columns = $columnsThree;
        return null;

    }


    /**
     *
     * Function  columnProc
     * @param $key
     * @param FormDb $column
     * @return  mixed
     */
    public function columnProc($key, $column)
    {


        if (empty($column->widget))
            $column->widget = ZHInputWidget::class;

        if (!$column->filter)
            $column->mergeHeader = true;

        if (empty($column->tooltip))
            $column->tooltip = $column->title;

        if (!empty($column->data))
            $column->ajax = false;

        if (!empty($column->depends))
            $column->ajax = false;

        switch ($column->widget) {

            case ZFileInputWidget::class:
                $column->width = '500px';
                $column->filter = false;
                break;

            case ZHRadioButtonGroupWidget::class:
            case ZHCheckboxButtonGroupWidget::class:
                $column->filter = false;
                break;


        }

        if (!empty($column->dynamic))
            $column->ajax = false;

        if ($this->tableRelated($key, $column)) {
            $column->dbType = dbTypeInteger;
            $column->index = true;
        }

        if ($this->tableRelatedJson($key, $column)) {
            $column->dbType = dbTypeJsonb;
        }

        return $column;
    }


    public function columnsList($excludes = null)
    {
        $returns = [];

        foreach ($this->columns as $key => $formDB) {
            if ($excludes === null) {
                $returns[] = $key;
            } else
                if (!ZArrayHelper::isIn($formDB->dbType, $excludes)) {
                    $returns[] = $key;
                }
        }

        return $returns;
    }


    /**
     *
     * Function  kernel
     * @return  bool|void
     */
    public function kernel()
    {

        $className = $this::className();

        $b1 = $className === ZModel::class;
        $b2 = $className === ZActiveRecord::class;
        $b3 = $className === ZDynamicModel::class;

        if ($b1 || $b2 || $b3) {
            Az::debug($className, 'ZModel or ZActiveRecord');
            return false;
        }

        $this->configs();

        if ($this->configs === null)
            return Az::warning($className, 'Config is Empty');

        $this->events();
        $this->cards();

        $this->columns();

        return true;
    }


    /**
     *
     * Function  configs
     */
    public function cards()
    {
        $this->cards = $this->card();
    }


    #endregion


    #region AllApps

    public function allApp()
    {
        $app = new ALLApp();

        $app->parentAttr = $this->parentAttr;
        $app->parentClass = $this->parentClass;
        $app->parentClassName = bname($this->parentClass);
        $app->parentId = $this->parentId;

        $app->configs = $this->configs;
        $app->columns = $this->columns;
        $app->cards = $this->cards;

        return $app;
    }


    /**
     *
     * Function  setApp
     * @param ALLApp $allApp
     */
    public function setApp($allApp)
    {

        if ($allApp !== null) {

            $this->parentAttr = $allApp->parentAttr;
            $this->parentClass = $allApp->parentClass;
            $this->parentClassName = $allApp->parentClassName;
            $this->parentId = $allApp->parentId;

            $this->configs = $allApp->configs;
            $this->columns = $allApp->columns;
            $this->cards = $allApp->cards;
        }
    }



#endregion


    #region Blockers
    /**
     *
     * Function  itemsByBlock, generate ZFormWidget columns array by blocks
     * @param integer $ident , searching block number,
     * @param bool $cnt
     * @return  array|mixed|null
     */
    public function itemsByBlock($ident, bool $cnt = false)
    {

        $allBlocks = $this->cards;
        $returns = null;
        $arrayBlock = [];
        //Get blocks As Array
        foreach ($allBlocks as $card) {
            foreach ($card['items'] as $item) {
                $arrayBlock[] = $item;
            }
        }

        foreach ($arrayBlock as $stepName => $block) {
            if ($stepName === $ident)
                $returns = $block;
        }

        if (!$cnt)
            return $returns;

        return $this->makeCard($returns);

    }

    /**
     *
     * Function  makeCard, generate ZFormWidget columns by count without model card
     * @param array $blocks attributes
     * @param integer $count count of columns
     * @return  array|null array of step
     */
    protected function makeCard($blocks, $count)
    {

        if (empty($blocks))
            return null;

        $returnsOne = [];

        foreach ($blocks['items'] as $key => $value) {
            $returnsOne = ZArrayHelper::merge($returnsOne, $value);
        }

        $returns = [];
        $i = 0;
        $row = [];
        foreach ($returnsOne as $item) {
            if ($i === $count) {
                $returns[] = $row;
                $row = [];
                $row[] = $item;
                $i = 0;
            } else {
                $row[] = $item;
            }
            $i++;
        }
        if (!empty($row))
            $returns[] = $row;

        return $returns;

    }

    /**
     *
     * Function  itemsByStep, generate ZFormWidget columns by steps
     * @param integer $ident searching step number, if not found returns all steps
     * @param bool $cnt
     * @return  array|null
     */
    public function itemsByStep($ident, bool $cnt = false)
    {

        $allBlocks = $this->cards;
        $returns = [];

        foreach ($allBlocks as $stepName => $blocks) {
            if ($stepName === $ident)
                foreach ($blocks['items'] as $block)
                    $returns = ZArrayHelper::merge($returns, $block['items']);
        }

        if (!$cnt)
            return $returns;

        return $this->makeCard($returns);
    }

    /**
     *
     * Function  itemsByCard
     * generate form by cards
     * @param $ident
     * @param bool $cnt
     * @return  array|null
     */

    //start|JakhongirKudratov|2020-10-20

    public function itemsByCard($ident, bool $cnt = false)
    {

        $allBlocks = $this->cards;
        $returns = [];
        foreach ($allBlocks as $stepName => $blocks) {
            if ($stepName === $ident)
                foreach ($blocks['items'] as $block) {
                    foreach ($block['items'] as $itemBlock) {
                        foreach ($itemBlock as $item)
                            $returns[] = [$item];

                    }
                }
        }
        return $returns;
    }

    //end|JakhongirKudratov|2020-10-20

    /**
     *
     * Function  blockByAll
     * @param bool $isOne
     * @param int $count
     * @return  array|null
     *
     *
     */
    public function blockByAll(bool $isOne = false, $count = 1)
    {
        $allBlocks = $this->cards;
        $returns = [];

        foreach ($allBlocks as $stepName => $blocks) {
            foreach ($blocks['items'] as $blockName => $blockItem)
                $returns = ZArrayHelper::merge($returns, $blockItem);
        }

        if (!$isOne)
            return $returns;

        return $this->makeCard($returns, $count);
    }

    /**
     *
     * Function  blockColumn, ZFormWidget columns generate in "all" type
     * @return  array
     */
    public function blockColumn()
    {

        $columns = $this->columns;
        $returns = [];
        foreach ($columns as $key => $value) {
            $returns[] = [$key];
        }
        return $returns;

    }

    #endregion


    #region Cores


    /**
     *
     * Function  isModel, if current object is model or not
     * @return  bool
     */
    public function isModel()
    {
        return $this instanceof ZActiveRecord;
    }

    public function isArray()
    {
        return $this->configs->type === ALLData::type['array'];
    }



    #endregion


    #region Columns

    /**
     *
     * Function  isAddColumn to check need add extra columns
     * @param string $attr extra column key with uppercase
     * @return  bool
     * @author Daho
     * @copyright 05.08.2020
     */
    public function isAddColumn($attr)
    {
        $config = 'add' . $attr;
        if ($this->isModel())
            if (isset($this->configs->$config))
                if ($this->configs->$config)
                    return true;

        return false;
    }


    /**
     *
     * Function  genColumn to generate extra columns
     * @param $attr string column key
     * @param $title string column title
     * @return  FormDb column
     * @author Daho
     * @since 04.08.2020
     */
    private function genColumn($attr, $title)
    {
        $column = new FormDb();
        $column->title = $title;

        switch ($attr) {

            case 'id':
                $column->index = true;
                $column->title = $this->configs->titleId;
                $column->widget = ZHInputWidget::class;
                $column->readonlyKernel = true;
                $column->mergeHeader = false;
                $column->filter = true;
                $column->width = '120px';
                $column->dbType = dbTypeInteger;
                $column->showForm = false;
                $column->showDyna = true;
                $column->rules = validatorInteger;
                break;

            case 'guid':
                $column->index = true;
                $column->title = $this->configs->titleGuid;
                $column->widget = ZHInputWidget::class;
                $column->readonlyKernel = true;
                $column->mergeHeader = false;
                $column->filter = true;
                $column->width = '50px';
                $column->dbType = dbTypeString;
                $column->showForm = false;
                $column->showDyna = false;

                break;

            case 'tranz':
                $column->title = Az::l('Транзакция');
                $column->dbType = dbTypeBoolean;
                $column->rules = validatorBoolean;
                $column->widget = ZKSwitchInputWidget::class;
                $column->filterWidget = ZMCheckboxWidget::class;
                $column->showDyna = false;
                $column->showForm = false;
                $column->index = true;

                break;

            case 'accept':
                $column->title = Az::l('Принято');

                $column->widget = ZKSwitchInputWidget::class;
                $column->index = true;
                $column->dbType = dbTypeBoolean;
                $column->rules = validatorBoolean;
                $column->widget = ZKSwitchInputWidget::class;
                $column->filterWidget = ZMCheckboxWidget::class;
                $column->value = function () {
                    if ($this->hasRoles(['admin', 'dev']))
                        return true;

                    return false;
                };
                $column->roleShow = [
                    RoleData::admin,
                    RoleData::dev,
                ];

                break;


            case 'active':
                $column->title = Az::l('Активный');
                $column->roleEdit = [
                    RoleData::admin,
                    RoleData::dev,
                ];
                $column->showDyna = false;
                $column->showForm = false;

                $column->dbType = dbTypeBoolean;
                $column->rules = validatorBoolean;
                $column->index = true;
                $column->widget = ZKSwitchInputWidget::class;
                $column->filterWidget = ZMCheckboxWidget::class;
                $column->value = true;
                $column->defaultValue = true;

                break;


            case 'sort':
                $column->title = Az::l('Сортировка');

                $column->index = true;
                $column->dbType = dbTypeInteger;
                $column->rules = validatorInteger;
                $column->showForm = false;
                $column->showDyna = false;

                $column->auto = false;
                $column->autoValue = function ($model) {
                    return $model->id ?? null;
                };
                break;

            case 'name':
                $column->title = $this->configs->titleName;
                $column->dbType = dbTypeString;
                $column->index = true;
                $column->showForm = false;
                $column->widget = ZHInputWidget::class;
                $column->width = '200px';

                $column->lang = true;

                $column->showDyna = false;
                $column->showDetail = false;
                $column->showView = true;

                $column->auto = $this->configs->nameAuto;
                $column->autoValue = $this->configs->nameValue;
                $column->autoWhenEmpty = $this->configs->nameAutoWhenEmpty;

                $column->indexUnique = $this->configs->nameIndexUnique;

                if ($this->configs->nameValidatorUnique)
                    $column->rules = [
                        [
                            validatorUnique
                        ]
                    ];


                if (!$column->auto)
                    if (is_string($column->rules))
                        $column->rules = null;

                if (!empty($this->configs->nameValidator))
                    $column->rules[] = $this->configs->nameValidator;

                $column->readonlyKernel = true;

                break;

            case 'title':
                $column->title = $this->configs->titleTitle;
                $column->dbType = dbTypeString;
                $column->index = true;
                $column->width = '100px';
                $column->rules = ZRequiredValidator::class;
                $column->widget = ZHInputWidget::class;
                $column->lang = true;

                break;

            case 'code':
                $column->title = $this->configs->titleCode;
                $column->dbType = dbTypeString;
                $column->width = '130px';
                $column->widget = ZHInputWidget::class;
                $column->auto = true;
                $column->readonlyKernel = true;
                $column->autoValue = $this->configs->codeValue;
                break;


            case 'deleted_text':
                $column->showForm = false;
                $column->hiddenFromExport = true;
                $column->dbType = dbTypeString;
                break;

            case 'deleted_by':
            case 'created_by':
            case 'modified_by':
                $column->index = true;
                $column->showForm = false;
                $column->readonlyKernel = true;
                $column->hiddenFromExport = true;
                $column->dbType = dbTypeInteger;
                $column->widget = ZKSelect2Widget::class;
                $column->fkTable = 'user';
                /*
                $column->auto = true;
                $column->autoValue = function ($model) {
                    return $this->userIdentity()->id;
                };
                */
                $column->rules = [
                    [validatorInteger]
                ];
                break;

            // todo:start AsrorZakirov

            case 'deleted_at':
            case 'created_at':
            case 'modified_at':
                $column->showForm = false;
                $column->index = true;
                $column->readonlyKernel = true;
                $column->hiddenFromExport = true;
                $column->dbType = dbTypeDateTime;
                $column->widget = ZKDateTimePickerWidget::class;
                $column->valueWidget = ZDateFormatWidget::class;
                $column->rules = [
                    [validatorString]
                ];
                /*
                   $column->auto = true;
                   $column->autoValue = function ($model) {
                       return date('Y-m-d H:i:s');
                   };
                */
                break;

            // todo:end AsrorZakirov

            case 'slug':
                $column->index = true;
                $column->readonly = true;
                break;

            case 'lang':
                $column->dbType = dbTypeJsonb;
                $column->widget = ZFormWidget::class;
                $column->width = '300px';
                $column->valueWidget = ZFormViewWidget::class;
                $column->options = [
                    'config' => [
                        'formClass' => LanguageForm::class,
                    ],
                ];
                $column->valueOptions = [
                    'config' => [
                        'formClass' => LanguageForm::class,
                    ],
                ];
                $column->mergeHeader = true;
                $column->filter = false;
                $column->pageSummary = false;
                break;

            case 'history':
                $column->dbType = dbTypeJsonb;
                $column->widget = ZMultiWidget::class;
                $column->valueWidget = ZMultiViewWidget::class;
                $column->filterWidget = ZKDatepickerWidget::class;
                $column->options = [
                    'config' => [
                        'formClass' => StatHistoryForm::class,
                    ],
                ];
                $column->valueOptions = [
                    'config' => [
                        'formClass' => StatHistoryForm::class,
                    ],
                ];
                $column->mergeHeader = true;
                $column->filter = false;
                $column->pageSummary = false;
                $column->readonlyKernel = true;
                break;
        }

        return $column;
    }


    /**
     *
     * Function  checkColumn to check generate or not column
     * @param $attr string column key
     * @return  bool
     * @author Daho
     * @copyright 04.08.2020
     *
     */
    public function checkColumn($attr)
    {
        $suffixs = [
            '_lang',
            '_history',
            '_slug',
        ];

        if (ZArrayHelper::isIn($attr, systemColumn) && $this->configs->showSystemColumn)
            return true;

        foreach ($suffixs as $str)
            if (ZStringHelper::endsWith($attr, $str))
                return $this->configs->showSystemColumn;

        if ($this->configs->showSystemColumn)
            return false;

        if ($this->configs->showDeleted) {
            if (ZArrayHelper::isIn($attr, trashColumn))
                return true;
            else
                return false;
        }

        if (!$this->configs->showSystemColumn) {
            if (!empty($this->configs->nameOn))
                if (ZArrayHelper::isIn($attr, $this->configs->nameOn))
                    return true;

            if (!empty($this->configs->nameOff))
                if (ZArrayHelper::isIn($attr, $this->configs->nameOff))
                    return false;

        }

        return true;
    }


#endregion


    #region Rules


    public function rules()
    {

        $columns = $this->columns;
        $returns = [];

        /** @var Form $form */
        foreach ($columns as $key => $form) {
            if (!empty($this->configs->ruleOn))
                if (!ZArrayHelper::isIn($key, $this->configs->ruleOn))
                    continue;

            $rules = $form->rules;

            if (is_callable($rules))
                $rules = $rules($this);

            if (is_string($rules))
                $rules = [[$rules]];

            if (!empty($rules))
                foreach ($rules as $rule) {
                    $returns[] = ZArrayHelper::merge([$key], $rule);
                }
        }
        return $returns;
    }


    /**
     * Creates validator objects based on the validation rules specified in [[rules()]].
     * Unlike [[getValidators()]], each time this method is called, a new list of validators will be returned.
     * @return ArrayObject validators
     * @throws InvalidConfigException if any validation rule configuration is invalid
     */
    public function createValidators()
    {
        $validators = new ArrayObject();

        foreach ($this->rules() as $rule) {
            if ($rule instanceof Validator) {
                $validators->append($rule);
            } elseif (is_array($rule) && isset($rule[0], $rule[1])) { // attributes, validator type
                $validator = ZValidator::createValidator($rule[1], $this, (array)$rule[0], array_slice($rule, 2));
                $validators->append($validator);
            } else {
                throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute name On and validator type.');
            }
        }

        return $validators;
    }


    #endregion


    #region CoreApp


    public function configALL($attr, $column)
    {

        foreach (ALLData::varString as $key)
            $column = $this->configGet($key, $column, $attr);

        foreach (ALLData::varBool as $key)
            $column = $this->configGet($key, $column, $attr);

        foreach (ALLData::varInt as $key)
            $column = $this->configGet($key, $column, $attr);

        foreach (ALLData::varCalls as $key)
            $column = $this->configGet($key, $column, $attr, false);

        foreach (ALLData::varArray as $key)
            $column = $this->configArray($key, $column, $attr);

        foreach (ALLData::varArrayInner as $key)
            $column = $this->configArray($key, $column, $attr);


        return $column;

    }


    /**
     *
     * Function  configGet
     * @param $attr
     * @param FormDb $column
     * @param $key
     * @param bool $call
     * @return  mixed
     */
    public function configGet($attr, $column, $key, $call = true)
    {

        if ($attr === 'title')
            $configAttr = 'titles';
        else
            $configAttr = $attr;


        $config = $this->configs;
        $myValue = null;

        $valueConfig = null;

        /*      if ($attr === 'changeReload')
                  vdd($config->$configAttr);*/

        if ($config->$configAttr !== null) {

            if (!is_array($config->$configAttr)) {
                $valueConfig = $config->$configAttr;


            } else
                switch (true) {
                    case ZArrayHelper::keyExists($key, $config->$configAttr):
                        $valueConfig = $config->$configAttr[$key];
                        break;

                    case ZArrayHelper::isIn($key, $config->$configAttr):
                        $valueConfig = true;
                        break;
                }
        }

        $valueColumn = $column->$attr;

        /*if ($key === 'id' && $attr === 'readonlyKernel')
        {
            vd('$valueConfig', $valueConfig);
            vd('Col', $valueColumn);
        }*/

        if ($valueConfig !== null && $column->preferConfig)
            $value = $valueConfig;
        else
            $value = $valueColumn;

        if ($call)
            if ($this->callableCheck($value))
                if ($this->callableCan())
                    $value = $this->callableExec($value);

        if ($attr === 'readonlyWidget' && $this->configs->readonlyWidget === false) {
            $value = $column->readonly;
        }
        $column->$attr = $value;

        return $column;
    }


    /**
     *
     * Function  configArray
     * @param $attr
     * @param FormDb $column
     * @param $key
     * @param bool $call
     * @return  mixed
     *
     */

    public function configArray($attr, $column, $key, $call = true)
    {

        $valueConfigAll = $this->configs->$attr;
        $valueColumn = $column->$attr;
        $return = [];

        if ($valueConfigAll !== null && $column->preferConfig) {
            if (is_array($valueConfigAll)) {
                $className = $this::className();
                $attrs = $className::attrs;
                $arrayKeys = array_keys($valueConfigAll);
                $number = false;
                $added = false;
                foreach ($arrayKeys as $keys) {
                    if (is_numeric($keys))
                        $number = true;
                }
                switch (true) {
                    case $number:
                        $return = $valueConfigAll;
                        $added = true;
                        break;
                    default:
                        foreach ($attrs as $list) {
                            if (ZArrayHelper::keyExists($list, $valueConfigAll) && $key === $list) {
                                $return = $valueConfigAll[$key];
                                $added = true;
                                break;
                            }
                        }
                        break;
                }
                if (!$added) {
                    $return = $valueColumn;
                }
            } else {
                $return = $valueConfigAll;
            }
        } else {
            $return = $valueColumn;
        }

        /**
         *
         * Choose
         */
        if ($call)
            if ($this->callableCheck($return))
                if ($this->callableCan()) {
                    $return = $this->callableExec($return);
                }

        $column->$attr = $return;
        return $column;
    }


#endregion


#region Search


    public function search($data = [], $bool = false)
    {

        if ($bool)
            return $this->search2();

        if ($this->isModel())
            return $this->searchModel();

        return $this->searchForm($data);

    }

    public function searchModel()
    {

        if (!empty($this->httpParams())) {
            $this->load($this->httpParams());
        }

        global $boot;

        $search = $this->httpGet('search');

        /* @var ZActiveQuery $Q */

        $query = $this->configs->query;

        if ($query !== null)
            if ($query instanceof Closure)
                $query = $query($this);

        if ($query === null)
            $Q = self::find($this);
        else
            $Q = $query;

        foreach ($this->columns as $key => $column) {

            if ($column->noSearch)
                continue;

            switch ($column->dbType) {

                case PgSqlSchema::TYPE_CHAR:
                case PgSqlSchema::TYPE_STRING:
//                    $val = explode('|', $this->{$key});
//                    if (\Dash\count($val) === 2)
//                        $this->{$key} = [
//                            'start' => $val[0],
//                            'end' => $val[1],
//                        ];
                    if (is_array($this->{$key})) {

                        if (empty($search)) {
                            $Q->andFilterWhere([$key => $this->{$key}]);
                        } else {
                            $search = ZArrayHelper::getValue($this->{$key}, $search);
                            $Q->andFilterWhere([$key => $search]);
                        }

                    } else {

                        if (empty($search))
                            $Q->andFilterWhere(['like', $key, $this->{$key}]);
                        else
                            $Q->orFilterWhere(['like', $key, $search]);

                    }

                    break;

                case PgSqlSchema::TYPE_DATETIME:
                case dbTypeDateTime:
//                $val = explode('|', $this->{$key});
//                    if (\Dash\count($val) === 2)
//                        $this->{$key} = [
//                            'start' => $val[0],
//                            'end' => $val[1],
//                        ];


                    if ($this->{$key}) {

                        if (is_array($this->{$key})) {

                            $value = $this->{$key};
                            $Q->andFilterWhere(['between', $key, ZArrayHelper::getValue($value, 'start'), ZArrayHelper::getValue($value, 'end')]);
                            $this->{$key} = null;

                        } else
                            $Q->andFilterWhere(['between', $key, $this->{$key} . ' 00:00:00', $this->{$key} . ' 23:59:59']);
                    }

                    break;

                case PgSqlSchema::TYPE_DATE:
                case PgSqlSchema::TYPE_TIME:
                case PgSqlSchema::TYPE_TIMESTAMP:

                    if (empty($search)) {

                        if (is_array($this->{$key})) {
                            $value = $this->{$key};
                            $Q->andFilterWhere(['beetwen', $key, ZArrayHelper::getValue($value, 'start'), ZArrayHelper::getValue($value, 'end')]);
                            // $this->{$key} = null;
                        } else {
                            $value = ZVarDumper::ajaxValue($this->{$key}, true);
                            $Q->andFilterWhere([$key => $value]);
                        }
                    } else
                        if (is_int($search)) {
                            $Q->orFilterWhere([$key => $search]);
                        }

                    /* if (ZArrayHelper::keyExists($key, $filter))
                         $Q->andFilterWhere([$key => $filter[$key]]);*/

                    break;

                case PgSqlSchema::TYPE_BOOLEAN:
                case PgSqlSchema::TYPE_FLOAT:
                case PgSqlSchema::TYPE_DECIMAL:
                case PgSqlSchema::TYPE_MONEY:
                case PgSqlSchema::TYPE_SMALLINT:
                case PgSqlSchema::TYPE_INTEGER:
                case PgSqlSchema::TYPE_BIGINT:

                    if (is_array($this->{$key})) {

                        if (empty($search)) {
                            $Q->andFilterWhere([$key => $this->{$key}]);
                        } else {
                            $search = ZArrayHelper::getValue($this->{$key}, $search);
                            $Q->andFilterWhere([$key => $search]);
                        }

                    } else {

                        if (empty($search)) {
                            $value = ZVarDumper::ajaxValue($this->{$key}, true);
                            $Q->andFilterWhere([$key => $value]);
                        } else
                            if (is_int($search)) {
                                $Q->orFilterWhere([$key => $search]);
                            }

                    }


                    /* if (ZArrayHelper::keyExists($key, $filter))
                         $Q->andFilterWhere([$key => $filter[$key]]);*/

                    break;


                case PgSqlSchema::TYPE_JSON:
                case PgSqlSchema::TYPE_JSONB:

                    if (!empty($this->{$key})) {

                        /*$value = ZVarDumper::search($this->{$key});
                        if (ZStringHelper::endsWith($key, '_ids'))
                            $value = strtr($value, [
                                '\'"' => '',
                                '"\'' => ''
                            ]);*/

                        if (empty($search)) {
                            $Q->whereJsonIn($key, $this->{$key}, true);
                        } else {
                            $search = ZArrayHelper::getValue($this->{$key}, $search);
                            $Q->andWhere("$key @> $search");
                        }
                    }
                    break;

                default:

                    break;
            }


        }
        if ($this->configs->customSort)
            $this->customSort($Q);

        if ($this->isAddColumn('ID'))
            $Q->andFilterWhere(['id' => $this->id]);
        /*
                if ($this->configs->addDel) {
                    if ($this->configs->showDeleted)
                        $Q->andWhere([
                            'not', ['deleted_at' => null,]
                        ]);
                    else
                        $Q->andWhere([
                            'deleted_at' => null,
                        ]);
                }*/

        $this->settingsFilter($Q);

        $pageSize = $this->configs->pageSize;
        if ($this->httpGet('all'))
            $pageSize = $this->configs->allPageSize;

        if ($pageSize === null)
            $pageSize = $boot->env('maxPageSize');

        $pagination = [
            'class' => ZPagination::class,
            'pageSize' => $pageSize,
            'defaultPageSize' => $pageSize,
            'route' => $this->urlArrayStr,
        ];

        $sort = [
            'class' => Sort::class,
            'defaultOrder' => $this->configs->order,
            'enableMultiSort' => true,
            'route' => $this->urlArrayStr,
        ];

        if (ZArrayHelper::keyExists('sort', $this->columns)) {
            $sort = [
                'defaultOrder' => [
                    'sort' => SORT_ASC,
                ],
            ];
        }

        if (!$this->isArray())
            return new ZActiveData([
                'query' => $Q,
                'sort' => $sort,
                'pagination' => $pagination,
            ]);

        $Q->asArray();

        return new ZActiveData([
            'query' => $Q,
            'sort' => $sort,
            'pagination' => $pagination
        ]);
    }

    public function getAttributes($names = null, $except = [])
    {
        $data = parent::getAttributes($names, $except);
        $collect = zcollect($data);

        $return = $collect
            ->only($this->columnsList())
            ->toArray();

        return $return;

    }


    private
    function customSort(&$Q)
    {
        //start|DavlatovRavshan|2020.10.10
        $sorts = $this->httpGet('sort');
        if (!empty($sorts))
            $sorts = explode(',', $sorts);

        $orderBy = [];
        if (!empty($sorts)) {

            foreach ($sorts as $sort) {

                $attribute = str_replace('-', '', $sort);

                if (ZStringHelper::startsWith($sort, '-'))
                    $orderBy[$attribute] = SORT_DESC;
                else
                    $orderBy[$attribute] = SORT_ASC;

            }

        } else {
            $orderBy = $this->configs->defaultOrder;
        }

        $Q->orderBy($orderBy);
        //end | DavlatovRavshan | 10.10.2020
    }

    private
    function settingsFilter(&$Q)
    {

        $dynaId = Az::$app->forms->dynas->dynaId($this->className);
        $coreDyna = DynaConfig::findOne([
            'dynaId' => $dynaId,
            'active' => true
        ]);

        if (!$coreDyna)
            return null;

        $models = DynaMulti::find()
            ->where([
                'dyna_config_id' => $coreDyna->id,
                'dynaId' => $dynaId,
                'active' => true,
            ])
            ->orderBy('id')
            ->all();

        $filters = Az::$app->market->filterForm->getFilters($models);

        foreach ($filters as $filter) {

            $operator = ZArrayHelper::getValue($filter, 'query');

            switch ($operator) {

                case 'or':
                    ZArrayHelper::remove($filter, 'query');
                    $Q->orWhere($filter);
                    break;

                default:
                    ZArrayHelper::remove($filter, 'query');
                    $Q->andWhere($filter);
                    break;

            }

        }

    }

    public function searchForm(array $data)
    {

        // todo: array item implementation

        $search = $this->httpGet('search');

        if (!CLI)
            $this->load(Az::$app->request->queryParams);

        $Q = new ZArrayQuery();
        $Q->from($data);

        foreach ($this->columnsList() as $key) {
            if (empty($search))
                $Q->andFilterWhere(['like', $key, $this->{$key}]);
            else
                $Q->orFilterWhere(['like', $key, $search]);
        }

        $sort = [
            'attributes' => $this->columnsList(),
            'enableMultiSort' => true,
            'class' => ZSort::class,
            'route' => $this->urlArrayStr,
        ];

        $pagination = [
            'class' => ZPagination::class,
            'pageSize' => 20,
            //'validatePage' => true,
            'totalCount' => 0,
            'defaultPageSize' => 20,
            //'pageSizeLimit' => [1, 50],
            'route' => $this->urlArrayStr,
        ];

        if ($this->httpGet('all'))
            $pagination = false;

        $this->configs->customSort = true;

        return new ZArrayData([
            //start: MurodovMirbosit 15.10.2020
            'allModels' => $Q->all(),
            'sort' => $sort,
            'pagination' => $pagination,
            'columns' => $this->columns,
            //end
        ]);

    }


    public function search2()
    {

        if (!empty($this->httpParams())) {
            $this->load($this->httpParams());
        }

        global $boot;

        /* @var ZActiveQuery $Q */

        if (!empty($this->configs->query))
            $Q = $this->configs->query;
        else
            $Q = self::find($this);

        $search = $this->httpGet('search');

        foreach ($this->columns as $key => $column) {

            if ($column->noSearch)
                continue;

            switch ($column->dbType) {

                case PgSqlSchema::TYPE_CHAR:
                case PgSqlSchema::TYPE_STRING:

                    if (is_array($this->{$key})) {

                        if (empty($search)) {
                            $Q->andFilterWhere([$key => $this->{$key}]);
                        } else {
                            $search = ZArrayHelper::getValue($this->{$key}, $search);
                            $Q->andFilterWhere([$key => $search]);
                        }

                    } else {

                        if (empty($search))
                            $Q->andFilterWhere(['like', $key, $this->{$key}]);
                        else
                            $Q->orFilterWhere(['like', $key, $search]);

                    }

                    break;

                case PgSqlSchema::TYPE_DATETIME:
                case dbTypeDateTime:

                    if ($this->{$key}) {

                        if (is_array($this->{$key})) {

                            $value = $this->{$key};
                            $Q->andFilterWhere(['between', $key, ZArrayHelper::getValue($value, 'start'), ZArrayHelper::getValue($value, 'end')]);
                            // $this->{$key} = null;

                        } else
                            $Q->andFilterWhere(['between', $key, $this->{$key} . ' 00:00:00', $this->{$key} . ' 23:59:59']);
                    }

                    break;

                case PgSqlSchema::TYPE_DATE:
                case PgSqlSchema::TYPE_TIME:
                case PgSqlSchema::TYPE_TIMESTAMP:

                    if (empty($search)) {

                        if (is_array($this->{$key})) {
                            $value = $this->{$key};
                            $Q->andFilterWhere(['beetwen', $key, ZArrayHelper::getValue($value, 'start'), ZArrayHelper::getValue($value, 'end')]);
                            // $this->{$key} = null;
                        } else {
                            $value = ZVarDumper::ajaxValue($this->{$key}, true);
                            $Q->andFilterWhere([$key => $value]);
                        }

                    } else
                        if (is_int($search)) {
                            $Q->orFilterWhere([$key => $search]);
                        }

                    /* if (ZArrayHelper::keyExists($key, $filter))
                         $Q->andFilterWhere([$key => $filter[$key]]);*/

                    break;

                case PgSqlSchema::TYPE_BOOLEAN:
                case PgSqlSchema::TYPE_FLOAT:
                case PgSqlSchema::TYPE_DECIMAL:
                case PgSqlSchema::TYPE_MONEY:
                case PgSqlSchema::TYPE_SMALLINT:
                case PgSqlSchema::TYPE_INTEGER:
                case PgSqlSchema::TYPE_BIGINT:

                    if (is_array($this->{$key})) {

                        if (empty($search)) {
                            $Q->andFilterWhere([$key => $this->{$key}]);
                        } else {
                            $search = ZArrayHelper::getValue($this->{$key}, $search);
                            $Q->andFilterWhere([$key => $search]);
                        }

                    } else {

                        if (empty($search)) {
                            $value = ZVarDumper::ajaxValue($this->{$key}, true);
                            $Q->andFilterWhere([$key => $value]);
                        } else
                            if (is_int($search)) {
                                $Q->orFilterWhere([$key => $search]);
                            }

                    }


                    /* if (ZArrayHelper::keyExists($key, $filter))
                         $Q->andFilterWhere([$key => $filter[$key]]);*/

                    break;

                case PgSqlSchema::TYPE_JSON:
                case PgSqlSchema::TYPE_JSONB:

                    if (!empty($this->{$key})) {

                        $value = ZVarDumper::search($this->{$key});
                        if (ZStringHelper::endsWith($key, '_ids'))
                            $value = strtr($value, [
                                '\'"' => '\'',
                                '"\'' => '\''
                            ]);
                        if (empty($search)) {
                            $Q->andWhere("$key @> $value");
                        } else {
                            $search = ZArrayHelper::getValue($this->{$key}, $search);
                            $Q->andWhere("$key @> $search");
                        }

                    }

                    break;

                default:

                    break;
            }


        }

        $sorts = $this->httpGet('sort');
        if (!empty($sorts))
            $sorts = explode(',', $sorts);

        $orderBy = [];
        if (!empty($sorts)) {

            foreach ($sorts as $sort) {

                $attribute = str_replace('-', '', $sort);

                if (ZStringHelper::startsWith($sort, '-'))
                    $orderBy[$attribute] = SORT_DESC;
                else
                    $orderBy[$attribute] = SORT_ASC;

            }

        } else {
            $orderBy = $this->configs->defaultOrder;
        }

        $Q->orderBy($orderBy);

        if ($this->isAddColumn('ID'))
            $Q->andFilterWhere(['id' => $this->id]);

        $this->settingsFilter($Q);

        $pageSize = $this->configs->pageSize;
        if ($this->httpGet('all'))
            $pageSize = $this->configs->allPageSize;

        if ($pageSize === null)
            $pageSize = $boot->env('maxPageSize');

        $pagination = [
            'class' => ZPagination::class,
            'pageSize' => $pageSize,
            'defaultPageSize' => $pageSize,
            'route' => $this->urlArrayStr,
        ];


        if ($this->configs->addDel) {
            if ($this->configs->showDeleted)
                $Q->andWhere([
                    'not', ['deleted_at' => null,]
                ]);
            else
                $Q->andWhere([
                    'deleted_at' => null,
                ]);
        }

        $sort = [
            'defaultOrder' => $order,
            'enableMultiSort' => true,
            'class' => ZSort::class,
            'route' => $this->urlArrayStr,
        ];

        if (ZArrayHelper::keyExists('sort', $this->columns)) {
            $sort = [
                'defaultOrder' => [
                    'sort' => SORT_ASC,
                ],
            ];
        }

        if (!$this->isArray()) {
            return new ZActiveData([
                'query' => $Q,
                'sort' => $sort,
                'pagination' => $pagination
            ]);

        }

        $data = $Q
            ->asArray()
            ->all();

        return new ArrayDataProvider([
            'allModels' => $data,
            //'sort' => $sort,
            'pagination' => $pagination
        ]);
    }

#endregion


}
