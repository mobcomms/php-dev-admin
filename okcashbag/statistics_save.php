<?php
/**********************
 *
 *    통계 페이지 / 일자별 통계
 *
 **********************/

include "./var.php";
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성

## 환경설정
define('_title_', '키보드 적립 통계');
define('_Menu_', 'manage');
define('_subMenu_', 'save');

include_once __head__;

$startDate = !empty($_REQUEST['startDate']) ? $_REQUEST['startDate'] : date("Y-m-d",strtotime("-1 month"));
$endDate = !empty($_REQUEST['endDate']) ? $_REQUEST['endDate'] : date("Y-m-d");

$sdate = str_replace("-","",$startDate);
$edate = str_replace("-","",$endDate);

//통계 데이터
$sql="
	SELECT
	ODS.stats_dttm
	,ODS.activity_num
	,IFNULL(OUP.user_cnt, 0) AS user_cnt
	,IFNULL(OUP.point_cnt, 0) AS point_cnt
	,IFNULL(OUP.sum_point, 0) AS sum_point

	,IFNULL(OUP.spot_user_cnt, 0) AS spot_user_cnt
	,IFNULL(OUP.spot_point_cnt, 0) AS spot_point_cnt
	,IFNULL(OUP.spot_sum_point, 0) AS spot_sum_point

	,IFNULL(OUP.spot_user_cnt10, 0) AS user_cnt_roulette
	,IFNULL(OUP.spot_point_cnt10, 0) AS point_cnt_roulette
	,IFNULL(OUP.spot_sum_point10, 0) AS sum_point_roulette

	,IFNULL(OUP.spot_user_cnt11, 0) AS user_cnt_ladder
	,IFNULL(OUP.spot_point_cnt11, 0) AS point_cnt_ladder
	,IFNULL(OUP.spot_sum_point11, 0) AS sum_point_ladder

	,IFNULL(OUP.spot_user_cnt12, 0) AS user_cnt_lotto
	,IFNULL(OUP.spot_point_cnt12, 0) AS point_cnt_lotto
	,IFNULL(OUP.spot_sum_point12, 0) AS sum_point_lotto

	FROM ocb_day_stats ODS
	LEFT JOIN ocb_save_point_one_day OUP ON OUP.stats_dttm = ODS.stats_dttm

	WHERE ODS.stats_dttm BETWEEN {$sdate} AND {$edate}
	ORDER BY ODS.stats_dttm DESC
";
//pre($sql);
$ret = $NDO->fetch_array($sql);

