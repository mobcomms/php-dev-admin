<?php
/**********************
 *
 *    통계 페이지 / 일자별 통계
 *
 **********************/
exit;
include "./var.php";
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성

## 환경설정
define('_title_', '키보드 적립 제한 설정');
define('_Menu_', 'adv');
define('_subMenu_', 'limit');

include_once __head__;

$startDate = !empty($_REQUEST['startDate']) ? $_REQUEST['startDate'] : date("Y-m-d",strtotime("-1 month"));
$endDate = !empty($_REQUEST['endDate']) ? $_REQUEST['endDate'] : date("Y-m-d");

$sdate=str_replace("-","",$startDate);
$edate=str_replace("-","",$endDate);

//통계 데이터
$sql="SELECT * FROM ckd_save_point_one_day WHERE stats_dttm BETWEEN :sdate AND :edate ORDER BY stats_dttm DESC";
$ret = $NDO->fetch_array($sql,[':sdate'=>$sdate,':edate'=>$edate]);

$html = '';
$TOTAL = [];
$make_array = array("sum_point","new_user_save_point","old_user_save_point");
foreach($make_array as $item){
	$TOTAL[$item] = 0;
}

if(!empty($ret)) {
	$old_user_save_point = 0;
	foreach ($ret as $row) {
		$TOTAL['sum_point'] += $row['sum_point'];
		$TOTAL['new_user_save_point'] += $row['new_user_save_point'];
		if(isset($row['sum_point']) && isset($row['new_user_save_point'])){
			$old_user_save_point = $row['sum_point'] - $row['new_user_save_point'];
			$TOTAL['old_user_save_point'] += $old_user_save_point;
		}
		$html .= "
			<tr class='".$fn->dateColor($row['stats_dttm'])."'>
				<td>{$row['stats_dttm']}</td>
				<td>".number_format($row['sum_point'])."</td>

				<td>".number_format($row['new_user_save_point'])."</td>
				<td>".number_format($old_user_save_point)."</td>

				<td>".number_format($row['new_user_save_point_limit'])."</td>
				<td>".number_format($row['old_user_save_point_limit'])."</td>
			 </tr>
		";
	}

}else{
	$html='<tr><td colspan="6">데이터가 없습니다.</td></tr>';
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
							<div class="header">
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
								<button class="btn btn-success" style="margin-left:10px;">검 색</button>

								<div style="display: inline-block;margin-left: 360px;" class="pull-right">
									[현재 적립 제한] 신규 : <?=$new_user_limit?>P / 신규제외 : <?=$old_user_limit?>P
									<button type="button" class="btn btn-success" style="margin-left:10px;" onclick="centerOpenWindow('save_point_limit.php', 'save_point_limit', '450', '284', '', 'N');">적립 제한</button>
								</div>
							</div><!-- header -->
						</div><!-- card -->
					</div><!-- row -->
				</form>

				<div class="row member_table .col-xs-12 .col-md-12" style="margin-top:20px">
					<div class="table-responsive">
						<table class="table table-hover mb30" id="" style="border:1px solid #b0b0b0;">
							<thead>
							<tr>
								<th>날짜</th>
								<th>총 적립 포인트</th>
								<th>신규(가입 후 7일) 적립포인트</th>
								<th>신규 제외적립포인트</th>
								<th>신규(가입 후 7일) 적립 제한</th>
								<th>신규 제외 적립 제한</th>
							</tr>
							</thead>
							<tbody>
							<?=$html?>
							</tbody>
						</table>

					</div><!-- table-responsive -->
				</div><!-- row -->
			</div><!-- panel-body -->
		</div><!-- panel -->
	</div><!-- contentpanel -->

<?php
include __foot__;
?>

<script>
// 화면 중앙에 새창 열기
function centerOpenWindow(theURL, winName, width, height, fstate, scrollbars) {
var features = "width=" + width ;
features += ",height=" + height ;
var state = "";

var scrollbars = scrollbars || "no";

if (fstate == "") {		// 옵션
state = features + ", left=" + (screen.width-width)/2 + ",top=" + (screen.height-height)/2 + ",scrollbars="+ scrollbars;
} else {
state = fstate + ", " + features + ", left=" + (screen.width-width)/2 + ",top=" + (screen.height-height)/2 + ",scrollbars="+ scrollbars;
}
var win = window.open(theURL,winName,state);
win.focus();
}
</script>
