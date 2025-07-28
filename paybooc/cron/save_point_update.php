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

//적립요청 사용자 수, 적립 요청 수,적립 금액 누적데이터 일괄 등록 및 업데이트
$today = empty($_REQUEST['today'])?"":$_REQUEST['today'];
if(empty($today)){
	$today = date('Ymd');
	$date_ago = $today;
}else{
	$date_ago = $today;
}
//pre($date_ago);
if(strlen($today) != 8){
	exit("date check!");
}

$sql="
	INSERT IGNORE INTO ckd_save_point_one_day (stats_dttm,os_type,user_cnt, point_cnt) 
	VALUES (:today, 'A', '0', '0'), (:today, 'I', '0', '0')
";
$row = $NDO->sql_query($sql,[":today"=>$today]);

//배너1(AOS)
$sql="
	INSERT INTO ckd_save_point_one_day (stats_dttm,os_type,spot_point_cnt1,spot_sum_point1)
	SELECT stats_dttm,os_type,spot_point_cnt1,spot_sum_point1
	FROM (
		SELECT :date_ago AS stats_dttm,'A' AS os_type,count(*) spot_point_cnt1,IFNULL(sum(point),0) spot_sum_point1
		FROM api_point WHERE reg_date_num = :date_ago AND zone='10885953' AND code=200
	) T ON DUPLICATE KEY UPDATE spot_point_cnt1 = T.spot_point_cnt1, spot_sum_point1 = T.spot_sum_point1, reg_date = NOW()
";
//pre($sql);
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

//배너2(AOS)
$sql="
	INSERT INTO ckd_save_point_one_day (stats_dttm,os_type,spot_point_cnt2,spot_sum_point2)
	SELECT stats_dttm,os_type,spot_point_cnt2,spot_sum_point2
	FROM (
		SELECT :date_ago AS stats_dttm,'A' AS os_type,count(*) spot_point_cnt2,IFNULL(sum(point),0) spot_sum_point2
		FROM api_point WHERE reg_date_num = :date_ago AND zone='10885954' AND code=200
	) T ON DUPLICATE KEY UPDATE  spot_point_cnt2 = T.spot_point_cnt2, spot_sum_point2 = T.spot_sum_point2, reg_date = NOW()
";
//pre($sql);
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

//배너3(AOS)
$sql="
	INSERT INTO ckd_save_point_one_day (stats_dttm,os_type,spot_point_cnt3,spot_sum_point3)
	SELECT stats_dttm,os_type,spot_point_cnt3,spot_sum_point3
	FROM (
		SELECT :date_ago AS stats_dttm,'A' AS os_type,count(*) spot_point_cnt3,IFNULL(sum(point),0) spot_sum_point3
		FROM api_point WHERE reg_date_num = :date_ago AND zone='10885955' AND code=200
	) T ON DUPLICATE KEY UPDATE  spot_point_cnt3 = T.spot_point_cnt3, spot_sum_point3 = T.spot_sum_point3, reg_date = NOW()
";
//pre($sql);
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

//배너1(iOS)
$sql="
	INSERT INTO ckd_save_point_one_day (stats_dttm,os_type,spot_point_cnt4,spot_sum_point4)
	SELECT stats_dttm,os_type,spot_point_cnt4,spot_sum_point4
	FROM (
		SELECT :date_ago AS stats_dttm,'I' AS os_type,count(*) spot_point_cnt4,IFNULL(sum(point),0) spot_sum_point4
		FROM api_point WHERE reg_date_num = :date_ago AND zone='10885957' AND code=200
	) T ON DUPLICATE KEY UPDATE spot_point_cnt4 = T.spot_point_cnt4, spot_sum_point4 = T.spot_sum_point4, reg_date = NOW()
";
//pre($sql);
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

//배너2(iOS)
$sql="
	INSERT INTO ckd_save_point_one_day (stats_dttm,os_type,spot_point_cnt5,spot_sum_point5)
	SELECT stats_dttm,os_type,spot_point_cnt5,spot_sum_point5
	FROM (
		SELECT :date_ago AS stats_dttm,'I' AS os_type,count(*) spot_point_cnt5,IFNULL(sum(point),0) spot_sum_point5
		FROM api_point WHERE reg_date_num = :date_ago AND zone='10885958' AND code=200
	) T ON DUPLICATE KEY UPDATE spot_point_cnt5 = T.spot_point_cnt5, spot_sum_point5 = T.spot_sum_point5, reg_date = NOW()
