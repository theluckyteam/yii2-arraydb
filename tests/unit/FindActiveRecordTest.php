<?php

use app\models\Movie;
use app\models\Person;
use app\models\Profession;
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
        $db = $this->createDb();
        Person::$db = $db;
        Profession::$db = $db;
        Movie::$db = $db;
    }

    /**
     * Find all models (by static method)
     */
    public function testFindAllByStaticMethod()
    {
        $keys = [Person::GARRY_POTTER_ID, Person::FREUD_SIGMUND_ID, Person::CHARLES_SPENCER_ID];

        $count = count($keys);
        $persons = Person::findAll($keys);

        $this->assertCount($count, $persons);
    }

    /**
     * Find all models (By static method and empty condition)
     */
    public function testFindAllByStaticMethodAndEmptyCondition()
    {
        $db = Person::getDb();
        $schema = $db->getSchema();
        $tableName = Person::tableName();

        $count = count($schema->data[$tableName]);
        $persons = Person::findAll([]);

        $this->assertCount($count, $persons);
    }

    /**
     * Find all by primary key and condition
     */
    public function testFindAllByPrimaryKeyAndCondition()
    {
        $count = 0;
        $persons = Person::findAll(['id' => Person::GARRY_POTTER_ID, 'first_name' => 'Alex']);
        $this->assertCount($count, $persons);

        $count = 1;
        $persons = Person::findAll(['id' => Person::TONY_STARK_ID, 'first_name' => 'Tony']);
        $this->assertCount($count, $persons);
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
    }

    /**
     * One to One relation
     */
    public function testOneToOneRelation()
    {
        $person = Person::findOne(Person::TONY_STARK_ID);
        $profession = $person->profession;

        $this->assertInstanceOf(Profession::class, $profession);
        $this->assertEquals($profession->id, Profession::SUPERHERO_ID);
    }

    /**
     * One to Many relation
     */
    public function testOneToManyRelation()
    {
        $person = Person::findOne(Person::GARRY_POTTER_ID);
        $movies = $person->movies;

        $this->assertTrue(is_array($movies));
        $this->assertNotEmpty($movies);
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
