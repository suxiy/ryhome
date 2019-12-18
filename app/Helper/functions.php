<?php
/**
 * 保存配置项
 */
if (!function_exists('option_set')) {
    function option_set($name,$value){
        return \App\Model\Options::query()->where('option_name',$name)->updateOrInsert(['option_name'=>$name],['option_value'=>serialize($value)]);
    }
}

/**
 * 获取配置项
 */
if (!function_exists('option_get')) {
    function option_get($name){
        static $options;
        if(!isset($options[$name])){
            $options[$name] = \App\Model\Options::query()->where('option_name',$name)->value('option_value');
        }
        return $options[$name];
    }
}

/**
 * 通过数组获取配置项
 */
if (!function_exists('option_get_with')) {
    function option_get_with($array){
        $model = \App\Model\Options::query()->whereIn('option_name',$array);
        return $model->count()?$model->pluck('option_value')->toArray():[];
    }
}

/**
 * 记录日志
 */
if (!function_exists('log_json')) {
    function log_json($dir,$method,$msg,$tenant_id = '0001'){
        $msg = is_array($msg)?json_encode($msg,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES):$msg;
        $msg = stripcslashes($msg);
        $path = public_path('logs/'.$dir.'/'.$method.'/');
        $filename = date('Ymd').'.log';
        $msg = date('[Y-m-d H:i:s]').$msg;
        if(!is_dir($path)){
            mkdir($path,0777,true);
        }
        file_put_contents($path.'/'.$filename,$msg.PHP_EOL,FILE_APPEND);
    }
}

if (!function_exists('log_array')) {
    function log_array($dir,$method,$msg,$tenant_id = 'default'){
        $msg = is_string($msg)?[$msg]:$msg;
        $path = public_path('logs/'.$dir.'/'.$method.'/');
        $filename = date('Ymd').'.log';
        $_msg = "####################################################\n\r";
        $_msg .= date('[Y-m-d H:i:s]').print_r($msg,true);
        $_msg .= "####################################################\n\r";
        if(!is_dir($path)){
            mkdir($path,0777,true);
        }
        file_put_contents($path.'/'.$filename,$_msg.PHP_EOL,FILE_APPEND);
    }
}

if (!function_exists('curl_get')) {
    function curl_get($url, $timeout = 5){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $file_contents = curl_exec($ch);
        curl_close($ch);
        return $file_contents;
    }
}

if (!function_exists('curl_post')) {
    function curl_post($url, array $params = array(), $timeout){
        $ch = curl_init();//初始化
        curl_setopt($ch, CURLOPT_URL, $url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        return ($data);
    }
}

if (!function_exists('curl_post_xml')) {
    function curl_post_xml($url,$data){
        $xml = "<XML>";
        foreach ($data as $k=>$v){
            $xml.="<".$k.">".$v."</".$k.">";
        }
        $xml.="</XML>";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch);
        return $data;
    }
}



