<?

$_meta['raw'] = true;

if( !$shutup ) {
if( $_FILES['uploadfile']['error'] === 0 ) { //note the ===
	if($_GET['what'] == 'document') {
		$upath = 'documents/';
	}elseif($_GET['what'] == 'image'){
		$upath = 'images/';
	}else{
		$error = true;
	}
	if( !$error && move_uploaded_file($_FILES['uploadfile']['tmp_name'], $upath . $_FILES['uploadfile']['name'] ) ) {
		$uploadfile = $_FILES['uploadfile']['name'];
	}else{
		$error = true;
	}
}
?>
<?= DOCTYPE ?>
<html <?= HTMLATTR ?>>
<head>

<script type="text/javascript">
<? if( strlen($uploadfile) > 3 && !$error ) { ?>
parent.uploadCatcher(<?= json_encode($uploadfile) ?>,<?= json_encode($_GET['what']) ?>);
<?
}
if($error) { ?> alert('Error Uploading File!');<? } ?>
</script>

</head>
<body style="text-align: center;">
	<form method="post" enctype="multipart/form-data">
		<input type="file" name="uploadfile"/>
		<br /><br />
		<input type="submit" value="Upload" />
	</form>
</body>
</html>
<?
}