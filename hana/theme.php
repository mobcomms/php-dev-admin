<?php
/**********************************************************
 *
 *	테마관리
 *
 * **********************************************************
 *
 *	Request Parameter
 *
 * 	 - theme_state
 * 	   : 01:공지사항, 02:문의하기, 03:자주하는질문, 04:버그신고, 05:제휴문의
 *
 ************************************************************/

include "./var.php";
include_once __func__;

## 환경설정
define('_title_', '테마관리');
define('_Menu_', 'theme');
define('_subMenu_', 'theme');
//$debugMode='Y';

include __head__; ## html 헤더 출력

$subQry="theme_state<>'99'";

//카테고리 선택 리스트
$sql = "
	SELECT code_id,code_val,count(*) cnt 
	FROM ckd_com_code 
	JOIN ckd_theme_cate ON code_id=theme_cate_code
	WHERE code_tp_id = 'theme_cate_code' 
	AND use_yn='Y'
	GROUP BY code_id
	ORDER BY code_id ASC
";
//pre($sql);
$cate = $NDO->fetch_array($sql);

// 검색
$theme_state=empty($_REQUEST['theme_state'])?"":$_REQUEST['theme_state'];
$keyword=empty($_REQUEST['keyword'])?"":$_REQUEST['keyword'];
$subQry.=" AND CTM.theme_partner_code IN ('01','02')";

$param = "";
$subQry2 = "";
$ckd_param = [];
$cate_code=empty($_REQUEST['cate_code'])?"":$_REQUEST['cate_code'];
if(!empty($cate_code)) {
	foreach ($cate_code AS $row) {
		$subQry2 .= " AND INSTR(code_id,'{$row}')";
		$param .= "&cate_code[]=".$row;
	}
	$subQry .= $subQry2;
}

if($theme_state){
	$subQry.=" AND CTM.theme_state=:theme_state";
	$ckd_param[":theme_state"] = $theme_state;
	$param .= "&theme_state=".$theme_state;
}
if($keyword){
	$subQry.=" AND theme_title like :theme_title";
	$ckd_param[":theme_title"] = "%".$keyword."%";
	$param .= "&keyword=".$keyword;
}

$sql = "
	SELECT *,group_concat(CCC.code_id) code_id
	FROM ckd_theme_cate CTC
	RIGHT JOIN ckd_theme_mng CTM ON CTC.theme_idx = CTM.theme_seq
	LEFT JOIN ckd_com_code CCC ON CCC.code_tp_id = 'theme_cate_code' and CTC.theme_cate_code = CCC.code_id
	GROUP BY CTM.theme_seq
	HAVING {$subQry}
";
//pre($sql);
$total = $NDO->fetch_array($sql,$ckd_param);
$total['cnt'] = count($total);

include_once __page__;
$now = ret(INPUT_GET, 'np');
$paging->np = $now;
$paging->ps = 20;
$PG = $paging->init($total['cnt']);

// 정렬 순서
$sortKey=empty($_REQUEST['sortKey'])?"":$_REQUEST['sortKey'];
if(!$sortKey) $sortKey="theme_sort";
$sortType="DESC";

$sql="
	SELECT 
	CTM.theme_seq
	,CTM.theme_tp_code
	,CTM.theme_partner_code
	,CTM.theme_title
	,CTM.theme_cnts
	,CTM.theme_down
	,CTM.theme_state
	,CTM.theme_sort
	,CTM.reg_user_no

	,IF(DATEDIFF(SYSDATE(), CTM.reg_dttm) < 14, 'Y', 'N')  theme_new_yn
	,group_concat(CCC.code_id) code_id
	,group_concat(CCC.code_val) theme_cate
	,CTM.reg_dttm

	FROM ckd_theme_cate CTC
	RIGHT JOIN ckd_theme_mng CTM ON CTC.theme_idx = CTM.theme_seq
	LEFT JOIN ckd_com_code CCC ON CCC.code_tp_id = 'theme_cate_code' and CTC.theme_cate_code = CCC.code_id
	GROUP BY CTM.theme_seq
	HAVING {$subQry}
	ORDER BY {$sortKey} {$sortType}
	LIMIT {$PG->first},{$PG->size}
";
//pre($sql);
$ret = $NDO->fetch_array($sql,$ckd_param);