$data = array();
if(is_array($ret)) {

	$html = '';
	$TOTAL = [];
	$make_array = [
		"activity_num","user_cnt","point_cnt","sum_point","spot_user_cnt","spot_point_cnt","spot_sum_point","s_user_cnt","s_point_cnt","s_sum_point"
		,"user_cnt_roulette","point_cnt_roulette","sum_point_roulette","user_cnt_ladder","point_cnt_ladder","sum_point_ladder"
		,"user_cnt_lotto","point_cnt_lotto","sum_point_lotto","user_cnt_total","point_cnt_total","sum_point_total"
	];
	foreach($make_array as $item){
		$TOTAL[$item] = 0;
	}

	foreach ($ret as $row) {
		$TOTAL['activity_num'] += $row['activity_num'];

		$TOTAL['user_cnt'] += $row['user_cnt'];
		$TOTAL['point_cnt'] += $row['point_cnt'];
		$TOTAL['sum_point'] += $row['sum_point'];

		$TOTAL['spot_user_cnt'] += $row['spot_user_cnt'];
		$TOTAL['spot_point_cnt'] += $row['spot_point_cnt'];
		$TOTAL['spot_sum_point'] += $row['spot_sum_point'];

		$row['s_user_cnt'] = $row['user_cnt'] + $row['spot_user_cnt'];
		$row['s_point_cnt'] = $row['point_cnt'] + $row['spot_point_cnt'];
		$row['s_sum_point'] = $row['sum_point'] + $row['spot_sum_point'];

		$TOTAL['s_user_cnt'] += $row['s_user_cnt'];
		$TOTAL['s_point_cnt'] += $row['s_point_cnt'];
		$TOTAL['s_sum_point'] += $row['s_sum_point'];

		$TOTAL['user_cnt_roulette'] += $row['user_cnt_roulette'];
		$TOTAL['point_cnt_roulette'] += $row['point_cnt_roulette'];
		$TOTAL['sum_point_roulette'] += $row['sum_point_roulette'];

		$TOTAL['user_cnt_ladder'] += $row['user_cnt_ladder'];
		$TOTAL['point_cnt_ladder'] += $row['point_cnt_ladder'];
		$TOTAL['sum_point_ladder'] += $row['sum_point_ladder'];

		$TOTAL['user_cnt_lotto'] += $row['user_cnt_lotto'];
		$TOTAL['point_cnt_lotto'] += $row['point_cnt_lotto'];
		$TOTAL['sum_point_lotto'] += $row['sum_point_lotto'];

		$row['user_cnt_total']= $row['user_cnt_roulette']+$row['user_cnt_ladder']+$row['user_cnt_lotto'];
		$row['point_cnt_total']= $row['point_cnt_roulette']+$row['point_cnt_ladder']+$row['point_cnt_lotto'];
		$row['sum_point_total']= $row['sum_point_roulette']+$row['sum_point_ladder']+$row['sum_point_lotto'];

		$TOTAL['user_cnt_total'] += $row['user_cnt_total'];
		$TOTAL['point_cnt_total'] += $row['point_cnt_total'];
		$TOTAL['sum_point_total'] += $row['sum_point_total'];

		$html .= "
			<tr class='".$fn->dateColor($row['stats_dttm'])."'>
				<td>{$row['stats_dttm']}</td>
				<td>".number_format($row['activity_num'])."</td>

				<td>".number_format($row['s_user_cnt'])."</td>
				<td>".number_format($row['s_point_cnt'])."</td>
				<td>".number_format($row['s_sum_point'])."</td>

				<td>".number_format($row['user_cnt'])."</td>
				<td>".number_format($row['point_cnt'])."</td>
				<td>".number_format($row['sum_point'])."</td>

				<td>".number_format($row['spot_user_cnt'])."</td>
				<td>".number_format($row['spot_point_cnt'])."</td>
				<td>".number_format($row['spot_sum_point'])."</td>

				<td class='disp_game1'>".number_format($row['user_cnt_total'])."</td>
				<td class='disp_game1'>".number_format($row['point_cnt_total'])."</td>
				<td class='disp_game1'>".number_format($row['sum_point_total'])."</td>
				
				<td class='disp_game2'>".number_format($row['user_cnt_roulette'])."</td>
				<td class='disp_game2'>".number_format($row['point_cnt_roulette'])."</td>
				<td class='disp_game2'>".number_format($row['sum_point_roulette'])."</td>
				
				<td class='disp_game2'>".number_format($row['user_cnt_ladder'])."</td>
				<td class='disp_game2'>".number_format($row['point_cnt_ladder'])."</td>
				<td class='disp_game2'>".number_format($row['sum_point_ladder'])."</td>
				
				<td class='disp_game2'>".number_format($row['user_cnt_lotto'])."</td>
				<td class='disp_game2'>".number_format($row['point_cnt_lotto'])."</td>
				<td class='disp_game2'>".number_format($row['sum_point_lotto'])."</td>
				
			 </tr>
		";
	}

}else{
	$html='<tr><td colspan="5">데이터가 없습니다.</td></tr>';
}

