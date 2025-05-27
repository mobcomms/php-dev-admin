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
$file = "";
$mode = $_REQUEST['mode'];
Switch($mode) {

	Case 'set_banner': ## 광고 설정

		$path = "";
		$allowedExts = array("gif", "jpeg", "jpg", "png");
		foreach($_FILES as $key=>$value) {

			if (!empty($_FILES[$key]['name'])) {
				if ($_FILES[$key]['error'] == '0') {

					$path = __root__."img/banner/";
					$url_path = "img/banner/";

					if (file_exists($path) != true) {
						mkdir($path, 0755);
					}

					if (!is_dir($path)) {
						pre($path);
						exit("Upload directory does not exist.");
					}

					if (!is_writable($path)) {
						pre($path);
						exit("Upload directory is not writable.");
					}

					$size = $_FILES[$key]["size"];
					$name = $_FILES[$key]['name'];
					$temp = explode(".", $name);
					$extension = end($temp);
					$max_size = 1024*1024*40; //40 메가

					if($size > $max_size) {
						Hist(($size/1024/1024) . " Mbyte is bigger than ".($max_size/1024/1024)." Mb ");
						exit;
					}

					if(!in_array($extension, $allowedExts)){
						Hist("이미지 파일만 업로드 가능 합니다.");
						exit;
					}

					$file = $key.".".$extension;
					if (move_uploaded_file($_FILES[$key]['tmp_name'], $path . strtolower($file)) == true) {

					} else {
						$text = file_err_chk($_FILES[$key]['error']);
						exit($fn->loca($go_page."?idx=".$idx."배너 이미지 ".$text));
					}
				} else {
					exit($fn->hist("이미지 파일 업로드 시 오류가 발생하였습니다.\\n" . $file . "\\n다시 등록해 주세요.."));
				}
			}
		}

		if(!empty($file)){
			$sql = "UPDATE ckd_banner_set SET useYN=:useYN, img=:img, font_color=:font_color, unit=:unit, point=:point, max_point=:max_point, frequency=:frequency, edit_date=now() WHERE type='1' ";
			$ret = $NDO->sql_query($sql,array(":useYN"=>$_REQUEST['useYN'],":img"=>"https://shinhancard-api.commsad.com/img/banner/".$file,":font_color"=>$_REQUEST['font_color'],":unit"=>$_REQUEST['reward_unit'],":point"=>$_REQUEST['reward_point'],":max_point"=>$_REQUEST['reward_max_point'],":frequency"=>$_REQUEST['reward_frequency']));
		}else{
			$sql = "UPDATE ckd_banner_set SET useYN=:useYN, font_color=:font_color, unit=:unit, point=:point, max_point=:max_point, frequency=:frequency, edit_date=now() WHERE type='1' ";
			$ret = $NDO->sql_query($sql,array(":useYN"=>$_REQUEST['useYN'],":font_color"=>$_REQUEST['font_color'],":unit"=>$_REQUEST['reward_unit'],":point"=>$_REQUEST['reward_point'],":max_point"=>$_REQUEST['reward_max_point'],":frequency"=>$_REQUEST['reward_frequency']));
		}

		if($_REQUEST['useYN'] == "Y"){
			$add_query = ",start_dttm=NOW()";
		}else if($_REQUEST['useYN'] == "N"){
			$add_query = ",stop_dttm=NOW()";
		}else{
			$add_query = "";
		}
		$sql = "
			INSERT INTO ckd_banner_set_history SET type='1', reward_unit='{$_REQUEST['reward_unit']}', reward_point='{$_REQUEST['reward_point']}', reward_max_point='{$_REQUEST['reward_max_point']}', reward_frequency='{$_REQUEST['reward_frequency']}' , img_name='{$file}', font_color='{$_REQUEST['font_color']}'
			{$add_query}
		";
		//pre($sql);
		$result = $NDO->sql_query($sql);

		if($result){
			echo "ok";
		}

		# 히스토리 저장
	break;

	Case 'reward_onoff': ## 광고 리워드 설정
		$typing_game['reward_mobon'] = $_REQUEST['reward_mobon'];
		$typing_game['reward_news'] = $_REQUEST['reward_news'];
		$typing_game['reward_coupang'] = $_REQUEST['reward_coupang'];
		$typing_game['reward_criteo'] = $_REQUEST['reward_criteo'];
		$typing_game['reward_moneytree'] = $_REQUEST['reward_moneytree'];

		$cfg_val = serialize($typing_game);

		$sql = "UPDATE ckd_cfg_info SET cfg_val=:cfg_val, alt_dttm=now() WHERE cfg_nm='reward_onoff' ";
		$ret = $NDO->sql_query($sql,array(":cfg_val"=>$cfg_val));
		if($ret){
			echo "ok";
		}
	break;




	default : break;
}
