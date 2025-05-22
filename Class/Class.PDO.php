<?php

class NDO extends PDO {

	function __construct() {
		return true;
	}

	function &_NDO (array $config) {
		try {
			$host = $config['ckd_db_host'];
			$data = DB_NAME;
			$user = $config['ckd_db_user'];
			$pass = $config['ckd_db_pass'];
			$port = $config['ckd_db_port'];
			$db = parent::__construct("mysql:host=$host;dbname=$data;port=$port", $user, $pass);
			//$sql = "set names utf8";
			//$this->exec($sql);
			return $db;
		} catch(Exception $e) {
			echo '오류 ->'.$e->getMessage();
		}
	}
}

if(!isset($NDO)) {
	$NDO = new NDO;
	$NDO->_NDO($settings);
}
