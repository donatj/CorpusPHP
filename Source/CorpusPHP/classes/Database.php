<?php

/**
* Database Connection / Manipulation Class
*
* @package CorpusPHP
* @subpackage Data
* @author Jesse G. Donat
* @author Jon Henderson
* @version 1.2
*/
abstract class Database {

	public static $link;
	
	/**
	* Constants for use with ::fetch
	*/
	const KEYVALUE = 'keyvalue', KEYROW = 'keyrow', ROW = 'row', SCALAR = 'scalar', FLAT = 'flat';

	/**
	* Creates connection to the database, checks the database character set, and sets the database to work in UTF-8
	*/
	public static function make_connection() {
		global $_ms;
		static::$_connection = @mysql_connect(static::$_host, static::$_user, static::$_password);
		if(!static::$_connection) trigger_error('Error Connecting to Database ' . static::$_host, E_USER_ERROR);
		if(!mysql_select_db(static::$_database)) trigger_error('Cannot Locate Database: ' . static::$_database, E_USER_ERROR);

		mysql_query( "SET NAMES "         . static::$_charset );
		mysql_query( "SET CHARACTER SET " . static::$_charset );
	}

	/**
	* Wrapper Class for mysql_real_escape_string which also trims unless instructed not to
	*
	* @param string $str string to escape/trim
	* @param bool $trim whether or not to trim
	* @return string escaped/trimmed string
	*/
	static function input($str, $trim = true) {
		if(!static::$_connection) { self::make_connection(); }
		
		if($trim) $str = trim($str);
		return mysql_real_escape_string($str);
	}

	/**
	* Wrapper for mysql_query with error-ing
	* Will return passed resource if passed a resource
	*
	* @param string|resource $query query string or query. 
	* @param mixed $fatal whether a query error is fatal to the application
	* @return resource the returned query resource
	*/
	static function query($query, $fatal = true, $display = false) {
		if(!static::$_connection) { self::make_connection(); }

		if( is_resource($query) ) { return $query; } 
		$qry = mysql_query($query);
		if(!$qry) self::error($query, mysql_error(), $fatal, $display);
		return $qry;
	}

	/**
	* Wrapper for mysql_insert_id
	*
	* @todo Determine implications of casting to int
	* @return int
	*/
	static function id() { return (int)mysql_insert_id(); }

	private static function error($qry, $error, $fatal, $display = true) {
		global $_ms;
		//needs logic look into
		if($fatal) {
			trigger_error($error . ' ' . $qry, E_USER_ERROR);
		}else{
			if(is_object($_ms) && $display) { $_ms->add($error . ' : ' . $qry . $trace_str, true); }
			return false;
		}
	}

	/**
	* A function for building an Insert / Update query string based on an array
	*
	* @author Jesse G. Donat
	* @author Jon Henderson
	* @param string $table The database table
	* @param array $data The data to be inserted / updated in the format of array(fieldname => value, fieldname2 => value) with special, extrasafe code injection via array( fieldname => array( true, 'raw unescaped, unquoted sql') )
	* @param string|bool $parameters
	* @param bool $escape whether or not to escape all data from the array
	* @return string the built query string
	*/
	static function build($table, $data, $parameters = false, $escape = true) {

		$values = '';

		foreach($data as $column => $value){

			if ( is_array($value) && $value[0] === true ) {
				$values .= "`{$column}` = {$value[1]}, ";
			}else{
				$values .= "`{$column}` = '" . ( $escape ? self::input( $value ) : $value ) . "', ";
			}

		}

		$action = is_string($parameters) ? " Update {$table} Set " : ( $parameters === true ? " Replace Into {$table} Set " : " Insert Into {$table} Set ");
		$values = rtrim($values, ', ');
		$condition = is_string($parameters) ? " where {$parameters} " : " ";

		return $action . $values . $condition;
	}

	/**
	* Builds and Executes a query based on an array
	*
	* @see self::build
	* @param string $table
	* @param array $data
	* @param string|bool $parameters
	* @param bool $fatal whether to die on the query returning an error
	* @param mixed $escape
	* @return resource
	*/
	static function perform($table, $data, $parameters = false, $fatal = true, $escape = true) {
		return self::query( self::build( $table, $data, $parameters, $escape ) , $fatal);
	}

	/**
	* Return from a query.  Note, this is *not* simply mysql_fetch_array wrapper
	*
	* @param string|resource $qry
	* @param bool|string $type ex: ::KEYVALUE, ::KEYROW, ::ROW, ::SCALAR, ::FLAT
	* @param bool $trim
	* @return array|scalar the result of the query in the format chosen by type
	*/
	static function fetch($qry, $type = false, $trim = true) {
		$qry = self::query( $qry );
		$data = array();

		switch( $type ) {
			case self::KEYVALUE:
				while($row = mysql_fetch_array($qry)) {
					if($trim) $row[1] = trim( $row[1] );
					$data[$row[0]] = $row[1];
				}
				break;
			case self::KEYROW:
				while($row = mysql_fetch_array($qry)) {
					$data[$row[0]] = $row;
				}
				break;
			case self::ROW:
				$data = mysql_fetch_assoc($qry);
				break;
			case self::SCALAR:
				$row = mysql_fetch_array($qry);
				$data = $row[0];
				break;
			case self::FLAT:
				while($row = mysql_fetch_array($qry)) {
					if($trim) $row[0] = trim( $row[0] );
					$data[] = $row[0];
				}
				break;
			default:
				while($row = mysql_fetch_assoc($qry)) { $data[] = $row; }
		}
		return $data;
	}

	/**
	* Backs up the database using MySQL Dump and Gzip
	* @todo This is not well implimented
	*/
	static function backup() {
		$db_file = DFS_DB_BACKUP . 'db_' . static::$_database . '-' . date('YmdHis') . '.sql.gz';
		$command = "mysqldump --opt --host=".DB_HOST." --user=".DB_USER." --password=".DB_PASSWORD." ".static::$_database." | gzip > ".$db_file;
		exec($command);
		if( is_file($db_file) ) { return true; }
		return false;
	}
	
	/**
	* Returns the members of a MySQL enumeration as an array
	* 
	* @todo Will fail if enumeration member contains ', or ')
	* @param string $table the table containing the enumeration
	* @param string $field the enumeration field
	* @return array the members of the enumeration
	*/
	static function enumMembers($table, $field) {
		$row = self::fetch("show columns from " . $table . " where field = '" . self::input($field) . "'", db::ROW);
		preg_match_all( '/\'(.*?)\'(?=[,)])/' , $row['Type'], $enum_array );
		return $enum_array[1];
	}

}
