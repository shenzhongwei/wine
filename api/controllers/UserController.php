<?php
namespace api\controllers;
/**
 * Created by PhpStorm.
 * User: me
 * Date: 2016/8/10
 * Time: 15:06
 */

class UserController extends ApiController{
    public function actionIndex(){
        return $this->showResult(200,'11',[1,'aa']);
    }
}