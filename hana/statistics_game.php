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
define('_title_', '키보드 사용 통계(통합)');
define('_Menu_', 'manage');
define('_subMenu_', 'game');

include_once __head__;

$ip = $fn->getRealClientIp();
$allow_ip = array('127.0.0.1','221.150.126.74','112.220.254.82','112.171.101.32','221.150.126.75');
//pre($ip);
if(in_array($ip, $allow_ip) && $_SESSION['Adm']['id'] == "mango"){
	$hidden_page = "show";
	$hidden_btn = "show";
}else{
	$hidden_page = "hide";
	$hidden_btn = "hide";
}

//오픈초기에는 커미션 없음
$hidden_page = "hide";

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
switch($os_type){
	case "A" :
		$add_query = "
			,IFNULL(CDS7.eprs_num, 0) + IFNULL(CDS9.eprs_num, 0) AS mw_eprs_sdk
			,IFNULL(CDS7.click_num, 0) + IFNULL(CDS9.click_num, 0) AS mw_click_sdk
			,IFNULL(CDS7.exhs_amt, 0) + IFNULL(CDS9.exhs_amt, 0) AS mw_exhs_sdk
		";
		$add_os = " AND os_type = 'A'";
		$add_info = " AND user_app_os = 'A'";
	break;
	case "I" :
		$add_query = "
			,IFNULL(CDS8.eprs_num, 0) + IFNULL(CDS10.eprs_num, 0) AS mw_eprs_sdk
			,IFNULL(CDS8.click_num, 0) + IFNULL(CDS10.click_num, 0) AS mw_click_sdk
			,IFNULL(CDS8.exhs_amt, 0) + IFNULL(CDS10.exhs_amt, 0) AS mw_exhs_sdk
		";
		$add_os = " AND os_type = 'I'";
		$add_info = " AND (user_app_os is null or user_app_os = 'I')";
	break;
	default  :
		$add_query = "
			,IFNULL(CDS7.eprs_num, 0) + IFNULL(CDS8.eprs_num, 0) + IFNULL(CDS9.eprs_num, 0) + IFNULL(CDS10.eprs_num, 0) AS mw_eprs_sdk
			,IFNULL(CDS7.click_num, 0) + IFNULL(CDS8.click_num, 0) + IFNULL(CDS9.click_num, 0) + IFNULL(CDS10.click_num, 0) AS mw_click_sdk
			,IFNULL(CDS7.exhs_amt, 0) + IFNULL(CDS8.exhs_amt, 0) + IFNULL(CDS9.exhs_amt, 0) + IFNULL(CDS10.exhs_amt, 0) AS mw_exhs_sdk
		";
		$add_os = "";
		$add_info = "";
	break;
}


// 통계 데이터
$sql="
	SELECT
		CDMS.stats_dttm
		,SUM(activity_num) AS activity_num
		,SUM(use_cnt) AS use_cnt
		,SUM(use_time) AS use_time
		,SUM(use_tot_cnt) AS use_tot_cnt

		{$add_query}

		,IFNULL(CDS19.eprs_num, 0) AS offerwall_participation
		,IFNULL(CDS19.click_num, 0) AS offerwall_click_num
		,IFNULL(CDS19.exhs_amt, 0) AS offerwall_exhs_amt

        ,IFNULL(CDS29.eprs_num, 0) AS hot_participation
		,IFNULL(CDS29.click_num, 0) AS hot_click_num
		,IFNULL(CDS29.exhs_amt, 0) AS hot_exhs_amt

	FROM ckd_day_app_stats CDMS

	LEFT JOIN ckd_day_ad_stats CDS7 ON CDS7.stats_dttm = CDMS.stats_dttm AND CDS7.service_tp_code='10'
	LEFT JOIN ckd_day_ad_stats CDS8 ON CDS8.stats_dttm = CDMS.stats_dttm AND CDS8.service_tp_code='11'
	LEFT JOIN ckd_day_ad_stats CDS9 ON CDS9.stats_dttm = CDMS.stats_dttm AND CDS9.service_tp_code='12'
	LEFT JOIN ckd_day_ad_stats CDS10 ON CDS10.stats_dttm = CDMS.stats_dttm AND CDS10.service_tp_code='13'

	LEFT JOIN ckd_day_ad_stats CDS19 ON CDS19.stats_dttm = CDMS.stats_dttm AND CDS19.service_tp_code='19'
	LEFT JOIN ckd_day_ad_stats CDS29 ON CDS29.stats_dttm = CDMS.stats_dttm AND CDS29.service_tp_code='29'

	WHERE CDMS.stats_dttm BETWEEN {$sdate} AND {$edate} $add_os
	GROUP BY CDMS.stats_dttm
	ORDER BY CDMS.stats_dttm DESC
