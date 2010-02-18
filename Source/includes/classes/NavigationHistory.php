<?

/**
* Navigation History Class 
* 	For Storing / Restoring Page States
* @package CorpusPHP
* @subpackage Navigation
* @author Jesse G. Donat
* @todo Change the way multi-historying works to be more similar to a message stack
*/
class NavigationHistory {
	
	function NavigationHistory() {
		if(!is_array($_SESSION['corpus']['nav_history']['snapshot'])) {
			$_SESSION['corpus']['nav_history']['snapshot']['default'] = array();
		}
	}
	
	function Reset($history ='default') { $_SESSION['corpus']['nav_history']['snapshot'][$history] = array();	}

	function RestoreURL($history ='default') {
		if( $this->IsSnapshot($history) ) {
			return $_SESSION['corpus']['nav_history']['snapshot'][$history]['page'] . '?' . http_build_query($_SESSION['corpus']['nav_history']['snapshot'][$history]['get']);
		}
		return false;
	}
	
	function IsSnapshot($history ='default') {
		if(strlen($_SESSION['corpus']['nav_history']['snapshot'][$history]['page']) > 1) {
			return true;
		}
		return false;
	}
	
	function SetSnapshot($page = '', $history ='default') {

		if (is_array($page)) {
			$_SESSION['corpus']['nav_history']['snapshot'][$history] = array('page' => $page['page'],
				'get' => $page['get'],
				'post' => $page['post']);
		} else {
			$_SESSION['corpus']['nav_history']['snapshot'][$history] = array('page' => href(),
				'get' => $_GET,
				'post' => $_POST);
		}
	}
	
	function GetPath() {
		return $_SESSION['corpus']['nav_history']['path'];
	}
	
	/**
	* On destuct of the object save it to the navigation history path
	* @todo Move this into a seperate function, and clean up logic, improve logic, logic it up
	*/
	function __destruct() {
		$current = array('page' => href(),
				'get' => $_GET,
				'post' => $_POST);
		
		if( !is_array( $_SESSION['corpus']['nav_history']['path'] ) ) { 
			$_SESSION['corpus']['nav_history']['path'] = array();
		}
		$last = end( $_SESSION['corpus']['nav_history']['path'] );
		if( $last != $current ) {
			$_SESSION['corpus']['nav_history']['path'][] = $current;
		}
		
		if( count( $_SESSION['corpus']['nav_history']['path'] ) > 30 ) {
			array_shift( $_SESSION['corpus']['nav_history']['path'] );	
		}
		
	}
	
	function prev( $get = false ) {
		for($i = count($_SESSION['corpus']['nav_history']['path']) - 1; $i >= 0; $i = $i - 1 ) {
			$page = $_SESSION['corpus']['nav_history']['path'][$i]['page'];
			if( $page != href() ) {
				return $page;
			}
		}
	}
	
}