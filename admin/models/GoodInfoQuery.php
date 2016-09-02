<?php

namespace admin\models;

/**
 * This is the ActiveQuery class for [[GoodInfo]].
 *
 * @see GoodInfo
 */
class GoodInfoQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return GoodInfo[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GoodInfo|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
