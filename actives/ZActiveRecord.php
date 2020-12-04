<?php
/**
 *
 * Author:  Asror Zakirov
 * Created: 10.07.2017 15:30
 * https://www.linkedin.com/in/asror-zakirov-167961a9
 */

namespace zetsoft\system\actives;

require Root . '/vendori/thread/reacts/vendor/autoload.php';

use ArrayObject;
use UserAgentParserTest\RenderingEngineTest;
use yii\base\InvalidConfigException;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\validators\Validator;
use zetsoft\dbitem\data\Form;
use zetsoft\dbitem\data\FormDb;
use zetsoft\models\core\CoreSession;
use zetsoft\models\core\CoreTransact;
use zetsoft\models\test\TestTerrabayt;
use zetsoft\service\cores\Cache;
use zetsoft\service\socket\start;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZJsonHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZActiveTrait;
use zetsoft\system\module\Models;
use function False\true;


/**
 * Class    ZActiveRecord
 * @package zetsoft\system\actives
 *
 * @property int $id;
 */
class ZActiveRecord extends ActiveRecord
{

    #region Vars

    use ZCoreTrait;
    use ZActiveTrait;

    public const tranzExclude = [
        CoreSession::class,
        CoreTransact::class,
    ];

    /* @var array $_tranz */
    public array $_tranz = [];
    public array $_attrs = [];

    #endregion


    #region Core

    /**
     *
     * Function  __get
     * @param string $name
     * @return  mixed|null
     *
     * Model Get Values from Attributes
     */

    public function __get($name)
    {


        /**
         *
         * Translations
         */
        $langColumn = $name . '_lang';
        $texts = $this->getAttribute($langColumn);

        switch (true) {
            case empty($this->columns):
                $return = null;
                break;

            case is_array($texts):
                if (!ZArrayHelper::keyExists(Az::$app->language, $texts))
                    return parent::__get($name);

                $lang = Az::$app->language;
                $value = $texts[$lang];

                if (!empty($value))
                    $this->setAttribute($name, $value);

            case ZArrayHelper::isIn($name, $this->columnsList()):
            case empty($texts):
            default:
                $return = parent::__get($name);
                break;

        }

        /**
         *
         * Transaction
         */

        if ($this->isTranz())
            return ZArrayHelper::getValue($this->_tranz, $name, $return);

        return $return;

    }

    public function __set($name, $value)
    {

        if ($this->isTranz())
            return $this->_tranz[$name] = $value;

        return parent::__set($name, $value);
    }


    public function attributes()
    {
        global $boot;

        if ($boot->env('checkTable'))
            if (!$this->dbHasTable($this::tableName()))
                return [];

        return array_keys(static::getTableSchema()->columns);
    }


    public function init()
    {
        parent::init();

        //   vt('!!!!!!');

        $this->paramSet(paramFull, false);
        $this->trait();
        $this->kernel();
    }


    /**
     *
     * Function  save
     * @param bool $runValidation
     * @param null $attributeNames
     * @return  bool
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        /**
         * Main Save
         */
        $this->paramSet($this->paramSecondSave, false);

        if (!$this->isNewRecord)
            $this->paramSet($this->paramIsUpdate, true);


        $this->params();


        /**
         *
         * Before Save ParamFull
         *
         * Hamma column lar va auto=true columnslar ham yaratilishini taminlaydi
         */
        $this->paramSet(paramFull, true);
        $this->columns();


        $dirtyAttributes = $this->dirtyAttributes;
        $saved = $this->saveAll($runValidation, $attributeNames);

        if (!$saved) {
            $this->paramSet($this->paramSecondSave, false);
            if (!$this->paramGet($this->paramNoWarn)) {
                print_r("Cannot save model: {$this::className()}");
                print_r(trace());
                print_r($this->errors);
            }
        }

        if (!empty($dirtyAttributes))
            foreach ($dirtyAttributes as $keyDirt => $valDirt) {
                $column = ZArrayHelper::getValue($this->columns, $keyDirt);
                $event = null;

                if ($column !== null)
                    $event = $column->event;

                if ($event !== null)
                    if (!$this->paramGet(paramMigration))
                        $event($this);
            }


