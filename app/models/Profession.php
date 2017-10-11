<?php

namespace app\models;

use yii\db\ActiveRecord;

class Profession extends ActiveRecord
{
    const SUPERHERO_ID = 1;

    public static $db;

    public static function tableName()
    {
        return 'profession';
    }

    public static function getDb()
    {
        return self::$db;
    }
}
