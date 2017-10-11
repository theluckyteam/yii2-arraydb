<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property Profession $profession
 * @property Movie[] $movies
 */
class Person extends ActiveRecord
{
    const GARRY_POTTER_ID = 2;
    const FREUD_SIGMUND_ID = 4;
    const TONY_STARK_ID = 6;
    const CHARLES_SPENCER_ID = 8;

    public static $db;

    public static function tableName()
    {
        return 'person';
    }

    public function getProfession()
    {
        return $this->hasOne(Profession::class, ['id' => 'profession_id']);
    }

    public function getMovies()
    {
        return $this->hasMany(Movie::class, ['person_id' => 'id']);
    }

    public static function getDb()
    {
        return self::$db;
    }
}
