<?php
/**********************************************************
 *
 * 쿠팡 상품 등록 API (쿠팡 상품링크 -> 채널아이디 포함해서 짧은 URL 생성)
 *
 * **********************************************************
 *
 * Request Parameter
 *
 *    - link : 쿠팡 상품 URL
 *
 ************************************************************/
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors", 1);

include_once '../var.php';
include_once __pdoDB__;    ## DB Instance 생성
include_once __fn__;

date_default_timezone_set('GMT+0');
define('API_URL', 'https://api-gateway.coupang.com'); // 쿠팡 파트너스 연동 url
define('API_PATH', '/v2/providers/affiliate_open_api/apis/openapi/v1/deeplink');

if(empty($_POST['link'])){
	$_JsonData['Result'] = "false";
	$_JsonData['errcode'] = "99";
	$_JsonData['errstr'] = "필수값이 없습니다.";
	$_jecData = json_encode($_JsonData);
	exit($_jecData);
}
if(empty($_POST['subId'])){
	$_JsonData['Result'] = "false";
	$_JsonData['errcode'] = "98";
	$_JsonData['errstr'] = "필수값이 없습니다.";
	$_jecData = json_encode($_JsonData);
	exit($_jecData);
}

$link[] = $_POST['link'];
$fields["coupangUrls"] = $link;
$fields["subId"] = $_POST['subId'];
$strjson = json_encode($fields);

// Replace with your own ACCESS_KEY and SECRET_KEY
$ACCESS_KEY = '6566d8b3-141d-4bde-8de9-606126385670';
$SECRET_KEY = 'dcfcadfdd0e4cf4610fd08b886859f975fde5a8f';

$datetime = date('ymd').'T'.date('His').'Z';
$method = 'POST';
$uri = API_PATH;
$message = $datetime.$method.str_replace('?', '', $uri);
$signature = hash_hmac('sha256', $message, $SECRET_KEY);
$authorization  = 'CEA algorithm=HmacSHA256, access-key='.$ACCESS_KEY.', signed-date='.$datetime.', signature='.$signature;
//pre(API_URL . $uri);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, API_URL . $uri);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:  application/json;charset=UTF-8', 'Authorization:'.$authorization]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $strjson);
curl_setopt($curl, CURLOPT_TIMEOUT, 10);
curl_setopt($curl, CURLOPT_POST, true);
$result = curl_exec($curl);
//pre(curl_getinfo($curl));//마지막 http 전송 정보 출력
//pre(curl_errno($curl));//마지막 에러 번호 출력
//pre(curl_error($curl));//현재 세션의 마지막 에러 출력

curl_close($curl);
$result = json_decode($result, JSON_UNESCAPED_UNICODE);

if(isset($result['rCode']) === false) {
	exit($result['message']);
}
if($result['rCode'] != 0) {
	$_JsonData['Result'] = "false";
	$_JsonData['errcode'] = $result['rCode'];
	$_JsonData['errstr'] = $result['rMessage'];
	$_jecData = json_encode($_JsonData);
}
foreach($result['data'] as $row){
	$data = $row;
}

$_JsonData['Result'] = "true";
$_JsonData['shortenUrl'] = $data['shortenUrl'];
$_jecData = json_encode($_JsonData);
exit($_jecData);