?>

	<script>
		/* 테마 상태 변경 [S] */
		function theme_state_change(idx){

			mType=$('#theme'+idx).attr("data-otpc");
			var formData = {mode:"themeMng",mType:mType,idx:idx};
			$.ajax({
				url:'./ajax/ajax_process.php',
				type: 'POST',
				data: formData,
				success:function(data){
					if(data=='R'){
						$('#theme'+idx).html('');
						$('#theme'+idx).prepend('<button class="btn btn-xs btn-default">일반</button>');
						$('#theme'+idx).prepend('<button class="btn btn-xs btn-primary active">테스트</button>');
						alert('이미지 or 파일을 등록해 주세요');
					}else{
						$('#theme'+idx).attr("data-otpc",data);
					}
				}
			});
		}

		function set_delete_theme(theme_seq) {
			if(!confirm("정말로 삭제하시겠습니까?")){
				return false;
			}
			var form = $('form[name="frmList"]');
			$('input[name="idx"]', form).val(theme_seq);
			form.submit();
		}

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

	<form name="frmList" method="POST" action="./process.php">
		<input type="hidden" name="idx" value="" />
		<input type="hidden" name="refpage" value="<?=__self__?>" />
		<input type="hidden" name="mode" value="DEL" />
	</form>

	<div class="contentpanel">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="panel-btns">
					<!--a href="" class="panel-close">&times;</a-->
					<a href="" class="minimize">−</a>
				</div><!-- panel-btns -->
				<h4 class="panel-title"><?=_title_?></h4>
			</div>
			<div class="panel-body">
				<div class="row">

					<div class="col-xs-12 col-md-12">

						<form name="scform" method="get" action="">
							<div class="pull-left" style="margin-bottom: 10px;width: 100%;">
								<div class="col-sm-12">
									<?php foreach($cate as $row){
										$checked = "";
										if(!empty($cate_code)) {
											foreach ($cate_code as $row2) {
												if ($row['code_id'] == $row2) {
													$checked = "checked";
												}
											}
										}
										?>
										<label style="padding-right: 7px">
											<div class="pull-left"><input style="width:20px;height:20px;" type="checkbox" name="cate_code[]" value="<?=$row['code_id']?>" <?=$checked?>></div>
											<div class="pull-left" style="padding-top:5px;padding-left: 3px;"><?=$row['code_val']?>(<?=$row['cnt']?>)</div>
										</label>
									<?php }?>
								</div>

								<div class="pull-left" style="clear: both;margin-bottom: 10px;">
								</div>

								<div class="pull-left" style="clear: both;">
									<select class="form-control pull-left border-input" name="theme_state" style="width:100px;margin-right:10px;">
										<option value="">상태 전체</option>
										<option value="01" <?=$theme_state=="01"?"selected":""?> >테스트</option>
										<option value="02" <?=$theme_state=="02"?"selected":""?> >일반</option>
									</select>

