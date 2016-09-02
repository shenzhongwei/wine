<?php

namespace admin\models;

/**
 * This is the ActiveQuery class for [[GoodColor]].
 *
 * @see GoodColor
 */
class GoodColorQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return GoodColor[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GoodColor|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
