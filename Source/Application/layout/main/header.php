<header>
	<div class="inner">
		<a href="<?= DWS_BASE ?>" id="logo">Corpus<span>PHP</span></a>
		<div id="headerbar">
			<?= draw_category_tree('header', 'nav') ?>
		</div>
		<a href="feed.rss" id="feed_icon"><img src="images/site/feed.png" alt="RSS Feed" title="Subscribe to our Blog" /></a>
		<?= breadcrumb( $_meta['id'] ) ?>
	</div>
</header>
<? $_ms->draw();