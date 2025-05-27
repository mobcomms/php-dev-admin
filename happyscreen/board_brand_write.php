<?php
/**********************************************************
 *
 * 게시판관리
 *
 ************************************************************/

include "./var.php";
include_once __func__;

### 게시글 가져오기 [S]
$idx=empty($_REQUEST['idx'])?"":$_REQUEST['idx'];

if(($idx)){
	$sql = "
		SELECT *
		FROM ckd_bbs
		WHERE del_yn='N' AND seq = '{$idx}'
	";
	$bbs = $NDO->getData($sql);

	if($bbs) {
		$bbs['s_hour']= substr($bbs['start_date'], 11, 2);
		$bbs['s_min'] = substr($bbs['start_date'], 14, 2);

		$bbs['e_hour'] = substr($bbs['end_date'], 11, 2);
		$bbs['e_min'] = substr($bbs['end_date'], 14, 2);

		$bbs['start_date'] = ($bbs['start_date'] == "0000-00-00") ? "" : substr($bbs['start_date'], 0, 10);
		$bbs['end_date'] = ($bbs['end_date'] == "0000-00-00") ? "" : substr($bbs['end_date'], 0, 10);

		//이미지 가져오기
		$sql = "
			SELECT * FROM ckd_file_upload
			WHERE service_tp_code='03' AND type='brand' AND board_idx = '{$idx}'
		";
		$img = $NDO->fetch_array($sql);

		foreach ($img as $temp) {
			$img_info[$temp['file_input_name']] = [];
			$img_info[$temp['file_input_name']]['idx'] = $temp['idx'];
			$img_info[$temp['file_input_name']]['file_name'] = $temp['file_path'] . $temp['file_name'];
			$img_info[$temp['file_input_name']]['root_path'] = __root__.$temp['file_path'] . $temp['file_name'];
		}
	}else{
		$fn->hist("잘못된 접근 입니다.");
	}
}

$url="http://".__host__;
$thumb_brand_img=!empty($img_info['brand_img']['file_name']) ? $url."/".$img_info['brand_img']['file_name'] : $url."/img/noImg.jpg";
$thumb_util_icon_img=!empty($img_info['util_icon_img']['file_name']) ? $url."/".$img_info['util_icon_img']['file_name'] : $url."/img/noImg.jpg";
### 이미지 가져오기 [E]

