<?php
/**********************
 *
 *    통계 페이지 / 일자별 통계
 *
 **********************/
include "./var.php";
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성


## 환경설정
define('_title_', '매체별 유니크 유저수');
define('_Menu_', 'manage');
define('_subMenu_', 'limit');

include_once __head__;
if($admin['id'] != 'mango'){
    exit;
}
$startDate = !empty($_REQUEST['startDate']) ? $_REQUEST['startDate'] : date("Y-m-d",strtotime("-1 month"));
$endDate = !empty($_REQUEST['endDate']) ? $_REQUEST['endDate'] : date("Y-m-d");

$sdate=str_replace("-","",$startDate);
$edate=str_replace("-","",$endDate);

//통계 데이터
$sql="
    SELECT * FROM cashkeyboard.chk_day_unique_user
    WHERE stats_dttm BETWEEN :sdate AND :edate 
    ORDER BY stats_dttm DESC, company_code ASC
";
//pre($sql);
$ret = $NDO->fetch_array($sql,[':sdate'=>$sdate,':edate'=>$edate]);
//pre($ret);

$html = '';
$TOTAL = [];

if(!empty($ret)) {
    foreach ($ret as $row) {
        $html .= "
			<tr class='".$fn->dateColor($row['stats_dttm'])."'>
				<td>{$row['stats_dttm']}</td>
				<td>{$row['company_code']}</td>
				<td>".number_format($row['sum_cnt'])."</td>
			 </tr>
		";
    }

}else{
    $html='<tr><td colspan="9">데이터가 없습니다.</td></tr>';
}

?>
<div class="contentpanel">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-btns">
                <!--a href="" class="panel-close">&times;</a-->
                <a href="" class="minimize">&minus;</a>
            </div><!-- panel-btns -->
            <h4 class="panel-title"><?=_title_?></h4>
        </div><!-- panel-heading -->

        <div class="panel-body">
            <form name="scform" method="get" action="">
                <div class="row">
                    <div class="card">
                        <div class="header">
                            <input type="hidden" name="rep" value="<?=_rep_;?>" />
                            <div class="input-group call">
                                <input type="text" class="form-control pull-left border-input databox" placeholder="yyyy-mm-dd" id="sdate" value="<?=$startDate?>" name="startDate" >
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                            <span class="pull-left space-in"> ~ </span>
                            <div class="input-group call">
                                <input type="text" class="form-control pull-left border-input databox" placeholder="yyyy-mm-dd" id="edate" value="<?=$endDate?>" name="endDate" >
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                            <button class="btn btn-success" style="margin-left:10px;">검 색</button>

                            <div style="display: inline-block;" class="pull-right">
                                <button class="btn btn-success" style="margin-left:10px;" id="ExcelDown" onclick="return false">Excel 다운</button>
                            </div>
                        </div><!-- header -->
                    </div><!-- card -->
                </div><!-- row -->
            </form>

            <div class="row member_table .col-xs-12 .col-md-12" style="margin-top:20px">
                <div class="table-responsive">
                    <table class="table table-hover mb30" id="ocb" style="border:1px solid #b0b0b0;">
                        <thead>
                        <tr>
                            <th>날짜</th>
                            <th>매체</th>
                            <th>유니크 유저수</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?=$html?>
                        </tbody>
                    </table>

                </div><!-- table-responsive -->
            </div><!-- row -->
        </div><!-- panel-body -->
    </div><!-- panel -->
</div><!-- contentpanel -->

<?php
include __foot__;
?>

<script>
    	function fnExcelReport(id, title) {
		var tab_text = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
		tab_text = tab_text + '<head><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">';
		tab_text = tab_text + '<xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>'
		tab_text = tab_text + '<x:Name>Sheet1</x:Name>';
		tab_text = tab_text + '<x:WorksheetOptions><x:Panes></x:Panes></x:WorksheetOptions></x:ExcelWorksheet>';
		tab_text = tab_text + '</x:ExcelWorksheets></x:ExcelWorkbook></xml></head><body>';
		tab_text = tab_text + '<table border="1px"">';
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
        fnExcelReport('ocb','유니크유저수');
    });
</script>
