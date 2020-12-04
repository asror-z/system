<?php

namespace zetsoft\system\actives;


/**
 *
 * Author:  Asror Zakirov
 * Created: 10.07.2017 15:30
 * https://www.linkedin.com/in/asror-zakirov-167961a9
 */


use yii\caching\TagDependency;
use yii\db\ActiveQuery;
use yii\db\ActiveQuery as ActiveQueryAlias;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use zetsoft\models\dyna\DynaChess;
use zetsoft\models\lang\Lang;
use zetsoft\models\shop\ShopCategory;
use zetsoft\models\shop\ShopOffer;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\user\User;
use zetsoft\service\smart\Insert;
use zetsoft\service\socket\start;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZJsonHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\module\Models;
use function False\true;

/**
 * CachedActiveQuery represents a ActiveQuery with cache.
 */
class ZActiveQuery extends ActiveQueryAlias
{

    /**
     * @var Models $model
     */
    public $model;


    /**
     * @var string $modelClass
     */
    public $modelClass;


    /**
     * @var bool
     *
     * Turn on kernel Configs & Columns processing for models
     */
    public $kernel = false;

    public bool $transform = true;


    #region Jsonb

    public static function whereJsonInTest()
    {
//        $data = User::find()->whereJsonIn('oauth', ['login' => 'Shakhrizod','login1' => 'Shakhrizod2',])
//            ->andWhere(['oauth_type' => 'github'])
//            ->one(); [ 1, 2, 3];
        // [1,2,3,4,5]
//        $data = DynaChess::find()->whereJsonIn('rols', ['1','2','3'])->all();
//        vdd($data);
    }

    /**
     * Function  whereJsonIn
     * @param string $column
     * @param $value
     * @return  bool|ZActiveQuery
     * @author Shakhrizod Nurmukhammadov
     * @example whereJsonIn('oauth', ['login' => 'Shakhrizod'])
     *          whereJsonIn('rols', 'admin')
     */
    public function whereJsonIn(string $column, $value, $array = false)
    {
        $data = false;
        switch (true) {
            case is_array($value) && $array:
                $val = implode(',', $value);
                $data = $this->andWhere("jsonb_exists_any($column, ARRAY['$val'])");
                break;

            default:
                $value = ZVarDumper::search($value);
//                if (ZStringHelper::endsWith($column, '_ids'))
//                    $value = strtr($value, [
//                        '\'"' => '\'',
//                        '"\'' => '\''
//                    ]);

                $data = $this->andWhere("$column @> $value");
        }

        return $data;
    }

    /**
     *
     * Function  whereNotIn
     * Запрос для запросов, где значение не содержится в массиве
     * @param string $attribute аттрибут
     * @param $array массив exclude
     * @return  ZActiveQuery
     */

    //start|DavlatovRavshan|2020.10.11
    public function andWhereNotIn(string $attribute, array $array)
    {
        return $this->andWhere(['not in', $attribute, $array]);
    }

    public function whereNotIn(string $attribute, array $array)
    {
        return $this->where(['not in', $attribute, $array]);
    }
    //end|DavlatovRavshan|2020.10.11
    /**
     * User::find()->where("oauth @> '{\"login\": \"$login\"}'")
     * ->andWhere(['oauth_type' => $service])
     * ->one();
     */
    #endregion


    #region Queries

    public static function allTest()
    {
        $models = ShopCategory::find()
            ->asArray()
            ->all();

        vd($models);

    }

