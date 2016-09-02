<?php

namespace admin\models;

/**
 * This is the ActiveQuery class for [[GoodCountry]].
 *
 * @see GoodCountry
 */
class GoodCountryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return GoodCountry[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GoodCountry|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
