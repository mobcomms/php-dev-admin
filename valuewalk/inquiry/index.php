<?php

include_once './../var.php';
include_once __pdoDB__;    ## DB Instance 생성
include_once __fn__;


/**
 * PHP 8에서 소개된 str_contains() 함수를 polyfill로 대체하는 함수입니다.
 * PHP 8 미만의 버전에서 str_contains() 함수를 사용하기 위해 사용됩니다.
 *
 * @param string $haystack 대상 문자열입니다.
 * @param string $needle 찾고자 하는 부분 문자열입니다.
 * @return bool 주어진 문자열이 부분 문자열을 포함하면 true를, 그렇지 않으면 false를 반환합니다.
 */
if (!function_exists('str_contains')) {
	/*
	 * str_contains() 함수의 polyfill
	 * 출처: https://core.trac.wordpress.org/browser/trunk/src/wp-includes/compat.php#L423
	*/
	function str_contains($haystack, $needle) {
		if ('' === $needle) {
			return true;
		}
		return false !== strpos($haystack, $needle);
	}
}

/**
 * 모바일 장치를 감지하여 체크하는 함수입니다.
 * HTTP_SEC_CH_UA_MOBILE 헤더를 확인하고, HTTP_USER_AGENT를 분석하여 모바일 장치인지 여부를 판단합니다.
 *
 * @return bool 모바일 장치가 감지되면 true를, 그렇지 않으면 false를 반환합니다.
 */
function is_mobile() {
	if (isset($_SERVER['HTTP_SEC_CH_UA_MOBILE'])) {
		// HTTP_SEC_CH_UA_MOBILE 헤더가 존재하고, 값이 '?1'인 경우 모바일로 판단합니다.
		// 참조: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Sec-CH-UA-Mobile
		return ( '?1' === $_SERVER['HTTP_SEC_CH_UA_MOBILE'] ); //
	} elseif (!empty($_SERVER['HTTP_USER_AGENT'])) {
		// HTTP_USER_AGENT를 분석하여 모바일 특징을 찾습니다.
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		return str_contains($user_agent, 'Mobile')
			|| str_contains($user_agent, 'Android')
			|| str_contains($user_agent, 'Silk/')
			|| str_contains($user_agent, 'Kindle')
			|| str_contains($user_agent, 'BlackBerry')
			|| str_contains($user_agent, 'Opera Mini')
			|| str_contains($user_agent, 'Opera Mobi');
	} else {
		// HTTP_SEC_CH_UA_MOBILE 헤더도 없고, HTTP_USER_AGENT도 없으면 모바일이 아닌 것으로 간주합니다.
		return false;
	}
}

/**
 * iOS를 감지하여 체크하는 함수입니다.
 *
 * @return bool 모바일 장치가 iOS인 경우 true를, 그렇지 않은 경우 false를 반환합니다.
 */
function is_ios() {
	return is_mobile() && preg_match('/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT']);
}

/**
 * Android를 감지하여 체크하는 함수입니다.
 *
 * @return bool 모바일 장치가 Android인 경우 true를, 그렇지 않은 경우 false를 반환합니다.
 */
function is_android() {
	return is_mobile() && preg_match('/Android/', $_SERVER['HTTP_USER_AGENT']);
}


$path = ".";
include_once "./html/index.php";
