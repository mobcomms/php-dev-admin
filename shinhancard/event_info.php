<?php
/**********************
 *
 *    이벤트 셋팅
 *
 **********************/
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors", 1);

include "./var.php";
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성
include __fn__;

$startDate = !empty($_REQUEST['startDate']) ? $_REQUEST['startDate'] : date("Y-m-d",strtotime("-1 month"));
$endDate = !empty($_REQUEST['endDate']) ? $_REQUEST['endDate'] : date("Y-m-d");
$mode = !empty($_REQUEST['mode']) ? $_REQUEST['mode']:null;
$PG_Param = "&mode=".$mode;

## 환경설정
define('_title_', '이벤트 진행 리스트');
define('_Menu_', 'event');

switch ($mode) {
    case 1:
        define('_subMenu_', 'event_info');
        $event_table = "ckd_game_event_point";
    break;
    case 2:
        define('_subMenu_', 'event_info2');
        $event_table = "ckd_game_event_point_2m";
    break;
    case 3:
        define('_subMenu_', 'event_info3');
        $event_table = "ckd_game_event_point_3m";
    break;
}

$where = "";
if(empty($_REQUEST['type'])){
    if(empty($_REQUEST['startDate'])){
        $type = 30;
    }else{
        $type = "";
    }
}else{
    $type = $_REQUEST['type'];
    define('_subMenu_', 'event_info5');
    $event_table = "ckd_game_zone_point";
    $where = " AND stats_dttm >= 20250512 AND reg_date > '2025-05-12' AND code_id = 11 ";
}

include_once __head__; ## html 헤더 출력

if($admin['level'] != 'super'){
	hist("권한이 없습니다.");
}

// 검색어
$key=empty($_REQUEST['key'])?"":$_REQUEST['key'];
$keyword=empty($_REQUEST['keyword'])?"":htmlspecialchars($_REQUEST['keyword']);
$point=empty($_REQUEST['point'])?"":$_REQUEST['point'];

// 검색어
if($keyword && $key=='user_id'){
    $where.=" AND (".$key." like '".trim($keyword)."%' or m.user_no='".$keyword."') ";
}else if($keyword){
    $where.=" AND ".$key." like '".trim($keyword)."%' ";
}
$PG_Param .= "&key=".$key."&keyword=".$keyword;

if($point == "5000"){
    $where.=" AND point='".$point."' ";
}
$PG_Param .= "&point=".$point."&type=".$type;

$today = date("Y-m-d");
$today1 = date("Y-m-01");

//$today = "2022-12-31";
//$today1 = "2022-12-01";;

switch($type){
	case '30'://최근 30일
		$startDate = date("Y-m-d",strtotime($today." -30 day"));
		$endDate = $today;
	break;
	case 'M'://이번달
		$startDate = $today1;
		$endDate = $today;
	break;
	case 'B1'://전월
		$startDate = date("Y-m-01", strtotime($today1." -1 month")); //지난달 1일
		$endDate = date("Y-m-t", strtotime($today1." -1 month")); //지난달 말일
	break;
	case 'B2'://전전월
		$startDate = date("Y-m-01", strtotime($today1." -2 month"));
		$endDate = date("Y-m-t", strtotime($today1." -2 month"));
	break;
	case '3M'://3개월
		$startDate = date("Y-m-01", strtotime($today1." -2 month"));
		$endDate = $today;
	break;
	case '6M'://6개월
		$startDate = date("Y-m-01", strtotime($today1." -5 month"));
		$endDate = $today;
	break;
}


$sql = "
    SELECT count(*) cnt FROM {$event_table}
    WHERE 1=1 {$where}
";
//pre($sql);
$total = $NDO->getData($sql);

include_once __page__;
$now = ret(INPUT_GET, 'np');
$paging->np = $now;
$paging->ps = 200;
$PG = $paging->init($total['cnt']);

$qry = "
    SELECT * FROM {$event_table}
    WHERE 1=1 {$where}
    ORDER BY spot_idx DESC
    LIMIT {$PG->first}, {$PG->size};
";
//pre($qry);
$result_list = $NDO->fetch_array($qry);

$sql = "
    SELECT * FROM ckd_game_event_set WHERE idx = '1'
