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
define('_subMenu_', 'use_new');

include_once __head__;

$ip = $fn->getRealClientIp();
$allow_ip = array('127.0.0.1','221.150.126.74','112.220.254.82','112.171.101.32','221.150.126.75','112.171.26.11');
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
$check_date = "2025-02-07";
if ($check_date > $startDate) {
    $news_show = true;
} else {
    $news_show = false;
}

$os_type = empty($_REQUEST['os_type']) ?"":$_REQUEST['os_type'];
switch($os_type){
	case "A" :
		$add_query = "
			,IFNULL(CDS1.eprs_num, 0) AS mw_eprs_main
			,IFNULL(CDS1.click_num, 0) AS mw_click_main
			,IFNULL(CDS1.exhs_amt, 0) AS mw_exhs_main

			,IFNULL(CDS2.eprs_num, 0) AS mw_eprs_set
			,IFNULL(CDS2.click_num, 0) AS mw_click_set
			,IFNULL(CDS2.exhs_amt, 0) AS mw_exhs_set
		";
		$add_os = " AND os_type = 'A'";
		$add_info = " AND user_app_os = 'A'";
	break;
	case "I" :
		$add_query = "
			,IFNULL(CDS3.eprs_num, 0) AS mw_eprs_main
			,IFNULL(CDS3.click_num, 0) AS mw_click_main
			,IFNULL(CDS3.exhs_amt, 0) AS mw_exhs_main

			,IFNULL(CDS4.eprs_num, 0) AS mw_eprs_set
			,IFNULL(CDS4.click_num, 0) AS mw_click_set
			,IFNULL(CDS4.exhs_amt, 0) AS mw_exhs_set
		";
		$add_os = " AND os_type = 'I'";
		$add_info = " AND (user_app_os is null or user_app_os = 'I')";
	break;
	default  :
		$add_query = "
			,IFNULL(CDS1.eprs_num, 0) + IFNULL(CDS3.eprs_num, 0) AS mw_eprs_main
			,IFNULL(CDS1.click_num, 0) + IFNULL(CDS3.click_num, 0) AS mw_click_main
			,IFNULL(CDS1.exhs_amt, 0) + IFNULL(CDS3.exhs_amt, 0) AS mw_exhs_main
			
			,IFNULL(CDS2.eprs_num, 0) + IFNULL(CDS4.eprs_num, 0) AS mw_eprs_set
			,IFNULL(CDS2.click_num, 0) + IFNULL(CDS4.click_num, 0) AS mw_click_set
			,IFNULL(CDS2.exhs_amt, 0) + IFNULL(CDS4.exhs_amt, 0) AS mw_exhs_set
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

		{$add_query}

		,IFNULL(CDS5.eprs_num, 0)  AS news_eprs_num
		,IFNULL(CDS5.click_num , 0)AS news_click_num
		,IFNULL(CDS5.exhs_amt, 0) AS news_exhs_amt

		,IFNULL(CDS6.eprs_num, 0) AS offerwall_participation
		,IFNULL(CDS6.click_num, 0) AS offerwall_click_num
		,IFNULL(CDS6.exhs_amt, 0) AS offerwall_exhs_amt

		,IFNULL(CSTATS1.click_num, 0) AS brand_click_num
		,IFNULL(CSTATS1.order_commission, 0) AS brand_order
		,IFNULL(CSTATS1.cancel_commission, 0) AS brand_cancel

	FROM ckd_day_app_stats CDMS

	LEFT JOIN ckd_day_ad_stats CDS1 ON CDS1.stats_dttm = CDMS.stats_dttm AND CDS1.service_tp_code='04'
	LEFT JOIN ckd_day_ad_stats CDS2 ON CDS2.stats_dttm = CDMS.stats_dttm AND CDS2.service_tp_code='05'
	LEFT JOIN ckd_day_ad_stats CDS3 ON CDS3.stats_dttm = CDMS.stats_dttm AND CDS3.service_tp_code='06'
	LEFT JOIN ckd_day_ad_stats CDS4 ON CDS4.stats_dttm = CDMS.stats_dttm AND CDS4.service_tp_code='07'
	LEFT JOIN ckd_day_ad_stats CDS5 ON CDS5.stats_dttm = CDMS.stats_dttm AND CDS5.service_tp_code='08'
	LEFT JOIN ckd_day_ad_stats CDS6 ON CDS6.stats_dttm = CDMS.stats_dttm AND CDS6.service_tp_code='09'

	LEFT JOIN ckd_day_ad_stats CDS7 ON CDS7.stats_dttm = CDMS.stats_dttm AND CDS7.service_tp_code='10'
	LEFT JOIN ckd_day_ad_stats CDS8 ON CDS8.stats_dttm = CDMS.stats_dttm AND CDS8.service_tp_code='11'
	LEFT JOIN ckd_day_ad_stats CDS9 ON CDS9.stats_dttm = CDMS.stats_dttm AND CDS9.service_tp_code='12'
	LEFT JOIN ckd_day_ad_stats CDS10 ON CDS10.stats_dttm = CDMS.stats_dttm AND CDS10.service_tp_code='13'

	LEFT JOIN ckd_day_coupang_stats CSTATS1 ON CSTATS1.stats_dttm = CDMS.stats_dttm AND CSTATS1.service_tp_code='01'

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
$make_array1 = array("accumulate","activity_num","accumulate","activity_num","use_cnt","useTotCntAvg","ctr");
$make_array2 = array("brand_click_num","brand_income");
$make_array3 = array("news_eprs_num","news_click_num","news_exhs_amt","noti_click_num","mobfeed_noti","reward_mobon_eprs_num","reward_mobon_click_num","reward_mobon","reward_mobon_ori");
$make_array4 = array("reward_coupang_click_num","reward_coupang_income","reward_coupang_ori");
$make_array5 = array("reward_news_eprs_num","reward_news_click_num","reward_news_income","mobimixer_eprs_num","mobimixer_click_num","mobimixer_income","criteo_eprs_num","criteo_click_num","criteo_income","offerwall_participation","offerwall_click_num","offerwall_exhs_amt");
$make_array6 = array("mw_eprs_main","mw_click_main","mw_exhs_main","mw_ctr_main","mw_eprs_set","mw_click_set","mw_exhs_set","mw_ctr_set","mw_exhs_main_ori","mw_exhs_set_ori","offerwall_exhs_amt_ori");

$make_array = array_merge ($make_array1, $make_array2, $make_array3, $make_array4, $make_array5, $make_array6);
foreach($make_array as $item){
	$TOTAL[$item] = 0;
}

foreach($ret as $key => $row){

	$commission = 1;

	//하나머니 계정에만 정산용 0.9 적용
	if($_SESSION['Adm']['id'] == "hana" && $row['stats_dttm'] >= "20240201"){
		$commission = 0.9;
	}

	# 커미션 100 으로 보여줌
	//쿠팡 수익 계산 (주문 커미션 - 취소 커미션 ) * 0.8
	$brand_income = ($row['brand_order'] + $row['brand_cancel']) * $commission ;

	$offerwall_commission = $commission;
	//하나머니 계정에만 오퍼월 커미션 조정
	if($_SESSION['Adm']['id'] == "hana" &&  $row['stats_dttm'] >= "20240215"){
		$offerwall_commission = $commission*0.95;
	}
	$offerwall_exhs_amt=round($row['offerwall_exhs_amt']);
	$row['offerwall_exhs_amt']=round($row['offerwall_exhs_amt'] * $offerwall_commission);

	//모비위드 CTR
	$mw_ctr_main = ($row['mw_eprs_main'] > 0) ? number_format($row['mw_click_main'] / $row['mw_eprs_main'] * 100, 1) : 0;
	$mw_ctr_set = ($row['mw_eprs_set'] > 0) ? number_format($row['mw_click_set'] / $row['mw_eprs_set'] * 100, 1) : 0;

	//모비위드
	$mw_commission = $commission;

	$row['news_exhs_amt']=round($row['news_exhs_amt'] * $mw_commission);
	$row['mw_exhs_set']=round($row['mw_exhs_set'] * $mw_commission);

	//하나머니 계정에만 메인띠베너 커미션 조정
	if($_SESSION['Adm']['id'] == "hana" && $row['stats_dttm'] >= "20240215"){
		$mw_commission = $commission*0.97;
	}
	$mw_exhs_main = round($row['mw_exhs_main']);
	$mw_exhs_set = round($row['mw_exhs_set']);
	$row['mw_exhs_main']=round($row['mw_exhs_main'] * $mw_commission);

	//하나머니 계정에만 광고 커미션 조정
	if($_SESSION['Adm']['id'] == "hana"){
		$sdk_commission = 0.8;
		if($_SESSION['Adm']['id'] == "hana" && $row['stats_dttm'] >= "20240521"){
			$sdk_commission = 0.75;
		}
	}

	//합계
	$TOTAL['accumulate'] += $accumulate_array[$row['stats_dttm']];//날짜
	$TOTAL['activity_num'] += $row['activity_num'];//누적 설정수
	$TOTAL['use_cnt'] += $row['use_cnt'];//평균 사용자수

	//모비위드 CTR
	$TOTAL['mw_ctr_main'] += $mw_ctr_main;
	$TOTAL['mw_ctr_set'] += $mw_ctr_set;

	$TOTAL['mw_eprs_main'] += $row['mw_eprs_main'];
	$TOTAL['mw_click_main'] += $row['mw_click_main'];
	$TOTAL['mw_exhs_main'] += round($row['mw_exhs_main']);
	$TOTAL['mw_exhs_main_ori'] += round($mw_exhs_main);

	$TOTAL['mw_eprs_set'] += $row['mw_eprs_set'];
	$TOTAL['mw_click_set'] += $row['mw_click_set'];
	$TOTAL['mw_exhs_set'] += round($row['mw_exhs_set']);
	$TOTAL['mw_exhs_set_ori'] += round($mw_exhs_set);

	$TOTAL['brand_click_num'] += $row['brand_click_num'];
	$TOTAL['brand_income'] += round($brand_income);

	$TOTAL['news_eprs_num'] += $row['news_eprs_num'];
	$TOTAL['news_click_num'] += $row['news_click_num'];
	$TOTAL['news_exhs_amt'] += $row['news_exhs_amt'];

	$TOTAL['offerwall_participation'] += $row['offerwall_participation'];
	$TOTAL['offerwall_click_num'] += $row['offerwall_click_num'];
	$TOTAL['offerwall_exhs_amt'] += round($row['offerwall_exhs_amt']);
	$TOTAL['offerwall_exhs_amt_ori'] += round($offerwall_exhs_amt);

	if($type=="3M" || $type=="6M"){
		//합계
		$month = substr($row['stats_dttm'],0,6);
		if(!isset($M_TOTAL[$month])){
			$make_array = array_merge ($make_array1, $make_array2, $make_array3, $make_array4, $make_array5, $make_array6);
			foreach($make_array as $item){
				$M_TOTAL[$month][$item] = 0;
			}
		}

		$M_TOTAL[$month]['accumulate'] += $accumulate_array[$row['stats_dttm']];
		$M_TOTAL[$month]['activity_num'] += $row['activity_num'];
		$M_TOTAL[$month]['use_cnt'] += $row['use_cnt'];

		$M_TOTAL[$month]['mw_eprs_main'] += $row['mw_eprs_main'];
		$M_TOTAL[$month]['mw_click_main'] += $row['mw_click_main'];
		$M_TOTAL[$month]['mw_exhs_main'] += round($row['mw_exhs_main']);
		$M_TOTAL[$month]['mw_exhs_main_ori'] += round($mw_exhs_main);

		$M_TOTAL[$month]['mw_eprs_set'] += $row['mw_eprs_set'];
		$M_TOTAL[$month]['mw_click_set'] += $row['mw_click_set'];
		$M_TOTAL[$month]['mw_exhs_set'] += round($row['mw_exhs_set']);
		$M_TOTAL[$month]['mw_exhs_set_ori'] += round($mw_exhs_set);

		$M_TOTAL[$month]['brand_click_num'] += $row['brand_click_num'];
		$M_TOTAL[$month]['brand_income'] += round($row['brand_click_num']);

		$M_TOTAL[$month]['news_eprs_num'] += $row['news_eprs_num'];
		$M_TOTAL[$month]['news_click_num'] += $row['news_click_num'];
		$M_TOTAL[$month]['news_exhs_amt'] += $row['news_exhs_amt'];

		$M_TOTAL[$month]['offerwall_participation'] += $row['offerwall_participation'];
		$M_TOTAL[$month]['offerwall_click_num'] += $row['offerwall_click_num'];
		$M_TOTAL[$month]['offerwall_exhs_amt'] += round($row['offerwall_exhs_amt']);
		$M_TOTAL[$month]['offerwall_exhs_amt_ori'] += round($offerwall_exhs_amt);

	}

	$css_brand_income = $brand_income<0 ?"color:red;":"color:blue;";
	$css_news_exhs_amt = $row['news_exhs_amt']<0 ?"color:red;":"color:blue;";
	$css_offerwall_exhs_amt = $row['offerwall_exhs_amt']<0 ?"color:red;":"color:blue;";
	$css_mw_main = $row['mw_exhs_main']<0 ?"color:red;":"color:blue;";
	$css_mw_set = $row['mw_exhs_set']<0 ?"color:red;":"color:blue;";

	$total_amt = $row['mw_exhs_main']+$row['mw_exhs_set']+$brand_income+$row['news_exhs_amt']+$row['offerwall_exhs_amt'];
	$total_amt_ori = $mw_exhs_main+$mw_exhs_set+$brand_income+$row['news_exhs_amt']+$offerwall_exhs_amt;

	$css_total_income = $total_amt<0 ?"color:red;":"color:blue;";

	$use_cnt = empty($row['use_cnt'])?0:number_format($row['use_cnt']);

	$html .= "<tr class='{$fn->dateColor($row['stats_dttm'])}'>";
	$html .= "
		<td>{$row['stats_dttm']}</td>

		<td>".number_format($accumulate_array[$row['stats_dttm']])."</td>
		<td style='font-weight:700;'>".number_format($row['activity_num'])."</td>
		<td style='color:red;'>".$use_cnt."</td> 

		<!-- 모비위드 메인 -->
		<td>".number_format($row['mw_eprs_main'])."</td>
		<td>".number_format($row['mw_click_main'])."</td>
		<td>".number_format($mw_ctr_main)."%</td>
		<td style='{$css_mw_main} font-weight:700;'>".number_format($row['mw_exhs_main'])."</td>
	";
	if($hidden_page == "show"){
		$html .= "<td style='{$css_mw_main} font-weight:700;'>".number_format($mw_exhs_main)."</td>";
	}
	$html .= "
		<!-- 모비위드 설정 -->
		<td>".number_format($row['mw_eprs_set'])."</td>
		<td>".number_format($row['mw_click_set'])."</td>
		<td>".number_format($mw_ctr_set)."%</td>
		<td style='{$css_mw_set} font-weight:700;'>".number_format($row['mw_exhs_set'])."</td>
	";
	if($hidden_page == "show"){
		$html .= "<td style='{$css_mw_main} font-weight:700;'>".number_format($mw_exhs_set)."</td>";
	}

	$html .= "
		<!-- 브랜드 -->
		<td>".number_format($row['brand_click_num'])."</td>
		<td style='{$css_brand_income} font-weight:700;'>".number_format($brand_income)."</td>";

    if($news_show == "show"){
        $html .= "
		<!-- 뉴스 -->
		<td>".number_format($row['news_click_num'])."</td>
		<td style='{$css_news_exhs_amt} font-weight:700;'>".number_format($row['news_exhs_amt'])."</td>";
    }

    $html .= "
		<!-- 오퍼월 -->
		<td>".number_format($row['offerwall_click_num'])."</td>
		<td>".number_format($row['offerwall_participation'])."</td>
		<td style='{$css_offerwall_exhs_amt} font-weight:700;'>".number_format($row['offerwall_exhs_amt'])."</td>

	";
	if($hidden_page == "show"){
		$html .= "<td style='{$css_offerwall_exhs_amt} font-weight:700;'>".number_format($offerwall_exhs_amt)."</td>";
	}

		$html .= "
			<!-- 총 수익(원) -->
			<td style='{$css_total_income} font-weight:700;'>".number_format($total_amt)."</td>
		";
	if($hidden_page == "show"){
		$html .= "<td style='{$css_total_income} font-weight:700;'>".number_format($total_amt_ori)."</td>";
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
$TOTAL['mw_ctr_main'] = ($TOTAL['mw_eprs_main'] > 0) ? number_format($TOTAL['mw_click_main'] / $TOTAL['mw_eprs_main'] * 100, 1) : 0;
$TOTAL['mw_ctr_set'] = ($TOTAL['mw_eprs_set'] > 0) ? number_format($TOTAL['mw_click_set'] / $TOTAL['mw_eprs_set'] * 100, 1) : 0;

$reward_eprs_num = $TOTAL['reward_mobon_eprs_num']+$TOTAL['reward_news_eprs_num'];
$reward_click_num = $TOTAL['reward_mobon_click_num']+$TOTAL['reward_coupang_click_num']+$TOTAL['reward_news_click_num'];
$reward_income = $TOTAL['reward_mobon']+$TOTAL['reward_coupang_ori']+$TOTAL['reward_news_income'];

//총수익
$total_amt_all = number_format($TOTAL['mw_exhs_main']+$TOTAL['mw_exhs_set']+$TOTAL['brand_income']+$TOTAL['news_exhs_amt']+$TOTAL['offerwall_exhs_amt']);
$total_amt_all_ori = number_format($TOTAL['mw_exhs_main_ori']+$TOTAL['mw_exhs_set_ori']+$TOTAL['brand_income']+$TOTAL['news_exhs_amt']+$TOTAL['offerwall_exhs_amt_ori']);
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
							<label style="margin: 4px 0 0 80px">
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
							<th colspan="4">앱 연동</th>

							<th colspan="<?=$hidden_page=="show"?"5":"4"?>">메인띠배너
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>메인띠배너 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
								</a>
							</th>
							<th colspan="<?=$hidden_page=="show"?"5":"4"?>">설정띠배너
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>설정띠배너 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
								</a>
							</th>

							<th colspan="2">쿠팡 연동 (OS공통)
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>브랜드 광고</strong><br />
										쿠팡 데이터 (매일 오후04:50분 전일 데이터 갱신)
									</span>
								</a>
							</th>
                            <?php if($news_show){ ?>
							<th colspan="2">모비피드 연동 (OS공통)
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>모비피드 연동</strong><br />
										API 스케쥴링 (매일 13시 26분 갱신)
									</span>
								</a>
							</th>
                            <?php } ?>

							<th colspan="<?=$hidden_page == "show"?"4":"3"?>">포미션 연동 (OS공통)</th>
							<th colspan="<?=$hidden_page == "show"?"2":"1"?>"></th>

						</tr>
						<tr>
							<th>날짜</th>
							<th>누적 설정수</th>
							<th>신규 설정수
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
									<strong>신규 설정수</strong><br />
									일일 신규 사용자수
									</span>
								</a>
							</th>
							<th style="color:red;">평균 사용자수
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>사용자수</strong><br />
										일일 접속한 사용자수
									</span>
								</a>
							</th>

							<th>노출</th>
							<th>클릭</th>
							<th>클릭율(%)</th>
							<th style="color:blue;">수익(원)</th>
							<?php if($hidden_page == "show"){?>
							<th style="color:blue;">원 수익(원)</th>
							<?php }?>

							<th>노출</th>
							<th>클릭</th>
							<th>클릭율(%)</th>
							<th style="color:blue;">수익(원)</th>
							<?php if($hidden_page == "show"){?>
							<th style="color:blue;">원 수익(원)</th>
							<?php }?>

							<th>브랜드 광고<br />클릭 수</th>
							<th style="color:blue;">브랜드 광고<br />수익(원)</th>

                            <?php if($news_show){ ?>
							<th>뉴스<br />클릭 수</th>
							<th style="color:blue;">뉴스<br />수익(원)</th>
                            <?php } ?>

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
							<td></td>
							<td style='font-weight:700;'><?=number_format($TOTAL['activity_num'])?></td>
							<td style="color:red;"><?=number_format($TOTAL['use_cnt'])?></td>

							<!-- 메인띠베너 -->
							<td><?=number_format($TOTAL['mw_eprs_main'])?></td>
							<td><?=number_format($TOTAL['mw_click_main'])?></td>
							<td><?=number_format($TOTAL['mw_ctr_main'])?>%</td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs_main'])?></td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs_main_ori'])?></td>
							<?php }?>

							<!-- 설정띠베너 -->
							<td><?=number_format($TOTAL['mw_eprs_set'])?></td>
							<td><?=number_format($TOTAL['mw_click_set'])?></td>
							<td><?=number_format($TOTAL['mw_ctr_set'])?>%</td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs_set'])?></td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs_set_ori'])?></td>
							<?php }?>

							<!-- 브랜드 -->
							<td><?=number_format($TOTAL['brand_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['brand_income'])?></td>

                            <?php if($news_show){ ?>
							<!-- 뉴스 -->
							<td><?=number_format($TOTAL['news_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['news_exhs_amt'])?></td>
                            <?php } ?>

							<!-- 오퍼월 -->
							<td><?=number_format($TOTAL['offerwall_click_num'])?></td>
							<td><?=number_format($TOTAL['offerwall_participation'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['offerwall_exhs_amt'])?></td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['offerwall_exhs_amt_ori'])?></td>
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

							$reward_eprs_num = $row['reward_mobon_eprs_num']+$row['reward_news_eprs_num'];
							$reward_click_num = $row['reward_mobon_click_num']+$row['reward_coupang_click_num']+$row['reward_news_click_num'];
							$reward_income = $row['reward_mobon']+$row['reward_coupang_ori']+$row['reward_news_income'];

							//모비위드 클릭율
							$row['mw_ctr_main'] = ($row['mw_eprs_main'] > 0) ? number_format($row['mw_click_main'] / $row['mw_eprs_main'] * 100 , 1) : 0;
							$row['mw_ctr_set'] = ($row['mw_eprs_set'] > 0) ? number_format($row['mw_click_set'] / $row['mw_eprs_set'] * 100 , 1) : 0;

							$month_total = $row['mw_exhs_main']+$row['mw_exhs_set']+$row['brand_income']+$row['news_exhs_amt']+$row['offerwall_exhs_amt'];
							$month_total_ori = $row['mw_exhs_main_ori']+$row['mw_exhs_set_ori']+$row['brand_income']+$row['news_exhs_amt']+$row['offerwall_exhs_amt_ori'];
							?>
						<tr class="" style="background-color: #afe076;">
							<td><?=$key?></td>
							<td></td>
							<td style='font-weight:700;'><?=number_format($row['activity_num'])?></td>
							<td style="color:red;"><?=number_format($row['use_cnt']/$this_month_day_cnt)?></td>

							<!-- 메인띠베너 -->
							<td><?=number_format($row['mw_eprs_main'])?></td>
							<td><?=number_format($row['mw_click_main'])?></td>
							<td><?=number_format($row['mw_ctr_main'])?>%</td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['mw_exhs_main'])?></td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($row['mw_exhs_main_ori'])?></td>
							<?php } ?>

							<!-- 설정띠베너 -->
							<td><?=number_format($row['mw_eprs_set'])?></td>
							<td><?=number_format($row['mw_click_set'])?></td>
							<td><?=number_format($row['mw_ctr_set'])?>%</td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['mw_exhs_set'])?></td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($row['mw_exhs_set_ori'])?></td>
							<?php } ?>

							<!-- 브랜드 -->
							<td><?=number_format($row['brand_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['brand_income'])?></td>

                            <?php if($news_show){ ?>
							<!-- 뉴스 -->
							<td><?=number_format($row['news_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['news_exhs_amt'])?></td>
                            <?php } ?>
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
					<th>누적 설정수</th>\
					<th>신규 설정수</th>\
					<th style='color:red;'>평균 사용자수</th>\
					<th>메인띠배너<br />노출</th>\
					<th>메인띠배너<br />클릭</th>\
					<th>메인띠배너<br />클릭율(%)</th>\
					<th style='color:blue;'>메인띠배너<br />수익(원)</th>\
					";
		<?php if($hidden_page == "show"){?>
		tab_text = tab_text + "<th style='color:blue;'>메인띠배너<br />원 수익(원)</th>";
		<?php } ?>

		tab_text = tab_text + "<th>설정띠배너<br />노출</th>\
					<th>설정띠배너<br />클릭</th>\
					<th>설정띠배너<br />클릭율(%)</th>\
					<th style='color:blue;'>설정띠배너<br />수익(원)</th>";
		<?php if($hidden_page == "show"){?>
		tab_text = tab_text + "<th style='color:blue;'>설정띠배너<br />원 수익(원)</th>";
		<?php } ?>

		tab_text = tab_text +
			"<th>브랜드 광고<br />클릭 수</th>\
			<th style='color:blue;'>브랜드 광고<br />수익(원)</th>\
			<th>뉴스광고<br />클릭 수</th>\
			<th style='color:blue;'>뉴스광고<br />수익(원)</th>\
			<th>오퍼월<br />클릭 수</th>\
			<th>오퍼월<br />참여 수</th>\
			<th style='color:blue;'>오퍼월<br />수익(원)</th>";
		<?php if($hidden_page == "show"){?>
		tab_text = tab_text + "<th style='color:blue;'>오퍼월<br />원 수익(원)</th>";
		<?php } ?>

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

$("section").css({"min-width":"2200px"});


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
