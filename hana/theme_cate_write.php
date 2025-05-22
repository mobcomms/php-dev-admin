<?php
/**********************
 *
 *    팝업/푸시관리 - 팝업관리
 *
 **********************/

include "./var.php";

## 환경설정
define('_title_', '팝업관리');
define('_Menu_', 'event');
define('_subMenu_', 'popup');

include_once __func__;
include_once __head_pop__; ## html 헤더 출력

//리스트
if(!empty($_GET['idx'])){
	$sql = "
		SELECT * FROM ckd_com_code
		WHERE code_tp_id='theme_cate_code' AND code_id=:idx
	";
	$row = $NDO->getData($sql, array(":idx" => $_GET['idx']));
}

//등록, 수정
if(!empty($_POST['mode'])){

	$qry = "code_tp_id = 'theme_cate_code', code_id=:code_id, code_val=:code_val, code_desc=:code_desc, use_yn=:use_yn, reg_user_no=:reg_user_no,";
	$pdo_param[":code_id"] = $_POST['code_id'];
	$pdo_param[":code_val"] = $_POST['code_desc'];
	$pdo_param[":code_desc"] = $_POST['code_desc'];
	$pdo_param[":use_yn"] = $_POST['use_yn'];
	$pdo_param[":reg_user_no"] = $_SESSION['Adm']['idx'];

	$qry2 = "code_tp_id = 'theme_cate_code', code_id='{$_POST['code_id']}', code_val='{$_POST['code_desc']}', code_desc='{$_POST['code_desc']}', use_yn='{$_POST['use_yn']}', reg_user_no='{$_SESSION['Adm']['idx']}',";

	//등록
	if($_POST['mode'] == "REG"){
		//존재 확인.
		$sql = "
			SELECT * FROM ckd_com_code WHERE code_tp_id = 'theme_cate_code' AND code_id=:idx
		";
		$row = $NDO->fetch_array($sql, array(":idx" => $_POST['code_id']));
		if($row){
			$fn->hist("이미 등록된 code_id가 있습니다. code_id는 같으면 안됩니다.");
			exit;
		}
		$sql = "
			INSERT INTO ckd_com_code SET {$qry2} reg_dttm=now()
			ON DUPLICATE KEY UPDATE {$qry2} alt_dttm=now()
		";
		$result = $NDO->sql_query($sql);
		if(!empty($result)){
			$fn->replace("theme_cate.php", "등록 되었습니다.");
		}
	}
	//수정
	if($_POST['mode'] == "EDIT"){

		if($_POST['idx'] != $_POST['code_id']){
			//존재 확인.
			$sql = "
				SELECT * FROM ckd_com_code WHERE code_tp_id = 'theme_cate_code' AND code_id=:idx
			";
			$row = $NDO->fetch_array($sql, array(":idx" => $_POST['code_id']));
			if($row){
				$fn->hist("이미 등록된 code_id가 있습니다. code_id는 같으면 안됩니다.");
				exit;
			}
		}else{
			$sql = "
				INSERT INTO ckd_com_code SET {$qry2} reg_dttm=now()
				ON DUPLICATE KEY UPDATE {$qry2} alt_dttm=now()
			";
			$result = $NDO->sql_query($sql);
			if(!empty($result)){
				$fn->replace("theme_cate.php", "수정 되었습니다.");
			}
		}
	}
	exit;
}
?>

<div class="contentpanel">

	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title">카테고리 관리 </h4>
		</div><!-- panel-heading -->
		<div class="panel-body">

			<form id="basicForm" class="form-horizontal" method="post">
				<input type="hidden" name="mode" value="<?=empty($row) ? "REG" : "EDIT"?>">
				<input type="hidden" name="idx" value="<?=empty($row) ? "" : $row['code_id']?>">
				<div class="row">
					<div class="panel panel-default">

						<div class="form-group">
							<label class="col-sm-2 control-label ">카테고리명<span class="asterisk"></span></label>
							<div class="col-sm-4">
								<input type="text" name="code_desc" class="form-control" placeholder=""
									   value="<?=empty($row) ? "" : $row['code_desc']?>" autocomplete="off" required/>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label ">code_id<span class="asterisk"></span></label>
							<div class="col-sm-1">
								<input type="text" name="code_id" class="form-control" placeholder=""
									   value="<?=empty($row) ? "" : $row['code_id']?>" minlength="2" maxlength="2"
									   autocomplete="off" required/>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label ">상태 <span class="asterisk"></span></label>
							<div class="col-sm-10">
								<div class="rdio rdio-default pull-left" style="line-height:18px;">
									<input type="radio" name="use_yn" value="Y"
										   id="radioWarning" <?=empty($row) || $row['use_yn'] == "Y" ? "checked" : ""?> />
									<label for="radioWarning" style="line-height:6px;padding-right:20px;">사용함</label>
								</div>
								<div class="rdio rdio-default pull-left" style="line-height:18px;">
									<input type="radio" name="use_yn" value="N"
										   id="radioSuccess" <?=!empty($row) && $row['use_yn'] == "N" ? "checked" : ""?> />
									<label for="radioSuccess" style="line-height:6px;padding-right:20px;">사용안함</label>
								</div>
							</div>
						</div>

					</div>
				</div>

				<div class="row">
					<div class="text-center">
						<div class="">
							<button class="btn btn-primary"><?=empty($_GET['idx']) ? "등록" : "수정"?></button>
							<button type="reset" class="btn btn-default" onclick="history.back()">취소</button>
						</div>
					</div>
				</div>

			</form>

		</div><!-- panel-body -->
	</div><!-- panel panel-default -->
</div><!-- contentpanel -->

<?php
include __foot__;
?>
