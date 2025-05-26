jQuery(window).load(function() {

  // Page Preloader
  jQuery('#status').fadeOut();
  jQuery('#preloader').delay(200).fadeOut(function(){
     jQuery('body').delay(200).css({'overflow':'visible'});
  });
});

jQuery(document).ready(function() {

  // Toggle Left Menu
  jQuery('.leftpanel .nav-parent > a').live('click', function() {

     var parent = jQuery(this).parent();
     var sub = parent.find('> ul');

     // Dropdown works only when leftpanel is not collapsed
     if(!jQuery('body').hasClass('leftpanel-collapsed')) {
        if(sub.is(':visible')) {
           sub.slideUp(200, function(){
              parent.removeClass('nav-active');
              jQuery('.mainpanel').css({height: ''});
              adjustmainpanelheight();
           });
        } else {
           closeVisibleSubMenu();
           parent.addClass('nav-active');
           sub.slideDown(200, function(){
              adjustmainpanelheight();
           });
        }
     }
     return false;
  });

  function closeVisibleSubMenu() {
     jQuery('.leftpanel .nav-parent').each(function() {
        var t = jQuery(this);
        if(t.hasClass('nav-active')) {
           t.find('> ul').slideUp(200, function(){
              t.removeClass('nav-active');
           });
        }
     });
  }

  function adjustmainpanelheight() {
     // Adjust mainpanel height
     var docHeight = jQuery(document).height();
     if(docHeight > jQuery('.mainpanel').height())
        jQuery('.mainpanel').height(docHeight);
  }
  adjustmainpanelheight();


  // Tooltip
  jQuery('.tooltips').tooltip({ container: 'body'});

  // Popover
  jQuery('.popovers').popover();

  // Close Button in Panels
  jQuery('.panel .panel-close').click(function(){
     jQuery(this).closest('.panel').fadeOut(200);
     return false;
  });

  // Form Toggles
  jQuery('.toggle').toggles({on: true});

  jQuery('.toggle-chat1').toggles({on: false});

  // Sparkline
  jQuery('#sidebar-chart').sparkline([4,3,3,1,4,3,2,2,3,10,9,6], {
   type: 'bar',
   height:'30px',
     barColor: '#428BCA'
  });

  jQuery('#sidebar-chart2').sparkline([1,3,4,5,4,10,8,5,7,6,9,3], {
   type: 'bar',
   height:'30px',
     barColor: '#D9534F'
  });

  jQuery('#sidebar-chart3').sparkline([5,9,3,8,4,10,8,5,7,6,9,3], {
   type: 'bar',
   height:'30px',
     barColor: '#1CAF9A'
  });

  jQuery('#sidebar-chart4').sparkline([4,3,3,1,4,3,2,2,3,10,9,6], {
   type: 'bar',
   height:'30px',
     barColor: '#428BCA'
  });

  jQuery('#sidebar-chart5').sparkline([1,3,4,5,4,10,8,5,7,6,9,3], {
   type: 'bar',
   height:'30px',
     barColor: '#F0AD4E'
  });


  // Minimize Button in Panels
  jQuery('.minimize').click(function(){
     var t = jQuery(this);
     var p = t.closest('.panel');
     if(!jQuery(this).hasClass('maximize')) {
        p.find('.panel-body, .panel-footer').slideUp(200);
        t.addClass('maximize');
        t.html('&plus;');
     } else {
        p.find('.panel-body, .panel-footer').slideDown(200);
        t.removeClass('maximize');
        t.html('&minus;');
     }
     return false;
  });


  // Add class everytime a mouse pointer hover over it
  jQuery('.nav-bracket > li').hover(function(){
     jQuery(this).addClass('nav-hover');
  }, function(){
     jQuery(this).removeClass('nav-hover');
  });


  // Menu Toggle
  jQuery('.menutoggle').click(function(){

     var body = jQuery('body');
     var bodypos = body.css('position');

     if(bodypos != 'relative') {

        if(!body.hasClass('leftpanel-collapsed')) {
           body.addClass('leftpanel-collapsed');
           jQuery('.nav-bracket ul').attr('style','');

           jQuery(this).addClass('menu-collapsed');

        } else {
           body.removeClass('leftpanel-collapsed chat-view');
           jQuery('.nav-bracket li.active ul').css({display: 'block'});

           jQuery(this).removeClass('menu-collapsed');

        }
     } else {

        if(body.hasClass('leftpanel-show'))
           body.removeClass('leftpanel-show');
        else
           body.addClass('leftpanel-show');

        adjustmainpanelheight();
     }

  });

  // Chat View
  jQuery('#chatview').click(function(){

     var body = jQuery('body');
     var bodypos = body.css('position');

     if(bodypos != 'relative') {

        if(!body.hasClass('chat-view')) {
           body.addClass('leftpanel-collapsed chat-view');
           jQuery('.nav-bracket ul').attr('style','');

        } else {

           body.removeClass('chat-view');

           if(!jQuery('.menutoggle').hasClass('menu-collapsed')) {
              jQuery('body').removeClass('leftpanel-collapsed');
              jQuery('.nav-bracket li.active ul').css({display: 'block'});
           } else {

           }
        }

     } else {

        if(!body.hasClass('chat-relative-view')) {

           body.addClass('chat-relative-view');
           body.css({left: ''});

        } else {
           body.removeClass('chat-relative-view');
        }
     }

  });

  reposition_topnav();
  reposition_searchform();

  jQuery(window).resize(function(){

     if(jQuery('body').css('position') == 'relative') {

        jQuery('body').removeClass('leftpanel-collapsed chat-view');

     } else {

        jQuery('body').removeClass('chat-relative-view');
        jQuery('body').css({left: '', marginRight: ''});
     }

     reposition_searchform();
     reposition_topnav();

  });



  /* This function will reposition search form to the left panel when viewed
   * in screens smaller than 767px and will return to top when viewed higher
   * than 767px
   */
  function reposition_searchform() {
     if(jQuery('.searchform').css('position') == 'relative') {
        jQuery('.searchform').insertBefore('.leftpanelinner .userlogged');
     } else {
        jQuery('.searchform').insertBefore('.header-right');
     }
  }



  /* This function allows top navigation menu to move to left navigation menu
   * when viewed in screens lower than 1024px and will move it back when viewed
   * higher than 1024px
   */
  function reposition_topnav() {
     if(jQuery('.nav-horizontal').length > 0) {

        // top navigation move to left nav
        // .nav-horizontal will set position to relative when viewed in screen below 1024
        if(jQuery('.nav-horizontal').css('position') == 'relative') {

           if(jQuery('.leftpanel .nav-bracket').length == 2) {
              jQuery('.nav-horizontal').insertAfter('.nav-bracket:eq(1)');
           } else {
              // only add to bottom if .nav-horizontal is not yet in the left panel
              if(jQuery('.leftpanel .nav-horizontal').length == 0)
                 jQuery('.nav-horizontal').appendTo('.leftpanelinner');
           }

           jQuery('.nav-horizontal').css({display: 'block'})
                                 .addClass('nav-pills nav-stacked nav-bracket');

           jQuery('.nav-horizontal .children').removeClass('dropdown-menu');
           jQuery('.nav-horizontal > li').each(function() {

              jQuery(this).removeClass('open');
              jQuery(this).find('a').removeAttr('class');
              jQuery(this).find('a').removeAttr('data-toggle');

           });

           if(jQuery('.nav-horizontal li:last-child').has('form')) {
              jQuery('.nav-horizontal li:last-child form').addClass('searchform').appendTo('.topnav');
              jQuery('.nav-horizontal li:last-child').hide();
           }

        } else {
           // move nav only when .nav-horizontal is currently from leftpanel
           // that is viewed from screen size above 1024
           if(jQuery('.leftpanel .nav-horizontal').length > 0) {

              jQuery('.nav-horizontal').removeClass('nav-pills nav-stacked nav-bracket')
                                       .appendTo('.topnav');
              jQuery('.nav-horizontal .children').addClass('dropdown-menu').removeAttr('style');
              jQuery('.nav-horizontal li:last-child').show();
              jQuery('.searchform').removeClass('searchform').appendTo('.nav-horizontal li:last-child .dropdown-menu');
              jQuery('.nav-horizontal > li > a').each(function() {

                 jQuery(this).parent().removeClass('nav-active');

                 if(jQuery(this).parent().find('.dropdown-menu').length > 0) {
                    jQuery(this).attr('class','dropdown-toggle');
                    jQuery(this).attr('data-toggle','dropdown');
                 }

              });
           }

        }

     }
  }


  // Sticky Header
  if(jQuery.cookie('sticky-header'))
     jQuery('body').addClass('stickyheader');

  // Sticky Left Panel
  if(jQuery.cookie('sticky-leftpanel')) {
     jQuery('body').addClass('stickyheader');
     jQuery('.leftpanel').addClass('sticky-leftpanel');
  }

  // Left Panel Collapsed
  if(jQuery.cookie('leftpanel-collapsed')) {
     jQuery('body').addClass('leftpanel-collapsed');
     jQuery('.menutoggle').addClass('menu-collapsed');
  }

  // Changing Skin
  var c = jQuery.cookie('change-skin');
  if(c) {
     jQuery('head').append('<link id="skinswitch" rel="stylesheet" href="css/style.'+c+'.css" />');
  }

  // Changing Font
  var fnt = jQuery.cookie('change-font');
  if(fnt) {
     jQuery('head').append('<link id="fontswitch" rel="stylesheet" href="css/font.'+fnt+'.css" />');
  }

  // Check if leftpanel is collapsed
  if(jQuery('body').hasClass('leftpanel-collapsed'))
     jQuery('.nav-bracket .children').css({display: ''});


  // Handles form inside of dropdown
  jQuery('.dropdown-menu').find('form').click(function (e) {
     e.stopPropagation();
   });


});


