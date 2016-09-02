<?php

namespace admin\models;

/**
 * This is the ActiveQuery class for [[GoodDry]].
 *
 * @see GoodDry
 */
class GoodDryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return GoodDry[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GoodDry|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
