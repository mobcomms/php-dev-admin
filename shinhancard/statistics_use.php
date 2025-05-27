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
define('_title_', '매출 통계');
define('_Menu_', 'use');
define('_subMenu_', 'use');

include_once __head__;

$startDate=isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : date("Y-m-d",strtotime("-1 month"));
$endDate=isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : date("Y-m-d");
if(empty($_REQUEST['type'])){
	if(empty($_REQUEST['startDate'])){
		$type = 30;
	}else{
		$type = "";
	}
}else{
	$type = $_REQUEST['type'];
}

$today = date("Y-m-d");
$today1 = date("Y-m-01");

//$today = "2022-12-31";
//$today1 = "2022-12-01";;

switch($type){
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
$edate=str_replace("-","",$endDate);

$os_type = empty($_REQUEST['os_type']) ?"":$_REQUEST['os_type'];

$sql="
	SELECT
		CDMS.stats_dttm
		,SUM(activity_num) AS activity_num

		,IFNULL(CDS1.exhs_amt, 0) AS mw_exhs1
		,IFNULL(CDS2.exhs_amt, 0) AS mw_exhs2
		,IFNULL(CDS3.exhs_amt, 0) AS mw_exhs3
		,IFNULL(CDS4.exhs_amt, 0) AS mw_exhs4
		,IFNULL(CDS5.exhs_amt, 0) AS mw_exhs5
		,IFNULL(CDS6.exhs_amt, 0) AS mw_exhs6
		,IFNULL(CDS7.exhs_amt, 0) + IFNULL(CDS9.exhs_amt, 0) AS cpm_mw_exhs1
		,IFNULL(CDS8.exhs_amt, 0) + IFNULL(CDS10.exhs_amt, 0)AS cpm_mw_exhs2

		,IFNULL(CSTATS1.order_commission, 0) + IFNULL(CSTATS1.cancel_commission, 0) AS coupang1
		,IFNULL(CSTATS2.order_commission, 0) + IFNULL(CSTATS2.cancel_commission, 0) AS coupang2
		,IFNULL(CSTATS3.order_commission, 0) + IFNULL(CSTATS3.cancel_commission, 0) AS coupang3
		,IFNULL(CSTATS4.order_commission, 0) + IFNULL(CSTATS4.cancel_commission, 0) AS coupang4

	FROM ckd_day_app_stats CDMS
	LEFT JOIN ckd_day_ad_stats CDS1 ON CDS1.stats_dttm = CDMS.stats_dttm AND CDS1.service_tp_code='01'
	LEFT JOIN ckd_day_ad_stats CDS2 ON CDS2.stats_dttm = CDMS.stats_dttm AND CDS2.service_tp_code='02'
	LEFT JOIN ckd_day_ad_stats CDS3 ON CDS3.stats_dttm = CDMS.stats_dttm AND CDS3.service_tp_code='03'
	LEFT JOIN ckd_day_ad_stats CDS4 ON CDS4.stats_dttm = CDMS.stats_dttm AND CDS4.service_tp_code='04'
	LEFT JOIN ckd_day_ad_stats CDS5 ON CDS5.stats_dttm = CDMS.stats_dttm AND CDS5.service_tp_code='05'
	LEFT JOIN ckd_day_ad_stats CDS6 ON CDS6.stats_dttm = CDMS.stats_dttm AND CDS6.service_tp_code='06'
	LEFT JOIN ckd_day_ad_stats CDS7 ON CDS7.stats_dttm = CDMS.stats_dttm AND CDS7.service_tp_code='07'
	LEFT JOIN ckd_day_ad_stats CDS8 ON CDS8.stats_dttm = CDMS.stats_dttm AND CDS8.service_tp_code='08'
	LEFT JOIN ckd_day_ad_stats CDS9 ON CDS9.stats_dttm = CDMS.stats_dttm AND CDS9.service_tp_code='09'
	LEFT JOIN ckd_day_ad_stats CDS10 ON CDS10.stats_dttm = CDMS.stats_dttm AND CDS10.service_tp_code='10'

	LEFT JOIN ckd_day_coupang_stats CSTATS1 ON CSTATS1.stats_dttm = CDMS.stats_dttm AND CSTATS1.service_tp_code='01'
	LEFT JOIN ckd_day_coupang_stats CSTATS2 ON CSTATS2.stats_dttm = CDMS.stats_dttm AND CSTATS2.service_tp_code='02'
	LEFT JOIN ckd_day_coupang_stats CSTATS3 ON CSTATS3.stats_dttm = CDMS.stats_dttm AND CSTATS3.service_tp_code='03'
	LEFT JOIN ckd_day_coupang_stats CSTATS4 ON CSTATS4.stats_dttm = CDMS.stats_dttm AND CSTATS4.service_tp_code='04'

	WHERE CDMS.stats_dttm BETWEEN {$sdate} AND {$edate}
	GROUP BY CDMS.stats_dttm
	ORDER BY CDMS.stats_dttm DESC
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
$M_TOTAL = [];
$make_array1 = array("accumulate","activity_num","accumulate","activity_num","useTotCntAvg","ctr");
$make_array2 = array("mw_exhs1","mw_exhs2","mw_exhs3","mw_exhs4","mw_exhs5","mw_exhs6","cpm_mw_exhs1","cpm_mw_exhs2");
$make_array3 = array("coupang_income1","coupang_income2","coupang_income3","coupang_income4");

$make_array = array_merge ($make_array1, $make_array2, $make_array3);
foreach($make_array as $item){
	$TOTAL[$item] = 0;
}

foreach($ret as $key => $row){

	$commission = 1;

	# 커미션 100 으로 보여줌
	//쿠팡 수익 계산 (주문 커미션 - 취소 커미션 ) * 0.8
	$coupang_income1 = $row['coupang1'] * $commission ;
	$coupang_income2 = $row['coupang2'] * $commission ;
	$coupang_income3 = $row['coupang3'] * $commission ;
	$coupang_income4 = $row['coupang4'] * $commission ;

	//모비위드
	$mw_commission = $commission;

	//합계
	$TOTAL['mw_exhs1'] += $row['mw_exhs1'];
	$TOTAL['mw_exhs2'] += $row['mw_exhs2'];
	$TOTAL['mw_exhs3'] += $row['mw_exhs3'];
	$TOTAL['mw_exhs4'] += $row['mw_exhs4'];
    $TOTAL['mw_exhs5'] += $row['mw_exhs5'];
    $TOTAL['mw_exhs6'] += $row['mw_exhs6'];
    $TOTAL['cpm_mw_exhs1'] += $row['cpm_mw_exhs1'];
    $TOTAL['cpm_mw_exhs2'] += $row['cpm_mw_exhs2'];

	$TOTAL['coupang_income1'] += $coupang_income1;
	$TOTAL['coupang_income2'] += $coupang_income2;
	$TOTAL['coupang_income3'] += $coupang_income3;
	$TOTAL['coupang_income4'] += $coupang_income4;

	if($type=="3M" || $type=="6M"){
		//합계
		$month = substr($row['stats_dttm'],0,6);
		if(!isset($M_TOTAL[$month])){
			$make_array = array_merge ($make_array1, $make_array2, $make_array3);
			foreach($make_array as $item){
				$M_TOTAL[$month][$item] = 0;
			}
		}

		$M_TOTAL[$month]['mw_exhs1'] += round($row['mw_exhs1']);
		$M_TOTAL[$month]['mw_exhs2'] += round($row['mw_exhs2']);
		$M_TOTAL[$month]['mw_exhs3'] += round($row['mw_exhs3']);
		$M_TOTAL[$month]['mw_exhs4'] += round($row['mw_exhs4']);
        $M_TOTAL[$month]['mw_exhs5'] += round($row['mw_exhs5']);
        $M_TOTAL[$month]['mw_exhs6'] += round($row['mw_exhs6']);
        $M_TOTAL[$month]['cpm_mw_exhs1'] += round($row['cpm_mw_exhs1']);
        $M_TOTAL[$month]['cpm_mw_exhs2'] += round($row['cpm_mw_exhs2']);

		$M_TOTAL[$month]['coupang_income1'] += round($coupang_income1);
		$M_TOTAL[$month]['coupang_income2'] += round($coupang_income2);
		$M_TOTAL[$month]['coupang_income3'] += round($coupang_income3);
		$M_TOTAL[$month]['coupang_income4'] += round($coupang_income4);

	}

	if($os_type == 1){
		$total_sales = $row['mw_exhs1'] + $row['mw_exhs3'] + $row['mw_exhs5'] + $coupang_income1 + $coupang_income2 + $row['cpm_mw_exhs1'];
	}else if($os_type == 2){
		$total_sales = $row['mw_exhs2'] + $row['mw_exhs4'] + $row['mw_exhs6'] + $coupang_income3 + $coupang_income4 + $row['cpm_mw_exhs2'];
	}else{
		$total_sales = $row['mw_exhs1'] + $row['mw_exhs2'] + $row['mw_exhs3'] + $row['mw_exhs4'] + $row['mw_exhs5'] + $row['mw_exhs6'] + $coupang_income1 + $coupang_income2 + $coupang_income3 + $coupang_income4 + $row['cpm_mw_exhs1'] + $row['cpm_mw_exhs2'];
	}

	$total_settlement = $total_sales * 0.75;
	$total_profit = $total_sales * 0.25;

	$html .= "<tr class='{$fn->dateColor($row['stats_dttm'])}'>";
	$html .= "<td>{$row['stats_dttm']}</td>";

	$html .= "<!-- 총 매출 --><td style='color:blue;font-weight:700;'>".number_format($total_sales)."</td>";
	$html .= "<!-- 총 정산금 --><td style='color:blue;font-weight:700;'>".number_format($total_settlement)."</td>";
	if($_SESSION['Adm']['id'] == "mango"){
		$html .= "<!-- 총 수익 --><td style='color:blue;font-weight:700;'>".number_format($total_profit)."</td>";
	}
	if($os_type != 2){
		$html .= "<!-- 일반광고(AOS) -->
			<td class='show_banner_aos' style='color:blue;font-weight:700;'>".number_format($row['mw_exhs1'])."</td>
		";
	}
	if($os_type != 1){
		$html .= "<!-- 일반광고(iOS) -->
			<td class='show_banner_ios' style='color:blue;font-weight:700;'>".number_format($row['mw_exhs2'])."</td>
		";
	}
	if($os_type != 2){
		$html .= "<!-- 쿠팡1 -->
			<td class='show_coupang_aos' style='color:blue;font-weight:700;'>".number_format($coupang_income2)."</td>
			";
	}
	if($os_type != 1){
		$html .= "<!-- 쿠팡2 -->
			<td class='show_coupang_ios' style='color:blue;font-weight:700;'>".number_format($coupang_income4)."</td>
			";
	}

	if($os_type != 2){
		$html .= "<!-- 미션존 광고(AOS) -->
			<td class='show_banner_aos' style='color:blue;font-weight:700;'>".number_format($row['mw_exhs3'])."</td>
		";
	}
	if($os_type != 1){
		$html .= "<!-- 미션존 광고(iOS) -->
			<td class='show_banner_ios' style='color:blue;font-weight:700;'>".number_format($row['mw_exhs4'])."</td>
		";
	}

	if($os_type != 2){
		$html .= "<!-- 미션존 쿠팡(AOS) -->
			<td class='show_banner_aos' style='color:blue;font-weight:700;'>".number_format($coupang_income1)."</td>
		";
	}
	if($os_type != 1){
		$html .= "<!-- 미션존 쿠팡(iOS) -->
			<td class='show_banner_ios' style='color:blue;font-weight:700;'>".number_format($coupang_income3)."</td>
		";
	}

    if($os_type != 2){
        $html .= "<!-- 미션존 스크립트(AOS) -->
			<td class='show_banner_aos' style='color:blue;font-weight:700;'>".number_format($row['mw_exhs5'])."</td>
		";
    }
    if($os_type != 1){
        $html .= "<!-- 미션존 스크립트(iOS) -->
			<td class='show_banner_ios' style='color:blue;font-weight:700;'>".number_format($row['mw_exhs6'])."</td>
		";
    }

    if($os_type != 2){
        $html .= "<!-- 사다리 CPM(AOS) -->
			<td class='show_banner_aos' style='color:blue;font-weight:700;'>".number_format($row['cpm_mw_exhs1'])."</td>
		";
    }
    if($os_type != 1){
        $html .= "<!-- 사다리 CPM(iOS) -->
			<td class='show_banner_ios' style='color:blue;font-weight:700;'>".number_format($row['cpm_mw_exhs2'])."</td>
		";
    }

}//foreach

if(empty($html)){
	$html="<tr><td colspan='31'>데이터가 없습니다.</td></tr>";
}


//총수익
if($os_type == 1){
	$total_sales_all = $TOTAL['mw_exhs1']+$TOTAL['mw_exhs3']+$TOTAL['mw_exhs5']+$TOTAL['coupang_income1']+$TOTAL['coupang_income3']+$TOTAL['cpm_mw_exhs1'];
}else if($os_type == 2){
	$total_sales_all = $TOTAL['mw_exhs2']+$TOTAL['mw_exhs4']+$TOTAL['mw_exhs6']+$TOTAL['coupang_income2']+$TOTAL['coupang_income4']+$TOTAL['cpm_mw_exhs2'];
}else{
	$total_sales_all = $TOTAL['mw_exhs1']+$TOTAL['mw_exhs2']+$TOTAL['mw_exhs3']+$TOTAL['mw_exhs4']+$TOTAL['mw_exhs5']+$TOTAL['mw_exhs6']+$TOTAL['coupang_income1']+$TOTAL['coupang_income2']+$TOTAL['coupang_income3']+$TOTAL['coupang_income4']+$TOTAL['cpm_mw_exhs1']+$TOTAL['cpm_mw_exhs2'];
}

$total_settlement_all = $total_sales_all * 0.75;
$total_profit_all = $total_sales_all * 0.25;

?>
<style>
	a.tooltips {outline:none; }
	a.tooltips strong {line-height:30px;}
	a.tooltips:hover {text-decoration:none;}
	a.tooltips span {
		z-index:10;display:none; padding:14px 20px;
		margin-top:30px; margin-left:-160px;
		width:350px; line-height:16px;
	}
	a.tooltips:hover span{
		display:inline; position:absolute;
		border:2px solid #FFF;  color:#EEE;
		background-color: black;
	}
	.callout {z-index:20;position:absolute;border:0;top:-14px;left:120px;}

	/*CSS3 extras*/
	a.tooltips span
	{
		border-radius:2px;
		box-shadow: 0px 0px 8px 4px #666;
		/*opacity: 0.8;*/
	}

</style>

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
							<div class="row" style="margin-left:0">
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
										<span>
											<a class="btn btn-default <?=($type=='30')?'active':''?>" href="?type=30">최근30일</a>
											<a class="btn btn-default <?=($type=='M')?'active':''?>" href="?type=M">이번달</a>
											<a class="btn btn-default <?=($type=='B1')?'active':''?>" href="?type=B1">전월</a>
											<a class="btn btn-default <?=($type=='B2')?'active':''?>" href="?type=B2">전전월</a>
											<a class="btn btn-default <?=($type=='3M')?'active':''?>" href="?type=3M">3개월</a>
											<a class="btn btn-default <?=($type=='6M')?'active':''?>" href="?type=6M">6개월</a>
										</span>
									<button class="btn btn-success" style="margin-left:10px;">검 색</button>
								</div>
								<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
								<div style="display: inline-block;" class="pull-left">
									<button type="button" class="btn btn-danger" style="margin-left:10px;">데이터 갱신(새창)</button>
								</div>
								<?php } ?>
							</div>

							<div class="row pull-left" style="margin-top: 10px;margin-bottom: 10px;margin-left: 0;">
								<div class="pull-left"><input style="width:20px;height:20px;accent-color: darkcyan;" type="radio" name="os_type" value="0" <?=empty($os_type)?"checked":""?>></div>
								<div class="pull-left" style="padding-top:5px;padding-left:3px;">전체</div>
								<div class="pull-left" style="margin-left: 10px;" ><input style="width:20px;height:20px;accent-color: darkcyan;" type="radio" name="os_type" value="1" <?=($os_type=="1")?"checked":""?>></div>
								<div class="pull-left" style="padding-top:5px;padding-left:3px;">Android</div>
								<div class="pull-left" style="margin-left: 10px;"><input style="width:20px;height:20px;accent-color: darkcyan;" type="radio" name="os_type" value="2" <?=($os_type=="2")?"checked":""?>></div>
								<div class="pull-left" style="padding-top:5px;padding-left:3px;">iOS</div>
							</div>

							<div style="display: inline-block;" class="pull-right">
								<button class="btn btn-success" style="margin-left:10px;" id="ExcelDown" onclick="return false">Excel 다운</button>
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
							<th rowspan="2">날짜</th>
							<th rowspan="2" style="color:blue;">총 매출</th>
							<th rowspan="2" style="color:blue;">매체 수수료</th>