";
//pre($sql);
$result = $NDO->getData($sql);
//pre($result);
?>
<div class="contentpanel">
	<div class="panel panel-default">
		<div class="panel-heading">

			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title">
				<div style="line-height: 40px;padding-right: 20px;"><?=_title_?></div>
			</h4>
		</div><!-- panel-heading -->
        <div class="panel-body">
            <form name="scform" method="get" action="">
            <input type="hidden" name="mode" value="<?=$mode?>">
            <input type="hidden" name="type" value="<?=$type?>">

                <div class="row">
                    <div class="card">
                        <div class="header">
                            <?php if(empty($mode)){ ?>
                            <div class="row" style="margin-left:0">
                                <div class="input-group call">
                                    <input type="text" class="form-control pull-left border-input databox" placeholder="yyyy-mm-dd" id="sdate" value="<?=$startDate?>" name="startDate" >
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                                <span class="pull-left space-in"> ~ </span>
                                <div class="input-group call">
                                    <input type="text" class="form-control pull-left border-input databox" placeholder="yyyy-mm-dd" id="edate" value="<?=$endDate?>" name="endDate" >
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>

                                <div style="display: inline-block;" class="pull-left">
										<span>
											<a class="btn btn-default <?=($type=='30')?'active':''?>" href="?type=30">최근30일</a>
											<a class="btn btn-default <?=($type=='M')?'active':''?>" href="?type=M">이번달</a>
											<a class="btn btn-default <?=($type=='B1')?'active':''?>" href="?type=B1">전월</a>
											<a class="btn btn-default <?=($type=='B2')?'active':''?>" href="?type=B2">전전월</a>
											<a class="btn btn-default <?=($type=='3M')?'active':''?>" href="?type=3M">3개월</a>
											<a class="btn btn-default <?=($type=='6M')?'active':''?>" href="?type=6M">6개월</a>
										</span>
                                </div>
                            </div>
                        <?php } ?>
                            <div class="row" style="margin-top:10px;">
                            <select class="form-control pull-left border-input" name="key" style="width:90px;margin-left:10px;height: 36px;">
                                <option value="user_uuid" <?=($key=='user_uuid')?'selected':''?>> 아이디 </option>
                            </select>
                            <input type="text" class="form-control pull-left border-input" name="keyword" style="width:380px;margin-left:10px;height: 36px;" value="<?=$keyword?>" placeholder="검색어" autocomplete="off" />
                            <label style="margin-top: 5px;cursor:pointer">
                                <input type="checkbox" name="point" style="width: 20px;margin-left:10px;height: 20px;" value="5000" <?=($point=='5000')?'checked':''?> />
                                <div style="float:right;margin: 5px;">5000포인트 당첨자만 표시</div>
                            </label>
                            <button class="btn btn-success" style="height: 36px;margin-top: -12px;">검 색</button>
                            <div style="display: inline-block;" class="pull-right">
                                <button class="btn btn-success" style="margin-right: 20px;" id="ExcelDown" onclick="return false">Excel 다운</button>
                            </div>
                            </div>
                        </div><!-- header -->
                    </div><!-- card -->
                </div><!-- row -->

            </form>
        <div class="row">
        <div class="col-xs-12 col-md-12">
        <div class="table-responsive" >

            <table class="table table-hover mb30 member_table" id="ocb">
                <caption style="text-align: left;">이벤트 참여자 리스트</caption>
                <thead>
                <tr>
                    <th width="100">번호</th>
                    <th width="100">고유값</th>
                    <th width="400">uuid</th>
                    <th>포인트</th>
                    <th>등록일</th>
                 </tr>
                </thead>
                <tbody>
                <?php if(empty($result_list)){ ?>
                    <td colspan="5">등록된 리스트가 없습니다</td>
                <?php }else{ foreach($result_list as $row){ ?>
                <tr>
                    <td><?=number_format($PG->first_num)?></td>
                    <td><?=$row['spot_idx']?></td>
                    <td><?=$row['user_uuid']?></td>
                    <td><?=$row['point']?></td>
                    <td><?=$row['reg_date']?></td>
                </tr>
                <?php
                    $PG->first_num--;
                    }
                }
                ?>
                </tbody>
            </table>

        <div class="row">
            <?=$paging->paging_new($PG,$PG_Param);?>
        </div><!-- row -->

        </div>
        </div>
        </div>
        </div><!-- panel-body -->

	</div><!-- panel panel-default -->
</div>

<script>
	$(document).ready(function(){

		//ON OFF 버튼 통합
		$(".use_YN").on("click",function(){
			var use_YN = $(this).data("otpc");
			var code_id = $(this).data("code_id");
			var formData = {mode:"use_YN", code_id:code_id, use_YN:use_YN};
			$.post("..<?=__self__?>",formData,function(result){
				if(result === "Y" || result === "N"){
					$("div[data-code_id="+code_id+"]").data("otpc",result);
					alert("적용 되었습니다.");
				}else{
					if(use_YN === "Y") {
						$(".use_YN[data-code_id='"+code_id+"']").children("button").eq(0).addClass("btn-primary").removeClass("btn-default").addClass("active");
						$(".use_YN[data-code_id='"+code_id+"']").children("button").eq(1).addClass("btn-default").removeClass("btn-primary").removeClass("active");
					}else{
						$(".use_YN[data-code_id='"+code_id+"']").children("button").eq(0).addClass("btn-default").removeClass("btn-primary").removeClass("active");
						$(".use_YN[data-code_id='"+code_id+"']").children("button").eq(1).addClass("btn-primary").removeClass("btn-default").addClass("active");
					}
					alert("수정 실패");
				}
			},"html");
		});

	});





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
        fnExcelReport('ocb','이벤트 당첨 리스트');
    });


</script>

<?php
include __foot__;
?>