?>
<div class="contentpanel">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title"><?=_title_?></h4>
		</div><!-- panel-heading -->

		<div class="panel-body">

			<form name="scform" method="get" action="">
				<div class="row">
					<div class="card">
						<div class="header pull-left">
							<input type="hidden" name="rep" value="<?=_rep_;?>" />
							<div class="input-group call">
								<input type="text" class="form-control pull-left border-input databox" placeholder="yyyy-mm-dd" id="sdate" value="<?=$startDate?>" name="startDate" >
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>
							<span class="pull-left space-in"> ~ </span>
							<div class="input-group call">
								<input type="text" class="form-control pull-left border-input databox" placeholder="yyyy-mm-dd" id="edate" value="<?=$endDate?>" name="endDate" >
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>

							<div style="display: inline-block;" class="pull-left">
								<button class="btn btn-success" style="margin-left:10px;">검 색</button>
							</div>
							<label style="margin: 4px 0 0 40px">
								<div class="pull-left"><input style="width:20px;height:20px;accent-color: darkcyan;" type="checkbox" id='checkbox2' onclick="display_toggle2();" checked ></div>
								<div class="pull-left" style="padding-top:5px;padding-left:3px;">게임존 통합 노출</div>
							</label>

						</div><!-- header -->
					</div><!-- card -->
				</div><!-- row -->
			</form>

			<div class="row member_table .col-xs-12 .col-md-12" style="margin-top:20px">
				<div class="table-responsive">
					<table class="table table-hover mb30" id="" style="border:1px solid #b0b0b0;">
						<thead>
						<tr>
							<th class="">날짜</th>
							<th class="">키보드 설정 수</th>

							<th class="">전체 적립요청 사용자 수</th>
							<th class="">전체 적립 요청</th>
							<th class="">전체 적립 금액</th>

							<th class="">사용적립 요청 사용자 수</th>
							<th class="">사용적립요청</th>
							<th class="">사용적립금액</th>

							<th class="">배너적립 요청 사용자수</th>
							<th class="">배너적립요청</th>
							<th class="">배너적립금액</th>

							<th class="disp_game1">게임적립 요청 사용자수</th>
							<th class="disp_game1">게임적립요청</th>
							<th class="disp_game1">게임적립금액</th>

							<th class="disp_game2">룰렛적립 요청 사용자수</th>
							<th class="disp_game2">룰렛적립요청</th>
							<th class="disp_game2">룰렛적립금액</th>

							<th class="disp_game2">사다리적립 요청 사용자수</th>
							<th class="disp_game2">사다리적립요청</th>
							<th class="disp_game2">사다리적립금액</th>

							<th class="disp_game2">복권적립 요청 사용자수</th>
							<th class="disp_game2">복권적립요청</th>
							<th class="disp_game2">복권적립금액</th>

						</tr>
						</thead>
						<tbody>
						<tr style="background-color: #F2F5A9;">
							<td>합계</td>
							<td><?=number_format($TOTAL['activity_num'])?></td>

							<td><?=number_format($TOTAL['s_user_cnt'])?></td>
							<td><?=number_format($TOTAL['s_point_cnt'])?></td>
							<td><?=number_format($TOTAL['s_sum_point'])?></td>

							<td><?=number_format($TOTAL['user_cnt'])?></td>
							<td><?=number_format($TOTAL['point_cnt'])?></td>
							<td><?=number_format($TOTAL['sum_point'])?></td>

							<td><?=number_format($TOTAL['spot_user_cnt'])?></td>
							<td><?=number_format($TOTAL['spot_point_cnt'])?></td>
							<td><?=number_format($TOTAL['spot_sum_point'])?></td>

							<td class="disp_game1"><?=number_format($TOTAL['user_cnt_total'])?></td>
							<td class="disp_game1"><?=number_format($TOTAL['point_cnt_total'])?></td>
							<td class="disp_game1"><?=number_format($TOTAL['sum_point_total'])?></td>

							<td class="disp_game2"><?=number_format($TOTAL['user_cnt_roulette'])?></td>
							<td class="disp_game2"><?=number_format($TOTAL['point_cnt_roulette'])?></td>
							<td class="disp_game2"><?=number_format($TOTAL['sum_point_roulette'])?></td>

							<td class="disp_game2"><?=number_format($TOTAL['user_cnt_ladder'])?></td>
							<td class="disp_game2"><?=number_format($TOTAL['point_cnt_ladder'])?></td>
							<td class="disp_game2"><?=number_format($TOTAL['sum_point_ladder'])?></td>

							<td class="disp_game2"><?=number_format($TOTAL['user_cnt_lotto'])?></td>
							<td class="disp_game2"><?=number_format($TOTAL['point_cnt_lotto'])?></td>
							<td class="disp_game2"><?=number_format($TOTAL['sum_point_lotto'])?></td>
						</tr>
						<?=$html?>
						</tbody>
					</table>

				</div><!-- table-responsive -->
			</div><!-- row -->
		</div><!-- panel-body -->
	</div><!-- panel -->
</div><!-- contentpanel -->
<script>
	function display_toggle2(){
		if($('#checkbox2').prop('checked')){
			$(".disp_game1").show();//게임존(개별)
			$(".disp_game2").hide();//게임존(통합)
		}else {
			$(".disp_game1").hide();//게임존(개별)
			$(".disp_game2").show();//게임존(통합)
		}
		table_resize();
	}

	function table_resize(){
		if(($('#checkbox2').prop('checked'))){
			$("section").css({"min-width":"2000px"});
		}else{
			$("section").css({"min-width":"2790px"});
		}
	}

	display_toggle2();
</script>
<?php
include __foot__;
?>