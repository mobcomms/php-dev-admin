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

if(!empty($_POST) && !empty($_POST['month'])){
	$url = "https://valuewalk-api.commsad.com/API/cron/mobwithad_data.php?postman=postman&date=".$_POST['month'];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($ch);
	//pre($result);
	curl_close($ch);


	$url = "https://valuewalk-api.commsad.com/API/cron/adjustment_data.php?postman=postman&date=".$_POST['month'];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($ch);
	//pre($result);
	curl_close($ch);
?>
	<script>
		self.close();
		opener.document.location.reload();
	</script>
<?php
	exit;
}
?>

<div class="contentpanel" style="padding: 0">

	<div class="panel panel-default" style="margin: 0">
		<div class="panel-heading">
			<h4 class="panel-title">모비위드 데이터 재호출</h4>
		</div><!-- panel-heading -->
		<div class="panel-body" style="padding-top: 0;padding-bottom: 0px;">

			<div class="row">
				<div class="panel panel-default">
					<form method="post" onsubmit="return chk_save()">
					<table class="table member_table" style="height: 168px;">
						<colgroup>
							<col width="20%">
							<col width="20%">
						</colgroup>
						<tbody>
							<tr>
								<td>데이터를 변경할 달</td>
								<td>
									<div style="line-height: 50px;">
										<select name="month" id="month" class="form-control pull-left border-input">
											<?php
											if(date("d") < 3){
												$d = mktime(0,0,0, date("m"), 1, date("Y")); //이번달 1일
												$prev_month = date("m",strtotime("-1 month", $d)); //한달전
											?>
												<option value="<?=$prev_month?>"><?=$prev_month?>월</option>
											<?php } ?>
											<option value="<?=date("m")?>"><?=date("m")?>월</option>
										</select>
									</div>
								</td>
							</tr>

							<tr>
								<td colspan="2">
									<button type="button" class="btn btn-danger" onclick="self.close()">취소</button>
									<button type="submit" class="btn btn-primary">적용</button>
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
