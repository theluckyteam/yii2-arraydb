<?php

namespace luckyteam\yii\arraydb;

use yii\base\Object;
use yii\db\Query;

class QueryBuilder extends Object
{
    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    public function build(Query $query)
    {
        $queryBuilder = $this;
        $query = $query->prepare($queryBuilder);

        return [['query' => $query], []];
    }
}
