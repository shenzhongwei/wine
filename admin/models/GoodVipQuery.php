<?php

namespace admin\models;

/**
 * This is the ActiveQuery class for [[GoodVip]].
 *
 * @see GoodVip
 */
class GoodVipQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return GoodVip[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GoodVip|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
