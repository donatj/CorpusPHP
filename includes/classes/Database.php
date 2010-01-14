<?

/**
* Database Connection / Manipulation Class
*
* @package CorpusPHP
* @subpackage Data
* @author Jesse G. Donat
* @author Jon Henderson
* @version 1.0
*/
class db extends Database {} class Database {

	var $link;

	/**
	* Creates connection to the database, checks the database character set, and sets the database to work in UTF-8
	*/
	function __construct(){
		global $_ms;
		$this->link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
		if(!$this->link) $_ms->add('Error Connecting to Database', true);
		if(!mysql_select_db(DB_DATABASE)) $_ms->add('Cannot Locate Database: ' . DB_DATABASE, true);

		//Check if the database is UTF-8 because we're responsible like that.
		if( mysql_fetch_object( self::query( "Show Variables Like 'character_set_database'" ) )->Value != 'utf8' ) {
			die( '<span style="color:red">Fatal Error:</span> The database\'s default encoding is not UTF-8. Please correctly configure your database and reimport to prevent corruption.' );
		}

		//Tell the DB we're doing it new school, because we're cool like that
		mysql_query( "SET NAMES utf8" );
		mysql_query( "SET CHARACTER SET utf8" );
	}

	/**
	* Wrapper Class for mysql_real_escape_string which also trims unless instructed not to
	*
	* @param string $str string to escape/trim
	* @param bool $trim whether or not to trim
	* @return string escaoed/trimed string
	*/
	static function input($str, $trim = true) {
		if($trim) $str = trim($str);
		return mysql_real_escape_string($str);
	}

	/**
	* Wrapper for mysql_query with erroring
	*
	* @param string $query query string
	* @param mixed $fatal whether a query error is fatal to the application
	* @return resource the returned query resource
	*/
	static function query($query, $fatal = true, $display = false) {
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
		$trace_str = _errorTracer('query');
		if($fatal) {
			die('<table><td valign="top"><strong>'. $error . '</strong><br /><br /><small>' . $qry . '</small></td><td valign="top">' . $trace_str . '</td></table>');
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
	* Get a JSON encoded result from a query
	*
	* @param string|resource $qry query
	* @param bool $flat whether to flatten the array
	* @param bool $trim whether to trim each result
	* @return string JSON encoded result set
	*/
	static function fetchJson($qry, $type = false, $trim = true) {
		return json_encode( self::fetch($qry, $type, $trim ) );
	}

	/**
	* Return from a query.  Note, this is *not* simply mysql_fetch_array wrapper
	*
	* @todo rename to prevent mysql_fetch_array name confusion
	* @param string|resource $qry ex: keyvalue, row, scalar, flat
	* @param bool|string $type
	* @param bool $trim
	* @return array|scalar the result of the query
	*/
	static function fetch($qry, $type = false, $trim = true) {
		$qry = self::queryIfNot( $qry );
		$data = array();

		switch( $type ) {
			case 'keyvalue':
				while($row = mysql_fetch_array($qry)) {
					if($trim) $row[1] = trim( $row[1] );
					$data[$row[0]] = $row[1];
				}
				break;
			case 'row':
				$data = mysql_fetch_assoc($qry);
				break;
			case 'scalar':
				$row = mysql_fetch_array($qry);
				$data = $row[0];
				break;
			case 'flat':
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
	* If is a resource, returns it, else makes a query result resource from presuming query string
	*
	* @param mixed $qry
	* @return resource
	*/
	static function queryIfNot( $qry ) {
		if( !is_resource($qry) ) { $qry = self::query($qry); }
		return $qry;
	}

	/**
	* Backs up the database using MySQL Dump and Gzip
	*
	*/
	static function backup() {
		$db_file = DFS_DB_BACKUP . 'db_' . DB_DATABASE . '-' . date('YmdHis') . '.sql.gz';
		$command = "mysqldump --opt --host=".DB_HOST." --user=".DB_USER." --password=".DB_PASSWORD." ".DB_DATABASE." | gzip > ".$db_file;
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
		$row = self::fetch("show columns from " . $table . " where field = '" . self::input($field) . "'", 'row');
		preg_match_all( '/\'(.*?)\'(?=[,)])/' , $row['Type'], $enum_array );
		return $enum_array[1];
	}

}
