<?php

namespace admin\models;

/**
 * This is the ActiveQuery class for [[GoodBoot]].
 *
 * @see GoodBoot
 */
class GoodBootQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return GoodBoot[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GoodBoot|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
