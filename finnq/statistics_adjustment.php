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
define('_title_', '정산');
define('_Menu_', 'adjustment');
define('_subMenu_', 'adjustment');

include_once __head__;

$s_year = empty($_REQUEST['s_year']) ? "":$_REQUEST['s_year'];
$s_month = empty($_REQUEST['s_month']) ? "":$_REQUEST['s_month'];
$startDate = $s_year.$s_month;
//pre($startDate);

$e_year = empty($_REQUEST['e_year']) ? "":$_REQUEST['e_year'];
$e_month = empty($_REQUEST['e_month']) ? "":$_REQUEST['e_month'];
$endDate = $e_year.$e_month;

if(empty($_REQUEST['type'])){
	if(empty($startDate)){
		$type = "all";
	}else{
		$type = "";
	}
}else{
	$type = $_REQUEST['type'];
}

$today = date("Y-m-d");
$today1 = date("Y-m-01");

switch($type){
	case "all" : //전체

	case '30'://최근 30일
		$startDate = date("Y-m-d",strtotime($today." -30 day"));
		$endDate = $today;
	break;
	case 'M'://이번달
		$startDate = $today1;
		$endDate = $today;
		break;
	case 'B1'://전월
		$startDate = date("Y-m-01", strtotime($today1." -1 month")); //지난달 1일
		$endDate = date("Y-m-t", strtotime($today1." -1 month")); //지난달 말일
	break;
	case 'B2'://전전월
		$startDate = date("Y-m-01", strtotime($today1." -2 month"));
		$endDate = date("Y-m-t", strtotime($today1." -2 month"));
	break;
	case '3M'://3개월
		$startDate = date("Y-m-01", strtotime($today1." -2 month"));
		$endDate = $today;
	break;
	case '6M'://6개월
		$startDate = date("Y-m-01", strtotime($today1." -5 month"));
		$endDate = $today;
	break;
}
$sdate=str_replace("-","",$startDate);
$sdate = substr($sdate,0,6);

$edate=str_replace("-","",$endDate);
$edate = substr($edate,0,6);
$add_query = "stats_dttm BETWEEN {$sdate} AND {$edate}";


$os_type = empty($_REQUEST['os_type']) ?"":$_REQUEST['os_type'];
switch($os_type){
	case "A" : $add_query .= " AND adjustment_type = 1"; break;
	case "I" : $add_query .= " AND adjustment_type = 2"; break;
	default : $add_query .= ""; break;
}

// 통계 데이터
$sql="
	SELECT * FROM ckd_day_stats_data
	WHERE {$add_query}
	ORDER BY stats_dttm DESC
";
//pre($sql);
$ret = $NDO->fetch_array($sql);
//pre($ret);


function date_color_code($day){
	$yoil = array("#fff3f3","","","","","","#f1fcff");
	return ($yoil[date('w', strtotime($day))]);
}

$html='';
$TOTAL = [];
$make_array = array("sales","settlement","price_ad","price_coupang","price_offerwall","mw_exhs2","mw_eprs3","mw_click3","mw_exhs3"
	,"mw_eprs4","mw_click4","mw_exhs4","mw_eprs5","mw_click5","mw_exhs5","mw_eprs6","mw_click6","mw_exhs6"
,"offerwall_exhs_amt_ori","mw_eprs_sdk","mw_click_sdk","mw_exhs_sdk","mw_ctr_sdk");


foreach($make_array as $item){
	$TOTAL[$item] = 0;
}

foreach($ret as $key => $row){

	$commission = 1;

	$offerwall_commission = $commission;

	//모비위드
	$mw_commission = $commission;

	//합계
	$TOTAL['sales'] += $row['sales'];
	$TOTAL['settlement'] += $row['settlement'];
	$TOTAL['price_ad'] += $row['price_ad'];
	$TOTAL['price_coupang'] +=$row['price_coupang'];
	$TOTAL['price_offerwall'] += $row['price_offerwall'];

	$html .= "<tr class='{$fn->dateColor($row['stats_dttm'])}'>";
	$html .= "
		<td>{$row['stats_dttm']}</td>
		<td>집계방식{$row['adjustment_type']}</td>
		<td style='font-weight:700;'>".number_format($row['settlement'])."</td>

		<!-- 일반광고 -->
		<td style='font-weight:700;'>".number_format($row['price_ad'])."</td>

		<!-- 쿠팡광고 -->
		<td style='font-weight:700;'>".number_format($row['price_coupang'])."</td>

		<!-- 오퍼월 -->
		<td style='font-weight:700;'>".number_format($row['price_offerwall'])."</td>

	</tr>
	";
}//foreach

