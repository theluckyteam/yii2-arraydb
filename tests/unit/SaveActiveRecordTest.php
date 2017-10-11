<?php

use app\models\Person;
use app\models\Profession;
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

        $key = Person::GARRY_POTTER_ID;
        $person = Person::findOne($key);
        $this->assertFalse($person->getIsNewRecord());

        $settingAttributeValue = 'Alex';
        $person->first_name = $settingAttributeValue;

        $this->assertTrue($person->save());
        $this->assertTrue($schema->data[$tableName][Person::GARRY_POTTER_ID]['first_name'] == $settingAttributeValue);
    }

    /**
     * Update model by condition
     */
    public function testUpdateModelsByPrimaryKeys()
    {
        $db = Person::getDb();
        $schema = $db->getSchema();
        $tableName = Person::tableName();

        $keys = [Person::FREUD_SIGMUND_ID, Person::CHARLES_SPENCER_ID];
        $settingAttributeValue = 'Alex';
        Person::updateAll(['first_name' => $settingAttributeValue] , ['id' => $keys]);

        foreach ($schema->data[$tableName] as $key => $item) {
            if (in_array($item['id'], $keys)) {
                $this->assertTrue($item['first_name'] == $settingAttributeValue);
            } else {
                $this->assertFalse($item['first_name'] == $settingAttributeValue);
            }
        }
    }

    /**
     * Test update primary key
     */
    public function testUpdatePrimaryKey()
    {
        /** @var Person $model */
        $model = Person::findOne(6);
        $model->id = 16;
        $this->assertTrue($model->save());

        /** @var Person $actualModel */
        $actualModel = Person::findOne(16);
        $this->assertNotNull($actualModel);
        $this->assertTrue($actualModel->id == $model->id);
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
                            'profession_id' => 2,
                        ],
                        Person::GARRY_POTTER_ID => [
                            'id' => Person::GARRY_POTTER_ID,
                            'first_name' => 'Garry',
                            'last_name' => 'Potter',
                            'profession_id' => 2,
                        ],
                        3 => [
                            'id' => 3,
                            'first_name' => 'Steven',
                            'last_name' => 'Rogers',
                            'profession_id' => Profession::SUPERHERO_ID,
                        ],
                        Person::FREUD_SIGMUND_ID => [
                            'id' => Person::FREUD_SIGMUND_ID,
                            'first_name' => 'Freud',
                            'last_name' => 'Sigmund',
                            'profession_id' => 4,
                        ],
                        5 => [
                            'id' => 5,
                            'first_name' => 'Michelangelo',
                            'last_name' => 'Buonarroti',
                            'profession_id' => 3,
                        ],
                        Person::TONY_STARK_ID => [
                            'id' => Person::TONY_STARK_ID,
                            'first_name' => 'Tony',
                            'last_name' => 'Stark',
                            'profession_id' => Profession::SUPERHERO_ID,
                        ],
                        7 => [
                            'id' => 7,
                            'first_name' => 'Robert',
                            'last_name' => 'Bruce',
                            'profession_id' => Profession::SUPERHERO_ID,
                        ],
                        Person::CHARLES_SPENCER_ID => [
                            'id' => Person::CHARLES_SPENCER_ID,
                            'first_name' => 'Charles',
                            'last_name' => 'Spencer',
                            'profession_id' => 2,
                        ],
                        9 => [
                            'id' => 9,
                            'first_name' => 'Damian',
                            'last_name' => 'Wayne',
                            'profession_id' => Profession::SUPERHERO_ID,
                        ],
                        10 => [
                            'id' => 10,
                            'first_name' => 'Peter',
                            'last_name' => 'Parker',
                            'profession_id' => Profession::SUPERHERO_ID,
                        ],
                    ],
                    'profession' => [
                        Profession::SUPERHERO_ID => [
                            'id' => Profession::SUPERHERO_ID,
                            'name' => 'Superhero',
                        ],
                        2 => [
                            'id' => 2,
                            'name' => 'Movie hero',
                        ],
                        3 => [
                            'id' => 3,
                            'name' => 'Craftsman',
                        ],
                        4 => [
                            'id' => 4,
                            'name' => 'Psychologist',
                        ],
                    ],
                    'movie' => [
                        1 => [
                            'id' => 1,
                            'name' => 'Harry Potter and the Philosopher\'s Stone',
                            'person_id' => Person::GARRY_POTTER_ID,
                        ],
                        2 => [
                            'id' => 2,
                            'name' => 'Harry Potter and the Goblet of Fire',
                            'person_id' => Person::GARRY_POTTER_ID,
                        ],
                        3 => [
                            'id' => 3,
                            'name' => 'Iron Man',
                            'person_id' => Person::TONY_STARK_ID,
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
                            [
                                'class' => ColumnSchema::class,
                                'name' => 'profession_id',
                            ],
                        ],
                    ],
                    [
                        'class' => TableSchema::class,
                        'primaryKey' => ['id'],
                        'name' => 'profession',
                        'columns' => [
                            [
                                'class' => ColumnSchema::class,
                                'name' => 'id',
                            ],
                            [
                                'class' => ColumnSchema::class,
                                'name' => 'name',
                            ],
                        ],
                    ],
                    [
                        'class' => TableSchema::class,
                        'primaryKey' => ['id'],
                        'name' => 'movie',
                        'columns' => [
                            [
                                'class' => ColumnSchema::class,
                                'name' => 'id',
                            ],
                            [
                                'class' => ColumnSchema::class,
                                'name' => 'name',
                            ],
                            [
                                'class' => ColumnSchema::class,
                                'name' => 'person_id',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        return $db;
    }
}
