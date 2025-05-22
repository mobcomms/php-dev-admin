<?php
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors", 1);

include 'var.php';
include __fn__;
include_once __func__;

function file_err_chk($error_no){
	switch($error_no){
		case 1:
		case 2: $text="업로드한 파일이 용량 초과입니다.";break;
		case 3: $text="파일이 일부분만 전송되었습니다.";break;
		case 4: $text="파일이 전송되지 않았습니다.";break;
		case 6: $text="파일 임시 폴더가 없습니다.";break;
		case 7: $text="디스크에 파일 쓰기를 실패했습니다.";break;
		case 8: $text="확장에 의해 파일 업로드가 중지되었습니다.";break;
		default:$text=$error_no." 업로드 실패.";break;
	}
	return $text;
}

$admin = $fn->chk_admin($_SESSION, 'Adm');

if(
	REFER($_POST['refpage'], '/finnq/board_brand_write.php') != true &&
	REFER($_POST['refpage'], '/finnq/board_brand_util_write.php') != true &&
	REFER($_POST['refpage'], '/finnq/board_banner_write.php') != true &&
	REFER($_POST['refpage'], '/finnq/board_promotion_write.php') != true &&
	REFER($_POST['refpage'], '/finnq/board_noti_write.php') != true &&
	REFER($_POST['refpage'], '/finnq/theme_write.php') != true &&
	REFER($_POST['refpage'], '/finnq/theme.php') != true &&

	REFER($_POST['refpage'], 'logout') != true
) {
	Hist("페이지 접근 권한이 없습니다");
	exit;
}