";
//pre($sql);
$ret = $NDO->fetch_array($sql);

//전체 누적 설정수
$sql="
	SELECT stats_dttm,activity_num FROM ckd_day_app_stats
	WHERE stats_dttm >= '20231226' AND stats_dttm <= '{$edate}' {$add_os}
	ORDER BY stats_dttm ASC
";
//pre($sql);
$ret2 = $NDO->fetch_array($sql);
$accumulate = 0;
foreach($ret2 as $key => $row){
	$accumulate+=$row['activity_num'];
	$accumulate_array[$row['stats_dttm']] = $accumulate;
}
//pre($accumulate_array);

function date_color_code($day){
	$yoil = array("#fff3f3","","","","","","#f1fcff");
	return ($yoil[date('w', strtotime($day))]);
}

$html='';
$TOTAL = [];
$M_TOTAL = [];
$make_array1 = array("accumulate","activity_num","use_time","accumulate","activity_num","use_time","use_cnt","use_tot_cnt","useTotCntAvg","ctr");
$make_array2 = array("mw_eprs_sdk","mw_click_sdk","mw_exhs_sdk","mw_ctr_sdk","offerwall_participation","offerwall_click_num","offerwall_exhs_amt","offerwall_exhs_amt_ori","hot_participation","hot_click_num","hot_exhs_amt","hot_exhs_amt_ori");

$make_array = array_merge ($make_array1, $make_array2);
foreach($make_array as $item){
	$TOTAL[$item] = 0;
}

