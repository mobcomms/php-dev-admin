<?php
/**********************
 *
 *    팝업/푸시관리 - 팝업관리
 *
 **********************/

include "./var.php";

## 환경설정
define('_title_', '팝업관리');
define('_Menu_', 'pop');
define('_subMenu_', 'popup');

include_once __func__;
include_once __head_pop__; ## html 헤더 출력

$sql = "
	SELECT cfg_seq,cfg_nm, cfg_val
	FROM ocb_cfg_info  
	WHERE cfg_seq IN (6,7)
";
$result = $NDO->fetch_array($sql);
//pre($result);

if(!empty($_POST)){
	$new_user = substr($_POST['new_user_save_point_limit'],0,5);
	$sql = "
		update ocb_cfg_info SET cfg_val=:new_user, alt_dttm=NOW()
		WHERE cfg_seq = 6
	";
	$result1 = $NDO->sql_query($sql,[':new_user'=>$new_user]);

	$old_user = substr($_POST['old_user_save_point_limit'],0,5);
	$sql = "
		update ocb_cfg_info SET cfg_val=:old_user, alt_dttm=NOW()
		WHERE cfg_seq = 7
	";
	$result2 = $NDO->sql_query($sql,[':old_user'=>$old_user]);
	if($result1 && $result2){
		$fn->closePOP('','','reload');
	}
}
?>

<div class="contentpanel" style="padding: 0">

	<div class="panel panel-default" style="margin: 0">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title">포인트 적립 제한 </h4>
		</div><!-- panel-heading -->
		<div class="panel-body" style="padding-top: 0;padding-bottom: 0px;">

			<div class="row">
				<div class="panel panel-default">
					<form method="post" onsubmit="return chk_save()">
					<table class="table member_table">
						<colgroup>
							<col width="20%">
							<col width="20%">
						</colgroup>
						<tbody>
						<?php
						foreach ($result as $row){
							switch ($row['cfg_seq']){
								case 6 :
									$title = "신규";
									$input_name = "new_user_save_point_limit";
									$input_value = $row['cfg_val'];
								break;
								case 7 :
									$title = "신규제외";
									$input_name = "old_user_save_point_limit";
									$input_value = $row['cfg_val'];
								break;
							}
						?>
							<tr>
								<td><?=$title?></td>
								<td>
									<div style="line-height: 40px;">
										<input type="number" class="form-control pull-left border-input" style="width: 150px;text-align: center;" name="<?=$input_name?>" value="<?=$input_value?>" maxlength="5" autocomplete="OFF"> P 까지
									</div>
								</td>
							</tr>
						<?php } ?>
							<tr>
								<td colspan="2">
									<button type="button" class="btn btn-danger" onclick="self.close()">취소</button>
									<button type="submit" class="btn btn-primary">적립제한</button>
								</td>
							</tr>
						</tbody>
					</table>
					</form>
				</div>
			</div>

		</div><!-- panel-body -->
	</div><!-- panel panel-default -->
</div><!-- contentpanel -->

<?php
include __foot__;
?>

<script>
	function chk_save() {
		var new_user = $("input[name='new_user_save_point_limit']").val();
		var old_user = $("input[name='old_user_save_point_limit']").val();

		if(!new_user){
			alert("신규포인트를 입력해 주세요");
			return false;
		}
		if(!old_user){
			alert("신규제외 포인트를 입력해 주세요");
			return false;
		}
		return true;
	}
</script>
<form name="frmList" method="POST">
	<input type="hidden" name="idx" value="" />
	<input type="hidden" name="refpage" value="<?=__self__?>" />
	<input type="hidden" name="mode" value="DEL" />
</form>