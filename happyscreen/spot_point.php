<?php
/**********************
 *
 *    스팟성 포인트 지급
 *
 **********************/
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors", 1);

include "./var.php";
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성
include __fn__;

## 환경설정
define('_title_', '이벤트 포인트 설정');
define('_Menu_', 'adv');
define('_subMenu_', 'spot_point');

include_once __head__; ## html 헤더 출력

if($admin['level'] != 'super'){
	hist("권한이 없습니다.");
}

$qry = "
	SELECT * FROM ckd_spot_point_set OSP
	JOIN ckd_com_code CCC ON CCC.code_id = OSP.code_id
	WHERE CCC.code_tp_id='spot_point_v2'
	ORDER BY CCC.orderby ASC
";
$ret = $NDO->fetch_array($qry);
//pre($ret);

?>
<div class="pageheader">
	<h4 class="panel-title"><?=_title_?></h4>
</div>

<div class="contentpanel">

	<?php
	foreach($ret AS $row){
		$start_temp = explode(" ",$row['start_date']);
		$start_date = $start_temp[0];
		$start_time = $start_temp[1];
		$start_time_temp = explode(":",$start_time);
		$start_time_h = $start_time_temp[0];
		$start_time_i = $start_time_temp[1];

		$end_temp = explode(" ",$row['end_date']);
		$end_date = $end_temp[0];
		$end_time = $end_temp[1];
		$end_time_temp = explode(":",$end_time);
		$end_time_h = $end_time_temp[0];
		$end_time_i = $end_time_temp[1];
	?>
	<div class="panel panel-default">
		<div class="panel-heading">

			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title">
				<div style="float: left;line-height: 40px;padding-right: 20px;"><?=$row['code_desc']?></div>
				<div class="btn-group btn-toggle use_YN" style="margin-bottom: 0;" data-otpc="<?=$row['use_YN']?>" data-code_id="<?=$row['code_id']?>">
					<button class="btn btn-xs <?=($row['use_YN']!='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="Y" style="z-index: 0;">ON </button>
					<button class="btn btn-xs <?=($row['use_YN']=='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="N" style="z-index: 0;">OFF </button>
				</div>
				<button class="btn btn-success save" data-code_id="<?=$row['code_id']?>" style="margin-left:10px;">해당 영역 부분 저장</button>
			</h4>

		</div><!-- panel-heading -->
		<div class="panel-body">
			<div class="form-group">
				<div class="col-sm-1" style="line-height:38px;">시작일시</div>
				<div class="pull-left input-group call  col-md-2" style="margin-right:20px;">
					<input type="text" class="form-control pull-left border-input" placeholder="yyyy-mm-dd" id="boardSdate[<?=$row['code_id']?>]" value="<?=$start_date?>" name="start_date[<?=$row['code_id']?>]" autocomplete="off" >
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
				</div>
				<select class="form-control pull-left border-input" name="s_hour[<?=$row['code_id']?>]" style="width:100px;height: 40px;margin: 0">
					<?php for($i=0;$i<24;$i++){?>
						<option value="<?=$i<10 ? "0".$i : $i?>" <?=$start_time_h==$i ? 'selected':''?>><?=$i<10 ? "0".$i : $i?>시</option>
					<?php }?>
				</select>
				<select class="form-control pull-left border-input" name="s_min[<?=$row['code_id']?>]" style="width:100px;height: 40px;margin-left: 10px;">
					<option value="00" <?=$start_time_i=='00' ?'selected':''?>> 00분 </option>
					<option value="30" <?=$start_time_i=='30' ?'selected':''?>> 30분 </option>
				</select>
			</div>
			<div class="form-group">
				<div class="col-sm-1" style="line-height:38px;">종료일시</div>
				<div class="pull-left input-group call  col-md-2" style="margin-right:20px;">
					<input type="text" class="form-control pull-left border-input" placeholder="yyyy-mm-dd" id="boardEdate[<?=$row['code_id']?>]" value="<?=$end_date?>" name="end_date[<?=$row['code_id']?>]" autocomplete="off" >
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
				</div>
				<select class="form-control pull-left border-input" name="e_hour[<?=$row['code_id']?>]" style="width:100px;height: 40px;margin: 0">
					<?php for($i=0;$i<24;$i++){?>
						<option value="<?=$i<10 ? "0".$i : $i?>" <?=$end_time_h==$i ?'selected':''?>><?=$i<10 ? "0".$i : $i?>시</option>
					<?php }?>
				</select>
				<select class="form-control pull-left border-input" name="e_min[<?=$row['code_id']?>]" style="width:100px;height: 40px;margin-left: 10px;">
					<option value="00" <?=$end_time_i=='00' ?'selected':''?>> 00분 </option>
					<option value="30" <?=$end_time_i=='30' ?'selected':''?>> 30분 </option>
				</select>
			</div>
			<div class="form-group">
				<div class="col-sm-1" style="line-height:35px;">지급주기</div>
				<div class="col-sm-2 pull-left" style="width: 361px;">
					<label style="margin-right: 10px;cursor: pointer;"><input type="radio" name="pay_cycle[<?=$row['code_id']?>]" <?=$row['pay_cycle']=="01"?"checked":""?> value="01" data-code_id="<?=$row['code_id']?>">일</label>
					<label style="margin-right: 10px;cursor: pointer;"><input type="radio" name="pay_cycle[<?=$row['code_id']?>]" <?=$row['pay_cycle']=="02"?"checked":""?> value="02" data-code_id="<?=$row['code_id']?>">주 (월~일)</label>
					<label style="margin-right: 10px;cursor: pointer;"><input type="radio" name="pay_cycle[<?=$row['code_id']?>]" <?=$row['pay_cycle']=="03"?"checked":""?> value="03" data-code_id="<?=$row['code_id']?>">당월</label>
					<label style="margin-right: 10px;cursor: pointer;"><input type="radio" name="pay_cycle[<?=$row['code_id']?>]" <?=$row['pay_cycle']=="04"?"checked":""?> value="04" data-code_id="<?=$row['code_id']?>">기간입력</label>
					<input type="text" class="form-control" style="width: 50px;height:35px;display: inline;" value="<?=$row['term']?>" name="term[<?=$row['code_id']?>]" maxlength="3" autocomplete="off" <?=$row['pay_cycle']=="04"?"":"readonly"?>  >
					<div class="col-sm-1" style="line-height: 35px;float: right;padding-left: 8px;">일</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-2" style="line-height:35px;width:200px;">지급 주기 기준 적립금 제한</div>
				<input type="text" class="form-control pull-left border-input" style="width:100px; height:35px;" autocomplete="off" name="give_point_limit[<?=$row['code_id']?>]" value="<?=$row['give_point_limit']?>">
				<div class="col-sm-1" style="line-height:35px;">P</div>
				<div class="col-sm-2" style="line-height:35px;width:210px;">지급 주기 기준 당첨인원 제한</div>
				<input type="text" class="form-control pull-left border-input"  style="width:100px; height:35px;" autocomplete="off" name="user_limit[<?=$row['code_id']?>]" value="<?=$row['user_limit']?>">
				<div class="col-sm-1" style="line-height:35px;">명</div>
			</div>
			<hr style="border: 1px #e7e7e7 dashed">
			<div class="form-group">
				<div class="col-sm-2" style="line-height:35px;width:200px;">깜짝 포인트 적립 빈도</div>
				<input readonly type="text" class="form-control pull-left border-input" name="s_give_cnt[<?=$row['code_id']?>]" value="<?=$row['s_give_cnt']?>" style="width:100px; height:35px;" autocomplete="off">
				<div class="col-sm-1" style="line-height:35px;">회마다 제공</div>
				<div class="col-sm-2" style="line-height:35px;width:210px;">인당 최대 적립가능 횟수</div>
				<input type="text" class="form-control pull-left border-input" name="s_user_max_point_cnt[<?=$row['code_id']?>]" value="<?=$row['s_user_max_point_cnt']?>" style="width:100px; height:35px;" autocomplete="off">
				<div class="col-sm-1" style="line-height:35px;">회</div>
				<div class="col-sm-2" style="line-height:35px;width:210px;">당첨 1회당 적립포인트</div>
				<input type="text" class="form-control pull-left border-input" name="s_one_time_give_point[<?=$row['code_id']?>]" value="<?=$row['s_one_time_give_point']?>" style="width:100px; height:35px;" autocomplete="off">
				<div class="col-sm-1" style="line-height:35px;">P</div>
			</div>
			<div class="form-group">
				<div class="col-sm-2" style="line-height:35px;width:200px;"></div>
				<div class="col-sm-1" style="line-height:35px;">최초 미션 달성시</div>
				<input type="text" class="form-control pull-left border-input" name="first_point[<?=$row['code_id']?>]" value="<?=$row['first_point']?>" style="width:100px; height:35px;" autocomplete="off">
				<div class="col-sm-1" style="line-height:35px;">P</div>
			</div>
			<div class="form-group">
				<div class="col-sm-1" style="line-height:35px;">선착순</div>
				<div class="col-sm-2 pull-left" style="line-height:35px;width:300px;">
					<label style="margin-right: 10px;cursor: pointer;"><input type="radio" name="f_first_come_served[<?=$row['code_id']?>]" <?=$row['f_first_come_served'] == "01" ? "checked":""?> value="01">없음</label>
					<label style="margin-right: 10px;cursor: pointer;"><input type="radio" name="f_first_come_served[<?=$row['code_id']?>]" <?=$row['f_first_come_served'] == "02" ? "checked":""?> value="02">일단위</label>
				</div>
				<div class="col-sm-2" style="line-height:35px;width:210px;">선착순 기준 적립금 제한</div>
				<input type="text" class="form-control pull-left border-input" name="f_give_point_limit[<?=$row['code_id']?>]" value="<?=$row['f_give_point_limit']?>" style="width:100px; height:35px;" autocomplete="off">
				<div class="col-sm-1" style="line-height:35px;">P</div>
				<div class="col-sm-2" style="line-height:35px;width:210px;">선착순 기준 당첨인원 제한</div>
				<input type="text" class="form-control pull-left border-input" name="f_user_limit[<?=$row['code_id']?>]" value="<?=$row['f_user_limit']?>" style="width:100px; height:35px;" autocomplete="off">
				<div class="col-sm-1" style="line-height:35px;">명</div>
			</div>
		</div><!-- panel-body -->
	</div><!-- panel panel-default -->

	<?php }?>