";
//pre($sql);
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

//배너3(iOS)
$sql="
	INSERT INTO ckd_save_point_one_day (stats_dttm,os_type,spot_point_cnt6,spot_sum_point6)
	SELECT stats_dttm,os_type,spot_point_cnt6,spot_sum_point6
	FROM (
		SELECT :date_ago AS stats_dttm,'I' AS os_type,count(*) spot_point_cnt6,IFNULL(sum(point),0) spot_sum_point6
		FROM api_point WHERE reg_date_num = :date_ago AND zone='10885959' AND code=200
	) T ON DUPLICATE KEY UPDATE spot_point_cnt6 = T.spot_point_cnt6, spot_sum_point6 = T.spot_sum_point6, reg_date = NOW()
";
//pre($sql);
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);


//쿠팡광고 2개
$sql="
	INSERT INTO ckd_save_point_one_day (stats_dttm,os_type,spot_point_cnt7,spot_sum_point7)
	SELECT stats_dttm,os_type,spot_point_cnt7,spot_sum_point7
	FROM (
		SELECT '{$date_ago}' AS stats_dttm,'A' AS os_type,count(*) spot_point_cnt7,IFNULL(sum(point),0) spot_sum_point7
		FROM api_point WHERE reg_date_num = '{$date_ago}' AND zone IN('payboocADAPIaos','repayboocADAPIaos') AND code=200
	) T ON DUPLICATE KEY UPDATE spot_point_cnt7 = T.spot_point_cnt7, spot_sum_point7 = T.spot_sum_point7, reg_date = NOW()
";
$NDO->sql_query($sql);

$sql="
	INSERT INTO ckd_save_point_one_day (stats_dttm,os_type,spot_point_cnt8,spot_sum_point8)
	SELECT stats_dttm,os_type,spot_point_cnt8,spot_sum_point8
	FROM (
		SELECT '{$date_ago}' AS stats_dttm,'I' AS os_type,count(*) spot_point_cnt8,IFNULL(sum(point),0) spot_sum_point8
		FROM api_point WHERE reg_date_num = '{$date_ago}' AND zone IN('payboocADAPIios','repayboocADAPIios') AND code=200
	) T ON DUPLICATE KEY UPDATE spot_point_cnt8 = T.spot_point_cnt8, spot_sum_point8 = T.spot_sum_point8, reg_date = NOW()
";
$NDO->sql_query($sql);

//오퍼월
$sql="
	INSERT INTO ckd_save_point_one_day (stats_dttm,os_type,offerwall_point_cnt,offerwall_sum_point)
	SELECT reg_date_num,os_type,offerwall_point_cnt,offerwall_sum_point
	FROM (
		SELECT reg_date_num,'A' AS os_type,count(*) offerwall_point_cnt,IFNULL(sum(point),0) offerwall_sum_point 
		FROM api_offerwall A 
		JOIN ckd_user_info B ON A.user_id = B.user_uuid WHERE reg_date_num = {$date_ago} AND code=200 GROUP BY reg_date_num
	) T ON DUPLICATE KEY UPDATE offerwall_point_cnt = T.offerwall_point_cnt, offerwall_sum_point = T.offerwall_sum_point, reg_date = NOW()
";
//pre($sql);
$NDO->sql_query($sql);

//오퍼월
$sql="
	INSERT INTO ckd_save_point_one_day (stats_dttm,os_type,offerwall_point_cnt,offerwall_sum_point)
	SELECT reg_date_num,os_type,offerwall_point_cnt,offerwall_sum_point
	FROM (
		SELECT reg_date_num,'I' AS os_type,count(*) offerwall_point_cnt,IFNULL(sum(point),0) offerwall_sum_point 
		FROM api_offerwall A 
		JOIN ckd_user_info B ON A.user_id = B.user_uuid WHERE reg_date_num = {$date_ago} AND code=200 GROUP BY reg_date_num
	) T ON DUPLICATE KEY UPDATE offerwall_point_cnt = T.offerwall_point_cnt, offerwall_sum_point = T.offerwall_sum_point, reg_date = NOW()
