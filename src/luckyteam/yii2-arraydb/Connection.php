<?php

namespace luckyteam\yii\arraydb;

use yii\base\Object;

class Connection extends Object
{
    /**
     * @var Schema Database schema
     */
    public $schema;

    /**
     * @var array Config of schema
     */
    public $schemaConfig = [];

    /**
     * @var QueryBuilder Builder of Query
     */
    public $queryBuilder;

    /**
     * @var SchemaBuilder Builder of database schema
     */
    public $schemaBuilder;
    
    /**
     * @var CommandExecutor Executor of command
     */
    public $commandExecutor;

    /**
     * @inheritdoc
     */
    public function __construct(
        SchemaBuilder $schemaBuilder, QueryBuilder $queryBuilder, CommandExecutor $commandExecutor, $config = []
    )
    {
        $this->schemaBuilder = $schemaBuilder;
        $this->commandExecutor = $commandExecutor;
        $this->queryBuilder = $queryBuilder;

        parent::__construct($config);
    }

    public function init()
    {
        $schemaBuilder = $this->schemaBuilder;
        $this->schema = $schemaBuilder->createSchema(
            $this->schemaConfig, [$this]
        );

        parent::init();
    }

    /**
     * Create Command of database
     *
     * @param array $commandProperties
     * @param array $commandParams
     *
     * @return Command
     */
    public function createCommand($commandProperties = [], $commandParams = [])
    {
        $schemaBuilder = $this->schemaBuilder;

        /** Constructor args @see Command */
        $commandParams[0] = $this;
        $commandParams[1] = $this->commandExecutor;

        $commandProperties['class'] = Command::class;
        $command = $schemaBuilder->createCommand($commandProperties, $commandParams);

        return $command;
    }

    /**
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }
}
