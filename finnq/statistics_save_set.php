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
define('_Menu_', 'reward');
define('_subMenu_', 'save_set');

include "./var.php";
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성
include_once __head__; ## html 헤더 출력

if($admin['level'] != 'super'){
	hist("권한이 없습니다.");
}

$sql=" SELECT useYN,unit,point,CONVERT(mod_date,datetime)  FROM api_point_setting WHERE type=1 ";
$banner_set_info = $NDO->getData($sql);
//pre($banner_set_info);

$sql=" SELECT useYN,unit,point,CONVERT(mod_date,datetime)  FROM api_point_setting WHERE type=2 ";
$coupang_set_info = $NDO->getData($sql);
//pre($coupang_set_info);


//히스토리 가져오기
$sql=" SELECT * FROM ckd_point_set_history WHERE type=1 ORDER BY idx DESC LIMIT 8";
$result1 = $NDO->fetch_array($sql);

$sql=" SELECT * FROM ckd_point_set_history WHERE type=2 ORDER BY idx DESC LIMIT 8";
$result2 = $NDO->fetch_array($sql);

?>
<div class="pageheader">
	<h4>적립설정</h4>
</div>

<div class="contentpanel">
	<div class="panel panel-default" style="border-radius: 20px;">
		<div class="panel-heading" style="border-radius: 20px;">
			<div class="panel-btns">
<!--				<a href="" class="panel-close">&times;</a>-->
<!--				<a href="" class="minimize">&minus;</a>-->
<!--				<a href="" class="minimize maximize" style="padding-top: 2px;">+</a>-->
			</div><!-- panel-btns -->
			<h4 class="panel-title">일반 광고<span style="font-size: 12px;font-weight: bold;float: right;color: <?=$banner_set_info['useYN']=="Y"?"blue":"red"?>;padding-right: 20px"><?=$banner_set_info['useYN']=="Y"?"사용중":"미사용"?></span></h4>
		</div><!-- panel-heading -->

		<div class="panel-body">
			<h4 class="panel-title">
				<div style="float: left;line-height: 27px;padding-right: 20px;">사용 여부</div>
				<div class="btn-group btn-toggle use_YN" style="margin-bottom: 0;" data-otpc="<?=$banner_set_info['useYN']?>" data-code_id="1">
					<button class="btn btn-xs <?=$banner_set_info['useYN']=="Y"?"btn-primary active":"btn-default"?>" type="button" value="Y" style="z-index: 0;">ON </button>
					<button class="btn btn-xs <?=$banner_set_info['useYN']=="N"?"btn-primary active":"btn-default"?>" type="button" value="N" style="z-index: 0;">OFF </button>
				</div>
				<span style="font-size: 12px;">사용 여부 off일시 적립통계 테이블에서 노출 X ‘0’으로 설정시 적립 통계 테이블에는 노출 O</span>
			</h4>
			<div class="table-responsive" style="display: none">
				<table class="table member_table">
					<thead>
					<tr>
						<th class="col-md-1">리워드 단위</th>
						<th class="col-md-1">지급 리워드</th>
<!--						<th class="col-md-1">1일 최대 참여 횟수</th>-->
					</tr>
					</thead>
					<tbody>
					<tr>
						<td><input type="text" class="form-control border-input" name="reward_unit1" id="reward_unit1" value="<?=$banner_set_info['unit']?>"></td>
						<td><input type="number" class="form-control border-input" name="reward_point1" id="reward_point1" value="<?=$banner_set_info['point']?>"></td>