";
//pre($sql);
$NDO->sql_query($sql);

//zone id 추가
//배너1(AOS)
$sql="
	INSERT INTO ckd_save_point_one_day (stats_dttm,os_type,spot_point_cnt11,spot_sum_point11)
	SELECT stats_dttm,os_type,spot_point_cnt11,spot_sum_point11
	FROM (
		SELECT :date_ago AS stats_dttm,'A' AS os_type,count(*) spot_point_cnt11,IFNULL(sum(point),0) spot_sum_point11
		FROM api_point WHERE reg_date_num = :date_ago AND zone='10886105' AND code=200
	) T ON DUPLICATE KEY UPDATE spot_point_cnt11 = T.spot_point_cnt11, spot_sum_point11 = T.spot_sum_point11, reg_date = NOW()
";
//pre($sql);
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

//배너2(AOS)
$sql="
	INSERT INTO ckd_save_point_one_day (stats_dttm,os_type,spot_point_cnt12,spot_sum_point12)
	SELECT stats_dttm,os_type,spot_point_cnt12,spot_sum_point12
	FROM (
		SELECT :date_ago AS stats_dttm,'A' AS os_type,count(*) spot_point_cnt12,IFNULL(sum(point),0) spot_sum_point12
		FROM api_point WHERE reg_date_num = :date_ago AND zone='10886106' AND code=200
	) T ON DUPLICATE KEY UPDATE  spot_point_cnt12 = T.spot_point_cnt12, spot_sum_point12 = T.spot_sum_point12, reg_date = NOW()
";
//pre($sql);
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

//배너3(AOS)
$sql="
	INSERT INTO ckd_save_point_one_day (stats_dttm,os_type,spot_point_cnt13,spot_sum_point13)
	SELECT stats_dttm,os_type,spot_point_cnt13,spot_sum_point13
	FROM (
		SELECT :date_ago AS stats_dttm,'A' AS os_type,count(*) spot_point_cnt13,IFNULL(sum(point),0) spot_sum_point13
		FROM api_point WHERE reg_date_num = :date_ago AND zone='10886107' AND code=200
	) T ON DUPLICATE KEY UPDATE  spot_point_cnt13 = T.spot_point_cnt13, spot_sum_point13 = T.spot_sum_point13, reg_date = NOW()
";
//pre($sql);
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

//배너1(iOS)
$sql="
	INSERT INTO ckd_save_point_one_day (stats_dttm,os_type,spot_point_cnt14,spot_sum_point14)
	SELECT stats_dttm,os_type,spot_point_cnt14,spot_sum_point14
	FROM (
		SELECT :date_ago AS stats_dttm,'I' AS os_type,count(*) spot_point_cnt14,IFNULL(sum(point),0) spot_sum_point14
		FROM api_point WHERE reg_date_num = :date_ago AND zone='10886102' AND code=200
	) T ON DUPLICATE KEY UPDATE spot_point_cnt14 = T.spot_point_cnt14, spot_sum_point14 = T.spot_sum_point14, reg_date = NOW()
";
//pre($sql);
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

//배너2(iOS)
$sql="
	INSERT INTO ckd_save_point_one_day (stats_dttm,os_type,spot_point_cnt15,spot_sum_point15)
	SELECT stats_dttm,os_type,spot_point_cnt15,spot_sum_point15
	FROM (
		SELECT :date_ago AS stats_dttm,'I' AS os_type,count(*) spot_point_cnt15,IFNULL(sum(point),0) spot_sum_point15
		FROM api_point WHERE reg_date_num = :date_ago AND zone='10886103' AND code=200
	) T ON DUPLICATE KEY UPDATE spot_point_cnt15 = T.spot_point_cnt15, spot_sum_point15 = T.spot_sum_point15, reg_date = NOW()
";
//pre($sql);
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

