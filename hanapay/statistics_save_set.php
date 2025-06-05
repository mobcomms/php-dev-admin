<?php
/**********************
 *
 *    적립 설정
 *
 **********************/
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors", 1);
## 환경설정
define('_title_', '적립 설정');
define('_Menu_', 'save_set');
define('_subMenu_', 'save_set');

include "./var.php";
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성
include_once __head__; ## html 헤더 출력

if($admin['level'] != 'super'){
	hist("권한이 없습니다.");
}

$sql="SELECT probability_config, bonus_box_amount, prize_config, description FROM moneybox_config WHERE moneybox_config_seq=1";
$set_info = $NDO->getData($sql);
$description = stripslashes(str_replace('\n',PHP_EOL,$set_info['description']));

$reward_point_info = json_decode($set_info['probability_config'], true);
?>
<div class="pageheader">
	<h4>적립설정</h4>
</div>
<div class="contentpanel">
	<div class="panel panel-default">
		<div class="panel-heading" style="height: 60px;">
			<div class="panel-btns">
<!--				<a href="" class="panel-close">&times;</a>-->
				<a href="" class="minimize">&minus;</a>
<!--				<a href="" class="minimize maximize" style="padding-top: 2px;">+</a>-->
			</div><!-- panel-btns -->
			<h4 class="panel-title">
				<div style="float: left;line-height: 27px;padding-right: 20px;">머니박스</div>
<?php /* ?>
				<div class="btn-group btn-toggle use_YN" style="margin-bottom: 0;" data-otpc="<?=$config_info?>" data-code_id="1">
					<button class="btn btn-xs <?=$config_info=="Y"?"btn-primary active":"btn-default"?>" type="button" value="Y" style="z-index: 0;">ON </button>
					<button class="btn btn-xs <?=$config_info=="N"?"btn-primary active":"btn-default"?>" type="button" value="N" style="z-index: 0;">OFF </button>
				</div>
<?php */ ?>
			</h4>
		</div><!-- panel-heading -->

		<div class="panel-body">
			<table class="table member_table">
				<tbody>
				<td>
					<span style="color: red">총 확률 [<span id="total"></span>] % / 총 5가지</span> 확률 경우
				</td>
				</tbody>
			</table>

			<h4 class="panel-title">
				<div style="float: left;line-height: 27px;padding-right: 20px;">머니박스 설정</div>
			</h4>
			<div class="table-responsive">
				<table class="table member_table">
					<tbody>
					<tr>
						<td style="background-color: #ddd;">리워드 포인트</td>
						<td>
							<div>
								<div class="col-sm-2" style="line-height:35px;width:200px;">
									<input type="number" class="form-control pull-left border-input" style="width:75px; height:35px;" autocomplete="off" name="odds_set_point[0]" value="<?=$reward_point_info[0]['point']?>">
									<div class="col-sm-3" style="line-height:35px;">머니</div>
								</div>
								<div class="col-sm-2" style="line-height:35px;width:200px;">
									<input type="number" class="form-control pull-left border-input" style="width:75px; height:35px;" autocomplete="off" name="odds_set_point[1]" value="<?=$reward_point_info[1]['point']?>">
									<div class="col-sm-3" style="line-height:35px;">머니</div>
								</div>
								<div class="col-sm-2" style="line-height:35px;width:200px;">
									<input type="number" class="form-control pull-left border-input" style="width:75px; height:35px;" autocomplete="off" name="odds_set_point[2]" value="<?=$reward_point_info[2]['point']?>">
									<div class="col-sm-3" style="line-height:35px;">머니</div>
								</div>
								<div class="col-sm-2" style="line-height:35px;width:200px;">
									<input type="number" class="form-control pull-left border-input" style="width:75px; height:35px;" autocomplete="off" name="odds_set_point[3]" value="<?=$reward_point_info[3]['point']?>">
									<div class="col-sm-3" style="line-height:35px;">머니</div>
								</div>
								<div class="col-sm-2" style="line-height:35px;width:200px;">
									<input type="number" class="form-control pull-left border-input" style="width:75px; height:35px;" autocomplete="off" name="odds_set_point[4]" value="<?=$reward_point_info[4]['point']?>">
									<div class="col-sm-3" style="line-height:35px;">머니</div>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td style="background-color: #ddd;">포인트 당첨확률 </td>
						<td>
							<div>
								<div class="col-sm-2" style="line-height:35px;width:200px;">
									<input type="number" class="form-control pull-left border-input" style="width:75px; height:35px;" autocomplete="off" name="odds_set_percent[0]" value="<?=$reward_point_info[0]['probability']*100?>">
									<div class="col-sm-1" style="line-height:35px;">%</div>
								</div>
								<div class="col-sm-2" style="line-height:35px;width:200px;">
									<input type="number" class="form-control pull-left border-input" style="width:75px; height:35px;" autocomplete="off" name="odds_set_percent[1]" value="<?=$reward_point_info[1]['probability']*100?>">
									<div class="col-sm-1" style="line-height:35px;">%</div>
								</div>
								<div class="col-sm-2" style="line-height:35px;width:200px;">
									<input type="number" class="form-control pull-left border-input" style="width:75px; height:35px;" autocomplete="off" name="odds_set_percent[2]" value="<?=$reward_point_info[2]['probability']*100?>">
									<div class="col-sm-1" style="line-height:35px;">%</div>
								</div>
								<div class="col-sm-2" style="line-height:35px;width:200px;">
									<input type="number" class="form-control pull-left border-input" style="width:75px; height:35px;" autocomplete="off" name="odds_set_percent[3]" value="<?=$reward_point_info[3]['probability']*100?>">
									<div class="col-sm-1" style="line-height:35px;">%</div>
								</div>
								<div class="col-sm-2" style="line-height:35px;width:200px;">
									<input type="number" class="form-control pull-left border-input" style="width:75px; height:35px;" autocomplete="off" name="odds_set_percent[4]" value="<?=$reward_point_info[4]['probability']*100?>">
									<div class="col-sm-1" style="line-height:35px;">%</div>
								</div>
							</div>
						</td>
					</tr>
					</tbody>
				</table>

				<h4 class="panel-title">
					<div style="float: left;line-height: 27px;padding-right: 20px;">보너스박스 설정</div>
				</h4>
				<table class="table">
					<tbody>
						<td style="width:125px;background-color: #ddd;">보너스 박스</td>
						<td>
