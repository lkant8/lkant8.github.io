<?php
include("../includes/config.php");
include("../includes/functions.php");
session_start();
$db = @mysqli_connect($conf['host'], $conf['user'], $conf['pass'], $conf['name']);
mysqli_query($db, 'SET NAMES utf8');

$confUrl = $conf['url'];

$resultSettings = mysqli_fetch_row(mysqli_query($db, getSettings($querySettings)));

// If captcha is enabled
if($resultSettings[9]) {
	if($_POST['captcha'] !== "{$_SESSION['captcha']}" || empty($_POST['captcha'])) {
		echo '<div class="alert alert-danger mt-3">Sorry but the captcha is invalid.</div>';
		return;
	}
}

if(substr($_POST['short'], 0, 7) !== 'http://' && substr($_POST['short'], 0, 8) !== 'https://') {
	$_POST['short'] = 'http://'.$_POST['short'];
}

$verifyAlias = verifyId('alias', $_POST['custom']);

if((isset($_POST['custom']) && $_POST['custom'] && $verifyAlias > 0) || in_array($_POST['custom'], ['page/disclaimer', 'page/privacy', 'page/contact', 'admin', 'welcome', 'page/tos', 'short'])) {
	echo '<div class="alert alert-danger mt-3">The current link can\'t have this alias, try another alias name.</div>';
	exit;
}