//배너3(iOS)
$sql="
	INSERT INTO ckd_save_point_one_day (stats_dttm,os_type,spot_point_cnt16,spot_sum_point16)
	SELECT stats_dttm,os_type,spot_point_cnt16,spot_sum_point16
	FROM (
		SELECT :date_ago AS stats_dttm,'I' AS os_type,count(*) spot_point_cnt16,IFNULL(sum(point),0) spot_sum_point16
		FROM api_point WHERE reg_date_num = :date_ago AND zone='10886104' AND code=200
	) T ON DUPLICATE KEY UPDATE spot_point_cnt16 = T.spot_point_cnt16, spot_sum_point16 = T.spot_sum_point16, reg_date = NOW()
";
//pre($sql);
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);



//적립내역으로 자체 클릭잡기
//일반 광고 6개
$sql="
	INSERT INTO ckd_day_ad_stats (stats_dttm,service_tp_code,partner_no,click_num)
	SELECT stats_dttm,service_tp_code,partner_no,click_num
	FROM (
	SELECT :date_ago AS stats_dttm, '11' AS service_tp_code, '10885953' AS partner_no, count(*) click_num 
	FROM api_point WHERE reg_date_num=:date_ago AND zone='10885953' AND code=200
	) T ON DUPLICATE KEY UPDATE click_num = T.click_num, reg_dttm = NOW()
";
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

$sql="
	INSERT INTO ckd_day_ad_stats (stats_dttm,service_tp_code,partner_no,click_num)
	SELECT stats_dttm,service_tp_code,partner_no,click_num
	FROM (
	SELECT :date_ago AS stats_dttm, '12' AS service_tp_code, '10885954' AS partner_no, count(*) click_num 
	FROM api_point WHERE reg_date_num=:date_ago AND zone='10885954' AND code=200
	) T ON DUPLICATE KEY UPDATE click_num = T.click_num, reg_dttm = NOW()
";
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

$sql="
	INSERT INTO ckd_day_ad_stats (stats_dttm,service_tp_code,partner_no,click_num)
	SELECT stats_dttm,service_tp_code,partner_no,click_num
	FROM (
	SELECT :date_ago AS stats_dttm, '13' AS service_tp_code, '10885955' AS partner_no, count(*) click_num 
	FROM api_point WHERE reg_date_num=:date_ago AND zone='10885955' AND code=200
	) T ON DUPLICATE KEY UPDATE click_num = T.click_num, reg_dttm = NOW()
";
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

$sql="
	INSERT INTO ckd_day_ad_stats (stats_dttm,service_tp_code,partner_no,click_num)
	SELECT stats_dttm,service_tp_code,partner_no,click_num
	FROM (
	SELECT :date_ago AS stats_dttm, '14' AS service_tp_code, '10885957' AS partner_no, count(*) click_num 
	FROM api_point WHERE reg_date_num=:date_ago AND zone='10885957' AND code=200
	) T ON DUPLICATE KEY UPDATE click_num = T.click_num, reg_dttm = NOW()
";
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

$sql="
	INSERT INTO ckd_day_ad_stats (stats_dttm,service_tp_code,partner_no,click_num)
	SELECT stats_dttm,service_tp_code,partner_no,click_num
	FROM (
	SELECT :date_ago AS stats_dttm, '15' AS service_tp_code, '10885958' AS partner_no, count(*) click_num 
	FROM api_point WHERE reg_date_num=:date_ago AND zone='10885958' AND code=200
	) T ON DUPLICATE KEY UPDATE click_num = T.click_num, reg_dttm = NOW()
";
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

$sql="
	INSERT INTO ckd_day_ad_stats (stats_dttm,service_tp_code,partner_no,click_num)
	SELECT stats_dttm,service_tp_code,partner_no,click_num
	FROM (
	SELECT :date_ago AS stats_dttm, '16' AS service_tp_code, '10885959' AS partner_no, count(*) click_num 
	FROM api_point WHERE reg_date_num=:date_ago AND zone='10885959' AND code=200
	) T ON DUPLICATE KEY UPDATE click_num = T.click_num, reg_dttm = NOW()
";
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);


//zoneId 추가 6개
$sql="
	INSERT INTO ckd_day_ad_stats (stats_dttm,service_tp_code,partner_no,click_num)
	SELECT stats_dttm,service_tp_code,partner_no,click_num
	FROM (
	SELECT :date_ago AS stats_dttm, '31' AS service_tp_code, '10886105' AS partner_no, count(*) click_num 
	FROM api_point WHERE reg_date_num=:date_ago AND zone='10886105' AND code=200
	) T ON DUPLICATE KEY UPDATE click_num = T.click_num, reg_dttm = NOW()
