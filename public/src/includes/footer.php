<?php /* Copyright 20xx Productions */ ?>
</div> <!-- end contentWrapper -->
</div> <!-- end row -->
<!-- footer -->
<footer>
<div id="footer">
	<div id="footerTop" class="hidden-phone"></div>
	<div class="row-fluid">
		<div class="span12">
			<div>&copy;2012 <?php echo SITE_TITLE; ?></div>
		</div>
	</div>
</div>
</footer>
</div> <!-- end container -->
<div id="mask"></div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<?php if ($includeParams['jquery-ui']) { ?>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
<?php }

/* Minify/Compress JS files */
$jsFiles = array('bootstrap.js','tethys.js');

if (file_exists(CORE_DIR_DEPTH.CORE_JS_DIR.'local.js')) {
	$jsFiles[] = 'local.js';
}
?>
<script type="text/javascript" src="/min/?b=<?php echo substr(CORE_JS_DIR, 0, strlen(CORE_JS_DIR)-1); ?>&amp;f=<?php echo implode(',',$jsFiles); ?>"></script>

<?php if (isset($includes['js'])) {
echo $includes['js'];
} ?>
<script type="text/javascript">
	var bmLevel = <?php echo CORE_BENCHMARK_LEVEL; ?>;
	var bmStartTime = '<?php echo $GLOBALS['bmObj']->startTime; ?>';
	var bmPageId = '<?php echo $GLOBALS['bmObj']->pageId; ?>';
	var bmPage = '<?php echo $GLOBALS['bmObj']->page; ?>';
	var bmVars = '<?php echo $GLOBALS['bmObj']->vars; ?>';
	var gTooltips = <?php echo ($includeParams['qtip']) ? 'true' : 'false'; ?>;
	var storePage = <?php echo ($includes['meta']['js-store'] === false) ? 'false' : 'true'; ?>;
<?php if ($includeParams['jquery'] || $includeParams['jquery-ui'] || $includeParams['qtip']) { ?>
	$(document).ready(function() {
		init();
	});
<?php } else { ?>
	window.onload = init;
<?php } ?>
</script>

<?php if (!CORE_DEVELOPMENT && GOOGLE_ANALYTICS_ID != '' && (GOOGLE_ANALYTICS_IGNORE_LIST == '' || !in_array($_SERVER['REMOTE_ADDR'], explode(',',GOOGLE_ANALYTICS_IGNORE_LIST)))) { ?>
<!-- google analytics -->
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?php echo GOOGLE_ANALYTICS_ID; ?>']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<?php } ?>
</body>
</html>
<?php recordPageView(); ?>
<?php SystemMessage::clear(); ?>
<?php ob_end_flush(); ?>
<?php $GLOBALS['bmObj']->log(1, 'Script end', 'This is the end of the script', true); ?>