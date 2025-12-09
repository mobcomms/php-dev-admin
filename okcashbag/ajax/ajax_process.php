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

	Case 'board_switch': ## 게시판 관리 (ON / OFF)
		$mType = $_REQUEST['mType'];
		$mType=($mType=='Y')?'N':'Y';
		$idx = $_REQUEST['idx'];

		$sql = "UPDATE ckd_bbs_okcash SET display_yn=:mType WHERE seq=:idx ";
		$ret = $NDO->sql_query($sql,array(":mType"=>$mType, ":idx"=>$idx));

		echo $mType;
	break;

	Case 'android_ver': ## 환경 설정 (앱버전 관리 - 안드로이드)
		$android_ver = $_REQUEST['android_ver'];
		if(!isset($android_ver)){
			echo "empty";
			exit;
		}
		$sql = "UPDATE ocb_cfg_info SET cfg_val=:android_ver, alt_dttm=now() WHERE cfg_nm='android_ver' ";
		$ret = $NDO->sql_query($sql,array(":android_ver"=>$android_ver));
		if($ret){
			echo "ok";
		}else{
			echo "error";
		}
	break;

	Case 'mobon_YN': ## 환경 설정 (모비온 연동 상태)
		$mobon_YN=($_REQUEST['mType']=='Y')?'N':'Y';

		$sql = "UPDATE ocb_cfg_info SET cfg_val=:mobon_YN, alt_dttm=now() WHERE cfg_nm='mobon_YN' ";
		$ret = $NDO->sql_query($sql,array(":mobon_YN"=>$mobon_YN));

		echo $mobon_YN;
	break;

	Case 'typing_game_YN': ## 환경 설정 (타이핑 게임 상태)
		$mobon_YN=($_REQUEST['mType']=='Y')?'N':'Y';

		$typing_game['status'] = $mobon_YN;
		$typing_game['url'] = $_REQUEST['url'];
		$cfg_val = serialize($typing_game);

		$sql = "UPDATE ocb_cfg_info SET cfg_val=:cfg_val, alt_dttm=now() WHERE cfg_nm='typing_game_YN' ";
		$ret = $NDO->sql_query($sql,array(":cfg_val"=>$cfg_val));

		echo $mobon_YN;
	break;

	Case 'ad_frequency': ## 환경 설정 (모비온 연동 상태)
		function frequency_chk($value){
			if($value === "" || $value == "-1"){
				return "";
			}

			$comma_count = substr_count($value, ",");

			if($comma_count == 0){
				if(is_numeric($value) && $value<=10){
					return $value;
				}else{
					return false;
				}

			}else if($comma_count > 0){
				$str = explode(",",$value);
				$array_count = 0;

				foreach ($str as $array_val){
					if(empty($array_val) || !is_numeric($array_val) || $array_val>10){
						return false;
					}
					$array_count ++;
				}
				if($comma_count+1 != $array_count){
					return false;
				}else{
					return $value;
				}
			}
		}

		$mobon = frequency_chk($_REQUEST['mobon']);
		if($mobon !== "" && $mobon === false){
			echo "error1";
			exit;
		}
		$mediation = frequency_chk($_REQUEST['mediation']);
		if($mediation !== "" && $mediation === false){
			echo "error2";
			exit;
		}
		$banner = frequency_chk($_REQUEST['banner']);
		if($banner !== "" && $banner === false){
			echo "error3";
			exit;
		}
		$coupang = frequency_chk($_REQUEST['coupang']);
		if($coupang !== "" && $coupang === false){
			echo "error4";
			exit;
		}
		$criteo = frequency_chk($_REQUEST['criteo']);
		if($criteo !== "" && $criteo === false){
			echo "error6";
			exit;
		}
		$reward = frequency_chk($_REQUEST['reward']);
		if($reward !== "" && $reward === false){
			echo "error7";
			exit;
		}
		$ratio = $_REQUEST['ratio'];
		if(!$ratio){
			echo "error0";
			exit;
		}else{
			//비율 계산.
			$mobon_count = empty($mobon)?0:count(explode(",",$mobon));
			$mediation_count = empty($mediation)?0:count(explode(",",$mediation));
			$banner_count = empty($banner)?0:count(explode(",",$banner));
			$coupang_count = empty($coupang)?0:count(explode(",",$coupang));
			$criteo_count = empty($criteo)?0:count(explode(",",$criteo));
			$reward_count = empty($reward)?0:count(explode(",",$reward));
			$f_cnt = $mobon_count+$mediation_count+$banner_count+$coupang_count+$criteo_count+$reward_count;
			if($f_cnt*10!=$ratio){
				echo "error0";
				exit;
			}
		}

		//노출 빈도 비율
		$ad_frequency['ratio'] = $ratio;
		//모비온
		$ad_frequency['mobon'] = $_REQUEST['mobon'];
		//외부광고
		$ad_frequency['mediation'] = $_REQUEST['mediation'];
		//배너광고
		$ad_frequency['banner'] = $_REQUEST['banner'];
		//배너광고
		$ad_frequency['coupang'] = $_REQUEST['coupang'];
		//크리테오
		$ad_frequency['criteo'] = $_REQUEST['criteo'];
		//리워드
		$ad_frequency['reward'] = $_REQUEST['reward'];

		$cfg_val = serialize($ad_frequency);
		$sql = "UPDATE ocb_cfg_info SET cfg_val=:cfg_val, alt_dttm=now() WHERE cfg_nm='ad_frequency' ";
		$ret = $NDO->sql_query($sql,array(":cfg_val"=>$cfg_val));
		if($ret){
			echo "ok";
		}
	break;

	Case 'noti_ad': ## 노티설정 저장
		$typing_game['noti_holding_time'] = $_REQUEST['noti_holding_time'];
		$typing_game['noti_point'] = $_REQUEST['noti_point'];

		$cfg_val = serialize($typing_game);

		$sql = "UPDATE ocb_cfg_info SET cfg_val=:cfg_val, alt_dttm=now() WHERE cfg_nm='noti_ad' ";
		$ret = $NDO->sql_query($sql,array(":cfg_val"=>$cfg_val));
		if($ret){
			echo "ok";
		}
	break;

	Case 'ad_reward': ## 광고 리워드 설정
		$typing_game['reward_max_view'] = $_REQUEST['reward_max_view'];
		$typing_game['reward_holding_time'] = $_REQUEST['reward_holding_time'];
		$typing_game['reward_point'] = $_REQUEST['reward_point'];

		$cfg_val = serialize($typing_game);

		$sql = "UPDATE ocb_cfg_info SET cfg_val=:cfg_val, alt_dttm=now() WHERE cfg_nm='ad_reward' ";
		$ret = $NDO->sql_query($sql,array(":cfg_val"=>$cfg_val));
		if($ret){
			echo "ok";
		}
	break;

	Case 'reward_onoff': ## 광고 리워드 설정
		$typing_game['reward_mobon'] = $_REQUEST['reward_mobon'];
		$typing_game['reward_news'] = $_REQUEST['reward_news'];
		$typing_game['reward_coupang'] = $_REQUEST['reward_coupang'];
		$typing_game['reward_criteo'] = $_REQUEST['reward_criteo'];
		$typing_game['reward_moneytree'] = $_REQUEST['reward_moneytree'];

		$cfg_val = serialize($typing_game);

		$sql = "UPDATE ocb_cfg_info SET cfg_val=:cfg_val, alt_dttm=now() WHERE cfg_nm='reward_onoff' ";
		$ret = $NDO->sql_query($sql,array(":cfg_val"=>$cfg_val));
		if($ret){
			echo "ok";
		}
	break;




	Case 'themeMng':	## 테사 상태 관리
		$mType = $_REQUEST['mType'];
		$mType=($mType=='01')?'02':'01';
		$idx = $_REQUEST['idx'];

		$serv_path = __root__."/img/theme";
		$path_thumb = $serv_path.'/thumb/'.ceil($idx/100).'/';
		$path_common = $serv_path.'/common/'.ceil($idx/100).'/';
		$path_custom = $serv_path.'/custom/'.ceil($idx/100).'/';
		if(!file_exists($path_common.'common_theme_'.$idx.'.zip') && !file_exists($path_custom.'custom_theme_'.$idx.'.zip')){
			$mType='01';
			$result="R";
		}else{
			$result=$mType;
		}

		$sql = "update ckd_theme_mng set theme_state='$mType' where theme_seq='$idx' ";
		$ret = $NDO->sql_query($sql);

		echo $result;
	break;

	Case 'themeSort':	## 순서 변경
		$idx = $_REQUEST['idx'];
		$option = ($_REQUEST['option']=='UP')?"ASC":"DESC";
		$symbol = ($_REQUEST['option']=='UP')?">":"<";

		$sql="select theme_seq idx, theme_sort sort from ckd_theme_mng where theme_seq='".$idx."' order by theme_sort $option limit 1";
		//$fn->filelog("theme_sort",$sql);
		$theme=$NDO->getData($sql);


		$sql="select theme_seq idx, theme_sort sort from ckd_theme_mng where theme_sort ".$symbol." ".$theme['sort']." order by theme_sort $option limit 1";
		//$fn->filelog("theme_sort",$sql);
		$sort=$NDO->getData($sql);

		if($sort['idx']){
			$sql="update ckd_theme_mng set theme_sort='".$sort['sort']."' where theme_seq='".$theme['idx']."' ";
			//$fn->filelog("theme_sort",$sql);
			$NDO->getData($sql);

			$sql="update ckd_theme_mng set theme_sort='".$theme['sort']."' where theme_seq='".$sort['idx']."' ";
			//$fn->filelog("theme_sort",$sql);
			$NDO->getData($sql);
		}

		echo "S";
	break;

	Case 'spot_point': ## 스팟성 포인트 ON,OFF
		$use_YN = $_REQUEST['use_YN']=='Y' ? 'N':'Y';
		$code_id = $_REQUEST['code_id'];

		$sql = " UPDATE ocb_spot_point_set SET use_YN='$use_YN', edit_date=NOW() WHERE code_id='$code_id' ";
		//pre($sql);
		$ret = $NDO->sql_query($sql);
		echo $use_YN;
	break;

	Case 'spot_point_save': ## 스팟성 포인트 저장

		$code_id = $_REQUEST['code_id'];
		$start_date = $_REQUEST['start_date'];
		$end_date = $_REQUEST['end_date'];
		$pay_cycle = $_REQUEST['pay_cycle'];
		$term = $_REQUEST['term'];
		$give_point_limit = $_REQUEST['give_point_limit'];
		$user_limit = $_REQUEST['user_limit'];
		$s_give_cnt = $_REQUEST['s_give_cnt'];
		$s_user_max_point_cnt = $_REQUEST['s_user_max_point_cnt'];
		$s_one_time_give_point = $_REQUEST['s_one_time_give_point'];
		$f_first_come_served = $_REQUEST['f_first_come_served'];
		$f_give_point_limit = $_REQUEST['f_give_point_limit'];
		$f_user_limit = $_REQUEST['f_user_limit'];
		$first_point = $_REQUEST['first_point'];

		$duplicate_add = "
		 code_id='{$code_id}', start_date='{$start_date}', end_date='{$end_date}', pay_cycle='{$pay_cycle}', term='{$term}', give_point_limit='{$give_point_limit}'  , user_limit='{$user_limit}'  
		 , s_give_cnt='{$s_give_cnt}'  , s_user_max_point_cnt='{$s_user_max_point_cnt}'  , s_one_time_give_point='{$s_one_time_give_point}' 
		 , f_first_come_served='{$f_first_come_served}'  , f_give_point_limit='{$f_give_point_limit}'  , f_user_limit='{$f_user_limit}'  , first_point='{$first_point}'
		 ";
		$sql = "
			INSERT INTO ocb_spot_point_set SET {$duplicate_add}, reg_date=NOW()
			ON DUPLICATE KEY UPDATE  {$duplicate_add}, edit_date=NOW() 
		";
		//pre($sql);
		$result = $NDO->sql_query($sql);
		if ($result) {
			echo "ok";
		} else {
			echo "error";
		}
	break;

	Case 'offerwall_show': ## 오퍼월 버튼 설정
		$offerwall_show = $_REQUEST['offerwall_show'];

		$sql = "UPDATE ocb_cfg_info SET cfg_val=:offerwall_show, alt_dttm=now() WHERE cfg_nm='offerwall_view' ";
		$ret = $NDO->sql_query($sql,array(":offerwall_show"=>$offerwall_show));
		if($ret){
			echo "ok";
		}
	break;

	Case 'offerwall_logic': ## 오퍼월 로직 설정
		$mType = $_REQUEST['mType'];

		$sql = "UPDATE ocb_cfg_info SET cfg_val=:offerwall_show, alt_dttm=now() WHERE cfg_nm='offerwall_logic' ";
		$ret = $NDO->sql_query($sql,array(":offerwall_show"=>$mType));
		if($ret){
			echo "ok";
		}
	break;




	default : break;
}