";
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

$sql="
	INSERT INTO ckd_day_ad_stats (stats_dttm,service_tp_code,partner_no,click_num)
	SELECT stats_dttm,service_tp_code,partner_no,click_num
	FROM (
	SELECT :date_ago AS stats_dttm, '32' AS service_tp_code, '10886106' AS partner_no, count(*) click_num 
	FROM api_point WHERE reg_date_num=:date_ago AND zone='10886106' AND code=200
	) T ON DUPLICATE KEY UPDATE click_num = T.click_num, reg_dttm = NOW()
";
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

$sql="
	INSERT INTO ckd_day_ad_stats (stats_dttm,service_tp_code,partner_no,click_num)
	SELECT stats_dttm,service_tp_code,partner_no,click_num
	FROM (
	SELECT :date_ago AS stats_dttm, '33' AS service_tp_code, '10886107' AS partner_no, count(*) click_num 
	FROM api_point WHERE reg_date_num=:date_ago AND zone='10886107' AND code=200
	) T ON DUPLICATE KEY UPDATE click_num = T.click_num, reg_dttm = NOW()
";
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

$sql="
	INSERT INTO ckd_day_ad_stats (stats_dttm,service_tp_code,partner_no,click_num)
	SELECT stats_dttm,service_tp_code,partner_no,click_num
	FROM (
	SELECT :date_ago AS stats_dttm, '34' AS service_tp_code, '10886102' AS partner_no, count(*) click_num 
	FROM api_point WHERE reg_date_num=:date_ago AND zone='10886102' AND code=200
	) T ON DUPLICATE KEY UPDATE click_num = T.click_num, reg_dttm = NOW()
";
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

$sql="
	INSERT INTO ckd_day_ad_stats (stats_dttm,service_tp_code,partner_no,click_num)
	SELECT stats_dttm,service_tp_code,partner_no,click_num
	FROM (
	SELECT :date_ago AS stats_dttm, '35' AS service_tp_code, '10886103' AS partner_no, count(*) click_num 
	FROM api_point WHERE reg_date_num=:date_ago AND zone='10886103' AND code=200
	) T ON DUPLICATE KEY UPDATE click_num = T.click_num, reg_dttm = NOW()
";
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

$sql="
	INSERT INTO ckd_day_ad_stats (stats_dttm,service_tp_code,partner_no,click_num)
	SELECT stats_dttm,service_tp_code,partner_no,click_num
	FROM (
	SELECT :date_ago AS stats_dttm, '36' AS service_tp_code, '10886104' AS partner_no, count(*) click_num 
	FROM api_point WHERE reg_date_num=:date_ago AND zone='10886104' AND code=200
	) T ON DUPLICATE KEY UPDATE click_num = T.click_num, reg_dttm = NOW()
";
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);


//쿠팡광고 2개
$sql="
	INSERT INTO ckd_day_coupang_stats (stats_dttm,service_tp_code,partner_no,click_num)
	SELECT stats_dttm,service_tp_code,partner_no,click_num
	FROM (
	SELECT :date_ago AS stats_dttm, '03' AS service_tp_code, zone AS partner_no, count(*) click_num 
	FROM api_point WHERE reg_date_num=:date_ago AND zone IN('payboocADAPIaos','repayboocADAPIaos') AND code=200
	) T ON DUPLICATE KEY UPDATE click_num = T.click_num, reg_dttm = NOW()
";
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

$sql="
	INSERT INTO ckd_day_coupang_stats (stats_dttm,service_tp_code,partner_no,click_num)
	SELECT stats_dttm,service_tp_code,partner_no,click_num
	FROM (
	SELECT :date_ago AS stats_dttm, '04' AS service_tp_code, zone AS partner_no, count(*) click_num 
	FROM api_point WHERE reg_date_num=:date_ago AND zone IN('payboocADAPIios','repayboocADAPIios') AND code=200
	) T ON DUPLICATE KEY UPDATE click_num = T.click_num, reg_dttm = NOW()
";
$NDO->sql_query($sql,[":date_ago"=>$date_ago]);

echo "OK";
