<?php
/**********************
 *
 *    통계 페이지 / 일자별 통계
 *
 **********************/

$sdate=date("Ymd",strtotime(" -2 day"));
$edate=date("Ymd",strtotime(" -2 day"));

// 통계 데이터
$sql="
	SELECT
		CDMS.stats_dttm
		,SUM(activity_num) AS activity_num
		,SUM(use_cnt) AS use_cnt
		,SUM(use_time) AS use_time
		,SUM(use_tot_cnt) AS use_tot_cnt
		,IFNULL(CDS11.click_num, 0) + IFNULL(CDS31.click_num, 0) AS mw_click1
		,IFNULL(CDS1.exhs_amt, 0) + IFNULL(CDS21.exhs_amt, 0) AS mw_exhs1

		,IFNULL(CDS12.click_num, 0) + IFNULL(CDS32.click_num, 0) AS mw_click2
		,IFNULL(CDS2.exhs_amt, 0) + IFNULL(CDS22.exhs_amt, 0) AS mw_exhs2

		,IFNULL(CDS13.click_num, 0) + IFNULL(CDS33.click_num, 0) AS mw_click3
		,IFNULL(CDS3.exhs_amt, 0) + IFNULL(CDS23.exhs_amt, 0) AS mw_exhs3
		
		,IFNULL(CDS14.click_num, 0)+ IFNULL(CDS34.click_num, 0) AS mw_click4
		,IFNULL(CDS4.exhs_amt, 0) + IFNULL(CDS24.exhs_amt, 0) AS mw_exhs4

		,IFNULL(CDS15.click_num, 0) + IFNULL(CDS35.click_num, 0) AS mw_click5
		,IFNULL(CDS5.exhs_amt, 0) + IFNULL(CDS25.exhs_amt, 0) AS mw_exhs5

		,IFNULL(CDS16.click_num, 0) + IFNULL(CDS36.click_num, 0) AS mw_click6
		,IFNULL(CDS6.exhs_amt, 0) + IFNULL(CDS26.exhs_amt, 0) AS mw_exhs6

		,IFNULL(CSTATS3.click_num, 0) AS coupang_click_num1
		,IFNULL(CSTATS1.order_commission, 0) + IFNULL(CSTATS5.order_commission, 0) AS coupang_order1
		,IFNULL(CSTATS1.cancel_commission, 0) + IFNULL(CSTATS5.cancel_commission, 0) AS coupang_cancel1

		,IFNULL(CSTATS4.click_num, 0) AS coupang_click_num2
		,IFNULL(CSTATS2.order_commission, 0) + IFNULL(CSTATS6.order_commission, 0) AS coupang_order2
		,IFNULL(CSTATS2.cancel_commission, 0) + IFNULL(CSTATS6.cancel_commission, 0)  AS coupang_cancel2
		,IFNULL(CDS7.eprs_num, 0) AS offerwall_participation
		,IFNULL(CDS7.click_num, 0) AS offerwall_click_num
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

	LEFT JOIN ckd_day_coupang_stats CSTATS1 ON CSTATS1.stats_dttm = CDMS.stats_dttm AND CSTATS1.service_tp_code='01'
	LEFT JOIN ckd_day_coupang_stats CSTATS2 ON CSTATS2.stats_dttm = CDMS.stats_dttm AND CSTATS2.service_tp_code='02'

	LEFT JOIN ckd_day_coupang_stats CSTATS3 ON CSTATS3.stats_dttm = CDMS.stats_dttm AND CSTATS3.service_tp_code='03'
	LEFT JOIN ckd_day_coupang_stats CSTATS4 ON CSTATS4.stats_dttm = CDMS.stats_dttm AND CSTATS4.service_tp_code='04'
	
    LEFT JOIN ckd_day_coupang_stats CSTATS5 ON CSTATS5.stats_dttm = CDMS.stats_dttm AND CSTATS5.service_tp_code='05'
	LEFT JOIN ckd_day_coupang_stats CSTATS6 ON CSTATS6.stats_dttm = CDMS.stats_dttm AND CSTATS6.service_tp_code='06'
	
	WHERE CDMS.stats_dttm BETWEEN {$sdate} AND {$edate}
	GROUP BY CDMS.stats_dttm
	ORDER BY CDMS.stats_dttm DESC
";
//pre($sql);
$ret = $NDO->fetch_array($sql);
//pre($ret);

foreach($ret as $key => $row) {

    $commission = 1;

    //평균 사용 시간
    $useTimeAvg = ($row['use_time'] > 0) ? round($row['use_time'] / $row['use_tot_cnt']) : 0;
    //평균 사용횟수
    $useTotCntAvg = ($row['use_cnt'] > 0) ? round($row['use_tot_cnt'] / $row['use_cnt'], 1) : 0;


    # 커미션 100 으로 보여줌
    //쿠팡 수익 계산 (주문 커미션 - 취소 커미션 ) * 0.8
    $coupang_income1 = ($row['coupang_order1'] + $row['coupang_cancel1']) * $commission;
    $coupang_income2 = ($row['coupang_order2'] + $row['coupang_cancel2']) * $commission;

    $offerwall_commission = $commission;

    $offerwall_exhs_amt = round($row['offerwall_exhs_amt']);
    $row['offerwall_exhs_amt'] = round($row['offerwall_exhs_amt'] * $offerwall_commission);

    $total_sales = $row['mw_exhs1'] + $row['mw_exhs2'] + $row['mw_exhs3'] + $row['mw_exhs4'] + $row['mw_exhs5'] + $row['mw_exhs6'] + $coupang_income1 + $coupang_income2 + $row['offerwall_exhs_amt'];

    $sql = "update paybooc.ckd_day_app_stats set use_tot_cnt='{$total_sales}' where stats_dttm='{$row['stats_dttm']}' ";
    $ret = $NDO->sql_query($sql);

}
?>