<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
							<th rowspan="2" style="color:blue;">매출 총이익</th>
<?php } ?>
							<th class="show_banner_aos">일반 광고(AOS)
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>모비위드 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
								</a>
							</th>
							<th class="show_banner_ios">일반 광고(iOS)
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>모비위드 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
								</a>
							</th>
							<th class="show_coupang_aos">쿠팡 연동 (AOS)
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>쿠팡 데이터 (매일 오후04:50분 전일 데이터 갱신)</span>
								</a>
							</th>
							<th class="show_coupang_ios">쿠팡 연동 (iOS)
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>쿠팡 데이터 (매일 오후04:50분 전일 데이터 갱신)</span>
								</a>
							</th>

							<th class="show_coupang_aos">미션존 일반 (AOS)
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>모비위드 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
								</a>
							</th>
							<th  class="show_coupang_ios">미션존 일반 (iOS)
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>모비위드 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
								</a>
							</th>
							<th class="show_coupang_aos">미션존 쿠팡 (AOS)
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>쿠팡 데이터 (매일 오후04:50분 전일 데이터 갱신)</span>
								</a>
							</th>
							<th  class="show_coupang_ios">미션존 쿠팡 (iOS)
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>쿠팡 데이터 (매일 오후04:50분 전일 데이터 갱신)</span>
								</a>
							</th>
                            <th class="show_coupang_aos">미션존 스크립트 (AOS)
                                <a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
                                    <span>
										<strong>모비위드 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
                                </a>
                            </th>
                            <th  class="show_coupang_ios">미션존 스크립트 (iOS)
                                <a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
                                    <span>
										<strong>모비위드 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
                                </a>
                            </th>

                            <th class="show_coupang_aos">사다리 CPM(AOS)
                                <a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
                                    <span>
										<strong>매조미디어+모비위드 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
                                </a>
                            </th>
                            <th  class="show_coupang_ios">사다리 CPM(iOS)
                                <a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
                                    <span>
										<strong>매조미디어+모비위드 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
                                </a>
                            </th>
						</tr>
						</thead>
						<tbody id="ocb" >
						<tr class="" style="background-color: #F2F5A9;">
							<td>합계</td>
							<td style="color:blue;font-weight:700;"><?=number_format($total_sales_all)?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($total_settlement_all)?></td>
							<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
							<td style="color:blue;font-weight:700;"><?=number_format($total_profit_all)?></td>
							<?php } ?>

							<?php if($os_type != 2){ ?>
							<!-- 일반 광고(AOS) -->
							<td class="show_banner_aos" style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs1'])?></td>
							<?php } ?>

							<?php if($os_type != 1){ ?>
							<!-- 일반 광고(iOS)  -->
							<td class="show_banner_ios" style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs2'])?></td>
							<?php } ?>

							<?php if($os_type != 2){ ?>
							<!-- 쿠팡 광고 (AOS) -->
							<td class="show_coupang_aos" style="color:blue;font-weight:700;"><?=number_format($TOTAL['coupang_income2'])?></td>
							<?php } ?>

							<?php if($os_type != 1){ ?>
							<!-- 쿠팡 광고 (iOS) -->
							<td class="show_coupang_ios" style="color:blue;font-weight:700;"><?=number_format($TOTAL['coupang_income4'])?></td>
							<?php } ?>

							<?php if($os_type != 2){ ?>
							<!-- 미션존 일반 (AOS) -->
							<td class="show_coupang_aos" style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs3'])?></td>
							<?php } ?>

							<?php if($os_type != 1){ ?>
							<!-- 미션존 일반 (iOS) -->
							<td class="show_coupang_ios" style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs4'])?></td>
							<?php } ?>

							<?php if($os_type != 2){ ?>
							<!-- 미션존 쿠팡 (AOS) -->
							<td class="show_coupang_aos" style="color:blue;font-weight:700;"><?=number_format($TOTAL['coupang_income1'])?></td>
							<?php } ?>

							<?php if($os_type != 1){ ?>
							<!-- 미션존 쿠팡 (iOS) -->
							<td class="show_coupang_ios" style="color:blue;font-weight:700;"><?=number_format($TOTAL['coupang_income3'])?></td>
							<?php } ?>

                            <?php if($os_type != 2){ ?>
                                <!-- 미션존 일반 (AOS) -->
                                <td class="show_coupang_aos" style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs5'])?></td>
                            <?php } ?>

                            <?php if($os_type != 1){ ?>
                                <!-- 미션존 일반 (iOS) -->
                                <td class="show_coupang_ios" style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs6'])?></td>
                            <?php } ?>
                            <?php if($os_type != 2){ ?>
                                <!-- 사다리 CPM (AOS) -->
                                <td class="show_coupang_aos" style="color:blue;font-weight:700;"><?=number_format($TOTAL['cpm_mw_exhs1'])?></td>
                            <?php } ?>

                            <?php if($os_type != 1){ ?>
                                <!-- 사다리 CPM (iOS) -->
                                <td class="show_coupang_ios" style="color:blue;font-weight:700;"><?=number_format($TOTAL['cpm_mw_exhs2'])?></td>
                            <?php } ?>

                        </tr>
					<?php

					//3개월 6개월 합계
					################################################################################################################################################################
					################################################################################################################################################################
					################################################################################################################################################################
					################################################################################################################################################################
					################################################################################################################################################################

					if($type=="3M" || $type=="6M"){
						foreach ($M_TOTAL as $key=>$row){
							$this_date = $key."01";
							$date = date("Y-m-d",strtotime ("-1 days", strtotime($this_date)));
							$date = date("Y-m-d",strtotime ("+1 days", strtotime($date)));
							$this_month_day_cnt = date('t',strtotime($date));

							$month_total_sales = $row['mw_exhs1']+$row['mw_exhs2']+$row['mw_exhs3']+$row['mw_exhs4']+$row['mw_exhs5']+$row['mw_exhs6']+$row['coupang_income1']+$row['coupang_income2']+$row['coupang_income3']+$row['coupang_income4']+$row['cpm_mw_exhs1']+$row['cpm_mw_exhs2'];
							$month_total_settlement = $month_total_sales * 0.7;
							$month_total_profit = $month_total_sales * 0.3;

							?>
						<tr class="" style="background-color: #afe076;">
							<td><?=$key?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($month_total_sales)?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($month_total_settlement)?></td>
							<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
							<td style="color:blue;font-weight:700;"><?=number_format($month_total_profit)?></td>
							<?php }?>
							<?php if($os_type != 2){ ?>
							<!-- 일반 광고(AOS) -->
							<td class="show_banner_aos" style="color:blue;font-weight:700;"><?=number_format($row['mw_exhs1'])?></td>
							<?php } ?>
							<?php if($os_type != 1){ ?>
							<!-- 일반 광고(iOS)  -->
							<td class="show_banner_ios" style="color:blue;font-weight:700;"><?=number_format($row['mw_exhs2'])?></td>
							<?php } ?>
							<?php if($os_type != 2){ ?>
							<!-- 쿠팡 연동 (AOS) -->
							<td class="show_coupang_aos" style="color:blue;font-weight:700;"><?=number_format($row['coupang_income2'])?></td>
							<?php } ?>
							<?php if($os_type != 1){ ?>
							<!-- 쿠팡 연동 (iOS) -->
							<td class="show_coupang_ios" style="color:blue;font-weight:700;"><?=number_format($row['coupang_income4'])?></td>
							<?php } ?>

							<?php if($os_type != 2){ ?>
							<!-- 미션존 일반 (AOS) -->
							<td class="show_coupang_aos" style="color:blue;font-weight:700;"><?=number_format($row['mw_exhs3'])?></td>
							<?php } ?>
							<?php if($os_type != 1){ ?>
							<!-- 미션존 일반 (iOS) -->
							<td class="show_coupang_ios" style="color:blue;font-weight:700;"><?=number_format($row['mw_exhs4'])?></td>
							<?php } ?>
							<?php if($os_type != 2){ ?>
							<!-- 미션존 쿠팡 (AOS) -->
							<td class="show_coupang_aos" style="color:blue;font-weight:700;"><?=number_format($row['coupang_income1'])?></td>
							<?php } ?>
                            <?php if($os_type != 1){ ?>
							<!-- 미션존 쿠팡 (iOS) -->
							<td class="show_coupang_ios" style="color:blue;font-weight:700;"><?=number_format($row['coupang_income3'])?></td>
                            <?php } ?>
                            <?php if($os_type != 2){ ?>
                                <!-- 미션존 일반 (AOS) -->
                                <td class="show_coupang_aos" style="color:blue;font-weight:700;"><?=number_format($row['mw_exhs5'])?></td>
                            <?php } ?>
                            <?php if($os_type != 1){ ?>
                                <!-- 미션존 일반 (iOS) -->
                                <td class="show_coupang_ios" style="color:blue;font-weight:700;"><?=number_format($row['mw_exhs6'])?></td>
                            <?php } ?>
                            <?php if($os_type != 2){ ?>
                                <!-- 사다리 CPM (AOS) -->
                                <td class="show_coupang_aos" style="color:blue;font-weight:700;"><?=number_format($row['cpm_mw_exhs1'])?></td>
                            <?php } ?>
                            <?php if($os_type != 1){ ?>
                                <!-- 사다리 CPM (iOS) -->
                                <td class="show_coupang_ios" style="color:blue;font-weight:700;"><?=number_format($row['cpm_mw_exhs2'])?></td>
                            <?php } ?>

						</tr>

					<?php }}?>

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
					<th style='color:blue;'>총 매출</th>\
					<th style='color:blue;'>총 정산금</th>\
		<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
					<th style='color:blue;'>총 수익</th>\
		<?php }?>
		";

		<?php if($os_type == 1){ ?>
		tab_text = tab_text + "<th>일반광고(AOS)</th>";
		<?php }else if($os_type == 2){ ?>
		tab_text = tab_text + "<th>일반광고(IOS)</th>";
		<?php }else{ ?>
		tab_text = tab_text + "<th>일반광고(AOS)</th>";
		tab_text = tab_text + "<th>일반광고(IOS)</th>";
		<?php }

		if($os_type == 1){ ?>
		tab_text = tab_text + "<th>쿠팡광고(AOS)</th>";
		<?php }else if($os_type == 2){ ?>
		tab_text = tab_text + "<th>쿠팡광고(IOS)</th>";
		<?php }else{ ?>
		tab_text = tab_text + "<th>쿠팡광고(AOS)</th>";
		tab_text = tab_text + "<th>쿠팡광고(IOS)</th>";
		<?php } ?>

		<?php if($os_type == 1){ ?>
		tab_text = tab_text + "<th>미션존 일반(AOS)</th>";
		tab_text = tab_text + "<th>미션존 쿠팡(AOS)</th>";
        tab_text = tab_text + "<th>미션존 스크립트(AOS)</th>";
		<?php }else if($os_type == 2){ ?>
		tab_text = tab_text + "<th>미션존 일반(IOS)</th>";
		tab_text = tab_text + "<th>미션존 쿠팡(IOS)</th>";
        tab_text = tab_text + "<th>미션존 스크립트(IOS)</th>";
		<?php }else{ ?>
		tab_text = tab_text + "<th>미션존 일반(AOS)</th>";
		tab_text = tab_text + "<th>미션존 일반(IOS)</th>";
		tab_text = tab_text + "<th>미션존 쿠팡(AOS)</th>";
		tab_text = tab_text + "<th>미션존 쿠팡(IOS)</th>";
        tab_text = tab_text + "<th>미션존 스크립트(AOS)</th>";
        tab_text = tab_text + "<th>미션존 스크립트(IOS)</th>";
		<?php } ?>

        <?php if($os_type == 1){ ?>
        tab_text = tab_text + "<th>사다리 CPM(AOS)</th>";
        <?php }else if($os_type == 2){ ?>
        tab_text = tab_text + "<th>사다리 CPM(iOS)</th>";
        <?php }else{ ?>
        tab_text = tab_text + "<th>사다리 CPM(AOS)</th>";
        tab_text = tab_text + "<th>사다리 CPM(iOS)</th>";
        <?php } ?>

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
	fnExcelReport('ocb','매출통계');
});

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
$(".btn-danger").click(function(){
	centerOpenWindow('upd_mobwithad_data.php', 'save_point_limit', '450', '284', '', 'N');
});

