<?php
function PageMain() {
	global $TMPL, $db, $confUrl;
	$title = array( 'privacy'    => 'Privacy Policy',
					'tos'		 => 'Terms of User',
					'about'		 => 'About',
					'disclaimer' => 'Disclaimer',
					'contact'    => 'Contact',
					'api'		 => 'API Documentation');
	if(!empty($_GET['b']) && isset($title[$_GET['b']])) {
		$b = $_GET['b'];
		
		$resultSettings = mysqli_fetch_row(mysqli_query($db, getSettings($querySettings)));
		
		$TMPL['url'] = $confUrl;
		$TMPL['title'] = "{$title[$b]} - ".$resultSettings[0]."";
		$skin = new skin("page/$b");
		return $skin->make();
	} else {
        header('Location: '.$confUrl, true, 301);
	}
}
?>