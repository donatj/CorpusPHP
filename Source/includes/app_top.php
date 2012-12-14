<?
error_reporting(E_ALL ^ E_NOTICE);
require_once('includes/configure.php');
require_once(DWS_INCL . 'init.php');

$_ms = new MessageStack;

require_once(DWS_CORPUS . 'Corpus.php');
require_once(DWS_CORPUS . 'Core.php');
require_once(DWS_CORPUS . 'Database.php');
require_once(DWS_CORPUS . 'FormElements.php');

require_once(DWS_FUNC . 'error.php');
require_once(DWS_FUNC . 'general.php');
require_once(DWS_FUNC . 'corporeal.php');

$_cfg = new Configuration;
$_cfg->PAGELOADS += 1;

date_default_timezone_set($_cfg->DEFAULT_TIMEZONE);

new Corpus;
$_nh = new NavigationHistory;
$_lg = new Login;

new Core;