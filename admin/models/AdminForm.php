<?php
namespace admin\models;

use admin\models\Admin;
use Yii;
use yii\base\Model;
/**
 * Login form
 */
class AdminForm extends Model
{
    public $wa_username;
    public $wa_password;
    public $rememberMe = true;

    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['wa_username'], 'required','message'=>'请输入账号'],
            [[ 'wa_password'],'required','message'=>'请输入密码'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['wa_password', 'validatePassword'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'wa_username' => '账号',
            'wa_password' => '密码',
            'rememberMe'=>'记住该账号',
        ];
    }


    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword()
    {
        $user = $this->getUser();
        if(!$user){
            $this->addError('wa_username','用户不存在');
        }elseif (!$this->checkPassword($user->wa_password)) {
            $this->addError('wa_password', '密码错误');
        }elseif (!$this->checkStatus($user->wa_status)) {
            $this->addError('wa_username', '该账号状态异常,请联系管理员');
        }elseif (!$this->checkType($user->wa_type)) {
            $this->addError('wa_username', '该用户非后台管理员');
        }elseif ($user->wa_lock!=0) {
            $this->addError('wa_username', '该用户已被锁定');
        }
    }

    public function checkPassword($password){
        if(!$this->hasErrors()){
            return $password == md5(Yii::$app->params['pwd_pre'].$this->wa_password);
        }else{
            return false;
        }
    }

    public function checkStatus($status){
        if(!$this->hasErrors()){
            if($status != 1){
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }
    }

    public function checkType($type){
        if(!$this->hasErrors()){
            if(!in_array($type,[1,2,3,4])){
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Admin::findIdentityByUsername($this->wa_username);
        }
        return $this->_user;
    }

    public function UpdateModel(){
        $user = $this->getUser();
        $user->wa_last_login_ip = Yii::$app->request->userIP;
        $user->wa_token = Yii::$app->security->generateRandomString();
        $user->wa_last_login_time = date('Y-m-d H:i:s',time());
        $user->updated_time = date('Y-m-d H:i:s',time());
        if(!$user->save()){
            return false;
        }else{
            return true;
        }
    }

}
