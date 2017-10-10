<?php

namespace luckyteam\yii\arraydb;

use yii\base\InvalidConfigException;
use yii\base\Object;
use yii\db\ColumnSchema;
use yii\db\TableSchema;
use yii\di\Container;

class SchemaBuilder extends Object
{
    /**
     * @var Container Dependency Injection Container
     */
    public $container;

    /**
     * @inheritdoc
     */
    public function __construct(Container $container, $config = [])
    {
        $this->container = $container;
        parent::__construct($config);
    }

    /**
     * Create Command of database
     *
     * @param array $properties
     * @param array $params
     *
     * @return Command
     * @throws InvalidConfigException
     */
    public function createCommand($properties = [], $params = [])
    {
        return $this->createObject($properties, $params);
    }

    /**
     * Create Schemas of database
     *
     * @param array $schemaConfig Config of table database
     * @param array $schemaParams Params of table database
     *
     * @return Schema
     * @throws InvalidConfigException
     */
    public function createSchema(array $schemaConfig = [], array $schemaParams = [])
    {
        $tableSchemas = isset($schemaConfig['tableSchemas']) ? $schemaConfig['tableSchemas'] : [];
        $tableSchemaParams = [];
        if (isset($schemaParams['tableSchemaParams'])) {
            $tableSchemaParams = $schemaParams['tableSchemaParams'];
            unset($schemaParams['tableSchemaParams']);
        }
        $schemaConfig['tableSchemas'] = $this->createTableSchemas($tableSchemas, $tableSchemaParams);

        return $this->createObject($schemaConfig, $schemaParams);
    }

    /**
     * Create Schemas of table
     *
     * @param array $tableSchemasConfig Config of table schemas
     * @param array $tableSchemaParams Params of table schema
     *
     * @return TableSchema
     * @throws \yii\base\InvalidConfigException
     */
    private function createTableSchemas(array $tableSchemasConfig = [], array $tableSchemaParams = [])
    {
        $tableSchemas = [];
        foreach ($tableSchemasConfig as $tableSchemaConfig) {
            $tableSchema = $this->createTableSchema(
                $tableSchemaConfig, $tableSchemaParams
            );
            $tableSchemaName = $tableSchema->name;
            $tableSchemas[$tableSchemaName] = $tableSchema;
        }

        return $tableSchemas;
    }

    /**
     * Create Schema of table
     *
     * @param array $tableSchemaConfig Config of table schema
     * @param array $tableSchemaParams Params of table schema
     *
     * @return TableSchema
     * @throws \yii\base\InvalidConfigException
     */
    private function createTableSchema(array $tableSchemaConfig = [], array $tableSchemaParams = [])
    {
        $columns = isset($tableSchemaConfig['columns']) ? $tableSchemaConfig['columns'] : [];
        $columnSchemaParams = [];
        if (isset($tableSchemaParams['columnSchemaParams'])) {
            $columnSchemaParams = $tableSchemaParams['columnSchemaParams'];
            unset($tableSchemaParams['columnSchemaParams']);
        }
        $tableSchemaConfig['columns'] = $this->createColumnSchemas($columns, $columnSchemaParams);

        return $this->createObject($tableSchemaConfig, $tableSchemaParams);
    }

    /**
     * Create Schema of column
     *
     * @param array $columnSchemaConfig Config of column schema
     * @param array $columnSchemaParams Params of column schema
     *
     * @return ColumnSchema
     * @throws \yii\base\InvalidConfigException
     */
    private function createColumnSchema(array $columnSchemaConfig = [], array $columnSchemaParams = [])
    {
        return $this->createObject($columnSchemaConfig, $columnSchemaParams);
    }

    /**
     * Create Schema of columns
     *
     * @param array $columnSchemasConfig Config of column schemas
     * @param array $columnSchemaParams Params of column schema
     *
     * @return ColumnSchema[]
     * @throws \yii\base\InvalidConfigException
     */
    private function createColumnSchemas(array $columnSchemasConfig, array $columnSchemaParams)
    {
        $columnSchemas = [];
        foreach ($columnSchemasConfig as $columnSchemaConfig) {
            $columnSchema = $this->createColumnSchema(
                $columnSchemaConfig, $columnSchemaParams
            );
            $columnSchemaName = $columnSchema->name;
            $columnSchemas[$columnSchemaName] = $columnSchema;
        }
        return $columnSchemas;
    }

    /**
     * Create object
     *
     * @param mixed $type Config of object
     * @param array $params Params of  object
     *
     * @return object Instance of object
     * @throws InvalidConfigException
     */
    public function createObject($type, array $params)
    {
        $container = $this->container;
        if (is_string($type)) {
            return $container->get($type, $params);
        } elseif (is_array($type) && isset($type['class'])) {
            $class = $type['class'];
            unset($type['class']);
            return $container->get(
                $class, $params, $type
            );
        }

        throw new InvalidConfigException('Object configuration must be an array containing a "class" element.');
    }

    /**
     * Configures an object with the initial property values
     *
     * @param object $object the object to be configured
     * @param array $properties the property initial values given in terms of name-value pairs
     *
     * @return object the object itself
     */
    public function configureObject($object, $properties)
    {
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }

        return $object;
    }
}
