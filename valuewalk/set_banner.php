<?php
/**********************
 *
 *    적립 설정
 *
 **********************/
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors", 1);
## 환경설정
define('_title_', '적립 설정');
define('_Menu_', 'setting');
define('_subMenu_', 'banner_set');

include "./var.php";
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성
include_once __head__; ## html 헤더 출력

if($admin['level'] != 'super'){
	hist("권한이 없습니다.");
}

$sql=" SELECT * FROM ckd_banner_set WHERE type=1 ";
$result = $NDO->getData($sql);
//pre($result);

//히스토리 가져오기
$sql=" SELECT * FROM ckd_banner_set_history WHERE type=1 ORDER BY idx DESC LIMIT 8";
$result1 = $NDO->fetch_array($sql);

$sql=" SELECT * FROM ckd_banner_set_history WHERE type=2 ORDER BY idx DESC LIMIT 8";
$result2 = $NDO->fetch_array($sql);

?>
<div class="pageheader">
	<h4 class="panel-title">단일 광고 설정</h4>
</div>

<div class="contentpanel">
	<div class="panel panel-default">

		<form method="post" name="set_banner" id="set_banner">
		<input type="hidden" name="mode" value="set_banner">
		<input type="hidden" name="useYN" id="use_YN" value="<?=$result['useYN']?>">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--				<a href="" class="panel-close">&times;</a>-->
				<!--				<a href="" class="minimize">&minus;</a>-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title">
				<div style="float: left;line-height: 40px;padding-right: 20px;">광고 사용 여부</div>
				<div class="btn-group btn-toggle use_YN" style="margin-bottom: 0;" data-otpc="<?=$result['useYN']?>" data-code_id="1">
					<button class="btn btn-xs <?=($result['useYN']!='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="Y" style="z-index: 0;">ON </button>
					<button class="btn btn-xs <?=($result['useYN']=='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="N" style="z-index: 0;">OFF </button>
				</div>

			</h4>
		</div><!-- panel-heading -->

		<div class="panel-body">
			<h4 class="panel-title"></h4>
			<div class="table-responsive">
				<table class="table member_table">
					<caption style="text-align: left;color: black;"><h4 style="margin-bottom:0;">광고 지면코드</h4></caption>
					<thead>
					<tr>
						<th class="col-md-1">지면명</th>
						<th class="col-md-1">zone ID</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>모비컴즈_신한카드_리워드배너_AOS_320_100(JSON)</td>
						<td>10886312</td>
					</tr>
					<tr>
						<td>모비컴즈_신한카드_리워드배너_iOS_320_100(JSON)</td>
						<td>10886313 </td>
					</tr>
					<tr>
						<td>모비컴즈_신한카드_사다리배너_AOS_300_250(JSON)</td>
						<td>10886314</td>
					</tr>
					<tr>
						<td>모비컴즈_신한카드_사다리배너_iOS_300_250(JSON)</td>
						<td>10886315</td>
					</tr>
					<tr>
						<td>쿠팡_리워드_배너_AOS</td>
						<td>shinhanpointaos</td>
					</tr>
					<tr>
						<td>쿠팡_리워드_배너_iOS</td>
						<td>shinhanpointios</td>
					</tr>
					<tr>
						<td>쿠팡_사다리배너_AOS</td>
						<td>shinhansadariaos</td>
					</tr>
					<tr>
						<td>쿠팡_사다리배너_iOS</td>
						<td>shinhansadariios</td>
					</tr>
					</tbody>
				</table>
				<table class="member_table2">
					<caption style="text-align: left;"><h4 style="margin-bottom:0;">리워드 이미지</h4></caption>
					<thead>
					<tr>
						<th class="col-md-1">리워드 BG 이미지</th>
						<th class="col-md-1">폰트컬러(Hex)</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>
							<div class="form-group">
								<div class="col-sm-9">
									<span style="float:left;width:120px;height:120px;display: inline-block;line-height: 120px;">
										<img src="<?=$result['img']?>?<?=time()?>" border="1" onerror="this.onerror=null;this.src='http://valuewalk-admin.commsad.com/img/noImg.jpg'">
									</span>
									<div style="margin-top: 40px;" class="fileupload fileupload-new" data-provides="fileupload"><input type="hidden">
										<div class="input-append">
											<div class="uneditable-input" style="clear:both;width: 300px;">
												<i class="glyphicon glyphicon-file fileupload-exists"></i>
												<span class="fileupload-preview"></span>
											</div>
											<span class="btn btn-default btn-file">
										<span class="fileupload-new">찾아보기</span>
										<span class="fileupload-exists">Change</span>
											<input type="file" name="banner_icon" onchange="checkFile(this)" accept="image/*">
										</span>
											<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
											<?php if(!empty($result['img']) && file_exists($result['img']) === true) { ?>
												<button type="button" class="btn btn-default" onclick="download_theme('<?=$img_info['util_icon_img']['idx']?>')">다운로드</button>
											<?php } ?>
										</div>
									</div>
								</div>
							</div>

						</td>
						<td style="padding:8px">
							<input type="text" class="form-control border-input" id="font_color" name="font_color" autocomplete="off" value="<?=$result['font_color']?>">
						</td>
					</tr>
					</tbody>
				</table>
				<table class="table member_table">
					<thead>
					<tr>
						<th class="col-md-1">리워드 단위</th>
						<th class="col-md-1">1회 제공 리워드</th>
						<th class="col-md-1">일 한도 리워드</th>
						<th class="col-md-1">프리퀀시</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td><input type="text" class="form-control border-input" name="reward_unit" id="reward_unit" value="<?=$result['unit']?>"></td>
						<td>
							<div class="input-group">
								<input style="text-align: right" type="number" class="form-control border-input" name="reward_point" id="reward_point" autocomplete="off" value="<?=$result['point']?>">
								<span class="input-group-addon">P</span>
							</div>
						</td>
						<td>
							<div class="input-group">
							<input style="text-align: right" type="number" class="form-control border-input" name="reward_max_point" id="reward_max_point" value="<?=$result['max_point']?>">
							<span class="input-group-addon">P</span>
							</div>
						</td>
						<td>
							<div class="input-group">
							<input style="text-align: right" type="number" class="form-control border-input" name="reward_frequency" id="reward_frequency" value="<?=$result['frequency']?>">
							<span class="input-group-addon">분</span>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</form>

				<div style="text-align: center;"><button type="button" class="btn btn-success save" data-code_id="1" style="width: 300px;">적용</button></div>
				<table class="table member_table">
					<caption style="text-align: left;"><h4 style="margin-bottom:0;">히스토리</h4></caption>
					<thead>
					<tr>
						<th class="col-md-1">시작일</th>
						<th class="col-md-1">종료일</th>
						<th class="col-md-1">리워드 단위</th>
						<th class="col-md-1">1회 제공 리워드</th>
						<th class="col-md-1">일 한도 리워드</th>
						<th class="col-md-1">프리퀀시</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($result1 as $row){ ?>
						<tr>
							<td><?=$row['start_dttm']?></td>
							<td><?=$row['stop_dttm']?></td>
							<td><?=$row['reward_point']?></td>
							<td><?=$row['reward_unit']?></td>
							<td><?=$row['reward_max_point']?></td>
							<td><?=$row['reward_frequency']?></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>

			</div><!-- table-responsive -->
		</div><!-- panel-body -->
	</div><!-- panel -->

</div><!-- contentpanel -->

<script>
	function checkFile(el){
		// files 로 해당 파일 정보 얻기.
		var file = el.files;

		// file[0].size 는 파일 용량 정보입니다.
		if(file[0] && file[0].size > 1024 * 1024 * 1){
			// 용량 초과시 경고후 해당 파일의 용량도 보여줌
			alert('1MB 이하 파일만 등록할 수 있습니다.\n\n' + '현재파일 용량 : ' + (Math.round(file[0].size / 1024 / 1024 * 100) / 100) + 'MB');
		}
	}
	$(document).ready(function(){

		//리워드 설정
		$(".use_YN, .save").on("click",function(){

			//광고 여부
			if($(this).hasClass("use_YN") == true) {
				var otpc = $(this).data("otpc");
				var otpc_value = otpc=="Y"?"N":"Y";

				$(".use_YN").data("otpc",otpc_value);
				$("#use_YN").val(otpc_value);
			}

			var form = $('#set_banner')[0];
			var formData = new FormData(form);

			//리워드 이미지
			var inputFile = $("input[name='banner_icon']");
			var files = inputFile[0].files;
			for(var i =0;i<files.length;i++){
				formData.append("banner_icon", files[i]);
			}

			//폰트컬러
			var font_color = $("#font_color").val();
			//리워드 단위
			var reward_unit = $("#reward_unit").val();
			//1회 제공 리워드
			var reward_point = $("#reward_point").val();
			//일 한도 리워드
			var reward_max_point = $("#reward_max_point").val();
			//프리퀀시
			var reward_frequency = $("#reward_frequency").val();

			if(!font_color){
				alert("폰트컬러(Hex)를 입력 하세요.");
				$("#font_color").focus();
				return;
			}
			if(!reward_unit){
				alert("리워드 단위를 입력 하세요.");
				$("#reward_unit").focus();
				return;
			}
			if(!reward_point){
				alert("지급할 포인트를 입력 하세요.");
				$("#reward_point").focus();
				return;
			}
			//var requestData = {mode:"ad_reward",otpc_value:otpc_value,font_color:font_color,reward_unit:reward_unit,reward_point:reward_point,reward_max_point:reward_max_point,reward_frequency:reward_frequency};
			//formData.append("mode","ad_reward");
			//formData.append("request", new Blob([JSON.stringify(requestData)], {type: "application/json"}));

			$.ajax({
				url: './ajax/ajax_process.php',
				type : 'POST',
				dataType : 'html',
				enctype : 'multipart/form-data',
				processData : false,
				contentType : false,
				data : formData,
				async : false,
				success : function(result){
					if(result === "ok"){
						alert("적용 되었습니다.");
						location.reload();
					}else {
						alert("수정 실패");
					}

				}

			}); //$.ajax


		});

	});
</script>

<?php
include __foot__;
?>
