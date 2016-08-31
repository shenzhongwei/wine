<?php
$config = require_once("gitConfig.php");
$log = "git.log";
file_put_contents($log,'日期：'.date('Y-m-d H:i:s')."\t");
if(isset($_POST['hook'])){
    $hook = json_decode($_POST['hook'],true);
    if($hook['password']!=$config['password']){
        file_put_contents($log,'密码错误'.PHP_EOL,FILE_APPEND);
        exit;
    }
    if(substr($hook['push_data']['ref'],strrpos($hook['push_data']['ref'],'/')+1)!=$config['branch']){
        file_put_contents($log,'分支错误'.PHP_EOL,FILE_APPEND);
        exit;
    }
    if($hook['push_data']['repository']['name']!='wine'){
        file_put_contents($log,'远程出错'.PHP_EOL,FILE_APPEND);
        exit;
    }
    $path = $config['path'];
    $shell = "cd ".$path." && git pull";
    $res = shell_exec($shell);
    foreach ($hook['push_data']['commits'] as $commit) {
        file_put_contents($log,
            $hook['push_data']['repository']['name'] . "\t" .
            $commit['message'] . "\t" .
            $commit['author']['name'] ."\t".$commit['author']['email'] .PHP_EOL,FILE_APPEND
        );
    }
    file_put_contents($log,$res.PHP_EOL,FILE_APPEND);
    exit;
}else{
    file_put_contents($log,'非法请求'.PHP_EOL,FILE_APPEND);
    exit;
}

?>
