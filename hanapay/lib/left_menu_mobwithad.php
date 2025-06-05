<?php
/**********************************
 *
 *   Level 등급
 *    - super : 최고관리자
 *    - develop : 개발팀
 *    - sysop : 운영팀
 *    - public : 일반관리자
 *
 ***********************************/
?>
<div class="leftpanel">

    <div class="logopanel">
        <h1>hanapay-SDK : <font color="#1caf9a" size="3"> <?=strtoupper($admin['level'])?></font></h1>
    </div><!-- logopanel -->

    <div class="leftpanelinner">

        <h5 class="sidebartitle"></h5>
        <ul class="nav nav-pills nav-stacked nav-bracket">
            <li class="<?=_Menu_=='main'?'active':''?>"><a href="index.php"><i class="glyphicon glyphicon-copyright-mark"></i> <span>메인</span></a></li>
            <li class="<?=_Menu_=='manage'?'active':''?>"><a href="statistics_use.php"><i class="fa fa-krw"></i> <span>머니박스 통계</span></a></li>
            <li class="<?=_Menu_=='reward'?'active':''?>"><a href="statistics_save.php"><i class="fa fa-product-hunt"></i> <span> 포인트 적립 통계</span></a></li>

            <li class="nav-hover" style="position: fixed;top: 800px;"><a href="javascript:void(0)" onclick="document.logoutform.submit();"><i class="glyphicon glyphicon-log-out"></i> Log Out</a></li>
        </ul>
    </div><!-- leftpanelinner -->
</div><!-- leftpanel -->
