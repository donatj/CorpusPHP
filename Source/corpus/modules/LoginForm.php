<?

$_meta['name'] = 'LoginForm';
$_meta['callable'] = true;

if( !$shutup ) :

$static['count']++;
$unID = 'username' . $static['count'];
$pwID = 'password' . $static['count'];

?>

<span class="accountForm">

<fieldset class="accountLogin" id="<?= $data[0] == 'free' ? 'lgFree' : 'lgInst' ?>">
<h4><?= $data[0] == 'free' ? 'Freelancer' : ( nempty( $data[0] ) ? 'Institution' : '') ?> Sign In</h4>
	<div class="loginInner">

		<form method="post" action="<?= href( href() . '?login', true, false, true ) ?>" onsubmit="if( $('<?= $unID ?>').get('value').trim()=='' ||  $('<?= $pwID ?>').get('value').trim()=='' ) { /* event.stop(); */ }" id="freelancer_login<?= $static['count'] ?>">

			<label>Email:</label>
			<?= fe::Textbox('username', true, 'id="'.$unID.'" style="width:115px;"') ?>
			<br clear="all"/>

			<label>Password:</label>
			<?= fe::Password('password', true, 'id="'.$pwID.'" style="width:115px;"') ?>
			<br clear="all"/>
			<label>&nbsp;</label>
			<?= button( 'Sign In »', true ) ?>

			<br clear="all">
			<br clear="all">

			<label style="width: 55px;">&nbsp;</label>
			<small style="float: left;">
				<a href="forgot_password.php">Forgot your Password? Click Here.</a>
			</small>

		</form>
	</div>
</fieldset>

</span>

<?
endif;