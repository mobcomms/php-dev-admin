<?php
/**********************************************************
 *
 * 게시판관리
 *
 * **********************************************************
 *
 * Request Parameter
 *
 *    - bType
 *      : 01:공지사항, 02:문의하기, 03:자주하는질문, 04:버그신고, 05:제휴문의
 *
 ************************************************************/

include "./var.php";
include_once __func__;

### 게시글 가져오기 [S]
$idx=empty($_REQUEST['idx'])?"":$_REQUEST['idx'];

if($idx){
	$sql = "
		SELECT *
		FROM ckd_bbs CBW
		WHERE seq = '{$idx}'
	";
	$bbs = $NDO->getData($sql);
	$noti_time = explode(":",$bbs['url']);
}

## 환경설정
define('_title_', '노티');
define('_Menu_', 'adv');
define('_subMenu_', 'noti');
$actTitle=($idx)?"수정":"등록";

include __head__; ## html 헤더 출력
?>
<div class="contentpanel">

	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns">
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title"><?=_title_?> <?=$actTitle?></h4>
		</div><!-- panel-heading -->
		<div class="panel-body">

			<form name="basicForm" id="basicForm" action="./process.php" class="form-horizontal" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="refpage" value="<?=__self__?>" />
				<input type="hidden" name="idx" value="<?=$idx?>" />
				<input type="hidden" name="mode" value="write" />
				<input type="hidden" name="type" value="noti" />
				<input type="hidden" name="page" value="<?=$page?>" />
				<div class="row">
					<div class="panel panel-default">

						<div class="form-group">
							<label class="col-sm-1 control-label" style="width: 200px;" ><span class="asterisk">*</span> 타이틀 </label>
							<div class="col-sm-9">
								<input type="text" name="title" class="form-control" style="width:500px" value="<?=empty($bbs)?"":$bbs['title']?>" placeholder="" required />
							</div>
						</div>

						<div class="form-group">
							<div class="col-sm-1 control-label" style="width: 200px;line-height:30px; margin-right:10px;">
								<span class="asterisk">*</span> 노티시간
							</div>
							<select class="form-control pull-left border-input" name="s_hour" style="width:100px;height: 40px;margin: 0">
								<?php for($i=0;$i<24;$i++){?>
									<option value="<?=$i?>" <?=(!empty($noti_time) && $noti_time[0]==$i)?'selected':''?>><?=$i?>시</option>
								<?php }?>
							</select>
							<select class="form-control pull-left border-input" name="s_min" style="width:100px;height: 40px;margin-left: 10px;">
								<?php for($i=0;$i<59;$i++){?>
								<option value="<?=$i?>" <?=(!empty($noti_time) && $noti_time[1]==$i)?'selected':''?>> <?=$i?>분 </option>
								<?php }?>
							</select>
						</div>

						<div class="form-group">
							<label class="col-sm-1 control-label" style="width: 200px;">노출여부</label>
							<div class="col-sm-10" style="line-height: 37px;">
								<input type="checkbox" class="chk_box" name="display_yn" value="Y" style="width:20px;height:20px;margin-left:10px;" <?=(!empty($bbs) && $bbs['display_yn']=='Y')?'checked':''?> >
							</div>
						</div>

					</div>
				</div>

				<div class="row">
					<div class="text-center">
						<div class="">
							<button type="button" class="btn btn-success" onClick="sendIt();"><?=$actTitle?></button>
							<?php if($idx){?>
								<button type="button" class="btn btn-warning" onClick="del_brand();">삭제</button>
							<?php }?>
							<button type="button" class="btn btn-default" onclick="location.href='board_noti.php' ">목록</button>
						</div>
					</div>
				</div>
			</form>

		</div><!-- panel-body -->
	</div><!-- panel panel-default -->
</div><!-- contentpanel -->

<form name="downloadForm" id="downloadForm" action="./process.php" class="form-horizontal" method="POST">
	<input type="hidden" name="refpage" value="<?=__self__?>" />
	<input type="hidden" name="idx" value="<?=$idx?>" />
	<input type="hidden" name="mode" value="theme_download" />
	<input type="hidden" name="file_idx" value="" />
</form>

<form name="delForm" id="delForm" action="./process.php" class="form-horizontal" method="POST">
	<input type="hidden" name="refpage" value="<?=__self__?>" />
	<input type="hidden" name="idx" value="<?=$idx?>" />
	<input type="hidden" name="mode" value="DEL" />
	<input type="hidden" name="file_idx" value="" />
</form>

<script>
	function sendIt(){
		var f=document.basicForm;

		if(!f.title.value){
			alert('타이틀을 입력해주세요.');
			f.title.focus();
			return;
		}
		f.submit();
	}

	function download_theme(board_idx){
		f=document.downloadForm;
		f.file_idx.value = board_idx;
		f.submit();
	}

	function del_brand(board_idx){
		f=document.delForm;
		f.file_idx.value = board_idx;
		f.submit();
	}

</script>

<?php
include __foot__;
?>