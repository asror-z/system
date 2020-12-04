<?php
/**
 * @link https://github.com/tigrov/yii2-pgsql
 * @author Sergei Tigrov <rrr-r@ya.ru>
 */

namespace zetsoft\system\schema;

use yii\db\Expression;
use zetsoft\system\schema\ArrayExpression;
use zetsoft\system\schema\CompositeExpression;
use zetsoft\system\schema\CompositeExpressionBuilder;
use zetsoft\system\schema\ArrayExpressionBuilder;

/**
 * QueryBuilder is the improved query builder for PostgreSQL databases.
 *
 * @author Sergei Tigrov <rrr-r@ya.ru>
 */
class QueryBuilder extends \yii\db\pgsql\QueryBuilder
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->typeMap[PgSqlSchema::TYPE_BIT] = 'bit';

        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultExpressionBuilders()
    {
        return array_merge(parent::defaultExpressionBuilders(), [
            ArrayExpression::class => ArrayExpressionBuilder::class,
            CompositeExpression::class => CompositeExpressionBuilder::class,
        ]);
    }
}
