<?php
namespace api\controllers;

use Yii;
use api\models\AdList;
use yii\helpers\ArrayHelper;

/**
 * Created by PhpStorm.
 * User: szw
 * Date: 2016/8/23
 * Time: 10:43
 */

class PicController extends ApiController{

    /**
     * 启动页图片
     */
    public function actionBootPic(){
        $colum = ['pic','pic_url'];
        $boot = AdList::Search($colum)->andWhere(['type'=>7])->one();
        if(!empty($boot)){
            $bootPic = $boot->toArray();
            $bootPic['pic'] = Yii::$app->params['img_path'].$bootPic['pic'];
        }else{
            $bootPic = [];
        }
        return $this->showResult(200,'成功',$bootPic);
    }

    /**
     * 首页广告图片
     */
    public function actionAdList(){
        $ads = AdList::find()->where('type<>7 and is_show=1 and postion=1')->limit(5)->asArray()->all();
        $head = ArrayHelper::getColumn($ads,function($element){
            return [
                'pic'=>Yii::$app->params['img_path'].$element['pic'],
                'type'=>$element['type'],
                'url'=>$element['pic_url'],
                'target'=>$element['target_id'],
            ];
        });
        $midAds = AdList::find()->where('type<>7 and is_show=1 and postion=2')->limit(5)->asArray()->all();
        $middle = ArrayHelper::getColumn($midAds,function($element){
            return [
                'pic'=>Yii::$app->params['img_path'].$element['pic'],
                'type'=>$element['type'],
                'url'=>$element['pic_url'],
                'target'=>$element['target_id'],
            ];
        });
        $data = [
            'head'=>$head,
            'middle'=>$middle,
        ];
        return $this->showResult(200,'成功',$data);
    }




}