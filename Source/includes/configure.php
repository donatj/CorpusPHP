<?

if( file_exists( '../Local/configure.php' ) ) :

	include( '../Local/configure.php' );

else:
	
	define('DB_HOST', 'localhost');
	define('DB_USER', '--username--');
	define('DB_PASSWORD', '--password--');
	define('DB_DATABASE', '--database--');
	define('DFS_DB_BACKUP', 'admin/db_backups/');
	if( strtolower($_SERVER['HTTPS']) == 'on' ) {
		define('DWS_BASE', 'http://' . $_SERVER['HTTP_HOST'] . '/');
	}else{
		define('DWS_BASE', 'http://' . $_SERVER['HTTP_HOST'] . '/');
	}
	define('DWS_ADMIN', DWS_BASE . 'admin/');
	define('DWS_DOMAIN', 'http://' . $_SERVER['HTTP_HOST']);

	define('DWS_INCL', 'includes/');
	define('DWS_CLASS', DWS_INCL . 'classes/');
	define('DWS_DBO', DWS_INCL . 'dbo/');
	define('DWS_FUNC', DWS_INCL . 'functions/');

	define('DWS_CORPUS',    'corpus/');
	define('DWS_LAYOUT',    DWS_CORPUS . 'layout/');
	define('DWS_TEMPLT',    DWS_CORPUS . 'templates/');
	define('DWS_CONTENT',   DWS_CORPUS . 'content/');
	define('DWS_DATABASES', DWS_CORPUS . 'databases/');
	define('DWS_MODULES',   DWS_CORPUS . 'modules/');

	define('STORE_OWNER_EMAIL_ADDRESS', 'noreply@donatstudios.com');
	define('STORE_GENERIC_FROM', 'noreply@donatstudios.com');
	
	$__autoload_paths = array( DWS_DATABASES, DWS_CLASS, DWS_DBO );

endif;