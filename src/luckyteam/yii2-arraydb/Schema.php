<?php

namespace luckyteam\yii\arraydb;

use yii\base\Object;
use yii\db\TableSchema;

class Schema extends Object
{
    /**
     * @var Connection Database connection
     */
    public $db;

    /**
     * @todo
     */
    public $data = [];

    /**
     * @var TableSchema[]
     */
    private $_tableSchemas;

    /**
     * @inheritdoc
     */
    public function __construct(Connection $db, $config = [])
    {
        $this->db = $db;

        parent::__construct($config);
    }

    public function getTableSchema($name, $refresh = false)
    {
        return $this->_tableSchemas[$name];
    }

    /**
     * @return  TableSchema[]
     */
    public function getTableSchemas()
    {
        return $this->_tableSchemas;
    }

    /**
     * @param TableSchema[] $tableSchemas
     */
    public function setTableSchemas($tableSchemas = [])
    {
        $this->_tableSchemas = $tableSchemas;
    }

    /**
     * Executes the INSERT command, returning primary key values
     *
     * @param string $table the table that new rows will be inserted into
     * @param array $columns the column data (name => value) to be inserted into the table
     *
     * @return array|false Primary key values or false if the command fails
     */
    public function insert($table, $columns)
    {
        $primaryKeys = $this->db->createCommand()->insert($table, $columns)->execute();

        return $primaryKeys;
    }
}
