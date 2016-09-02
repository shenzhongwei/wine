<?php

namespace admin\models;

/**
 * This is the ActiveQuery class for [[MerchantInfo]].
 *
 * @see MerchantInfo
 */
class MerchantInfoQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MerchantInfo[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MerchantInfo|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
