<?php

/**
 * PDO Wrapper
 *
 * @author Anton Samson  <anton@antonsamson.com>
 */

class db
{
	private $_error, $_counter, $_rows;
	public $_db;

	/**
	 * Constructs a PDO object for MySQL queries.
	 *
	 * @param	host		Address of the MySQL server.
	 * @param	username	Username to connect with.
	 * @param	password	Password to connect with.
	 * @param	table 		The database table to use.
	 * @param	cache		Cache class object.
	 * @param	error		Error class object.
	 */
	function __construct($host, $username, $password, $table, &$error = null)
	{
		$this->_error	= $error;
		$this->_cache	= $cache;
		$this->_db		= $this->connect($host, $username, $password, $table);
		$this->_counter	= 0;	// number of queries run
		$this->_rows	= 0;	// number of rows returned
	}

	/**
	 * Connects to MySQL.
	 *
	 * @param	host		Address of the MySQL server.
	 * @param	username	Username to connect with.
	 * @param	password	Password to connect with.
	 * @param	table 		The database table to use.
	 */
	private function connect($host, $username, $password, $table)
	{
		try
		{
			$dsn = "mysql:dbname=$table;host=$host";
			$dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		}
		catch(PDOException $e)
		{
			die("Could not connect to database.");
		}

		return $dbh;
	}
	
	/**
	 * Begin a transaction.
	 */
	public function begin_transaction()
	{
		$this->_db->beginTransaction();
	}
	
	/**
	 * Commit a transaction.
	 */
	public function commit()
	{
		$this->_db->commit();
	}
	
	/**
	 * Rollback a transaction.
	 */
	public function rollback()
	{
		$this->_db->rollBack();
	}

	/**
	 * Run an SQL query.
	 *
	 * @param	query	An SQL query.
	 * @param	params	Parameters for the SQL query.
	 */
	public function query($query, $params)
	{
		$q = $this->_db->prepare($query);

		if(is_array($params))
		{
			$size = count($params);

			$i = 0;
			for(; $i < $size; $i++)
				$q->bindValue($i+1, $params[$i]);
		}
		else if(!is_null($params))
		{
			$q->bindValue(1, $params);
		}

		$q->execute();
        $this->_error = $q;

		$this->_rows = $q->rowCount();
		$this->_counter++;
		return $q;
	}

	/**
	 * Run an SQL query and return a single row.
	 *
	 * @param	query	An SQL query.
	 * @param	params	Parameters for the SQL query.
	 * @param	style	The PDO format of the result array.
	 */
	public function get_row($query, $params, $style = PDO::FETCH_ASSOC)
	{
		$q = $this->query($query, $params);
		return $q->fetch($style);
	}

	/**
	 * Run an SQL query and return an array of results.
	 *
	 * @param	query	An SQL query.
	 * @param	params	Parameters for the SQL query.
	 * @param	style	The PDO format of the result array.
	 */
	public function get_array($query, $params, $style = PDO::FETCH_ASSOC)
	{
		$q = $this->query($query, $params);
		return $q->fetchAll($style);
	}

	/**
	 * Run an SQL query and return an array of results.
	 *
	 * The first column of each result is set as the array key.
	 *
	 * May not need this function?
	 *
	 * @param	query	An SQL query.
	 * @param	params	Parameters for the SQL query.
	 * @param	style	The PDO format of the result array.
	 */
	public function get_assoc_array($query, $params, $style = PDO::FETCH_ASSOC)
	{
		$a = $this->get_array($query, $params, $style);
		return $this->rekey($a);
	}

	/**
	 * Rekeys an array to use whatever is currently the first value of each row.
	 */
	private function rekey($array)
	{
		$out = array();
		$max = count($array);

		for($i = 0; $i < $max; $i++)
		{
			$keys = array_values($array[$i]);
			if(is_numeric($keys[0]))	// remove this to allow anything to be key
			{
				array_shift($array[$i]);
				$out[$keys[0]] = $array[$i];
			}
		}

		return $out;
	}

	/**
	 * Returns the number of queries run.
	 */
	public function get_count()
	{
		return $this->_counter;
	}
	
	/**
	 * Returns the number of rows currently retrieved.
	 */
	public function get_row_count()
	{
		return $this->_rows;
	}
    
    //Get the error message for the last query operation
    public function get_error()
    {
        return $this->_error->errorInfo();
    }

}

?>
