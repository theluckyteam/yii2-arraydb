<?php

use app\models\Person;
use luckyteam\yii\arraydb\Connection;
use luckyteam\yii\arraydb\Schema;
use yii\db\ColumnSchema;
use yii\db\TableSchema;
use yii\di\Container;
use yii\helpers\VarDumper;

class FindActiveRecordTest extends \Codeception\Test\Unit
{
    /**
     * @inheritdoc
     */
    protected function _before()
    {
        Person::$db = $this->createDb();
    }

    /**
     * Find all models (by static method)
     */
    public function testFindAllByStaticMethodAndEmptyCondition()
    {
        $db = Person::getDb();
        $schema = $db->getSchema();
        $tableName = Person::tableName();

        $count = count($schema->data[$tableName]);
        $persons = Person::findAll([]);

        $this->assertCount($count, $persons);
//        codecept_debug(VarDumper::dumpAsString($persons, 3));
    }

    /**
     * Find all models (by static method)
     */
    public function testFindAllByStaticMethod()
    {
        $keys = [2, 4, 8];

        $count = count($keys);
        $persons = Person::findAll($keys);

        $this->assertCount($count, $persons);
//        codecept_debug(VarDumper::dumpAsString($persons, 3));
    }

    /**
     * Find all by primary key and condition
     */
    public function testFindAllByPrimaryKeyAndCondition()
    {
        $count = 0;
        $persons = Person::findAll(['id' => 2, 'first_name' => 'Alex']);

        $this->assertCount($count, $persons);
//        codecept_debug(VarDumper::dumpAsString($persons, 3));

        $count = 1;
        $persons = Person::findAll(['id' => 5, 'first_name' => 'Tony']);

        $this->assertCount($count, $persons);
//        codecept_debug(VarDumper::dumpAsString($persons, 3));
    }

    /**
     * Find all models (by call chain)
     */
    public function testFindAllByCallChain()
    {
        $db = Person::getDb();
        $schema = $db->getSchema();
        $tableName = Person::tableName();

        $count = count($schema->data[$tableName]);
        $persons = Person::find()->all();

        $this->assertCount($count, $persons);
//        codecept_debug(VarDumper::dumpAsString($persons, 3));
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
