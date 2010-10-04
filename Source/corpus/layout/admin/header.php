<div id="header">
	<!-- UPPER NAVIGATION GOES HERE (Home | New Name | Info) id="uppernav" -->
	<!-- TOP NAVIGATION GOES HERE id="topnav" -->
	<div id="topnav">
		<ul>
			<li class="nopadding"><a href="<?= href() ?>?backup" >Back Up Database <br /><small>Last: <?= DB_LAST_BACKUP ?></small></a></li>
			<li><a href="admin/content">Admin Home</a></li>
			<li><a href="admin/content/edit.php" id="newPage">New Page</a></li>
			<li><a href="admin/files.php">Images / Documents</a></li>
			<li><a href="admin/?logout">Logout</a></li>
		</ul>
	</div>
	<div id="logo">
		<a href="<?= DWS_BASE ?>/admin/content"><img src="images/site/logo.png" alt="<?= STORE_NAME ?>" id="logo" /></a>
	</div>
</div>