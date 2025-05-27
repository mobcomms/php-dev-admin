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
		$code_id = $_REQUEST['code_id'];
		$reward_unit = $_REQUEST['reward_unit'];
		$reward_point = $_REQUEST['reward_point'];
		$otpc_value = $_REQUEST['otpc_value'];

		$add_fild = "";
		if(!empty($otpc_value)){
			$add_fild = ",useYN='{$otpc_value}'";
		}

		$sql = "UPDATE api_point_setting SET unit='{$reward_unit}', point='{$reward_point}' {$add_fild}, mod_date=now() WHERE type='{$code_id}' ";
		$ret = $NDO->sql_query($sql);

		if($otpc_value == "Y"){
			$add_query = ",start_dttm=NOW()";
		}else if($otpc_value == "N"){
			$add_query = ",stop_dttm=NOW()";
		}else{
			$add_query = "";
		}
		$sql = "
			INSERT INTO ckd_point_set_history SET type='{$code_id}', reward_unit='{$reward_unit}', reward_point='{$reward_point}' {$add_query}
		";
		//pre($sql);
		$result = $NDO->sql_query($sql);
		if($result){
			echo "ok";
		}
	break;

	default : break;
}
