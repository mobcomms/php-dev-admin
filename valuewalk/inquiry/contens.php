<?php
if(!empty($_POST)){

	include_once '../var.php';
	include_once __pdoDB__;    ## DB Instance 생성
	include_once __fn__;

	$reg_user = empty($_REQUEST['reg_user'])?"":$_REQUEST['reg_user'];
	$sql = "
		SELECT question, answer, bbs_state, regdate, editdate FROM ckd_bbs_inquiry
		WHERE del_yn = 'N' AND reg_user=:reg_user
		ORDER BY seq DESC
	";
	$params = [];
	$params[':reg_user'] = $reg_user;
	$result = $NDO->fetch_array($sql, $params);
}

foreach($result as $key => $row){
	$css_text = "";
	if($key === array_key_first($result)){
		$css_text = "active";
	}
	if(empty($row['bbs_state']) || $row['bbs_state']=='01'){
		$mode_html = '<span class="receipt">접수</span>';
		$css_text = "off";
		$arrow_html = '';
	}else{
		$mode_html = '<span class="end">답변 완료</span>';
		$arrow_html = '<a href="javascript:void(0)">arrow</a>';
	}
	$regdate = empty($row['regdate'])?"":str_replace("-",".",$row['regdate']);

?>
<div class="tab_inner <?=$css_text?>">
	<div>
		<?=$mode_html?>
		<p><?=$regdate?></p>
	</div>
	<div><?=nl2br($row['question'])?></div>
	<?=$arrow_html?>
	<?php if($row['bbs_state']=='02'){
		$editdate = empty($row['editdate'])?"":str_replace("-",".",$row['editdate']);
	?>
		<div class="active_cont">
			<div>
				<span class="service">고객센터</span>
				<p><?=$editdate?></p>
			</div>
			<div><?=nl2br($row['answer'])?></div>
		</div>
	<?php } ?>
</div>
<?php } ?>
