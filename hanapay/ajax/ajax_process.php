<?php
/******************************************
*
*  ajax 사용하여 처리하는 페이지
*
* *****************************************/

include_once '../var.php';
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성

if (empty($_SESSION['Adm']['idx']) || empty($_SESSION['Adm']['id'])) {
	exit();
}

$mode = $_REQUEST['mode'];
Switch($mode) {

	Case 'ad_reward': ## 광고 리워드 설정
		$reward_unit = $_REQUEST['odds_set_point'];
		$reward_point = $_REQUEST['odds_set_percent'];
		$config_info = $_REQUEST['config_info'];
		//$description = $_REQUEST['description'];

		$reward_point_info = [];
		foreach($reward_unit as $key=>$row){
			$reward_point_info[$key]['point'] = $row;
			$reward_point_info[$key]['probability'] = $reward_point[$key]/100;
		}
		$reward_point_info = json_encode($reward_point_info, JSON_NUMERIC_CHECK);

		//$description = str_replace('""','\"',$description);
		//$description = str_replace(PHP_EOL,'\n',$description);

		//확률 업데이트
		$sql = "UPDATE api_moneybox_config SET probability_config='{$reward_point_info}', bonus_box_amount='{$config_info}', mod_date=now() WHERE config_seq=1";
		$ret = $NDO->sql_query($sql);

		if($ret){
			echo "ok";
		}
	break;

	default : break;
}
