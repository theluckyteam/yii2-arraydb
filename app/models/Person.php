<?php

namespace app\models;

use yii\db\ActiveRecord;

class Person extends ActiveRecord
{
    public static $db;

    public static function tableName()
    {
        return 'person';
    }

    public static function getDb()
    {
        return self::$db;
    }
}
