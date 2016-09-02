<?php

namespace admin\models;

/**
 * This is the ActiveQuery class for [[ShoppingCert]].
 *
 * @see ShoppingCert
 */
class ShoppingCertQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ShoppingCert[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ShoppingCert|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