Switch($_POST['refpage']) {

	default:    ## 이외 리다이렉트

		Alert("권한이 없습니다");
		Loca("/finnq/login.php");
		exit;

	break;

	## 브랜드, 고정광고 ,프로모션 등록, 노티 추가
	Case '/finnq/board_brand_write.php':
	Case '/finnq/board_brand_util_write.php':
	Case '/finnq/board_banner_write.php':
	Case '/finnq/board_promotion_write.php':
	Case '/finnq/board_noti_write.php':

		if($_POST['refpage'] =='/finnq/board_brand_write.php'){
			$go_page = "board_brand_write.php";
			$go_page_l = "board_brand.php";
		}else if($_POST['refpage'] =='/finnq/board_brand_util_write.php'){
			$go_page = "board_brand_util_write.php";
			$go_page_l = "board_brand_util.php";
		}else if($_POST['refpage'] =='/finnq/board_banner_write.php'){
			$go_page = "board_banner_write.php";
			$go_page_l = "board_banner.php";
		}else if($_POST['refpage'] =='/finnq/board_promotion_write.php'){
			$go_page = "board_promotion_write.php";
			$go_page_l = "board_promotion.php";
		}else if($_POST['refpage'] =='/finnq/board_noti_write.php'){
			$go_page = "board_noti_write.php";
			$go_page_l = "board_noti.php";
		}

		$idx = ret(INPUT_POST, 'idx');
		$mode = ret(INPUT_POST, 'mode');
		$type = ret(INPUT_POST, 'type');
		$page = ret(INPUT_POST, 'page');

		$title = ret(INPUT_POST, 'title');

		$sdate = ret(INPUT_POST, 'sdate');
		$s_hour = ret(INPUT_POST, 's_hour');
		$s_min = ret(INPUT_POST, 's_min');
		$start_date = $sdate." ".$s_hour.":".$s_min.":00";

		$edate = ret(INPUT_POST, 'edate');
		$e_hour = ret(INPUT_POST, 'e_hour');
		$e_min = ret(INPUT_POST, 'e_min');
		$end_date = $edate." ".$e_hour.":".$e_min.":00";

		$url = ret(INPUT_POST, 'url');

		if($_POST['refpage'] =='/finnq/board_noti_write.php'){
			$s_hour = ret(INPUT_POST, 's_hour');
			$s_min = ret(INPUT_POST, 's_min');
			$url = $s_hour.":".$s_min;
		}

		$more_url = ret(INPUT_POST, 'more_url');
		$display_yn = empty(ret(INPUT_POST, 'display_yn'))? "N":ret(INPUT_POST, 'display_yn');
		$default_yn = empty(ret(INPUT_POST, 'default_yn'))? "N":ret(INPUT_POST, 'default_yn');

		$contents = ret(INPUT_POST, 'contents');

		$serv_path = __root__."img/{$type}";
		$type_path = "img/{$type}";

		include_once __pdoDB__;

		if ($mode == 'DEL') {

			$sql = "UPDATE ckd_bbs SET del_yn='Y' WHERE seq='{$idx}'";
			$ret = $NDO->sql_query($sql);
			if ($ret) {
				exit($fn->loca($go_page_l,'삭제 되었습니다.'));
			} else {
				exit('삭제 시 오류가 발생하였습니다. 다시 시도해 주세요.');
			}

		} else if ($mode == 'write') {
			//게시판 저장
			$sql = "
				INSERT INTO ckd_bbs (
					seq,title,type,start_date,end_date,url,more_url,display_yn,default_yn,contents,reg_user
				) VALUES (
					'{$idx}', '{$title}','{$type}','{$start_date}','{$end_date}','{$url}','{$more_url}','{$display_yn}','{$default_yn}','{$contents}','{$admin['id']}'
				) ON DUPLICATE KEY UPDATE
					title='{$title}',
					type='{$type}',
					start_date='{$start_date}',
					end_date='{$end_date}',
					url='{$url}',
					more_url='{$more_url}',
					display_yn='{$display_yn}',
					default_yn='{$default_yn}',
					contents='{$contents}',
					edit_user= '{$admin['id']}',
					editdate= now()
			";
			//$fn->debug($sql);
			$ret = $NDO->sql_query($sql);

			if(empty($idx)){
				$sql = "select seq from ckd_bbs order by seq desc limit 1";
				$temp = $NDO->getData($sql);
				$idx = $temp['seq'];
			}

			if (!$ret) {
				exit('등록 시 오류가 발생하였습니다.\n\n다시 시도해 주세요.');
			}

			$allowedExts = array("gif", "jpeg", "jpg", "png");
			foreach($_FILES as $key=>$value) {

				if (!empty($_FILES[$key]['name'])) {
					if ($_FILES[$key]['error'] == '0') {

						$path = $serv_path . '/' . date("Y") . '/';
						$url_path = $type_path . '/' . date("Y") . '/';

						if (file_exists($serv_path) != true) {
							mkdir($serv_path, 0755);
						}

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

						$file = $key."_". $idx .".".$extension;

						if (move_uploaded_file($_FILES[$key]['tmp_name'], $path . strtolower($file)) == true) {

							$qry ="
								service_tp_code='03', type='{$type}', board_idx='{$idx}', file_input_name='{$key}',
								file_path = '{$url_path}',
								file_name = '{$file}',
								ori_file_name = '{$_FILES[$key]['name']}',
							";
							$sql = "
								INSERT INTO ckd_file_upload SET 
									{$qry}
									reg_date = NOW()
								ON DUPLICATE KEY UPDATE
									{$qry}
									alt_date = now()
							";
							//pre($sql);
							$ret = $NDO->sql_query($sql);
						} else {
							$img_type = $key=='util_icon_img' ? "유틸아이콘":"광고";
							$text = file_err_chk($_FILES[$key]['error']);
							exit($fn->loca($go_page."?idx=".$idx, $img_type." 이미지 ".$text));
						}
					} else {
						exit($fn->hist("이미지 파일 업로드 시 오류가 발생하였습니다.\\n" . $file . "\\n다시 등록해 주세요.."));
					}
				}
			}

			if($ret) {
				if($idx){
					exit($fn->loca("{$go_page}?idx=".$idx ,'정상적으로 등록되었습니다.'));
				}else{
					exit($fn->loca($go_page,'정상적으로 등록되었습니다.'));
				}
			} else {
				//$fn->debug($sql);
				exit('등록 시 오류가 발생하였습니다.\n\n다시 시도해 주세요.');
			}
		} else if ('img_download') {

			$file_idx = $_POST['file_idx'];

			if (empty($idx) === true) {
				exit($fn->hist('이미지 정보가 없습니다.'));
			}

			$sql = "
				SELECT file_path, file_name FROM ckd_file_upload 
				WHERE idx='{$file_idx}'
			";
			$ret = $NDO->fetch_array($sql);
			foreach ($ret as $row){
				$result = $row;
			}
			if(empty($result)){
				exit($fn->hist('파일정보가 없습니다.'));
			}

			$root_path = __root__.$result['file_path'].$result['file_name'];
			if (!file_exists($root_path) === true) {
				exit($fn->hist('서버에 파일정보가 없습니다.'));
			}
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . $result['file_name'] . '"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($root_path));
			readfile($root_path);
			exit;
		}
	break;



	Case '/finnq/theme_write.php' : ## 테마등록

		$idx = ret(INPUT_POST, 'idx');
		$mode = ret(INPUT_POST, 'mode');
		$download_type = ret(INPUT_POST, 'download_type');
		$theme_title = ret(INPUT_POST, 'theme_title');
		$theme_cnts = htmlspecialchars($_POST['theme_cnts']);

		$theme_tp_code = ret(INPUT_POST, 'theme_tp_code');
		$theme_tp_code = $theme_tp_code ? $theme_tp_code : '00';
		$partner_code = ret(INPUT_POST, 'partner_code');
		if(empty($partner_code)){
			$partner_code = "00";
		}

		//$theme_cate = ret(INPUT_POST, 'theme_cate');
		$theme_cate = empty($_POST['theme_cate'])?[]:$_POST['theme_cate'];

		$page = ret(INPUT_POST, 'page');

		$img = array();
		$img2 = array();
		$wide_img = array();

		$app_icon_img='';
		$qlink_logo_img='';

		include_once __pdoDB__;

		if($mode =='write'){

			$sql="select max(theme_seq) mseq from ckd_theme_mng";
			$res = $NDO->getData($sql);
			$tsort=$res['mseq']+1;

			if(!$idx){
				$tdix=$res['mseq']+1;
			}else{
				$tdix=$idx;
			}

			$serv_path = __root__."img/theme";
			$path = $serv_path.'/thumb/'.ceil($tdix/100).'/';
			$path_offerwall = $serv_path.'/thumb_offerwall/'.ceil($tdix/100).'/';

			if(file_exists($path) != true) {
				@mkdir($path, 0755);
			}
			if(file_exists($path_offerwall) != true) {
				@mkdir($path_offerwall, 0755);
			}

			//썸네일
			if(isset($_FILES['theme_thumb']['name'])){
				if(!empty($_FILES['theme_thumb']['name'])){
					if($_FILES['theme_thumb']['error'] == '0') {
						$file = "theme_".$tdix.".png";
						/*
							if (!is_dir($path)) {
								exit("Upload directory does not exist.");
							}

							if (!is_writable($path)) {
								exit("Upload directory is not writable.");
							}
						*/
						if(!move_uploaded_file($_FILES['theme_thumb']['tmp_name'], $path.strtolower($file))) {
							$text = file_err_chk($_FILES['theme_thumb']['error']);
							exit($fn->loca("theme_write.php?idx=".$tdix, "썸네일 ".$text));
						}
					} else {
						$text = file_err_chk($_FILES['theme_thumb']['error']);
						exit($fn->loca("theme_write.php?idx=".$tdix, "썸네일 ".$text));
					}
				}
			}

			//공통 테마
			if(isset($_FILES['common_theme_file']['name'])){
				if(!empty($_FILES['common_theme_file']['name'])){
					$common_path = $serv_path."/common/".ceil($tdix/100)."/";
					if(file_exists($common_path) != true) {
						mkdir($common_path, 0755);
					}

					if($_FILES['common_theme_file']['error'] == '0') {
						$file = "common_theme_".$tdix.".zip";
						if(!move_uploaded_file($_FILES['common_theme_file']['tmp_name'], $common_path.strtolower($file))) {
							$text = file_err_chk($_FILES['common_theme_file']['error']);
							exit($fn->loca("theme_write.php?idx=".$tdix, "공통 테마 ".$text));
						}
					} else {
						$text = file_err_chk($_FILES['common_theme_file']['error']);
						exit($fn->loca("theme_write.php?idx=".$tdix, "공통 테마 ".$text));
					}
				}
			}

			//커스텀 테마
			if(isset($_FILES['custom_theme_file']['name'])){
				if(!empty($_FILES['custom_theme_file']['name'])){
					$custom_path = $serv_path."/custom/".ceil($tdix/100)."/";
					if(file_exists($custom_path) != true) {
						mkdir($custom_path, 755);
					}

					if($_FILES['custom_theme_file']['error'] == '0') {
						$file = "custom_theme_".$tdix.".zip";
						if(!move_uploaded_file($_FILES['custom_theme_file']['tmp_name'], $custom_path.strtolower($file))) {
							$text = file_err_chk($_FILES['custom_theme_file']['error']);
							exit($fn->loca("theme_write.php?idx=".$tdix, "커스텀 테마 ".$text));
						}
					} else {
						$text = file_err_chk($_FILES['custom_theme_file']['error']);
						exit($fn->loca("theme_write.php?idx=".$tdix, "커스텀 테마 ".$text));
					}
				}
			}

			//넘어온 카테고리와 저장된 카테고리 비교
			$sql = "SELECT theme_cate_code FROM ckd_theme_cate WHERE theme_idx='{$idx}'";
			$ret = $NDO->fetch_array($sql);
			$rs = [];
			foreach($ret as $res){
				$rs[] = $res['theme_cate_code'];
			}
			$array_diff  = array_diff($theme_cate, $rs);

			if(count($theme_cate) != count($rs) || $array_diff){
				//기존 카테고리 삭제후 변경된 카테고리 저장.
				$sql = "DELETE FROM ckd_theme_cate WHERE theme_idx='{$idx}'";
				$ret = $NDO->sql_query($sql);

				if($theme_cate){
					foreach($theme_cate as $theme_cate_code){
						if(!empty($qry_values)) $qry_values .= ", ";
						$qry_values .= "('{$idx}','{$theme_cate_code}')";
					}
					$sql = "INSERT INTO ckd_theme_cate (theme_idx, theme_cate_code) VALUES {$qry_values}";
					$ret = $NDO->sql_query($sql);
				}
			}

			//테마
			$sql = "
				INSERT INTO ckd_theme_mng (
					theme_seq,theme_tp_code,theme_partner_code, theme_title,theme_cnts,theme_sort,alt_user_no
				) VALUES (
					'{$tdix}', '$theme_tp_code','$partner_code','$theme_title','$theme_cnts','$tsort','{$_SESSION['Adm']['idx']}'
				) ON DUPLICATE KEY UPDATE
					theme_tp_code='$theme_tp_code',
					theme_partner_code='$partner_code',
					theme_title='$theme_title',
					theme_cnts='$theme_cnts',
					alt_user_no= '{$_SESSION['Adm']['idx']}',
					alt_dttm= now()
			";
			//$fn->debug($sql);
			$ret = $NDO->sql_query($sql);
			if($ret){
				if($idx) exit( $fn->loca('theme_write.php?idx='.$tdix.'&np='.$page, '정상적으로 수정되었습니다.') );
				else exit( $fn->loca('theme.php', '정상적으로 등록되었습니다.') );
			}else{
				//$fn->debug($sql);
				exit( '등록 시 오류가 발생하였습니다.\n\n다시 시도해 주세요.');
			}


		} else if('theme_download') {


			if(empty($idx) === true) {
				exit($fn->hist('테마 정보가 없습니다.'));
			}

			$serv_path = __root__."img/theme";

			switch($download_type){
				case "image" : $path = $serv_path.'/thumb/'.ceil($idx/100).'/';$file = "theme_{$idx}.png";break;
				case "image2" : $path = $serv_path.'/thumb/'.ceil($idx/100).'/';$file = "theme_{$idx}_new.png";break;
				case "image3" : $path = $serv_path.'/thumb_offerwall/'.ceil($idx/100).'/';$file = "theme_{$idx}.png";break;
				case "common_theme" : $path = $serv_path."/common/".ceil($idx/100)."/";$file = "common_theme_{$idx}.zip";break;
				case "custom_theme" : $path = $serv_path."/custom/".ceil($idx/100)."/";$file = "custom_theme_{$idx}.zip";break;
			}

			if(file_exists($path.$file) === false) {
				exit($fn->hist('테마 파일정보가 없습니다.'));
			}

			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$file.'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($path.$file));
			readfile($path.$file);
			exit;
		}
	break;

	Case '/finnq/theme.php' : ## 테마삭제
		$idx = ret(INPUT_POST, 'idx');
		$mode = ret(INPUT_POST, 'mode');
		$serv_path = __root__."img/theme";

		include_once __pdoDB__;

		if($mode == 'DEL') {
			if(empty($idx) === true) {
				exit($fn->hist('테마 번호가 없습니다.'));
			}
			$sql = 'SELECT COUNT(1) cnt FROM ckd_theme_mng where theme_seq = ?';
			$data = $NDO->getData($sql, [$idx]);

			if((int)$data['cnt'] === 0) {
				exit($fn->hist('테마 정보가 없습니다.'));
			}

			$sql = 'DELETE FROM ckd_theme_mng WHERE theme_seq = ?';
			$result = $NDO->sql_query($sql, [$idx]);

			if(!$result) {
				exit($fn->hist('삭제를 실패했습니다. 다시시도해주세요.'));
			}

			//카테고리정보 삭제
			$sql = 'DELETE FROM ckd_theme_cate WHERE theme_idx = ?';
			$result = $NDO->sql_query($sql, [$idx]);

			$directory = "thumb/";
			$file = "theme_".$idx.".png";
			$path = $serv_path.'/'.$directory.ceil($idx/100).'/';
			if(file_exists($path.strtolower($file)) === true) {
				unlink($path.strtolower($file));
			}
			$file = "theme_".$idx."_new.png";
			$path = $serv_path.'/'.$directory.ceil($idx/100).'/';
			if(file_exists($path.strtolower($file)) === true) {
				unlink($path.strtolower($file));
			}
			$directory = "common/";
			$file = "common_theme_".$idx.".zip";
			$path = $serv_path.'/'.$directory.ceil($idx/100).'/';
			if(file_exists($path.strtolower($file)) === true) {
				unlink($path.strtolower($file));
			}
			$directory = "custom/";
			$file = "custom_theme_".$idx.".zip";
			$path = $serv_path.'/'.$directory.ceil($idx/100).'/';
			if(file_exists($path.strtolower($file)) === true) {
				unlink($path.strtolower($file));
			}
			exit($fn->loca('/finnq/theme.php', '삭제되었습니다.'));
		}

	break;


}
