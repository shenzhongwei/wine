<?php
namespace api\controllers;

use Yii;
use api\models\AdList;
use yii\helpers\ArrayHelper;

/**
 * Created by PhpStorm.
 * User: me
 * Date: 2016/8/23
 * Time: 10:43
 */

class PicController extends ApiController{

    /**
     * 启动页图片
     */
    public function actionBootPic(){
        $colum = ['pic','url'];
        $bootPic = AdList::Search($colum)->andWhere(['type'=>7])->one()->toArray();
        $bootPic['pic'] = Yii::$app->params['img_path'].$bootPic['pic'];
        return $this->showResult(200,'成功',$bootPic);
    }

    /**
     * 首页广告图片
     */
    public function actionAdList(){
        $ads = AdList::find()->where('type<>7 and is_show=1')->limit(5)->asArray()->all();
        $data = ArrayHelper::getColumn($ads,function($element){
            return [
                'pic'=>Yii::$app->params['img_path'].$element['pic'],
                'type'=>$element['type'],
                'url'=>$element['url'],
                'target'=>$element['target_id'],
            ];
        });
        return $this->showResult(200,'成功',$data);
    }

}