<?php
/*************************************************
 *      OCB 적립 통계 누적 데이터 INSERT
 *************************************************/
if(!empty($_REQUEST['postman']) && $_REQUEST['postman'] == "postman"){
	include_once '../paybooc/var_cron.php';
	include_once __pdoDB__;    ## DB Instance 생성
	include_once __fn__;

}else{
	$path = '/home/paybooc/public_html/';
	include_once $path . 'paybooc/var_cron.php';
	include_once $path . 'Class/Class.Func.php';
	include_once $path . 'Class/Class.PDO.DB.php';
}

set_time_limit(0);

$date = empty($_REQUEST['date'])?"":$_REQUEST['date'];
if(empty($date)){
	$today = date('Ymd');
	$date_ago = $today;
}else{
	if(strlen($date) != 2){
		exit("조회 가능한 달이 아닙니다");
	}
	$year = date('Y');
	$lastDay = date('t', strtotime($year.$date."01"));
	$date_ago = $year.$date.$lastDay;
}
//pre($date_ago);

//집계방식
$first_day = substr($date_ago,0,6)."01";

$sql="
SELECT
	CDMS.stats_dttm
	,(IFNULL(CDS1.exhs_amt, 0) + IFNULL(CDS2.exhs_amt, 0) + IFNULL(CDS3.exhs_amt, 0) + IFNULL(CDS4.exhs_amt, 0) + IFNULL(CDS5.exhs_amt, 0) + IFNULL(CDS6.exhs_amt, 0)) +
	(IFNULL(CDS21.exhs_amt, 0) + IFNULL(CDS22.exhs_amt, 0) + IFNULL(CDS23.exhs_amt, 0) + IFNULL(CDS24.exhs_amt, 0) + IFNULL(CDS25.exhs_amt, 0) + IFNULL(CDS26.exhs_amt, 0)) AS mw_ad1
	,IFNULL(CSTATS3.order_commission, 0) + IFNULL(CSTATS3.cancel_commission, 0) + IFNULL(CSTATS4.order_commission, 0) + IFNULL(CSTATS4.cancel_commission, 0) AS mw_coupang1

	,(IFNULL(CDS11.click_num, 0) + IFNULL(CDS12.click_num, 0) + IFNULL(CDS13.click_num, 0) + IFNULL(CDS14.click_num, 0) + IFNULL(CDS15.click_num, 0) + IFNULL(CDS16.click_num, 0)) *7 +
	(IFNULL(CDS31.click_num, 0) + IFNULL(CDS32.click_num, 0) + IFNULL(CDS33.click_num, 0) + IFNULL(CDS34.click_num, 0) + IFNULL(CDS35.click_num, 0) + IFNULL(CDS36.click_num, 0)) * 7 AS mw_ad2
	,(IFNULL(CSTATS1.click_num, 0) + IFNULL(CSTATS2.click_num, 0)) * 3.5 AS mw_coupang2
	
	,IFNULL(CDS7.exhs_amt, 0) AS offerwall_exhs_amt

	FROM ckd_day_app_stats CDMS
	LEFT JOIN ckd_day_ad_stats CDS1 ON CDS1.stats_dttm = CDMS.stats_dttm AND CDS1.service_tp_code='01'
	LEFT JOIN ckd_day_ad_stats CDS2 ON CDS2.stats_dttm = CDMS.stats_dttm AND CDS2.service_tp_code='02'
	LEFT JOIN ckd_day_ad_stats CDS3 ON CDS3.stats_dttm = CDMS.stats_dttm AND CDS3.service_tp_code='03'
	LEFT JOIN ckd_day_ad_stats CDS4 ON CDS4.stats_dttm = CDMS.stats_dttm AND CDS4.service_tp_code='04'
	LEFT JOIN ckd_day_ad_stats CDS5 ON CDS5.stats_dttm = CDMS.stats_dttm AND CDS5.service_tp_code='05'
	LEFT JOIN ckd_day_ad_stats CDS6 ON CDS6.stats_dttm = CDMS.stats_dttm AND CDS6.service_tp_code='06'

	LEFT JOIN ckd_day_ad_stats CDS7 ON CDS7.stats_dttm = CDMS.stats_dttm AND CDS7.service_tp_code='07'

	LEFT JOIN ckd_day_ad_stats CDS11 ON CDS11.stats_dttm = CDMS.stats_dttm AND CDS11.service_tp_code='11'
	LEFT JOIN ckd_day_ad_stats CDS12 ON CDS12.stats_dttm = CDMS.stats_dttm AND CDS12.service_tp_code='12'
	LEFT JOIN ckd_day_ad_stats CDS13 ON CDS13.stats_dttm = CDMS.stats_dttm AND CDS13.service_tp_code='13'
	LEFT JOIN ckd_day_ad_stats CDS14 ON CDS14.stats_dttm = CDMS.stats_dttm AND CDS14.service_tp_code='14'
	LEFT JOIN ckd_day_ad_stats CDS15 ON CDS15.stats_dttm = CDMS.stats_dttm AND CDS15.service_tp_code='15'
	LEFT JOIN ckd_day_ad_stats CDS16 ON CDS16.stats_dttm = CDMS.stats_dttm AND CDS16.service_tp_code='16'

	LEFT JOIN ckd_day_ad_stats CDS21 ON CDS21.stats_dttm = CDMS.stats_dttm AND CDS21.service_tp_code='21'
	LEFT JOIN ckd_day_ad_stats CDS22 ON CDS22.stats_dttm = CDMS.stats_dttm AND CDS22.service_tp_code='22'
	LEFT JOIN ckd_day_ad_stats CDS23 ON CDS23.stats_dttm = CDMS.stats_dttm AND CDS23.service_tp_code='23'
	LEFT JOIN ckd_day_ad_stats CDS24 ON CDS24.stats_dttm = CDMS.stats_dttm AND CDS24.service_tp_code='24'
	LEFT JOIN ckd_day_ad_stats CDS25 ON CDS25.stats_dttm = CDMS.stats_dttm AND CDS25.service_tp_code='25'
	LEFT JOIN ckd_day_ad_stats CDS26 ON CDS26.stats_dttm = CDMS.stats_dttm AND CDS26.service_tp_code='26'

	LEFT JOIN ckd_day_ad_stats CDS31 ON CDS31.stats_dttm = CDMS.stats_dttm AND CDS31.service_tp_code='31'
	LEFT JOIN ckd_day_ad_stats CDS32 ON CDS32.stats_dttm = CDMS.stats_dttm AND CDS32.service_tp_code='32'
	LEFT JOIN ckd_day_ad_stats CDS33 ON CDS33.stats_dttm = CDMS.stats_dttm AND CDS33.service_tp_code='33'
	LEFT JOIN ckd_day_ad_stats CDS34 ON CDS34.stats_dttm = CDMS.stats_dttm AND CDS34.service_tp_code='34'
	LEFT JOIN ckd_day_ad_stats CDS35 ON CDS35.stats_dttm = CDMS.stats_dttm AND CDS35.service_tp_code='35'
	LEFT JOIN ckd_day_ad_stats CDS36 ON CDS36.stats_dttm = CDMS.stats_dttm AND CDS36.service_tp_code='36'

	LEFT JOIN ckd_day_coupang_stats CSTATS1 ON CSTATS1.stats_dttm = CDMS.stats_dttm AND CSTATS1.service_tp_code='03'
	LEFT JOIN ckd_day_coupang_stats CSTATS2 ON CSTATS2.stats_dttm = CDMS.stats_dttm AND CSTATS2.service_tp_code='04'

	LEFT JOIN ckd_day_coupang_stats CSTATS3 ON CSTATS3.stats_dttm = CDMS.stats_dttm AND CSTATS3.service_tp_code='01'
	LEFT JOIN ckd_day_coupang_stats CSTATS4 ON CSTATS4.stats_dttm = CDMS.stats_dttm AND CSTATS4.service_tp_code='02'

	WHERE CDMS.stats_dttm BETWEEN {$first_day} AND {$date_ago}
	GROUP BY CDMS.stats_dttm
	ORDER BY CDMS.stats_dttm DESC
