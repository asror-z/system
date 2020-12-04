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

namespace zetsoft\system\control;


use Closure;
use Doctrine\Instantiator\Instantiator;
use Opis\Closure\ReflectionClosure;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;
use yii\caching\TagDependency;
use yii\web\JqueryAsset;
use ZALL;
use zetsoft\dbdata\auth\RoleData;
use zetsoft\dbitem\core\WebItem;
use zetsoft\dbitem\core\SessionItem;
use zetsoft\dbitem\data\Config;
use zetsoft\dbitem\data\ConfigDB;
use zetsoft\dbitem\data\Form;
use zetsoft\dbitem\data\FormDb;
use zetsoft\models\chat\ChatMail;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\test\TestOrder;
use zetsoft\models\user\User;
use zetsoft\service\cores\Auth;
use zetsoft\service\cores\Cache;
use zetsoft\service\cores\Langs;
use zetsoft\service\forms\Modelz;
use zetsoft\service\forms\ZPjax;
use zetsoft\service\utility\Execs;
use zetsoft\service\utility\Views;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\actives\ZDynamicModel;
use zetsoft\system\actives\ZModel;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFormatter;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZJsonHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZTrait;
use zetsoft\system\kernels\ZView;
use zetsoft\system\module\Models;
use zetsoft\widgets\inputes\ZKDateRangePickerWidget;
use zetsoft\widgets\inputes\ZKTouchSpinWidget;
use zetsoft\widgets\notifier\ZKAlertWidget;
use function DusanKasan\Knapsack\diff;
use function Spatie\SslCertificate\length;
use function zetsoft\apisys\edit\returnn;


/**
 * Trait ZCoreTrait
 * @package zetsoft\system\control
 *
 * @property ZView $view
 * @method thisGet()
 * @method thisSet($object = null)
 */
trait ZCoreTrait
{

    use  ZTrait;


    #region Vars


    /**
     * @var string|null
     *
     * Disable Second Save for Model
     */
    public ?string $paramSecondSave = null;
    public ?string $paramNoWarn = null;
    public ?string $paramIsUpdate = null;


    public ?WebItem $actionItem = null;
    public $modelAttrs = [];

    /**
     * @var bool
     * Search parameter for ZActiveTrait, search method, used in DynaWidget
     * @author AsrorZakirov
     */
    public $search = false;


    public ?string $mainFile = null;

    public ?string $clazz = null;
    public ?string $className = null;


    /**
     * @var string|null
     *
     * http://mplace.zoft.uz/shop/admin/ware-return/asror.aspx?sadf=sdf&dsaf=sadf
     * "/shop/admin/ware-return/asror.aspx"
     */
    public ?string $urlMain = null;

    /**
     * @var array
     *
     * http://mplace.zoft.uz/shop/admin/ware-return/asror.aspx?sadf=sdf&dsaf=sadf
     * array:2 [▼
     * "sadf" => "sdf"
     * "dsaf" => "sadf"
     * ]
     *
     */
    public array $urlParam = [];


    /**
     * @var string|null
     *
     * http://mplace.zoft.uz/shop/admin/ware-return/asror.aspx?sadf=sdf&dsaf=sadf
     *  "sadf=sdf&dsaf=sadf"
     */
    public ?string $urlParamStr = null;

    /**
     * @var array
     *
     * http://mplace.zoft.uz/shop/admin/ware-return/asror.aspx?sadf=sdf&dsaf=sadf
     *
     * ^ array:4 [▼
     * 0 => "shop"
     * 1 => "admin"
     * 2 => "ware-return"
     * 3 => "asror"
     * ]
     */

    public array $urlArray = [];

    /**
     * @var array
     *
     * http://mplace.zoft.uz/shop/admin/ware-return/asror.aspx?sadf=sdf&dsaf=sadf
     * ^ array:3 [▼
     * 0 => "shop"
     * 1 => "admin"
     * 2 => "ware-return"
     * ]
     */
    public array $urlModule = [];


    /**
     * @var string|null
     *
     * http://mplace.zoft.uz/shop/admin/ware-return/asror.aspx?sadf=sdf&dsaf=sadf
     * /shop/admin/ware-return/asror
     */
    public ?string $urlArrayStr = null;


    /**
     * @var string|null
     *
     * http://mplace.zoft.uz/shop/admin/ware-return/asror.aspx?sadf=sdf&dsaf=sadf
     * /shop/admin/ware-return
     */
    public ?string $urlModuleStr = null;


    /**
     * @var string|null
     *
     * http://mplace.zoft.uz/shop/admin/ware-return/asror.aspx?sadf=sdf&dsaf=sadf
     * ^ "/index.php"
     */
    public ?string $urlScript = null;

    /**
     * @var string|null
     *
     * http://mplace.zoft.uz/shop/admin/ware-return/asror.aspx?sadf=sdf&dsaf=sadf
     * shop/admin/ware-return/asror.aspx
     */
    public ?string $urlPath = null;


