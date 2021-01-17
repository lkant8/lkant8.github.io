<?php
require_once('./includes/config.php');
require_once('./includes/skins.php');
require_once('./includes/functions.php');

$db = @mysqli_connect($conf['host'], $conf['user'], $conf['pass'], $conf['name']);
mysqli_query($db, 'SET NAMES utf8');

if(!$db) {	
	echo "Failed to connect to MySQL: (" . mysqli_connect_errno() . ") " . mysqli_connect_error();
}

if(isset($_GET['a']) && isset($action[$_GET['a']])) {
	$page_name = $action[$_GET['a']];
} else {
	$page_name = 'welcome';
}

require_once("./sources/{$page_name}.php");

$confUrl = $conf['url'];

$TMPL['content'] = PageMain();

$resultSettings = mysqli_fetch_row(mysqli_query($db, getSettings($querySettings)));

$TMPL['twitterFooter'] = $resultSettings[6];
$TMPL['facebookFooter'] = $resultSettings[7];

$TMPL['siteTitle'] = $resultSettings[0];
$TMPL['footer'] = $resultSettings[0];
$TMPL['url'] = $conf['url'];
$TMPL['year'] = date('Y');

if($_GET['a'] == 'redirect') { // Change the body file if the redirect is enabled
$skin = new skin('frame');
} else {
$skin = new skin('wrapper');
}
echo $skin->make();

mysqli_close($db);
?>