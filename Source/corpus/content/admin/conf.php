<?

$_meta['layout'] = 'admin';
$_meta['execModuleCalls'] = false;
$_lg->VerifyLoggedIn();

if( !($_lg->user instanceof Admin) ) {
	$_ms->add(GENERIC_PERM_ERROR, true);
	redirect( DWS_BASE );
}

//note add logic to ensure admin

if( isset($_GET['backup']) ) {
	if( db::backup() ) {
		$localCfg = new Configuration();
		$localCfg->DB_LAST_BACKUP = date("M j, y, g:i a");
		$_ms->add('Successfully Backed Up Database');
		redirect(href());
	}else{
		$_ms->add('Unsuccessful at Backing Up Database', true);
	}
}