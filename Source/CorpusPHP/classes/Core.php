<?

/**
*	The (Spicy Hot) Swappable Core
*
* @package CorpusPHP
* @subpackage Core
*/
class _ extends Core {} class Core {

	static $id  = false;
	static $url = false;

	function __construct() {
		global $___Urls, $_meta;
		$this->cacheUrls();
		self::$id = (int)$_GET['id'];
		self::$url = trim($_GET['corpusphp_url']);
		
		if( self::$url == '' ) {
			self::$url = 'index.php';
		}
		
		//SEO Urls
		if( self::$id <= 0 && isset($___Urls[ self::$url ]) ){
			self::$id = $___Urls[ self::$url ];
		}
		$_meta['id'] = self::$id;
		$_meta['page'] = parse_url( $_SERVER['REQUEST_URI'] );
		$_meta['page']['url'] = self::$url;

	}

	private function cacheUrls() {
		global $___Urls;
		$qry = db::query("select categories_id, url from categories where char_length(url) > 1 group by url");
		while( $row = mysql_fetch_assoc($qry) ) {
			if( !is_numeric( $row['url'] ) ){
				$___Urls[ (int)$row['categories_id'] ] = trim($row['url']);

				if( !isset( $___Urls[ trim($row['url']) ] ) ) { //2x multiplier
					$___Urls[ trim($row['url']) ] = (int)$row['categories_id'];
				}
			}
		}
	}

	/**
	* The very core of Corpus, loads the content for the page
	* 
	* @return string
	*/
	public static function content() {

		global $___Urls, $_meta, $_data;
		if( self::$id > 0 ) { //loads databased pages
			if( $_data = self::data( self::$id ) ) {

				if( $_data['template'][0] != '_' && self::$url != $___Urls[ self::$id ] && isset($___Urls[ self::$id ]) ) { //if the SEO Url isn't prefect, correct it
					redirect( href( self::$id ), 301 );
				}
				
				$_meta['layout'] = $_data['layout'];
				$_content = co::template($_data['template'], $_data);
			}
		}elseif( strlen(self::$url) > 0 ){ //loads things like forms / search page from content/ folder
			$_content = co::content( self::$url );
		}

		return $_content;

	}

	/**
	* Get/Standardize a URL
	* 
	* @todo strings starting with ./ resolve from current path
	* @param string|int|bool $url The URL to Standardize.  If an int will check the URL cache, if false/blank returns the current page
	* @param mixed $encode whether or not to encode entities
	* @param mixed $strict
	* @param bool|null $ssl enable/disable ssl, if null - auto
	* @return string
	*/
	public static function href($url = false, $encode = true, $strict = false, $ssl = null) {
		global $___Urls, $_meta;
		if($url === false) { 
			$url = $_meta['page']['path']; 
		}
		
		/* elseif( strpos($url, './') === 0 ){
			$url = $_meta['page']['path'] . substr($url, 1);
		}*/
		

		if( is_numeric($url) ) {
			if( strlen($___Urls[$url]) > 1 ) {
				$url = urlencode($___Urls[$url]);
			}else{
				$url = '?id=' . (int)$url;
			}
		}

		if(!$strict && strpos( strtolower($url) , 'http://') === false && strpos( strtolower($url) , 'https://') === false) {
			if($url[0] == '/') {
				$url = DWS_DOMAIN . $url;
			}else{
				$url = DWS_BASE . $url; //a full path;
			}
		}
		
		if( $ssl === true || ( strtolower($_SERVER['HTTPS']) == 'on' && $ssl === null ) ) {
			$url = str_ireplace( 'http://', 'https://', $url );
		}
		
		if($encode){ $url = htmlE($url); }
		return $url;
	}

	/**
	* Allow mendicants access to the cores category data
	* 
	* @param int $id
	* @return array
	*/
	public static function data($id) {
		$data = db::fetch( "Select * From categories Where categories_id = " . (int)$id, db::ROW );
		$data['supplementary'] = json_decode($data['supplementary'], true);
		return $data;
	}

}