    /**
     * When query caching is available return caching,
     * If not,Executes query and generates caching then returns all results as an array.
     * @param Connection $db the DB connection used to create the DB command.
     * If null, the DB connection returned by [[modelClass]] will be used.
     * @return array|ZActiveQuery[] the query results. If the query results in nothing, an empty array will be returned.
     * @throws \Exception
     */
    public function all($db = null)
    {

        $object = new $this->modelClass();

        /*Az::$app->utility->mains->paramSet(paramFull, true);*/

        if (!empty($this->model->configs))
            $object = $this->model;

        if ($object->configs->addDel)
            if (!Az::$app->cores->rbac->paramGets([paramInsertCreate, paramNorms]))
                if ($object->configs->showDeleted)
                    $this->andWhere([
                        'not', ['deleted_at' => null]
                    ]);
                else
                    $this->andWhere([
                       'deleted_at' => null
                    ]);


        if ($object->configs->addActive)
            if ($object->configs->showActive)
                $this->andWhere([
                    'active' => true
                ]);

        if ($object->configs->addAccept)
            if ($object->configs->showAccept)
                $this->andWhere([
                    'accept' => true
                ]);


        /**
         *
         * Transaction
         */

        if ($object->configs->addTranz)
            if (!Az::$app->cores->rbac->paramGets([paramInsertCreate, paramNorms]))
                if (Az::$app->cores->langs->paramGet(paramTransact))
                    $this->andWhere([
                        'tranz' => 1
                    ]);
                else
                    $this->andWhere([
                        'tranz' => null
                    ]);


        /** @var ZActiveRecord $model */
        if (!$this->useCache(__FUNCTION__))
            $models = parent::all($db);
        else
            $models = Az::$app->getDb()->cache(function (Connection $db) {
                return parent::all($db);
            }, 0, new TagDependency(['tags' => $this->modelClass]));

        $return = $models;

        foreach ($models as $key => $model) {
            if (!empty($model)) {
                if (is_array($model)) {
                    foreach ($model as $i => $item) {
                        if (ZArrayHelper::keyExists($i, $object->columns)) {
                            if ($object->columns[$i]->dbType === dbTypeJsonb || $object->columns[$i]->dbType === dbTypeJson) {
                                if ($item !== null || $item !== '')
                                    $model[$i] = ZJsonHelper::decode($item);
                            }
                        }
                    }
                } else {
                    if (ZArrayHelper::keyExists($key, $object->columns)) {
                        if ($object->columns[$key]->dbType === dbTypeJsonb || $object->columns[$key]->dbType === dbTypeJson) {
                            if ($model !== null || $model !== '')
                                $model[$key] = ZJsonHelper::decode($model);
                        }
                    }
                }
            }
            $return[$key] = $model;
        }
        if (!$this->kernel)
            return $return;

        $return = [];

        foreach ($models as $key => $model) {
            if (!empty($model)) {
                if (!is_array($model)) {
                    $model->kernel();
                }
            }
            $return[$key] = $model;
        }

        return $return;

    }

    /**
     *
     * Function  whereBetween
     * @param $column
     * @param $start
     * @param $end
     * @return  ZActiveQuery
     *
     * @example User::find()->whereBetween('created_at','2020-09-15 12:16:45', '2020-09-19 09:06:07')->count();
     */
    public function whereBetween($column, $start, $end)
    {
        return $this->where(['between', $column, $start, $end]);
    }

    /**
     * When query caching is available return caching,
     * If not,Executes query and generates caching then returns all results .
     * @param Connection $db the DB connection used to create the DB command.
     * If null, the DB connection returned by [[modelClass]] will be used.
     * @return ZActiveQuery|array|null a single row of query result. Depending on the setting of [[asArray]],
     * the query result may be either an array or an CachedActiveQuery object. Null will be returned
     * if the query results in nothing.
     * @throws \Exception
     */
    public function one($db = null)
    {

        $model = new $this->modelClass();
        if (!empty($this->model->configs))
            $model = $this->model;

        if ($model->configs->addDel) {
            if (!$model->configs->showDeleted) {
                $this->andWhere([
                    'deleted_at' => null
                ]);
            } else {
                $this->andWhere([
                    'not', ['deleted_at' => null]
                ]);
            }
        }

        if (!$this->useCache(__FUNCTION__))
            $model = parent::one($db);
        else
            $model = Az::$app->getDb()->cache(function (Connection $db) {
                return parent::one($db);
            }, 0, new TagDependency(['tags' => $this->modelClass]));

        if (!$this->kernel)
            return $model;

        if (!empty($model))
            if (!is_array($model))
                $model->kernel();

        return $model;
    }

    #endregion


    #region Utilities


    protected function createModels($rows)
    {
        if ($this->asArray) {
            return $rows;
        }

        $clonable = false;
        if (is_object($this->model))
            $clonable = true;

        $models = [];
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        foreach ($rows as $row) {

            /** @var Models $model */

            if ($clonable)
                $model = clone $this->model;
            else
                $model = $class::instantiate($row);

            if ($this->kernel)
                $model->kernel();

            $modelClass = get_class($model);
            $modelClass::populateRecord($model, $row);
            $models[] = $model;
        }
        return $models;
    }


    private function useCache(string $func)
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

        /** @var ZActiveQuery $this */
        $cacheMethod = cacheConfig[$func];

        $dbCache = $boot->env('dbCache', false);

        if ($cacheModel && $cacheMethod && $dbCache) {
            Az::debug("DB_Cache | ZActiveQuery | ON | {$this->modelClass}");
            return true;
        }

