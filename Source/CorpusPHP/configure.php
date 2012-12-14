<?php

if( file_exists( '../Local/configure.php' ) ) {
	include( '../Local/configure.php' );
}


define('DB_HOST',       'localhost');
define('DB_USER',       '--username--');
define('DB_PASSWORD',   '--password--');
define('DB_DATABASE',   '--database--');
define('DFS_DB_BACKUP', 'admin/db_backups/');

if( strtolower($_SERVER['HTTPS']) == 'on' ) {
	define('DWS_BASE', 'http://' . $_SERVER['HTTP_HOST'] . '/');
}else{
	define('DWS_BASE', 'http://' . $_SERVER['HTTP_HOST'] . '/');
}

define('DWS_ADMIN', DWS_BASE . 'admin/');
define('DWS_DOMAIN', 'http://' . $_SERVER['HTTP_HOST']);

define('DWS_INCL', 'includes/');
define('DWS_VENDOR', 'Vendor/');
define('DWS_CORPUS', 'CorpusPHP/');
	define('DWS_CLASSES', DWS_CORPUS . 'classes/');
	define('DWS_FILT',  DWS_CORPUS . 'filters/');
	define('DWS_FUNC',  DWS_CORPUS . 'functions/');

define('DWS_APP', 'Application/');
	define('DWS_DBO',       DWS_APP . 'dbo/');
	define('DWS_LAYOUT',    DWS_APP . 'layout/');
	define('DWS_TEMPLT',    DWS_APP . 'templates/');
	define('DWS_CONTENT',   DWS_APP . 'content/');
	define('DWS_DATABASES', DWS_APP . 'databases/');
	define('DWS_MODULES',   DWS_APP . 'modules/');

define('GENERIC_FROM_EMAIL',        'noreply@donatstudios.com');

$__autoload_paths = array( DWS_DATABASES, DWS_CLASSES, DWS_VENDOR, DWS_DBO );