<?php

namespace luckyteam\yii\arraydb;

use yii\base\Object;
use yii\db\Query;

/**
 * Command of database
 */
class Command extends Object
{
    /**
     * @var Connection Database connection
     */
    public $db;

    /**
     * @var string Name of operation
     */
    public $operation;

    /**
     * @var Query Query
     */
    public $query;

    /**
     * @var string Name of table
     */
    public $table;

    /**
     * @var array Column data
     */
    public $columns;

    /**
     * @var array Condition
     */
    public $condition;

    /**
     * @var array Parameters to be bound to the command
     */
    public $params;

    /**
     * @var CommandExecutor Executor of command
     */
    public $commandExecutor;

    /**
     * @inheritdoc
     */
    public function __construct(Connection $db, CommandExecutor $executor, $config = [])
    {
        $this->db = $db;
        $this->commandExecutor = $executor;
        parent::__construct($config);
    }

    /**
     *  Executes the command
     */
    public function execute()
    {
        $db = $this->db;
        $commandExecutor = $this->commandExecutor;
        $result = $commandExecutor->execute($db, $this);

        return $result;
    }

    /**
     * Select one record
     *
     * @return array
     */
    public function queryOne()
    {
        $row = null;
        $this->query->limit(1);
        $rows = $this->queryAll();
        if ($rows) {
            $row = reset($rows);
        }

        return $row;
    }

    /**
     * Select all records
     *
     * @return array
     */
    public function queryAll()
    {
        $db = $this->db;
        $commandExecutor = $this->commandExecutor;
        $this->operation = 'select';
        $rows = $commandExecutor->execute($db, $this);

        return $rows;
    }

    /**
     * Creates an INSERT command
     *
     * @param string $table the table that new rows will be inserted into.
     * @param array $columns the column data (name => value) to be inserted into the table
     *
     * @return $this the command object itself
     */
    public function insert($table, $columns)
    {
        $this->operation = 'insert';
        $this->table = $table;
        $this->columns = $columns;

        return $this;
    }

    /**
     * Creates an UPDATE command
     *
     * @param string $table the table to be updated.
     * @param array $columns the column data (name => value) to be updated.
     * @param array $condition the condition that will be put in the WHERE part
     * @param array $params the parameters to be bound to the command
     *
     * @return $this the command object itself
     */
    public function update($table, $columns, $condition = [], $params = [])
    {
        $this->operation = 'update';
        $this->table = $table;
        $this->columns = $columns;
        $this->condition = $condition;
        $this->params = $params;

        return $this;
    }
}