(function ($, window) {

   $.fn.contextMenu = function (settings) {

       return this.each(function () {

           // Open context menu
           $(this).on("contextmenu", function (e) {
               // return native menu if pressing control
               if (e.ctrlKey) return;

               var user_no=$(this).data("id");
               $("#user_no").val(user_no);

               //open menu
               var $menu = $(settings.menuSelector)
                   .data("invokedOn", $(e.target))
                   .show()
                   .css({
                       position: "absolute",
                       left: getMenuPosition(e.clientX, 'width', 'scrollLeft'),
                       top: getMenuPosition(e.clientY, 'height', 'scrollTop')
                   })
                   .off('click')
                   .on('click', 'a', function (e) {
                       $menu.hide();

                       var $invokedOn = $menu.data("invokedOn");
                       var $selectedMenu = $(e.target);

                       settings.menuSelected.call(this, $invokedOn, $selectedMenu);
                   });

               return false;
           });

           //make sure menu closes on any click
           $('body').click(function () {
               $(settings.menuSelector).hide();
           });
       });

       function getMenuPosition(mouse, direction, scrollDir) {
           var win = $(window)[direction](),
               scroll = $(window)[scrollDir](),
               menu = $(settings.menuSelector)[direction](),
               position = mouse + scroll;

           // opening menu would pass the side of the page
           if (mouse + menu > win && menu < mouse)
               position -= menu;

           return position;
       }

   };
})(jQuery, window);

