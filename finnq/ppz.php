<?php
exit;
/**********************************************************
 *
 *      오퍼월 리스트
 *
 ***********************************************************
 *
 * Request Parameter
 *
 *    - scale_type : 기기 스케일
 *    - userid : 사용자 아이디
 *    - partner_code : 파트너 코드 00:후후, 01:OCB
 *
 **********************************************************/
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors", 1);

//뒤로가기 캐쉬 삭제.
header("Progma:no-cache");
header("Cache-Control: no-store, no-cache ,must-revalidate");

include_once './var.php';

include_once __pdoDB__;    ## DB Instance 생성
include_once __fn__;

$p_date = empty($_REQUEST['date'])?"":$_REQUEST['date'];
$p_user_key = empty($_REQUEST['uuid'])?"":urldecode($_REQUEST['uuid']);
$p_user_ad_id = empty($_REQUEST['adid'])?"":$_REQUEST['adid'];

if(empty($p_date)){
	exit("date is NULL");
}

if(empty($p_user_key) && empty($p_user_ad_id)){
}else if(!empty($p_user_key) && !empty($p_user_ad_id)){
}else{
	exit("uuid, adid is NULL");
}

// 포미션(오퍼월) 연동
function pomission_curl($url,$header_data,$params=""){
	$ch = curl_init();                                  //curl 초기화
	curl_setopt($ch, CURLOPT_URL, $url);                //URL 지정하기
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header_data); //header 지정하기
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);     //요청 결과를 문자열로 반환
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	if(!empty($params)){
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	}
	$res = curl_exec($ch);
	//print_r(curl_getinfo($ch));//마지막 http 전송 정보 출력
	//echo curl_errno($ch);//마지막 에러 번호 출력
	//echo curl_error($ch);//현재 세션의 마지막 에러 출력
	curl_close($ch);
	return $res;
}

function get_History($token,$p_date,$p_hour){
	global $domain, $p_user_key, $p_user_ad_id;

	$url = "{$domain}/api/v1/mission/mediaHourHistory?p_date={$p_date}&p_hour={$p_hour}";
	if(!empty($p_user_ad_id)){
		$url .= "&p_user_ad_id={$p_user_ad_id}";
	}
	//pre($url);
	$header_data = [];
	$header_data[] = "x-access-token:{$token}";
	$result_cate = pomission_curl($url,$header_data);
	$result = json_decode($result_cate, true);
	//pre($result);
	return ($result);
}


$domain = "https://api.pomission.com";

//토큰발급
$media_id = "finnq";
$default_token = "eyJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MjM1MTU0MDV9.UQHdN_37a4MJoQAtAjino6xi6Etf8GIo1v7_H-ENS3I";

$url = "{$domain}/api/v1/common/getAccessToken?media_id={$media_id}";
$header_data = [];
$header_data[] = "x-refresh-token:{$default_token}";
$result = pomission_curl($url,$header_data);
$result = json_decode($result, true);
//pre($result);

if(empty($result) || $result['result']!=0){
	$_JsonData['Result'] = "false";
	$_JsonData['errcode'] = "98";
	$_JsonData['errstr'] = "토큰 생성 실패";
	$_jecData = json_encode($_JsonData);
	exit($_jecData);
}
$token = $result['token'];

for($i=0;$i<24;$i++){
	$participation_sum = "";
	$result_list = get_History($token, $p_date, $i);
	pre($result_list);

	if(!empty($result_list) && $result_list['result'] == 0){

		$first_key = array_key_first($result_list['list']);
		$first_value = @$result_list['list'][$first_key];

		$last_key = array_key_last($result_list['list']);
		$last_value = @$result_list['list'][$last_key];

		if(empty($first_value) || empty($last_value)){
			continue;
		}
		foreach ($result_list['list'] as $row){
			if(!empty($participation_sum)){
				$participation_sum .= ",".$row['participation_seq'];
			}else{
				$participation_sum = $row['participation_seq'];
			}
		}

		$sql = "
			SELECT count(*) cnt FROM api_offerwall
			WHERE reg_date_num='{$p_date}' AND ad_id>='{$first_value['participation_seq']}' AND ad_id<='{$last_value['participation_seq']}'
		";
		if(!empty($p_user_key)){
			$sql .= " AND user_uuid='{$p_user_key}' ";
		}
		pre($sql);
		$ret = $NDO->getdata($sql);

		if($result_list['total_count'] == $ret['cnt']){
			echo "$p_date $i 시경 {$result_list['total_count']} 개 OK <br>".PHP_EOL;
		}else if($result_list['total_count'] > $ret['cnt']){
			# 포미션이 갯수가 많을때
			echo "$p_date $i 시경 {$result_list['total_count']} 개 FAIL <br>".PHP_EOL;

			$sql = "
				SELECT ad_id FROM api_offerwall
				WHERE reg_date_num='{$p_date}' AND ad_id>='{$first_value['participation_seq']}' AND ad_id<='{$last_value['participation_seq']}'
			";
			if(!empty($p_user_key)){
				$sql .= " AND user_uuid='{$p_user_key}' ";
			}
			pre($sql);
			$hana_db_result = $NDO->fetch_array($sql);
			pre(count($hana_db_result));

			$participation_array = explode(",",$participation_sum);
			foreach ($hana_db_result as $row){
				$target_key = array_search ($row['participation_seq'] , $participation_array);
				if($target_key !== false ){
					array_splice($participation_array,$target_key,1);
				}
			}
			pre($participation_array);

		}else{
			# 하나가 갯수가 많을때
			echo "$p_date $i 시경 {$result_list['total_count']} 개 FAIL <br>".PHP_EOL;
			$sql = "
				SELECT * FROM api_offerwall
				WHERE reg_date_num='{$p_date}' AND ad_id>='{$first_value['participation_seq']}' AND ad_id<='{$last_value['participation_seq']}' AND ad_id not in({$participation_sum})
			";
			if(!empty($p_user_key)){
				$sql .= " AND user_uuid='{$p_user_key}'";
			}
			pre($sql);
			$ret = $NDO->getdata($sql);
			pre($ret);
		}
	}
	//0.5초 지연
	usleep(500000);
}
exit("END!!");
