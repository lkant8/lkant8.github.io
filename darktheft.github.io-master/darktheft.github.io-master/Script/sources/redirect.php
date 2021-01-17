<?php
function PageMain() {
	global $TMPL, $db, $confUrl;
	
	if(!isset($_GET['url'])) {
		header('Location: '.$confUrl);
	}
	
	$query = sprintf("SELECT * FROM links WHERE `gid` = '%s' OR `alias` = '%s'", mysqli_real_escape_string($db, $_GET['url']), mysqli_real_escape_string($db, $_GET['url']));
	$result = mysqli_fetch_row(mysqli_query($db, $query));

    header('Location: '.$result[1], true, 301);

	return;
}
?>