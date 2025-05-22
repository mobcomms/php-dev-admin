<?php
/**********************************************************
 *
 *	문의관리
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

$target = empty($_REQUEST['target'])?"":trim($_REQUEST['target']);
if($target=="SDK"){
	define('_subMenu_', 'inquiry_sdk');
}else if($target=="PPZ"){
	define('_subMenu_', 'inquiry_ppz');
}else{
	define('_subMenu_', 'inquiry');
}

## 환경설정
define('_title_', '문의하기');
define('_Menu_', 'inquiry');
//$debugMode='Y';

include __head__; ## html 헤더 출력

$type = empty($_REQUEST['type'])?"":$_REQUEST['type'];
$search_type = empty($_REQUEST['search_type'])?"":$_REQUEST['search_type'];
$keyword = empty($_REQUEST['keyword'])?"":trim($_REQUEST['keyword']);
$os_type = empty($_REQUEST['os_type'])?"":trim($_REQUEST['os_type']);

$subQry = "";
$ckd_param = [];
$param = "";
if($type){
	if($type==4){
		$subQry .= " AND bbs_state='01'";
	}else if($type==5){
		$subQry .= " AND bbs_state='02'";
	}else{
		$subQry .= " AND type=:type";
		$ckd_param[":type"] = $type;
	}
	$param .= "&type=".$type;
}
$param .= "&search_type=".$search_type;
if($keyword){
	if($search_type){
		switch($search_type){
			case "01" : $subQry.=" AND question";break;
			case "02" : $subQry.=" AND reg_user";break;
		}
	}
	$subQry.=" like :question";
	$ckd_param[":question"] = "%".$keyword."%";
	$param .= "&keyword=".urlencode($keyword);
}

if($os_type == "A"){
	$subQry .= " AND user_app_os = 'aos' ";
}else if($os_type == "I"){
	$subQry .= " AND user_app_os = 'ios' ";
}else if($os_type == "N"){
	$subQry .= " AND user_app_os is null ";
}
$param .= "&os_type={$os_type}";

switch($target){
	case "SDK" : $subQry .= " AND title = 'SDK'";break;
	case "PPZ" : $subQry .= " AND title = 'PPZ'";break;
	default : $subQry .= " AND title = 'SDK'";break;
}
$param .= "&target={$target}";

$sql="
	SELECT count(*) AS cnt FROM ckd_bbs_inquiry cbi
	LEFT JOIN ckd_user_info AS cui ON  cbi.reg_user = cui.user_uuid
	WHERE del_yn = 'N' 
	{$subQry}
	ORDER BY seq DESC
";
//pre($sql);
$total = $NDO->getData($sql,$ckd_param);

include_once __page__;
$np = $now = ret(INPUT_GET, 'np');
$paging->np = $now;
$paging->ps = 20;
$PG = $paging->init($total['cnt']);

$sql="
	SELECT * FROM ckd_bbs_inquiry cbi
	LEFT JOIN ckd_user_info AS cui ON  cbi.reg_user = cui.user_uuid
	WHERE del_yn = 'N'
	{$subQry}
	ORDER BY seq DESC
	LIMIT {$PG->first},{$PG->size}
";
//pre($sql);
$result = $NDO->fetch_array($sql,$ckd_param);
//pre($result);

$query_string = http_build_query(compact('type','search_type', 'keyword', 'np','target'));
//pre($query_string);
?>

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
							<input type="hidden" name="target" value="<?=$target?>">
							<div class="pull-left" style="margin-bottom: 10px;width: 100%;">
								<div class="col-sm-12">
									<label style="padding-right: 7px;cursor: pointer">
										<div class="pull-left"><input style="width:20px;height:20px;" type="radio" name="type" value="0" <?=empty($type) || $type==0?"checked":""?> ></div>
										<div class="pull-left" style="padding-top:5px;padding-left: 3px;">전체</div>
									</label>
									<label style="padding-right: 7px;cursor: pointer">
										<div class="pull-left"><input style="width:20px;height:20px;" type="radio" name="type" value="01" <?=$type==1?"checked":""?> ></div>
										<div class="pull-left" style="padding-top:5px;padding-left: 3px;">이용 문의</div>
									</label>
									<label style="padding-right: 7px;cursor: pointer">
										<div class="pull-left"><input style="width:20px;height:20px;" type="radio" name="type" value="02" <?=$type==2?"checked":""?> ></div>
										<div class="pull-left" style="padding-top:5px;padding-left: 3px;">적립 문의</div>
									</label>
									<label style="padding-right: 7px;cursor: pointer">
										<div class="pull-left"><input style="width:20px;height:20px;" type="radio" name="type" value="03" <?=$type==3?"checked":""?> ></div>
										<div class="pull-left" style="padding-top:5px;padding-left: 3px;">기타</div>
									</label>
									<label style="padding-right: 7px;cursor: pointer">
										<div class="pull-left"><input style="width:20px;height:20px;" type="radio" name="type" value="04" <?=$type==4?"checked":""?> ></div>
										<div class="pull-left" style="padding-top:5px;padding-left: 3px;">미답변</div>
									</label>
									<label style="padding-right: 7px;cursor: pointer">
										<div class="pull-left"><input style="width:20px;height:20px;" type="radio" name="type" value="05" <?=$type==5?"checked":""?> ></div>
										<div class="pull-left" style="padding-top:5px;padding-left: 3px;">답변완료</div>
									</label>
								</div>

								<label style="margin: 4px 0 0 10px">
									<div class="pull-left" style="line-height: 30px;font-weight: bold;">운영체제 선택</div>
								</label>
								<label style="margin: 4px 0 0 20px">
									<div class="pull-left"><input style="width:20px;height:20px;accent-color: darkcyan;" type="radio" name="os_type" id="os_type" <?=empty($os_type)?"checked":""?> value=""></div>
									<div class="pull-left" style="padding-top:5px;padding-left:3px;">전체</div>
								</label>
								<label style="margin: 4px 0 0 20px">
									<div class="pull-left"><input style="width:20px;height:20px;accent-color: darkcyan;" type="radio" name="os_type" id="os_type_a" <?=($os_type=="A")?"checked":""?> value="A"></div>
									<div class="pull-left" style="padding-top:5px;padding-left:3px;">Android</div>
								</label>
								<label style="margin: 4px 0 0 20px">
									<div class="pull-left"><input style="width:20px;height:20px;accent-color: darkcyan;" type="radio" name="os_type" id="os_type_i" <?=($os_type=="I")?"checked":""?> value="I"></div>
									<div class="pull-left" style="padding-top:5px;padding-left:3px;">iOS</div>
								</label>
								<label style="margin: 4px 0 0 20px">
									<div class="pull-left"><input style="width:20px;height:20px;accent-color: darkcyan;" type="radio" name="os_type" id="os_type_n" <?=($os_type=="N")?"checked":""?> value="N"></div>
									<div class="pull-left" style="padding-top:5px;padding-left:3px;">미등록</div>
								</label>

								<div style="display: inline-block;" class="pull-right">
									<button class="btn btn-success" style="margin-left:10px;" id="ExcelDown" onclick="return false">Excel 다운</button>
								</div>

								<div class="pull-left" style="clear: both;">
									<select class="form-control pull-left border-input" name="search_type" style="width:120px;height: 38px;margin-right: 5px;">
										<option value="01" <?=$search_type=="01"?"selected":""?>>문의내용</option>
										<option value="02" <?=$search_type=="02"?"selected":""?>>사용자토큰</option>
									</select>
									<input type="text" class="form-control pull-left border-input" name="keyword" style="width:280px; height:38px;" placeholder="검색 하세요" value="<?=$keyword?>" autocomplete="off" />
									<button class="btn btn-success" style="height:38px;">검 색</button>
								</div>
								<div style="display: inline-block;" class="pull-left">
									<button type="button" class="btn btn-warning" style="margin-left:10px;">일괄 변경</button>
								</div>
							</div>
						</form>

						<div class="table-responsive">
							<table class="table table-hover mb30 member_table" id="Excel">
								<thead>
								<tr>
									<th class="col-md-1" style="width: 70px;"><input style="width:15px;height:15px;accent-color: darkcyan;" type="checkbox" id="is_chk"></th>
									<th class="col-md-1" style="width: 70px;">번호</th>
									<th class="col-md-1" style="width: 70px;">key</th>
									<th class="col-md-1" style="width: 280px;">ADID</th>
									<th class="col-md-1">문의유형</th>
									<th>문의내용</th>
									<th class="col-md-1">등록일</th>
									<th class="col-md-1">답변일</th>
									<th class="col-md-1">답변상태</th>
									<th class="col-md-1">삭제</th>
								</tr>
								</thead>
								<tbody id="Excel">
								<?php if(empty($result) || !is_array($result)){?>
									<tr><td colspan="11">문의 리스트가 없습니다.</td></tr>
								<?php
								}else{
									foreach( $result AS $res){
										switch($res['type']){
											case "01" : $inquiry_type = "이용 문의";break;
											case "02" : $inquiry_type = "적립 문의";break;
											case "03" : $inquiry_type = "기타";break;
											case "04" : $inquiry_type = "미답변";break;
											case "05" : $inquiry_type = "답변완료";break;
											default : $inquiry_type = "선택안함";break;
										}
									?>
									<tr>
										<td align="center"><input style="width:15px;height:15px;accent-color: darkcyan;" type="checkbox" value="<?=$res['seq']?>" class="_checkbox"></td>
										<td><?=number_format($PG->first_num)?></td>
										<td><?=$res['seq']?></td>
										<td><?=$res['user_adid']?></td>
										<td><?=$inquiry_type?></td>
										<td style="text-align: left;"><?=htmlspecialchars_decode($res['question'])?></td>
										<td><?=substr($res['regdate'],0,10)?></td>
										<td><?=empty($res['editdate'])?"":substr($res['editdate'],0,10)?></td>
										<td><?=$res['bbs_state']=='01'?"미답변":"답변완료";?></td>
										<td>
											<button style="margin-bottom: 3px;" class="btn btn-xs btn-primary" onclick="location.href='board_inquiry_write.php?seq=<?=$res['seq']?>&<?=$query_string?>'">수정</button>
											<button class="btn btn-xs btn-danger" onclick="inquiry_delete(<?=$res['seq']?>)">삭제</button>
										</td>
									</tr>
								<?php
									$PG->first_num--;
									}//forach
								}
								?>

								</tbody>
							</table>
						</div><!-- table-responsive -->
					</div><!-- col-md-12 -->

				</div><!-- row -->

				<div class="row">
					<?=$paging->paging_new($PG,$param);?>
				</div><!-- row -->

			</div>
		</div>
	</div><!-- contentpanel -->

<script>
	function inquiry_delete(seq) {
		if(!confirm("정말로 삭제하시겠습니까?")){
			return false;
		}
		$.post("board_inquiry_write.php",{seq:seq ,method:'del2'},function(data){
			if(data == "OK"){
				location.reload();
			}else{
				alert('오류가 발생 했습니다. 새로고침후 다시 시도해 주세요');
			}
		},'html');
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

	function display_toggle(os_type){
		//location.href="?search_type=<?=$search_type?>&type=<?=$type?>&keyword=<?=$keyword?>&target=<?=$target?>&os_type="+os_type;
	}


	function fnExcelReport(id, title) {
		var tab_text = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
		tab_text = tab_text + '<head><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">';
		tab_text = tab_text + '<xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>'
		tab_text = tab_text + '<x:Name>Sheet1</x:Name>';
		tab_text = tab_text + '<x:WorksheetOptions><x:Panes></x:Panes></x:WorksheetOptions></x:ExcelWorksheet>';
		tab_text = tab_text + '</x:ExcelWorksheets></x:ExcelWorkbook></xml></head><body>';
		tab_text = tab_text + "<table border='1px'>";
		var exportTable = $('#' + id).clone();
		exportTable.find('input').each(function (index, elem) { $(elem).remove(); });
		tab_text = tab_text + exportTable.html();
		tab_text = tab_text + '</table></body></html>';
		var data_type = 'data:application/vnd.ms-excel';
		var ua = window.navigator.userAgent;
		var msie = ua.indexOf("MSIE ");
		var fileName = title + '.xls';
//Explorer 환경에서 다운로드
		if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
			if (window.navigator.msSaveBlob) {
				var blob = new Blob([tab_text], {
					type: "application/csv;charset=utf-8;"
				});
				navigator.msSaveBlob(blob, fileName);
			}
		} else {
			var blob2 = new Blob([tab_text], {
				type: "application/csv;charset=utf-8;"
			});
			var filename = fileName;
			var elem = window.document.createElement('a');
			elem.href = window.URL.createObjectURL(blob2);
			elem.download = filename;
			document.body.appendChild(elem);
			elem.click();
			document.body.removeChild(elem);
		}
	}
	$("#ExcelDown").click(function(){
		fnExcelReport('Excel','문의내역');
	});

	$(".btn-warning").click(function(){
		var chk_seq = [];
		$("._checkbox").each(function(){
			if($(this).is(':checked')){
				chk_seq.push($(this).val());
			}
		});
		if(chk_seq.length > 0){
			centerOpenWindow('upd_board_inquiry.php', 'upd_board_inquiry', '700', '624', '', 'N');
		}else{
			alert("일괄변경할 문의를 선택해 주세요.");
		}
	});

	$("#is_chk").click(function(){
		if($(this).is(":checked")){
			$("._checkbox").prop("checked",true);
		}else{
			$("._checkbox").prop("checked",false);
		}
	});
</script>

<?php
include __foot__;
?>