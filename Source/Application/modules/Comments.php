<?

if( !$shutup ) :

$group_id = $data[0];
$group = firstNotEmpty( $data[1], 'default' );
$_cs = new MessageStack('CommentStack');

if( is_array($_POST['Comment']) ) {
	/**
	* @todo Create and use some kind of class better than the FormUtility.
	*/
	if( trim( $_POST['Comment']['name'] ) && trim( $_POST['Comment']['comment'] ) ) {
	
		$cdata = array(
			'name' => $_POST['Comment']['name'],
			'email' => $_POST['Comment']['email'],
			'comment' => $_POST['Comment']['comment'],
			'comment_date' => array(true, 'now()'),
			'comment_ip' => $_SERVER['REMOTE_ADDR'],
			'enabled' => $_POST['Wolfsbane'] == 'howl',
			'grouping' => $group,
			'grouping_id' => $group_id,
		);
	
		if( db::perform('comments', $cdata) ) {
			if( $cdata['enabled'] ) {
				$_cs->add('Your Comment has Successfully Been Added');
				_cu('#Comment' . db::id() );
			}else{
				$_cs->add('Your Comment has Successfully Been Added and is Awaiting Approval');
				_cu('#MakeComment' );			
			}
		}else{
			$_cs->add('Error Posting Comments');
		}
	
	}else{
		$_cs->add('Please Complete Name and Comments', true);
	}
	
}

/*
$comments = db::fetch( "Select c.*, u.access From comments c Left Join users u Using( user_id ) Where enabled And grouping='".db::input($group)."' And grouping_id = " . (int)$group_id . " Order By comment_date ASC" );
*/


$comments = db::fetch("Select c.*, u.access From comments c Left Join users u Using( user_id ) Where enabled And grouping='".db::input($group)."' And grouping_id = " . (int)$group_id . " Order By parent_id, comment_date ASC");

foreach( $comments as $comment ) {
	$comment_data[ $comment['comment_id'] ]['data'] = $comment;
	$comment_data[ $comment['parent_id'] ]['children'][ $comment['comment_id'] ] =& $comment_data[ $comment['comment_id'] ];
	$comment_data[ $comment['comment_id'] ]['parent'] =& $comment_data[ $comment['parent_id'] ];
}

if( !function_exists('draw_comment_tree') ) {
	function draw_comment_tree($comment_data, $class_prefix = "nav_", $root_id = "nav", $comment_id = 0, $depth = 0) {
	
		$src = '';
		if(is_array( $comment_data[$comment_id]['children'] )) {

			foreach( $comment_data[$comment_id]['children'] as $comment ) {
	
				$csrc = draw_comment_tree( $comment_data[$comment_id]['children'], $class_prefix, $root_id, $comment['data']['comment_id'], $depth + 1 );
				$ts = strtotime( $comment['data']['comment_date'] );
				
				$grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=" . 
					md5( strtolower( firstNotEmpty($comment['data']['email'], $comment['data']['comment_ip']) ) ). "&amp;default=" . 
					urlencode("retro") . '&amp;s=64&amp;r=x';
					
				$src .= '<div'.($depth > 0 ? ' style="margin-left: '.($depth * 22).'px;"' : '' ).' id="Comment' . $comment['data']['comment_id'] .'">';
				$src .= '<h3>Comment by: <strong>' . $comment['data']['name'] .'</strong> on <time class="entryDate" title="' . date(DATE_W3C, $ts) . '" datetime="' . date(DATE_W3C, $ts) . '">' . date( DISPLAY_DATE_FORMAT , $ts) . '</time></h3>';
				$src .= '<div class="Comment' . ( $comment['data']['access'] ? ' CommentsUser_' . $comment['data']['access'] : '' ) . '"><img alt="' . htmlE( $comment['data']['name'] ).' Gravatar" src="'. $grav_url .'" />' . nl2br($comment['data']['comment']) .'<br style="clear: both;" /></div>';
				$src .= '</div>';

				$src .= $csrc;
			}
		}
		return $src;
	}
}



?>
<div id="Comments">
<? 
echo draw_comment_tree($comment_data);
?>
<form method="post" action="<?= href() ?>#MakeComment">
<? $_cs->draw(); ?>
<fieldset id="MakeComment">
	<div class="column">
		
		<label class="required">Name</label>
		<?= fe::Textbox("Comment[name]", $_POST['Comment']['name'] ) ?>
		<br style="clear: both;" />
		
		<label>Email</label>
		<?= fe::Textbox("Comment[email]", $_POST['Comment']['email'] ) ?>
		<br style="clear: both;" />
		<br style="clear: both;" />
				
	</div>
	<div class="column last">
		<?= fe::Textarea( 'Comment[comment]', $_POST['Comment']['comment'] ) ?>
	</div>
	<label>&nbsp;</label>
	<?= button('Post Comments', true) ?>
</fieldset>
<?= fe::HiddenField('Wolfsbane', '') ?>
</form>
</div>
<script type="text/javascript">
window.addEvent('domready', function() {
	$$('#Comments fieldset input, #Comments fieldset textarea').addEvent('change', function(){
		$$('input[name=Wolfsbane]').set('value','howl');
	})	
});
</script>
<?
endif;
