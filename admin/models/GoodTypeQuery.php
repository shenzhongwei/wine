<?php

namespace admin\models;

/**
 * This is the ActiveQuery class for [[GoodType]].
 *
 * @see GoodType
 */
class GoodTypeQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return GoodType[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GoodType|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