$(".table td.member-id").contextMenu({
   menuSelector: "#contextMenu",
   menuSelected: function (invokedOn, selectedMenu) {
       var msg = "You selected the menu item '" + selectedMenu.text() +
           "' on the value '" + invokedOn.text() + "'";
   }
});

// 회원아이디 오른쪽 클릭 메뉴 링크
function popSelMenu(menuMode){
  var user_no=$("#user_no").val();
  switch(menuMode){
     case "memInfo" :
         popUserInfo(user_no)
         break;

     case "addHistory" :
         popUserHistory(user_no);
         break;

     case "useStore" :
         popUserStore(user_no);
         break;

     case "smsSend" :
         popUserSMS(user_no);
         break;

     case "mailSend" :
         popUserMail(user_no);
         break;

     default:
         break;
  }
}


// 회원 적립내역
function popUserHistory(user_no){
    window.open('member_accumulate.html?user_no='+user_no,'적립내역','width=800, height=700, toolbar=no, menubar=no, scrollbars=no, resizable=yes');
}
// 회원 회원정보
function popUserInfo(user_no){
    window.open('member_detail.html?user_no='+user_no,'회원정보_'+user_no,'width=800, height=700, toolbar=no, menubar=no, scrollbars=no, resizable=yes');
}
// 회원 친구현황
function popUserFriend(user_no){
    window.open('member_Friend_status.html?user_no='+user_no,'친구현황','width=1200, height=750, toolbar=no, menubar=no, scrollbars=no, resizable=yes');
}
// 회원 회원등록/수정
function popUserMember(user_no){
    window.open('member_Add.html?user_no='+user_no,'회원등록/수정','width=800, height=800, toolbar=no, menubar=no, scrollbars=no, resizable=yes');
}
// 메일보내기
function popUserMail(user_no){
  if(user_no=='363') window.open('member_Send_Mail.html?user_no='+user_no,'메일전송','width=800, height=700, toolbar=no, menubar=no, scrollbars=no, resizable=yes');
   else alert('준비중입니다.');

}
// 스토어 내역
function popUserStore(user_no){
  if(user_no=='363') window.open('member_store.html?user_no='+user_no,'스토어내역','width=800, height=700, toolbar=no, menubar=no, scrollbars=no, resizable=yes');
   else alert('준비중입니다.');

}
// 문자보내기
function popUserSMS(user_no){
   if(user_no=='363') window.open('member_Send_SMS.html?user_no='+user_no,'문자전송','width=800, height=700, toolbar=no, menubar=no, scrollbars=no, resizable=yes');
   else alert('준비중입니다.');
}