<!--						<td><input type="number" class="form-control border-input" name="reward_point" id="reward_point" value="--><?//=$ad_reward['reward_point']?><!--"></td>-->
					</tr>
					</tbody>
				</table>

				<table class="table member_table">
					<thead>
					<tr>
						<th class="col-md-1">시작일</th>
						<th class="col-md-1">종료일</th>
						<th class="col-md-1">지급 리워드</th>
						<th class="col-md-1">리워드 단위</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($result1 as $row){ ?>
						<tr>
							<td><?=$row['start_dttm']?></td>
							<td><?=$row['stop_dttm']?></td>
							<td><?=$row['reward_point']?></td>
							<td><?=$row['reward_unit']?></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>

				<div style="text-align: center;padding-top: 50px;"><button class="btn btn-success save" data-code_id="1" style="margin-left:10px;">해당 영역 부분 저장</button></div>
			</div><!-- table-responsive -->
		</div><!-- panel-body -->
	</div><!-- panel -->




	<div class="panel panel-default" style="border-radius: 20px;">
		<div class="panel-heading" style="border-radius: 20px;">
			<div class="panel-btns">
				<!--				<a href="" class="panel-close">&times;</a>-->
				<!--				<a href="" class="minimize">&minus;</a>-->
<!--				<a href="" class="minimize maximize" style="padding-top: 2px;">+</a>-->
			</div><!-- panel-btns -->
			<h4 class="panel-title">쿠팡 광고<span style="font-size: 12px;font-weight: bold;float: right;color: <?=$coupang_set_info['useYN']=="Y"?"blue":"red"?>;padding-right: 20px"><?=$coupang_set_info['useYN']=="Y"?"사용중":"미사용"?></span></h4>

		</div><!-- panel-heading -->

		<div class="panel-body">
			<h4 class="panel-title">
				<div style="float: left;line-height: 27px;padding-right: 20px;">사용 여부</div>
				<div class="btn-group btn-toggle use_YN" style="margin-bottom: 0;" data-otpc="<?=$coupang_set_info['useYN']?>" data-code_id="2">
					<button class="btn btn-xs <?=$coupang_set_info['useYN']=="Y"?"btn-primary active":"btn-default"?>" type="button" value="Y" style="z-index: 0;">ON </button>
					<button class="btn btn-xs <?=$coupang_set_info['useYN']=="N"?"btn-primary active":"btn-default"?>" type="button" value="N" style="z-index: 0;">OFF </button>
				</div>
				<span style="font-size: 12px;">사용 여부 off일시 적립통계 테이블에서 노출 X ‘0’으로 설정시 적립 통계 테이블에는 노출 O</span>
			</h4>
			<div class="table-responsive" style="display: none">
				<table class="table member_table">
					<thead>
					<tr>
						<th class="col-md-1">리워드 단위</th>
						<th class="col-md-1">지급 리워드</th>
						<!--						<th class="col-md-1">1일 최대 참여 횟수</th>-->
					</tr>
					</thead>
					<tbody>
					<tr>
						<td><input type="text" class="form-control border-input" name="reward_unit2" id="reward_unit2" value="<?=$coupang_set_info['unit']?>"></td>
						<td><input type="number" class="form-control border-input" name="reward_point2" id="reward_point2" value="<?=$coupang_set_info['point']?>"></td>
						<!--						<td><input type="number" class="form-control border-input" name="reward_point" id="reward_point" value="--><?//=$ad_reward['reward_point']?><!--"></td>-->
					</tr>
					</tbody>
				</table>

				<table class="table member_table">
					<thead>
					<tr>
						<th class="col-md-1">시작일</th>
						<th class="col-md-1">종료일</th>
						<th class="col-md-1">리워드 단위</th>
						<th class="col-md-1">지급 리워드</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($result2 as $row){ ?>
						<tr>
							<td><?=$row['start_dttm']?></td>
							<td><?=$row['stop_dttm']?></td>
							<td><?=$row['reward_point']?></td>
							<td><?=$row['reward_unit']?></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>

				<div style="text-align: center;padding-top: 50px;"><button class="btn btn-success save" data-code_id="2" style="margin-left:10px;">해당 영역 부분 저장</button></div>
			</div><!-- table-responsive -->
		</div><!-- panel-body -->
	</div><!-- panel -->
</div><!-- contentpanel -->

<script>
$(document).ready(function(){

	//리워드 설정
	$(".use_YN, .save").on("click",function(){

		if($(this).hasClass("use_YN") == true) {
			var otpc = $(this).data("otpc");
			var otpc_value = otpc=="Y"?"N":"Y";
			$(this).data("otpc",otpc_value);
		}else{
			otpc_value = "";
		}


		var code_id = $(this).data("code_id");
		if(code_id == 1){
			var reward_unit = $("#reward_unit1").val();
			var reward_point = $("#reward_point1").val();
			if(!reward_unit){
				alert("리워드 단위를 입력 하세요.");
				$("#reward_unit1").focus();
				return;
			}
			if(!reward_point){
				alert("지급할 포인트를 입력 하세요.");
				$("#reward_point1").focus();
				return;
			}
		}else{
			var reward_unit = $("#reward_unit2").val();
			var reward_point = $("#reward_point2").val();
			if(!reward_unit){
				alert("리워드 단위를 입력 하세요.");
				$("#reward_unit2").focus();
				return;
			}
			if(!reward_point){
				alert("지급할 포인트를 입력 하세요.");
				$("#reward_point2").focus();
				return;
			}
		}

		var formData = {mode:"ad_reward",reward_unit:reward_unit,reward_point:reward_point,code_id:code_id,otpc_value:otpc_value};
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