//전체 클릭시 일반,쿠팡,오퍼월 모두 체크 해제
$("#ad_type").click(function(){
	if($(this).prop("checked")){
		$("[name='ad_type[1]']").prop("checked", false);
		$("[name='ad_type[2]']").prop("checked", false);
		$("[name='ad_type[3]']").prop("checked", false);
	}
});
//일반,쿠팡,오퍼월 모두 체크 해제시 전체 체크
$("[name='ad_type[1]'], [name='ad_type[2]'], [name='ad_type[3]']").click(function(){
	if(!$("[name='ad_type[1]']").prop("checked") && !$("[name='ad_type[2]']").prop("checked") && !$("[name='ad_type[3]']").prop("checked")){
		$("#ad_type").prop("checked", true);
	}else{
		$("#ad_type").prop("checked", false);
	}
});

$(".show_banner_aos").hide();
$(".show_banner_ios").hide();
$(".show_coupang_aos").hide();
$(".show_coupang_ios").hide();

<?php
if(!empty($ad_type) && !empty($ad_type[1])){
	if($os_type == 1){
?>
	$(".show_banner_aos").show();
<?php }else if($os_type == 2){ ?>
	$(".show_banner_ios").show();
<?php }else{ ?>
	$(".show_banner_aos").show();
	$(".show_banner_ios").show();
<?php
	}
}

if(!empty($ad_type) && !empty($ad_type[2])){
	if($os_type == 1){
?>
	$(".show_coupang_aos").show();
<?php }else if($os_type == 2){ ?>
	$(".show_coupang_ios").show();
<?php }else{ ?>
	$(".show_coupang_aos").show();
	$(".show_coupang_ios").show();
<?php
	}
}

if(empty($ad_type)){
	if($os_type == 1){
?>
		$(".show_banner_aos").show();
		$(".show_coupang_aos").show();
	<?php }else if($os_type == 2){ ?>
		$(".show_banner_ios").show();
		$(".show_coupang_ios").show();
	<?php }else{ ?>
		$(".show_banner_aos").show();
		$(".show_coupang_aos").show();
		$(".show_banner_ios").show();
		$(".show_coupang_ios").show();
	<?php
	}
}
?>

	$("section").css({"min-width":"2200px"});
</script>

<?php
include __foot__;
?>