/* 테마 순서 변경 [S] */
function theme_sort(el,idx,state,now,movie){

 option=state;
 var formData = {mode:"themeSort",option:option,idx:idx};
 $.ajax({
   url:'./ajax/ajax_process.php',
   type: 'POST',
   data: formData,
   success:function(data){

     if(data=='S'){

       if(movie=='M'){
         if(state=='UP'){
           now=now-1;
           location.href='theme.html?np='+now;
         }
         if(state=='DN'){
           now=(now*1)+1;
           location.href='theme.html?np='+now;
         }
       }else{
         if(state=='UP'){
           var $tr = $(el).parent().parent(); // 클릭한 버튼이 속한 tr 요소
           $tr.prev().before($tr); // 현재 tr 의 이전 tr 앞에 선택한 tr 넣기
         }
         if(state=='DN'){
           var $tr = $(el).parent().parent(); // 클릭한 버튼이 속한 tr 요소
           $tr.next().after($tr); // 현재 tr 의 다음 tr 뒤에 선택한 tr 넣기
         }
       }
     }else{
       alert(data);
     }
   }
 });

}

/* 게시판 관리버튼 기능 [S] */
function mng(idx){

 mType=$('#mng'+idx).attr("data-otpc");
 var formData = {mode:"boardMng",mType:mType,idx:idx};
 $.ajax({
   url:'./ajax/ajax_process.php',
   type: 'POST',
   data: formData,
   success:function(data){
     $('#mng'+idx).attr("data-otpc",data);
     if(data=='Y') $('.mng_'+idx).removeClass("mngOff");
     else $('.mng_'+idx).addClass("mngOff");
   }
 });
}


// 퀵링크 관리버튼
function qlink_mng(idx){
 mType=$('#mng'+idx).attr("data-otpc");
 var formData = {mode:"qlinkMng",mType:mType,idx:idx};
 $.ajax({
   url:'./ajax/ajax_process.php',
   type: 'POST',
   data: formData,
   success:function(data){

     $('#mng'+idx).attr("data-otpc",data);
   }
 });
}



