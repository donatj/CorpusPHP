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


$comments = db::fetch( "Select * From comments Where enabled And grouping='".db::input($group)."' And grouping_id = " . (int)$group_id );

//$default = "http://oasisband.net/images/tear.png";
$size = 48;


?>
<div id="Comments">
<? 
foreach( $comments as $comment ) { 
	$ts = strtotime( $comment['comment_date'] );
	
	$grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=" . 
		md5( strtolower( $comment['email'] ) ). "&default=" . 
		urlencode($default) . "&s=".$size . '&r=x';
	
?>
	<a name="Comment<?= $comment['comment_id'] ?>"></a>
	<h3>Comment by: <strong><?= $comment['name'] ?></strong> on <dfn title="<?= date('r', $ts) ?>"><?= date('M jS, Y', $ts) ?></dfn></h3>
	<div class="Comment"><img src="<?= $grav_url ?>" /><?= $comment['comment'] ?><br clear="all" /></div>
<? 
} 
?>
<form method="post" action="<?= href() ?>#MakeComment">
<? $_cs->draw(); ?>
<fieldset>
	<div class="column">
		
		<label class="required">Name</label>
		<?= fe::Textbox("Comment[name]", $_POST['Comment']['name'] ) ?>
		<br clear="all" />
		
		<label>Email</label>
		<?= fe::Textbox("Comment[email]", $_POST['Comment']['email'] ) ?>
		<br clear="all" />
		<br clear="all" />
		
		<label>&nbsp;</label>
		<?= button('Post Comments', true) ?>
		
	</div>
	<div class="column last">
		<?= fe::Textarea( 'Comment[comment]', $_POST['Comment']['comment'] ) ?>
	</div>
	<a name="MakeComment"></a>
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