if(!validateUrl($_POST['short'])) {
	echo '<div class="alert alert-danger mt-3">The link you have entered is not valid.</div>';
	$x = 1;
} else {
	$id = generateId();
	if(validateAlphaDash($_POST['custom']) || empty($_POST['custom'])) { // verify if the Alias is only letters+-numbers or if it empty/not set.
		$query = sprintf("INSERT INTO links (`url`, `gid`, `alias`, `date`) VALUES ('%s', '%s', '%s', '%s')",
		mysqli_real_escape_string($db, $_POST['short']),
		mysqli_real_escape_string($db, $id),
		mysqli_real_escape_string($db, ($_POST['custom'] ? $_POST['custom'] : '')),
		mysqli_real_escape_string($db, date("Y-m-d H:i:s")));
		mysqli_query($db, $query);
	} else {
		echo '<div class="alert alert-danger mt-3">The alias must consist only from letters, numbers and dashes.</div>';
		$x = 1;
	}
}
mysqli_close($db);
?>
<?php
// OUTPUT THE RESULT
if($x !== 1) { // Check if there is no error to display the content
	echo '<div class="card mt-3"><div class="card-body"><div class="alert alert-success">Link shortened successfully.</div><div class="short-container" id="focus">';
		if(!empty($_POST['custom'])) {
			echo '<div class="form-group"><label>Custom URL</label><input type="text" value="'.$confUrl.'/'.e($_POST['custom']).'" readonly="readonly" class="form-control"></div>';
		}
		echo '<div class="form-group"><label>Shortened Url</label><input type="text" value="'.$confUrl.'/'.$id.'" readonly="readonly" class="form-control"></div>';
		echo '<div class="form-group"><label>Original Url</label><input type="text" value="'.e($_POST['short']).'" readonly="readonly" class="form-control"></div>';
		echo '<label>Share</label>
	<div class="d-flex flex-wrap">
	
		<a href="#" id="share-twitter" class="d-flex align-items-center icon-twitter p-2 mb-3 mt-2 text-white text-white mr-3 rounded" data-url="'.$confUrl.'/'.$id.'" data-title="">
			<svg xmlns="http://www.w3.org/2000/svg" class="icon-social fill-current" viewBox="0 0 24 19.5"><path d="M24,2.3a9,9,0,0,1-2.8.8A5.32,5.32,0,0,0,23.4.4a9.08,9.08,0,0,1-3.1,1.2A5.23,5.23,0,0,0,16.6,0a4.91,4.91,0,0,0-4.9,4.9A3.75,3.75,0,0,0,11.8,6,13.68,13.68,0,0,1,1.7.9,4.3,4.3,0,0,0,1,3.4,5,5,0,0,0,3.2,7.5,7.09,7.09,0,0,1,1,6.9V7a4.85,4.85,0,0,0,3.9,4.8,5.07,5.07,0,0,1-1.3.2,2.77,2.77,0,0,1-.9-.1,4.74,4.74,0,0,0,4.6,3.4,10,10,0,0,1-6.1,2.1A4.87,4.87,0,0,1,0,17.3a14,14,0,0,0,7.5,2.2,13.87,13.87,0,0,0,14-14V4.9A10.3,10.3,0,0,0,24,2.3"></path></svg>                </a>

		<a href="#" id="share-facebook" class="d-flex align-items-center icon-facebook p-2 mb-3 mt-2 text-white text-white mr-3 rounded" data-url="'.$confUrl.'/'.$id.'" data-title="">
			<svg xmlns="http://www.w3.org/2000/svg" class="icon-social fill-current" viewBox="0 0 24 23.88"><path d="M12,0a12,12,0,0,0-1.79,23.85V15.18h-3V12h3V9.93c0-3.48,1.69-5,4.58-5a16.26,16.26,0,0,1,2.46.15V7.83h-2c-1.23,0-1.66,1.16-1.66,2.47V12h3.6l-.49,3.15H13.62v8.7A12,12,0,0,0,12,0Z"></path></svg>                </a>

		<a href="#" id="share-reddit" class="d-flex align-items-center icon-reddit p-2 mb-3 mt-2 text-white text-white mr-3 rounded" data-url="'.$confUrl.'/'.$id.'" data-title="">
			<svg xmlns="http://www.w3.org/2000/svg" class="icon-social fill-current" viewBox="0 0 46.08 42"><path d="M27,0c-2.1,0-4.57,1.65-4.94,9,.32,0,.62,0,.94,0L24.1,9C24.34,4.6,25.4,2,27,2c.7,0,1.1.38,1.84,1.19A7.43,7.43,0,0,0,33.1,5.81,7.18,7.18,0,0,1,33,5a5.49,5.49,0,0,1,.13-1.22,5.61,5.61,0,0,1-2.82-1.94A4.36,4.36,0,0,0,27,0ZM39,1a4,4,0,1,0,4,4A4,4,0,0,0,39,1ZM23,11C10.91,11,1,17.18,1,26s9.87,16,22,16,22-7.18,22-16S35.17,11,23,11Zm-17.5.94a5.56,5.56,0,0,0-3.91,1.65A5.45,5.45,0,0,0,.54,19.84,18,18,0,0,1,8,12.56,5.58,5.58,0,0,0,5.54,11.94Zm35,0a5.58,5.58,0,0,0-2.47.62,18,18,0,0,1,7.47,7.28,5.43,5.43,0,0,0-1.09-6.25A5.56,5.56,0,0,0,40.54,11.94ZM15,20a3,3,0,1,1-3,3A3,3,0,0,1,15,20Zm16,0a3,3,0,1,1-3,3A3,3,0,0,1,31,20ZM14.1,31a1,1,0,0,1,.66.41c.11.14,2.45,3.28,8.28,3.28s8.26-3.22,8.28-3.25a1,1,0,0,1,1.41-.25A1,1,0,0,1,33,32.56c-.12.17-3,4.13-9.91,4.13s-9.79-4-9.91-4.13a1,1,0,0,1,.22-1.37A1,1,0,0,1,14.1,31Z"></path></svg>                </a>

		<a href="#" id="share-pinterest" class="d-flex align-items-center icon-pinterest p-2 mb-3 mt-2 text-white text-white mr-3 rounded" data-url="'.$confUrl.'/'.$id.'" data-title="">
			<svg xmlns="http://www.w3.org/2000/svg" class="icon-social fill-current" viewBox="0 0 24 24"><path d="M12,0A12,12,0,0,0,7.63,23.17a11.37,11.37,0,0,1,0-3.44l1.41-6A4.33,4.33,0,0,1,8.72,12c0-1.67,1-2.92,2.17-2.92a1.51,1.51,0,0,1,1.52,1.69,24.2,24.2,0,0,1-1,4,1.75,1.75,0,0,0,1.78,2.17c2.13,0,3.77-2.25,3.77-5.5a4.73,4.73,0,0,0-5-4.88,5.19,5.19,0,0,0-5.42,5.21,4.74,4.74,0,0,0,.89,2.74.34.34,0,0,1,.09.34c-.09.38-.3,1.19-.34,1.36s-.17.27-.4.16c-1.5-.7-2.43-2.89-2.43-4.65,0-3.78,2.75-7.26,7.93-7.26,4.16,0,7.39,3,7.39,6.93,0,4.14-2.6,7.47-6.22,7.47a3.22,3.22,0,0,1-2.75-1.38s-.61,2.29-.75,2.85a13.47,13.47,0,0,1-1.49,3.15A12.2,12.2,0,0,0,12,24,12,12,0,0,0,12,0Z"></path></svg>                </a>

		<a href="#" id="share-linkedin" class="d-flex align-items-center icon-linkedin p-2 mb-3 mt-2 text-white text-white mr-3 rounded" data-url="'.$confUrl.'/'.$id.'" data-title="">
			<svg xmlns="http://www.w3.org/2000/svg" class="icon-social fill-current" viewBox="0 0 22 22"><path d="M20,0H2A2,2,0,0,0,0,2V20a2,2,0,0,0,2,2H20a2,2,0,0,0,2-2V2A2,2,0,0,0,20,0ZM7,18H4V8.51H7ZM5.45,7.15A1.72,1.72,0,1,1,7.17,5.43,1.72,1.72,0,0,1,5.45,7.15ZM18,18H15.06V13.38c0-1.1,0-2.51-1.54-2.51s-1.77,1.2-1.77,2.43V18H8.8V8.51h2.83v1.3h0a3.11,3.11,0,0,1,2.79-1.54c3,0,3.54,2,3.54,4.52Z"></path></svg>                </a>

		<a href="#" id="share-email" class="d-flex align-items-center icon-email p-2 mb-3 mt-2 text-white text-white mr-3 rounded" data-url="'.$confUrl.'/'.$id.'" data-title="">
			<svg xmlns="http://www.w3.org/2000/svg" class="icon-social fill-current" viewBox="0 0 20 16"><path d="M2,0A2,2,0,0,0,.07,1.5L10,7.73l9.94-6.21A2,2,0,0,0,18,0ZM0,3.73V14a2,2,0,0,0,2,2H18a2,2,0,0,0,2-2V3.76L10,10Z"></path></svg>                </a>
			
		<a href="#" id="share-qr" class="d-flex align-items-center icon-qr p-2 mb-3 mt-2 text-white text-white mr-3 rounded" data-url="'.$confUrl.'/'.$id.'" data-title="" data-qr="https://demo.phpshort.com/qr/29">
				<svg xmlns="http://www.w3.org/2000/svg" class="icon-social text-white-important fill-current" viewBox="0 0 100 100"><path d="M0,0V33.33H33.33V0ZM44.44,0V11.11H55.56V0ZM66.67,0V33.33H100V0ZM11.11,11.11H22.22V22.22H11.11Zm66.67,0H88.89V22.22H77.78ZM44.44,22.22V33.33H55.56V22.22ZM0,44.44V55.56H11.11V44.44Zm22.22,0V55.56H33.33V44.44Zm22.22,0V55.56H55.56V44.44ZM55.56,55.56V66.67H66.67V55.56Zm11.11,0H77.78V44.44H66.67Zm11.11,0V66.67H88.89V55.56Zm11.11,0H100V44.44H88.89Zm0,11.11V77.78H100V66.67Zm0,11.11H77.78V88.89H88.89Zm0,11.11V100H100V88.89Zm-11.11,0H66.67V100H77.78Zm-11.11,0V77.78H55.56V88.89Zm-11.11,0H44.44V100H55.56Zm0-11.11V66.67H44.44V77.78Zm11.11,0H77.78V66.67H66.67ZM0,66.67V100H33.33V66.67ZM11.11,77.78H22.22V88.89H11.11Z"></path></svg>                </a>
	</div>';
	echo '</div></div></div>';
}
?>
<script>
	document.querySelector('#share-twitter') && document.querySelector('#share-twitter').addEventListener('click', function (e) {
		e.preventDefault();

		popupCenter("https://twitter.com/intent/tweet?text="+encodeURIComponent(this.dataset.title)+"&url="+encodeURIComponent(this.dataset.url), encodeURIComponent(this.dataset.title), 550, 250);
	});

	document.querySelector('#share-facebook') && document.querySelector('#share-facebook').addEventListener('click', function (e) {
		e.preventDefault();

		popupCenter("https://www.facebook.com/sharer/sharer.php?u="+encodeURIComponent(this.dataset.url), encodeURIComponent(this.dataset.title), 550, 300);
	});

	document.querySelector('#share-reddit') && document.querySelector('#share-reddit').addEventListener('click', function (e) {
		e.preventDefault();

		popupCenter("http://www.reddit.com/submit?url="+encodeURIComponent(this.dataset.url), encodeURIComponent(this.dataset.title), 550, 530);
	});

	document.querySelector('#share-pinterest') && document.querySelector('#share-pinterest').addEventListener('click', function (e) {
		e.preventDefault();

		popupCenter("http://pinterest.com/pin/create/button/?url="+encodeURIComponent(this.dataset.url)+"&description="+encodeURIComponent(this.dataset.title), encodeURIComponent(this.dataset.title), 550, 300);
	});

	document.querySelector('#share-linkedin') && document.querySelector('#share-linkedin').addEventListener('click', function (e) {
		e.preventDefault();

		popupCenter("https://www.linkedin.com/sharing/share-offsite/?url="+encodeURIComponent(this.dataset.url), encodeURIComponent(this.dataset.title), 550, 300);
	});

	document.querySelector('#share-email') && document.querySelector('#share-email').addEventListener('click', function (e) {
		e.preventDefault();

		window.open("mailto:?Subject="+encodeURIComponent(this.dataset.title)+"&body="+encodeURIComponent(this.dataset.title)+" - "+encodeURIComponent(this.dataset.url), "_self");
	});

	document.querySelector('#share-qr') && document.querySelector('#share-qr').addEventListener('click', function (e) {
		e.preventDefault();

		popupCenter('http://chart.googleapis.com/chart?cht=qr&chs=300x300&choe=UTF-8&chld=H|0&chl='+this.dataset.url, '', 300, 300);
	});

	/**
	 * Center the pop-up window
	 *
	 * @param url
	 * @param title
	 * @param w
	 * @param h
	 */
	let popupCenter = (url, title, w, h) => {
		// Fixes dual-screen position                         Most browsers      Firefox
		let dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : window.screenX;
		let dualScreenTop = window.screenTop != undefined ? window.screenTop : window.screenY;

		let width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
		let height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

		let systemZoom = width / window.screen.availWidth;
		let left = (width - w) / 2 / systemZoom + dualScreenLeft;
		let top = (height - h) / 2 / systemZoom + dualScreenTop;
		let newWindow = window.open(url, title, 'scrollbars=yes, width=' + w / systemZoom + ', height=' + h / systemZoom + ', top=' + top + ', left=' + left);

		// Puts focus on the newWindow
		if (window.focus) newWindow.focus();
	};
</script>
<style>
	.icon-social {
		width: 1.25rem;
		height: 1.25rem;
		fill: currentColor;
	}

	.icon-twitter {
		background: #1DA1F2;
	}

	.icon-facebook {
		background: #4267B2;
	}

	.icon-reddit {
		background: #FF4500;
	}

	.icon-pinterest {
		background: #E60023;
	}

	.icon-linkedin {
		background: #0077B5;
	}

	.icon-email {
		background: #6e6e6e;
	}

	.icon-qr {
		background: #343434;
	}
</style>