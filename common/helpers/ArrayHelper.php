<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\helpers;
use yii\helpers\BaseArrayHelper;
/**
 * ArrayHelper provides additional array functionality that you can use in your
 * application.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ArrayHelper extends BaseArrayHelper
{

	public static function getOneError($errors){
		foreach($errors as $val){
			return $val[0];
			exit;
		}
	}

	public static function myGetFirstError($model){
		$errors = $model->getFirstErrors();
		return current($errors);
	}

	public static function myJsonError($status,$message){
		return [
			'status'=>$status,
			'message'=>$message
		];
	}
	static public function cut_utf8str($source,$length){
			$return_str = '';
			$i = 0;
			$n = 0;
			$strlength = strlen($source);
			while(($n<$length) &&($i<$strlength)){
				$tem_str = substr($source,$i,1);
				$ascnum = ord($tem_str);
				if($ascnum>=224){
					$return_str .= substr($source,$i,3);
					$i +=3;
					$n++;
				}elseif($ascnum>=192){
					$return_str .= substr($source,$i,2);
					$i +=2;
					$n++;
				}elseif($ascnum>=65 && $ascnum<=90){
					$return_str .=substr($source,$i,1);
					$i +=1;
					$n++;
				}else{
					$return_str .=substr($source,$i,1);
					$i +=1;
					$n +=0.5;
				}
			}
			if($strlength>$length){
				$return_str .='...';
			}
			return $return_str;
		}

	/**
	 * 	curl get 请求
	 *
	 * 	$return result
	 */
	public static  function getRequire($url)
	{

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$result = curl_exec($ch);

		curl_close($ch);

		return $result;
	}

}