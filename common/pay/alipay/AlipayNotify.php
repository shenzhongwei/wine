<?php
 namespace common\pay\alipay;
 use Yii;
 use yii\base\Object;
 use common\pay\alipay\helpers\RsaHelper;
 use common\pay\alipay\helpers\AlipayHelper;

 class AlipayNotify extends Object
 {

 	/**
 	 * HTTPS形式消息验证地址
 	 */
	public $https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
	/**
     * HTTP形式消息验证地址
     */
	public  $http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';

	public  $alipay_config = [
				'private_key_path'=>'@common/pay/alipay/key/rsa_private_key.pem', //商户私钥路径
				'ali_public_key_path'=>'@common/pay/alipay/key/rsa_public_key.pem', //支付宝公钥路径
				'sign_type'=> 'RSA',  //加密方式
				'input_charset'=> 'utf-8', //字符编码
				'cacert'=> '@common/pay/alipay/cacert.pem',//验证文件
				'transport'=>'http',  					//协议
				'partner'=>'2088421849581905'			//合作者id
			];

	// function __construct($alipay_config){
	// 	$this->alipay_config = $alipay_config;
	// }

    // function AlipayNotify($alipay_config) {
    // 	$this->__construct($alipay_config);
    // }
    /**
     * 针对notify_url验证消息是否是支付宝发出的合法消息
     * @return boolean
     */
	function verifyNotify(){
		if(empty($_POST)) {//判断POST来的数组是否为空
			return false;
		} else {
			$log = new AlipayHelper();
            $log->log_result($_POST["notify_id"]);
			//生成签名结果
			$isSign = $this->getSignVeryfy($_POST, $_POST["sign"]);
			//获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
			$responseTxt = 'true';
			if (! empty($_POST["notify_id"])) 
			{
				$responseTxt = $this->getResponse($_POST["notify_id"]);
			}
			
			//写日志记录
			if ($isSign) {
				$isSignStr = 'true';
			}
			else {
				$isSignStr = 'false';
			}
			$log_text = "responseTxt=".$responseTxt."\n notify_url_log:isSign=".$isSignStr.",";
            $str = AlipayHelper::createLinkString($_POST);
			$log_text = $log_text.$str;
			$log->log_result($log_text);
            $log->log_result($responseTxt);
            $log->log_result($isSign);
			//验证
			//$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
			//isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
			if (preg_match("/true$/i",$responseTxt) && $isSign) {
				return true;
			} else {
				return false;
			}
		}
	}
	
    /**
     * 针对return_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
	function verifyReturn(){
		if(empty($_GET)) {//判断POST来的数组是否为空
			return false;
		}
		else {
			//生成签名结果
			$isSign = $this->getSignVeryfy($_GET, $_GET["sign"]);
			//获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
			$responseTxt = 'true';
			if (! empty($_GET["notify_id"])) 
				{
					$responseTxt = $this->getResponse($_GET["notify_id"]);
				}
			
//			写日志记录
			if ($isSign) {
				$isSignStr = 'true';
			}
			else {
				$isSignStr = 'false';
			}
			$log_text = "responseTxt=".$responseTxt."\n return_url_log:isSign=".$isSignStr.",";
			$log_text = $log_text.createLinkString($_GET);
			logResult($log_text);
			
			//验证
			//$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
			//isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
			if (preg_match("/true$/i",$responseTxt) && $isSign) {
				return true;
			} else {
				return false;
			}
		}
	}
	
    /**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
	function getSignVeryfy($para_temp, $sign) {
		//除去待签名参数数组中的空值和签名参数
		$para_filter = AlipayHelper::paraFilter($para_temp);
        $log = new AlipayHelper();
		
		//对待签名参数数组排序
		$para_sort = AlipayHelper::argSort($para_filter);
		
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = AlipayHelper::createLinkstring($para_sort);
        $log->log_result($prestr);
        $log->log_result($this->alipay_config['ali_public_key_path']);
		$isSgin = false;
		switch (strtoupper(trim($this->alipay_config['sign_type']))) {
			case "RSA" :
				$isSgin = RsaHelper::rsaVerify($prestr, trim($this->alipay_config['ali_public_key_path']), $sign);
				break;
			default :
				$isSgin = false;
		}
		
		return $isSgin;
	}

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id 通知校验ID
     * @return 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空 
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
	function getResponse($notify_id){
		$transport = strtolower(trim($this->alipay_config['transport']));
		$partner = trim($this->alipay_config['partner']);
		$veryfy_url = '';
		if($transport == 'https') {
			$veryfy_url = $this->https_verify_url;
		}
		else {
			$veryfy_url = $this->http_verify_url;
		}
		$veryfy_url = $veryfy_url."partner=" . $partner . "&notify_id=" . $notify_id;
		$responseTxt = AlipayHelper::getHttpResponseGET($veryfy_url, $this->alipay_config['cacert']);
		
		return $responseTxt;
	}
}
?>