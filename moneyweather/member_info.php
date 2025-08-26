<?php
/**********************
 *
 *    회원관리 페이지 (회원정보 리스트)
 *
 **********************/

include "./var.php";
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성

## 환경설정
define('_title_', '사용자관리');
define('_Menu_', 'user');
define('_subMenu_', 'user');

include_once __head__; ## html 헤더 출력

$startDate = !empty($_REQUEST['startDate']) ? $_REQUEST['startDate'] : "";
$sdate = str_replace("-","",$startDate);
if(!empty($sdate)){
    $add_date = "AND a.stats_dttm = '{$sdate}'";
}else{
    $add_date = "";
}

//유저정보
$sql = "
	SELECT * FROM ckd_user_info 
	WHERE user_uuid='{$_GET['uuid']}' 
";
//$fn->debug($sql);
$user_info = $NDO->getData($sql);

//적립정보
$sql = "
	select count(*) as cnt
	from ckd_game_zone_point as a
	where user_uuid='{$_GET['uuid']}' {$add_date}
";
//$fn->debug($sql);
$total = $NDO->getData($sql);

include_once __page__;
$now = ret(INPUT_GET, 'np');
$paging->np = $now;
$paging->ps = 30;
$PG = $paging->init($total['cnt']);
$PG_Param = "&uuid=".urlencode($_GET['uuid'])."&startDate=".$startDate;

$sql = "
    SELECT * FROM moneyweather.ckd_game_zone_point a
    LEFT JOIN moneyweather.ckd_game_zone_ticket b ON a.reg_date=b.use_date AND a.user_uuid=b.uuid
    WHERE user_uuid='{$_GET['uuid']}' {$add_date}
    ORDER BY spot_idx DESC
	LIMIT {$PG->first}, {$PG->size}
";
//$fn->debug($sql);
$point_list = $NDO->fetch_array($sql);

$sql = "
	SELECT sum(point) AS point
	FROM ckd_game_zone_point a 
	WHERE user_uuid='{$_GET['uuid']}' {$add_date}
";
//$fn->debug($sql);
$temp = $NDO->getData($sql);
$total_point = $temp['point'];
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
<div class="contentpanel">

	<div class="panel panel-default" style="width:900px">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title"><?=_title_?></h4>
		</div><!-- panel-heading -->

		<div class="panel-body">
			<div class="row col-xs-12 col-md-12">
				<div class="table-responsive">
					사용자정보
					<table class="table table-hover mb30 member_table" id="" style="border: 1px solid #b0b0b0;margin-top:0;">
						<tbody>
						<tr>
							<td>keyboard_id</td>
							<td><?=$user_info['user_uuid']?></td>
							<td>생성일자</td>
							<td><?=$user_info['reg_dttm']?></td>
						</tr>
						<tr>
							<td>키보드 종류</td>
							<td><?=$user_info['user_app_type']?></td>
							<td>최종접속일자</td>
							<td><?=$user_info['alt_dttm']?></td>
						</tr>
						</tbody>
					</table>
				</div><!-- table-responsive -->
			</div><!-- row -->
		</div>

		<div class="panel-body">
            <form name="scform" method="get" action="">
                <input type="hidden" name="uuid" value="<?=$_GET['uuid']?>">
                <div class="row">
                    <div class="card">
                        <div class="header">
                            <div class="input-group call">
                                <input type="text" class="form-control pull-left border-input databox" placeholder="yyyy-mm-dd" id="sdate" value="<?=$startDate?>" name="startDate" autocomplete="off">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                            <button class="btn btn-success" style="height: 34px;">검 색</button>
                            <button class="btn btn-warning" style="height: 34px;" onclick="document.getElementById('sdate').value='';">초기화</button>
                        </div><!-- header -->
                    </div><!-- card -->
                </div><!-- row -->
            </form>

            <div class="row col-xs-12 col-md-12">
				<div class="table-responsive">
					<div style="float: left;line-height: 40px;">적립정보</div>
					<table class="table table-hover mb30 member_table" id="" style="border: 1px solid #b0b0b0;margin-top:0;">
						<thead>
						<tr>
							<th>적립 번호</th>
							<th>게임 종류</th>
                            <th>버튼 종류</th>
							<th>적립 일시</th>
							<th>적립 포인트</th>
						</tr>
						</thead>
						<tbody>
						<?php if(is_array($point_list)){ ?>
							<tr>
								<td>합계</td>
								<td>-</td>
								<td>-</td>
                                <td>-</td>
								<td><?=number_format(empty($total_point)?0:$total_point)?></td>
							</tr>

							<?php
							foreach($point_list AS $row){
								switch($row['code_id']){
									case "ladder": $game_type = "사다리";break;
									case "roulette":$game_type = "룰렛";break;
									case "lotto":$game_type = "복권";break;
								}
                                switch($row['ad_type']){
                                    case "ad1_1": $ad_type = "버튼1(연속)";break;
                                    case "ad1_2": $ad_type = "버튼1(정시)";break;
                                    case "ad2_1": $ad_type = "버튼2(연속)";break;
                                    case "ad2_2": $ad_type = "버튼2(정시)";break;
                                    default : $ad_type = "버튼 고도화 이전";
                                }
							?>
								<tr>
									<td><?=$row['spot_idx']?></td>
									<td><?=$game_type?></td>
                                    <td><?=$ad_type?></td>
									<td><?=$row['reg_date']?></td>
									<td><?=number_format($row['point'])?></td>
								</tr>
							<?php
								$PG->first_num--;
							}
						}else{
						?>
							<tr>
								<td colspan="6">적립 내용이 없습니다.</td>
							</tr>
						<?php
							}
						?>
						</tbody>
					</table>
				</div><!-- table-responsive -->
			</div><!-- row -->

			<div class="row" style="width:800px">
				<?=$paging->paging_new($PG,$PG_Param);?>
			</div><!-- row -->

		</div><!-- panel-body -->
	</div><!-- panel -->

</div><!-- contentpanel -->
<?php
include __foot__;
?>