</div>

<?php
include __foot__;
?>
<script>
$(document).ready(function(){

	//ON OFF 버튼 통합
	$(".use_YN").on("click",function(){
		var use_YN = $(this).data("otpc");
		var code_id = $(this).data("code_id");
		var formData = {mode:"spot_point", code_id:code_id, use_YN:use_YN};
		$.post("./ajax/ajax_process.php",formData,function(result){
			if(result === "Y" || result === "N"){
				$("div[data-code_id="+code_id+"]").data("otpc",result);
				alert("적용 되었습니다.");
			}else{
				if(use_YN === "Y") {
					$(".use_YN[data-code_id='"+code_id+"']").children("button").eq(0).addClass("btn-primary").removeClass("btn-default").addClass("active");
					$(".use_YN[data-code_id='"+code_id+"']").children("button").eq(1).addClass("btn-default").removeClass("btn-primary").removeClass("active");
				}else{
					$(".use_YN[data-code_id='"+code_id+"']").children("button").eq(0).addClass("btn-default").removeClass("btn-primary").removeClass("active");
					$(".use_YN[data-code_id='"+code_id+"']").children("button").eq(1).addClass("btn-primary").removeClass("btn-default").addClass("active");
				}
				alert("수정 실패");
			}
		},"html");
	});

	//저장
	$(".save").on("click",function(){
		var code_id = $(this).data("code_id");

		var start_date = $("input[name='start_date["+code_id+"]']").val()+" "+$("select[name='s_hour["+code_id+"]']").val()+":"+$("select[name='s_min["+code_id+"]']").val()+":00"
		var end_date = $("input[name='end_date["+code_id+"]']").val()+" "+$("select[name='e_hour["+code_id+"]']").val()+":"+$("select[name='e_min["+code_id+"]']").val()+":00";

		var pay_cycle = $("input[name='pay_cycle["+code_id+"]']:checked").val();
		var term = $("input[name='term["+code_id+"]']").val();

		var give_point_limit = $("input[name='give_point_limit["+code_id+"]']").val();
		var user_limit = $("input[name='user_limit["+code_id+"]']").val();

		var s_give_cnt = $("input[name='s_give_cnt["+code_id+"]']").val();
		var s_user_max_point_cnt = $("input[name='s_user_max_point_cnt["+code_id+"]']").val();
		var s_one_time_give_point = $("input[name='s_one_time_give_point["+code_id+"]']").val();

		var f_first_come_served = $("input[name='f_first_come_served["+code_id+"]']:checked").val();
		var f_give_point_limit = $("input[name='f_give_point_limit["+code_id+"]']").val();
		var f_user_limit = $("input[name='f_user_limit["+code_id+"]']").val();
		var first_point = $("input[name='first_point["+code_id+"]']").val();

		var formData = {
			mode:"spot_point_save"
			,code_id:code_id
			,start_date:start_date
			,end_date:end_date
			,pay_cycle:pay_cycle
			,term:term
			,give_point_limit:give_point_limit
			,user_limit:user_limit
			,s_give_cnt:s_give_cnt
			,s_user_max_point_cnt:s_user_max_point_cnt
			,s_one_time_give_point:s_one_time_give_point
			,f_first_come_served:f_first_come_served
			,f_give_point_limit:f_give_point_limit
			,f_user_limit:f_user_limit
			,first_point:first_point
		};
		$.post("./ajax/ajax_process.php",formData,function(result){
			if(result === "ok"){
				alert("적용 되었습니다.");
			}else{
				alert("수정 실패");
			}
		},"html");
	});

	$("input[name^='pay_cycle']").on("change",function(){
		var code_id = $(this).data("code_id");
		var this_value= $(this).val();

		if(this_value === "04"){
			$("input[name='term["+code_id+"]']").prop("readonly", false);
		}else{
			$("input[name='term["+code_id+"]']").prop("readonly", true);
		}
	});

	$(function() {$("[id^='boardSdate'], [id^='boardEdate']" ).datepicker({dateFormat: 'yy-mm-dd'});});
});
</script>

