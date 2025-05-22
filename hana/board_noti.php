<?php
/**********************************************************
 *
 *	게시판관리
 *
 * **********************************************************
 *
 *	Request Parameter
 *
 * 	 - bType
 * 	   : 01:공지사항, 02:문의하기, 03:자주하는질문, 04:버그신고, 05:제휴문의
 *
 ************************************************************/

include "./var.php";

## 환경설정
define('_title_', "노티 광고");
define('_Menu_', 'adv');
define('_subMenu_', "noti");

include_once __func__;
include_once __pdoDB__; ## DB Instance 생성
include_once __head__;  ## html 헤더 출력

$where = " type='noti' AND del_yn='N' ";

// 검색어
$key=empty($_REQUEST['key'])?"":$_REQUEST['key'];
$keyword=empty($_REQUEST['keyword'])?"":$_REQUEST['keyword'];
if($keyword){
	$where.=" AND ".$key." like '%{$keyword}%' ";
}

$sql = " SELECT count(*) cnt FROM ckd_bbs CBW WHERE {$where} ";
$total = $NDO->getData($sql);

include_once __page__;
$now = ret(INPUT_GET, 'np');
$paging->np = $now;
$paging->ps = 30;
$PG = $paging->init($total['cnt']);

$sql = "
	SELECT seq,title,url,regdate,display_yn
	FROM ckd_bbs CBW
	WHERE {$where}
	ORDER BY SEQ DESC
	LIMIT {$PG->first}, {$PG->size}
";

$ret = $NDO->fetch_array($sql);


$sql="
	SELECT * FROM ckd_cfg_info
";
//$fn->debug($sql);
$config = $NDO->fetch_array($sql);
foreach($config as $res){
	$cfg[$res['cfg_nm']]=$res['cfg_val'];
}
$noti_ad = unserialize($cfg['noti_ad']);
?>
<script>
	function _board_del() {
		var idx_arr = __checkbox_value_list('chk_box');
		if( idx_arr.length == 0 ){
			alert("삭제할 게시물을 선택해주세요");
			return;
		}

		var f=document.basicForm;
		if (confirm('선택된 게시글을 삭제 하시겠습니까?')) {
			f.idx.value=idx_arr;
			f.mode.value="DEL";
			f.submit();
		}
	}
</script>
<form name="basicForm" action="./process.php" class="form-horizontal" method="POST">
	<input type="hidden" name="refpage" value="<?= __self__ ?>"/>
	<input type="hidden" name="idx" value=""/>
	<input type="hidden" name="page" value="<?= $page ?>"/>
	<input type="hidden" name="mode" value=""/>
</form>

<div class="contentpanel">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns">
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title"><?=_title_?></h4>
		</div><!-- panel-heading -->

		<div class="panel-body">
			<div class="row">

				<div class="col-xs-12 col-md-12">

					<div class="row">

						<div class="pull-right">
							<button class="btn btn-primary" onclick="location.href='board_noti_write.php' " style="width: 100px;height: 40px;">등록</button>
							<!--<button type="button" class="btn btn-danger" onClick="javascript:_board_del()">삭제</button>-->
						</div>
					</div>

					<div class="row">
						<div class="table-responsive">
							<p><span style="color:red">※</span> 여러개가 등록 되어도 최근 10개만 노출됩니다.</p>
							<table class="table table-hover mb30 member_table" style="border: 1px solid #b0b0b0;">
								<thead>
								<tr>
									<!--<th class="col-md-1" style="width:50px;"><input type="checkbox" class="checkall"> </th>-->
									<th class="col-md-1">광고번호</th>
									<th>타이틀</th>
									<th class="col-md-2">노출시간</th>
									<th class="col-md-1">유지시간(공통)</th>
									<th class="col-md-1">지급포인트(공통)</th>
									<th class="col-md-2">노출여부</th>
								</tr>
								</thead>
								<tbody>

								<?php foreach($ret as $res){ ?>
									<tr>
										<!--										<td><input type="checkbox" class="chk_box" name="chk" value="-->
										<td><?=number_format($PG->first_num)?></td>
										<td style="text-align: left;padding-left: 20px;">
											<a href="board_noti_write.php?idx=<?= $res['seq'] ?>&page=<?= $now ?>&key=<?= $key ?>&keyword=<?= $keyword ?>"><?=$res['title'] ?> </a>
										</td>
										<td><?= $res['url'] ?></td>

										<td><?= $noti_ad['noti_holding_time'] ?></td>
										<td><?= $noti_ad['noti_point'] ?></td>

										<td style="padding:10px 0px 0px 0px;">
											<div class="btn-group btn-toggle" onClick="switch_mng('<?= $res['seq'] ?>')" id="mng<?= $res['seq'] ?>" data-otpc="<?= $res['display_yn'] ?>">
												<button class="btn btn-xs <?= ($res['display_yn'] == 'Y') ? 'btn-primary active' : 'btn-default' ?>" type="button">ON </button>
												<button class="btn btn-xs <?= ($res['display_yn'] == 'N') ? 'btn-primary active' : 'btn-default' ?>" type="button">OFF </button>
											</div>
										</td>
									</tr>
									<?php
									$PG->first_num--;
								}
								if(empty($ret)){
									?>
									<tr><td colspan="5">데이터가 없습니다.</td></tr>
								<?php }?>
								</tbody>
							</table>
						</div><!-- table-responsive -->
					</div><!-- col-md-12 -->

				</div><!-- row -->

				<div class="row">
					<?=!empty($ret) ? $paging->paging_new($PG,"&key={$key}&keyword={$keyword}") :""?>
				</div><!-- row -->

			</div>
		</div>
	</div><!-- contentpanel -->


<?php
include __foot__;
?>
