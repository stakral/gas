<?php
require_once(LIB_PATH.DS."config.php");

class Database {

	protected $db; // Pripojenie k DB.
	protected $statement;
	protected $fetchType = PDO::FETCH_OBJ; // PDO::FETCH_OBJ je default, ale mozme ho zmenit  s setFetchType.

	
	/**
	 * DB constructor.
	 * @param $db
	 */
	public function __construct()
	{
		$this->db = new PDO(
			DB_TYPE
				.":dbname=". DB_NAME
				.";host=".   DB_HOST
				.";charset=".DB_CHARSET
				.";port=".   DB_PORT,
			DB_USER,
			DB_PASS
		);

		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		// $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);  // Only for very old PHP versions.
	}


	/**
	 * Set type of returned resuts [ array, object, ... ]
	 *
	 * @param mixed $fetchType
	 */
	public function setFetchType($fetchType)
	{
		$this->fetchType = $fetchType;
	}


	/**
	 * Create database query
	 *
	 * @param $query
	 * @param array $data
	 * @return PDO Statement
	 */
	public function query($query, $data = [])
	{
		try
		{
			// throw new PDOException("Some error message"); // Just to test PDOException.
			$this->statement = $this->db->prepare($query); // Tvorime prepared statement
			$this->statement->execute($data); // Vkladame data.

			return $this->statement;
		}
		catch (PDOException $e)
		{
			$error  = date( 'j M Y, G:i' ) . PHP_EOL;
			$error .= '------------------' . PHP_EOL;
			$error .= $e->getMessage() . ' in [ '. __FILE__ .' : '. __LINE__ .' ] ' . PHP_EOL . PHP_EOL;
		
			file_put_contents( SITE_ROOT . '/logs/error.log', $error, FILE_APPEND );
		}
	}


	/**
	 * Fetch all results of query
	 *
	 * @param $query
	 * @param array $data
	 * @return array
	 */
	public function getAll($query, $data = [])
	{
		return $this->query($query, $data)->fetchAll( $this->fetchType );
	}


	/**
	 * Fetch a single result of query
	 *
	 * @param $query
	 * @param array $data
	 * @return mixed
	 */
	public function getOne($query, $data = [])
	{
		return $this->query($query, $data)->fetch( $this->fetchType );
	}


	/**
	 * Update a single item
	 *
	 * @param $query
	 * @param array $data
	 * @return mixed
	 */
	public function updateOne($query, $data = [])
	{
		return $this->query($query, $data);
	}


	/**
	 * Create / Insert a new DB record
	 *
	 * @param $query
	 * @param array $data
	 * @return mixed
	 */
	public function insert($query, $data = [])
	{
		return $this->query($query, $data);
	}


	/**
	 * DeleteOne DB record
	 *
	 * @param $query
	 * @param array $data
	 * @return mixed
	 */
	public function deleteOne($query, $data = [])
	{
		return $this->query($query, $data);
	}

	/**
	 * 
	 * Close DB connection
	 * close db connection when job is done.
	 */
	public function close_connection(){
		$this->db = null;
	}


}

$database = new Database();
$db =& $database; // We can use $db as a reference to $database


/*

class MySQLDatabase {
	
	private $connection; // Private attribute to use connection by other functions in the class.
	
	function __construct(){
		$this->open_connection();
	}
	public function open_connection(){
		$this->connection = mysqli_connect('DB_SERVER', 'DB_USER', 'DB_PASS', 'DB_NAME');
		mysqli_set_charset($this->connection, 'utf8'); // Setting database character coding
		if(mysqli_connect_errno()){
			die("Database connection failed: ". mysqli_connect_error().
				"{". mysqli_connect_erno(). "}"
		);
		}
	}
	public function close_connection(){
		if(isset($this->connection)){
			mysqli_close($this->connection);
			unset($this->connection);
		}
	}
	public function query($sql){
		$result = mysqli_query($this->connection, $sql);
		$this->confirm_query($result);
		return $result;
	}
	private function confirm_query($result){
		if (!$result){
			die("Database query failed.");
		}
	}
	public function escape_value($string){
		
		$escaped_string = mysqli_real_escape_string($this->connection,
		$string);
		return $escaped_string;
	}
	// Below are "database neutral" functions
	public function fetch_array($result_set){
		return mysqli_fetch_array($result_set);
	}
	public function num_rows($result_set){
		return mysqli_num_rows($result_set);
	}
	public function insert_id(){
		// returns the last id inserted over the current db connection
		return mysqli_insert_id($this->connection);
	}
	public function affected_rows(){
		return mysqli_affected_rows($this->connection);
	}
}

$database = new MySQLDatabase();
$db =& $database; // We can use $db as a reference to $database
*/

?>