    /**
     * @var Models $model
     *
     */
    public $model;

    /**
     * @var Models $model
     * Главная модель который идет от ZWidget, для ZMultiwidget
     */

    public $modelMain;

    public $sample;


    /* @var Models $model */
    public $modelClass = null;

    public ?string $modelClassName = null;
    public $formUrl;
    public $attribute;

    /**
     * @var
     * Attribute Given from Parent class
     */
    public ?string $parentAttr = null;

    /**
     * @var
     * Parent Class name for Dynamicform
     */
    public ?string $parentClass = null;
    public ?string $parentClassName = null;

    /* @var Models $parentModel */
    public $parentModel;

    public ?int $parentId = null;


    public ?string $attributeAll = null;

    /* @var ConfigDB $modelConfigs */
    public ?Config $modelConfigs = null;

    /* @var FormDb[] $modelColumns */
    public ?array $modelColumns = [];

    /* @var FormDb $modelColumn */
    public ?Form $modelColumn = null;
    public $modelCards;


    /**
     * @var
     */

    public ?string $moduleId = null;
    public ?string $actionId = null;


    #endregion

    #region Exec

    public function execPhpDefault($cmd, $app = App)
    {
        Az::$app->utility->execs->php($cmd, $app, Execs::type['default']);
    }

    public function execPhpBackground($cmd, $app = App)
    {
        Az::$app->utility->execs->php($cmd, $app, Execs::type['background']);
    }

    public function execPhpContinous($cmd, $app = App)
    {
        Az::$app->utility->execs->php($cmd, $app, Execs::type['continous']);
    }


    public function execDefault($cmd)
    {
        Az::$app->utility->execs->exec($cmd, Execs::type['default']);
    }

    public function execBackground($cmd)
    {
        Az::$app->utility->execs->exec($cmd, Execs::type['background']);
    }

    public function execContinous($cmd, $restartDelay = 1)
    {
        $this->paramSet('restartDelay', $restartDelay);
        Az::$app->utility->execs->exec($cmd, Execs::type['continous']);
    }


    #endregion


    #region URL
    /**
     *
     * Function  prelastUrl
     * @return  string
     *
     */
    public function prelastUrl()
    {


        $explode_URL = $this->urlArray;
        $path = Root . '/webhtm/' . $this->urlArrayStr;
        if (is_dir($path)) {
            return '/' . $this->urlArrayStr;
        }
        unset($explode_URL[count($explode_URL) - 1]);
        $explode_URL = implode('/', $explode_URL);

        return '/' . $explode_URL;
    }

    public function parentUrl()
    {
        $url = $this->urlGoIndex() . 'index.aspx';
        return $url;
    }

    public function modifyUrl(int $index = null, bool $removeLast = false)
    {
        $explode = $this->urlArray;

        if ($removeLast) {
            ZArrayHelper::remove($explode, count($explode) - 1);
        }

        if (is_array($index)) {
            foreach ($index as $item) {
                ZArrayHelper::remove($explode, $item);
            }

            $url = '/' . implode('/', $explode);
            return str_replace('.aspx', '', $url);
        }

        if ($index !== null) {
            ZArrayHelper::remove($explode, $index);
            return '/' . implode('/', $explode);
        }

        return str_replace('.aspx', '', $this->urlArrayStr);
    }

    public function urlData(int $index = 0)
    {
        return ZArrayHelper::getValue($this->urlArray, $index);
    }



    #endregion

    #region Model Delete

    public $dbHasTable = [];

    public function dbHasTable($name = null)
    {

        if ($name === null)
            $name = $this::tableName();

        if (is_bool($value = ZArrayHelper::getValue($this->dbHasTable, $name)))
            return $value;

        $tableSchema = Az::$app->db->schema->getTableSchema($name, true);
        if ($tableSchema === null)
            $this->dbHasTable[$name] = false;
        else
            $this->dbHasTable[$name] = true;

        return $this->dbHasTable[$name];
    }


    public function dbTruncateTable($table)
    {

        //$rows = Az::$app->db->createCommand()->truncateTable($table)->execute();
        $rows = Az::$app->db->createCommand('TRUNCATE TABLE "' . $table . '" RESTART IDENTITY')->execute();
        Az::info($rows, 'Truncated Rows');

    }

    public function dbDeleteData($table, $condition)
    {
        $rows = Az::$app->db->createCommand()->delete($table, $condition)->execute();
        Az::info($rows, 'Truncated Rows');
    }

    #endregion

    #region Model Check

    public function hasModel()
    {
        return $this->modelCheck();
    }

    public function instanceOfClass(string $childClass, string $parentClass)
    {

        if (is_subclass_of($childClass, $parentClass))
            return true;

        return false;

    }

