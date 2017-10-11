<?php

use app\models\Person;
use luckyteam\yii\arraydb\Connection;
use luckyteam\yii\arraydb\Schema;
use yii\db\ColumnSchema;
use yii\db\TableSchema;
use yii\di\Container;
use yii\helpers\VarDumper;

/**
 * @property integer id
 */
class SaveActiveRecordTest extends \Codeception\Test\Unit
{
    /**
     * @inheritdoc
     */
    protected function _before()
    {
        Person::$db = $this->createDb();
    }

    /**
     * Update model
     */
    public function testUpdateModel()
    {
        $db = Person::getDb();
        $schema = $db->getSchema();
        $tableName = Person::tableName();

        $key = 2;
        $person = Person::findOne($key);
        $this->assertFalse($person->getIsNewRecord());

        $attributeValue = 'Alex';
        $person->first_name = $attributeValue;

        $this->assertTrue($person->save());
        $this->assertTrue($schema->data[$tableName][2]['first_name'] == $attributeValue);

//        codecept_debug(VarDumper::dumpAsString($person, 3));
    }

    /**
     * Update model by condition
     */
    public function testUpdateModelsByPrimaryKeys()
    {
        $db = Person::getDb();
        $schema = $db->getSchema();
        $tableName = Person::tableName();

        $keys = [2, 3, 5];
        $attributeValue = 'Alex';
        Person::updateAll(['first_name' => $attributeValue] , ['id' => $keys]);

        foreach ($schema->data[$tableName] as $key => $item) {
            if (in_array($item['id'], $keys)) {
                $this->assertTrue($item['first_name'] == $attributeValue);
            } else {
                $this->assertFalse($item['first_name'] == $attributeValue);
            }
        }

//        codecept_debug(VarDumper::dumpAsString($schema->data[$tableName], 3));
    }

    /**
     * Test update primary key
     */
    public function testUpdatePrimaryKey()
    {
        $db = Person::getDb();
        $schema = $db->getSchema();
        $tableName = Person::tableName();

        /** @var Person $model */
        $model = Person::findOne(6);
        $model->id = 16;
        $this->assertTrue($model->save());

        /** @var Person $actualModel */
        $actualModel = Person::findOne(16);
        $this->assertNotNull($actualModel);
        $this->assertTrue($actualModel->id == $model->id);

//        codecept_debug(PHP_EOL);
//        codecept_debug(
//            VarDumper::dumpAsString($model->attributes, 3)
//        );
//        codecept_debug(
//            VarDumper::dumpAsString($schema->data[$tableName], 3)
//        );
    }

    /**
     * Create database
     */
    public function createDb()
    {
        $container = new Container();
        $container->set(Container::class, $container);
        $db = $container->get(Connection::class, [], [
            'schemaConfig' => [
                'class' => Schema::class,
                'data' => [
                    'person' => [
                        1 => [
                            'id' => 1,
                            'first_name' => 'Sherlock',
                            'last_name' => 'Holmes',
                        ],
                        2 => [
                            'id' => 2,
                            'first_name' => 'Garry',
                            'last_name' => 'Potter',
                        ],
                        3 => [
                            'id' => 3,
                            'first_name' => 'Steven',
                            'last_name' => 'Rogers',
                        ],
                        4 => [
                            'id' => 4,
                            'first_name' => 'Steven',
                            'last_name' => 'Rogers',
                        ],
                        5 => [
                            'id' => 5,
                            'first_name' => 'Tony',
                            'last_name' => 'Stark',
                        ],
                        6 => [
                            'id' => 6,
                            'first_name' => 'Tony',
                            'last_name' => 'Stark',
                        ],
                        7 => [
                            'id' => 7,
                            'first_name' => 'Robert',
                            'last_name' => 'Bruce',
                        ],
                        8 => [
                            'id' => 8,
                            'first_name' => 'Robert',
                            'last_name' => 'Bruce',
                        ],
                        9 => [
                            'id' => 9,
                            'first_name' => 'Damian',
                            'last_name' => 'Wayne',
                        ],
                        10 => [
                            'id' => 10,
                            'first_name' => 'Peter',
                            'last_name' => 'Parker',
                        ],
                    ],
                ],
                'tableSchemas' => [
                    [
                        'class' => TableSchema::class,
                        'primaryKey' => ['id'],
                        'name' => 'person',
                        'columns' => [
                            [
                                'class' => ColumnSchema::class,
                                'name' => 'id',
                            ],
                            [
                                'class' => ColumnSchema::class,
                                'name' => 'first_name',
                            ],
                            [
                                'class' => ColumnSchema::class,
                                'name' => 'last_name',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        return $db;
    }
}