<!--									<select class="form-control pull-left border-input" name="key" style="width:100px;margin-right:10px;">-->
<!--										<option value="theme_title"> 테마명 </option>-->
<!--									</select>-->

									<input type="text" class="form-control pull-left border-input" name="keyword" style="width:200px; height:38px;" placeholder="테마명 검색" value="<?=$keyword?>" autocomplete="off" />
									<button class="btn btn-success" style="height:38px;">검 색</button>
								</div>
								<div class="pull-right" style="margin:10px 0 10px 0;">
									<div class="pull-left">
										<!-- <button type="button" class="btn btn-success" onclick="centerOpenWindow('info.php', 'theme_cate', '800', '510', '', 'N');return false;">php 정보</button>-->
										<button type="button" class="btn btn-warning" onclick="centerOpenWindow('theme_cate.php', 'theme_cate', '800', '800', '', 'N');return false;">카테고리 관리</button>
										<button type="button" class="btn btn-primary" onclick="location.href='theme_write.php' ">등록</button>
									</div>
								</div>

							</div>
						</form>

						<div class="table-responsive">
							<table class="table table-hover mb30 member_table">
								<thead>
								<tr>
									<th class="col-md-1">번호</th>
									<th class="col-md-1">테마적용<br />/프리미엄</th>
									<th class="col-md-1">썸네일</th>
									<th class="col-md-1" style="min-width: 150px;">common 파일</th>
									<th class="col-md-1" style="min-width: 150px;">custom 파일</th>
									<th class="col-md-2">카테고리</th>
									<th class="col-md-2">테마명</th>
									<th class="col-md-1">상태</th>
									<th width="300px" class="col-md-1">등록일</th>
									<th class="col-md-1" colspan="2">관리</th>
								</tr>
								</thead>
								<tbody>
								<?php if(empty($ret) || !is_array($ret)){?>
									<tr><td colspan="11">테마 리스트가 없습니다.</td></tr>
								<?php
								}else{
								for($i=0;$i<count($ret);$i++){
									$res = $ret[$i];
									$ver=($res['theme_state']=='01')?'<font color="blue">TEST</font>':'';
									$premium=($res['theme_tp_code']=='01')?"프리미엄":"일반";
									$theme_seq=$res['theme_seq'];

									switch ($res['theme_partner_code']){
										case '01':$thumb_path = "thumb";break;
										case '02':$thumb_path = "thumb_offerwall";break;
									}

									//썸네일 URL
									$thumb = "/img/theme/{$thumb_path}/".ceil($theme_seq/100)."/";

									//실제 zip 파일 경로
									$path = '/img/theme/common/'.ceil($theme_seq/100).'/';
									$hpath = '/home/hanamoney/public_html'.$path;
									if(file_exists($hpath.'common_theme_'.$theme_seq.'.zip')){
										$link_common="<a href='".$path."common_theme_".$theme_seq.".zip'>common_theme_{$theme_seq}.zip</a>";
									}else{
										$link_common="<font color='red'>NONE</font>";
									}

									//실제 zip 파일 경로
									$path = '/img/theme/custom/'.ceil($theme_seq/100).'/';
									$hpath = '/home/hanamoney/public_html'.$path;
									if(file_exists($hpath.'custom_theme_'.$theme_seq.'.zip')){
										$link_custom="<a href='".$path."custom_theme_".$theme_seq.".zip'>custom_theme_{$theme_seq}.zip</a>";
									}else{
										$link_custom="<font color='red'>NONE</font>";
									}
									?>
									<tr>
										<!--<td><input type="checkbox" name="chk"></td>-->
										<td><?=number_format($PG->first_num)?></td>
										<td><?=$premium?></td>
										<td><img src="<?=$thumb?>theme_<?=$theme_seq?>.png?<?=time()?>" style="width:100px;"></td>
										<td><?=$link_common?></td>
										<td><?=$link_custom?></td>
										<td><?=$res['theme_cate']?></td>
										<td><?=$res['theme_title']?></td>
										<td>
											<div class="btn-group btn-toggle" onClick="theme_state_change('<?=$res['theme_seq']?>')" id="theme<?=$res['theme_seq']?>" data-otpc="<?=$res['theme_state']?>">
												<button class="btn btn-xs <?=($res['theme_state']=='01')?'btn-primary active':'btn-default'?>">테스트</button>
												<button class="btn btn-xs <?=($res['theme_state']=='02')?'btn-primary active':'btn-default'?>">일반</button>
											</div>
										</td>
										<td><?=substr($res['reg_dttm'],0,10)?></td>

										<?php if(empty($keyword) && empty($_REQUEST['sortKey'])){?>
											<td>
												<button class="glyphicon glyphicon-arrow-up" onClick="theme_sort(this,'<?=$res['theme_seq']?>','UP','<?=$PG->now?>','<?=($i==0&&$PG->now>1)?'M':''?>');"></button><br>
												<button class="glyphicon glyphicon-arrow-down" onClick="theme_sort(this,'<?=$res['theme_seq']?>','DN','<?=$PG->now?>','<?=($i==$PG->size-1&&$PG->now<$PG->block)?'M':''?>');"></button>
											</td>
										<?php }?>
										<td>
											<button style="margin-bottom: 3px;" class="btn btn-xs btn-primary" onclick="location.href='theme_write.php?idx=<?=$res['theme_seq']?>&np=<?=$now?>'">수정</button>
											<button class="btn btn-xs btn-danger" onclick="set_delete_theme(<?=$res['theme_seq']?>)">삭제</button>
										</td>
									</tr>
									<?php
									$PG->first_num--;
								}//for
								}
								?>

								</tbody>
							</table>
						</div><!-- table-responsive -->
					</div><!-- col-md-12 -->

				</div><!-- row -->

				<div class="row">
					<?=$paging->paging_new($PG,"{$param}&sortKey=".$sortKey);?>
				</div><!-- row -->

			</div>
		</div>
	</div><!-- contentpanel -->

<?php
include __foot__;
?>