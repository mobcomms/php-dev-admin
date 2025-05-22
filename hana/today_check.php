<?php
/**********************
 *
 *    통계 페이지 / 일자별 통계
 *
 **********************/
set_time_limit(0);

include "./var.php";
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성


$sql="
	SELECT user_trmnl_unq_key, b.reg_dttm
	FROM ckd_device_check a
	JOIN ckd_user_info b ON a.user_trmnl_unq_key=b.user_uuid AND  DATE_FORMAT(b.reg_dttm,'%Y%m') >= '202406'
	LEFT JOIN ckd_event_point c on c.user_uuid=a.user_trmnl_unq_key 
	WHERE a.stats_dttm >= 20240601 and c.user_uuid is null
	group by user_trmnl_unq_key
	ORDER BY user_trmnl_unq_key DESC
";
$result = $NDO->fetch_array($sql);


echo count($result);

$uuid_array = [];
foreach($result as $row){
	$sql="
		SELECT * FROM hana.ckd_event_point
		where user_uuid='{$row['user_trmnl_unq_key']}'
	";
	$result2 = $NDO->getdata($sql);
	if(empty($result2)){
		$uuid_array[] = $row['user_trmnl_unq_key'];
	}
}

echo count($result);
pre($uuid_array);