## 환경설정
define('_title_', '브랜드 고정광고');
define('_Menu_', 'adv');
define('_subMenu_', 'brand');
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
				<input type="hidden" name="type" value="brand" />
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
								<span class="asterisk">*</span> 시작일시
							</div>
							<div class="pull-left input-group call  col-md-2" style="margin-right:20px;">
								<input type="text" class="form-control pull-left border-input" placeholder="yyyy-mm-dd" id="boardSdate" value="<?=empty($bbs)?"":$bbs['start_date']?>" name="sdate" autocomplete="off" >
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>
							<select class="form-control pull-left border-input" name="s_hour" style="width:100px;height: 40px;margin: 0">
								<?php for($i=0;$i<24;$i++){?>
								<option value="<?=$i<10 ? "0".$i : $i?>" <?=!empty($bbs) && ($bbs['s_hour']==$i)?'selected':''?>><?=$i<10 ? "0".$i : $i?>시</option>
								<?php }?>
							</select>
							<select class="form-control pull-left border-input" name="s_min" style="width:100px;height: 40px;margin-left: 10px;">
								<option value="00" <?=!empty($bbs) && ($bbs['s_min']=='00')?'selected':''?>> 00분 </option>
								<option value="30" <?=!empty($bbs) && ($bbs['s_min']=='30')?'selected':''?>> 30분 </option>
							</select>
						</div>

						<div class="form-group">
							<div class="col-sm-1 control-label" style="width: 200px;line-height:30px; margin-right:10px;">
								<span class="asterisk">*</span> 종료일시
							</div>
							<div class="pull-left input-group call  col-md-2" style="margin-right:20px;">
								<input type="text" class="form-control pull-left border-input" placeholder="yyyy-mm-dd" id="boardEdate" value="<?=empty($bbs)?"":$bbs['end_date']?>" name="edate" autocomplete="off" >
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>
							<select class="form-control pull-left border-input" name="e_hour" style="width:100px;height: 40px;margin: 0">
								<?php for($i=0;$i<24;$i++){?>
									<option value="<?=$i<10 ? "0".$i : $i?>" <?=!empty($bbs) && ($bbs['e_hour']==$i)?'selected':''?>><?=$i<10 ? "0".$i : $i?>시</option>
								<?php }?>
							</select>
							<select class="form-control pull-left border-input" name="e_min" style="width:100px;height: 40px;margin-left: 10px;">
								<option value="00" <?=!empty($bbs) && ($bbs['e_min']=='00')?'selected':''?>> 00분 </option>
								<option value="30" <?=!empty($bbs) && ($bbs['e_min']=='30')?'selected':''?>> 30분 </option>
							</select>
						</div>

						<div class="form-group">
							<label class="col-sm-1 control-label" style="width: 200px;"><span class="asterisk">*</span>  유틸 아이콘 이미지</label>
							<div class="col-sm-9">
								<span style="float:left;width:120px;height:100px;display: inline-block;"><img src="<?=$thumb_util_icon_img?>?<?=time()?>" border="1" style="width:120px;"></span>
								<div class="fileupload fileupload-new" data-provides="fileupload"><input type="hidden">
									<div class="input-append">
										<div class="uneditable-input" style="width: 300px;">
											<i class="glyphicon glyphicon-file fileupload-exists"></i>
											<span class="fileupload-preview"></span>
										</div>
										<span class="btn btn-default btn-file">
										<span class="fileupload-new">찾아보기</span>
										<span class="fileupload-exists">Change</span>
											<input type="file" name="util_icon_img" onchange="checkFile(this)" accept="image/*">
										</span>
										<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
										<?php if(!empty($img_info) && file_exists($img_info['util_icon_img']['root_path']) === true) { ?>
											<button type="button" class="btn btn-default" onclick="download_theme('<?=empty($img_info)?"":$img_info['util_icon_img']['idx']?>')">다운로드</button>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-1 control-label" style="width: 200px;">광고 이미지</label>
							<div class="col-sm-9">
								<span style="float:left;width:120px;height:100px;display: inline-block;"><img src="<?=$thumb_brand_img?>?<?=time()?>" border="1" style="width:120px;"></span>
								<div class="fileupload fileupload-new" data-provides="fileupload"><input type="hidden">
									<div class="input-append">
										<div class="uneditable-input" style="width: 300px;">
											<i class="glyphicon glyphicon-file fileupload-exists"></i>
											<span class="fileupload-preview"></span>
										</div>
										<span class="btn btn-default btn-file">
										<span class="fileupload-new">찾아보기</span>
										<span class="fileupload-exists">Change</span>
											<input type="file" name="brand_img" onchange="checkFile(this)" accept="image/*">
										</span>
										<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
										<?php if(!empty($img_info) && file_exists($img_info['brand_img']['root_path']) === true) { ?>
											<button type="button" class="btn btn-default" onclick="download_theme('<?=$img_info['brand_img']['idx']?>')">다운로드</button>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class=" col-sm-1 control-label" style="width: 200px;">쿠팡 링크</label>
							<div class="col-sm-9">
								<input type="text" id="coupang_link" class="form-control" style="width: 500px;float: left;" />
								<button type="button" class="btn btn-primary" style="line-height: 25px;" id="make_coupang_link">링크생성</button>
							</div>
						</div>

						<div class="form-group">
							<label class=" col-sm-1 control-label" style="width: 200px;">광고 링크 URL</label>
							<div class="col-sm-9">
								<input type="text" name="url" class="form-control" style="width:700px" placeholder="이동해야할 페이지가 있다면 이곳에 입력해주세요"  value="<?=empty($bbs)?"":$bbs['url']?>" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-1 control-label" style="width: 200px;">노출여부</label>
							<div class="col-sm-10" style="line-height: 37px;">
								<input type="checkbox" class="chk_box" name="display_yn" value="Y" style="width:20px;height:20px;margin-left:10px;" <?=!empty($bbs) && $bbs['display_yn']=='Y'?'checked':''?> >
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-1 control-label" style="width: 200px;">default 여부</label>
							<div class="col-sm-10" style="line-height: 37px;">
								<input type="checkbox" class="chk_box" name="default_yn" value="Y" style="width:20px;height:20px;margin-left:10px;" <?=!empty($bbs) && $bbs['default_yn']=='Y'?'checked':''?> >
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
							<button type="button" class="btn btn-default" onclick="location.href='board_brand.php' ">목록</button>
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
	<input type="hidden" name="mode" value="img_download" />
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
		var sdate=$("[name=sdate]").val().replace(/[^0-9]/g,"");
		var edate=$("[name=edate]").val().replace(/[^0-9]/g,"");

		if(!f.title.value){
			alert('타이틀을 입력해주세요.');
			f.title.focus();
			return;
		}

		if(!sdate) {
			alert("시작일시를 입력해 주세요.");
			$("[name=sdate]").focus();
			return;
		}
		if(!edate) {
			alert("종료일시를 입력해 주세요.");
			$("[name=edate]").focus();
			return;
		}
		if(sdate || edate) {
			var datatimeRegexp = /[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])/;

			if( sdate && !datatimeRegexp.test(f.sdate.value) ) {
				alert("날짜는 yyyy-mm-dd 형식으로 입력해주세요.");
				f.sdate.focus();
				return false;
			}

			if( edate && !datatimeRegexp.test(f.edate.value) ) {
				alert("날짜는 yyyy-mm-dd 형식으로 입력해주세요.");
				f.edate.focus();
				return false;
			}

			if(sdate && edate && sdate > edate) {
				alert('종료일이 시작일보다 빠릅니다. 기간 설정을 확인해주세요.');
				return;
			}
		}
<?php if(!$idx){?>
		if(!$("[name=util_icon_img]").val()){
			alert("유틸 아이콘 이미지를 입력해 주세요.");
			return;
		}
		if(!$("[name=brand_img]").val()){
			alert("광고 이미지를 입력해 주세요.");
			return;
		}
<?php }?>
		f.submit();
	}

	function download_theme(board_idx){
		f=document.downloadForm;
		f.file_idx.value = board_idx;
		f.submit();
	}

	function del_brand(board_idx){
		if(confirm("정말로 삭제 하시겠습니까?")) {
			f = document.delForm;
			f.file_idx.value = board_idx;
			f.submit();
		}
	}

	function checkFile(el){
		// files 로 해당 파일 정보 얻기.
		var file = el.files;

		// file[0].size 는 파일 용량 정보입니다.
		if(file[0] && file[0].size > 1024 * 1024 * 40){
			// 용량 초과시 경고후 해당 파일의 용량도 보여줌
			alert('40MB 이하 파일만 등록할 수 있습니다.\n\n' + '현재파일 용량 : ' + (Math.round(file[0].size / 1024 / 1024 * 100) / 100) + 'MB');
		}
	}

	//쿠팡 링크생성 API 연동.
	$("#make_coupang_link").on("click",function() {
		var url = "./ajax/post_coupang_link.php";
		var data = {
			"link" : $("#coupang_link").val()
			,"subId" : "ocbkeyboard"
		};
		$.post(url, data, function(api){
			if(api.errcode){
				alert("errorCode : "+api.errcode+"\n"+api.errstr);
			}else{
				$("input[name='url']").val(api.shortenUrl);
			}
		},"json")
	})
</script>

<?php
include __foot__;
?>