if(empty($html)){
	$html="<tr><td colspan='31'>데이터가 없습니다.</td></tr>";
}

$now_date = date("Y-m");
$temp_date = explode("-",$now_date);
$now_date6m = date("Y-m",strtotime("-6 month"));
$temp_date6m = explode("-",$now_date6m);

$d1[] = $temp_date[0];
$d1[] = $temp_date6m[0];
$d1 = array_unique($d1);
//pre($d1);
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
							<div class="form-group pull-left">
								<select class="form-control pull-left border-input" name="s_year" style="width:100px;height: 35px;margin: 0">
									<option>연도</option>
									<?php foreach($d1 as $item){ ?>
										<option value="<?=$item?>" <?=(!empty($sdate) && $item == substr($sdate,0,4))?'selected':''?>><?=$item?>년</option>
									<?php }?>
								</select>
								<select class="form-control pull-left border-input" name="s_month" style="width:100px;height: 35px;margin-left: 10px;">
									<option>월</option>
									<?php for($i=1;$i<=12;$i++){?>
										<option value="<?=$i<9?"0".$i:$i?>" <?=(!empty($sdate) && $i == substr($sdate,4,2))?'selected':''?>> <?=$i?>월</option>
									<?php }?>
								</select>
								<span class="pull-left space-in"> ~ </span>
								<select class="form-control pull-left border-input" name="e_year" style="width:100px;height: 35px;margin: 0">
									<option>연도</option>
									<?php foreach($d1 as $item){?>
										<option value="<?=$item?>" <?=(!empty($edate) && $item == substr($edate,0,4))?'selected':''?>><?=$item?>년</option>
									<?php }?>
								</select>
								<select class="form-control pull-left border-input" name="e_month" style="width:100px;height: 35px;margin-left: 10px;">
									<option>월</option>
									<?php for($i=1;$i<=12;$i++){?>
										<option value="<?=$i<9?"0".$i:$i?>" <?=(!empty($edate) && $i == substr($edate,4,2))?'selected':''?>> <?=$i?>월</option>
									<?php }?>
								</select>
							</div>

							<div style="display: inline-block;" class="pull-left">
									<span>
										<a class="btn btn-default <?=($type=='M')?'active':''?>" href="?type=M">이번달</a>
										<a class="btn btn-default <?=($type=='B1')?'active':''?>" href="?type=B1">전월</a>
										<a class="btn btn-default <?=($type=='B2')?'active':''?>" href="?type=B2">전전월</a>
										<a class="btn btn-default <?=($type=='3M')?'active':''?>" href="?type=3M">3개월</a>
										<a class="btn btn-default <?=($type=='6M')?'active':''?>" href="?type=6M">6개월</a>
									</span>
								<button class="btn btn-success" style="margin-left:10px;">검 색</button>
							</div>
							<div style="display: inline-block;" class="pull-right">
								<button class="btn btn-success" style="margin-left:10px;" id="ExcelDown" onclick="return false">Excel 다운</button>
							</div>

							<div style="clear: both;padding-top: 15px;">
								<label>
									<div class="pull-left"><input style="width:20px;height:20px;accent-color: darkcyan;" type="radio" id="os_type" onclick="display_toggle('');" <?=empty($os_type)?"checked":""?>></div>
									<div class="pull-left" style="padding-top:5px;padding-left:3px;">전체</div>
								</label>
								<label style="margin: 4px 0 0 20px">
									<div class="pull-left"><input style="width:20px;height:20px;accent-color: darkcyan;" type="radio" id="os_type_a" onclick="display_toggle('A');" <?=($os_type=="A")?"checked":""?>></div>
									<div class="pull-left" style="padding-top:5px;padding-left:3px;">집게방식1</div>
								</label>
								<label style="margin: 4px 0 0 20px">
									<div class="pull-left"><input style="width:20px;height:20px;accent-color: darkcyan;" type="radio" id="os_type_i" onclick="display_toggle('I');" <?=($os_type=="I")?"checked":""?>></div>
									<div class="pull-left" style="padding-top:5px;padding-left:3px;">집게방식2</div>
								</label>
							</div>

						</div><!-- header -->
					</div><!-- card -->
				</div><!-- row -->
			</form>
			<div class="row member_table .col-xs-12 .col-md-12" style="margin-top:8px">

				<div class="table-responsive">
					<table class="table table-hover mb30" style="border:1px solid #b0b0b0;">
						<thead>
						<tr>
							<th>날짜</th>
							<th>매출 집계방식</th>
							<th>총 정산금</th>
							<th>일반 광고<br>매출</th>
							<th>쿠팡 광고<br>매출</th>
							<th>오퍼월<br>매출</th>
						</tr>
						</thead>
						<tbody id="ocb" >
						<tr class="" style="background-color: #F2F5A9;">
							<td>합계</td>
							<td></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['settlement'])?></td>

							<!-- 일반 광고 -->
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['price_ad'])?></td>

							<!-- 쿠팡 연동-->
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['price_coupang'])?></td>

							<!-- 오퍼월 -->
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['price_offerwall'])?></td>
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
	function fnExcelReport(id, title) {
		var tab_text = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
		tab_text = tab_text + '<head><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">';
		tab_text = tab_text + '<xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>'
		tab_text = tab_text + '<x:Name>Sheet1</x:Name>';
		tab_text = tab_text + '<x:WorksheetOptions><x:Panes></x:Panes></x:WorksheetOptions></x:ExcelWorksheet>';
		tab_text = tab_text + '</x:ExcelWorksheets></x:ExcelWorkbook></xml></head><body>';
		tab_text = tab_text + "\
		<table border='1px'>\
			<thead>\
				<tr>\
					<th>날짜</th>\
					<th>매출 집계방식</th>\
					<th>총 정산금</th>\
					<th>일반 광고<br />매출</th>\
					<th>쿠팡 광고<br />매출</th>\
					<th>오퍼월<br />매출</th>\
					";

		tab_text = tab_text + "</tr>\
			</thead>";
		var exportTable = $('#' + id).clone();
		exportTable.find('input').each(function (index, elem) { $(elem).remove(); });
		tab_text = tab_text + exportTable.html();
		tab_text = tab_text + '</table></body></html>';
		var data_type = 'data:application/vnd.ms-excel';
		var ua = window.navigator.userAgent;
		var msie = ua.indexOf("MSIE ");
		var fileName = title + '.xls';
//Explorer 환경에서 다운로드
		if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
			if (window.navigator.msSaveBlob) {
				var blob = new Blob([tab_text], {
					type: "application/csv;charset=utf-8;"
				});
				navigator.msSaveBlob(blob, fileName);
			}
		} else {
			var blob2 = new Blob([tab_text], {
				type: "application/csv;charset=utf-8;"
			});
			var filename = fileName;
			var elem = window.document.createElement('a');
			elem.href = window.URL.createObjectURL(blob2);
			elem.download = filename;
			document.body.appendChild(elem);
			elem.click();
			document.body.removeChild(elem);
		}
	}
$("#ExcelDown").click(function(){
	fnExcelReport('ocb','정산');
});

function display_toggle(os_type){
	var s_year = '<?=empty($_GET['s_year'])?"":$_GET['s_year']?>';
	var s_month = '<?=empty($_GET['s_month'])?"":$_GET['s_month']?>';
	var e_year = '<?=empty($_GET['e_year'])?"":$_GET['e_year']?>';
	var e_month = '<?=empty($_GET['e_month'])?"":$_GET['e_month']?>';


	var type = '<?=empty($_GET['type'])?"":$_GET['type']?>';
	var now_os_type = '<?=empty($_GET['os_type'])?"":$_GET['os_type']?>';

	if(os_type == now_os_type){
		switch(os_type){
			case "A" : $("#os_type_a").prop("checked", true); break;
			case "I" : $("#os_type_i").prop("checked", true); break;
			default : $("#os_type").prop("checked", true); break;
		}
		return false;
	}
	location.href="?s_year="+s_year+"&s_month="+s_month+"&e_year="+e_year+"&e_month="+e_month+"&type="+type+"&os_type="+os_type;
}

</script>

<?php
include __foot__;
?>
