<?php /* Copyright 20xx Productions */
$includeDefaults['jquery-ui'] = false;

$includeParams = (is_array($includeParams)) ? array_merge($includeDefaults, $includeParams) : $includeDefaults; ?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<title><?php echo ($includes['meta']['title'] != '') ? $includes['meta']['title'] : SITE_TITLE; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="description" content="<?php echo ($includes['meta']['description'] != '') ? $includes['meta']['description'] : SITE_DESCRIPTION; ?>" />
<meta name="keywords" content="<?php echo ($includes['meta']['keywords'] != '') ? $includes['meta']['keywords'] : SITE_KEYWORDS; ?>" />
<meta name="author" content="<?php echo ($includes['meta']['author'] != '') ? $includes['meta']['author'] : SITE_AUTHOR; ?>" />
<meta name="robots" content="<?php echo ($includes['meta']['robots'] != '') ? $includes['meta']['robots'] : 'all'; ?>" />
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<meta name="copyright" content="<?php echo date('Y'); ?> <?php echo SITE_TITLE; ?>" />
<meta name="generator" content="TethysCMS <?php echo CORE_VERSION; ?>" />

<?php if ($includes['meta']['url'] != '') { ?>
	<link rel="canonical" href="<?php echo $includes['meta']['url']; ?>" />
<?php } ?>

<?php if (isset($includes['headers'])) {
echo $includes['headers'];
} ?>

<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
<?php /* Minify/Compress CSS Files */
$cssFiles = array('bootstrap.min.css');

if ($includeParams['jquery-ui']) {
	$cssFiles[] = 'jquery-themes/overcast/jquery-ui-1.8.17.custom.css';
}

$cssFiles[] = 'tethys.css';

if (file_exists(CORE_DIR_DEPTH.CORE_CSS_DIR.'local.css')) {
	$cssFiles[] = 'local.css';
}
?>

<link rel="stylesheet" type="text/css" media="all" href="/min/?b=<?php echo substr(CORE_CSS_DIR, 0, strlen(CORE_CSS_DIR)-1); ?>&amp;f=<?php echo implode(',', $cssFiles); ?>" />

<?php if (isset($includes['css'])) {
echo $includes['css'];
} ?>

</head>
<body>
<div class="container">
	<div class="row">
		<div class="span12">
			<header>
				<div id="header">Telesto Tweet Scheduler</div>
			</header>
		</div>
	</div>
	<div class="row" id="mainContentArea">
		<!-- Main Column -->
		<div class="span12">