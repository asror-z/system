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

namespace zetsoft\system\actives;


use PDO;
use yii\db\Connection;
use yii\db\mysql\Schema;
use zetsoft\system\Az;
use zetsoft\system\schema\MySqlSchema;
use zetsoft\system\schema\PgSqlSchema;

class ZConnection extends Connection
{

    public const Db_PgSQL = 1;
    public const Db_MySQL = 2;
    public const Db_SQLite = 3;

    public $dbType = self::Db_PgSQL;


    public $schemaMap = [
        'mysql' => MySqlSchema::class,
        'pgsql' => PgSqlSchema::class,
        'sqlite' => \yii\db\sqlite\Schema::class,
    ];

    public const Port = [
        self::Db_PgSQL => '5432',
        self::Db_MySQL => '3306',
        self::Db_SQLite => '5432',
    ];

    public const Driver = [
        self::Db_PgSQL => 'pgsql',
        self::Db_MySQL => 'mysql',
        self::Db_SQLite => 'sqlite',
    ];


    public $dbHost;
    public $dbPort;
    public $dbName;
    public $dbDriver;

    public $timeZone;


    public $attributes = [
        PDO::ATTR_TIMEOUT => 5 * 60
    ];
    public $charset = 'utf8';
    public $tablePrefix = '';

    public $emulatePrepare = null;
    public $enableSavepoint = true;
    public $pdo = null;
    public $pdoClass = null;

    public $enableSlaves = false;

    public $masterConfig = [];
    public $masters = [];
    public $slaveConfig = [];
    public $slaves = [];
    public $shuffleMasters = true;


    public $queryCacheDuration = 0;
    public $schemaCacheDuration = 0;

    public $schemaCacheExclude = [];
    public $serverRetryInterval = 10 * 60;


    public function init()
    {
        global $boot;

        $this->dbDriver = self::Driver[$this->dbType];

        if (empty($this->dbPort))
            $this->dbPort = self::Port[$this->dbType];
        /*  'pgsql:host=10.10.3.207;dbname=library;port=5432'*/


        if (empty($this->dsn))
            $this->dsn = "{$this->dbDriver}:host={$this->dbHost};dbname={$this->dbName};port={$this->dbPort}";

        /*if (!empty($this->timeZone))
            $this->dsn .= ";serverTimezone={$this->timeZone}";*/
        $this->queryCache = $boot->env('queryCache');
        $this->schemaCache = $boot->env('schemaCache');
        $this->serverStatusCache = $boot->env('serverStatusCache');
        
        $this->enableQueryCache = $boot->env('enableQueryCache');
        $this->enableSchemaCache = $boot->env('enableSchemaCache');

        
        parent::init();
    }


}
