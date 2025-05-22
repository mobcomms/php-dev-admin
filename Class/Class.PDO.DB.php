<?php
Class NDO extends PDO
{

	// Database Connection Configuration Parameters
	// array('driver' => 'mysql','host' => '','dbname' => '','username' => '','password' => '')
	protected $_config;
	// Database Connection

	public $dbc, $dbc2;

	//function NDO(){ return true;    }

	function &_NDO(array $config)
	{
		$this->_config = $config;
		return $this->_config;
	}

	function __construct(){
		return false;
	}

	public function __destruct()
	{
		$dbc = NULL;
	}

	/* Function _NDO1
	 * Get a connection to the database using PDO.
	 * Insert, Delete, Update
	 */
	function &_ActionDB()
	{
		// Create the connection
		$dsn = "" .
			$this->_config['ckd_db_driver'] .
			":host=" . $this->_config['ckd_db_host'] .
			";dbname=" . $this->_config['ckd_db_name'] .
			";port=" . $this->_config['ckd_db_port'];
		try {
			$this->dbc = new PDO($dsn, $this->_config['ckd_db_user'], $this->_config['ckd_db_pass']);
			if(empty($this->dbc)){
				throw new PDOException('Database connection failed.');
			}
			return $this->dbc;
		} catch (PDOException $e) {
			pre($e->getMessage());
			exit;
		}

	}

	/* Function _NDO2
	 * Get a connection to the database using PDO.
	 * Select
	 */
	function &_SelectDB()
	{
		// Create the connection
		$dsn = "" .
			$this->_config['ckd_db_driver'] .
			":host=" . $this->_config['ckd_db_host2'] .
			";dbname=" . $this->_config['ckd_db_name'] .
			";port=" . $this->_config['ckd_db_port'];
		try {
			$this->dbc2 = new PDO($dsn, $this->_config['ckd_db_user'], $this->_config['ckd_db_pass']);
			if(empty($this->dbc2)){
				throw new PDOException('Database connection failed.');
			}
			$this->dbc2->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbc2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			return $this->dbc2;
		} catch (PDOException $e) {
			pre($e->getMessage());
			exit;
		}

	}


	function get_mtime()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	function result_A($sql, $param = array())
	{
		$sql = trim($sql);
		$this->_ActionDB();
		$selectDB = $this->dbc;
		$stmt = $selectDB->prepare($sql);
		$stmt->execute($param);
		return $stmt;
	}

	function result_S($sql, $param = array())
	{
		$sql = trim($sql);
		$this->_SelectDB();
		$selectDB = $this->dbc2;
		$stmt = $selectDB->prepare($sql);
		$stmt->execute($param);
		return $stmt;
	}

	function trans_query($sql, $param = array())
	{
		$sql = trim($sql);
		if(!$this->dbc) {
			$this->_ActionDB();
		}
		$selectDB = $this->dbc;
		$stmt = $selectDB->prepare($sql);
		$result = $stmt->execute($param);
		return $result;
	}

	function trans_sql($sql, $param = array())
	{
		$sql = trim($sql);
		if(!$this->dbc) {
			$this->_ActionDB();
		}
		$selectDB = $this->dbc;
		$stmt = $selectDB->prepare($sql);
		$stmt->execute($param);
		$count = $stmt->rowCount();
		return $count;
	}

	function getInsertID() {
		return $this->dbc->lastInsertId();
	}

	// 1개열 전달
	function getData($sql, $param = array())
	{
		$stmt = $this->result_S($sql, $param);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result;
	}

	// 배열로 값 전달
	function fetch_array($sql, $param = array())
	{
		$stmt = $this->result_S($sql, $param);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	// Object 로 값 전달
	function fetch_obj($sql, $param = array())
	{
		$stmt = $this->result_S($sql, $param);
		$result = $stmt->fetchAll(PDO::FETCH_OBJ);
		return $result;
	}

	// insert, update, delete 실행
	function sql_query($sql, $param = array())
	{
		$result = $this->result_A($sql, $param);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}

	// select limit 실행
	function limit_query($sql, $param = array())
	{
		$sql = trim($sql);
		if(!$this->dbc2) {
			$this->_SelectDB();
		}
		$selectDB = $this->dbc2;
		$stmt = $selectDB->prepare($sql);
		foreach($param as $key=>$row){
			if($key == ":page"){
				$stmt->bindValue(':page',(int)$row, PDO::PARAM_INT);
			}else if($key == ":limit_list"){
				$stmt->bindValue(':limit_list',(int)$row,PDO::PARAM_INT);
			}else{
				$stmt->bindValue($key,$row,PDO::PARAM_STR);
			}
		}
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
}


#------------------------------------------------------------------------------------------------
# 서버 설정
#------------------------------------------------------------------------------------------------
$_DevClient = "221.150.126.74";  // 개발 클라이언트

$_TestServer = array("localhost",'127.0.0.1','127.0.0.2','127.0.0.3'); // 테스트서버 아이피
$_DevServer = array("192.168.102.100"); // 개발서버 아이피
$_ProductionServer = array("192.168.102.100"); // 실서버 아이피
$_ServerAddr = $_SERVER['SERVER_ADDR'];
//pre($_ServerAddr);

$uri = $_SERVER['REQUEST_URI']; // 현재 URL 경로 가져오기
$parts = explode("/", $uri); // '/' 기준으로 분리
$target = $parts[1]; // `hana` 부분 추출
//pre($target);


// 서버환경 : production,dev,test
if(@in_array($_ServerAddr,$_TestServer)){
	define('CASHKEYBOARD_ENV', 'test');
	$settings = parse_ini_file(__root__."/lib/site_config_test.php");
	//$settings = parse_ini_file(__root__."/lib/site_config_local.php");
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
}else if(@in_array($_ServerAddr,$_DevServer)){
	define('CASHKEYBOARD_ENV', 'dev');
	$settings = parse_ini_file(__root__."/lib/site_config_dev.php");
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
}else if(@in_array($_ServerAddr,$_ProductionServer)){
	define('CASHKEYBOARD_ENV', 'production');
	$settings = parse_ini_file(__root__."/lib/site_config.php");

	session_set_cookie_params(0, '/');
	@session_name('cashkeyboard');
	//@ini_set("session.cookie_domain", ".cashkeyboard.co.kr");
	//ini_set("display_errors", 0);

}else{
	exit("IP check");
}

//pre(CASHKEYBOARD_ENV);
//pre($settings);

// DB 연결
if (!isset($NDO)) {
	$NDO = new NDO();
	$NDO->_NDO($settings);
}
//var_dump($NDO);
//exit;
#------------------------------------------------------------------------------------------------
