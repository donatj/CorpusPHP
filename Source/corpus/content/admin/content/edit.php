<?



	if( !$shutup ) {

		$_id = (int)$_GET['cid'];

		if( count($_POST) > 0 ) {
			$success = true;
			$_id = (int)$_POST['categories_id'];
			$iu_data = array(
			'parent_id' => (int)$_POST['parent_id'],
			'name' => $_POST['name'],
			'description' => $_POST['description'],
			'large_description' => $_POST['large_description'],
			'details' => $_POST['details'],
			'main_image_id' => (int)$_POST['main_image_id'],
			'listing_image_id' => (int)$_POST['listing_image_id'],
			'main_image_section' => $_POST['main_image_section'],
			'listing_image_section' => $_POST['listing_image_section'],
			'secondary_image_section_sort' => $_POST['secondary_image_section_sort'],
			'layout' => (int)$_POST['layout'],
			'meta_title' => $_POST['meta_title'],
			'meta_keywords' => $_POST['meta_keywords'],
			'meta_description' => $_POST['meta_description'],
			'redirect' => $_POST['redirect'],
			'url' => $_POST['url'],
			'bullet1'=> $_POST['li1'],
			'bullet2'=> $_POST['li2'],
			'bullet3'=> $_POST['li3'],
			'bullet4'=> $_POST['li4'],
			'bullet5'=> $_POST['li5'],
			'bullet6'=> $_POST['li6'],
			//		'cta_text' => $_POST['cta_text'],
			//		'cta_url' => $_POST['cta_url'],
			);

			if($_id > 0) {
				$success = $success && db::perform('categories', $iu_data, 'categories_id = ' . (int)$_id);
			}else{
				$success = $success && db::perform('categories', $iu_data);
				$_id = db::id();
				$success = $success && db::perform('categories', array('sort' => $_id), 'categories_id = ' . (int)$_id);
			}


			$x_data = _::data($_id);

			if( is_array( $_POST['image'] ) ) {
				foreach( $_POST['image'] as $img_id => $image ) {
					if(!$image['delete']) {
						$success = $success && db::perform( 'images', array(
						'section' => trim($image['section']),
						'description' => $image['description'],
						'alt' => $image['alt'],
						'link' => $image['link'],
						'sort' => (int)$image['sort'],
						),
						'images_id = ' . (int)$img_id
						);
					}else{
						$success = $success && db::query("delete from images where images_id = ".(int)$img_id);
					}
				}
				if($x_data['main_image_id'] == 0) {
					$x_data['main_image_id'] = (int)$img_id;
					$success = $success && db::perform('categories', array('main_image_id' => (int)$img_id), 'categories_id = ' . (int)$_id);
				}
			}

			if( is_array( $_POST['new_image'] ) ) {
				foreach( $_POST['new_image'] as $new_image ) {
					$success = $success && db::perform('images', array(
					'categories_id' => (int)$_id,
					'filename' => trim($new_image['filename']),
					'section' => trim($new_image['section']),
					'description' => $new_image['description'],
					'alt' => $new_image['alt'],
					));
					$img_id = db::id();
				}
				if($x_data['main_image_id'] == 0) {
					$x_data['main_image_id'] = $img_id;
					$success = $success && db::perform('categories', array('main_image_id' => $img_id), 'categories_id = ' . $_id);
				}
			}

			if( is_array( $_POST['document'] ) ) {
				foreach( $_POST['document'] as $doc_id => $document ) {
					if(!$document['delete']) {
						$success = $success && db::perform( 'documents', array(
						'description' => $document['description'],
						'sort' => (int)$document['sort'],
						),
						'documents_id = ' . (int)$doc_id
						);
					}else{
						$success = $success && db::query("delete from documents where documents_id = ".(int)$doc_id);
					}
				}
			}

			if( is_array( $_POST['new_document'] ) ) {
				foreach( $_POST['new_document'] as $new_document ) {
					$success = $success && db::perform('documents', array(
					'categories_id' => (int)$_id,
					'filename' => trim($new_document['filename']),
					'description' => $new_document['description'],
					));
				}
			}

			if( $success ) {
				$_ms->add('Page &ldquo;'.$_POST['name'].'&rdquo; Successfully Modified');
			}else{
				$_ms->add('An Indistinct Error Occured While Modifying The Page');
			}
		}



		if( $_id > 0 ) {
			$_data = _::data( (int)$_id );
			$_dataE = htmlE( $_data );
		}else{
			//presets for new
			$_data['layout'] = 1;
		}

		$_meta['title'] = 'Editing ' . $_data['name'];
	?>
	<script type="text/javascript" src="js/tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript" src="js/admin_edit.js"></script>
	<script type="text/javascript">
		tinyMCE.init({
			mode : 'exact',
			elements: 'large_description',
			theme : "advanced",
			plugins : 'jimage,table',
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontsizeselect",
			theme_advanced_buttons3 : "table,hr,jimage,removeformat,visualaid,|,sub,sup,|,charmap,|,insertlayer,moveforward,movebackward,absolute",
			theme_advanced_toolbar_location : "top",
			document_base_url : <?= json_encode(DWS_BASE) ?>
		});

		tinyMCE.init({
			mode : 'exact',
			elements: 'description, details',
			theme : "simple",
			document_base_url : <?= json_encode(DWS_BASE) ?>
		});

		var sections = <?= db::fetchJson("select distinct section from images where char_length(section) > 2 order by section", 'flat', true) ?>;
		var images = <?= json_encode( getImagesArray() ) ?>;
		var documents = <?= json_encode( getDocumentsArray() ) ?>;
		var url_regex = new RegExp(<?= json_encode( SEO_URL_PATTERN ) ?>);
	</script>

	<? if($_id > 0) { ?>
		<a href="<?= href($_id) ?>" target="_blank" style="float:right">View Page (New Window)</a>
		<? } ?>
	<form method="post" action="<?= $_meta['page']['path'] ?>">
		<h3>Layout</h3>
		<div style="font-size: 9px">
			<?
				if($_id > 0){ echo fe::HiddenField('categories_id',$_id); }

				$layouts = array(
				-1 => array( 'post_label' => 'Pass Through' ),
				-2 => array( 'post_label' => 'Redirect', 'label_post' => '' ),
				1 => array( 'post_label' => '<img src="images/admin/layout1.gif" /><br />Page' ),
				2 => array( 'post_label' => '<img src="images/admin/layout2.gif" /><br />Product Listing' ),
				3 => array( 'post_label' => '<img src="images/admin/layout5.gif" /><br />Product' ),
				);
				echo fe::RadioButtonsFromArray($layouts, 'layout', $_data['layout'], 'class="layout_radio"');
			?>
		</div>
		<br clear="all" />
		<hr />
		<table width="100%"><tr><td>

					<h3>Page Details</h3>
					<label>Name</label><?= fe::Textbox('name', $_data['name'], 'id="name"') ?>
					<br clear="all" />

					<label>Parent Page</label><?= fe::DropdownFromSql("Select categories_id, name from categories where categories_id <> 130 Order By parent_id, sort",'parent_id', $_data['parent_id'], '', "Left Nav Root") ?>
					<br clear="all" />

					<span class="hide" rel="[-2]">
						<label>Redirect to:</label><?= fe::Textbox('redirect', $_data['redirect']) ?>
						<br clear="all" />
					</span>

				</td><td>

					<h3>SEO</h3>
					<label>Title</label><?= fe::Textbox('meta_title', $_data['meta_title']) ?>
					<br clear="all" />

					<label>Keywords</label><?= fe::Textbox('meta_keywords', $_data['meta_keywords']) ?>
					<br clear="all" />

					<label>Description</label><?= fe::Textbox('meta_description', $_data['meta_description']) ?>
					<br clear="all" />

					<label>URL</label><?= fe::Textbox('url', $_data['url'], 'id="url"') ?>
					<br clear="all" />
					<label>&nbsp;</label><span id="url_text"></span>
				</td></tr></table>

		<hr />

		<table width="100%">
			<td width="38%">
				<div>
					<h3>Category Level Description</h3>
					<textarea id="description" name="description" rows="5" cols="70" style="width: 100%"><?= $_dataE['description'] ?></textarea>
				</div>
				<div class="hide" rel="[3]">
					<h3>List Items</h3>
					Item 1: <input type="text" name="li1" value="<?= $_dataE['bullet1'] ?>" size="40" /><br />
					Item 2: <input type="text" name="li2" value="<?= $_dataE['bullet2'] ?>" size="40" /><br />
					Item 3: <input type="text" name="li3" value="<?= $_dataE['bullet3'] ?>" size="40" /><br />
					Item 4: <input type="text" name="li4" value="<?= $_dataE['bullet4'] ?>" size="40" /><br />
					Item 5: <input type="text" name="li5" value="<?= $_dataE['bullet5'] ?>" size="40" /><br />
					Item 6: <input type="text" name="li6" value="<?= $_dataE['bullet6'] ?>" size="40" />
				</div>
				<!-- div class="hide" rel="[0]">
					<h3>Details</h3>
					<textarea id="details" name="details" rows="15" cols="70" style="width: 100%"><?= $_dataE['details'] ?></textarea>
				</div -->

			</td>
			<td>
				<h3>Large Description</h3>
				<textarea id="large_description" name="large_description" rows="25" cols="70" style="width: 100%"><?= $_dataE['large_description'] ?></textarea>
			</td></table>


		<div class="hide" rel="[2,3,4,5,6]" style="clear:both">
			<hr />
			<table width="100%" class="hide" rel="[5]">

			<tr><td width="725">
				<h3>Documents</h3>
				<div class="adminImages">
					<?
						$docQry = db::query("Select * from documents where categories_id = ".(int)$_id." order by sort");
						$i = 0;
						while( $docRow = mysql_fetch_array($docQry) ) {
							$i++;
							echo '<div style="width: 200px; float:left">';
							echo $docRow['filename'];
							echo '<br />description<br />' . fe::Textbox('document[' . $docRow['documents_id'] . '][description]', $docRow['description']);
							echo '<br />sort<br />' . fe::Textbox('document[' . $docRow['documents_id'] . '][sort]', $i, 'class="sort"');
							echo '<br>' . fe::Checkbox('document[' . $docRow['documents_id'] . '][delete]') . 'delete document';
							echo '</div>';
						}
					?>
				</div>
			</td>
			<td width="265" class="addFiles">
				<fieldset style="height: 145px"><legend>Existing Documents</legend>
					<small>Start Typing to Find Document</small>
					<?= fe::Textbox('existing_document', '', 'id="existing_document"') ?>
					<small>description</small>
					<?= fe::Textbox('existing_document_description','', 'id="existing_document_description"') ?>
					<br />
					<input type="button" id="addDocumentButton" value="Add Document" />
				</fieldset>
			</td><td width="265" class="addFiles">
			<fieldset  style="height: 145px"><legend>Upload Document</legend>
				<iframe src="admin/upload.php?what=document" style="border: 0; height: 84px; width: 240px;" scrolling="no">Uploading Requires IFrame Support</iframe>
			</fieldset>

		</div>
		</td>
		</tr>

		</table>
		<fieldset id="newImageHolder"><legend>Files to be Added at Product Update</legend></fieldset>
		<table width="100%">
			<tr><td width="725">

					<h3>Images Sections</h3>
					<div class="adminImages">
						<?
							$imgQry = db::query("Select * from images where categories_id = ".(int)$_id." order by " . sectionLiker( $data['main_image_section'] ) . ", ".sectionLiker('room').", " . sectionSortLiker($_data['secondary_image_section_sort']) . " section, sort");
							$i = 0;
							$oldSection = false;
							while( $imgRow = mysql_fetch_array($imgQry) ) {
								$i++;
								if($oldSection != $imgRow['section']) {
									$i = 0;
									echo '<br clear="all" /><h2>' . $imgRow['section'] . '</h2>';//the br is because IE is stupid
								}elseif($i % 4 == 0){
									echo '<br clear="all" />';
								}
								echo '<div style="width: 160px; float:left">';
								//print_r($imgRow);
								echo fe::Textbox('image[' . $imgRow['images_id'] . '][section]', $imgRow['section'], 'class="greyed"');
								echo sImgTag($imgRow['images_id'], 140,'','w','style="padding-bottom: 3px;"');
								echo '<br />alt<br />' . fe::Textbox('image[' . $imgRow['images_id'] . '][alt]', $imgRow['alt']);
								echo '<br />description<br />' . fe::Textbox('image[' . $imgRow['images_id'] . '][description]', $imgRow['description']);
								echo '<br />link<br />' . fe::Textbox('image[' . $imgRow['images_id'] . '][link]', $imgRow['link'], 'class="image_link"');
								echo '<br />sort<br />' . fe::Textbox('image[' . $imgRow['images_id'] . '][sort]', $i,'class="sort"');
								echo '<br>' . fe::Checkbox('image[' . $imgRow['images_id'] . '][delete]') . 'delete image';
								echo '<br>' . fe::RadioButton('main_image_id', $_data['main_image_id'] == $imgRow['images_id'], $imgRow['images_id'],'style="width: auto"' ) . 'main image';
								echo '<br>' . fe::RadioButton('listing_image_id', $_data['listing_image_id'] == $imgRow['images_id'], $imgRow['images_id'],'style="width: auto"' ) . 'listing image';
								echo '</div>';
								echo "\n";
								$oldSection = $imgRow['section'];
							}
						?>
					</div>

				</td>
				<td class="addFiles">
					<div>
						<h3>Add Images</h3>
						<fieldset><legend>Existing Image</legend>
							<img src="images/admin/blank.gif" id="existing_image_img" />
							<br clear="all" />
							<small>Start Typing to Find Image</small>
							<?= fe::Textbox('existing_image', '', 'id="existing_image"') ?>
							<small>section</small>
							<?= fe::Textbox('existing_image_section', '', 'id="existing_image_section"') ?>
							<small>alt</small>
							<?= fe::Textbox('existing_image_alt', '', 'id="existing_image_alt"') ?>
							<small>description</small>
							<?= fe::Textbox('existing_image_description','', 'id="existing_image_description"') ?>
							<br />
							<input type="button" id="addImageButton" value="Add Image" />
						</fieldset>

						<fieldset><legend>Upload Image</legend>
							<iframe src="admin/upload.php?what=image" style="border: 0; height: 84px; width: 240px;" scrolling="no">Uploading Requires IFrame Support</iframe>
						</fieldset>

						<fieldset><legend>Image Settings</legend>
							<small>main image section</small>
							<?= fe::Textbox('main_image_section', $_data['main_image_section'], 'id="main_image_section"') ?>
							<small>listing image section</small>
							<?= fe::Textbox('listing_image_section', $_data['listing_image_section'], 'id="listing_image_section"') ?>
							<small>secondary listing image sort (advanced)</small>
							<?= fe::Textbox('secondary_image_section_sort', $_data['secondary_image_section_sort'], 'id="secondary_image_section_sort"') ?>
						</fieldset>
					</div>
				</td>
			</tr></table>

		</div>

		<div id="bottomSticker">
			<?
				if($_id > 0) {
					echo '<strong style="float:left; color: #333">&nbsp;&nbsp;Editing &ldquo;'.$_data['name'].'&rdquo;</strong>';
				}else{
					echo '<strong style="float:left; color: #333">&nbsp;&nbsp;New Page</strong>';
				}
			?>
			<input type="button" value="Cancel" onclick="if( confirm('Are you sure you want to cancel?') ) { document.location = admin_root; }" />
			<input type="submit" id="update_page" value="<?= $_id > 0 ? 'Update' : 'Create' ?> Page">&nbsp;
		</div>
	</form>
	<?
}