      //  vdd($this->columnsList());

        $this->autoComplete();

        if (!empty($this->dirtyAttributes)) {
            $this->paramSet($this->paramSecondSave, true);
            $this->saveAll();
        }


        /**
         *
         * Key generate
         */

        $key = false;
        if ($this->configs->addID)
            if ($this->id !== null)
                $key = true;

        if ($key)
            $this->keys();


        /**
         *
         * Invalidate
         */

        TagDependency::invalidate(Az::$app->cache, $this::className());


        /**
         *
         * After Save ParamFull.
         * Bu narsa form yaratilganidan keyin editorni ichida bitta attribute emas, balki hammasi chiqib qolmasligi uchun kerak
         */
        $this->paramSet(paramFull, false);
        $this->columns();

        return true;

    }


    public function saveAll($runValidation = true, $attributeNames = null)
    {
        if ($this->isTranz()) {
            $return = $this->tranzSet();

            if ($this->isNewRecord && $this->configs->addTranz) {

                foreach ($this->_tranz as $key => $value)
                    $this->setAttribute($key, $value);

                $this->setAttribute('tranz', true);

                return $this->saveApp($runValidation, $attributeNames);
            }

        } else
            $return = $this->saveApp($runValidation, $attributeNames);

        return $return;
    }


    public function saveApp($runValidation = true, $attributeNames = null)
    {
        return parent::save($runValidation, $attributeNames);
    }



    #endregion

    #region Tranz

    public function isTranz()
    {
        if ($this->paramGet(paramTransact) && !ZArrayHelper::isIn($this->clazz, self::tranzExclude))
            return true;

        return false;
    }


    public function tranzApp()
    {
        if ($this->paramGet(paramTransact)) {
            $trans = CoreTransact::find()
                ->where([
                    'models' => $this->clazz,
                    'session' => Az::$app->cores->session->getCookieSession()
                ])
                ->asArray()
                ->one();

            if ($trans !== null) {

                $value = $trans['value'];
                $value = ZJsonHelper::decode($value);

                unset($value['id']);

                $this->setAttributes($value);

                //  vd($this->region_form);

            }
        }
    }


    public function tranzSet()
    {
        $where = [];
        if (!empty($this->id))
            $where = [
                'modelId' => $this->id,
            ];

        $trans = CoreTransact::find()
            ->where(ZArrayHelper::merge($where, [
                'models' => $this->clazz,
                'session' => Az::$app->cores->session->getCookieSession()
            ]))
            ->one();


        if ($trans !== null) {
            $trans->value = $this->_tranz;
        } else {

            $trans = new CoreTransact();
            $trans->value = $this->_tranz;
            $trans->models = $this->clazz;
            $trans->session = Az::$app->cores->session->getCookieSession();
            if (empty($this->id)) {
                $trans->is_new = true;
            } else {
                $trans->modelId = $this->id;
            }
        }


        $saved = $trans->saveApp();

        return $saved;

    }

    public function tranzGet()
    {
        $where = [];
        if (!empty($this->id))
            $where = [
                'modelId' => $this->id,
            ];

        $trans = CoreTransact::find()
            ->where(ZArrayHelper::merge($where, [
                'models' => $this->clazz,
                'session' => Az::$app->cores->session->getCookieSession()
            ]))
            ->asArray()
            ->one();

        if ($trans !== null) {

            $value = $trans['value'];

            /** @var array $value */
            $value = ZJsonHelper::decode($value);

            unset($value['id']);

            $this->_tranz = $value;
        }


        return false;
    }


    #endregion

    #region Cache

    /**
     *
     * Function  getOne
     * @param  $class
     * @param $condition
     * @return  |null
     *
     */
    public function getOne($class, $condition)
    {

        $return = $this->cache('getOne' . ZVarDumper::export($condition) . $class . $this->id, Cache::type['array'], function () use ($class, $condition) {

            $key = key($condition);
            $attribute = $condition[$key];
            $value = $this->$attribute;

            if (!empty($value))
                return $class::find()
                    ->where([
                        $key => $value
                    ])
                    ->limit(1)
                    ->one();

            return null;
        });

        return $return;
    }

    /**
     *
     * Function  getMany
     * @param string $class
     * @param $condition
     * @return array|mixed|ZActiveQuery[]|null
     * @throws \Exception
     */
    public function getMany($class, $condition)
    {


        $return = $this->cache('getMany' . ZVarDumper::export($condition) . $class . $this->id, Cache::type['array'], function () use ($class, $condition) {

            $key = key($condition);
            $attribute = $condition[$key];
            $value = $this->$attribute;

            if (!empty($value))

                return $class::find()
                    ->where([
                        $key => $value
                    ])
                    ->all();
            return null;
        });

        return $return;
    }


    /**
     *
     * Function  getMulti
     * @param Models $class
     * @param $condition
     * @return  array|mixed|ZActiveQuery[]|null
     * @throws \Exception
     */
    public function getMulti($class, $condition)
    {
        $return = $this->cache('getMulti' . ZVarDumper::export($condition) . $class . $this->id, Cache::type['array'], function () use ($class, $condition) {

            $key = key($condition);
            $attribute = $condition[$key];
            $value = $this->$attribute;

            if (!empty($value))
                /** @var Models $class */
                return $class::find()
                    ->where([
                        $key => $value
                    ])
                    ->all();
            return null;
        });

        return $return;
    }


    #endregion

    #region Relations

    /**
     *
     * Function  find
     * @return object|ZActiveQuery
     */
    public static function find($model = null)
    {

        $object = Az::createObject([
            'class' => ZActiveQuery::class,
            'modelClass' => static::class,
            'model' => $model,
        ], [
            static::class
        ]);

        return $object;

    }


    public static function findAllArray($condition)
    {
        return static::findByCondition($condition)
            ->asArray()
            ->all();
    }
    public static function findOneArray($condition)
    {
        return static::findByCondition($condition)
            ->asArray()
            ->one();
    }


    public static function findAllData($model = null, bool $asArray = false)
    {

        /** @var ZActiveQuery $object */
        $object = Az::createObject([
            'class' => ZActiveQuery::class,
            'modelClass' => get_called_class(),
            'model' => $model,
        ], [
            get_called_class()
        ]);

        $data = $object->all();

        return $data;

    }


    public static function findLast()
    {

        $lastId = Az::$app->forms->modelz->lastID(static::class);

        return static::findByCondition([
            'id' => $lastId
        ])
            ->one();
    }


    public function delete()
    {
        //start @Terrabayt 31.10.2020
//        $manyRelation = $this->configs->hasMany;
//        $this->paramSet(paramNoEvent, true);
//        foreach ($manyRelation as $i => $relation) {
//            $manyClass = $this->bootFull($i);
//            if ($manyClass) {
//                $models = $manyClass::find();
//                foreach ($relation as $j => $column) {
//                    $models = $models->andWhere([$j => $this->$column]);
//                }
//                $models = $models->all();
//                foreach ($models as $model) {
//                    $model->delete();
//                }
//            }
//        }

        $b1 = $this->isAddColumn('Del');
        $b2 = (!$this->configs->showDeleted || $this->bootEnv('enableDeleteFromDb'));

        if ($b1 && $b2) {
            return $this->softDelete();
        }

        if (!parent::delete()) {
            vd("Cannot delete model: {$this::className()}");
        }

        TagDependency::invalidate(Az::$app->cache, $this::className());

        return true;
    }

    public function deleteParent()
    {

        if (!parent::delete()) {
            vd("Cannot delete model: {$this::className()}");
        }

        TagDependency::invalidate(Az::$app->cache, $this::className());

        return true;
    }


    public function softDelete()
    {

        $this->deleted_by = $this->userIdentity()->id;
        $this->deleted_at = Az::$app->cores->date->dateTime();
        $this->configs->rules = validatorSafe;
        $this->save();

        return true;

    }

    #endregion

    #region Methods

    public function validate($attributeNames = null, $clearErrors = true)
    {

        //   Nimaga bu qo'yilgan? -> Javobni Asror Zakirovga et!
        //     $this->columns();

        /**
         *
         * Auto Generate
         */
        //     $this->autoComplete();
        return parent::validate($attributeNames, $clearErrors);
    }


    public function autoComplete()
    {
        $res = false;
        if ($this->paramGet(paramInsertCreate))
            return false;

        foreach ($this->columns as $key => $column)
            if (!in_array($key, columnAuto))
                $res = $this->columnAuto($column, $key);

        foreach (columnAuto as $key)
            if (in_array($key, static::attrs))
                $res = $this->columnAuto($this->columns[$key], $key);

        return $res;
    }


    public function columnAuto($column, $key)
    {
        $res = false;

        if ($column->auto)
            if ($column->autoValue !== null)
                if (!$column->autoWhenEmpty) {
                    $this->autoValue($column, $key);
                    $res = true;
                } else
                    if (empty($this->$key)) {
                        $this->autoValue($column, $key);
                        $res = true;
                    }

        return $res;
    }

    /**
     *
     * Function  autoValue
     * @param Form $column
     * @param $key
     */

    public function autoValue($column, $key)
    {

        $auto = $column->autoValue;

        if (is_callable($auto))
            $this->$key = $auto($this);
        else {
            $list = $this->columnsList();
            $replace = [];
            foreach ($list as $item) {
                //    '{name} - {user_ids} / {user_ids}';
                $replace['{' . $item . '}'] = $this->$item;
            }
            $this->$key = strtr($auto, $replace);
        }

        return true;

    }

    public function keys()
    {
        $table = $this::tableName();
        $sequence = $table . '_id_seq';

        $sql = <<<SQL
    SELECT setval('{$sequence}'::regclass, (SELECT MAX("id") FROM "public"."{$table}")); 
SQL;

        Az::$app->smart->migra->exec($sql);

    }
    #endregion

    #region Saves

    public function restore()
    {
        $this->deleted_at = null;
        $this->deleted_by = null;
        $this->save();
    }


    private function eventSkip()
    {

        $b1 = $this->events === null;
        $b2 = $this->paramGet(paramNoEvent);
        if ($b1 || $b2)
            return true;

        return false;
    }


    private function useCache()
    {

        global $boot, $cacheIgnoreMain;

        //!Az::$app->db->enableQueryCache
        if (!$boot->env('enableQueryCache')) {
            Az::debug("DB_Cache | QueryCache Is Disabled | {$this::className()}");
            return false;
        }

        /**
         *
         * Checking for cache
         */
        $cacheIgnore = ZArrayHelper::merge($cacheIgnoreMain, cacheIgnore);
        $cacheModel = !ArrayHelper::isIn($this->modelClass, $cacheIgnore);

        $dbCache = $boot->env('dbCache', false);

        if ($cacheModel && $dbCache)
            return Az::debug("DB_Cache | TagDependency::invalidate | ON | {$this::className()}");


        Az::debug("DB_Cache TagDependency::invalidate | OFF | {$this::className()}");
        return false;

    }



    #endregion

    #region Events


    /**
     * check model columns type
     * if column type = jsonb
     * try to find files and delete them
     * @return bool
     */
    public function beforeDelete()
    {
        /**
         * Skipping
         */
        if ($this->eventSkip())
            return parent::beforeDelete();

        if (!$this->configs->beforeDelete)
            return parent::beforeDelete();

        $function = $this->events->beforeDelete;

        if ($function !== null)
            $function($this);

        Az::$app->forms->modelz->removeFile($this);

        return parent::beforeDelete();
    }


    public function afterDelete()
    {
        parent::afterDelete();

        /**
         *
         * Skipping
         */

        if ($this->eventSkip())
            return null;

        if (!$this->configs->afterDelete)
            return null;


        $function = $this->events->afterDelete;

        if ($function !== null)
            $function($this);


    }


    public function beforeSave($insert)
    {


        /**
         *
         * Skipping
         */

        if (!$this->configs->beforeSave)
            return parent::beforeSave($insert);

        if ($this->eventSkip())
            return parent::beforeSave($insert);

        if ($this->paramGet($this->paramSecondSave) === true)
            return parent::beforeSave($insert);
        /**
         *
         * Slug History
         */
        foreach ($this->columns as $key => $column) {

            /**
             * it will generate value for column ending with _slug. Name column value will be given for this column
             */
            $this->slug($column, $key);

            if ($column->history) {
                $this->history($column, $key);
            }
        }

        $function = $this->events->beforeSave;


        if ($this->isAddColumn('By')) {
            $this->modified_at = Az::$app->cores->date->dateTime();
            $this->modified_by = $this->userIdentity()->id;
            if ($this->isNewRecord) {

                //if (empty($this->created_by))
                $this->created_by = $this->userIdentity()->id;

                //if ($this->created_at)
                $this->created_at = Az::$app->cores->date->dateTime();
            }
        }

        if ($function !== null && is_bool($function($this)))
            return $function($this);

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        /**
         * Skipping
         */
        if ($this->eventSkip())
            return null;

        if (!$this->configs->afterSave)
            return null;

        if ($this->paramGet($this->paramSecondSave))
            return parent::afterSave($insert, $changedAttributes);


        $function = $this->events->afterSave;

        if ($function !== null)
            $function($this);


        parent::afterSave($insert, $changedAttributes);

    }

    public function beforeValidate()
    {
        /**
         * Skipping
         */
        if ($this->eventSkip())
            return parent::beforeValidate();

        if (!$this->configs->beforeValidate)
            return parent::beforeValidate();

        $function = $this->events->beforeValidate;

        if ($function !== null)
            $function($this);

        return parent::beforeValidate();
    }

    public function afterValidate()
    {

        parent::afterValidate();

        /**
         * Skipping
         */
        if ($this->eventSkip())
            return null;

        if (!$this->configs->afterValidate)
            return null;

        $function = $this->events->afterValidate;

        if ($function !== null)
            $function($this);


    }


