<?php

namespace admin\models;

/**
 * This is the ActiveQuery class for [[GoodCollection]].
 *
 * @see GoodCollection
 */
class GoodCollectionQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return GoodCollection[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GoodCollection|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