    public function checkModel($className)
    {

        if ($this->instanceOfClass($className, ZActiveRecord::class)) {
            return true;
        }

        return false;
    }

    public function checkForm($className)
    {

        $b1 = $className instanceof ZModel;
        $b2 = $className instanceof ZDynamicModel;
        if ($b1 || $b2) {
            return true;
        }
        return false;
    }

    public function modelCheck(bool $array = false)
    {

        $notModel = $this->model === null;
        $notAttr = $this->attribute === null;
        $notObject = !is_object($this->model);

        if ($array === true)
            $all = $notModel || $notAttr;
        else
            $all = $notModel || $notAttr || $notObject;

        if ($all)
            return false;

        return true;
    }

    /**
     *
     * Function  tableRelated
     * @param $key
     * @param FormDb $column
     * @return  bool
     */
    public function tableRelated($key, $column)
    {
        $b1 = ZStringHelper::endsWith($key, '_id');
        $b2 = !empty($column->fkTable) && !$column->fkMulti;
        if ($b1 || $b2)
            return true;

        return false;
    }

    public function tableRelatedJson($key, $column)
    {
        $b1 = ZStringHelper::endsWith($key, '_ids');
        $b2 = !empty($column->fkTable) && $column->fkMulti;
        if ($b1 || $b2)
            return true;

        return false;
    }

    public function modelCheckDyna()
    {
        if (!$this->modelCheck())
            return false;

        if (!ZStringHelper::startsWith($this->attribute, '['))
            return false;

        return true;
    }

    #endregion


    #region Form

    public function formSave(&$model)
    {
        return Az::$app->forms->modelz->saveForm($model);
    }

    public function formModel($model)
    {
        return Az::$app->forms->modelz->form($model);
    }

    public function formGet(string $modelClass)
    {
        return Az::$app->forms->modelz->getForm($modelClass);
    }

    #endregion

    #region Model


    /**
     *
     * Function  modelSave
     * @param Models $model
     * @return  bool
     *
     */
    public function modelSave(&$model)
    {
        $saved = Az::$app->forms->modelz->save($model);
//        $model->columns();
        return $saved;

    }


    public function modelGet(string $modelClass, int $id = 0)
    {
        return Az::$app->forms->modelz->get($modelClass, $id);
    }

    /**
     *
     * Function  modelAutoSave
     * @param $modelClass
     * @param string $url
     * @return  ZActiveRecord|Models|null
     */
    public function modelAutoSave($modelClass, $url = 'create')
    {

        $id = $this->httpGet('id');

        /** @var ZActiveRecord $modelClass */
        if (!empty($id))
            $model = $modelClass::findOne($id);

        if (empty($id) || $model === null) {

            /** @var Models $model */
            $model = new $modelClass();
            $model->configs->rules = validatorSafe;

            if ($model->save()) {
                $this->urlRedirect([
                    $url,
                    'id' => $model->id,
                    'isNew' => true,
                    'spa' => $this->httpGet('spa'),
                ]);
            }
        }

        return $model;
    }


    public function modelFormPjax($model)
    {
        return Az::$app->forms->modelz->formPjax($model);
    }

    public function isRest()
    {

        if ($this->moduleId === 'api' || $this->moduleId === 'rest')
            return true;

        return false;

    }

    public function modelClone($modelClass, $id)
    {
        return Az::$app->forms->modelz->clone($modelClass, $id);
    }


    #endregion


    #region Test

    public function testEqual($one, $two)
    {
        try {
            Assert::assertEquals($one, $two);

        } catch (ExpectationFailedException $exception) {

        }

    }

    #endregion


    #region CAllable


    public function callableCheck($item): bool
    {

        if ($item instanceof Closure)
            return true;

        return false;

    }


    public function callableCan()
    {
        $b1 = $this->paramGet(paramMigration);
        $b2 = $this->paramGet(paramNorms);
        $b3 = $this->paramGet(paramModel);

        if (!$b1 && !$b2 && !$b3)
            return true;

        return false;
    }


    public function callableExec($item, $object = null)
    {
        $closure = new ReflectionClosure($item);
        $myValue = null;
//               vd($closure);

        if ($object === null)
            $object = $this;

        if ($closure->getNumberOfParameters() <= 1)
            $myValue = $item($object);

        return $myValue;
    }

    #endregion

    #region Param


    public function paramGet($key, $default = null)
    {
        if (!isset(Az::$app->params[$key])) {
            return false;
        }
        return ZArrayHelper::getValue(Az::$app->params, $key, $default);
    }

    public function paramGets($keys)
    {
        foreach ($keys as $key) {
            if ($this->paramGet($key))
                return true;

        }
        return false;
    }


    public function paramGetValue($data, $key, $default = null)
    {
        $data = $this->paramGet($data);
        return ZArrayHelper::getValue($data, $key, $default);
    }

