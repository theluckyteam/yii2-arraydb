<?php

namespace luckyteam\yii\arraydb;

use luckyteam\arraydb\ConditionBuilder;
use yii\base\Object;

/**
 * Executor of commands
 */
class CommandExecutor extends Object
{
    /**
     * @var ConditionBuilder
     */
    public $_conditionBuilder;

    /**
     * @inheritdoc
     */
    public function __construct(ConditionBuilder $conditionBuilder, $config = [])
    {
        $this->_conditionBuilder = $conditionBuilder;

        parent::__construct($config);
    }

    /**
     * Execute command
     *
     * @param Connection $db Database connection
     * @param Command $command Database command
     *
     * @return array Result of command
     */
    public function execute(Connection $db, Command $command)
    {
        /**
         * If SELECT command @see executeSelect
         * If INSERT command @see executeInsert
         * If UPDATE command @see executeUpdate
         */
        $method = 'execute' . ucfirst($command->operation);

        return call_user_func(
            [$this, $method], $db, $command
        );
    }

    /**
     * Execute SELECT command
     *
     * @param Connection $db Database connection
     * @param Command $command Database command
     *
     * @return array Result of command
     */
    private function executeSelect(Connection $db, Command $command)
    {
        $schema = $db->getSchema();
        $conditionBuilder = $this->getConditionBuilder();

        $query = $command->query;
        $table = reset($query->from);
        $condition = $query->where;

        $records = [];

        if ($condition) {
            $tableSchema = $schema->getTableSchema($table);
            $primaryKey = $tableSchema->primaryKey;
            $primaryKey = reset($primaryKey);

            if (
                is_array($condition) && count($condition) == 1
                    && array_key_exists($primaryKey, $condition)
            ) {
                // Value of condition by primaryKey is array ($primaryKey => [1, 2, 3, 5])
                if (is_array($condition[$primaryKey]) && $condition[$primaryKey]) {
                    foreach ($condition[$primaryKey] as $key) {
                        $records[] = $schema->data[$table][$key];
                    }

                // Value of condition by primaryKey is empty array ($primaryKey => [])
                } elseif (is_array($condition[$primaryKey]) && !$condition[$primaryKey]) {
                    $records = $schema->data[$table];

                // Value of condition by primaryKey is scalar ($primaryKey => 5)
                } else {
                    $records[] = $schema->data[$table][$condition[$primaryKey]];
                }

            } else {
                $conditionExecutor = $conditionBuilder->build($condition);
                foreach ($schema->data[$table] as $row) {
                    if ($conditionExecutor->execute($row)) {
                        $records[] = $row;
                    }
                }
            }
        } else {
            $records = $schema->data[$table];
        }

        return $records;
    }

    /**
     * Execute INSERT command
     *
     * @param Connection $db Database connection
     * @param Command $command Database command
     *
     * @return array Primary keys record
     */
    private function executeInsert(Connection $db, Command $command)
    {
        $schema = $db->getSchema();
        $table = $command->table;
        $columns = $command->columns;

        $key = max(array_keys($schema->data[$table])) + 1;

        $tableSchema = $db->getSchema()->getTableSchema($table);
        $primaryKey = $tableSchema->primaryKey;
        $primaryKey = reset($primaryKey);
        if (!array_key_exists($primaryKey, $columns) || !$columns[$primaryKey]) {
            $columns[$primaryKey] = $key;
        }

        foreach ($columns as $column => $value) {
            $schema->data[$table][$key][$column] = $value;
        }

        return [$primaryKey => $key];
    }

    /**
     * Execute UPDATE command
     *
     * @param Connection $db Database connection
     * @param Command $command Database command
     *
     * @return array Count updated records
     */
    private function executeUpdate(Connection $db, Command $command)
    {
        $schema = $db->getSchema();
        $conditionBuilder = $this->getConditionBuilder();

        $table = $command->table;
        $condition = $command->condition;
        $columns = $command->columns;

        $countUpdatedRecords = 0;
        if ($condition) {
            $tableSchema = $schema->getTableSchema($table);
            $primaryKey = $tableSchema->primaryKey;
            $primaryKey = reset($primaryKey);

            if (
                is_array($condition) && count($condition) == 1
                    && array_key_exists($primaryKey, $condition)
            ) {
                // Value of condition by primaryKey is array ($primaryKey => [1, 2, 3, 5])
                if (is_array($condition[$primaryKey]) && $condition[$primaryKey]) {
                    foreach ($condition[$primaryKey] as $key) {
                        foreach ($columns as $column => $value) {
                            $schema->data[$table][$key][$column] = $value;
                        }
                        $countUpdatedRecords++;
                    }

                // Value of condition by primaryKey is empty array ($primaryKey => [])
                } elseif (is_array($condition[$primaryKey]) && !$condition[$primaryKey]) {
                    foreach ($schema->data[$table] as $key => $record) {
                        foreach ($columns as $column => $value) {
                            $schema->data[$table][$key][$column] = $value;
                        }
                        $countUpdatedRecords++;
                    }

                // Value of condition by primaryKey is scalar ($primaryKey => 5)
                } else {

                    if (
                        array_key_exists($primaryKey, $columns)
                            && $columns[$primaryKey]
                    ) {
                        $renewedPrimaryKey = $columns[$primaryKey];
                        $outdatedPrimaryKey = $condition[$primaryKey];

                        $record = $schema->data[$table][$outdatedPrimaryKey];
                        unset($schema->data[$table][$outdatedPrimaryKey]);

                        foreach ($columns as $column => $value) {
                            $record[$column] = $value;
                        }
                        $schema->data[$table][$renewedPrimaryKey] = $record;
                    } else {
                        foreach ($columns as $column => $value) {
                            $schema->data[$table][$condition[$primaryKey]][$column] = $value;
                        }
                    }
                    $countUpdatedRecords++;
                }

            } else {
                $conditionExecutor = $conditionBuilder->build($condition);
                foreach ($schema->data[$table] as $key => $record) {
                    if ($conditionExecutor->execute($record)) {
                        foreach ($columns as $column => $value) {
                            $schema->data[$table][$key][$column] = $value;
                        }
                        $countUpdatedRecords++;
                    }
                }
            }
        } else {
            foreach ($schema->data[$table] as $key => $record) {
                foreach ($columns as $column => $value) {
                    $schema->data[$table][$key][$column] = $value;
                }
                $countUpdatedRecords++;
            }
        }

        return $countUpdatedRecords;
    }

    /**
     * @internal
     */
    public function getConditionBuilder()
    {
        return $this->_conditionBuilder;
    }

    private function isNotEmptyCondition($condition)
    {
        return !empty($condition);
    }
}