foreach($ret as $key => $row){

	$commission = 1;

	//하나머니 계정에만 정산용 0.9 적용
	if($_SESSION['Adm']['id'] == "hana" && $row['stats_dttm'] >= "20240201"){
		$commission = 0.8;
	}

	//평균 사용 시간
	$useTimeAvg = ($row['use_time'] > 0) ? round($row['use_time'] / $row['use_tot_cnt']) : 0;
	//평균 사용횟수
	$useTotCntAvg = ($row['use_cnt'] > 0) ? round($row['use_tot_cnt'] / $row['use_cnt'], 1) : 0;

	//ctr
	$mw_ctr_sdk = ($row['mw_eprs_sdk'] > 0) ? number_format($row['mw_click_sdk'] / $row['mw_eprs_sdk'] * 100, 1) : 0;
	//하나머니 계정에만 광고 커미션 조정
	if($_SESSION['Adm']['id'] == "hana"){
		$sdk_commission = 0.8;
		if($_SESSION['Adm']['id'] == "hana" && $row['stats_dttm'] >= "20240521"){
			$sdk_commission = 0.75;
		}
		$row['mw_exhs_sdk']=round($row['mw_exhs_sdk'] * $sdk_commission);
	}
	$offerwall_commission = $commission;
	//하나머니 계정에만 오퍼월 커미션 조정
	if($_SESSION['Adm']['id'] == "hana" &&  $row['stats_dttm'] >= "20240215"){
		$offerwall_commission = $commission;
	}
	$offerwall_exhs_amt=round($row['offerwall_exhs_amt']);
	$row['offerwall_exhs_amt']=round($row['offerwall_exhs_amt'] * $offerwall_commission);

    $hot_exhs_amt=round($row['hot_exhs_amt']);
    $row['hot_exhs_amt']=round($row['hot_exhs_amt'] * $offerwall_commission);

    //합계
	$TOTAL['accumulate'] += $accumulate_array[$row['stats_dttm']];//날짜
	$TOTAL['activity_num'] += $row['activity_num'];//누적 설정수
	$TOTAL['use_time'] += $row['use_time'];//신규 설정수
	$TOTAL['use_cnt'] += $row['use_cnt'];//평균 사용자수
	$TOTAL['use_tot_cnt'] += $row['use_tot_cnt'];//평균 사용시간
	$TOTAL['useTotCntAvg'] += $useTotCntAvg; //평균사용횟수

	$TOTAL['mw_ctr_sdk'] += $mw_ctr_sdk;
	$TOTAL['mw_eprs_sdk'] += $row['mw_eprs_sdk'];
	$TOTAL['mw_click_sdk'] += $row['mw_click_sdk'];
	$TOTAL['mw_exhs_sdk'] += ($row['mw_exhs_sdk']);

	$TOTAL['offerwall_participation'] += $row['offerwall_participation'];
	$TOTAL['offerwall_click_num'] += $row['offerwall_click_num'];
	$TOTAL['offerwall_exhs_amt'] += ($row['offerwall_exhs_amt']);
	$TOTAL['offerwall_exhs_amt_ori'] += ($offerwall_exhs_amt);

    $TOTAL['hot_participation'] += $row['hot_participation'];
    $TOTAL['hot_click_num'] += $row['hot_click_num'];
    $TOTAL['hot_exhs_amt'] += ($row['hot_exhs_amt']);
    $TOTAL['hot_exhs_amt_ori'] += ($hot_exhs_amt);

    if($type=="3M" || $type=="6M"){
		//합계
		$month = substr($row['stats_dttm'],0,6);
		if(!isset($M_TOTAL[$month])){
			$make_array = array_merge ($make_array1, $make_array2);
			foreach($make_array as $item){
				$M_TOTAL[$month][$item] = 0;
			}
		}

		$M_TOTAL[$month]['accumulate'] += $accumulate_array[$row['stats_dttm']];
		$M_TOTAL[$month]['activity_num'] += $row['activity_num'];
		$M_TOTAL[$month]['use_time'] += $row['use_time'];
		$M_TOTAL[$month]['use_cnt'] += $row['use_cnt'];
		$M_TOTAL[$month]['use_tot_cnt'] += $row['use_tot_cnt'];

		$M_TOTAL[$month]['mw_eprs_sdk'] += $row['mw_eprs_sdk'];
		$M_TOTAL[$month]['mw_click_sdk'] += $row['mw_click_sdk'];
		$M_TOTAL[$month]['mw_exhs_sdk'] += round($row['mw_exhs_sdk']);

		$M_TOTAL[$month]['offerwall_participation'] += $row['offerwall_participation'];
		$M_TOTAL[$month]['offerwall_click_num'] += $row['offerwall_click_num'];
		$M_TOTAL[$month]['offerwall_exhs_amt'] += round($row['offerwall_exhs_amt']);
		$M_TOTAL[$month]['offerwall_exhs_amt_ori'] += round($offerwall_exhs_amt);

        $M_TOTAL[$month]['hot_participation'] += $row['hot_participation'];
        $M_TOTAL[$month]['hot_click_num'] += $row['hot_click_num'];
        $M_TOTAL[$month]['hot_exhs_amt'] += round($row['hot_exhs_amt']);
        $M_TOTAL[$month]['hot_exhs_amt_ori'] += round($hot_exhs_amt);

    }

	$total_amt = $row['mw_exhs_sdk']+$row['offerwall_exhs_amt']+$row['hot_exhs_amt'];
	$total_amt_ori = $row['mw_exhs_sdk']+$offerwall_exhs_amt+$hot_exhs_amt;

    $css_total_income = $total_amt<0 ?"color:red;":"color:blue;";

	$use_cnt = empty($row['use_cnt'])?0:number_format($row['use_cnt']);

	$html .= "<tr class='{$fn->dateColor($row['stats_dttm'])}'>";
	$html .= "
		<td>{$row['stats_dttm']}</td>

		<!-- 사다리 타기 광고 SDK -->
		<td>".number_format($row['mw_eprs_sdk'])."</td>
		<td>".number_format($row['mw_click_sdk'])."</td>
		<td>".number_format($mw_ctr_sdk)."%</td>
		<td style='color:blue;font-weight:700;'>".number_format($row['mw_exhs_sdk'])."</td>

		<!-- 콕머니 쌓기 -->
		<td>".number_format($row['offerwall_click_num'])."</td>
		<td>".number_format($row['offerwall_participation'])."</td>
		<td style='color:blue; font-weight:700;'>".number_format($row['offerwall_exhs_amt'])."</td>
    ";
    if($hidden_page == "show"){
		$html .= "<td style='color:blue; font-weight:700;'>".number_format($offerwall_exhs_amt)."</td>";
    }
    $html .= "
		<!-- 핫플레이스 -->
		<td>".number_format($row['hot_click_num'])."</td>
		<td>".number_format($row['hot_participation'])."</td>
		<td style='color:blue; font-weight:700;'>".number_format($row['hot_exhs_amt'])."</td>

	";
	if($hidden_page == "show"){
		$html .= "<td style='color:blue; font-weight:700;'>".number_format($hot_exhs_amt)."</td>";
	}

    $html .= "
        <!-- 총 수익(원) -->
        <td style='color:blue; font-weight:700;'>".number_format($total_amt)."</td>
    ";
	if($hidden_page == "show"){
		$html .= "<td style='color:blue; font-weight:700;'>".number_format($total_amt_ori)."</td>";
	}
	$html .= "
		 </tr>
	";

}//foreach