    public function paramSet($key, $value)
    {
        return Az::$app->params[$key] = $value;
    }

    #endregion

    #region Time

    public function timeGet($key, $type = null)
    {

        if ($type === null)
            return ZArrayHelper::getValue(Az::$app->params, 'time.' . $key);
        else
            return ZArrayHelper::getValue(Az::$app->params, 'time.' . $key . '.' . $type);

    }

    public function timeGetAfter($key)
    {
        $this->timeAfter($key);
        return $this->timeGet($key);
    }

    public function timeBefore($key)
    {
        Az::$app->params['time'][$key]['before'] = Az::$app->cores->date->dateTimeFull();
    }

    public function timeAfter($key)
    {
        Az::$app->params['time'][$key]['after'] = Az::$app->cores->date->dateTimeFull();

        $before = $this->timeGet($key, 'before');
        $after = $this->timeGet($key, 'after');

        $diff = Az::$app->cores->date->diffMicro($before, $after);

        Az::$app->params['time'][$key]['diff'] = $diff;
    }


    public function watchStart($event)
    {
        return Az::$app->process->stopWatch->start($event);
    }

    public function watchLap($event)
    {
        return Az::$app->process->stopWatch->lap($event);
    }

    public function watchStop($event)
    {
        return Az::$app->process->stopWatch->stop($event);
    }



    #endregion

    #region Boot


    public function bootEnv($key)
    {
        global $boot;
        return $boot->env($key);
    }


    public function bootFullUrl($urlIndex = 1)
    {

        return $this->bootFull(ZInflector::camelize($this->urlData($urlIndex)));

    }


    public function bootFull($className)
    {
        $return = ZStringHelper::full($className);

        $val = str_replace('\\\\', '\\', $return);
        if (class_exists($val)) {
            return $val;
        }
        $val = str_replace('\\\\', '\\App\\' . App . '\\', $return);
        return $val;
    }

    public function bootFullForm($className)
    {
        return ZStringHelper::fullForm($className);
    }




    #endregion


    #region App


    public function myId()
    {
//        return bname(static::className()) . '-' . random_int(1, 100000);
        return random_int(1, 100000);
    }

    public function appModule()
    {
        return App;
    }



    #endregion

    #region Category

    public function catModel($model)
    {
        return ZStringHelper::catModel($model);
    }

    public function catForm($model)
    {
        return ZStringHelper::catForm($model);
    }


    #endregion


    #region Model Utility

    public function modelData($sampleType = Modelz::sample['Select2'])
    {
        return Az::$app->forms->modelz->data($sampleType);
    }

    public function modelInfo($model)
    {
        return Az::$app->forms->modelz->info($model);
    }

    public function modelPost($die = false)
    {
        return Az::$app->forms->modelz->post($die);
    }

    public function tableExists($table)
    {
        return Az::$app->forms->modelz->exists($table);
    }


    #endregion


    #region User

    public function userIdentity()
    {

        return Az::$app->cores->auth->getIdentity();
    }

    public function userIsGuest()
    {
        if (!$this->isCLI())
            return Az::$app->cores->auth->isGuest();
        else
            return false;
    }

    public function userPhotos()
    {
        $this->thisSet();
        return Az::$app->cores->auth->photos();
    }

    public function userPhoto()
    {
        $this->thisSet($this);
        return Az::$app->cores->auth->photo();
    }

    public function userRole()
    {
        $this->thisSet();
        return Az::$app->cores->rbac->getRole();
    }

    public function userRoleTitle()
    {
        $this->thisSet();

        $role = Az::$app->cores->rbac->getRole();

        $langs = (new RoleData)->lang();

        $roleTitle = ZArrayHelper::getValue($langs, $role);

        return $roleTitle;
    }

    /**
     *
     * Function  userisAdmin
     * @return  bool
     * @author Daho
     * @lines 3
     */
    public function userIsAdmin()
    {
        $roles = [
            'admin',
            'dev'
        ];

        return ZArrayHelper::isIn($this->userRole(), $roles);
    }
    #endregion

    #region Perms


    public function hasRole($role)
    {
        $this->thisSet();
        return Az::$app->cores->rbac->checkRole($role);
    }

    public function hasRoles($roles)
    {
        $this->thisSet();
        return Az::$app->cores->rbac->checkRoles($roles);
    }


    #endregion

    #region Names

    public function nameProvider($object)
    {
        $className = $object->modelClassName;
        $url = $object->urlArrayStr;
        $userId = $object->userIdentity()->id;

    }

    #endregion


    #region Alert

    public function alertInfo($data, $title = Azl . 'Информация')
    {
        Az::$app->utility->alert->info($title, $data);
        return true;
    }

    public function alertSuccess($data, $title = Azl . 'Успешно')
    {
        Az::$app->utility->alert->success($title, $data);
        return true;
    }

