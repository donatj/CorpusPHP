<?

/**
* The Corpus from which all modules, layouts, templates and content are compiled
*
* @package CorpusPHP
* @subpackage Layout
* @version 1.0.0.2
* @subpackage Output
*/
class co extends Corpus {} class Corpus {

	/**
	* Holds the result of $meta on a full shutup scan
	* @var array
	*/
	private static $metaSupreme;

	/**
	* Holds static data for the load function between module executions
	* @var array
	*/
	private static $staticSupreme;

	private static $workingLayout = false;

	/**
	* Sets up the $metaSupreme variable
	*/
	function __construct(){
		self::$metaSupreme['content']['meta'] = self::__meta_scan(DWS_CONTENT);
		self::$metaSupreme['modules']['meta'] = self::__meta_scan(DWS_MODULES);
		
		foreach( self::$metaSupreme['modules']['meta'] as $k => $v ) {
			if( $v['callable'] === true ) { self::$metaSupreme['modules']['calls'][$v['name']] = $k; }
		}
		
	}

	/**
	* Scans all files in a path and returns the meta when $shutup is on
	*
	* @param string $path eg: DWS_LAYOUT, DWS_CONTENT
	* @param string $sub sub directory, for use with recursion only
	* @return array
	*/
	private static function __meta_scan( $path = DWS_CONTENT, $sub = '' ) {
		$data = array();
		$files = scandir( $path . $sub );
		foreach($files as $file) {
			if( strpos($file,'.php') && $file != 'conf.php' ) {
				$meta = array();
				self::__load($path . $sub . $file,'', false, true, $meta, false, false);
				if($meta) { $data[$sub . $file] = $meta; }
			}elseif( is_dir( $path . $sub . $file ) && $file != '.' && $file != '..' ) {
				$data = array_merge( self::__meta_scan( $path, $sub . $file . '/' ), $data);
			}
		}
		return $data;
	}
	
	private static function __premeta( $fname ) {
		if( strpos( $fname, DWS_MODULES ) === 0 ) {
			return self::$metaSupreme['modules']['meta'][ substr( $fname, strlen( DWS_MODULES ) ) ];
		}
	}

	/**
	* Does the actual loading of and sets up the enviornment for content/layouts/modules/templates
	* wrapped in an output buffer, within a function for maximum encapsulation
	*
	* @param string $fname filename
	* @param string|bool $lname layout name
	* @param array $data an array passed to, for use within, the module, template, etc
	* @param bool $shutup
	* @param mixed $_meta
	* @param mixed $__execModuleCalls
	* @param bool $__execConf
	* @return string the compiled result
	*/
	private static function __load( $fname,  $lname = '', $data = false, $shutup = false, &$_meta = false, $__execModuleCalls = true, $__execConf = true ) {
		global $_ms, $_lg, $_nh;
		$fname = self::__get_filename($fname);
		if( $_meta === false ) { global $_meta; }
		if( $__execConf ) { self::__conf_load($fname, $_meta); }
		if( !$shutup ) { 
			$_premeta = self::__premeta( $fname ); 
			if( $_premeta['name'] ) {
				$_cache = new Cache( $_premeta['name'] );
				$_config = new Configuration( $_premeta['name'] );
			}
		}
		
		$static =& self::$staticSupreme[ $fname ];
		if( file_exists( $fname ) ) {
			ob_start();
			require($fname);
			$content = ob_get_clean();
			if( !$shutup && $__execModuleCalls && $_meta['execModuleCalls'] !== false  && $_meta['removeModuleCalls'] !== true ) { self::exec_module_calls( $content ); }
      
      			if($_meta['removeModuleCalls']) { $content = preg_replace(PATTERN_MODULE_CALL,'',$content); }

			if(!$lname || $_meta['raw']) {
				return $content;
			}else{
				return '<div id="layout_'.str_replace( "/", "-", $lname).'">' . $content . '</div>';
			}
		}else{
			if( !$shutup ) { $_ms->add( $fname . ' Not Found', true ); }
			return false;
		}
	}

	/**
	* execs conf.php meta from a content subfolder into the meta array
	*
	* @todo Rewrite this whole garbage heap, added because functionality was needed, though not good
	* @param string $fname filename
	* @param mixed $_meta
	*/
	private static function __conf_load( $fname, &$_meta ) {
		$pathA = explode( '/', preg_replace('%^'.preg_quote(DWS_CONTENT).'%','', pathinfo($fname, PHP_URL_PATH), 1, $is_content) );
		if( $is_content ) {
			foreach( $pathA as $pathSub ) {
				$path .= $pathSub . '/';
				if( is_file(  DWS_CONTENT . $path . 'conf.php' ) ) {
					//needs to not shutup because its the only way to tell from scan at current
					self::__load( DWS_CONTENT . $path . 'conf.php', false, false, false, $_meta, false, false );
				}
			}
		}
	}

	/**
	* Executes modules within content via a regex callback
	*
	* @param string $content the content the modules are to be executed upon by reference
	*/
	private static function exec_module_calls( &$content ) {

		if( is_callable( array(  get_class(), '___modules_exec___' ), true, $call ) ) {
			$content = preg_replace_callback( PATTERN_MODULE_CALL, $call, $content );
		}else{
			die('Module Call Error');
		}

	}

	/**
	* executes the modules
	*
	* @param array $m the data array passed to the callback
	* @return string the result of the module call
	*/
	private static function ___modules_exec___( $m ) {
		global $_ms;

		$module = self::$metaSupreme['modules']['calls'][ $m[1] ];
		if( strlen( $module ) > 0 ) {
			$m[3] = str_replace( "'", '"', $m[3] );
			if( $m[2] == '[' ) {
				$data = json_decode( '[' . $m[3] . ']', true );
			}else{
				$data = json_decode( '{' . $m[3] . '}', true );
			}
			return self::module( $module, $data );
		}else{
			$_ms->add( 'Call ' . $m[1] . ' not set', true );
		}

		return false;
	}

	public static function template($name, $data = false, &$_meta = false) {
		return self::__load(DWS_TEMPLT . $name, $name, $data, false, $_meta);
	}

	public static function content($name, $shutup = false, &$_meta = false) {
		return self::__load(DWS_CONTENT . $name, $name, false, $shutup, $_meta);
	}

	public static function layout($name, $data = false, $layout = false, &$_meta = false) {

		if( !$layout ) {
			if( self::$workingLayout == false ) {
				$layout = 'main';
			}else{
				$layout = self::$workingLayout;
			}
		}

		if( !self::$workingLayout ) { self::$workingLayout = $layout; }

		return self::__load(DWS_LAYOUT . $layout . '/' . $name, false, $data, false, $_meta);
		self::$workingLayout = false;
	}

	public static function module($name, $data = false, &$_meta = false) {
		if($_meta == false) { $_meta = array(); } //modules should not set the global meta unless otherwise passed the global meta manually
		return self::__load(DWS_MODULES . $name, false, $data, false, $_meta);
	}

	public static function content_info() {
		return self::$metaSupreme['content']['meta'];
	}

	/**
	* Gets a filename for a corpus component
	*
	* @param string $name Corpus Name
	* @return string The filename of the corpus component
	*/
	private static function __get_filename($name) {

		if( substr($name, -1) == '/' ) {
			$name .= 'index.php';
		}elseif( is_dir( $name ) ) {
			$name .= '/index.php';
		}elseif(!strpos($name, '.php')) {
			$name .= '.php';
		}

		return $name;
	}

}
