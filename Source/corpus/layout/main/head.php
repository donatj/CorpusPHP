<? global $_data, $_meta //bad bad bad ?> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<? /* * Before you do any SEO Hacking, consult Jesse, 
	there is an elegant way to do anything you're trying, I built it, 
	and it is freaking awesome, don't screw it up in the name of laze * */ ?>
<title><?= DEFAULT_META_TITLE_PRE ?><?= htmlE( firstNotEmpty($_meta['title'], $_data['meta_title'], $_data['title'], DEFAULT_META_TITLE) ) ?><?= DEFAULT_META_TITLE_POST ?></title>
<meta name="Description" content="<?= htmlE( firstNotEmpty($_meta['description'], $_data['meta_description'], DEFAULT_META_DESC) ) ?>" />
<meta name="Keywords" content="<?= htmlE( firstNotEmpty($_meta['keywords'], $_data['meta_keywords'], DEFAULT_META_KEYS) ) ?>" />
<base href="<?= DWS_BASE ?>" />

<link rel="stylesheet" href="css/main.css" type="text/css" media="screen,print" />
<link rel="stylesheet" href="css/form.css" type="text/css" media="screen,print" />
<link rel="stylesheet" href="css/print.css" type="text/css" media="print" />
<link rel="shortcut icon" href="favicon.ico" type="image/vnd.microsoft.icon" />
<link rel="alternate" type="application/rss+xml" title="RSS Feed" href="feed.rss"/>

<script src="js/mootools-1.2.4-core-yc.js" type="text/javascript"></script>
<script src="js/mootools-1.2.3.1-more.js" type="text/javascript"></script>
<script src="js/general.js" type="text/javascript"></script>
<?= $_meta['header'] //for extra special meta stuff, eg contact page ?>

