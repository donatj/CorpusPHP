<? global $_data, $_meta //bad bad bad ?> 
<meta charset="utf-8" />
<? /* * Before you do any SEO Hacking, consult Jesse, 
	there is an elegant way to do anything you're trying, I built it, 
	and it is freaking awesome, don't screw it up in the name of laze * */ ?>
<title><?= DEFAULT_META_TITLE_PRE ?><?= htmlE( firstNotEmpty($_meta['title'], $_data['meta_title'], $_data['title'], $_data['name'], DEFAULT_META_TITLE) ) ?><?= DEFAULT_META_TITLE_POST ?></title>
<meta name="Description" content="<?= htmlE( firstNotEmpty($_meta['description'], $_data['meta_description'], DEFAULT_META_DESC) ) ?>" />
<meta name="Keywords" content="<?= htmlE( firstNotEmpty($_meta['keywords'], $_data['meta_keywords'], DEFAULT_META_KEYS) ) ?>" />
<base href="<?= DWS_BASE ?>" />

<link rel="stylesheet" href="css/main.css" type="text/css" media="screen,print" />
<link rel="stylesheet" href="css/print.css" type="text/css" media="print" />

<!--[if lte IE 8]>
<script src="js/html5.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="css/ielte8.css" />
<![endif]-->

<link rel="shortcut icon" href="favicon.ico" type="image/vnd.microsoft.icon" />
<link rel="alternate" type="application/rss+xml" title="RSS Feed" href="feed.rss"/>

<script src="http://ajax.googleapis.com/ajax/libs/mootools/1.4.5/mootools-yui-compressed.js" type="text/javascript"></script>
<script src="js/mootools.more.js" type="text/javascript"></script>
<script src="js/general.js" type="text/javascript"></script>
<?= $_meta['header'] //for extra special meta stuff, eg contact page ?>
<?= $_data['head'] //HEAD data from the content ?>

