<?php

namespace app\models;

use yii\db\ActiveRecord;

class Movie extends ActiveRecord
{
    public static $db;

    public static function tableName()
    {
        return 'movie';
    }

    public static function getDb()
    {
        return self::$db;
    }
}