// 게시판 관리버튼
function switch_mng(idx){

    var mType=$('#mng'+idx).attr("data-otpc");
    if(mType == "N"){
        if(!confirm('해당 광고를 노출하시겠습니까?')){
            $("#mng"+idx+ " > button").eq(0).addClass("btn-primary").removeClass("btn-default").addClass("active");
            $("#mng"+idx+ " > button").eq(1).addClass("btn-default").removeClass("btn-primary").removeClass("active");
            return;
        }
    }else{
		if(!confirm('해당 광고를 노출을 종료하시겠습니까?')) {
			$("#mng"+idx+ " > button").eq(0).addClass("btn-default").removeClass("btn-primary").removeClass("active");
			$("#mng"+idx+ " > button").eq(1).addClass("btn-primary").removeClass("btn-default").addClass("active");
			return;
		}
    }
    var formData = {mode:"board_switch",mType:mType,idx:idx};
    $.ajax({
        url:'./ajax/ajax_process.php',
        type: 'POST',
        data: formData,
        success:function(data){
            $('#mng'+idx).attr("data-otpc",data);
        }
    });
}

// 팝업 관리버튼
function popup_mng(idx){
 mType=$('#mng'+idx).attr("data-otpc");
 var formData = {mode:"popupMng",mType:mType,idx:idx};
 $.ajax({
   url:'./ajax/ajax_process.php',
   type: 'POST',
   data: formData,
   success:function(data){

     $('#mng'+idx).attr("data-otpc",data);
   }
 });
}


// 충전소 적립요청  처리상황
function reward_requestStatus(idx,gubun,str,color){
 if(!confirm('상태값을 변경합니다.')) {
   $("#state"+idx+" option:eq(0)").attr("selected", "selected");
   $("#state"+idx).css("color", color);
   return;
 }
 var formData = {mode:"RequestStatus",gubun:gubun,str:str,idx:idx};
 $.ajax({
   url:'./ajax/ajax_process.php',
   type: 'POST',
   data: formData,
   success:function(data){
   //  alert(data);
     code=data.split('||');
     if(code[0]=='E') alert(code[1]);
   }
 });
}




// 쿠폰 사용처리
function coupon_status(idx,tr_id,gubun){
 var txt='';

 if(gubun=='H'){
    txt="환불처리";
    txt2="\n회원에게 포인트가 환불됩니다.";
 }
 if(gubun=='C'){
    txt="쿠폰회수";
    txt2="\n쿠폰 회수는 회원에게 포인트가 환불되지 않습니다.";
 }

 if(gubun){
   msg='상태값을 '+txt+'로 변경합니다. 변경후 복구는 불가능합니다.'+txt2;
   if(!confirm(msg)) {
     $("#status"+idx+" option:eq(0)").attr("selected", "selected");
     return;
   }
 }


 var formData = {mode:"CouponStatus",gubun:gubun,tr_id:tr_id,idx:idx};
 $.ajax({
   url:'./ajax/ajax_process.php',
   type: 'POST',
   data: formData,
   success:function(data){
    //alert(data);
     code=data.split('|');
     if(code[0]=='E') alert(code[1]);
     else if(code[0]=='S'){
       $("#statusState_"+idx).html(code[1]);
       $("#statusContent_"+idx).html(code[2]);
       if(gubun) $("#statusMng_"+idx).html('');
     }
   }
 });
}



// 샵트리 주문상태처리
function order_status(ordernum,status,CASHKEYBOARD_ENV){

   if(!status) return;

   msg='주문상태를 '+status+'(으)로 변경 하시겠습니까?';
   if(!confirm(msg)) return;

   var formData = {mode:"OrderStatus",ordernum:ordernum,status:status,CASHKEYBOARD_ENV:CASHKEYBOARD_ENV};
   $.ajax({
       url:'./ajax/ajax_process.php',
       type: 'POST',
       data: formData,
       success:function(data){
           alert(data);
           code=data.split('|');
           if(code[0]=='E') alert(code[1]);
           else if(code[0]=='S'){
               $("#statusState_"+ordernum).html(code[2]);
               $("#statusMng_"+ordernum).html(code[3]);
              // location.reload();
           }
       }
   });
}



