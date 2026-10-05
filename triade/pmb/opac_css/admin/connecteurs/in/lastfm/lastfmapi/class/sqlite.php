<?php
/**
 * Stores the sqlite database methods
 * @package base
 */
/**
 * Allows access to the sqlite database using a standard database class
 * @package base
 */
class lastfmApiDatabase {
	/**
	 * Stores the path to the database
	 * @var string
	 */
	public $path;
	/**
	 * Stores the connection status
	 * @var boolean
	 */
	public $dbConn;
	/**
	 * Stores the error details
	 * @var array
	 */
	public $error;
	
	/**
	 * Run when the class is created. Sets up the variables
	 * @param string $path The path to the database
	 * @return void
	 */
	public function __construct($path) {
		$this->path = $path;
		$this->connectToDb();
	}
	
	/**
	 * Internal command to connect to the database
	 * @return void
	 */
	public function connectToDb () {
		if (!$this->dbConn = @sqlite_open($this->path, 0666, $this->error)) {
			return false;
		}
	}
	
	/**
	 * Method which runs queries. Returns a class on success and false on error
	 * @param string $sql The SQL query to run
	 * @return object
	 * @uses lastfmApiDatabase_result
	 */
	public function & query($sql) {
		if ( !$queryResource = sqlite_query($this->dbConn, $sql, SQLITE_BOTH, $this->error) ) {
			return false;
		}
		else {
			return new lastfmApiDatabase_result($this, $queryResource);
		}
	}
}

/**
 * A class which allows interaction with results when a query is run by lastfmApiDatabase
 * @package base
 */
class lastfmApiDatabase_result {
	/**
	 * Stores the sqlite class
	 * @var object
	 */
	public $sqlite;
	/**
	 * Stores the query
	 * @var object
	 */
	public $query;
	
	/**
	 * Run when the class is created. Sets up the variables
	 * @param object $sqlite The sqlite class
	 * @param object $query The query
	 * @return void
	 */
	public function __construct(&$sqlite, $query) {
		$this->sqlite = &$sqlite;
		$this->query = $query;
	}
	
	/**
	 * Fetches the next result
	 * @return array
	 */
	public function fetch () {
		if ( $row = sqlite_fetch_array($this->query) ) {
			return $row;
		}
		else if ( $this->size() > 0 ) {
			sqlite_seek($this->query, 0);
			return false;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Fetches all the results
	 * @return array
	 */
	public function fetchAll() {
		$result = array();
		while ( $row = sqlite_fetch_array($this->query) ) {
			$result[] = $row;
		}
		return $result;
	}
	
	/**
	 * Shows the number of results
	 * @return integer
	 */
	public function size () {
		return sqlite_num_rows($this->query);
	}
}
?>