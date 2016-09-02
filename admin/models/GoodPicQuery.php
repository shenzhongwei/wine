<?php

namespace admin\models;

/**
 * This is the ActiveQuery class for [[GoodPic]].
 *
 * @see GoodPic
 */
class GoodPicQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return GoodPic[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GoodPic|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
