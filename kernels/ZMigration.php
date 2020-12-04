<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    22.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\kernels;

use yii\db\ColumnSchemaBuilder;
use yii\db\Exception;
use yii\db\PdoValue;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZArrayHelper;
use yii\db\IndexConstraint;
use yii\db\Migration;
use yii\db\TableSchema;
use zetsoft\system\helpers\ZCollect;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZJsonHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\schema\PgSqlSchema;
use function foo\func;


/**
 * Class    ZMigration
 * @package zetsoft\system\kernels
 *
 * @property boolean $isGenModel
 * @property boolean $isGenCrud
 * @property string $genName
 * @property string $genTitle
 * @property array $data
 *
 */
class ZMigration extends Migration
{


    /**
     *
     * Constants
     */

    public const ON_RESTRICT = 'RESTRICT';
    public const ON_CASCADE = 'CASCADE';
    public const ON_NOACTION = 'NO ACTION';
    public const ON_SETDEFAULT = 'SET DEFAULT';
    public const ON_SETNULL = 'SET NULL';

    public $onMode = self::ON_SETNULL;


    private $coreIDs = [];
    private $coreBys = [];


    public $comments;


    public $table;
    public $class;
    /* @var TableSchema[] $tables */
    public $tables;
    public $tableTypes;
    public $tableKeys;


    /**
     *
     * Scheme
     */
    public $public = true;

    /** @var PgSqlSchema $schema */
    public $schema;

    use ZCoreTrait;

    public $data;

    #region Core

    public function init()
    {
        parent::init();
        $this->trait();

        /**
         *
         * DB Scheme
         */
        $this->schema = $this->db->schema;

        $tables = $this->schema->getTableSchemas();
        $this->tables = ZArrayHelper::index($tables, function (TableSchema $element) {
            return $element->name;
        });
        $this->tableKeys = array_keys($this->tables);


        foreach ($this->tables as $tables) {

            $aColumnALL = $tables->columns;
            $typeData = [];

            foreach ($aColumnALL as $columns) {
                $typeData[$columns->name] = $columns->type;
            }

            $this->tableTypes[$tables->name] = $typeData;
        }


        /**
         *
         * Make Table Name
         */

        $this->table = ZInflector::underscore($this->class);

    }


    #endregion

    #region Helper

    /**
     *
     *
     * Helper Methods
     */

    public function timestamptz($precision = null)
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(PgSqlSchema::TYPE_TIMESTAMPTZ, $precision);
    }

    public function timestamp($precision = null)
    {
        return parent::timestamp();
    }


    public function jsonb()
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(PgSqlSchema::TYPE_JSONB);
    }


    #endregion

    #region Table


    public function tableExists(string $table)
    {
        return ZArrayHelper::isIn($table, $this->tableKeys);
    }

    /**
     *
     * Function  addTable
     * @param array $columns
     * @return bool|void
     */
    public function tableAdd(array $columns)
    {
        $table = $this->table;

        if (!$this->tableExists($table)) {
            $this->createTable("{{%{$table}}}", $columns);
            return Az::debug("Creating Table {$table}");
        }

        return Az::debug("Table {$table} Exists");
    }


    public function tableRemove()
    {
        if ($this->tableExists($this->table)) {
            Az::debug("Dropping Table {$this->table}");
            try {
                $this->dropTable("{{%{$this->table}}}");
            } catch (\Exception $e) {
                Az::warning($e->getMessage(), 'Exception in dropTable');
            }
        } else
            Az::debug("Table {$this->table} Not Exists");
    }


    #endregion

    #region FKey


    public function keyFAdd(string $column, string $refTableArg, string $refColumn = 'id')
    {
        $name = "fk_{$this->table}_{$column}";
        Az::debug($name, 'Adding Foreign key');

        $sRefTableSample = $this->public ? '"public"."{table}"' : '"{table}"';

        $sRefTable = strtr($sRefTableSample, [
            '{table}' => $refTableArg
        ]);

        $this->addForeignKey($name, "{{%{$this->table}}}", $column, $sRefTable, $refColumn, $this->onMode, $this->onMode);
    }

    public function keyPAdd(string $column)
    {
        $name = "pk_{$this->table}_{$column}";
        Az::debug($name, 'Adding Primary key');

        $this->addPrimaryKey($name, "{{%{$this->table}}}", $column);
    }


    public function keyRemove()
    {
        /** @var TableSchema $table */

        $table = $this->tables[$this->table];

        $aFK = $table->foreignKeys;

        foreach ($aFK as $key => $item) {
            Az::debug($key, 'Dropping Foreign key');
            try {
                $this->dropForeignKey($key, $table->name);
            } catch (\Exception $e) {
                Az::warning($e->getMessage(), 'Exception in dropForeignKey');
            }
        }

        return true;
    }


    #endregion


    public function haveIndex($column, $unique)
    {

        if (!$unique) {
            Az::debug("$this->table-$column", 'Adding Index');
            $name = "{$this->table}-{$column}-idx";
        } else {
            Az::debug("$this->table-$column", 'Adding Unique Index');
            $name = "{$this->table}-{$column}-idx-unique";
        }


        //  $this->dropIndex($name, "{{%{$this->table}}}");
        $this->hasIndex($name, "{{%{$this->table}}}", $column, $unique);

    }


    #region Index

    public function indexAdd($column, bool $unique = false)
    {


        if (!$unique) {
            Az::debug("$this->table-$column", 'Adding Index');
            $name = "{$this->table}-{$column}-idx";
        } else {
            Az::debug("$this->table-$column", 'Adding Unique Index');
            $name = "{$this->table}-{$column}-idx-unique";
        }

        $this->createIndex($name, "{{%{$this->table}}}", $column, $unique);

    }

    public function indexRemoveAll()
    {

        /** @var TableSchema $table */

        if (!ZArrayHelper::keyExists($this->table, $this->tables))
            return Az::debug($this->table, 'Table Not Exists for RemoveIndex');

        $table = $this->tables[$this->table];

        /** @var IndexConstraint[] $indexes */
        $indexes = $this->schema->loadTableIndexes($table->name);

        foreach ($indexes as $index)
            $this->indexRemove($index, $table);

        return true;

    }


    public function indexList($tableName)
    {
        /** @var IndexConstraint[] $indexes */
        $indexes = $this->schema->loadTableIndexes($tableName);
        $collect = zcollect($indexes);

        /** @var ZCollect $icollect */
        $icollect = $collect->map(function ($item, $key) {
            return $item->name;
        });
        return $icollect->toArray();
    }


    public function indexExists($name, $tableName)
    {
        $data = $this->indexList($tableName);
        if (ZArrayHelper::keyExists($name, $data))
            return true;

        return false;
    }


    public final function indexRemove(IndexConstraint $index, TableSchema $table)
    {
        if ($index->isPrimary)
            return Az::debug("Cannot Drop Primary Key $index->name from $table->name");

        Az::debug("Drop Index $index->name from $table->name");

        try {
            return $this->dropIndex($index->name, $table->name);
        } catch (\Exception $e) {
            return Az::warning($e->getMessage(), 'Exception in dropIndex');
        }


    }


#endregion Index

}