";
//pre($sql);
$result = $NDO->fetch_array($sql);
//pre($result);

$mw_ad1 = 0;
$mw_coupang1 = 0;
$mw_ad2 = 0;
$mw_coupang2 = 0;
$offerwall_exhs_amt = 0;
$sales = 0;
$settlement1 = 0;
$settlement2 = 0;

foreach ($result as $row){
	$mw_ad1 += $row['mw_ad1'];
	$mw_coupang1 += $row['mw_coupang1'];
	$mw_ad2 += $row['mw_ad2'];
	$mw_coupang2 += $row['mw_coupang2'];
	$offerwall_exhs_amt += $row['offerwall_exhs_amt'];

	$sales = $mw_ad1 + $mw_coupang1 + $offerwall_exhs_amt;
	$settlement1 = $sales * 0.7;
	$settlement2 = $mw_ad2 + $mw_coupang2 + $offerwall_exhs_amt;
}
if($settlement1 > $settlement2){
	$sql = "
		INSERT INTO ckd_day_stats_data SET
			stats_dttm = :date_ago
			,adjustment_type = '1'
			,sales = '{$sales}'
			,settlement = '{$settlement1}'
			,price_ad = '{$mw_ad1}'
			,price_coupang = '{$mw_coupang1}'
			,price_offerwall = '{$offerwall_exhs_amt}'
			,reg_date = NOW()
		 ON DUPLICATE KEY UPDATE 
			sales = '{$sales}'
			,settlement = '{$settlement1}'
			,price_ad = '{$mw_ad1}'
			,price_coupang = '{$mw_coupang1}'
			,price_offerwall = '{$offerwall_exhs_amt}'
			,edit_date = NOW()
	";
}else{
	$sql = "
		INSERT INTO ckd_day_stats_data SET
			stats_dttm = :date_ago
			,adjustment_type = '2'
			,sales = '{$sales}'
			,settlement = '{$settlement2}'
			,price_ad = '{$mw_ad2}'
			,price_coupang = '{$mw_coupang2}'
			,price_offerwall = '{$offerwall_exhs_amt}'
			,reg_date = NOW()
		 ON DUPLICATE KEY UPDATE 
			sales = '{$sales}'
			,settlement = '{$settlement2}'
			,price_ad = '{$mw_ad1}'
			,price_coupang = '{$mw_coupang1}'
			,price_offerwall = '{$offerwall_exhs_amt}'
			,edit_date = NOW()
	";

}
//pre($sql);
$NDO->sql_query($sql,[":date_ago"=>substr($date_ago,0,6)]);

echo "OK";