#endregion


    #region event utilities

    public function afterFind()
    {

        parent::afterFind();

        if ($this->isTranz())
            $this->tranzGet();

        if ($this->eventSkip())
            return null;

        $function = $this->events->afterFind;

        if ($function !== null)
            $function($this);

    }

    public function afterRefresh()
    {

        parent::afterRefresh();

        if ($this->eventSkip())
            return null;

        $function = $this->events->afterRefresh;

        if ($function !== null)
            $function($this);

    }


    #endregion


    #region Utilities


    public function slug($column, $key)
    {
        if (ZStringHelper::find($key, '_slug')) {
            $this->$key = ZStringHelper::slug($column->name);
        }
    }


    public function history($column, $key)
    {

        if ($this->isNewRecord)
            return null;


        /**
         *
         * Interval
         */

        $date = $interval = Az::$app->cores->date->dateTime();
        if ($this->configs->historyInterval !== null)
            $interval = Az::$app->cores->date->dateTime('-' . $this->configs->historyInterval);


        /*
          if (!$diff->expired)
              return null;*/

        $attr = $key . '_history';

        if ($this->$key === ZArrayHelper::getValue($this->oldAttributes, $key))
            return null;

        $history = $this->$attr;

        if (empty($history)) {

            $name = $this->getOldAttribute($key);

            $this->$attr = [[
                'name' => $name,
                'user_id' => $this->userIdentity()->id,
                'date' => $date,
            ]];

            return null;
        }

        $last_date = end($history);

        if ($last_date['date'] > $interval)
            return null;

        $history[] = [
            'name' => $this->getOldAttribute($key),
            'user_id' => $this->userIdentity()->id,
            'date' => $date,
        ];


        $this->$attr = $history;

    }


#endregion


}