<!--							<div class="col-sm-1" style="line-height:35px;">-->
<!--								<label style="margin-right:0;cursor: pointer;"><input type="radio" name="bonusYN" checked="" value="Y" style="vertical-align: middle;height:17px;width:17px;margin:5px">사용</label>-->
<!--							</div>-->
							<div class="col-sm-2" style="line-height:35px;">
								<input type="number" id="config_info" class="form-control pull-left border-input" style="width:75px; height:35px;" autocomplete="off" name="config_info" value="<?=$set_info['bonus_box_amount']?>">
								<div class="col-sm-3" style="line-height:35px;">머니</div>
							</div>
<!--							<div class="col-sm-1" style="line-height:35px;">-->
<!--								<label style="margin-right:0;cursor: pointer;"><input type="radio" name="bonusYN" checked="" value="N" style="vertical-align: middle;height:17px;width:17px;margin:5px">미사용</label>-->
<!--							</div>-->
						</td>
					</tbody>
				</table>

				<table>
					<tbody>
					<tr><td><?=nl2br($description)?></td></tr>
					</tbody>
				</table>

				<div style="text-align: center;padding-top: 50px;"><button class="btn btn-success save" data-code_id="1" style="margin-left:10px;">해당 영역 부분 저장</button></div>
			</div><!-- table-responsive -->
		</div><!-- panel-body -->
	</div><!-- panel -->


<script>
//확률 합계 체크
function chk_sum(){
	var sum_value = 0;
	$("input[name^='odds_set_percent']").each(function () {
		sum_value+=Number($(this).val());
	});
	$("#total").text(sum_value.toFixed(2));
	if(sum_value.toFixed(2) !=100){
		return false;
	}else{
		return true;
	}
}
//보너스박스 사용 유무
function bonus_box(){
	if($("input[name='bonusYN']:checked").val() == "Y"){
		$("#odds_bonus_percent").attr('readonly', false);
	}else{
		$("#odds_bonus_percent").attr('readonly', true);
	}
}

$(document).ready(function(){
	//퍼센트 합계 체크
	chk_sum();
	$("input[name^='odds_set_percent']").on("change",function() {
		chk_sum();
	});
	//보너스박스 사용 유무
	bonus_box();
	$("input[name='bonusYN']").on("change", function(){
		bonus_box();
	});

	//리워드 저장
	$(".use_YN, .save").on("click",function(){
		if(!chk_sum()){
			alert("당첨확률의 합이 100이 아닙니다. ");
			return;
		}

		var description = $("textarea").val();
		var odds_set_point = [];
		$("input[name^='odds_set_point']").each(function(){
			odds_set_point.push(Number($(this).val()));
		});
		var odds_set_percent = [];
		$("input[name^='odds_set_percent']").each(function(){
			odds_set_percent.push(Number($(this).val()));
		});

		var config_info = $("#config_info").val();
		var formData = {mode:"ad_reward",odds_set_point:odds_set_point,odds_set_percent:odds_set_percent,config_info:config_info, description:description};
		$.post("./ajax/ajax_process.php",formData,function(result){
			if(result === "ok"){
				alert("적용 되었습니다.");
				location.reload();
			}else {
				alert("수정 실패");
			}
		},"html");
	});

});
</script>

<?php
include __foot__;
?>