// 휴대번호 추가
$("#regPhone").click(function(){

   var phone=$("#inputPhone").val();
   phone=phone.replace(/[^0-9]/g,"");
   if(!phone) {
       alert('휴대폰 번호가 없습니다.');
       return;
   }
   console.log(phone);
   var formData = {mode:"SecessionPhoneInsert",phone:phone};
   $.ajax({
       url:'./ajax/ajax_process.php',
       type: 'POST',
       data: formData,
       success:function(data){
           var code;
           console.log(data);
           code=data.split('|');
           if(code[0]=='E') alert(code[1]);
           else if(code[0]=='S'){

               Date.prototype.format = function(f) {
                   if (!this.valueOf()) return " ";

                   var weekName = ["일요일", "월요일", "화요일", "수요일", "목요일", "금요일", "토요일"];
                   var d = this;

                   return f.replace(/(yyyy|yy|MM|dd|E|hh|mm|ss|a\/p)/gi, function($1) {
                       switch ($1) {
                           case "yyyy": return d.getFullYear();
                           case "yy": return (d.getFullYear() % 1000).zf(2);
                           case "MM": return (d.getMonth() + 1).zf(2);
                           case "dd": return d.getDate().zf(2);
                           case "E": return weekName[d.getDay()];
                           case "HH": return d.getHours().zf(2);
                           case "hh": return ((h = d.getHours() % 12) ? h : 12).zf(2);
                           case "mm": return d.getMinutes().zf(2);
                           case "ss": return d.getSeconds().zf(2);
                           case "a/p": return d.getHours() < 12 ? "오전" : "오후";
                           default: return $1;
                       }
                   });
               };

               String.prototype.string = function(len){var s = '', i = 0; while (i++ < len) { s += this; } return s;};
               String.prototype.zf = function(len){return "0".string(len - this.length) + this;};
               Number.prototype.zf = function(len){return this.toString().zf(len);};



               var date = new Date().format("yyyy-MM-dd HH:mm:ss");


               var html;
               html='<tr id="idx_'+code[1]+'">\n' +
                   '    <td>'+date+'</td>\n' +
                   '    <td>'+phone+'</td>\n' +
                   '    <td><button class="btn btn-primary s_btn" type="button" onclick="javascript:secessionPhoneDel('+code[1]+');">삭제</button></td>\n' +
                   '</tr>';

               $('#secessionTable > tbody:last').append(html);
              // location.reload();
           }
       }
   })
});

// ip번호 추가
$("#regIp").click(function(){

   var ip=$("#inputIp").val();

   ip=ip.replace(/[^0-9\\.]/gm,"");

   if(!ip) {
       alert('입력된 ip 를 확인해 주세요.');
       return;
   }


   var formData = {mode:"IpAddressInsert",ip:ip};
   $.ajax({
       url:'./ajax/ajax_process.php',
       type: 'POST',
       data: formData,
       success:function(data){
           var code;
           console.log(data);
           code=data.split('|');
           if(code[0]=='E') alert(code[1]);
           else if(code[0]=='S'){

               location.reload();

           }
       }
   })
});


// 휴대번호 삭제
function secessionPhoneDel(idx){

   if(!idx) {
       alert('선택한 휴대폰 번호가 없습니다.');
       return;
   }
   if(!confirm('해당 휴대번호를 삭제(제한 철회) 하시겠습니까?')) return;

   var formData = {mode:"SecessionPhoneDel",idx:idx};
   $.ajax({
       url:'./ajax/ajax_process.php',
       type: 'POST',
       data: formData,
       success:function(data){
           //console.log(data);
           code=data.split('|');
           if(code[0]=='E') alert(code[1]);
           else if(code[0]=='S'){
               $("#idx_"+idx).remove();
              // location.reload();
           }
       }
   });
}

// 휴대번호 삭제
function IpAddressDel(user_no){

   if(!user_no) {
       alert('선택한 회원 번호가 없습니다.');
       return;
   }
   if(!confirm('해당 회원을 삭제(제한 철회) 하시겠습니까?')) return;

   var formData = {mode:"IpAddressDel",user_no:user_no};
   $.ajax({
       url:'./ajax/ajax_process.php',
       type: 'POST',
       data: formData,
       success:function(data){
           //console.log(data);
           code=data.split('|');
           if(code[0]=='E') alert(code[1]);
           else if(code[0]=='S'){
                location.reload();
           }
       }
   });
}



