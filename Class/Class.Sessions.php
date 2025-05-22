<?php

	/*
	Revised code by Dominick Lee
	Original code derived from "Essential PHP Security" by Chriss Shiflett
	Last Modified 2/27/2017


	CREATE TABLE sessions
	(
		id varchar(32) NOT NULL,
		access int(10) unsigned,
		data text,
		PRIMARY KEY (id)
	);

	+--------+------------------+------+-----+---------+-------+
	| Field  | Type             | Null | Key | Default | Extra |
	+--------+------------------+------+-----+---------+-------+
	| id     | varchar(32)      |      | PRI |         |       |
	| access | int(10) unsigned | YES  |     | NULL    |       |
	| data   | text             | YES  |     | NULL    |       |
	+--------+------------------+------+-----+---------+-------+

	*/


class Session {
	private $db;
	public $maxTime;

	public function __construct(){
		$this->maxTime['gc'] = 21600; //21600 = 6 hours

		// Instantiate new Database object
		$this->db = new Database;

		// Set handler to overide SESSION
		session_set_save_handler(
			array($this, "_open"),
			array($this, "_close"),
			array($this, "_read"),
			array($this, "_write"),
			array($this, "_destroy"),
			array($this, "_gc")
		);

		// Start the session
		@session_start();
	}
	public function _open(){
		// If successful
		if($this->db)
		{
			// Return True
			return true;
		}
		// Return False
		return false;
	}
	public function _close(){
		// Close the database connection
		// If successful
		if($this->db->close())
		{
			$this->_gc($this->maxTime['gc']);
			// Return True
			return true;
		}
		// Return False
		return false;
	}
	public function _read($id){
		$old = time() - $this->maxTime['gc'];
		//최후 활동 시간이 셋팅된 시간 이전이면 로그인이 풀림.
		// Set query
		$this->db->query('SELECT data FROM sessions WHERE id = :id AND access >= :old');
		// Bind the Id

		$this->db->bind(':id', $id);
		$this->db->bind(':old', $old);
		// Attempt execution
		// If successful
		if($this->db->execute())
		{
			if($this->db->rowCount() > 0)
			{
				// Save returned row
				$row = $this->db->single();
				// Return the data
				return $row['data'];
			}
		}
		// Return an empty string
		return '';
	}

	public function getRealClientIp() {
		if (getenv('HTTP_CLIENT_IP')) {
			$ipaddress = getenv('HTTP_CLIENT_IP');
		} else if(getenv('HTTP_X_FORWARDED_FOR')) {
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		} else if(getenv('HTTP_X_FORWARDED')) {
			$ipaddress = getenv('HTTP_X_FORWARDED');
		} else if(getenv('HTTP_FORWARDED_FOR')) {
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		} else if(getenv('HTTP_FORWARDED')) {
			$ipaddress = getenv('HTTP_FORWARDED');
		} else if(getenv('REMOTE_ADDR')) {
			$ipaddress = getenv('REMOTE_ADDR');
		} else {
			$ipaddress = '알수없음';
		}
		return $ipaddress;
	}

	public function _write($id, $data){

		// Create time stamp
		$access = time();
		// Set query  
		$this->db->query('REPLACE INTO sessions VALUES (:id, :access, :data, :ip)');
		// Bind data
		$this->db->bind(':id', $id);
		$this->db->bind(':access', $access);  
		$this->db->bind(':data', $data);
		$this->db->bind(':ip', $this->getRealClientIp());
		// Attempt Execution
		// If successful
		if($this->db->execute())
		{
			// Return True
			return true;
		}
		// Return False
		return false;
	}
	public function _destroy($id){
		// Set query
		$this->db->query('DELETE FROM sessions WHERE id = :id');
		// Bind data
		$this->db->bind(':id', $id);
		// Attempt execution
		// If successful
		if($this->db->execute())
		{
			$this->_gc($this->maxTime['gc']);
			// Return True
			return true;
		}
		// Return False
		return false;
	}
	public function _gc($max){
		// Calculate what is to be deemed old
		$old = time() - $max;
		// Set query
		$this->db->query('DELETE FROM sessions WHERE access < :old');
		// Bind data
		$this->db->bind(':old', $old);
		// Attempt execution
		if($this->db->execute())
		{
			// Return True
			return true;
		}
		// Return False
		return false;
	}
}
?>