    public function alertPrimary($data, $title = Azl . 'Информация')
    {
        Az::$app->utility->alert->primary($title, $data);
        return true;
    }

    public function alertWarning($data, $title = Azl . 'Внимание!')
    {
        Az::$app->utility->alert->warning($title, $data);
        return true;
    }

    public function alertDefault($data, $title = Azl . 'Внимание!')
    {
        Az::$app->utility->alert->default($title, $data);
        return true;
    }

    public function alertDanger($data, $title = Azl . 'Ошибка')
    {
        Az::$app->utility->alert->danger($title, $data);
        return true;
    }

    public function alertCustom($data, $title = Azl . 'Предупреждение')
    {
        Az::$app->utility->alert->custom($title, $data);
        return true;
    }

    #endregion

    #region Notify


    public function mailAll($subject, $userId, $view, $data, $file = "")
    {
        /** @var User $user */
        $user = User::findOne($userId);
        Az::$app->utility->mails->to = $user->email;
        Az::$app->utility->mails->subject = $subject;
        Az::$app->utility->mails->view = $view;
        Az::$app->utility->mails->data = $data;
        Az::$app->utility->mails->file = $file;
        Az::$app->utility->mails->run();

        $mail = new ChatMail();
        $mail->text = ZVarDumper::export($data);
        $mail->title = $subject;
        $mail->user_id = $userId;
        $mail->to = $user->email;
        $mail->save();

        return true;

    }


    public function notifyInfo($title, $data = null,  $user = null, $link = null)
    {
        Az::$app->utility->notify->info($title, $data, $user, $link);
        return true;
    }


    public function notifySuccess($title, $data = null, $user = null, $link = null)
    {
        Az::$app->utility->notify->success($title, $data, $user, $link);
        return true;
    }

    public function notifyError($title, $data = null, $user = null, $link = null)
    {
        Az::$app->utility->notify->danger($title, $data, $user,$link);
        return false;
    }

    public function notifyWarning($title, $data = null, $user = null, $link = null)
    {
        Az::$app->utility->notify->warning($title, $data, $user,$link);
        return false;
    }



    #endregion


    #region View


    public function viewItem()
    {

        /** @var WebItem $action */
        $action = $this->paramGet(paramAction);
        $this->actionItem = $action;
    }

    public function viewTitle()
    {
        $this->viewItem();
        return $this->actionItem->title;
    }


    public function viewAsset($file, $params = [])
    {
        return Az::$app->utility->views->assets($file, $params);
    }

    public function title()
    {
        return Az::$app->utility->views->title();
    }


    public function toolbar()
    {

        //$mode = true;

        switch (true) {
            case $this->moduleId === 'edit':
            case $this->isMobile():
                $mode = false;
                break;

            default:
                /** @var WebItem $action */
                $action = $this->paramGet(paramAction);
                $mode = $action->debug;


        }

        return Az::$app->utility->views->toolbarMode($mode);
    }


    public function isMobile()
    {
        $isMobile = Az::$app->mobile->mobileDetecting->checkMobileDevice();
        if (!$isMobile)
            return false;

        return true;
    }

    public function toolbarMode($mode)
    {
        return Az::$app->utility->views->toolbarMode($mode);
    }

    public function mobile($action = null)
    {
        if ($action === null)
            $action = $this->actionId . '-m';

        $detect = new MobileDetect();

        if ($detect->isMobile())
            $this->urlRedirect($action);

    }

    #endregion

    #region Render

    public function requirePart($file, $params = [], $key = null)
    {
        if ($key === null)
            $key = $file . ZJsonHelper::encode($params);
        $return = $this->cache($key, Cache::type['array'], function () use ($file, $params) {
            return Az::$app->utility->views->renderPart($file, $params);
        });
        return $return;
    }

    public function requirePartGrape($file, $params = [])
    {
        return Az::$app->utility->views->renderPartGrape($file, $params);
    }

    public function requirePartFile($file, $params = [], $key = null)
    {
        if ($key === null)
            $key = $file . ZJsonHelper::encode($params);
        $return = $this->cache($key, Cache::type['array'], function () use ($file, $params) {
            return Az::$app->utility->views->renderPartFile($file, $params = []);
        });
        return $return;
    }

    public function requireAjax($file, $params = [], $key = null)
    {
        if ($key === null)
            $key = $file . ZJsonHelper::encode($params);

        $return = $this->cache($key, Cache::type['array'], function () use ($file, $params) {
            return Az::$app->utility->views->renderAjax($file, $params);
        });
        return $return;
    }


    public function require($file, $params = [], $key = null, $addRoot = true): ?string
    {
        if ($addRoot) {
            $file = Root . $file;
        }
        if ($key === null)
            $key = $file . ZJsonHelper::encode($params);

        $return = $this->cache($key, Cache::type['array'], function () use ($file, $params) {


            return Az::$app->view->renderPhpFile($file, $params);


        });

        return $return;
    }

