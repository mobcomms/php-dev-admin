<?php
/**********************
 *
 *    포인트지급
 *
 **********************/

include "./var.php";

## 환경설정
define('_title_', '포인트 지급');
define('_Menu_', 'pop');
define('_subMenu_', 'popup');

include_once __func__;
include_once __head_pop__; ## html 헤더 출력

$user_uuid = $_GET['uuid'];

//유저정보
$sql = "
	SELECT *
	FROM ocb_user_info 
	WHERE user_uuid='{$user_uuid}' 
";
//$fn->debug($sql);
$user_info = $NDO->getData($sql);
//pre($user_info);

$sql = "
	SELECT *
	FROM ocb_user_point_today 
	WHERE stats_dttm='".date("Ymd")."' AND user_uuid='{$user_uuid}'  AND event_id = 1
";
//pre($sql);
$point_info = $NDO->getData($sql);
if(empty($point_info)){
	$point_info = [];
	$point_info['point'] = 0;
}
//pre($point_info);


if(!empty($_POST['mode']) && $_POST['mode'] == "SAVE_POINT"){
	$save_point = $_POST['save_point'];

	//사용자 포인트 체크
	$sql="
			SELECT user_give_point FROM ocb_user_info
			WHERE user_uuid = :user_uuid
		";
	$result = $NDO->getData($sql,array(":user_uuid"=>$user_uuid));

	if($result['user_give_point'] + $save_point < 0){
		$fn->hist("회수하려는 포인트가 더 많습니다. 다시 시도해 주세요.");
		exit;
	}
	if($result['user_give_point'] + $save_point > 99){
		$fn->hist("적립 대기 포인트가 99P를 초과하였습니다. 다시 시도해 주세요.");
		exit;
	}

	try {
		$NDO->beginTransaction();

		//사용자 포인트 업데이트
		$sql="
			UPDATE ocb_user_info SET 
			user_give_point = user_give_point+{$save_point}
			,alt_dttm = NOW()
			WHERE user_uuid = :user_uuid
		";
		//pre($sql);
		$result1 = $NDO->trans_query($sql,array(":user_uuid"=>$user_uuid));
		if(!$result1){
			throw new Exception('사용자 회원정보 변경 실패', 89);
		}

		//오늘 쌓은 포인트 누적
		$sql = "
			INSERT INTO ocb_user_point_today SET
			stats_dttm=:date
			,user_uuid=:user_uuid 
			,event_id = 2
			,point = '{$save_point}'
			,reg_dttm=NOW()
			ON DUPLICATE KEY UPDATE
			stats_dttm=:date
			,user_uuid=:user_uuid
			,event_id = 2
			,point = point+'{$save_point}'
			,alt_dttm=NOW()
		";
		$result2 = $NDO->trans_sql($sql,array(":date"=>date("Ymd"), ":user_uuid"=>$user_uuid));
		if(!$result2){
			throw new Exception('오늘 쌓은 포인트 추가 실패', 88);
		}

		$NDO->commit();
		$fn->closePOP("지급 되었습니다.");

	} catch(Exception $e) {
		$NDO->rollback();
		$_JsonData['Result'] = "false";
		$_JsonData['errcode'] = $e->getCode();
		$_JsonData['errstr'] = $e->getMessage();
		$fn->closePOP("errorNo:{$_JsonData['errcode']},{$_JsonData['errstr']}");
		exit;
	}
}

?>

<div class="contentpanel" style="padding: 0">

	<div class="panel panel-default" style="margin: 0">
		<div class="panel-heading">
			<div class="panel-btns">
			</div><!-- panel-btns -->
			<h4 class="panel-title">포인트 지급 </h4>
		</div><!-- panel-heading -->
		<div class="panel-body" style="padding-top: 0;padding-bottom: 0px;">

			<div class="row">
				<div class="panel panel-default">
					<form method="post" onsubmit="return chk_save()">
						<input type="hidden" name="mode" value="SAVE_POINT">
					<table class="table member_table">
						<colgroup>
							<col width="20%">
							<col width="20%">
						</colgroup>
						<tbody>
						<tr>
							<td>오늘 적립받은 포인트</td>
							<td>
								<div style="line-height: 40px;">
									<span style="width: 150px;text-align: center;"><?=$point_info['point']?></span> P
								</div>
							</td>
						</tr>
						<tr>
						
							<tr>
								<td>현재 적립 대기 포인트</td>
								<td>
									<div style="line-height: 40px;">
										<span id="new_user_save_point_limit" class="form-control pull-left border-input" style="width: 150px;text-align: center;"><?=$user_info['user_give_point']?></span> P
									</div>
								</td>
							</tr>
							<tr>
								<td>지급 포인트</td>
								<td>
									<div style="line-height: 40px;">
										<input type="number" class="form-control pull-left border-input" style="width: 150px;text-align: center;" name="save_point" maxlength="2" autocomplete="OFF"> P
									</div>
								</td>
							</tr>

						</tbody>
					</table>
						<div style="color:red">
							* 사용자가 키보드 이용시 적립 대기포인트가 달라질 수 있습니다.
						</div>
						<div>
							* 최대+- 99P까지 지급 가능합니다. <span style="color:red">(양수:지급, 음수:회수)</span>
						</div>
						<div style="height: 50px;">
							* 바로 OCB포인트 적립되지 않고 키보드단에 적립 대기 포인트로 쌓입니다.
						</div>
						<div style="text-align: center;height: 50px;">
							<button type="button" class="btn btn-danger" onclick="self.close()">취소</button>
							<button type="submit" class="btn btn-primary">포인트 지급</button>
						</div>
					</form>
				</div>
			</div>

		</div><!-- panel-body -->
	</div><!-- panel panel-default -->
</div><!-- contentpanel -->

<script>
	function chk_save() {

		var new_user = $("#new_user_save_point_limit").text()*1;
		var old_user = $("input[name='save_point']").val();

		if(!old_user || old_user == 0){
			alert("지급 포인트를 입력 하세요.");
			return false;
		}

		if(old_user > 99-new_user){
			alert(99-new_user+" P 까지 지급 할 수 있습니다.");
			return false;
		}
		return true;
	}

</script>

<?php
include __foot__;
?>
