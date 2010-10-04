<?
if( !$shutup ) {
	$start = microtime(true);
	if($_GET['type'] == 'docs'){
		$fs_f = getDocumentsArray();
	$db_f = db::fetch("SELECT filename, categories_id FROM documents ORDER BY filename", 'keyvalues');
		$fpath = 'documents/';
	}else{
		$fs_f = getImagesArray();
	$db_f = db::fetch("SELECT filename, categories_id FROM images ORDER BY filename", 'keyvalues');
		$fpath = 'images/';
	}

	$db_f = array();

	$categories = db::fetch("SELECT categories_id, name FROM categories", 'keyvalue');

	if( $_GET['page'] < 1 ) $_GET['page'] = 1;

	function paginator($total, $perpage) {
		$max = (int)($total / $perpage) + 1;
		if( $_GET['page'] > $max ) $_GET['page'] = $max;
		echo '<fieldset><form action="'.href().'">';
		echo fe::DropdownFromArray(array('imgs' => 'Images','docs' => 'Documents'), 'type', $_GET['type'], 'onchange="this.form.submit()"');
		echo '&nbsp;&nbsp;&nbsp;';
		if( $_GET['page'] - 1 >= 1 ) {
			echo '<a href="'.href(). '?page='. ($_GET['page'] - 1) .'&'. getvarsSerializer('page') .'">&lArr; Prev</a>&nbsp;&nbsp;Page:';
		}
		echo fe::NumericRangeDropdown(1, $max,'page', $_GET['page'], 'onchange="this.form.submit()"');
		if( $_GET['page'] + 1 <= $max ) {
			echo '&nbsp;&nbsp;<a href="'.href().'?page='. ($_GET['page'] + 1) .'&'. getvarsSerializer('page') .'">Next &rArr;</a>';
		}
		echo '&nbsp;&nbsp;&nbsp;<label class="s">' . fe::Checkbox('unused',isset($_GET['unused']),1, 'onchange="this.form.submit()"') . 'Only Display Unused</label>';
		echo fe::Textbox('search', $_GET['search'], 'class="imageSearch" style="width: 275px"') . '<input type="submit" value="Search" />';
		echo '</form></fieldset>';
	}

	function searchCallback($var) { return stripos($var, $_GET['search']) !== false; }

	if( strlen($_GET['drop']) > 0 ) {
		$g = getvarsSerializer('drop');
		$_ms->add(sImgTag($_GET['drop'],100, false, 'h','style="float: left;"').'Caution, are you sure you want to delete the file &ldquo;'.htmlE($_GET['drop']).'&rdquo;?
		<p><a href="'.href().'?drop_sure='.urlencode($_GET['drop']).'&'.$g.'">Yes</a>
		&mdash; <big><a href="'.href().'?'.$g.'">No</a></big></p>', true);
		_cu( $g );
	}

	if( strlen($_GET['drop_sure']) > 0 ) {

		if( @unlink( $fpath . $_GET['drop_sure'] ) ) {
			$_ms->add('Successfully Deleted ' . $_GET['drop_sure']);
		}else{
			$_ms->add('Error Deleting ' . $_GET['drop_sure'], true);
		}

		_cu( getvarsSerializer('drop','drop_sure') );

	}

?>
<script>
	var images = <?= json_encode( $fs_f ); ?>
	window.addEvent('domready', function() {
		$$('.imageSearch').each(function(el){
			new Autocompleter.Local( el, images, {
				minLength: 2,
				injectChoice: function(token){
					var choice = new Element('li');
					new Element('span', {'html': this.markQueryValue(token)}).inject(choice);
					new Element('img', {
						'src': 'images/h/40/' + token,
						'alt': token,
						'styles':{'float':'right'}
					}).inject(choice);
					new Element('br',{'styles':{'clear':'right'}}).inject(choice);
					choice.inputValue = token;
					this.addChoiceEvents(choice).inject(this.choices);
				}
			});
		});
	});
	function uploadCatcher(filename, what) {
		$('uploadMsgs').set('text',what + ' ' + filename + " Uploaded successfully.  Refresh page to see in listing.");
		$('uploadMsgs').highlight("#f48827");
	}
</script>

<h1>Images / Documents</h1>

<a href="#" onclick="$('uploads').setStyle('display','block'); this.dispose(); return false;">Upload Files</a>

<div id="uploads" style="display: none;">
	<div id="uploadMsgs"></div>
	<fieldset style="width: 100px; float: left;"><legend>Upload Images</legend>
		<iframe src="admin/upload.php?what=image" style="border: 0; height: 84px; width: 240px;" scrolling="no">Uploading Requires IFrame Support</iframe>
	</fieldset>
	<fieldset style="width: 100px; float: left;"><legend>Upload Documents</legend>
		<iframe src="admin/upload.php?what=document" style="border: 0; height: 84px; width: 240px;" scrolling="no">Uploading Requires IFrame Support</iframe>
	</fieldset>
	<br clear="all"/>
</div>
<?

	if( isset($_GET['unused']) ) {
		$files = array_diff($fs_f, array_keys($db_f));
	}else{
		$files = $fs_f;
	}

	if(strlen(trim( $_GET['search'] ))) { $files = array_filter( $files, 'searchCallback' ); }

	$per_page = 90;

	$files = array_values($files);
	paginator( sizeof($files), $per_page );

	for($i = 0; $i < $per_page; ++$i) {
		$index = $i + (($_GET['page'] - 1) * $per_page);
		$file = $files[$index];

		if(!$file) { continue; }
		echo '<div style="align: center; width: 320px; float: left; margin-top: 10px;">';
		if( $_GET['type'] == 'docs' ) {
			echo '<div>';
		}else{
			echo boxImage( $file, 140,500 );
			echo '<div style="float:right; width: 160px">';
		}
		echo '<a href="' . href( $fpath . $file) . '" target="_blank">' . $file . '</a><br />';
		echo filesize( $fpath . $file) / 1000 . 'kb <br />';

		if( $db_f[ $file ] ) {
			echo '<strong>Used On</strong><br />';
			$_i = 0;
			foreach( $db_f[ $file ] as $cat ) {
				if($_i++) echo ', ';
					echo '<a href="admin/edit.php?id='.$cat.'">' . $categories[$cat] . '</a>';
				}

			}else{
				$ins = db::fetch("select categories_id from categories where large_description like '%" . db::input($file) . "%'", 'flat');

			if( $ins ) {
				echo '<strong>Embeded In</strong><br />';
				foreach( $ins as $cat ) {
					echo '<a href="admin/edit.php?id='.$cat.'">' . $categories[$cat] . '</a><br />';
				}
			}else{
				echo '<strong>Possibly Unused</strong><br />';
				echo '<a href="' . href().'?drop='. urlencode( $file ) .'&'. getvarsSerializer('drop') . '">Delete Image <img align="absmiddle" src="images/admin/drop.gif" alt="drop" /></a>';
						}
					}
		//echo '<strong>Possibly Unused</strong><br />';
		echo '</div>';
		echo '</div>';

		if(($i + 1) % 3 == 0) { echo '<br clear="all" /><hr />'; }

	}
	echo '<br clear="all" />';
	paginator( sizeof($files), $per_page );

?>
</div>
<?
}