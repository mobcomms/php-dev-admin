<?php
/**********************
 *
 *    팝업/푸시관리 - 팝업관리
 *
 **********************/

include "./var.php";

## 환경설정
define('_title_', '팝업관리');
define('_Menu_', 'pop');
define('_subMenu_', 'popup');

include_once __func__;
include_once __head_pop__; ## html 헤더 출력

//삭제
if(!empty($_POST['mode']) && $_POST['mode'] = 'DEL') {
	$sql = "
		DELETE FROM ckd_com_code
		WHERE code_tp_id='theme_cate_code' AND code_id=:code_id
	";
	$result = $NDO->sql_query($sql,array(":code_id"=>$_POST['idx']));
}

$sql = "
	SELECT count(*)AS cnt
	FROM ckd_com_code  
	WHERE code_tp_id='theme_cate_code'
";
$total = $NDO->getData($sql);

include_once __page__;
$now = ret(INPUT_GET, 'np');
$paging->np = $now;
$paging->ps = 50;
$PG = $paging->init($total['cnt']);

$sql = "
	SELECT * FROM ckd_com_code
	WHERE code_tp_id='theme_cate_code'
	ORDER BY code_id DESC
	LIMIT {$PG->first}, {$PG->size}
";
$result = $NDO->fetch_array($sql);

?>

<div class="contentpanel">

	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title">카테고리 관리 </h4>
		</div><!-- panel-heading -->
		<div class="panel-body">
			<div class="pull-right" style="margin:10px 0 10px 0;">
				<div class="pull-left">
					<button class="btn btn-primary" onclick="location.href='theme_cate_write.php' ">카테고리 추가</button>
				</div>
			</div>

			<div class="row">
				<div class="panel panel-default">
					<table class="table table-hover mb30 member_table">
						<colgroup>
							<col width="10%">
							<col width="20%">
							<col width="20%">
							<col width="20%">
						</colgroup>
						<thead>
						<tr>
							<th class="col-md-1">code_id</th>
							<th class="col-md-2">카테고리명</th>
							<th class="col-md-2">상태</th>
							<th class="col-md-2">관리</th>
						</tr>
						</thead>

						<tbody>
						<?php foreach($result as $row){?>
						<tr>
							<td><?=$row['code_id']?></td>
							<td><?=$row['code_desc']?></td>
							<td><?=$row['use_yn']=='Y'?"사용함":"사용안함"?></td>
							<td>
								<button type="button" class="btn btn-xs btn-primary" onclick="location.href='theme_cate_write.php?idx=<?=$row['code_id']?>'">수정</button>
								<button type="button" class="btn btn-xs btn-danger" onclick="set_delete_theme('<?=$row['code_id']?>')">삭제</button>
							</td>
						</tr>
						<?php }?>
						</tbody>
					</table>
				</div>
			</div>

		</div><!-- panel-body -->
	</div><!-- panel panel-default -->
</div><!-- contentpanel -->

<?php
include __foot__;
?>

<script>
	function set_delete_theme(code_id) {
		if(!confirm("정말로 삭제하시겠습니까?")){
			return false;
		}
		var form = $('form[name="frmList"]');
		$('input[name="idx"]', form).val(code_id);
		form.submit();
	}
</script>
<form name="frmList" method="POST">
	<input type="hidden" name="idx" value="" />
	<input type="hidden" name="refpage" value="<?=__self__?>" />
	<input type="hidden" name="mode" value="DEL" />
</form>