        Az::debug("DB_Cache | ZActiveQuery | OFF | {$this->modelClass} | Method = {$func}");
        return false;
    }

    #endregion


    #region SQL


    public function sqlFromQ(Query $Q): string
    {
        return $Q->prepare(Az::$app->db->queryBuilder)->createCommand()->rawSql;
    }

    public function sql(): string
    {
        return $this->prepare(Az::$app->db->queryBuilder)->createCommand()->rawSql;
    }


    #endregion

    #region Service


    /**
     * Returns the query result as a scalar value.
     * The value returned will be the first column in the first row of the query results.
     * @param Connection $db the database connection used to generate the SQL statement.
     * If this parameter is not given, the `db` application component will be used.
     * @return string|boolean the value of the first column in the first row of the query result.
     * False is returned if the query result is empty.
     */
    public function scalar($db = null)
    {
        if (!$this->useCache(__FUNCTION__))
            return parent::scalar($db);

        return Az::$app->getDb()->cache(function (Connection $db) {
            return parent::scalar($db);
        }, 0, new TagDependency(['tags' => $this->modelClass]));
    }

    /**
     * Returns the number of records.
     * @param string $q the COUNT expression. Defaults to '*'.
     * Make sure you properly [quote](guide:db-dao#quoting-table-and-column-names) column names in the expression.
     * @param Connection $db the database connection used to generate the SQL statement.
     * If this parameter is not given (or null), the `db` application component will be used.
     * @return integer|string number of records. The result may be a string depending on the
     * underlying database engine and to support integer values higher than a 32bit PHP integer can handle.
     */
    public function count($q = '*', $db = null)
    {

        $model = new $this->modelClass();

        if (!empty($this->model->configs))
            $model = $this->model;
        //todo:start Daho
        if ($model->configs->addDel) {
            if (!$model->configs->showDeleted) {
                $this->andWhere([
                    'deleted_at' => null
                ]);
            } else {
                $this->andWhere([
                    'not', ['deleted_at' => null]
                ]);
            }
        }
        //todo:end

        if (!$this->useCache(__FUNCTION__))
            return parent::count($q, $db);


        return Az::$app->getDb()->cache(function (Connection $db) use ($q) {
            return parent::count($q, $db);
        }, 0, new TagDependency(['tags' => $this->modelClass]));
    }

    /**
     * Returns the sum of the specified column values.
     * @param string $q the column name or expression.
     * Make sure you properly [quote](guide:db-dao#quoting-table-and-column-names) column names in the expression.
     * @param Connection $db the database connection used to generate the SQL statement.
     * If this parameter is not given, the `db` application component will be used.
     * @return mixed the sum of the specified column values.
     */

    public function sum($q, $db = null)
    {

        if (!$this->useCache(__FUNCTION__))
            return parent::sum($q, $db);

        return Az::$app->getDb()->cache(function (Connection $db) use ($q) {
            return parent::sum($q, $db);
        }, 0, new TagDependency(['tags' => $this->modelClass]));
    }

    /**
     * Returns the average of the specified column values.
     * @param string $q the column name or expression.
     * Make sure you properly [quote](guide:db-dao#quoting-table-and-column-names) column names in the expression.
     * @param Connection $db the database connection used to generate the SQL statement.
     * If this parameter is not given, the `db` application component will be used.
     * @return mixed the average of the specified column values.
     */
    public function average($q, $db = null)
    {

        if (!$this->useCache(__FUNCTION__))
            return parent::average($q, $db);

        return Az::$app->getDb()->cache(function (Connection $db) use ($q) {
            return parent::average($q, $db);
        }, 0, new TagDependency(['tags' => $this->modelClass]));
    }

    /**
     * Returns the minimum of the specified column values.
     * @param string $q the column name or expression.
     * Make sure you properly [quote](guide:db-dao#quoting-table-and-column-names) column names in the expression.
     * @param Connection $db the database connection used to generate the SQL statement.
     * If this parameter is not given, the `db` application component will be used.
     * @return mixed the minimum of the specified column values.
     * @throws \Throwable
     */
    public function min($q, $db = null)
    {
        if (!$this->useCache(__FUNCTION__))
            return parent::min($q, $db);


        return Az::$app->getDb()->cache(function (Connection $db) use ($q) {
            return parent::min($q, $db);
        }, 0, new TagDependency(['tags' => $this->modelClass]));
    }

    /**
     * Returns the maximum of the specified column values.
     * @param string $q the column name or expression.
     * Make sure you properly [quote](guide:db-dao#quoting-table-and-column-names) column names in the expression.
     * @param Connection $db the database connection used to generate the SQL statement.
     * If this parameter is not given, the `db` application component will be used.
     * @return mixed the maximum of the specified column values.
     */
    public function max($q, $db = null)
    {
        if (!$this->useCache(__FUNCTION__))
            return parent::max($q, $db);

        return Az::$app->getDb()->cache(function (Connection $db) use ($q) {
            return parent::max($q, $db);
        }, 0, new TagDependency(['tags' => $this->modelClass]));
    }

    /**
     * Returns a value indicating whether the query result contains any row of data.
     * @param Connection $db the database connection used to generate the SQL statement.
     * If this parameter is not given, the `db` application component will be used.
     * @return boolean whether the query result contains any row of data.
     */
    public function exists($db = null)
    {
        if (!$this->useCache(__FUNCTION__))
            return parent::exists($db);
        return Az::$app->getDb()->cache(function (Connection $db) {
            return parent::exists($db);
        }, 0, new TagDependency(['tags' => $this->modelClass]));
    }

    #endregion

    #region Joins

    /**
     *
     * https://www.w3schools.com/sql/sql_join.asp
     *
     * (INNER) JOIN: Returns records that have matching values in both tables
     * LEFT (OUTER) JOIN: Returns all records from the left table, and the matched records from the right table
     * RIGHT (OUTER) JOIN: Returns all records from the right table, and the matched records from the left table
     * FULL (OUTER) JOIN: Returns all records when there is a match in either left or right table
     */
    public const joinType = [
        'left' => 'LEFT JOIN',
        'left_outer' => 'LEFT OUTER JOIN',
        'right' => 'RIGHT JOIN',
        'right_outer' => 'RIGHT OUTER JOIN',
        'full' => 'FULL JOIN',
        'full_outer' => 'FULL OUTER JOIN',
    ];

    /**
     * Old version using joinsWith() query ShopOrderItem::find()->joinsWith('ShopCatalog')->one();
     * You had to give proper function name as argument so we have changed it to className
     *
     * Below given new version of joinWith() query
     *
     * @param $with ClassName of model
     * @param bool $eagerLoading
     * @param string $joinType
     * @return ZActiveQuery
     */

    public function joinsWith($with, $eagerLoading = true, $joinType = self::joinType['left'])
    {
        $with = lcfirst(bname($with));

        return parent::joinWith($with, $eagerLoading, $joinType);

    }

    /**
     * Old version using join() query: ShopOrderItem::find()->join('INNER JOIN', 'shop_order', 'shop_order.id = core_order_item.shop_order_id')->all();
     *
     * Below given new version of join() query
     *
     * @param $type
     * @param $modelClass
     * @param string $on
     * @param array $params
     * @return ZActiveQuery
     */

    public function joinsAll($type, $modelClass, $params = [])
    {
        /** @var Models $modelClass */
        $join_table = $modelClass::tableName();
        $class = $this->modelClass;
        $current_table = $class::tableName();

        $on = "$join_table.id = $current_table.{$join_table}_id";

        $this->join[] = [$type, $join_table, $on];
        return $this->addParams($params);
    }

    /**
     * You can use multiple joins at once
     * Function  joinLeft
     * @param $modelClass
     * @return ZActiveQuery
     */

    public function joinsLeft($modelClass)
    {
        /** @var Models $modelClass */
        $join_table = $modelClass::tableName();

        $class = $this->modelClass;
        $current_table = $class::tableName();
        return $this->join('LEFT JOIN', $join_table, "$join_table.id = $current_table.{$join_table}_id");
    }

    /**
     * You can use multiple joins at once
     * Function  joinLeft
     * @param $modelClass
     * @return ZActiveQuery
     */
    public function joinsInner($modelClass)
    {
        /** @var Models $modelClass */
        $join_table = $modelClass::tableName();

        $class = $this->modelClass;
        $current_table = $class::tableName();
        return $this->join('INNER JOIN', $join_table, "$join_table.id = $current_table.{$join_table}_id");
    }

    /**
     * You can use multiple joins at once
     * Function  joinLeft
     * @param $modelClass
     * @return ZActiveQuery
     */
    public function joinsRight($modelClass)
    {
        /** @var Models $modelClass */
        $join_table = $modelClass::tableName();

        $class = $this->modelClass;
        $current_table = $class::tableName();
        return $this->join('Right JOIN', $join_table, "$join_table.id = $current_table.{$join_table}_id");
    }

    #endregion


}
