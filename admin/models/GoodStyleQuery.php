<?php

namespace admin\models;

/**
 * This is the ActiveQuery class for [[GoodStyle]].
 *
 * @see GoodStyle
 */
class GoodStyleQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return GoodStyle[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GoodStyle|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