// 포인트 충전
function pointSet(userno){

   var setPoint=$("#setPoint").val();
   var pointType=$("#pointType").val();
   var pointMemo=$("#pointMemo").val();

   if(!userno){
       alert('회원정보가 없습니다.');
       return false;
   }
   if(!userno || !setPoint){
       alert('포인트를 입력해주세요.');
       return false;
   }

   if(!confirm('포인트를 충전하시겠습니까?')) return false;
   var formData = {mode:"poinSet",user_no:userno,point:setPoint,type:pointType,pointMemo:pointMemo};
   $.ajax({
       url:'./ajax/ajax_process.php',
       type: 'POST',
       data: formData,
       success:function(data){
           //alert(data);
           if(data=='E'){
               alert('포인트 적립중에 오류가 발생했습니다.');
           }
           else{
               alert(setPoint+'포인트를 적립했습니다.');
               location.reload();
           }
       }
   });

}

jQuery(document).ready(function(){
   //최상단 체크박스 클릭
   $(".checkall").click(function(){
       //클릭되었으면
       if($(".checkall").prop("checked")){
           //input태그의 name이 chk인 태그들을 찾아서 checked옵션을 true로 정의
           $("input[name=chk]").prop("checked",true);
           //클릭이 안되있으면
       }else{
           //input태그의 name이 chk인 태그들을 찾아서 checked옵션을 false로 정의
           $("input[name=chk]").prop("checked",false);
       }
   })

   $('.btn-toggle').click(function() {
   $(this).find('.btn').toggleClass('active');

   if ($(this).find('.btn-primary').size()>0) {
     $(this).find('.btn').toggleClass('btn-primary');
   }
   if ($(this).find('.btn-danger').size()>0) {
     $(this).find('.btn').toggleClass('btn-danger');
   }
   if ($(this).find('.btn-success').size()>0) {
     $(this).find('.btn').toggleClass('btn-success');
   }
   if ($(this).find('.btn-info').size()>0) {
     $(this).find('.btn').toggleClass('btn-info');
   }

   $(this).find('.btn').toggleClass('btn-default');

 });


});
/* 게시판 관리버튼 기능 [E] */

// 체크박스 : 선택된 값 배쳘처리
function __checkbox_value_list(field){
   var idx = [];
   $("." + field ).each(function(){
       if($(this).is(':checked')){
           idx.push($(this).val());
       }
   });
   return idx;
}

$.datepicker.regional["ko"] = {
    closeText: "닫기",
    prevText: "이전달",
    nextText: "다음달",
    currentText: "오늘",
    monthNames: ["1월(JAN)","2월(FEB)","3월(MAR)","4월(APR)","5월(MAY)","6월(JUN)", "7월(JUL)","8월(AUG)","9월(SEP)","10월(OCT)","11월(NOV)","12월(DEC)"],
    monthNamesShort: ["1월","2월","3월","4월","5월","6월", "7월","8월","9월","10월","11월","12월"],
    dayNames: ["일","월","화","수","목","금","토"],
    dayNamesShort: ["일","월","화","수","목","금","토"],
    dayNamesMin: ["일","월","화","수","목","금","토"],
    weekHeader: "Wk",
    dateFormat: "yymmdd",
    firstDay: 0,
    isRTL: false,
    showMonthAfterYear: true,
    yearSuffix: ""
};
$.datepicker.setDefaults($.datepicker.regional["ko"]);

// datepicker
$(document).ready(function() {
 $(function() {$( "#sdate, #edate" ).datepicker({dateFormat: 'yy-mm-dd', maxDate:''});});
});

// 게시판 기간설정
$(document).ready(function() {
 $(function() {$("#boardSdate, #boardEdate" ).datepicker({dateFormat: 'yy-mm-dd'});});
});


