<?php
/*************************************************
*
*      쿠팡 리포트 api
*      주문취소 데이터
*
*
*************************************************/
if(!empty($_REQUEST['postman']) && $_REQUEST['postman'] == "postman"){
	include_once '../paybooc/var_cron.php';
	include_once __pdoDB__;    ## DB Instance 생성
	include_once __fn__;

}else{
	$path = '/home/paybooc/public_html/';
	include_once $path . 'paybooc/var_cron.php';
	include_once $path . 'Class/Class.Func.php';
	include_once $path . 'Class/Class.PDO.DB.php';
}

set_time_limit(0);
date_default_timezone_set('GMT+0');
define('API_URL', 'https://api-gateway.coupang.com'); // 쿠팡 파트너스 연동 url
define('API_PATH', '/v2/providers/affiliate_open_api/apis/openapi/reports/cancels'); // 취소, 수수료
define('API_SUB_ID', ['payboocADAPIaos','payboocADAPIios','repayboocADAPIaos','repayboocADAPIios']); // 수집할 채널

//service_tp_code 숫자 조정
$set_number_digits = 1;

$curl_data = array();
function coupang_curl($subId, $page=0){
	global $curl_data;
	$datetime = date('ymd').'T'.date('His').'Z';
	$method = 'GET';
	$startDate = empty($_REQUEST['startDate'])?"":$_REQUEST['startDate'];
	if($startDate){
		$param = [
			'startDate' => $startDate,
			'endDate' => $startDate,
			'subId' => $subId,
			'page' => $page,
		];
	}else{
		$param = [
			'startDate' => date('Ymd', strtotime('-1 day')),
			'endDate' => date('Ymd', strtotime('-1 day')),
			'subId' => $subId,
			'page' => $page,
		];
	}

    //쿠팡 계정 선택
    switch($subId){
        case "payboocADAPIaos":
        case "payboocADAPIios" :
            $ACCESS_KEY = '37a19127-d99d-4415-b059-937ecaad7a85';
            $SECRET_KEY = 'eac2c8e2ccc7962f736b31b5f8a3f1c009e5212c';
            break;
        case "repayboocADAPIaos" :
        case "repayboocADAPIios" :
            $ACCESS_KEY = '22d1f6c8-ed62-4770-8b46-1096fda2afde';
            $SECRET_KEY = '8dc3c6857f53ad65b00458a14b75645d7be51273';
            break;
    }

	$uri = API_PATH .'?'. http_build_query($param);
	echo $uri.PHP_EOL;
	$message = $datetime.$method.str_replace('?', '', $uri);
	$signature = hash_hmac('sha256', $message, $SECRET_KEY);
	$authorization  = 'CEA algorithm=HmacSHA256, access-key='.$ACCESS_KEY.', signed-date='.$datetime.', signature='.$signature;

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, API_URL . $uri);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
	curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:  application/json;charset=UTF-8', 'Authorization:'.$authorization]);
	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($curl);
	curl_close($curl);
	$result = json_decode($result, JSON_UNESCAPED_UNICODE);
	//pre($result);

	if(!isset($result['rCode']) === false && $result['rCode'] == 0) {
		if(empty($result['data']) === false) {

			foreach($result['data'] as $key => $value) {
				if(!in_array($value['subId'], API_SUB_ID)) {
					continue;
				}
				if(isset($curl_data[$value['date']]) === false) {
					$curl_data[$value['date']][$value['subId']]['gmv'] = 0;
					$curl_data[$value['date']][$value['subId']]['commission'] = 0;
				}
				$curl_data[$value['date']][$value['subId']]['gmv'] += $value['gmv'];
				$curl_data[$value['date']][$value['subId']]['commission'] += $value['commission'];
			}
		}
	}

	//echo "Total CNT : ".count($result['data']).PHP_EOL;
	//갯수가 1000건 일때 무한반복
	if(!empty($result['data']) && count($result['data']) === 1000) {
		$page++;
		coupang_curl($subId, $page);
	}
}

foreach (API_SUB_ID as $subId) {
	coupang_curl($subId);
}

//pre($curl_data);
if(!empty($curl_data)) {
	foreach($curl_data as $date => $value) {
		foreach($value as $subId => $value2) {

			if(!in_array($subId, API_SUB_ID)) {
				continue;
			}

            switch($subId){
                case "payboocADAPIaos":
                    $service_tp_code = "01";
                    break;
                case "payboocADAPIios" :
                    $service_tp_code = "02";
                    break;
                case "repayboocADAPIaos" :
                    $service_tp_code = "05";
                    break;
                case "repayboocADAPIios" :
                    $service_tp_code = "06";
                    break;
            }

			$sql = "
				INSERT INTO ckd_day_coupang_stats SET
					stats_dttm = '{$date}'
					,service_tp_code = '{$service_tp_code}'
					,cancel_amt = '{$value2['gmv']}'
					,cancel_commission = '{$value2['commission']}'
				 ON DUPLICATE KEY UPDATE 
					alt_user_no = 1
					,alt_dttm = now()
					,cancel_amt = '{$value2['gmv']}'
					,cancel_commission = '{$value2['commission']}'
			";
			//pre($sql);
			$NDO->sql_query($sql);
		}
	}
	echo "OK";

} else {
	exit('no_data<br>');
}