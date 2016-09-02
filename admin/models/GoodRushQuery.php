<?php

namespace admin\models;

/**
 * This is the ActiveQuery class for [[GoodRush]].
 *
 * @see GoodRush
 */
class GoodRushQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return GoodRush[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GoodRush|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
