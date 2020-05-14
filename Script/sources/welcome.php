<?php
function PageMain() {
	global $TMPL, $db, $confUrl;
	$resultSettings = mysqli_fetch_row(mysqli_query($db, getSettings($querySettings)));
	
	if(ctype_alnum($_GET['a'])) {
		$query = "SELECT * FROM links WHERE `gid` = '".$_GET['a']."' OR `alias` = '".$_GET['a']."'";
		$result = mysqli_fetch_row(mysqli_query($db, $query));

		header('Location: '.$result[1]);
	}
	
	$TMPL['url'] = $confUrl;
	$TMPL['title'] = $resultSettings[0];
	
	$TMPL['ad1'] = $resultSettings[2];
	$TMPL['ad2'] = $resultSettings[3];
	
	if($resultSettings[9]) {
		// Captcha
		$TMPL['captcha'] = '
<div class="col-12 col-md">
<div class="input-group mt-3 mt-md-0">
  <input type="text" name="captcha" placeholder="Captcha" id="captcha" autocomplete="off" class="captcha form-control form-control-lg font-size-lg">
  <div class="input-group-append">
    <span class="input-group-text p-0 bg-white" id="basic-addon2"><img src="'.$confUrl.'/captcha.php"></span>
  </div>
</div>
</div>';
	}
	
	$skin = new skin('welcome/content');
	return $skin->make();
}
?>