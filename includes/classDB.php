<?php
/**	DB Object class made to simplify query code
 *  Copyright 2014, 2015 Alvin R. Betoya
 *  Released under the MIT license (https://tldrlegal.com/license/mit-license)
 *	v0.4.3
 * 
 *	changelog:
 *	0.4.3	- initialized error
 *	0.4.2	- added affected rows
 *	0.4.1	- added error reporting
 *	0.4.0	- migrated to mysqli
 *	0.3.2	- added last insert id method
 *	0.3.1	- fixed bug on nonstandard character queries

	==Usage instructions==
	General:
	Php symbol "->" is equivalent to java/.net ".".
		Ex:	PHP		- Object->method("args");
			Java	- Object.method("args");

	Create a new DB connection using:
		$db = new DBObject("database_name"); OR
		$db = new DBObject("database_name", "host_name"); OR
		$db = new DBObject("database_name", "host_name", "user_name"); OR
		$db = new DBObject("database_name", "host_name", "user_name", "password");

	IMPORTANT! USE escape() FUNCTION FOR NON-HASHED QUERY VARS AND PRE_HASHED PASSWORD:
		$usern = $db->escape($_POST["username"]); //escape to protect against SQL injection attacks
		$sql = "SELECT * FROM usertable WHERE username = '$usern' AND userpass = '$encpass'";
		$sql = "INSERT INTO usertable VALUES ('$usern', '$encpass')";

	IMPORTANT! USE intval() when using ints in query:
		$id = intval($id);
		$sql = "SELECT * FROM usertable WHERE userid = $id";

	SELECT statements:
		$sql = "SELECT ...";
		$result = $db->query($sql);
		if ($row = mysqli_fetch_array($result)) {
			//TODO: code that operates on query results [$row]
		} else {
			//TODO: code that runs if $row is empty
		}
			OR
		while ($row = mysqli_fetch_array($result)) {
			//TODO: code that operates on query results [$row]
		}

	INSERT and DELETE statements:
		$sql = "INSERT ...";
		if ($db->query($sql)) {
			//TODO: code for successful update
		} else {
			//TODO: code for failed update
		}
*/
class DBObject {
	private $database;
	private $host;
	private $dbuser;
	private $dbpass;
	private $con;
	private $lastid;
	private $error = '';
	private $affected;

	/**
	 * Create a new DB connection using either of the following:
	 * $db = new DBObject("database_name");
	 * $db = new DBObject("database_name", "host_name");
	 * $db = new DBObject("database_name", "host_name", "user_name");
	 * $db = new DBObject("database_name", "host_name", "user_name", "password");
	 */
	public function __construct($_database, $_host = 'localhost', $_dbuser = 'root', $_dbpass = '') {
		$this->host = $_host;
		$this->dbuser = $_dbuser;
		$this->dbpass = $_dbpass;
		$this->database = $_database;
	}
	
	/** Escapes using mysqli_real_escape_string. */
	public function escape($var) {
		$this->connect();
		mysqli_set_charset($this->con, 'utf8'); //added v0.3.1
		$escaped = mysqli_real_escape_string($this->con, $var);
		mysqli_close($this->con);
		return $escaped;
	}
	
	/**
	 * Simple query function. Logs the following:
	 * 
	 * last insert id
	 * 
	 * error(s)
	 * 
	 * number of affected rows
	 */
	public function query($sql) {
		$this->connect();
		mysqli_set_charset($this->con, 'utf8'); //added v0.3.1
		mysqli_select_db($this->con, $this->database);
		mysqli_query($this->con, "SET time_zone = 'Asia/Manila'");
		$result = mysqli_query($this->con, $sql);
		$this->lastid = mysqli_insert_id($this->con);
		$this->error = mysqli_error($this->con);
		$this->affected = mysqli_affected_rows($this->con);
		mysqli_close($this->con);

		return $result;
	}
	
	private function connect() {
		$this->con = mysqli_connect($this->host, $this->dbuser, $this->dbpass);
		if (mysqli_connect_errno()) {
			echo 'Error: Cannot connect to database!';
			return false;
		}
		return true;
	}

	public function setDatabase($_database) {
		$this->database = $_database;
	}

	public function setHost($_host) {
		$this->host = $_host;
	}

	public function setUser($_dbuser) {
		$this->dbuser = $_dbuser;
	}

	public function setPassword($_dbpass) {
		$this->dbpass = $_dbpass;
	}

	public function getDatabse() {
		return $this->database;
	}

	public function getHost() {
		return $this->host;
	}

	public function getUser() {
		return $this->dbuser;
	}

	public function getPassword() {
		return $this->dbpass;
	}
	
	/** added v0.3.2 */
	public function getLastID() {
		return $this->lastid;
	}
	
	/** added v0.4.1 */
	public function getError() {
		return $this->error;
	}
	
	/** added v0.4.2 */
	function getAffected() {
		return $this->affected;
	}

}