    public function requireApi($file, $params = [], $key = null)
    {
        if ($key === null)
            $key = $file . ZJsonHelper::encode($params);

        $return = $this->cache($key, Cache::type['redis'], function () use ($file, $params) {
            return require $file;
        });

        return $return;
    }


    public function emptyVar($var)
    {
        if (!isset($var) || empty($var))
            return true;

        return false;

    }

    public function emptyOrNullable($var)
    {
        if (!isset($var) || empty($var) || $var === null)
            return true;

        return false;

    }


#endregion


    #region Grapes

    public function require_block($path)
    {

        $path = str_replace(Az::getAlias('@zetsoft'), '', $path);
        $blockPath = 'zetsoft' . str_replace(['\\', '.php'], ['/', ''], $path);

        ?>
        <div class="template-block parent-div" data-css="" ajaxable="true">
            <!--TEMPLATEBEGIN-->
            <div class='widgetsWrap' widget="<?= $blockPath ?>" basename="<?= bname($blockPath) ?>">

                <? require Root . $path ?>

            </div>
            <!--TEMPLATEEND-->
            <!--$this->require_block(Root . '<?= $path ?>');-->
        </div>
        <?php
    }


    public function requireAjaxFile($file, $params = [], $key = null)
    {
        if ($key === null)
            $key = $file . ZJsonHelper::encode($params);
        $return = $this->cache($key, Cache::type['array'], function () use ($file, $params) {
            return Az::$app->utility->views->renderAjaxFile($file, $params = []);
        });
        return $return;
    }

    public function requireAjaxFilePreg($file, $params = [], $key = '')
    {
        if ($key === null)
            $key = $file . ZJsonHelper::encode($params);

        $return = $this->cache($key, Cache::type['array'], function () use ($file, $params) {
            return Az::$app->utility->views->renderAjaxFilePreg($file, $params = []);
        });

        return $return;
    }


    #endregion


    #region Require HTM


    public function requireHtmPart($htm)
    {
        return Az::$app->utility->views->renderHtmPart($htm);
    }


    public function requireHtmAjax($htm)
    {
        return Az::$app->utility->views->renderHtmAjax($htm);
    }


    #endregion

    #region Assets


    public function jscode($value)
    {
        return ZVarDumper::jscode($value);
    }


    public function fileGet($url)
    {
        $return = $this->cache($url, Cache::type['array'], function () use ($url) {
            return file_get_contents($url);
        });

        return $return;
    }

    public function fileJs($url, $position = Views::position['head'], $depends = null)
    {
        $this->thisSet();

        $this->headerJs($url);
        return Az::$app->utility->views->addJs($url, $position, $depends);
    }

    public function fileCss($url, $depends = [])
    {
        $this->thisSet();

        $this->headerCss($url);
        return Az::$app->utility->views->addCss($url, $depends);
    }


    public function headerCss($url)
    {
        Az::$app->utility->views->headCss($url);
    }

    public function headerJs($url)
    {
        Az::$app->utility->views->headJs($url);
    }

    public function headerImg($url, $execute = false)
    {
        Az::$app->utility->views->headImg($url, $execute);
    }

    public function headerFont($url)
    {
        Az::$app->utility->views->headFont($url);
    }


    public function linkAll()
    {
        return Az::$app->utility->views->linkAll();

    }







    #endregion


    #region URL GO

    public function urlGoBack()
    {
        $this->thisSet();
        return Az::$app->utility->urlApp->goBacks();
    }

    public function urlGoIndex()
    {
        $this->thisSet();
        return Az::$app->utility->urlApp->goIndex();
    }
    #endregion

    #region URL

    public function urlRedirectAll()
    {
        if ($this->httpGet('returnUrl')) {
            return $this->urlRedirectReturn();
        }

        $url = $this->urlRole();

        // vdd($url);
        $this->urlRedirect($url);
    }

    public function urlRole()
    {
        $url = new RoleData;
        $menu = $url->menu();
        $url = $menu[$this->userRole()];

        return $url;
    }

    public function urlRedirect($urlData, $merge = false, $code = 200)
    {
        $this->thisSet();
        return Az::$app->utility->urlApp->forwards($urlData, $merge, $code);
    }

    public function urlHasReturn()
    {
        $url = $this->httpGet('returnUrl');
        
        if ($url === null)
            $url = $this->sessionGet('returnUrl');

        if ($url !== null)
            return $url;

        return null;

    }

    public function urlRedirectReturn()
    {
        if ($url = $this->urlHasReturn())
            return $this->urlRedirect($url);
        return false;
    }

    public function urlRefresh()
    {
        $this->thisSet();
        return Az::$app->utility->urlApp->refreshes();
    }

    public function urlCheck($urlTo)
    {
        return Az::$app->utility->urlApp->check($urlTo);
    }


