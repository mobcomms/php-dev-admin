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
if(!empty($_POST)){
	if($_POST['mode'] == "use_YN"){
		//pre($_POST);
		$use_YN = $_POST['use_YN']=='Y' ? 'N':'Y';
        $sql = "
            UPDATE ckd_game_event_set SET useYN='{$use_YN}'
            WHERE idx = '1'
        ";
        //pre($sql);
        $result = $NDO->sql_query($sql);
        if(!empty($result)){
            echo $use_YN;
        }
    }else{
        $sdate = $_POST['sdate'];
        $s_hour = $_POST['s_hour'];
        $s_min = $_POST['s_min'];
        $start_date = $sdate." ".$s_hour.":".$s_min.":00";

        $edate = $_POST['edate'];
        $e_hour = $_POST['e_hour'];
        $e_min = $_POST['e_min'];
        $end_date = $edate." ".$e_hour.":".$e_min.":00";

        $point = $_POST['point'];

        $sql = "
            UPDATE ckd_game_event_set SET start_date='{$start_date}', end_date='{$end_date}' , point='{$point}'
            WHERE idx = '1'
        ";
        $fn->replace($_SERVER['PHP_SELF']);
        $result = $NDO->sql_query($sql);
    }
    exit();
}
## 환경설정
define('_title_', '이벤트 설정');
define('_Menu_', 'event');
define('_subMenu_', 'event_set');

include_once __head__; ## html 헤더 출력

if($admin['level'] != 'super'){
	hist("권한이 없습니다.");
}
$sql = "
    SELECT * FROM ckd_game_event_set WHERE idx = '1'
";
//pre($sql);
$result = $NDO->getData($sql);
//pre($result);

$result['s_hour']= substr($result['start_date'], 11, 2);
$result['s_min'] = substr($result['start_date'], 14, 2);

$result['e_hour'] = substr($result['end_date'], 11, 2);
$result['e_min'] = substr($result['end_date'], 14, 2);

$result['start_date'] = ($result['start_date'] == "0000-00-00") ? "" : substr($result['start_date'], 0, 10);
$result['end_date'] = ($result['end_date'] == "0000-00-00") ? "" : substr($result['end_date'], 0, 10);

?>
<div class="contentpanel">
	<div class="panel panel-default">
		<div class="panel-heading">

			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title">
				<div style="float: left;line-height: 40px;padding-right: 20px;"><?=_title_?></div>
				<div class="btn-group btn-toggle use_YN" style="margin-bottom: 0;" data-otpc="<?=$result['useYN']?>" data-code_id="<?=$result['idx']?>">
					<button class="btn btn-xs <?=($result['useYN']!='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="Y" style="z-index: 0;">ON </button>
					<button class="btn btn-xs <?=($result['useYN']=='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="N" style="z-index: 0;">OFF </button>
				</div>
                <button class="btn btn-success save" style="margin-left:10px;">저장</button>
			</h4>
		</div><!-- panel-heading -->
        <div class="panel-body">
        <form method="post" name="frm" id="frm">
            <input type="hidden" name="mode" value="update">
            <div class="form-group">
                <div class="col-sm-1 control-label" style="width: 150px;line-height:40px; margin-right:10px;">
                    <span class="asterisk">*</span> 시작일시
                </div>
                <div class="pull-left input-group call  col-md-2" style="margin-right:20px;">
                    <input type="text" class="form-control pull-left border-input" placeholder="yyyy-mm-dd" id="boardSdate" value="<?=empty($result)?"":$result['start_date']?>" name="sdate" autocomplete="off" >
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
                <select class="form-control pull-left border-input" name="s_hour" style="width:100px;height: 40px;margin: 0">
                    <?php for($i=0;$i<24;$i++){?>
                        <option value="<?=$i<10 ? "0".$i : $i?>" <?=!empty($result) && ($result['s_hour']==$i)?'selected':''?>><?=$i<10 ? "0".$i : $i?>시</option>
                    <?php }?>
                </select>
                <select class="form-control pull-left border-input" name="s_min" style="width:100px;height: 40px;margin-left: 10px;">
                    <option value="00" <?=!empty($result) && ($result['s_min']=='00')?'selected':''?>> 00분 </option>
                    <option value="30" <?=!empty($result) && ($result['s_min']=='30')?'selected':''?>> 30분 </option>
                </select>
            </div>

            <div class="form-group">
                <div class="col-sm-1 control-label" style="width: 150px;line-height:40px; margin-right:10px;">
                    <span class="asterisk">*</span> 종료일시
                </div>
                <div class="pull-left input-group call  col-md-2" style="margin-right:20px;">
                    <input type="text" class="form-control pull-left border-input" placeholder="yyyy-mm-dd" id="boardEdate" value="<?=empty($result)?"":$result['end_date']?>" name="edate" autocomplete="off" >
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
                <select class="form-control pull-left border-input" name="e_hour" style="width:100px;height: 40px;margin: 0">
                    <?php for($i=0;$i<24;$i++){?>
                        <option value="<?=$i<10 ? "0".$i : $i?>" <?=!empty($result) && ($result['e_hour']==$i)?'selected':''?>><?=$i<10 ? "0".$i : $i?>시</option>
                    <?php }?>
                </select>
                <select class="form-control pull-left border-input" name="e_min" style="width:100px;height: 40px;margin-left: 10px;">
                    <option value="00" <?=!empty($result) && ($result['e_min']=='00')?'selected':''?>> 00분 </option>
                    <option value="30" <?=!empty($result) && ($result['e_min']=='30')?'selected':''?>> 30분 </option>
                </select>
            </div>

            <div class="form-group">
                <label class=" col-sm-1 control-label" style="width: 150px;line-height: 40px;"> <span class="asterisk">*</span> 적립 한계 포인트</label>
                <div class="col-sm-9">
                    <input type="text" name="point" class="form-control" style="width: 100px;float: left;" value="<?=$result['point']?>"/>
                </div>
            </div>
        </form>

        </div>
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
    $(".save").click(function (){
        $("#frm").submit();
    })
</script>

<?php
include __foot__;
?>
