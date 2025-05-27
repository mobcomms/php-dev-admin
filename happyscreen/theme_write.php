<?php
/**********************************************************
 *
 * 테마관리
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
//include __pdoDB__;    ## DB Instance 생성
$idx=empty($_REQUEST['idx'])?"":$_REQUEST['idx'];
$page=empty($_REQUEST['np'])?"":$_REQUEST['np'];

## 환경설정
define('_title_', "테마");
define('_Menu_', 'theme');
define('_subMenu_', 'theme');

include __head__; ## html 헤더 출력


### 게시글 가져오기 [S]
$thumb_path = "thumb";
if($idx){
	$sql = "
		SELECT theme_tp_code, theme_partner_code, theme_title, theme_cnts, theme_state, CCC.code_val AS partner_code_val
		FROM ckd_theme_mng CTM
		LEFT JOIN ckd_com_code CCC ON CCC.code_tp_id = 'partner_code' AND  CCC.code_id = CTM.theme_partner_code
		WHERE theme_seq='{$idx}'
	";
	//pre($sql);
	$theme = $NDO->getData($sql);
	//pre($theme);
// 썸네일 이미지
	switch ($theme['theme_partner_code']){
		case '01':$thumb_path = "thumb";break;
		case '02':$thumb_path = "thumb_offerwall";break;
		default:$thumb_path = "thumb";break;
	}

	$path = ceil($idx/100).'/';
	$hpath = __root__."img/theme/thumb/{$path}";
	$offerwall_path = __root__."img/theme/thumb_offerwall/{$path}";
	$common_path = __root__."img/theme/common/{$path}";
	$custom_path = __root__."img/theme/custom/{$path}";
}
$thumb=!empty($idx) ? "/img/theme/{$thumb_path}/".ceil($idx/100)."/theme_".$idx.".png" : "/img/noImg.jpg";
$actTitle=!empty($idx)?"수정":"등록";
?>
<script>
	function sendIt(){
		f=document.basicForm;
		if(f.partner_code.value == "01"){
			if(!f.idx.value && (!f.theme_thumb.value || !f.theme_thumb.value)){
				alert('썸네일이 없습니다.');
				return false;
			}
		}else if(f.partner_code.value == "02"){
			if(!f.idx.value && (!f.theme_thumb_offerwall.value || !f.theme_thumb_offerwall.value)){
				alert('오퍼월 썸네일이 없습니다.');
				return false;
			}
		}
	}

	function download_theme(download_type){
		f=document.downloadForm;
		f.download_type.value = download_type;
		f.submit();
	}
</script>

<div class="contentpanel">

	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title"><?=_title_?> <?=$actTitle?></h4>
		</div><!-- panel-heading -->
		<div class="panel-body">

			<form name="downloadForm" id="downloadForm" action="./process.php" class="form-horizontal" method="POST">
				<input type="hidden" name="refpage" value="<?=__self__?>" />
				<input type="hidden" name="idx" value="<?=$idx?>" />
				<input type="hidden" name="mode" value="theme_download" />
				<input type="hidden" name="download_type" value="" />
			</form>

			<form name="basicForm" id="basicForm" action="./process.php" class="form-horizontal" enctype="multipart/form-data" method="POST" onsubmit="return sendIt();">
				<input type="hidden" name="refpage" value="<?=__self__?>" />
				<input type="hidden" name="idx" value="<?=$idx?>" />
				<input type="hidden" name="page" value="<?=$page?>" />
				<input type="hidden" name="mode" value="write" />

				<div class="row">
					<div class="panel panel-default">

						<div class="form-group">
							<label class="col-sm-1 control-label " >프리미엄<span class="asterisk"></span></label>
							<div class="col-sm-10">
								<label class='checkbox'> <input type='checkbox' class='bootstrap-wysihtml5-insert-link-target' name="theme_tp_code" value="01" <?=(!empty($theme) && $theme['theme_tp_code']=='01')?"checked":""?>> 적용 </label>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-1 control-label">파일첨부 <span class="asterisk"></span></label>
							<div class="col-sm-9">
								<span style="float:left;width:120px;height:210px;display: inline-block;"><img src="<?=$thumb?>?<?=time()?>" border="1" style="width:120px;"></span>

								<label class="theme_thumb col-sm-1 control-label" style="width:130px;">썸네일 <span class="asterisk"></span></label>
								<div class="theme_thumb fileupload fileupload-new" data-provides="fileupload" >
									<div class="input-append">
										<div class="uneditable-input">
											<i class="glyphicon glyphicon-file fileupload-exists"></i>
											<span class="fileupload-preview"></span>
										</div>
										<span class="btn btn-default btn-file">
											<span class="fileupload-new">찾아보기</span>
											<span class="fileupload-exists">Change</span>
											<input type="file" name="theme_thumb" />
										  </span>
										<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
										<?php if(!empty($idx) && file_exists($hpath.'theme_'.$idx.'.png') === true) { ?>
											<button type="button" class="btn btn-default" onclick="download_theme('image')">다운로드</button>
										<?php } ?>
									</div>
								</div>

								<label class="col-sm-1 control-label" style="width:130px;margin-top:10px;">신규테마 공통 <span class="asterisk"></span></label>
								<div class="fileupload fileupload-new" data-provides="fileupload" style="margin-top:10px;">
									<div class="input-append">
										<div class="uneditable-input">
											<i class="glyphicon glyphicon-file fileupload-exists"></i>
											<span class="fileupload-preview"></span>
										</div>
										<span class="btn btn-default btn-file">
											<span class="fileupload-new">찾아보기</span>
											<span class="fileupload-exists">Change</span>
											<input type="file" name="common_theme_file" />
										</span>
										<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
										<?php if(!empty($idx) && file_exists($common_path.'common_theme_'.$idx.'.zip') === true) { ?>
											<button type="button" class="btn btn-default" onclick="download_theme('common_theme')">다운로드</button>
										<?php } ?>
									</div>
								</div>

								<label class="col-sm-1 control-label" style="width:130px;margin-top:10px;">신규테마 커스텀 <span class="asterisk"></span></label>
								<div class="fileupload fileupload-new" data-provides="fileupload" style="margin-top:10px;">
									<div class="input-append">
										<div class="uneditable-input">
											<i class="glyphicon glyphicon-file fileupload-exists"></i>
											<span class="fileupload-preview"></span>
										</div>
										<span class="btn btn-default btn-file">
											<span class="fileupload-new">찾아보기</span>
											<span class="fileupload-exists">Change</span>
											<input type="file" name="custom_theme_file" />
										</span>
										<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
										<?php if(!empty($idx) && file_exists($custom_path.'custom_theme_'.$idx.'.zip') === true) { ?>
											<button type="button" class="btn btn-default" onclick="download_theme('custom_theme')">다운로드</button>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class=" col-sm-1 control-label" >카테고리 <span class="asterisk"></span></label>
							<div class="col-sm-10">
								<?php
								//현재 테마에 적용된 카테고리 리스트
								if($idx){
									$sql="
										SELECT * FROM ckd_theme_cate
										WHERE theme_idx='{$idx}'
									";
									$ret=$NDO->fetch_array($sql);
									foreach($ret as $res){
										$act_theme[] = $res['theme_cate_code'];
									}
								}

								//등록된 카테고리 리스트
								$sql="
									SELECT * FROM ckd_com_code
									WHERE code_tp_id='theme_cate_code'
									";
								$ret=$NDO->fetch_array($sql);
								foreach($ret as $res){
									$checked = "";
									if(!empty($act_theme)) {
										if (in_array($res['code_id'], $act_theme)) {
											$checked = "checked";
										}
									}
								?>
									<label style="padding-right: 7px">
										<div class="pull-left"><input style="width:20px;height:20px;" type="checkbox" name="theme_cate[]" value="<?=$res['code_id']?>" <?=$checked?>></div>
										<div class="pull-left" style="padding-top:5px;padding-left: 3px;"><?=$res['code_desc']?></div>
									</label>
								<?php }?>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-1 control-label " >테마 적용 범위<span class="asterisk"></span></label>
							<div class="col-sm-10">
								<select class="form-control pull-left border-input" name="partner_code" style="width:150px;margin-right:10px;" required>
									<option value="">테마 적용 범위</option>
									<option value="01" <?=!empty($theme) && $theme['theme_partner_code']=="01"?"selected":""?> >일반</option>
									<option value="02" <?=!empty($theme) && $theme['theme_partner_code']=="02"?"selected":""?> >프리미엄</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-1 control-label " >테마 타이틀<span class="asterisk"></span></label>
							<div class="col-sm-10">
								<input type="text" name="theme_title" class="form-control"  value="<?=empty($theme)?"":$theme['theme_title']?>" placeholder="" required />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-1 control-label ">테마설명 <span class="asterisk"></span></label>
							<div class="col-sm-10" style="margin-bottom:20px;">
								<textarea id="theme_cnts" name="theme_cnts" placeholder="" class="form-control" rows="15"><?=empty($theme)?"":$theme['theme_cnts']?></textarea>
							</div>
						</div>

					</div>
				</div>

				<div class="row">
					<div class="text-center">
						<div class="">
							<button type="submit" class="btn btn-success"><?=$actTitle?></button>
							<button type="button" class="btn btn-default" onclick="location.href='theme.php?np=<?=$page?>' ">목록</button>
						</div>
					</div>
				</div>

			</form>

		</div><!-- panel-body -->
	</div><!-- panel panel-default -->

</div><!-- contentpanel -->

<script>
$(document).ready(function(){


});
</script>

<?php
include __foot__;
?>