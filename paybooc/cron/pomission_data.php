<?php
/*************************************************
 *
 *      포미션(오퍼월) 통계 api
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

//토큰발급
$media_id = "paybooc";
$version = 1;
$default_token = "eyJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MTE1OTYyOTB9.nGOJmtBYn2pHGIs6YLKeIMlRcYTHX_wzw5c8eUyfc4Y";

$pomission_domain = "https://pri-api.pomission.com";
$url = "{$pomission_domain}/api/v1/common/getAccessToken?media_id={$media_id}";
$header_data = [];
$header_data[] = "x-refresh-token:{$default_token}";
$result = $fn->pomission_curl($url,$header_data);
$result = json_decode($result, true);
//pre($result);

if(empty($result) || $result['result']!=0){

	$_JsonData['Result'] = "false";
	$_JsonData['errcode'] = "99";
	$_JsonData['errstr'] = "토큰 생성 실패";
	$_jecData = json_encode($_JsonData);
	exit($_jecData);
}
$token = $result['token'];

function get_pomission_data($report_date){
	global $NDO, $fn, $version, $token, $pomission_domain;
	$url = "{$pomission_domain}/api/v{$version}/mission/dailyReport?report_date={$report_date}";
	$header_data = [];
	$header_data[] = "x-access-token:{$token}";
	$result2 = $fn->pomission_curl($url, $header_data);
	$result2 = json_decode($result2, true);
	//pre($result2);
	if($result2['result'] === 0){
		$participation = $result2['report']['participation'];
		$click = $result2['report']['click'];
		$expense = $result2['report']['expense'];
	}else{
		$participation = 0;
		$click = 0;
		$expense = 0;
	}
	$sql = "
		INSERT INTO ckd_day_ad_stats SET 
			stats_dttm = '{$report_date}'
			,service_tp_code = '07'
			,partner_no = '0' 
			,eprs_num='{$participation}'
			,click_num='{$click}'
			,exhs_amt='{$expense}'
			,reg_dttm=NOW()
		ON DUPLICATE KEY UPDATE eprs_num='{$participation}', click_num='{$click}', exhs_amt='{$expense}', alt_dttm=NOW()
	";
	//pre($sql);
	$NDO->sql_query($sql);
}

$max_day = 2;//갱신할 날짜
$report_date = empty($_REQUEST['date'])?"":$_REQUEST['date'];
if(empty($report_date)){
	$report_date = date('Ymd');
}
for ($i=1;$i<=$max_day; $i++){
	$date_array[] = $report_date;
	$report_date = date('Ymd', strtotime($report_date.' -1 day'));
}
foreach ($date_array as $date){
	get_pomission_data($date);
}

echo "OK";