    public function urlGetBack()
    {
        return Az::$app->utility->urlApp->getBack();
    }

    public function urlGetMain()
    {
        return Az::$app->utility->urlApp->getMain();
    }

    public function urlGetBase()
    {
        return Az::$app->utility->urlApp->getBase();
    }

    #endregion

    #region Url IS

    public function urlIsMain()
    {
        return Az::$app->utility->urlApp->isMain();
    }

    public function urlIsLogin()
    {
        return Az::$app->utility->urlApp->isLogin();
    }

    #endregion

    #region URL App

    public function urlMerge($urlData, $merge = true)
    {
        $this->thisSet();
        return Az::$app->utility->urlApp->merge($urlData, $merge);
    }

    public function urlTo($urlData, $merge = true)
    {
        $this->thisSet();
        return Az::$app->utility->urlApp->to($urlData, $merge);
    }


    #endregion

    #region Cache


    public function cacheSet($name, $value = '', string $type = Cache::type['cache'], $dependency = null)
    {
        return Az::$app->cores->cache->set($name, $value, $type, $dependency);
    }

    public function cacheGet($name, string $type = Cache::type['cache'])
    {
        return Az::$app->cores->cache->get($name, $type);
    }

    public function cacheExists($name, string $type = Cache::type['cache'])
    {
        return Az::$app->cores->cache->exists($name, $type);
    }


    public function cacheDel($name, string $type = Cache::type['cache'])
    {
        return Az::$app->cores->cache->delete($name, $type);
    }

    public function cacheFlush(string $type = Cache::type['cache'])
    {
        return Az::$app->cores->cache->flush($type);
    }

    public function cache(string $name, string $type, callable $callable, $dependency = null)
    {

        return Az::$app->cores->cache->getOrSet($name, $type, $callable, $dependency);
    }

    public function cacheAdd($name, $value = '', string $type = Cache::type['cache'], $dependency = null)
    {
        return Az::$app->cores->cache->add($name, $value, $type, $dependency);
    }


    #endregion

    #region Cache Target

    public function cacheRedis($key, $callable, $dependency = null)
    {
        if (is_array($dependency))
            return $this->cache($key, Cache::type['redis'], $callable, new TagDependency(['tags' => $dependency]));

        return $this->cache($key, Cache::type['redis'], $callable, $dependency);
    }

    public function cacheArray($key, $callable)
    {
        return $this->cache($key, Cache::type['array'], $callable);
    }

    #endregion


    #region HTTP

    public function httpIsPost()
    {
        if ($this->isCLI())
            return null;

        return Az::$app->request->isPost;
    }


    public function httpIsGet()
    {
        if ($this->isCLI())
            return null;


        return Az::$app->request->isGet;
    }


    public function httpIsAjax()
    {

        if ($this->isCLI())
            return null;

        return Az::$app->request->isAjax;
    }


    public function httpPost($name = null, $defaultValue = null, bool $required = false)
    {
        if ($this->isCLI())
            return null;

        $data = Az::$app->request->post($name, $defaultValue);

        return ZFormatter::filter($data);
    }


    public function httpForm($model, $name = null, $defaultValue = null)
    {
        if ($this->isCLI())
            return null;


        if (is_object($model))
            $model = get_class($model);

        $formName = bname($model);
        $value = ZArrayHelper::getValue($this->httpPost(), "{$formName}.{$name}");

        if (empty($value))
            return $defaultValue;

        return $value;
    }


    /**
     *
     * Function  httpGet
     * @param null $name
     * @param null $defaultValue
     * @return  array|mixed
     */
    public function httpGet($name = null, $defaultValue = null, $required = false)
    {
        if ($this->isCLI())
            return null;

        $value = Az::$app->request->get($name, $defaultValue);

        if (!is_array($value))
            switch (true) {
                case $name === 'id':
                case $name === 'amount':
                case ZStringHelper::endsWith($name, 'id', true):
                    $value = (int)$value;
                    break;
            }


        if ($required && empty($value)) {

            $reqData = $this->paramGet(paramRequired);
            if ($reqData)
                $this->paramSet(paramRequired, ZArrayHelper::merge($reqData, $name));

            // $this->required($name);

        }

        return $value;
    }


    public function httpParams($name = null)
    {
        if ($this->isCLI())
            return [];

        if ($name === null)
            return Az::$app->request->queryParams;
        else
            return Az::$app->request->getQueryParam($name);

    }



    #endregion


    #region Chechker


    public function required()
    {


        $name = $this->paramGet(paramRequired);
        $data = Az::l('{name} является обязательным параметров для запроса', [
            'name' => $name
        ]);

        $title = Az::l('Ошибка.');

        //vdd($name);
        /*echo ZKAlertWidget::widget([
             'config' => [
                'type' => ZKAlertWidget::type['danger'],
                'title' => $title,
                'body' => $data
             ]
        ]);*/

        return true;
        //return false;

        //  die;


    }