if(empty($html)){
	$html="<tr><td colspan='31'>데이터가 없습니다.</td></tr>";
}

//평균 사용자 수
if(!empty($accumulate_array)){
	$date1 = new DateTime($startDate);
	$date2 = new DateTime($endDate);
	$interval = $date1->diff($date2);
	$diff_data = (int)$interval->format('%a');

	//합계 평균 사용자 수
	$TOTAL['use_cnt'] = $TOTAL['use_cnt'] / ($diff_data+1);
}

//클릭율
$TOTAL['mw_ctr_sdk'] = ($TOTAL['mw_eprs_sdk'] > 0) ? number_format($TOTAL['mw_click_sdk'] / $TOTAL['mw_eprs_sdk'] * 100, 1) : 0;

//총수익
$total_amt_all = number_format($TOTAL['mw_exhs_sdk']+$TOTAL['offerwall_exhs_amt']);
$total_amt_all_ori = number_format($TOTAL['mw_exhs_sdk']+$TOTAL['offerwall_exhs_amt_ori']);
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
			<h4 class="panel-title">키보드 사용 통계</h4>
		</div><!-- panel-heading -->

		<div class="panel-body">

			<form name="scform" method="get" action="">
				<div class="row">
					<div class="card">
						<div class="header">
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
							<?php if($hidden_btn == "show"){ ?>
								<div style="display: inline-block;" class="pull-left">
									<button type="button" class="btn btn-danger" style="margin-left:10px;">모비위드 API 재호출(창 띄우기)</button>
								</div>
							<?php } ?>
							<label style="margin: 4px 0 0 30px">
								<div class="pull-left" style="line-height: 30px;font-weight: bold;">운영체제 선택</div>
							</label>
							<label style="margin: 4px 0 0 20px">
								<div class="pull-left"><input style="width:20px;height:20px;accent-color: darkcyan;" type="checkbox" id="os_type" onclick="display_toggle('');" <?=empty($os_type)?"checked":""?>></div>
								<div class="pull-left" style="padding-top:5px;padding-left:3px;">전체</div>
							</label>
							<label style="margin: 4px 0 0 20px">
								<div class="pull-left"><input style="width:20px;height:20px;accent-color: darkcyan;" type="checkbox" id="os_type_a" onclick="display_toggle('A');" <?=($os_type=="A")?"checked":""?>></div>
								<div class="pull-left" style="padding-top:5px;padding-left:3px;">Android</div>
							</label>
							<label style="margin: 4px 0 0 20px">
								<div class="pull-left"><input style="width:20px;height:20px;accent-color: darkcyan;" type="checkbox" id="os_type_i" onclick="display_toggle('I');" <?=($os_type=="I")?"checked":""?>></div>
								<div class="pull-left" style="padding-top:5px;padding-left:3px;">iOS</div>
							</label>

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
							<th colspan="4" class="col-md-3">사다리 타기 광고 SDK
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>사다리 타기 광고 SDK</strong><br />
										(2일전 데이터 매시갱신)
									</span>
								</a>
							</th>
							<th class="col-md-3" colspan="<?=$hidden_page == "show"?"4":"3"?>">콕 머니쌓기 (OS공통)</th>
                            <th class="col-md-3" colspan="<?=$hidden_page == "show"?"4":"3"?>">핫플레이스 (OS공통)</th>
							<th class="col-md-2" colspan="<?=$hidden_page == "show"?"2":"1"?>"></th>
						</tr>
						<tr>

							<th>노출</th>
							<th>클릭</th>
							<th>클릭율(%)</th>
							<th style="color:blue;">수익(원)</th>

							<th>오퍼월<br />클릭 수</th>
							<th>오퍼월<br />참여 수</th>
							<th style="color:blue;">오퍼월<br />수익(원)</th>
							<?php if($hidden_page == "show"){?>
								<th style="color:blue;">원 오퍼월<br />수익(원)</th>
							<?php }?>

                            <th>오퍼월<br />클릭 수</th>
                            <th>오퍼월<br />참여 수</th>
                            <th style="color:blue;">오퍼월<br />수익(원)</th>
                            <?php if($hidden_page == "show"){?>
                                <th style="color:blue;">원 오퍼월<br />수익(원)</th>
                            <?php }?>

							<th style="color:blue;">총 수익(원)</th>
							<?php if($hidden_page == "show"){?>
							<th style="color:blue;">원 총 수익(원)</th>
							<?php }?>
						</tr>
						</thead>
						<tbody id="ocb" >
						<tr class="" style="background-color: #F2F5A9;">
							<td>합계</td>

							<!-- 사다리 타기 광고 SDK -->
							<td><?=number_format($TOTAL['mw_eprs_sdk'])?></td>
							<td><?=number_format($TOTAL['mw_click_sdk'])?></td>
							<td><?=number_format($TOTAL['mw_ctr_sdk'])?>%</td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs_sdk'])?></td>

							<!-- 오퍼월 -->
							<td><?=number_format($TOTAL['offerwall_click_num'])?></td>
							<td><?=number_format($TOTAL['offerwall_participation'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['offerwall_exhs_amt'])?></td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['offerwall_exhs_amt_ori'])?></td>
							<?php } ?>

                            <!-- 핫플레이스 -->
                            <td><?=number_format($TOTAL['hot_click_num'])?></td>
                            <td><?=number_format($TOTAL['hot_participation'])?></td>
                            <td style="color:blue;font-weight:700;"><?=number_format($TOTAL['hot_exhs_amt'])?></td>
                            <?php if($hidden_page == "show"){?>
                                <td style="color:blue;font-weight:700;"><?=number_format($TOTAL['hot_exhs_amt_ori'])?></td>
                            <?php } ?>

							<td style="color:blue;font-weight:700;"><?=$total_amt_all?></td>
							<?php if($hidden_page == "show"){?>
								<td style="color:blue;font-weight:700;"><?=$total_amt_all_ori?></td>
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

							// 쿠팡 클릭율
							//$TOTAL['cclick_rate'] = ($TOTAL['eprs_num'] > 0) ? ($TOTAL['cclick_num'] / $TOTAL['eprs_num']) * 100 : 0;

							//전체 사용율
							$row['totuseAvg'] = ($row['accumulate'] > 0) ? $row['use_cnt'] / $row['accumulate'] * 100 : 0;

							//합계 평균 사용시간
							$row['useAvg'] = ($row['use_tot_cnt'] > 0) ? round($row['use_time'] / $row['use_tot_cnt'], 1) : 0;

							//합계 평균 사용횟수
							$row['totCntAvg'] = ($row['use_cnt'] > 0) ? round($row['use_tot_cnt'] / $row['use_cnt'], 1) : 0;


							//모비위드 클릭율
							$row['mw_ctr_sdk'] = ($row['mw_eprs_sdk'] > 0) ? number_format($row['mw_click_sdk'] / $row['mw_eprs_sdk'] * 100 , 1) : 0;

							$month_total = $row['mw_exhs_sdk']+$row['offerwall_exhs_amt'];
							$month_total_ori = $row['mw_exhs_sdk']+$row['offerwall_exhs_amt_ori'];
							?>
						<tr class="" style="background-color: #afe076;">
							<td><?=$key?></td>

							<!-- 사다리 타기 광고 SDK -->
							<td><?=number_format($row['mw_eprs_sdk'])?></td>
							<td><?=number_format($row['mw_click_sdk'])?></td>
							<td><?=number_format($row['mw_ctr_sdk'])?>%</td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['mw_exhs_sdk'])?></td>

							<!-- 오퍼월 -->
							<td><?=number_format($row['offerwall_click_num'])?></td>
							<td><?=number_format($row['offerwall_participation'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['offerwall_exhs_amt'])?></td>
							<?php if($hidden_page == "show"){?>
								<td style="color:blue;font-weight:700;"><?=number_format($row['offerwall_exhs_amt_ori'])?></td>
							<?php } ?>

							<td style="color:blue;font-weight:700;"><?=number_format($month_total)?></td>
							<?php if($hidden_page == "show"){?>
								<td style="color:blue;font-weight:700;"><?=number_format($month_total_ori)?></td>
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
					<th>사다리 타기 광고 SDK<br />노출</th>\
					<th>사다리 타기 광고 SDK<br />클릭</th>\
					<th>사다리 타기 광고 SDK<br />클릭율(%)</th>\
					<th style='color:blue;'>사다리 타기 광고 SDK<br />수익(원)</th>\
					<th>콕머니쌓기<br />클릭 수</th>\
					<th>콕머니쌓기<br />참여 수</th>\
					<th style='color:blue;'>콕머니쌓기<br />수익(원)</th>\
                <?php if($hidden_page == "show"){?>
                <th style='color:blue;'>콕머니쌓기<br />원 수익(원)</th>\
                <?php } ?>
                    <th>핫플레이스 <br />클릭 수</th>\
					<th>핫플레이스 <br />참여 수</th>\
					<th style='color:blue;'>핫플레이스 <br />수익(원)</th>\
                <?php if($hidden_page == "show"){?>
                <th style='color:blue;'>핫플레이스<br />원 수익(원)</th>\
                <?php } ?>
                ";
                tab_text = tab_text + "<th style='color:blue;'>총 수익(원)</th>";
				<?php if($hidden_page == "show"){?>
				tab_text = tab_text + "<th style='color:blue;'>원 총 수익(원)</th>";
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
	fnExcelReport('ocb','통계내역');
});

	//외부 통게 데이터 show/hide
	function show_display_partner(stats_dttm){
		var currentRow = $("."+stats_dttm).closest('tr');
		if(currentRow.is(":visible")){
			currentRow.hide();
		} else {
			currentRow.show();
		}
	}

function display_toggle(os_type){
	var startDate = '<?=empty($_GET['startDate'])?"":$_GET['startDate']?>';
	var endDate = '<?=empty($_GET['endDate'])?"":$_GET['endDate']?>';
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
	location.href="?startDate="+startDate+"&endDate="+endDate+"&type="+type+"&os_type="+os_type;
}

$("section").css({"min-width":"1500px"});

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
	})
</script>

<?php
include __foot__;
?>