// 날자 설정
function SetDate(O, S, E, type, sub){
 var n = new Date();
 y=n.getFullYear();
 m=n.getMonth()+1;
 d=n.getDate();
 switch(O){
   case 'Y':
     n.setFullYear(y,m-1,d-1);
     break;
   case 'T':
     n.setFullYear(y,m-1,d);
     break;
   case '7':
     n.setFullYear(y,m-1,d-7);
     break;
   case '30':
     n.setFullYear(y,m-1,d-30);
     break;
   case 'L':
     n.setFullYear(y,m-2,d);
     break;
   case 'M':
     n.setFullYear(y,m-1,d);
     break;
   case 'B1':
     n.setFullYear(y,m-2,d);
     break;
   case 'B2':
     n.setFullYear(y,m-3,d);
     break;
   case 'B12':
     n.setFullYear(y,m-12,d);
     break;
   case '3M':
     n.setFullYear(y,m-3,d);
     break;
   case '6M':
     n.setFullYear(y,m-6,d);
     break;
   case '9M':
     n.setFullYear(y,m-9,d);
     break;
   case '12M':
     n.setFullYear(y,m-12,d);
     break;
   case 'ALL':
     n.setFullYear(y,m,d);
     break;
   case 'YY':
     n.setFullYear(y,m,d);
     break;
   case 'Y1':
     n.setFullYear(y-1,m,d);
     break;
 }
 var yy=n.getFullYear();
 var mm=n.getMonth()+1;
 if(mm<10) mm="0"+mm;
 if(m<10) m="0"+m;
 var dd=n.getDate();
 if(dd<10) dd="0"+dd;
 if(d<10) d="0"+d;

 switch(O){
   default:
     var sdate=yy+'-'+mm+'-'+dd;
     var edate=y+'-'+m+'-'+d;
     $("#"+S).val(sdate);
     $("#"+E).val(edate);
     break;
   case 'Y':
     var sdate=yy+'-'+mm+'-'+dd;
     var edate=yy+'-'+mm+'-'+dd;
     $("#"+S).val(sdate);
     $("#"+E).val(edate);
     break;
   case 'L':
     var last=(new Date(yy,mm,0)).getDate();
     var sdate=yy+'-'+mm+'-01';
     var edate=yy+'-'+mm+'-'+last;
     $("#"+S).val(sdate);
     $("#"+E).val(edate);
     break;
   case 'M':
     var last=(new Date(y,m,0)).getDate();
     if(last<10) last="0"+last;
     var sdate=yy+'-'+mm+'-01';
     var edate=y+'-'+m+'-'+last;
     $("#"+S).val(sdate);
     $("#"+E).val(edate);
     break;
   case 'B1':
   case 'B2':
     var last=(new Date(y,mm,0)).getDate();
     if(last<10) last="0"+last;
     var sdate=yy+'-'+mm+'-01';
     var edate=yy+'-'+mm+'-'+last;
     $("#"+S).val(sdate);
     $("#"+E).val(edate);
     break;
   case 'B12':
     var sdate=(yy-1)+'-'+m;
     var edate=y+'-'+m;
     $("#"+S).val(sdate);
     $("#"+E).val(edate);
     break;
   case '3M':
   case '6M':
   case '9M':
   case '12M':
     var last=(new Date(y,m,d)).getDate();
     if(last<10) last="0"+last;
     var sdate=yy+'-'+mm+'-01';
     var edate=y+'-'+m+'-'+last;
     $("#"+S).val(sdate);
     $("#"+E).val(edate);
     break;
   case 'YY':
     var sdate=y+'-01';
     var edate=y+'-12';
     $("#"+S).val(sdate);
     $("#"+E).val(edate);
     break;
   case 'Y1':
     var sdate=(yy-1)+'-01';
     var edate=(y-1)+'-12';
     $("#"+S).val(sdate);
     $("#"+E).val(edate);
     break;
   case 'ALL':
     var last=(new Date(y,m,0)).getDate();
     if(last<10) last="0"+last;
     var sdate='2017-10-01';
     var edate=y+'-'+m+'-'+d;
     $("#"+S).val(sdate);
     $("#"+E).val(edate);
     break;
 }

 // option : 리포트에서 클릭시 리포트로 이동 기능
 if(type){
   report(type,sub,sdate,edate);
 }



}