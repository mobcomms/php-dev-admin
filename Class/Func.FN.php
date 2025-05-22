<?php

function Alert($msg) {echo '<script>alert("'.$msg.'");</script>';}
function Loca($url, $msg=false) {if(!empty($msg)) {Alert($msg);} echo '<script language="javascript">document.location.href="'.$url.'";</script>';}
function Replace($url, $msg=false) {if(!empty($msg)) {Alert($msg);} echo '<script language="javascript">document.location.replace("'.$url.'");</script>';}
function Hist($msg=false, $go=false) {if(!empty($msg)){Alert($msg);}$go=($go!=false)?$go:'-1';echo '<script>history.go("'.$go.'");</script>';}
function SLASH($V,$E=false) {if($E === false) {$V = addslashes($V);$V = htmlspecialchars($V);} else if ($E === true) {$V = stripslashes($V);$V = htmlspecialchars_decode($V);}return $V;}
function BASE($I, $R='', $V='') {$ret = ($R==='') ? urlencode(base64_encode(urlencode($I))) : urldecode(base64_decode(urldecode($I)));if($V==='') return $ret;else echo $ret;}
function CutStr($str,$len,$suf='...') {if(strlen($str)<=$len) return $str;$cpos=$len-1;$count_2B=0;$lastchar=$str[$cpos];while(ord($lastchar)>127 && $cpos>=0) {$count_2B++;$cpos--;$lastchar=$str[$cpos];}if($count_2B%2) {$len--;}return substr($str,0,$len).$suf;}
function ACC($ref) {return (preg_match('/'.$ref.'/', $_SERVER['HTTP_REFERER']))?true:false;}
function ACHK($V) {if(!$V['Adm']['idx'] && !$V['Adm']['id'] && !$V['Adm']['name']) {Alert("로그인 정보가 없습니다.\\n\\n다시 로그인 해 주세요");Loca('/');}}
function CLCHK($V) {if(!$V['CL']['idx'] && !$V['CL']['id'] && !$V['CL']['name']) {Alert("로그인 정보가 없습니다.\\n\\n다시 로그인 해 주세요");Loca('//');}}
function MECHK($V) {if(!$V['ME']['idx'] && !$V['ME']['id'] && !$V['ME']['name']) {Alert("로그인 정보가 없습니다.\\n\\n다시 로그인 해 주세요");Loca('/');exit;}}
function Sele($S, $D, $V=false) {$R = ($S===$D)?' selected':'';if($V!=false) {return $R;}else{echo $R;}}
function Chkd($S, $D, $V=false) {$R = ($S===$D)?' checked':'';if($V!=false) {return $R;}else{echo $R;}}
function Order($ord, $asc, $N, $L, $V=false) {$S = $_SERVER['PHP_SELF'];$H = '&nbsp;&nbsp;<a href="'.$S.'?'.$L.'&_Ord='.$ord.'&_Asc=asc">'.(($ord===$N&&$asc==='asc')?'▲':'△').'</a><a href="'.$S.'?'.$L.'&_Ord='.$ord.'&_Asc=desc">'.(($ord===$N&&$asc==='desc')?'▼':'▽').'</a>';if($V!=false) return $H;else echo $H;}
function SERVER() {echo 'IP : '.$_SERVER['REMOTE_ADDR'].'<br /><br />';echo '<pre>';print_r($_SERVER);echo '</pre>';}
function REFER($ref, $val) {return ($ref!=$val) ? false : true;}
function ROUTE($str) { $route=array("direct"=>"직접입력","naver"=>"네이버","kakao"=>"다음카카오","facebook"=>"페이스북"); return $route[$str];}
