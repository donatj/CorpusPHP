<?php
$_meta['title'] = 'Contact Us';

if( !$shutup ) :
//hide from sitemap
$_meta['header'] = '<link rel="stylesheet" href="css/form.css" type="text/css" charset="utf-8" />';

$x_message_stack = new MessageStack('contact_us');
$thanks_stack = new MessageStack('thanks');

if($_POST) {
	$formUtil = new FormUtility;
	$formUtil->requiredFields(array('name'));
	$formUtil->validateEmail('email');

	if(!isset($formUtil->errorMessage)) {

		$mail = new PHPMailer();
		$mail->IsHTML(true);
		$mail->From = $formUtil->filtered['email']; // the email field of the form
		$mail->FromName = $formUtil->filtered['name']; // the name field of the form
		$mail->AddAddress(GENERIC_FROM_EMAIL); // the form will be sent to this address
		$mail->Subject = 'Contact Us Form Submission'; // the subject of email

		$mail->Body = 	'<table cell-padding="10" cell-spacing="0">' . "\n" .
			'	<tr><td><strong>Name</strong></td><td style="padding-left:25px;">' . $formUtil->html['name'] . '</td></tr>' . "\n" .
			'	<tr><td><strong>Email</strong></td><td style="padding-left:25px;">' . $formUtil->html['email'] . '</td></tr>' . "\n" .
			'	<tr><td><strong>Comments</strong></td><td style="padding-left:25px;">' . $formUtil->html['comments'] . '</td></tr>' . "\n" .
		$mail->Body .= '</table>' . "\n";

		db::perform('FormContact', array(
			'name' => $_POST['name'],
			'email' => $_POST['email'],
			'comments' => $_POST['comments'],
		));

		if($mail->Send()) {
			$thanks_stack->add( 'Thank you for Contacting Us' );
			redirect('thank_you.php?contact_us');
		}else{
			$x_message_stack->add( 'Error, Please Try Again', true );
		}


	}else{
		$x_message_stack->add( $formUtil->errorMessage, true );
	}

}
?>

<?= $x_message_stack->draw() ? '<br clear="all" />' : '' ?>

<div style="float: right; width: 320px; text-align: right">

<br />
<?= crux('PLATITUDE') ?>
</div>
<div style="float: left">
<form method="post" action="<?= href() ?>">

	<h1>Contact Us</h1>
	<br clear="left"/>

	<label class="required">Name:</label>
	<?= fe::Textbox('name', $_POST['name']) ?>
	<br clear="left"/>

	<label class="required">Email:</label>
	<?= fe::Textbox('email', $_POST['email']) ?>
	<br clear="all"/>

	<label>Comments:</label>
	<textarea name="comments" cols="30" rows="8"><?= @htmlE($_POST['comments']) ?></textarea>
	<br clear="all"/>

	<label>&nbsp;</label>
	<?= button('Contact Us', true) ?>
	<br clear="all"/>

<?= fe::HiddenField('Wolfsbane', '') ?>
</form>
</div>
<script type="text/javascript">
window.addEvent('domready', function() {
	$$('input, textarea').addEvent('change', function(){
		$$('input[name=Wolfsbane]').set('value','howl');
	})
});
</script>

<?php
endif;