<?php

namespace zetsoft\system\schema;

use Yii;
use yii\db\ColumnSchemaBuilder;
use yii\db\TableSchema;

/**
 * Schema is the improved class for retrieving metadata from a PostgreSQL database
 * (version 9.x and above).
 *
 * @author Sergei Tigrov <rrr-r@ya.ru>
 */
class MySqlSchema extends \yii\db\mysql\Schema
{




    public function loadTableIndexes($tableName)
    {
        return parent::loadTableIndexes($tableName);
    }
    /**
     * @inheritdoc
     *
     * @return QueryBuilder query builder instance
     */
    public function createQueryBuilder()
    {
        return new QueryBuilder($this->db);
    }


}
