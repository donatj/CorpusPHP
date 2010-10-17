<?

require_once('includes/configure.php');

//Load DBO --MUST BE BEFORE INIT--
require_once(DWS_DBO . 'User.php');

require_once(DWS_INCL . 'init.php');

require_once(DWS_CLASS . 'AssetStack.php');
require_once(DWS_CLASS . 'MessageStack.php');
$_ms = new MessageStack;

require_once(DWS_CLASS . 'Core.php');

include(DWS_FUNC . 'error.php');
error_reporting(E_ALL ^ E_NOTICE);
//set_error_handler( _errorHandler, E_ALL ^ E_NOTICE );

require_once(DWS_FUNC . 'general.php');
require_once(DWS_FUNC . 'corporeal.php');

require_once(DWS_CLASS . 'Database.php');
$_db = new Database;

require_once(DWS_CLASS . 'Cache.php');
require_once(DWS_CLASS . 'Configuration.php');
$_cfg = new Configuration;
$_cfg->PAGELOADS += 1;

require_once(DWS_CLASS . 'Corpus.php');
new Corpus;

require_once(DWS_CLASS . 'NavigationHistory.php');
$_nh = new NavigationHistory;

require_once(DWS_CLASS . 'Login.php');
$_lg = new Login;

if(isset($_GET['logout'])){
	$_nh->Reset('login');
	$_lg->Logout();
	redirect(DWS_BASE);
}

if(isset( $_GET['login'] )) {
	if( $_lg->attempt( $_POST['username'], $_POST['password'] ) ) {
		$url = $_nh->RestoreURL('login');
		if($url) {
			$_nh->Reset('login');
			redirect($url);
		}else{
			redirect('application');
		}
	}else{
		$_ms->add("Error Logging In", true);
		redirect('login.php');
	}
}

require_once(DWS_CLASS . 'SplitPageResults.php');

require_once(DWS_CLASS . 'Elements.php');
require_once(DWS_CLASS . 'FormElements.php');

new Core;