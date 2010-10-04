<?= co::layout('head', false, 'main') ?>
<link rel="stylesheet" href="css/admin.css" type="text/css" charset="UTF-8" />
<link rel="stylesheet" href="css/form.css" type="text/css" charset="UTF-8" />
<link rel="stylesheet" href="css/Autocompleter.css" type="text/css" charset="UTF-8" />
<script type="text/javascript" src="js/Autocompleter.js"></script>
<script type="text/javascript" src="js/Autocompleter.Local.js"></script>
<script type="text/javascript" src="js/Observer.js"></script>

<script type="text/javascript">
var admin_root = <?= json_encode(DWS_ADMIN) ?>;
var urls = <?= db::fetchJson("select distinct trim(url) from categories where list = 1 and categories_id <> " . (int)$GLOBALS['_id'], 'flat', true) ?>;
window.addEvent('domready', function() {
	if( $('quick_edit') ){ new Autocompleter.Local($('quick_edit'), urls, { 'autoSubmit': true }); }
});

</script>