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
		<h1>finnq-SDK : <font color="#1caf9a" size="3"> <?=strtoupper($admin['level'])?></font></h1>
	</div><!-- logopanel -->

	<div class="leftpanelinner">

		<h5 class="sidebartitle"></h5>
		<ul class="nav nav-pills nav-stacked nav-bracket">

			<li class="<?=_Menu_=='main'?'active':''?>"><a href="index.php"><i class="glyphicon glyphicon-copyright-mark"></i> <span>메인</span></a></li>
			<li class="<?=_Menu_=='manage'?'active':''?>"><a href="statistics_use.php"><i class="fa fa-krw"></i> <span>매출 통계</span></a></li>
			<li class="nav-parent <?=_Menu_=='reward'?'active nav-active':''?>"><a href="member_info.php"><i class="fa fa-product-hunt"></i> <span>리워드 관리</span></a>
				<ul class="children" style="display:<?=_Menu_=='reward'?'block':'none'?>;">
					<li class=" <?=_subMenu_=='save'?'active':''?>"><a href="statistics_save.php"><i class="fa fa-caret-right"></i>적립 통계</a></li>
					<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
					<li class=" <?=_subMenu_=='save_set'?'active':''?>"><a href="statistics_save_set.php"><i class="fa fa-caret-right"></i>적립 설정</a></li>
					<?php } ?>
				</ul>
			</li>
			<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
			<li class="<?=_Menu_=='user'?'active':''?>"><a href="member_list.php"><i class="glyphicon glyphicon-user"></i> <span>사용자 관리</span></a></li>
			<li class="nav-parent <?=_Menu_=='inquiry'?'active nav-active':''?>"><a href="layouts.html"><i class=" glyphicon glyphicon-picture"></i> <span>문의하기</span></a>
				<ul class="children" style="display:<?=_Menu_=='inquiry'?'block':'none'?>;">
					<li class=" <?=_subMenu_=='inquiry'?'active':''?>"><a href="board_inquiry.php"><i class="fa fa-caret-right"></i>문의하기</a></li>
					<li class=" <?=_subMenu_=='inquiry_ppz'?'active':''?>"><a href="board_inquiry.php?target=PPZ"><i class="fa fa-caret-right"></i>오퍼월 문의하기</a></li>
				</ul>
			</li>
			<?php } ?>
			<li class="nav-hover" style="position: fixed;top: 800px;width: 200px;"><a href="javascript:;" onclick="document.logoutform.submit();"><i class="glyphicon glyphicon-log-out"></i> Log Out</a></li>

		</ul>
	</div><!-- leftpanelinner -->
</div><!-- leftpanel -->