    public function exception()
    {


        $name = $this->paramGet(paramRequired);
        $data = Az::l('{name} является обязательным параметров для запроса', [
            'name' => $name
        ]);

        $title = Az::l('Ошибка.');

        vdd($name);
        /*echo ZKAlertWidget::widget([
             'config' => [
                'type' => ZKAlertWidget::type['danger'],
                'title' => $title,
                'body' => $data
             ]
        ]);*/

        return true;
        return false;

        //  die;


    }

    public function checked()
    {

        return true;

        $b1 = $this->required();
        $b2 = $this->exception();

        if ($b1 || $b2)
            return false;

        return true;

    }


    #endregion


    #region Session


    /**
     *
     * Function  session
     * @param $key
     * @param null $default
     * @return  mixed|\yii\web\Session
     */
    public function sessionGet($key = null)
    {

        if ($key !== null) {
            return Az::$app->cores->session->get($key);
        }

        return null;
    }

    public function sessionGetAll($key = null)
    {
        if ($key !== null) {
            return Az::$app->cores->session->getAll($key);
        }
        return $key;
    }

    public function sessionSet($key, $value, $time = Auth::duration, $user_id = null)
    {
        if ($key !== null)
            return Az::$app->cores->session->set($key, $value, $time, $user_id);
        return $key;
    }


    public function sessionCookie()
    {
        return Az::$app->cores->session->getCookieSession();
    }

    public function sessionDel($key = null)
    {
        //     $this->cookieDel('session');
        if ($key !== null)
            return Az::$app->cores->session->delete($key);
        return $key;
    }

    public function sessionSetObject($key = null, SessionItem $item, $time = Auth::duration)
    {
        if ($key !== null)
            return Az::$app->cores->session->setObject($key, $item, $time);
        return $key;
    }

    public function sessionGetObject($key = null)
    {
        if ($key !== null)
            return Az::$app->cores->session->getObject($key);
        return $key;
    }

    #endregion

    #region Cookie

    public function cookieGet($key = null, $default = null)
    {
        if (isset($_COOKIE[$key]))
            return $_COOKIE[$key];
        return false;
    }

    public function cookieSet($key, $value, $options = [])
    {
        $toAdd = [
            'name' => $key,
            'value' => $value,
            'domain' => '',
            'expire' => null,
            'path' => '/',
            'secure' => false,
            'httpOnly' => true,
            'sameSite' => null,
        ];

        if (!empty($options))
            $toAdd = array_merge($toAdd, $options);

        setcookie($toAdd['name'], $toAdd['value'], $toAdd['expire'], $toAdd['path'], $toAdd['domain'], $toAdd['secure'], $toAdd['httpOnly']);
    }


    public function cookieDel($key = null)
    {
        switch (true) {
            case $key === null:
                foreach ($_COOKIE as $k => $v) {
                    setcookie($k, $v, time() - 3600, "/", "");
                    unset($_COOKIE[$k]);
                }
                break;

            default:
                setcookie($key, '', time() - 3600);
                unset($_COOKIE[$key]);
        }
    }



    #endregion


    #region ActiveForm


    public function activeBegin($options = null)
    {

        if ($this->paramGet('grape'))
            echo '<!--$form = $this->activeBegin();-->';

        return Az::$app->forms->active->begin($options);
    }


    public function activeEnd()
    {

        Az::$app->forms->active->end();

        if ($this->paramGet('grape'))
            echo '<!--$this->activeEnd();-->';
    }

    #endregion

    #region Ajax


    public function ajaxBegin($options = null)
    {
        return Az::$app->forms->ajaxer->begin($options);
    }


    public function ajaxEnd($options = null)
    {
        Az::$app->forms->ajaxer->end();
    }


    #endregion

    #region Pjax


    /**
     *
     * Function  pjaxBegin
     * @param null $config
     *
     */
    public function pjaxBegin($config = null)
    {
        return Az::$app->forms->zPjax->begin($config);
    }

    public function pjaxEnd()
    {
        return Az::$app->forms->zPjax->end();
    }

    public function pjaxEnable()
    {
        $data = !empty($this->paramGet(paramChangeReloadId));
        return $data;
    }


    #endregion


    #region System

    public function isCLI()
    {
        global $boot;
        return $boot->isCLI();
    }

    #endregion

    #region Utilities
    public function toObject($className, array &$array, $isArray = false)
    {
        return Az::$app->utility->mains->array2object($className, $array, $isArray);
    }

    public function instant($className)
    {
        $instantiator = new Instantiator();
        $new = $instantiator->instantiate($className);
        return $new;
    }
    #endregion

    #region Validation
    public function isEmail($value)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

    public function isPhone($value)
    {
        $value = str_replace(array('+', '-', ' ', '%2B'), '', $value);
        return is_numeric($value);
    }
    #endregion
}
