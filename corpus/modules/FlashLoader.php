<?

$_meta['call'] = 'FlashLoader';

if( !$shutup ) :

$id = 'FlashLoader' . uniqid();
?>
<div class="flashload" id="<?= $id ?>">Placeholder For <?= $data[0] ?></div>
<script type="text/javascript">
window.addEvent('load', function() {
	new Swiff('flash/<?= $data[0] ?>', {'container':<?= json_encode($id) ?>,'width':<?= (int)$data[1] ?>,'height':<?= (int)$data[2] ?>});
});
</script>
<?	

endif;