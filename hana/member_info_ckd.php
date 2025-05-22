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
define('_title_', '회원관리');
define('_Menu_', 'manage');
define('_subMenu_', 'info');

include_once __head__; ## html 헤더 출력

//적립정보
$sql = "
	SELECT count(*) cnt FROM ckd_user_point_today
	WHERE user_uuid='{$_GET['uuid']}' 
";
//$fn->debug($sql);
$total = $NDO->getData($sql);

include_once __page__;
$now = ret(INPUT_GET, 'np');
$paging->np = $now;
$paging->ps = 30;
$PG = $paging->init($total['cnt']);
$PG_Param = "&uuid=".$_GET['uuid'];

$sql = "
	SELECT *
	FROM ckd_user_point_today 
	WHERE user_uuid='{$_GET['uuid']}' 
	ORDER BY reg_dttm DESC
	LIMIT {$PG->first}, {$PG->size}
";
//pre($sql);
$point_list = $NDO->fetch_array($sql);

$sql = "
	SELECT sum(point) AS point
	FROM ckd_user_point 
	WHERE user_uuid='{$_GET['uuid']}' 
";
//$fn->debug($sql);
$temp = $NDO->getData($sql);
$total_point = $temp['point'];
?>

<div class="contentpanel">

	<div class="panel panel-default" style="width:900px">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title">키보드 사용자 (키보드 사용 적립정보) </h4>
		</div><!-- panel-heading -->

		<div class="panel-body">
			<div class="row col-xs-12 col-md-12">
				<div class="table-responsive">
					<div style="float: left;line-height: 40px;">키보드 사용 적립정보</div>

					<table class="table table-hover mb30 member_table" id="" style="border: 1px solid #b0b0b0;margin-top:0;">
						<thead>
						<tr>
							<th>적립 날짜</th>
							<th>적립 요청 일시</th>
							<th>사용 적립 포인트</th>
						</tr>
						</thead>
						<tbody>
						<?php if(is_array($point_list)){ ?>
							<tr>
								<td>합계</td>
								<td>-</td>
								<td><?=number_format(empty($total_point)?0:$total_point)?></td>
							</tr>

							<?php foreach($point_list AS $row){ ?>
								<tr>
									<td><?=$row['stats_dttm']?></td>
									<td><?=$row['reg_dttm']?></td>
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
