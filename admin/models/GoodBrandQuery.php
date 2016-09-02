<?php

namespace admin\models;

/**
 * This is the ActiveQuery class for [[GoodBrand]].
 *
 * @see GoodBrand
 */
class GoodBrandQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return GoodBrand[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GoodBrand|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
