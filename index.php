<?php

header("Content-Type:text/html;charset=utf8");

function get($url, $data = null){
	// 1. 初始化
	$ch = curl_init();

	// 2. 设置选项，包括URL
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_HEADER,0);

	// 3. 执行并获取HTML文档内容
	$output = curl_exec($ch);
	if($output === FALSE ){
		echo "CURL Error:".curl_error($ch);
	}

	// 4. 释放curl句柄
	curl_close($ch);
	return $output;
}

function post($url,$requestString,$timeout = 5){
	if($url == '' || $requestString == '' || $timeout <=0){
		return false;
	}
	$con = curl_init((string)$url);
	curl_setopt($con, CURLOPT_HEADER, false);
	curl_setopt($con, CURLOPT_POSTFIELDS, $requestString);
	curl_setopt($con, CURLOPT_POST,true);
	curl_setopt($con, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($con, CURLOPT_TIMEOUT,(int)$timeout);
	curl_setopt($con, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
	));
	return curl_exec($con); 
}

$url = "https://oapi.dingtalk.com/sns/gettoken?appid=dingoa7vgbzxwuujhfv9lt&appsecret=TLBMK8xBSWvO3D4BmI_C0abEhyTOhPAUhdQV3TQcNt7nevCVWJX3XJSgsOx7kmOV";
$access_token = json_decode(get($url), true)['access_token'];

$url2 = "https://oapi.dingtalk.com/sns/get_persistent_code?access_token=".$access_token;
$persistent_code = json_decode(post($url2, json_encode(['tmp_auth_code'=>$_GET['code']])), true);

$url3 = "https://oapi.dingtalk.com/sns/get_sns_token?access_token=".$access_token;
$sns_token = json_decode(post($url3, json_encode(['openid'=>$persistent_code['openid'], 'persistent_code'=>$persistent_code['persistent_code']])), true);

$url4 = "https://oapi.dingtalk.com/sns/getuserinfo?sns_token=".$sns_token['sns_token'];
$user_info = get($url4);

print_r($user_info);die;