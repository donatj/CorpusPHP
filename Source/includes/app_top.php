<?
error_reporting(E_ALL ^ E_NOTICE);
require_once('includes/configure.php');
require_once(DWS_INCL . 'init.php');

$_ms = new MessageStack;

require_once(DWS_CLASS . 'Corpus.php');
require_once(DWS_CLASS . 'Core.php');
require_once(DWS_CLASS . 'Database.php');
require_once(DWS_CLASS . 'FormElements.php');
require_once(DWS_CLASS . 'FormUtility.php');

require_once(DWS_FUNC . 'error.php');
require_once(DWS_FUNC . 'general.php');
require_once(DWS_FUNC . 'corporeal.php');

$_db  = new Database;
$_cfg = new Configuration;
$_cfg->PAGELOADS += 1;

new Corpus;
$_nh = new NavigationHistory;
$_lg = new Login;

new Core;